<?php

namespace HardikSolanki\OpenAILaravel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePromptTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'variables' => ['required', 'array'],
            'variables.*.name' => ['required', 'string'],
            'variables.*.type' => ['required', 'string', 'in:string,number,boolean'],
            'variables.*.required' => ['sometimes', 'boolean'],
            'variables.*.default' => ['sometimes'],
            'variables.*.description' => ['sometimes', 'nullable', 'string'],
            'model' => ['nullable', 'string'],
            'is_public' => ['sometimes', 'boolean'],
        ];
    }
}
