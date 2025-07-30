<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use App\Enums\UserStatus;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function generateTokens(): array
    {
        if (Auth::check()) {
            $dateTimeNow = Carbon::now()->toImmutable();

            $accessTokenExpiresAt = $dateTimeNow->addMinutes(intval(config('sanctum.expiration')) ?: 2);
            $refreshTokenExpiresAt = $dateTimeNow->addMinutes(intval(config('sanctum.refresh_token_expiration')) ?: 4);

            $accessToken = $this->createToken('access_token', ['*'], $accessTokenExpiresAt);
            $refreshToken = $this->createRefreshToken($accessToken->accessToken->id, $refreshTokenExpiresAt);

            return [
                'access_token' => $accessToken->plainTextToken,
                'refresh_token' => $refreshToken->plainTextToken,
                'expires_in' => $accessTokenExpiresAt->diffInSeconds($dateTimeNow, true),
                'expires_at' => $accessTokenExpiresAt->timestamp,
                'token_type' => 'Bearer',
            ];
        }

        throw new \Exception("Unauthenticated", 401);
    }

    private function createRefreshToken(int $parentId, ?DateTimeInterface $expiresAt = null) : NewAccessToken
    {
        $plainTextToken = $this->generateTokenString();

        $token = $this->tokens()->create([
            'name' => 'refresh_token',
            'token' => hash('sha256', $plainTextToken),
            'abilities' => ['refresh'],
            'expires_at' => $expiresAt,
            'parent_id' => $parentId,
        ]);

        return new NewAccessToken($token, $token->getKey().'|'.$plainTextToken);
    }
}
