<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Events\Project\ProjectDetailsUpdated;
use App\Events\Project\ProjectDeleted;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProjectController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        // La validación ya se ha hecho en el FormRequest
        $validated = $request->validated();

        // El modelo gestiona la inserción y asignamos el rol pivot directamente
        $project = Auth::user()->projects()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => 'active',
            'visibility' => 'private',
        ], ['role' => 'admin']);

        Cache::forget(Auth::user()->sidebarCacheKey());

        // Redirect back with success message
        return back()->with('success', 'Proyecto creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $project->load(['tasks' => function ($query) {
            $query->where(function ($q) {
                $q->where('status', '!=', 'completed')
                  ->orWhere('updated_at', '>=', now()->subDays(7));
            })
            ->with(['steps', 'assignedUser'])
            ->withCount('steps')
            ->withCount(['steps as completed_steps_count' => function ($q) {
                $q->where('is_completed', true);
            }])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');
        }]);

        $tasks = $project->tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'name' => $task->name,
                'description' => $task->description,
                'has_description' => !empty($task->description),
                'status' => $task->status,
                'priority' => $task->priority,
                'due_date' => $task->due_date?->format('Y-m-d'), // Formato ISO para input[type=date]
                'created_at' => $task->created_at->format('Y-m-d'),
                'updated_at' => $task->updated_at->format('Y-m-d'),
                'steps' => $task->steps->map(function ($step) {
                    return [
                        'id' => $step->id,
                        'name' => $step->name,
                        'is_completed' => $step->is_completed,
                    ];
                }),
                'steps_count' => $task->steps_count,
                'completed_steps_count' => $task->completed_steps_count,
                'assigned_user_id' => $task->assigned_user_id,
                'assigned_user' => $task->assignedUser ? [
                    'id' => $task->assignedUser->id,
                    'name' => $task->assignedUser->name,
                    'initials' => $task->assignedUser->initials,
                ] : null,
            ];
        });

        $project->unsetRelation('tasks');

        return view('pages.projects.show', [
            'project' => $project,
            'tasks' => $tasks,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        // FormRequest se encarga de la Autorización (Policy) y Validación.
        $validated = $request->validated();

        $project->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        // 1. Limpiar caché de sidebar de TODOS los miembros
        $project->clearMembersSidebarCache();

        // 2. Notificar a los demás miembros del cambio
        $otherMemberIds = $project->getOtherMemberIds();
        if (!empty($otherMemberIds)) {
            ProjectDetailsUpdated::dispatch($project->id, $project->name, $otherMemberIds);
        }

        return back()->with('success', 'Proyecto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        // Usamos el Policy para autorizar la acción
        $this->authorize('delete', $project);

        $projectId = $project->id;
        $projectUrl = route('projects.show', $project);
        
        // 1. Limpiar caché de sidebar de TODOS los miembros
        $project->clearMembersSidebarCache();

        // 2. Notificar a los demás miembros de la eliminación (el actual ya es redirigido)
        $otherMemberIds = $project->getOtherMemberIds();
        if (!empty($otherMemberIds)) {
            ProjectDeleted::dispatch($projectId, $otherMemberIds);
        }

        // 3. Eliminar el proyecto
        $project->delete();

        if (url()->previous() === $projectUrl) {
            return redirect()->route('dashboard')->with('success', 'Proyecto eliminado correctamente.');
        }

        return back()->with('success', 'Proyecto eliminado correctamente.');
    }
}
