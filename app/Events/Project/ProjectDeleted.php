<?php

namespace App\Events\Project;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param int $projectId ID del proyecto eliminado.
     * @param array<int> $memberIds IDs de los miembros a notificar.
     */
    public function __construct(
        public int $projectId,
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

    /**
     * Datos a enviar en el broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'project_id' => $this->projectId,
        ];
    }

    /**
     * Nombre del evento en el frontend.
     */
    public function broadcastAs(): string
    {
        return 'ProjectDeleted';
    }
}
