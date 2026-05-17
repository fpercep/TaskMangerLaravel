<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $task = $this->route('task'); // Obtenemos la tarea desde la URL

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'status' => ['sometimes', 'required', 'string', 'in:pending,in_progress,completed,cancelled'],
            'priority' => ['sometimes', 'required', 'string', 'in:low,medium,high,urgent'],
            'description' => ['sometimes', 'nullable', 'string'],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'assigned_user_id' => [
                'sometimes',
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($task) {
                    if ($value && !$task->project->users()->where('users.id', $value)->exists()) {
                        $fail('El usuario asignado debe ser miembro del proyecto.');
                    }
                },
            ],
        ];
    }
}
