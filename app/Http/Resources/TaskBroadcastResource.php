<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskBroadcastResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request = null): array
    {
        $this->loadMissing(['steps', 'assignedUser', 'project']);
        
        $this->loadCount([
            'steps',
            'steps as completed_steps_count' => fn($q) => $q->where('is_completed', true)
        ]);

        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'project' => $this->project ? [
                'id' => $this->project->id,
                'name' => $this->project->name,
            ] : null,
            'assigned_user_id' => $this->assigned_user_id,
            'assigned_user' => $this->assignedUser ? [
                'id' => $this->assignedUser->id,
                'name' => $this->assignedUser->name,
                'initials' => $this->assignedUser->initials,
            ] : null,
            'name' => $this->name,
            'description' => $this->description,
            'has_description' => !empty($this->description),
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'steps_count' => $this->steps_count ?? 0,
            'completed_steps_count' => $this->completed_steps_count ?? 0,
            'steps' => $this->steps->map(fn($step) => [
                'id' => $step->id,
                'name' => $step->name,
                'is_completed' => $step->is_completed,
            ])->toArray(),
        ];
    }
}
