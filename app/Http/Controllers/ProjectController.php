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

        $project->delete();

        return back()->with('success', 'Proyecto eliminado correctamente.');
    }
}
