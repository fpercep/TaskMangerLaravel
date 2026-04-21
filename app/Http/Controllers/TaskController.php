<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Almacena una nueva tarea.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:pending,in_progress,completed'],
            'priority' => ['required', 'string', 'in:low,medium,high,urgent'],
            'due_date' => ['nullable', 'date'],
        ]);

        $project->tasks()->create($validated);

        return back()->with('success', 'Tarea creada correctamente.');
    }

    /**
     * Actualiza el estado de una tarea (para el Kanban).
     */
    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,in_progress,completed'],
        ]);

        // Idealmente verificar permisos aquí
        // $this->authorize('update', $task);

        $task->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'message' => 'Estado actualizado correctamente',
        ]);
    }
}
