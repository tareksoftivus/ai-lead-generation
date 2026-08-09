<?php

namespace App\Modules\GoogleMapsSettings\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleMapsSetting extends Model
{
    protected $table = 'google_maps_settings';

    protected $fillable = ['key', 'value'];
}
