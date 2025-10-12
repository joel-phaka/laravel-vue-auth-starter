<?php

namespace App\Http\Controllers\Auth;

use App\Enums\AuthType;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Events\UserLoggedIn;
use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Models\OAuthProvider;
use App\Models\Role;
use App\Models\User;
use App\Support\Auth\AuthEventData;
use App\Support\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class SocialLoginController extends Controller
{
    public function redirectToProvider(Request $request, OAuthProvider $oauthProvider): BaseResponse
    {
        if (is_local_url(strval($request->query('return_url')))) {
            session(['return_url' => $request->query('return_url')]);
        }

        return Socialite::driver($oauthProvider->code)
            ->stateless()
            ->redirect();
    }

    public function handleProviderCallback(Request $request, OAuthProvider $oauthProvider): BaseResponse
    {
        $externalUser = Socialite::driver($oauthProvider->code)
            ->stateless()
            ->user();

        $email = $externalUser->getEmail();
        $user = User::firstWhere('email', $email);
        $isNewUser = false;

        if (!$user) {
            if (Feature::isDisabled('user_registration')) {
                return response()->redirectTo('/signin');
            }

            $nameArr = preg_split('/\s+/', $externalUser->getName());
            $firstName = $nameArr[0];
            $lastName = count($nameArr) > 1 ? implode(' ', array_slice($nameArr, 1)) : null;

            $user = User::create([
                'role_id' => Role::findUserRole(UserRole::USER)?->id,
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]);

            $user?->refresh();
            $isNewUser = !!$user;
        }

        if ($user?->status != UserStatus::ACTIVE) {
            return response()->redirectTo('/signin');
        }

        $authEventData = new AuthEventData($user, AuthType::SESSION, session()->id(), $oauthProvider->id);

        if ($isNewUser) {
            event(new UserRegistered($authEventData));
        } else {
            event(new UserLoggedIn($authEventData));
        }

        $user->oauthProviders()->syncWithoutDetaching([
            $oauthProvider->id => ['oauth_provider_user_id' => $externalUser->getId()]
        ]);

        $returnUrl = strval(session()->pull('return_url'));
        $returnToPath = parse_url($returnUrl, PHP_URL_PATH) ?: '/';

        return response()->redirectTo($returnToPath);
    }
}
