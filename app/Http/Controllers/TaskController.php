<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Almacena una nueva tarea.
     */
    public function store(StoreTaskRequest $request, Project $project)
    {
        $this->authorize('view', $project);

        $project->tasks()->create($request->validated());

        return back()->with('success', 'Tarea creada correctamente.');
    }

    /**
     * Actualiza una tarea de forma genérica.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $task->update($request->validated());

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

        $task->load('steps')->loadCount([
            'steps',
            'steps as completed_steps_count' => fn($q) => $q->where('is_completed', true)
        ]);

        return DB::transaction(function () use ($task) {
            $newTask = $task->replicate();
            $newTask->assigned_user_id = null;
            $newTask->save();

            foreach ($task->steps as $step) {
                $newStep = $step->replicate();
                $newStep->task_id = $newTask->id;
                $newStep->save();
            }

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

        $task->delete(); 
        
        return response()->json([
            'message' => 'Tarea eliminada correctamente',
        ]);
    }
}
