<?php

namespace App\Modules\Leads\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'place_id' => ['required', 'array', 'min:1'],
            'place_id.*' => ['integer', 'exists:places,id'],
        ];
    }
}
