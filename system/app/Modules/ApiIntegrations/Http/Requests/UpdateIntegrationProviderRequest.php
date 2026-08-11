<?php

namespace App\Modules\ApiIntegrations\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIntegrationProviderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'category' => ['required', 'string', 'max:80'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:10000'],
            'is_active' => ['nullable', 'boolean'],
            'requires_configuration' => ['nullable', 'boolean'],
            'docs_url' => ['nullable', 'url', 'max:255'],
        ];
    }
}
