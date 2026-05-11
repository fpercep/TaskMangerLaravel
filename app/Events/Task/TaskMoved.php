<?php

namespace App\Events\Task;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskMoved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param array $task Data of the moved task.
     * @param string $oldStatus Previous status/column.
     * @param string $newStatus New status/column.
     * @param array<int> $memberIds IDs of project members to notify.
     */
    public function __construct(
        public array $task,
        public string $oldStatus,
        public string $newStatus,
        public array $memberIds
    ) {}

    /**
     * Broadcast to each project member's private channel.
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
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ];
    }

    public function broadcastAs(): string
    {
        return 'TaskMoved';
    }
}
