<?php

namespace App\Modules\ApiIntegrations\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreIntegrationConnectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_name' => ['nullable', 'string', 'max:120'],
            'sync_new_leads' => ['required', 'boolean'],
            'minimum_score' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sync_new_leads' => $this->boolean('sync_new_leads'),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $provider = $this->route('provider') ?: $this->route('connection')?->provider;

            if (! $provider?->requires_configuration) {
                return;
            }

            if (trim((string) $this->input('account_name')) === '') {
                $validator->errors()->add('account_name', __('Add the account, sheet, channel, or label for this integration.'));
            }
        });
    }
}
