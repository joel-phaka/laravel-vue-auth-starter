<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Events\UserLoggedIn;
use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Models\AuthProvider;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class SocialLoginController extends Controller
{
    public function redirectToProvider(Request $request, AuthProvider $authProvider): BaseResponse
    {
        if (is_local_url(strval($request->query('return_url')))) {
            session(['return_url' => $request->query('return_url')]);
        }

        return Socialite::driver($authProvider->name)
            ->stateless()
            ->redirect();
    }

    public function handleProviderCallback(Request $request, AuthProvider $authProvider): BaseResponse
    {
        $externalUser = Socialite::driver($authProvider->name)
            ->stateless()
            ->user();

        $nameArr = preg_split('/\s+/', $externalUser->getName());
        $firstName = $nameArr[0];
        $lastName = count($nameArr) > 1 ? implode(' ', array_slice($nameArr, 1)) : null;
        $email = $externalUser->getEmail();

        $user = User::firstWhere('email', $email);
        $isNewUser = false;

        if (!$user) {
            $user = User::create([
                'role_id' => Role::findUserRole(UserRole::USER)?->id,
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]);
            $isNewUser = true;
            $user->refresh();
        }

        if ($user?->status == UserStatus::ACTIVE) {
            if ($isNewUser) event(new UserRegistered($user));

            $user->authProviders()->syncWithoutDetaching([
                $authProvider->id => ['auth_provider_user_id' => $externalUser->getId()]
            ]);
        } else {
            return response()->redirectTo('/signin');
        }

        Auth::login($user);

        event(new UserLoggedIn($user));

        $returnUrl = strval(session()->pull('return_url'));
        $returnToPath = parse_url($returnUrl, PHP_URL_PATH) ?: '/';

        return response()->redirectTo($returnToPath);
    }
}
