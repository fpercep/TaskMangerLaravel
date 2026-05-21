<?php

namespace App\Events\Project;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $userId,
        public int $projectId,
        public string $projectName,
        public string $role,
        public array $memberIds
    ) {}

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
            'user_id' => $this->userId,
            'role' => $this->role,
        ];
    }

    public function broadcastAs(): string
    {
        return 'MemberUpdated';
    }
}
