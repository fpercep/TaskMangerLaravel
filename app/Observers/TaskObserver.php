<?php

namespace App\Observers;

use App\Models\Task;
use App\Events\Task\TaskCreated;
use App\Events\Task\TaskUpdated;
use App\Events\Task\TaskDeleted;
use App\Events\Task\TaskAssigned;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\TaskBroadcastResource;

class TaskObserver
{
    /**
     * Se ejecuta automáticamente al crear una nueva tarea (o al duplicarla).
     */
    public function created(Task $task): void
    {
        // Obtenemos a quién notificar
        $otherMemberIds = $task->project->getOtherMemberIds(auth()->id());
        
        if (!empty($otherMemberIds)) {
            TaskCreated::dispatch(TaskBroadcastResource::make($task)->resolve(), $otherMemberIds);
        }
    }

    /**
     * Se ejecuta automáticamente cuando una tarea existente es modificada.
     */
    public function updated(Task $task): void
    {
        // 1. Invalidar caché de estadísticas del dashboard
        // Verificamos si hay un usuario autenticado para evitar errores en consola/tareas automáticas
        if (auth()->check()) {
            Cache::forget('dashboard_stats_' . auth()->id());
        }

        // 2. Notificar a los demás miembros
        $otherMemberIds = $task->project->getOtherMemberIds(auth()->id());
        
        if (!empty($otherMemberIds)) {
            // El método nativo isDirty() verifica si un campo específico cambió en esta actualización exacta
            if ($task->isDirty('assigned_user_id')) {
                TaskAssigned::dispatch(TaskBroadcastResource::make($task)->resolve(), $otherMemberIds);
            } else {
                TaskUpdated::dispatch(TaskBroadcastResource::make($task)->resolve(), $otherMemberIds);
            }
        }
    }

    /**
     * Se ejecuta automáticamente después de eliminar una tarea de la base de datos.
     */
    public function deleted(Task $task): void
    {
        // 1. Invalidar caché
        if (auth()->check()) {
            Cache::forget('dashboard_stats_' . auth()->id());
        }

        // 2. Notificar eliminación
        $otherMemberIds = $task->project->getOtherMemberIds(auth()->id());
        
        if (!empty($otherMemberIds)) {
            TaskDeleted::dispatch($task->id, $task->project_id, $otherMemberIds);
        }
    }
}
