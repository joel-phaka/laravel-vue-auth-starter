<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AuthProvider extends Model
{
    protected $fillable = [
        'name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function resolveRouteBindingQuery($query, $value, $field = null): Builder
    {
        return parent::resolveRouteBindingQuery($query, $value, $field)
            ->where('is_active', true);
    }

    public function getRouteKeyName(): string
    {
        return 'name';
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'auth_providers_users', 'auth_provider_id', 'user_id')
            ->withPivot(['auth_provider_user_id'])
            ->withTimestamps();
    }
}
