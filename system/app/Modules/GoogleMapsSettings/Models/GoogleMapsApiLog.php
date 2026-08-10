<?php

namespace App\Modules\GoogleMapsSettings\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleMapsApiLog extends Model
{
    protected $fillable = [
        'action',
        'method',
        'url',
        'request_payload',
        'status_code',
        'successful',
        'attempts',
        'duration_ms',
        'response_body',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'request_payload' => 'array',
            'response_body' => 'array',
            'status_code' => 'integer',
            'successful' => 'boolean',
            'attempts' => 'integer',
            'duration_ms' => 'integer',
        ];
    }
}
