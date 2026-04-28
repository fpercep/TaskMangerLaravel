<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskStep extends Model
{
    use HasFactory;

    /**
     * El usuario solicitó solo fecha de creación. Desactivamos updated_at nativamente.
     */
    const UPDATED_AT = null;

    protected $fillable = [
        'task_id',
        'name',
        'is_completed',
        'position',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * La tarea a la que pertenece este paso.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
