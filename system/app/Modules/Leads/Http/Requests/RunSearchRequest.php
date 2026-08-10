<?php

namespace App\Modules\Leads\Http\Requests;

use App\Modules\Leads\Services\SearchPromptParser;
use Illuminate\Foundation\Http\FormRequest;

class RunSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $prompt = trim((string) $this->input('prompt', ''));

        if ($prompt === '') {
            return;
        }

        $parsed = app(SearchPromptParser::class)->parse($prompt);

        $this->merge(array_filter([
            'keyword' => $this->filled('keyword') ? $this->input('keyword') : ($parsed['keyword'] ?? null),
            'location' => $this->filled('location') ? $this->input('location') : ($parsed['location'] ?? null),
            'min_reviews' => $this->filled('min_reviews') ? $this->input('min_reviews') : ($parsed['min_reviews'] ?? null),
            'min_reviews_from' => $this->filled('min_reviews_from') ? $this->input('min_reviews_from') : ($parsed['min_reviews_from'] ?? null),
            'requested_count' => $this->filled('requested_count') ? $this->input('requested_count') : ($parsed['requested_count'] ?? null),
        ], fn ($value) => $value !== null && $value !== []));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'prompt' => ['nullable', 'string', 'max:500'],
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
            'requested_count' => ['nullable', 'integer', 'min:1', 'max:30'],
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
