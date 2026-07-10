<?php

namespace HardikSolanki\OpenAILaravel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => ['required', 'string', 'in:user,system'],
            'content' => ['required', 'string'],
            'stream' => ['sometimes', 'boolean'],
        ];
    }
}
