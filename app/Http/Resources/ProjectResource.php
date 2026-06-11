<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'created_at' => $this->created_at->format('Y-m-d'),
            // Cargamos las tareas solo si están presentes para evitar N+1 accidentales
            'tasks' => TaskResource::collection($this->whenLoaded('tasks')),
        ];
    }
}
