<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Events\Task\TaskCreated;
use App\Events\Task\TaskUpdated;
use App\Events\Task\TaskDeleted;
use App\Events\Task\TaskMoved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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

        $task = $project->tasks()->create($validated);

        // Notificar a los demás miembros
        $memberIds = $project->users()->pluck('users.id')->toArray();
        $otherMemberIds = array_values(array_diff($memberIds, [Auth::id()]));

        if (!empty($otherMemberIds)) {
            TaskCreated::dispatch($task->toBroadcastArray(), $otherMemberIds);
        }

        return back()->with('success', 'Tarea creada correctamente.');
    }

    /**
     * Actualiza una tarea de forma genérica.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'status' => ['sometimes', 'required', 'string', 'in:pending,in_progress,completed,cancelled'],
            'priority' => ['sometimes', 'required', 'string', 'in:low,medium,high,urgent'],
            'description' => ['sometimes', 'nullable', 'string'],
            'due_date' => ['sometimes', 'nullable', 'date'],
        ]);

        $task->update($validated);

        // Invalidar caché de estadísticas del dashboard
        Cache::forget('dashboard_stats_' . auth()->id());

        // Notificar a los demás miembros
        $memberIds = $task->project->users()->pluck('users.id')->toArray();
        $otherMemberIds = array_values(array_diff($memberIds, [Auth::id()]));

        if (!empty($otherMemberIds)) {
            TaskUpdated::dispatch($task->toBroadcastArray(), $otherMemberIds);
        }

        return response()->json([
            'message' => 'Tarea actualizada correctamente',
        ]);
    }


    /**
     * Duplica una tarea (calco 1:1).
     */
    public function duplicate(Task $task)
    {
        $this->authorize('update', $task);

        // Cargamos la relación y los conteos en una sola operación encadenada.
        // load('steps') es necesario para el foreach; loadCount para los contadores del payload.
        $task->load('steps')->loadCount([
            'steps',
            'steps as completed_steps_count' => fn($q) => $q->where('is_completed', true)
        ]);

        return DB::transaction(function () use ($task) {
            // 1. Replicar la tarea base
            $newTask = $task->replicate();
            $newTask->save();

            // 2. Replicar los pasos (TaskStep)
            foreach ($task->steps as $step) {
                $newStep = $step->replicate();
                $newStep->task_id = $newTask->id;
                $newStep->save();
            }

            // 3. Replicar las asignaciones de usuarios
            $newTask->users()->sync($task->users->pluck('id'));

            // Invalidar caché de estadísticas
            Cache::forget('dashboard_stats_' . auth()->id());

            if (!empty($otherMemberIds)) {
                TaskCreated::dispatch($newTask->toBroadcastArray(), $otherMemberIds);
            }

            // Transformación a Array Limpio (Regla 4.4)
            return response()->json([
                'message' => 'Tarea duplicada correctamente',
                'task' => $newTask->toBroadcastArray()
            ]);
        });
    }

    /**
     * Elimina una tarea.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $projectId = $task->project_id;
        $taskId = $task->id;

        // Obtener miembros antes de borrar
        $memberIds = $task->project->users()->pluck('users.id')->toArray();

        $task->delete();

        // Invalidar caché de estadísticas
        Cache::forget('dashboard_stats_' . auth()->id());

        // Notificar a los demás miembros
        $otherMemberIds = array_values(array_diff($memberIds, [Auth::id()]));
        if (!empty($otherMemberIds)) {
            TaskDeleted::dispatch($taskId, $projectId, $otherMemberIds);
        }

        return response()->json([
            'message' => 'Tarea eliminada correctamente',
        ]);
    }
}
