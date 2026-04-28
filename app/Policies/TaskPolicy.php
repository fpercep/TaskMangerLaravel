<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Lógica compartida para verificar acceso al proyecto de la tarea.
     */
    private function canAccess(User $user, Task $task): bool
    {
        return $user->projects()->where('project_id', $task->project_id)->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        return $this->canAccess($user, $task);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        return $this->canAccess($user, $task);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return $this->canAccess($user, $task);
    }
}
