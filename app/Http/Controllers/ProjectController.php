<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
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
            ->with('steps')
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
                'due_date' => $task->due_date?->format('d/m/Y'),
                'created_at' => $task->created_at->format('d/m/Y'),
                'updated_at' => $task->updated_at->format('d/m/Y'),
                'steps' => $task->steps->map(function ($step) {
                    return [
                        'id' => $step->id,
                        'name' => $step->name,
                        'is_completed' => $step->is_completed,
                    ];
                }),
                'steps_count' => $task->steps_count,
                'completed_steps_count' => $task->completed_steps_count,
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

        Cache::forget(Auth::user()->sidebarCacheKey());

        return back()->with('success', 'Proyecto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        // Usamos el Policy para autorizar la acción
        $this->authorize('delete', $project);

        $projectUrl = route('projects.show', $project);
        $project->delete();

        Cache::forget(Auth::user()->sidebarCacheKey());

        // Si el usuario estaba en la página del proyecto que se acaba de eliminar, 
        // lo redirigimos al dashboard para evitar un 404.
        if (url()->previous() === $projectUrl) {
            return redirect()->route('dashboard')->with('success', 'Proyecto eliminado correctamente.');
        }

        // Si estaba en cualquier otra página (Dashboard, Mi Día, etc.), volvemos atrás.
        return back()->with('success', 'Proyecto eliminado correctamente.');
    }
}
