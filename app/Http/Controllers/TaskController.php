<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
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
