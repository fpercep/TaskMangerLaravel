<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        return $user->projects()->where('project_id', $project->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model (settings).
     */
    public function update(User $user, Project $project): bool
    {
        return $this->hasRole($user, $project, ['admin', 'manager']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        return $this->hasRole($user, $project, ['admin']);
    }

    /**
     * Determine whether the user can manage project members.
     */
    public function manageMembers(User $user, Project $project): bool
    {
        return $this->hasRole($user, $project, ['admin', 'manager']);
    }

    /**
     * Determine whether the user can leave the project.
     */
    public function leave(User $user, Project $project): bool
    {
        return $user->projects()->where('project_id', $project->id)->exists();
    }

    /**
     * Comprueba si el usuario tiene uno de los roles especificados en el proyecto.
     */
    private function hasRole(User $user, Project $project, array $roles): bool
    {
        return $user->projects()
            ->where('project_id', $project->id)
            ->whereIn('role', $roles)
            ->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        return false;
    }
}
