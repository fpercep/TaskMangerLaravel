<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskStep;
use Illuminate\Http\Request;

class TaskStepController extends Controller
{
    /**
     * Crea un nuevo paso en una tarea.
     */
    public function store(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $maxPosition = $task->steps()->max('position') ?? -1;

        $step = $task->steps()->create([
            'name' => $validated['name'],
            'is_completed' => false,
            'position' => $maxPosition + 1,
        ]);

        return response()->json([
            'message' => 'Paso creado correctamente.',
            'step' => [
                'id' => $step->id,
                'name' => $step->name,
                'is_completed' => $step->is_completed,
            ],
        ], 201);
    }

    /**
     * Alterna el estado completado de un paso.
     */
    public function toggle(TaskStep $step)
    {
        $this->authorize('update', $step->task);

        $step->update([
            'is_completed' => !$step->is_completed,
        ]);

        return response()->json([
            'message' => 'Paso actualizado.',
            'is_completed' => $step->is_completed,
        ]);
    }

    /**
     * Actualiza el nombre de un paso.
     */
    public function update(Request $request, TaskStep $step)
    {
        $this->authorize('update', $step->task);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $step->update([
            'name' => $validated['name'],
        ]);

        return response()->json([
            'message' => 'Paso actualizado.',
            'step' => [
                'id' => $step->id,
                'name' => $step->name,
                'is_completed' => $step->is_completed,
            ],
        ]);
    }

    /**
     * Elimina un paso.
     */
    public function destroy(TaskStep $step)
    {
        $this->authorize('update', $step->task);

        $step->delete();

        return response()->json([
            'message' => 'Paso eliminado.',
        ]);
    }
}
