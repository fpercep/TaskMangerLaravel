<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProjectMemberService
{
    /**
     * Add a single user to the project.
     */
    public function addMember(Project $project, int $userId, string $role): array
    {
        if ($project->users()->where('user_id', $userId)->exists()) {
            return [
                'error' => 'El usuario ya es miembro de este proyecto.', 
                'status' => 409 
            ];
        }

        $project->users()->attach($userId, ['role' => $role]);
        
        $this->clearUserSidebarCache($userId);

        return [
            'success' => 'Usuario añadido correctamente.',
            'status' => 201
        ];
    }

    /**
     * Update a single member's role.
     */
    public function updateMemberRole(Project $project, int $userId, string $role): array
    {
        $affectedRows = $project->users()->updateExistingPivot($userId, ['role' => $role]);

        if ($affectedRows > 0) {
            $this->clearUserSidebarCache($userId);
            return ['success' => 'Rol actualizado correctamente.'];
        }

        return ['info' => 'El usuario ya tenía asignado este rol.'];
    }

    /**
     * Remove a single user from the project.
     */
    public function removeMember(Project $project, int $userId): array
    {
        if ($userId === Auth::id()) {
            return ['error' => 'No puedes eliminarte a ti mismo del proyecto.', 'status' => 403];
        }

        $project->users()->detach($userId);
        $this->clearUserSidebarCache($userId);

        return ['success' => 'Usuario eliminado del proyecto.'];
    }

    /**
     * Sync multiple members.
     */
    public function syncMembers(Project $project, array $users): array
    {
        $syncData = [];
        foreach ($users as $userData) {
            $syncData[$userData['user_id']] = ['role' => $userData['role']];
        }

        $changes = $project->users()->syncWithoutDetaching($syncData);
        $idsToClearCache = array_merge($changes['attached'], $changes['updated']);

        if (empty($idsToClearCache)) {
            return ['info' => 'No se realizaron cambios. Los usuarios ya existían con los mismos roles.'];
        }

        foreach ($idsToClearCache as $id) {
            $this->clearUserSidebarCache($id);
        }

        return ['success' => 'Usuarios procesados correctamente.'];
    }

    /**
     * Remove multiple users (Bulk).
     */
public function removeMembersBulk(Project $project, array $userIds): array
    {
        $idsToRemove = array_diff($userIds, [Auth::id()]);

        if (empty($idsToRemove)) {
            return [
                'error' => 'No se han podido eliminar los usuarios seleccionados.', 
                'status' => 422
            ];
        }

        $existingUserIds = $project->users()
            ->whereIn('users.id', $idsToRemove)
            ->pluck('users.id')
            ->toArray();

        if (empty($existingUserIds)) {
            return [
                'info' => 'Los usuarios seleccionados no formaban parte del proyecto.',
                'status' => 404
            ];
        }

        $project->users()->detach($existingUserIds);

        foreach ($existingUserIds as $id) {
            $this->clearUserSidebarCache($id);
        }

        return [
            'success' => 'Usuarios eliminados correctamente.', 
            'status' => 200
        ];
    }

    /**
     * Clear the sidebar cache for a user.
     */
    protected function clearUserSidebarCache(int $userId): void
    {
        Cache::forget(User::getSidebarCacheKeyForId($userId));
    }
}
