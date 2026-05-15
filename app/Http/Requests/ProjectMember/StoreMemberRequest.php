<?php

namespace App\Http\Requests\ProjectMember;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización ya la maneja el controlador
    }

    public function rules(): array
    {
        return [
            // El usuario debe existir en la BD
            'user_id' => ['required', 'integer', 'exists:users,id'],
            // Ajusta estos roles según los que uses en tu sistema
            'role' => ['required', 'string', 'in:admin,editor,viewer'],
        ];
    }
}
