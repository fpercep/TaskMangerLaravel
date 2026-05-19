<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

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
                ->orWhereHas('users', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                });
        });
    }

    /**
     * Obtener los IDs de todos los miembros del proyecto.
     */
    public function getMemberIds(): array
    {
        return $this->users()->pluck('users.id')->toArray();
    }

    public function getOtherMemberIds(?int $excludeId = null): array
    {
        $excludeId = func_num_args() > 0 ? $excludeId : auth()->id();
        
        if ($excludeId) {
            return array_values(array_diff($this->getMemberIds(), [$excludeId]));
        }

        return $this->getMemberIds();
    }
}
