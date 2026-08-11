<?php

namespace App\Modules\ApiIntegrations\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;

class ApiKeyService
{
    public const TOKEN_NAME_PREFIX = 'leadatlas-api:';

    public const ABILITY_READ = 'leads:read';

    public const ABILITY_SEARCH_WRITE = 'searches:write';

    /**
     * @return Collection<int, PersonalAccessToken>
     */
    public function keysFor(User $user): Collection
    {
        return $user->tokens()
            ->where('name', 'like', self::TOKEN_NAME_PREFIX.'%')
            ->latest()
            ->get();
    }

    public function create(User $user, string $name, string $scope): NewAccessToken
    {
        $abilities = $scope === 'full'
            ? [self::ABILITY_READ, self::ABILITY_SEARCH_WRITE]
            : [self::ABILITY_READ];

        return $user->createToken(self::TOKEN_NAME_PREFIX.$name, $abilities);
    }

    public function displayName(PersonalAccessToken $token): string
    {
        return str($token->name)->after(self::TOKEN_NAME_PREFIX)->toString();
    }

    public function scopeLabel(PersonalAccessToken $token): string
    {
        $abilities = $token->abilities ?? [];

        return in_array(self::ABILITY_SEARCH_WRITE, $abilities, true) ? 'Full access' : 'Read-only';
    }

    public function scopeValue(PersonalAccessToken $token): string
    {
        $abilities = $token->abilities ?? [];

        return in_array(self::ABILITY_SEARCH_WRITE, $abilities, true) ? 'full' : 'read';
    }

    public function preview(PersonalAccessToken $token): string
    {
        return 'pat_'.$token->id.'_'.substr($token->token, 0, 4).'...'.substr($token->token, -4);
    }

    public function isManagedToken(PersonalAccessToken $token): bool
    {
        return str_starts_with($token->name, self::TOKEN_NAME_PREFIX);
    }
}
