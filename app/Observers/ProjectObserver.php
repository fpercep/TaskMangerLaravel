<?php

namespace App\Observers;

use App\Models\Project;
use App\Events\Project\ProjectDetailsUpdated;
use App\Events\Project\ProjectDeleted;
use App\Services\SidebarCacheService;

class ProjectObserver
{
    /**
     * Se ejecuta automáticamente después de que un proyecto es actualizado.
     */
    public function updated(Project $project): void
    {
        // 1. Limpiar caché de todos los miembros
        SidebarCacheService::forgetForMembers($project->getMemberIds());

        // 2. Notificar a los demás miembros
        $otherMemberIds = $project->getOtherMemberIds(auth()->id());
        if (!empty($otherMemberIds)) {
            ProjectDetailsUpdated::dispatch(
                $project->id, 
                $project->name, 
                $otherMemberIds
            );
        }
    }

    /**
     * Se ejecuta automáticamente después de que un proyecto es eliminado.
     */
    public function deleted(Project $project): void
    {
        // 1. Limpiar caché
        SidebarCacheService::forgetForMembers($project->getMemberIds());

        // 2. Notificar eliminación
        $otherMemberIds = $project->getOtherMemberIds(auth()->id());
        if (!empty($otherMemberIds)) {
            ProjectDeleted::dispatch($project->id, $otherMemberIds);
        }
    }
}
