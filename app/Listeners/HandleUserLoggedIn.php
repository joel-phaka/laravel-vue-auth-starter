<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use App\Models\LoginLog;
use App\Models\User;
use App\Support\UserLoginInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;


class HandleUserLoggedIn
{
    public UserLoginInfo $userLoginInfo;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserLoggedIn $event): void
    {
        $this->userLoginInfo = $event->getUserLoginInfo();
        $this->logLogin();
    }

    private function logLogin(): void
    {
        $loginLog = new LoginLog();
        $loginLog->user_id = $this->userLoginInfo->getUser()->id;;
        $loginLog->auth_type = $this->userLoginInfo->getAuthType()->value;
        $loginLog->auth_type_id = (string)$this->userLoginInfo->getAuthTypeId();
        $loginLog->ip = request()->ip();
        $loginLog->user_agent = request()->header('user-agent');
        $loginLog->created_at = Carbon::now();

        if (!!$loginLog->ip &&
            !!($location = Location::get($loginLog->ip)) &&
            !$location->isEmpty() &&
            !!$location->countryCode
        ) {
            $loginLog->location = $location->countryName . (!!$location->regionName ? ", {$location->regionName}" : '') . (!!$location->cityName ? ", {$location->cityName}" : '');
            $loginLog->country_code = $location->countryCode;
            $loginLog->region_code = $location->regionCode;
            $loginLog->area_code = $location->areaCode;
            $loginLog->zip_code = $location->zipCode;
            $loginLog->timezone = $location->timezone;
        }

        $agent = new Agent();

        if ($agent->isiOS() || $agent->isiPhone()) $loginLog->device_platform = 'ios';
        else if ($agent->isiPadOS() || $agent->isiPad()) $loginLog->device_platform = 'ipados';
        else if ($agent->isAndroidOS()) $loginLog->device_platform = 'android';
        else if ($agent->iswebOS()) $loginLog->device_platform = 'webos';
        else if (stripos($loginLog->user_agent, 'kaios') !== false) $loginLog->device_platform = 'kaios';
        else if ($agent->isDesktop()) $loginLog->device_platform = 'web';

        $loginLog->save();
    }
}
