<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OAuthProvider extends Model
{
    protected $table = 'oauth_providers';
    protected $fillable = [
        'code',
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
        return 'code';
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'oauth_providers_users', 'oauth_provider_id', 'user_id')
            ->withPivot(['oauth_provider_user_id'])
            ->withTimestamps();
    }

    public static function findByCode(string $code): ?OAuthProvider
    {
        return static::firstWhere([
            'is_active' => true,
            'code' => $code
        ]);
    }

    public static function getActiveProviders(): array
    {
        return static::where('is_active', true)
            ->get()
            ->map(fn($oauthProvider) => [
                'code' => $oauthProvider->code,
                'name' => $oauthProvider->name,
                'url' => route('auth.signin.provider',  $oauthProvider->code)
            ])
            ->toArray();
    }
}
