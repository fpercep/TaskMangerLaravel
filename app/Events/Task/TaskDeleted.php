<?php

namespace App\Events\Task;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param int $taskId ID of the deleted task.
     * @param int $projectId ID of the project the task belonged to.
     * @param array<int> $memberIds IDs of project members to notify.
     */
    public function __construct(
        public int $taskId,
        public int $projectId,
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
            'task_id' => $this->taskId,
            'project_id' => $this->projectId,
        ];
    }

    public function broadcastAs(): string
    {
        return 'TaskDeleted';
    }
}
