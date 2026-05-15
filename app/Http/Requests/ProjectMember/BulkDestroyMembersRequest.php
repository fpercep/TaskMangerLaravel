<?php

namespace App\Http\Requests\ProjectMember;

use Illuminate\Foundation\Http\FormRequest;

class BulkDestroyMembersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
