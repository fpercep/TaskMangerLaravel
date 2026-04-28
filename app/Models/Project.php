<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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
     * Las tareas del proyecto.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
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
     * Usuarios con rol admin en el proyecto (creadores).
     */
    public function admins(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'admin');
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
                ->orWhereExists(function ($subquery) use ($user) {
                    $subquery->select(DB::raw(1))
                        ->from('project_user')
                        ->whereColumn('project_user.project_id', 'projects.id')
                        ->where('project_user.user_id', $user->id);
                });
        });
    }
}
