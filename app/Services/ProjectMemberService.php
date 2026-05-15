<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use App\Events\Project\MemberAddedToProject;
use App\Events\Project\MemberRemovedFromProject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProjectMemberService
{
    /**
     * Add a single member to the project.
     */
    public function addMember(Project $project, int $userId, string $role): array
    {
        if (!$this->authorizeRoleAction($project, null, $role)) {
            return ['error' => 'Solo los administradores pueden asignar roles superiores a Editor.', 'status' => 403];
        }

        if ($this->getProjectRole($project, $userId)) {
            return ['error' => 'El usuario ya es miembro de este proyecto.', 'status' => 409];
        }

        $project->users()->attach($userId, ['role' => $role]);
        $this->triggerPostActionEvents($userId, $project, 'added');

        return ['success' => 'Usuario añadido correctamente.', 'status' => 201];
    }

    /**
     * Update a single member's role.
     */
    public function updateMemberRole(Project $project, int $userId, string $role): array
    {
        $targetUserRole = $this->getProjectRole($project, $userId);

        if (!$this->authorizeRoleAction($project, $targetUserRole, $role)) {
            return ['error' => 'No tienes permisos para asignar este rol o modificar a este usuario.', 'status' => 403];
        }

        $affectedRows = $project->users()->updateExistingPivot($userId, ['role' => $role]);

        if ($affectedRows > 0) {
            $this->clearUserSidebarCache($userId);
            return ['success' => 'Rol actualizado correctamente.', 'status' => 200];
        }

        return ['info' => 'El usuario ya tenía asignado este rol.', 'status' => 200];
    }

    /**
     * Remove a single user from the project.
     */
    public function removeMember(Project $project, int $userId): array
    {
        if ($userId === Auth::id()) {
            return ['error' => 'No puedes eliminarte a ti mismo del proyecto.', 'status' => 403];
        }

        $targetUserRole = $this->getProjectRole($project, $userId);

        if (!$this->authorizeRoleAction($project, $targetUserRole)) {
            return ['error' => 'No tienes permisos para eliminar a este miembro.', 'status' => 403];
        }

        $project->users()->detach($userId);
        $this->triggerPostActionEvents($userId, $project, 'removed');

        return ['success' => 'Usuario eliminado del proyecto.', 'status' => 200];
    }

    /**
     * Sync multiple members.
     */
    public function syncMembers(Project $project, array $users): array
    {
        $syncData = [];
        foreach ($users as $userData) {
            $targetUserRole = $this->getProjectRole($project, $userData['user_id']);
            if ($this->authorizeRoleAction($project, $targetUserRole, $userData['role'])) {
                $syncData[$userData['user_id']] = ['role' => $userData['role']];
            }
        }

        if (empty($syncData)) {
            return ['error' => 'No tienes permisos para procesar a estos usuarios.', 'status' => 403];
        }

        $changes = $project->users()->syncWithoutDetaching($syncData);
        $idsToClearCache = array_merge($changes['attached'], $changes['updated']);

        if (empty($idsToClearCache)) {
            return ['info' => 'No se realizaron cambios. Los usuarios ya existían con los mismos roles.', 'status' => 200];
        }

        foreach ($idsToClearCache as $id) {
            $this->clearUserSidebarCache($id);
        }

        foreach ($changes['attached'] as $attachedId) {
            MemberAddedToProject::dispatch($attachedId, $project->id, $project->name);
        }

        return ['success' => 'Usuarios procesados correctamente.', 'status' => 200];
    }

    /**
     * Remove multiple users (Bulk).
     */
    public function removeMembersBulk(Project $project, array $userIds): array
    {
        $idsToRemove = array_diff($userIds, [Auth::id()]);
        $authorizedIdsToRemove = [];

        foreach ($idsToRemove as $userId) {
            $targetUserRole = $this->getProjectRole($project, $userId);
            if ($targetUserRole && $this->authorizeRoleAction($project, $targetUserRole)) {
                $authorizedIdsToRemove[] = $userId;
            }
        }

        if (empty($authorizedIdsToRemove)) {
            return ['error' => 'No se han podido eliminar los usuarios seleccionados o faltan permisos.', 'status' => 422];
        }

        $project->users()->detach($authorizedIdsToRemove);

        foreach ($authorizedIdsToRemove as $id) {
            $this->triggerPostActionEvents($id, $project, 'removed');
        }

        return ['success' => 'Usuarios eliminados correctamente.', 'status' => 200];
    }

    // =========================================================================
    // MÉTODOS PRIVADOS DE SOPORTE (DRY & KISS)
    // =========================================================================

    /**
     * Obtiene el rol de un usuario de forma optimizada sin hidratar el modelo completo.
     */
    private function getProjectRole(Project $project, int $userId): ?string
    {
        return $project->users()->where('users.id', $userId)->value('project_user.role');
    }

    /**
     * Centraliza la lógica de permisos:
     * Un Admin puede hacer todo.
     * Cualquier otro rol (ej. manager) solo puede modificar a 'editors' y asignar el rol de 'editor'.
     */
    private function authorizeRoleAction(Project $project, ?string $targetRole = null, ?string $newRole = null): bool
    {
        $currentUserRole = $this->getProjectRole($project, Auth::id());

        if ($currentUserRole === 'admin') {
            return true;
        }

        if ($targetRole && $targetRole !== 'editor') {
            return false;
        }

        if ($newRole && $newRole !== 'editor') {
            return false;
        }

        return !is_null($currentUserRole);
    }

    /**
     * Agrupa el despacho de eventos y la limpieza de caché para reducir repeticiones.
     */
    private function triggerPostActionEvents(int $userId, Project $project, string $action): void
    {
        $this->clearUserSidebarCache($userId);

        if ($action === 'added') {
            MemberAddedToProject::dispatch($userId, $project->id, $project->name);
        } elseif ($action === 'removed') {
            MemberRemovedFromProject::dispatch($userId, $project->id, $project->name);
        }
    }

    /**
     * Clear the sidebar cache for a user.
     */
    private function clearUserSidebarCache(int $userId): void
    {
        Cache::forget(User::getSidebarCacheKeyForId($userId));
    }

    /**
     * Determina si el usuario es el único administrador restante del proyecto.
     * Esta función es vital para prevenir que el proyecto se quede sin administradores (orfandad).
     */
    protected function isLastAdmin(Project $project, int $userId): bool
    {
        $adminIds = $project->users()
            ->wherePivot('role', 'admin')
            ->limit(2)
            ->pluck('users.id');

        return $adminIds->count() === 1 && $adminIds->first() === $userId;
    }
}
