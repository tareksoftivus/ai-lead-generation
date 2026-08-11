<?php

namespace App\Modules\AiTools\Http\Requests;

use App\Modules\AiTools\Models\BusinessAnalysisRun;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RunBusinessAnalysisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'skip_analysed' => $this->boolean('skip_analysed'),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'source' => ['required', 'integer', Rule::exists('lead_lists', 'id')->where('user_id', $this->user()->id)],
            'skip_analysed' => ['required', 'boolean'],
            'focus' => ['required', 'string', Rule::in(BusinessAnalysisRun::focuses())],
        ];
    }
}
