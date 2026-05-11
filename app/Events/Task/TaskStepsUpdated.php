<?php

namespace App\Events\Task;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskStepsUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param array $task Data of the task whose steps were updated.
     * @param array<int> $memberIds IDs of project members to notify.
     */
    public function __construct(
        public array $task,
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
        ];
    }

    public function broadcastAs(): string
    {
        return 'TaskStepsUpdated';
    }
}
