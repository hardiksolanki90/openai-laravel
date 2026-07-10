<?php

namespace HardikSolanki\OpenAILaravel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'system_prompt' => ['nullable', 'string'],
            'model' => ['nullable', 'string', 'in:'.implode(',', array_keys(config('openai.models', [])))],
            'api_key_id' => ['nullable', 'integer', 'exists:api_keys,id'],
        ];
    }
}
