<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\UserStatus;
use App\Events\UserLoggedIn;
use App\Exceptions\AccessTokenException;
use App\Helpers\AuthUtils;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RefreshTokenRequest;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use PeterPetrus\Auth\PassportToken;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        abort_if(
            !Auth::attempt($request->only('email', 'password'), $request->boolean('remember_me')),
            response()
                ->json([
                    'message' => 'Unauthorized',
                    'error_code' => 'auth_invalid_credentials'
                ])
                ->unauthorized()
        );

        event(new UserLoggedIn(Auth::user()));

        return response()->json(Auth::user());
    }

    public function user(): JsonResponse
    {
        return response()->json(Auth::user());
    }

    public function logout(Request $request): JsonResponse
    {
        $guard = Auth::getDefaultDriver();

        if ($guard === 'sanctum') {
            Auth::guard('web')->logout();
            session()->invalidate();
            session()->regenerateToken();
        } else if ($guard === 'api') {
            auth()->user()->token()->revoke();
        }

        return response()
            ->json(['success' => true]);
    }

    /**
     * @throws AccessTokenException
     * @throws ConnectionException
     * @throws RequestException
     */
    private function getTokens(array $credentials): array
    {
        $credentials['grant_type'] ??= '';

        if (empty($credentials) || !in_array($credentials['grant_type'], ['password', 'refresh_token'])) {
            throw new InvalidArgumentException('grant_type is required and must be one of "password", "refresh_token".');
        }

        if ($credentials['grant_type'] == 'password') {
            $credentials['username'] = $credentials['username'] ?? $credentials['email'] ?? null;
            data_forget($credentials, 'email');

            if (empty($credentials['username']) || empty($credentials['password'])) {
                $usernameField = !empty($credentials['username']) ? 'username' : 'email';

                throw new InvalidArgumentException("{$usernameField} and password are required.");
            }
        } else {
            if (empty($credentials['refresh_token'])) {
                throw new InvalidArgumentException('refresh_token is required.');
            }
        }

        $urls = [
            'password' => route('api.oauth.token'),
            'refresh_token' => route('api.oauth.token.refresh')
        ];

        $oauthUrl = $urls[$credentials['grant_type']];

        $credentials = [
            ...$credentials,
            'client_id' => config('passport.password_grant_client.id'),
            'client_secret' => config('passport.password_grant_client.secret'),
        ];

        $options = [
            'verify' => !config('app.debug')
        ];

        if (config('app.debug')) {
            $options['timeout'] = 240;
        }

        $response = Http::withOptions($options)
            ->acceptJson()
            ->post($oauthUrl, $credentials);

        if ($response->failed()) {
            if ($response->status() == 400) {
                throw new AccessTokenException($response->json());
            } else {
                throw $response->toException();
            }
        }

        $tokenData = $response->json();
        $user = AuthUtils::findUserByAccessToken($tokenData['access_token']);

        if (!$user) {
            throw new AccessTokenException();
        } else if (!$user->is_active) {
            throw new AccessTokenException(
                reason: match ($user->status) {
                    UserStatus::INACTIVE => 'auth_user_inactive',
                    UserStatus::SUSPENDED => 'auth_user_suspended',
                    UserStatus::BANNED => 'auth_user_banned',
                    default => 'unauthorized'
                }
            );
        }

        $passportToken = new PassportToken($tokenData['access_token']);
        $tokenData['expires_at'] = Carbon::parse($passportToken->expires_at)->timestamp;

        return [
            'token_type' => $tokenData['token_type'],
            'expires_in' => $tokenData['expires_in'],
            'expires_at' => $tokenData['expires_at'],
            'access_token' => $tokenData['access_token'],
            'refresh_token' => $tokenData['refresh_token'],
            'user' => $user,
        ];
    }

    /**
     * @throws RequestException
     * @throws AccessTokenException
     * @throws ConnectionException
     */
    public function issueToken(LoginRequest $request): JsonResponse
    {
        $credentials = [
            'grant_type' => 'password',
            ...$request->only(['email', 'password'])
        ];

        $tokens = $this->getTokens($credentials);

        Auth::login($tokens['user']);

        event(new UserLoggedIn($tokens['user']));

        return response()->json(Arr::except($tokens, ['user']));
    }

    /**
     * @throws RequestException
     * @throws AccessTokenException
     * @throws ConnectionException
     */
    public function refreshToken(RefreshTokenRequest $request): JsonResponse
    {
        $credentials = [
            'grant_type' => 'refresh_token',
            ...$request->only('refresh_token')
        ];

        $tokens = $this->getTokens($credentials);

        return response()->json(Arr::except($tokens, ['user']));
    }
}
