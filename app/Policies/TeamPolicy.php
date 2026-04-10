<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TeamPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Team $team): bool
    {
        return $user->teams()->where('teams.id', $team->id)->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Team $team): bool
    {
        return $user->teams()
            ->where('teams.id', $team->id)
            ->wherePivotIn('role', ['owner', 'admin'])
            ->exists();
    }
}
