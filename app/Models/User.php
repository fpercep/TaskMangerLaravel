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
     * @var array<string, string>
     */
    protected $appends = ['initials'];


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
     * Las tareas asignadas al usuario.
     */
    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Task::class, 'assigned_user_id');
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
     * Scope para buscar usuarios por nombre o email de forma segura y agrupada.
     */
    public function scopeSearch($query, ?string $term)
    {
        return $query->when($term, function ($q) use ($term) {
            $q->where(function ($inner) use ($term) {
                $inner->where('name', 'like', "%{$term}%")
                      ->orWhere('email', 'like', "%{$term}%");
            });
        });
    }

    /**
     * Obtener las iniciales del nombre del usuario.
     */
    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', $this->name))
            ->map(fn($n) => mb_substr($n, 0, 1))
            ->take(2)
            ->implode('');
    }

    /**
     * Clave de caché para los proyectos del sidebar (versión estática para optimización).
     */
    public static function getSidebarCacheKeyForId($id): string
    {
        return "sidebar_projects_{$id}";
    }

    /**
     * Clave de caché para los proyectos del sidebar de este usuario.
     */
    public function sidebarCacheKey(): string
    {
        return self::getSidebarCacheKeyForId($this->id);
    }
}
