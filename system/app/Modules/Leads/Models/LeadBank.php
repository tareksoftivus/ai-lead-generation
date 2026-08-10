<?php

namespace App\Modules\Leads\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadBank extends Model
{
    protected $table = 'leads_bank';

    protected $fillable = [
        'place_id',
        'google_place_id',
        'name',
        'formatted_address',
        'business_type',
        'business_type_normalized',
        'location',
        'location_normalized',
        'phone',
        'website',
        'google_category',
        'rating',
        'review_count',
        'searchable_text_normalized',
        'location_text_normalized',
        'raw_response',
        'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'float',
            'review_count' => 'integer',
            'raw_response' => 'array',
            'last_seen_at' => 'datetime',
        ];
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }
}
