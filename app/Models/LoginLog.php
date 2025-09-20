<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginLog extends Model
{
    protected $fillable = [
        'user_id',
        'auth_type',
        'auth_type_id',
        'ip',
        'user_agent',
        'device_platform',
        'location',
        'country_code',
        'region_code',
        'area_code',
        'zip_code',
        'timezone',
        'created_at',
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
