<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
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

        $project->tasks()->create($validated);

        return back()->with('success', 'Tarea creada correctamente.');
    }

    /**
     * Actualiza una tarea de forma genérica.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'status' => ['sometimes', 'required', 'string', 'in:pending,in_progress,completed'],
            'priority' => ['sometimes', 'required', 'string', 'in:low,medium,high,urgent'],
            'description' => ['sometimes', 'nullable', 'string'],
            'due_date' => ['sometimes', 'nullable', 'date'],
        ]);

        $this->authorize('update', $task);

        $task->update($validated);

        // Invalidar caché de estadísticas del dashboard
        Cache::forget('dashboard_stats_' . auth()->id());

        return response()->json([
            'message' => 'Tarea actualizada correctamente',
        ]);
    }

    /**
     * Actualiza el estado de una tarea (para el Kanban).
     */
    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,in_progress,completed'],
        ]);

        $this->authorize('update', $task);

        $task->update([
            'status' => $validated['status'],
        ]);

        // Invalidar caché de estadísticas del dashboard
        Cache::forget('dashboard_stats_' . auth()->id());

        return response()->json([
            'message' => 'Estado actualizado correctamente',
        ]);
    }

    /**
     * Duplica una tarea (calco 1:1).
     */
    public function duplicate(Task $task)
    {
        $this->authorize('update', $task);

        // Cargamos los conteos una sola vez de forma eficiente
        $task->loadCount([
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

            // Transformación a Array Limpio (Regla 4.4)
            return response()->json([
                'message' => 'Tarea duplicada correctamente',
                'task' => [
                    'id' => $newTask->id,
                    'name' => $newTask->name,
                    'status' => $newTask->status,
                    'priority' => $newTask->priority,
                    'due_date' => $newTask->due_date,
                    'has_description' => !empty($newTask->description),
                    'steps_count' => $task->steps_count,
                    'completed_steps_count' => $task->completed_steps_count,
                ]
            ]);
        });
    }

    /**
     * Elimina una tarea.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        // Invalidar caché de estadísticas
        Cache::forget('dashboard_stats_' . auth()->id());

        return response()->json([
            'message' => 'Tarea eliminada correctamente',
        ]);
    }
}
