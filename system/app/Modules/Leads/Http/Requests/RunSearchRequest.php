<?php

namespace App\Modules\Leads\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RunSearchRequest extends FormRequest
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
            'keyword' => ['required', 'array', 'min:1'],
            'keyword.*' => ['string', 'max:100'],
            'location' => ['required', 'array', 'min:1'],
            'location.*' => ['string', 'max:255'],
            'radius' => ['nullable', 'integer', 'min:1', 'max:50'],
            'exclude_keyword' => ['nullable', 'array'],
            'exclude_keyword.*' => ['string', 'max:100'],
            'min_rating' => ['nullable', 'numeric', 'in:3,4,4.5'],
            'min_reviews' => ['nullable'],
            'min_reviews_from' => ['nullable', 'integer', 'min:0'],
            'min_reviews_to' => ['nullable', 'integer', 'min:0'],
            'has_website' => ['nullable', 'boolean'],
            'has_phone' => ['nullable', 'boolean'],
            'category' => ['nullable', 'array'],
            'category.*' => ['string', 'max:100'],
            'skip_owned' => ['nullable', 'boolean'],
            'one_per_business' => ['nullable', 'boolean'],
            'name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
