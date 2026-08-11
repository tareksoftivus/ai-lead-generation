<?php

namespace App\Modules\ApiIntegrations\Services;

use App\Models\User;
use App\Modules\ApiIntegrations\Models\ApiIntegrationProvider;
use App\Modules\ApiIntegrations\Models\UserIntegrationConnection;
use Illuminate\Support\Collection;

class IntegrationProviderCatalog
{
    /**
     * @return Collection<int, ApiIntegrationProvider>
     */
    public function providersForUser(User $user): Collection
    {
        return ApiIntegrationProvider::query()
            ->where(function ($query) use ($user): void {
                $query->active()
                    ->orWhereHas('connections', fn ($connectionQuery) => $connectionQuery->where('user_id', $user->id));
            })
            ->ordered()
            ->with(['connections' => fn ($query) => $query->where('user_id', $user->id)])
            ->get();
    }

    public function connect(User $user, ApiIntegrationProvider $provider, array $attributes): UserIntegrationConnection
    {
        $connection = UserIntegrationConnection::query()->firstOrNew([
            'user_id' => $user->id,
            'api_integration_provider_id' => $provider->id,
        ]);

        $connection->fill([
            'account_name' => $attributes['account_name'] ?? null,
            'status' => UserIntegrationConnection::STATUS_CONFIGURED,
            'settings' => [
                'sync_new_leads' => (bool) ($attributes['sync_new_leads'] ?? false),
                'minimum_score' => (int) ($attributes['minimum_score'] ?? 0),
            ],
        ]);

        if (! $connection->exists) {
            $connection->configured_at = now();
        }

        $connection->save();

        return $connection;
    }
}
