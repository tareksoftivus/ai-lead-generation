<?php

namespace App\Modules\ApiIntegrations\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserIntegrationConnection extends Model
{
    public const STATUS_CONFIGURED = 'configured';

    public const STATUS_CONNECTED = 'connected';

    public const STATUS_PAUSED = 'paused';

    protected $fillable = [
        'user_id',
        'api_integration_provider_id',
        'account_name',
        'status',
        'settings',
        'synced_leads_count',
        'last_synced_at',
        'configured_at',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'synced_leads_count' => 'integer',
            'last_synced_at' => 'datetime',
            'configured_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(ApiIntegrationProvider::class, 'api_integration_provider_id');
    }

    public function isConnected(): bool
    {
        return $this->status === self::STATUS_CONNECTED;
    }

    public function isConfigured(): bool
    {
        return in_array($this->status, [self::STATUS_CONFIGURED, self::STATUS_CONNECTED], true);
    }
}
