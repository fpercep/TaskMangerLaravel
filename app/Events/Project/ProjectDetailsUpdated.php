<?php

namespace App\Events\Project;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectDetailsUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param int $projectId
     * @param string $projectName
     * @param array<int> $memberIds IDs de los miembros a notificar.
     */
    public function __construct(
        public int $projectId,
        public string $projectName,
        public array $memberIds
    ) {}

    /**
     * Broadcast al canal privado de cada miembro del proyecto.
     */
    public function broadcastOn(): array
    {
        return collect($this->memberIds)->map(function ($id) {
            return new PrivateChannel('App.Models.User.' . $id);
        })->toArray();
    }

    public function broadcastWith(): array
    {
        return [
            'project_id' => $this->projectId,
            'project_name' => $this->projectName,
        ];
    }

    public function broadcastAs(): string
    {
        return 'ProjectDetailsUpdated';
    }
}
