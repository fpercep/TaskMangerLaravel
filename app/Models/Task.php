<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'assigned_user_id',
        'name',
        'description',
        'status',
        'priority',
        'start_date',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date' => 'date',
        ];
    }

    /**
     * El proyecto al que pertenece la tarea.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Los pasos (checklist) de esta tarea.
     */
    public function steps(): HasMany
    {
        return $this->hasMany(TaskStep::class)->orderBy('position');
    }

    /**
     * El usuario asignado a esta tarea.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    /**
     * Scope: tareas pendientes.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: tareas en progreso.
     */
    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope: tareas completadas.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: filtrar por prioridad.
     */
    public function scopeWithPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope: tareas vencidas (fecha pasada y no completadas).
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereNotNull('due_date')
            ->where('due_date', '<', today())
            ->whereNotIn('status', ['completed', 'cancelled']);
    }

    /**
     * Formatea la tarea para su envío a través de WebSockets.
     */
    public function toBroadcastArray(): array
    {
        $this->load(['steps', 'assignedUser']);
        
        $this->loadCount([
            'steps',
            'steps as completed_steps_count' => fn($q) => $q->where('is_completed', true)
        ]);

        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
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
