<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'has_description' => !empty($this->description),
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
            'steps' => $this->whenLoaded('steps', function () {
                return $this->steps->map(fn($step) => [
                    'id' => $step->id,
                    'name' => $step->name,
                    'is_completed' => $step->is_completed,
                ]);
            }),
            'steps_count' => $this->steps_count ?? 0,
            'completed_steps_count' => $this->completed_steps_count ?? 0,
            'assigned_user_id' => $this->assigned_user_id,
            'assigned_user' => $this->whenLoaded('assignedUser', function () {
                return [
                    'id' => $this->assignedUser->id,
                    'name' => $this->assignedUser->name,
                    'initials' => $this->assignedUser->initials,
                ];
            }),
        ];
    }
}
