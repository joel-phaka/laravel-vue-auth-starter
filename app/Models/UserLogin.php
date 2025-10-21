<?php

namespace App\Models;

use App\Enums\AuthState;
use App\Enums\AuthType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLogin extends Model
{
    protected $fillable = [
        'user_id',
        'auth_state',
        'auth_type',
        'auth_type_id',
        'oauth_provider_id',
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

    protected $casts = [
        'auth_state' => AuthState::class,
        'auth_type' => AuthType::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
