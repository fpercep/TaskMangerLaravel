<?php

namespace App\Services;

use App\Models\Project;
use App\Events\Project\MemberAddedToProject;
use App\Events\Project\MemberRemovedFromProject;
use App\Services\SidebarCacheService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use App\Events\Project\MemberUpdated;

class ProjectMemberService
{
    /**
     * Add a single member to the project.
     */
    public function addMember(Project $project, int $userId, string $role): void
    {
        if (!$this->authorizeRoleAction($project, null, $role)) {
            throw new AuthorizationException('Solo los administradores pueden asignar roles superiores a Editor.');
        }

        if ($this->getProjectRole($project, $userId)) {
            throw ValidationException::withMessages(['user_id' => 'El usuario ya es miembro de este proyecto.']);
        }

        $project->users()->attach($userId, ['role' => $role]);
        $this->triggerPostActionEvents($userId, $project, 'added');
    }

    /**
     * Update a single member's role.
     */
    public function updateMemberRole(Project $project, int $userId, string $role): void
    {
        $targetUserRole = $this->getProjectRole($project, $userId);

        if (!$this->authorizeRoleAction($project, $targetUserRole, $role)) {
            throw new AuthorizationException('No tienes permisos para asignar este rol o modificar a este usuario.');
        }

        // Protección: Evita que el único admin se baje el nivel a sí mismo u otro lo haga
        if ($targetUserRole === 'admin' && $role !== 'admin' && $this->isLastAdmin($project, $userId)) {
            throw ValidationException::withMessages(['role' => 'No puedes quitarle el rol al único administrador del proyecto.']);
        }

        $affectedRows = $project->users()->updateExistingPivot($userId, ['role' => $role]);

        if ($affectedRows > 0) {
            SidebarCacheService::forget($userId);
            
            $memberIds = $project->users()->pluck('users.id')->toArray();
            MemberUpdated::dispatch($userId, $project->id, $project->name, $role, $memberIds);
        }
    }

    /**
     * Remove a single user from the project.
     */
    public function removeMember(Project $project, int $userId): void
    {
        if ($userId === Auth::id()) {
            throw ValidationException::withMessages(['user_id' => 'No puedes eliminarte a ti mismo del proyecto.']);
        }

        $targetUserRole = $this->getProjectRole($project, $userId);

        if (!$this->authorizeRoleAction($project, $targetUserRole)) {
            throw new AuthorizationException('No tienes permisos para eliminar a este miembro.');
        }

        // Protección: Evita borrar al último admin
        if ($targetUserRole === 'admin' && $this->isLastAdmin($project, $userId)) {
            throw ValidationException::withMessages(['user_id' => 'No puedes eliminar al único administrador del proyecto.']);
        }

        $project->users()->detach($userId);
        $this->triggerPostActionEvents($userId, $project, 'removed');
    }

    /**
     * Sync multiple members.
     */
    public function syncMembers(Project $project, array $users): void
    {
        $syncData = [];
        foreach ($users as $userData) {
            $targetUserRole = $this->getProjectRole($project, $userData['user_id']);
            
            if ($this->authorizeRoleAction($project, $targetUserRole, $userData['role'])) {
                // Protección durante la sincronización masiva
                if ($targetUserRole === 'admin' && $userData['role'] !== 'admin' && $this->isLastAdmin($project, $userData['user_id'])) {
                    throw ValidationException::withMessages(['users' => 'No puedes quitar el rol de administrador al único admin del proyecto.']);
                }
                
                $syncData[$userData['user_id']] = ['role' => $userData['role']];
            }
        }

        if (empty($syncData)) {
            throw new AuthorizationException('No tienes permisos para procesar a estos usuarios.');
        }

        $changes = $project->users()->syncWithoutDetaching($syncData);
        $idsToClearCache = array_merge($changes['attached'], $changes['updated']);

        foreach ($idsToClearCache as $id) {
            SidebarCacheService::forget($id);
        }

        foreach ($changes['attached'] as $attachedId) {
            MemberAddedToProject::dispatch($attachedId, $project->id, $project->name);
        }

        $memberIds = $project->users()->pluck('users.id')->toArray();
        foreach ($changes['updated'] as $updatedId) {
            $role = $syncData[$updatedId]['role'] ?? null;
            if ($role) {
                MemberUpdated::dispatch($updatedId, $project->id, $project->name, $role, $memberIds);
            }
        }
    }

    /**
     * Remove multiple users (Bulk).
     */
    public function removeMembersBulk(Project $project, array $userIds): void
    {
        $idsToRemove = array_diff($userIds, [Auth::id()]);
        $authorizedIdsToRemove = [];

        foreach ($idsToRemove as $userId) {
            $targetUserRole = $this->getProjectRole($project, $userId);
            
            // Ignoramos al último admin en eliminación masiva
            if ($targetUserRole === 'admin' && $this->isLastAdmin($project, $userId)) {
                continue; 
            }

            if ($targetUserRole && $this->authorizeRoleAction($project, $targetUserRole)) {
                $authorizedIdsToRemove[] = $userId;
            }
        }

        if (empty($authorizedIdsToRemove)) {
            throw ValidationException::withMessages(['user_ids' => 'No se han podido eliminar los usuarios seleccionados o faltan permisos.']);
        }

        $project->users()->detach($authorizedIdsToRemove);

        foreach ($authorizedIdsToRemove as $id) {
            $this->triggerPostActionEvents($id, $project, 'removed');
        }
    }

    /**
     * Leave the project.
     */
    public function leaveProject(Project $project, int $userId): bool
    {
        $userRole = $this->getProjectRole($project, $userId);

        // Si no es miembro, no hacer nada
        if (!$userRole) {
            return false;
        }

        // Si es admin y es el último admin, bloquear
        if ($userRole === 'admin' && $this->isLastAdmin($project, $userId)) {
            return false;
        }

        // Desasignar tareas del usuario en este proyecto
        $project->tasks()->where('assigned_user_id', $userId)->update(['assigned_user_id' => null]);

        // Detach del proyecto
        $project->users()->detach($userId);
        $this->triggerPostActionEvents($userId, $project, 'removed');

        return true;
    }

    // =========================================================================
    // MÉTODOS PRIVADOS DE SOPORTE
    // =========================================================================

    private function getProjectRole(Project $project, int $userId): ?string
    {
        return $project->users()->where('users.id', $userId)->value('project_user.role');
    }

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

    private function triggerPostActionEvents(int $userId, Project $project, string $action): void
    {
        SidebarCacheService::forget($userId);

        if ($action === 'added') {
            MemberAddedToProject::dispatch($userId, $project->id, $project->name);
        } elseif ($action === 'removed') {
            MemberRemovedFromProject::dispatch($userId, $project->id, $project->name);
        }
    }

    protected function isLastAdmin(Project $project, int $userId): bool
    {
        $adminIds = $project->users()
            ->wherePivot('role', 'admin')
            ->limit(2)
            ->pluck('users.id');

        return $adminIds->count() === 1 && $adminIds->first() === $userId;
    }
}
