<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskStep;
use App\Events\Task\TaskStepsUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TaskBroadcastResource;

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

        $this->notifyTaskStepsUpdated($task);

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
     * Notifica a los demás miembros del proyecto sobre cambios en los pasos.
     */
    private function notifyTaskStepsUpdated(Task $task)
    {
        $otherMemberIds = $task->project->getOtherMemberIds(auth()->id());

        if (!empty($otherMemberIds)) {
            TaskStepsUpdated::dispatch(TaskBroadcastResource::make($task)->resolve(), $otherMemberIds);
        }
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

        $this->notifyTaskStepsUpdated($step->task);

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

        $this->notifyTaskStepsUpdated($step->task);

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

        $task = $step->task;
        $step->delete();

        $this->notifyTaskStepsUpdated($task);

        return response()->json([
            'message' => 'Paso eliminado.',
        ]);
    }
}
