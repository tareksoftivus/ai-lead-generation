<?php

namespace App\Modules\Leads\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveLeadsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'search_run_id' => ['nullable', 'integer', 'exists:search_runs,id'],
            'save_all' => ['nullable', 'boolean'],
            'place_id' => [Rule::requiredIf(fn () => ! $this->boolean('save_all')), 'array', 'min:1'],
            'place_id.*' => ['integer', 'exists:places,id'],
        ];
    }
}
