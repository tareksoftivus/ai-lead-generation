<?php

namespace App\Modules\AiTools\Http\Requests;

use App\Modules\AiTools\Models\EmailDraft;
use App\Modules\Leads\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveEmailTemplateRequest extends FormRequest
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
            'draft_id' => ['nullable', 'integer'],
            'lead_id' => ['nullable', 'integer'],
            'template_name' => ['required', 'string', 'max:160'],
            'template_gap' => ['required', Rule::in(['booking', 'site', 'contact', 'reputation', 'conversion', 'any'])],
            'tone' => ['required', Rule::in(['direct', 'warm', 'formal'])],
            'length' => ['required', Rule::in(['short', 'medium', 'long'])],
            'opening' => ['required', Rule::in(['gap', 'praise', 'question'])],
            'subject' => ['required', 'string', 'max:190'],
            'body' => ['required', 'string', 'max:10000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (! $this->filled('draft_id')) {
                $this->validateLead($validator);

                return;
            }

            $exists = EmailDraft::query()
                ->forUser($this->user()->id)
                ->whereKey($this->integer('draft_id'))
                ->exists();

            if (! $exists) {
                $validator->errors()->add('draft_id', __('Choose a valid draft.'));
            }

            $this->validateLead($validator);
        });
    }

    protected function validateLead($validator): void
    {
        if (! $this->filled('lead_id')) {
            return;
        }

        $exists = Lead::query()
            ->forUser($this->user()->id)
            ->whereKey($this->integer('lead_id'))
            ->exists();

        if (! $exists) {
            $validator->errors()->add('lead_id', __('Choose a valid lead.'));
        }
    }
}
