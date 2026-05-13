<?php
/* [NEW] TaskAssigned.php */
namespace App\Events\Task;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param array $task Datos de la tarea actualizada.
     * @param array<int> $memberIds IDs de los miembros del proyecto a los que notificar.
     */
    public function __construct(
        public array $task,
        public array $memberIds
    ) {}

    /**
     * Emite a los canales privados de cada miembro del proyecto.
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
            'task' => $this->task,
        ];
    }

    public function broadcastAs(): string
    {
        return 'TaskAssigned';
    }
}
