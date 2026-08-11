<?php

namespace App\Modules\AiTools\Http\Requests;

use App\Modules\AiTools\Models\EmailDraft;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadList;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QueueEmailCampaignRequest extends FormRequest
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
            'lead_id' => ['required', 'integer'],
            'scope_type' => ['required', Rule::in(['one', 'list'])],
            'lead_list_id' => ['nullable', 'integer'],
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
            $userId = $this->user()->id;

            $leadExists = Lead::query()
                ->forUser($userId)
                ->whereKey($this->integer('lead_id'))
                ->exists();

            if (! $leadExists) {
                $validator->errors()->add('lead_id', __('Choose a valid lead.'));
            }

            if ($this->filled('draft_id')) {
                $draftExists = EmailDraft::query()
                    ->forUser($userId)
                    ->whereKey($this->integer('draft_id'))
                    ->exists();

                if (! $draftExists) {
                    $validator->errors()->add('draft_id', __('Choose a valid draft.'));
                }
            }

            if ($this->input('scope_type') !== 'list') {
                return;
            }

            $listExists = LeadList::query()
                ->forUser($userId)
                ->whereKey($this->integer('lead_list_id'))
                ->exists();

            if (! $listExists) {
                $validator->errors()->add('lead_list_id', __('Choose a valid lead list.'));
            }
        });
    }
}
