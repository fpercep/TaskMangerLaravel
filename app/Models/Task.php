<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'parent_id',
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
     * La tarea padre (null si es tarea principal).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    /**
     * Las subtareas de esta tarea.
     */
    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    /**
     * Los usuarios asignados a esta tarea.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * ¿Es una subtarea?
     */
    public function isSubtask(): bool
    {
        return $this->parent_id !== null;
    }

    /**
     * ¿Tiene subtareas?
     */
    public function hasSubtasks(): bool
    {
        return $this->subtasks()->exists();
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
            ->where('due_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled']);
    }
}
