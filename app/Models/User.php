<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use App\Enums\UserStatus;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
//use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasApiTokens, HasFactory, Notifiable, MustVerifyEmail;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'email',
        'password',
        'first_name',
        'last_name',
        'email_verified_at',
        'country_code',
        'phone_number',
        'phone_number_verified_at',
        'remember_token',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $with = ['role'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_number_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function getIsActiveAttribute(): bool
    {
        return !!$this->getKey() && $this->status === UserStatus::ACTIVE;
    }

    public function oauthProviders(): BelongsToMany
    {
        return $this->belongsToMany(OAuthProvider::class, 'oauth_providers_users', 'user_id', 'oauth_provider_id')
            ->withPivot(['oauth_provider_user_id'])
            ->withTimestamps();
    }

    public function logins(): HasMany
    {
        return $this->hasMany(UserLogin::class);
    }
}
