<?php

namespace App\Modules\Leads\Services;

class SearchPromptParser
{
    protected const MAX_REQUESTED_COUNT = 30;

    /**
     * @return array<string, mixed>
     */
    public function parse(string $prompt): array
    {
        $text = trim(preg_replace('/\s+/', ' ', $prompt) ?? '');

        if ($text === '') {
            return [];
        }

        $filters = ['prompt' => $text];

        if ($keyword = $this->keyword($text)) {
            $filters['keyword'] = [$keyword];
        }

        if ($location = $this->location($text)) {
            $filters['location'] = [$location];
        }

        if ($reviews = $this->minimumReviews($text)) {
            $filters['min_reviews'] = 'custom';
            $filters['min_reviews_from'] = $reviews;
        }

        if ($requested = $this->requestedCount($text)) {
            $filters['requested_count'] = $requested;
        }

        return $filters;
    }

    protected function keyword(string $text): ?string
    {
        $head = preg_split('/\s+in\s+/i', $text, 2)[0] ?? '';
        $head = preg_replace('/^\s*(find|get|generate|search for|show me)\s+/i', '', $head) ?? $head;
        $head = preg_replace('/^\s*(the|a|an)\s+/i', '', $head) ?? $head;
        $head = trim($head, " \t\n\r\0\x0B.,");

        return $head !== '' ? $head : null;
    }

    protected function location(string $text): ?string
    {
        if (! preg_match('/\s+in\s+(.+?)(?:\s+with\s+|\s+that\s+|\s+(?:at\s*least|atleast|atleat|atlest|minimum|min)\s+|\s+\d+\s*\+?\s*(?:reviews?|leads?|businesses?|results?)|[.?!]?$)/i', $text, $matches)) {
            return null;
        }

        $location = trim($matches[1], " \t\n\r\0\x0B.,");

        return $location !== '' ? $location : null;
    }

    protected function minimumReviews(string $text): ?int
    {
        if (! preg_match('/(?:with\s+|at\s*least\s+|atleast\s+|atleat\s+|atlest\s+|minimum\s+|min\s+)?(\d{1,5})\s*\+?\s*reviews?/i', $text, $matches)) {
            return null;
        }

        return max(0, (int) $matches[1]);
    }

    protected function requestedCount(string $text): ?int
    {
        if (! preg_match('/(?:at\s*least\s+|atleast\s+|atleat\s+|atlest\s+|minimum\s+|min\s+)?(\d{1,3})\s*\+?\s*(?:leads?|businesses?|results?)/i', $text, $matches)) {
            return null;
        }

        return min(self::MAX_REQUESTED_COUNT, max(1, (int) $matches[1]));
    }
}
