<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        'workspace_name',
        'timezone',
        'search_defaults',
        'email_preferences',
    ];

    protected function casts(): array
    {
        return [
            'search_defaults' => 'array',
            'email_preferences' => 'array',
        ];
    }

    public static function defaultsFor(User $user): array
    {
        return [
            'workspace_name' => self::defaultWorkspaceName($user),
            'timezone' => setting('default_timezone', config('app.timezone', 'UTC')) ?: 'UTC',
            'search_defaults' => self::defaultSearchDefaults(),
            'email_preferences' => self::defaultEmailPreferences(),
        ];
    }

    public static function defaultWorkspaceName(User $user): string
    {
        $name = trim((string) $user->name);

        return $name !== '' ? "{$name}'s workspace" : config('app.name', 'LeadAtlas');
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultSearchDefaults(): array
    {
        return [
            'default_location' => '',
            'default_radius' => 10,
            'min_rating' => null,
            'min_reviews' => 10,
            'skip_no_phone' => true,
            'skip_closed' => true,
            'skip_seen' => true,
        ];
    }

    /**
     * @return array<string, bool>
     */
    public static function defaultEmailPreferences(): array
    {
        return [
            'email_search_done' => true,
            'email_low_credits' => true,
            'email_weekly' => false,
            'email_product' => false,
        ];
    }

    public static function forUser(User $user): self
    {
        return self::query()->firstOrCreate(
            ['user_id' => $user->id],
            self::defaultsFor($user)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function mergedSearchDefaults(): array
    {
        return array_merge(self::defaultSearchDefaults(), $this->search_defaults ?? []);
    }

    /**
     * @return array<string, bool>
     */
    public function mergedEmailPreferences(): array
    {
        return array_merge(self::defaultEmailPreferences(), $this->email_preferences ?? []);
    }

    /**
     * @return array<string, mixed>
     */
    public function searchFilters(): array
    {
        $defaults = $this->mergedSearchDefaults();
        $location = trim((string) ($defaults['default_location'] ?? ''));
        $minReviews = $defaults['min_reviews'] ?? null;
        $supportedReviewBucket = in_array((string) $minReviews, ['10', '50', '200'], true);

        $filters = [
            'location' => $location !== '' ? [$location] : [],
            'radius' => (int) ($defaults['default_radius'] ?? 10),
            'min_rating' => $defaults['min_rating'] ?: null,
            'min_reviews' => $supportedReviewBucket ? (string) $minReviews : null,
            'min_reviews_from' => null,
            'has_phone' => (bool) ($defaults['skip_no_phone'] ?? true),
            'skip_owned' => (bool) ($defaults['skip_seen'] ?? true),
            'exclude_keyword' => [],
        ];

        if (! $supportedReviewBucket && $minReviews !== null && $minReviews !== '') {
            $filters['min_reviews'] = 'custom';
            $filters['min_reviews_from'] = (int) $minReviews;
        }

        if (! empty($defaults['skip_closed'])) {
            $filters['exclude_keyword'][] = 'permanently closed';
        }

        return $filters;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
