<?php

namespace HardikSolanki\OpenAILaravel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'monthly_limit' => ['required', 'numeric', 'min:0'],
            'warning_threshold' => ['required', 'numeric', 'min:0'],
            'block_on_limit' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'month_starts_at' => ['sometimes', 'integer', 'min:1', 'max:28'],
        ];
    }
}
