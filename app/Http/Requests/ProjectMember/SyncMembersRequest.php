<?php

namespace App\Http\Requests\ProjectMember;

use Illuminate\Foundation\Http\FormRequest;

class SyncMembersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'users' => ['required', 'array'],
            // Validamos que cada elemento del array tenga la estructura correcta
            'users.*.user_id' => ['required', 'integer', 'exists:users,id'],
            'users.*.role' => ['required', 'string', 'in:admin,editor,viewer'],
        ];
    }
}
