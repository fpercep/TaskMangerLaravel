<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Los equipos a los que pertenece el usuario (con su rol).
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Los equipos donde el usuario es dueño.
     */
    public function ownedTeams(): BelongsToMany
    {
        return $this->teams()->wherePivot('role', 'owner');
    }

    /**
     * Las tareas asignadas al usuario.
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class)->withTimestamps();
    }

    /**
     * Los proyectos asignados directamente al usuario (con su rol).
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Comprobar si el usuario tiene un rol específico en un equipo.
     */
    public function hasRoleInTeam(Team $team, string $role): bool
    {
        return $this->teams()
            ->where('teams.id', $team->id)
            ->wherePivot('role', $role)
            ->exists();
    }

    /**
     * Comprobar si el usuario es miembro de un equipo (cualquier rol).
     */
    public function isMemberOf(Team $team): bool
    {
        return $this->teams()->where('teams.id', $team->id)->exists();
    }

    /**
     * Comprobar si el usuario es owner o admin de un equipo.
     */
    public function canManageTeam(Team $team): bool
    {
        return $this->teams()
            ->where('teams.id', $team->id)
            ->wherePivotIn('role', ['owner', 'admin'])
            ->exists();
    }
}
