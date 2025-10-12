<?php

namespace App\Traits;

use App\Enums\AuthState;
use App\Enums\AuthType;
use App\Models\User;
use App\Models\UserLogin;
use App\Support\Auth\AuthEventData;
use Illuminate\Support\Carbon;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;

trait CreatesUserLogin
{
    protected function createUserLogin(AuthState $authState, AuthEventData $authEventData): void
    {
        $userLogin = new UserLogin;
        $userLogin->user_id = $authEventData->getUser()->id;
        $userLogin->auth_type = $authEventData->getAuthType();
        $userLogin->auth_state = $authState;
        $userLogin->auth_type_id = (string)$authEventData->getAuthTypeId();
        $userLogin->oauth_provider_id = $authEventData->getOauthProviderId();
        $userLogin->ip = request()->ip();
        $userLogin->user_agent = request()->header('user-agent');
        $userLogin->created_at = Carbon::now();

        if (!!$userLogin->ip &&
            !!($location = Location::get($userLogin->ip)) &&
            !$location->isEmpty() &&
            !!$location->countryCode
        ) {
            $userLogin->location = $location->countryName . (!!$location->regionName ? ", {$location->regionName}" : '') . (!!$location->cityName ? ", {$location->cityName}" : '');
            $userLogin->country_code = $location->countryCode;
            $userLogin->region_code = $location->regionCode;
            $userLogin->area_code = $location->areaCode;
            $userLogin->zip_code = $location->zipCode;
            $userLogin->timezone = $location->timezone;
        }

        $agent = new Agent();

        if ($agent->isiOS() || $agent->isiPhone()) $userLogin->device_platform = 'ios';
        else if ($agent->isiPadOS() || $agent->isiPad()) $userLogin->device_platform = 'ipados';
        else if ($agent->isAndroidOS()) $userLogin->device_platform = 'android';
        else if ($agent->iswebOS()) $userLogin->device_platform = 'webos';
        else if (stripos($userLogin->user_agent, 'kaios') !== false) $userLogin->device_platform = 'kaios';
        else if ($agent->isDesktop()) $userLogin->device_platform = 'web';

        $userLogin->save();
    }
}
