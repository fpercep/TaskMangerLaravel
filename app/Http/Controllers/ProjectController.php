<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProjectController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $validated = $request->validated();

        Auth::user()->projects()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => 'active',
            'visibility' => 'private',
        ], ['role' => 'admin']);

        Cache::forget(Auth::user()->sidebarCacheKey());

        return back()->with('success', 'Proyecto creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        // Carga de tareas con sus relaciones y contadores
        $project->load(['tasks' => function ($query) {
            $query->with(['steps', 'assignedUser'])
                  ->withCount('steps')
                  ->withCount(['steps as completed_steps_count' => fn($q) => $q->where('is_completed', true)])
                  ->orderByDesc('priority')
                  ->orderByDesc('created_at');
        }]);

        return view('pages.projects.show', [
            'project' => $project,
            // TaskResource se encarga de todo el formateo de los datos
            'tasks' => TaskResource::collection($project->tasks)->resolve(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update($request->validated());

        // La limpieza de caché y los eventos ahora ocurren automáticamente en el ProjectObserver

        return back()->with('success', 'Proyecto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $projectUrl = route('projects.show', $project);
        
        $project->delete(); // El Observer se encarga de los eventos secundarios al eliminar

        if (url()->previous() === $projectUrl) {
            return redirect()->route('dashboard')->with('success', 'Proyecto eliminado correctamente.');
        }

        return back()->with('success', 'Proyecto eliminado correctamente.');
    }
}
