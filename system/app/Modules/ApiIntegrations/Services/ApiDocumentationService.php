<?php

namespace App\Modules\ApiIntegrations\Services;

class ApiDocumentationService
{
    public function baseUrl(): string
    {
        return url('/api/v1');
    }

    public function rateLimit(): int
    {
        return 120;
    }

    /**
     * @return array<int, array{name: string, type: string, description: string}>
     */
    public function leadQueryParameters(): array
    {
        return [
            ['name' => 'list_id', 'type' => 'integer', 'description' => 'Only leads on one list.'],
            ['name' => 'status', 'type' => 'string', 'description' => 'new, contacted, replied, qualified, or lost.'],
            ['name' => 'min_score', 'type' => 'integer', 'description' => '0-100. Leads scoring at least this.'],
            ['name' => 'limit', 'type' => 'integer', 'description' => '1-100, default 25.'],
            ['name' => 'cursor', 'type' => 'integer', 'description' => 'The next_cursor from the last response.'],
        ];
    }

    /**
     * @return array<int, array{code: int, means: string, action: string}>
     */
    public function errors(): array
    {
        return [
            ['code' => 401, 'means' => 'Key missing, wrong, or revoked.', 'action' => 'Check the bearer token header.'],
            ['code' => 403, 'means' => 'The key does not have the required ability.', 'action' => 'Create a full-access key when write access is needed.'],
            ['code' => 404, 'means' => 'The resource does not exist for this account.', 'action' => 'Check the id and account ownership.'],
            ['code' => 422, 'means' => 'A parameter is missing or invalid.', 'action' => 'Read the validation errors in the response body.'],
            ['code' => 429, 'means' => 'Too many requests.', 'action' => 'Back off and retry later.'],
        ];
    }
}
