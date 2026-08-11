<?php

namespace App\Modules\ApiIntegrations\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApiKeyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key_name' => ['required', 'string', 'max:80'],
            'key_scope' => ['required', 'in:full,read'],
        ];
    }
}
