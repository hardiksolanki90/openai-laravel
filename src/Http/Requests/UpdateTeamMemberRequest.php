<?php

namespace HardikSolanki\OpenAILaravel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => ['required', 'string', 'in:'.implode(',', config('openai.rbac.roles', ['admin', 'member', 'viewer']))],
            'permissions' => ['sometimes', 'array'],
        ];
    }
}
