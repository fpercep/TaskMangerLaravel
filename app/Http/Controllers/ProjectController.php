<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Support\Facades\Auth;

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

        // Redirect back with success message
        return back()->with('success', 'Proyecto creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        // En un caso real, podrías autorizar si el usuario puede ver el proyecto.
        // $this->authorize('view', $project);

        return view('pages.projects.show', [
            'project' => $project,
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

        // Si el usuario estaba en la página del proyecto que se acaba de eliminar, 
        // lo redirigimos al dashboard para evitar un 404.
        if (url()->previous() === $projectUrl) {
            return redirect()->route('dashboard')->with('success', 'Proyecto eliminado correctamente.');
        }

        // Si estaba en cualquier otra página (Dashboard, Mi Día, etc.), volvemos atrás.
        return back()->with('success', 'Proyecto eliminado correctamente.');
    }
}
