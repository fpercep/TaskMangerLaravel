<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'visibility',
    ];

    /**
     * Las tareas del proyecto (solo tareas principales, sin subtareas).
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Solo las tareas raíz (sin padre).
     */
    public function rootTasks(): HasMany
    {
        return $this->hasMany(Task::class)->whereNull('parent_id');
    }

    /**
     * Los usuarios asignados directamente al proyecto (con su rol).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Usuarios con rol viewer en el proyecto.
     */
    public function viewers(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'viewer');
    }

    /**
     * Usuarios con rol editor en el proyecto.
     */
    public function editors(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'editor');
    }

    /**
     * Usuarios con rol manager en el proyecto.
     */
    public function managers(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'manager');
    }

    /**
     * Scope: proyectos activos.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: proyectos públicos.
     */
    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    /**
     * Scope: proyectos visibles para un usuario.
     * Incluye públicos + asignados directamente.
     */
    public function scopeVisibleTo($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('visibility', 'public')
                ->orWhereIn('id', $user->projects()->pluck('projects.id'));
        });
    }
}
