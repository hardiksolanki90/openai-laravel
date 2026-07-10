<?php

namespace HardikSolanki\OpenAILaravel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UseTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'conversation_id' => ['required', 'integer', 'exists:conversations,id'],
            'variables' => ['required', 'array'],
        ];
    }
}
