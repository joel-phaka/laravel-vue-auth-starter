<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Uri;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Passport::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadDynamicConfigs();
        $this->configurePassport();
    }

    private function loadDynamicConfigs(): void
    {
        if (app()->runningInConsole()) return;

        $baseUrl = base_url();

        config([
            'app.url' => $baseUrl,
            'cors.allowed_origins' => [$baseUrl],
            'sanctum.stateful' => array_unique(
                array_filter(
                    array_merge(((array)config('sanctum.stateful')), [parse_url($baseUrl, PHP_URL_HOST)]),
                    'boolval'
                )
            ),
        ]);

        URL::useOrigin(config('app.url'));

        if (str_starts_with(config('app.url'), "https")) {
            URL::forceScheme('https');
        }

        Paginator::currentPathResolver(function () {
            $path = trim(request()->path(), '/');

            if (!!$path && !str_starts_with($path, '?')) {
                $path = '/' . $path;
            }

            return config('app.url') . $path;
        });

        VerifyEmail::createUrlUsing(function ($notifiable) {
            $uri = Uri::of(config('app.url'))
                ->withPath('verify')
                ->withQuery([
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]);

            return sign_url($uri, 60 * 60);
        });

        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            $uri = Uri::of(config('app.url'))
                ->withPath('password/reset/' . $token)
                ->withQuery([
                    'email' => $notifiable->getEmailForPasswordReset(),
                ]);

            return strval($uri);
        });
    }

    private function configurePassport(): void
    {
        $dateTimeNow = now()->toImmutable();

        Passport::enablePasswordGrant();
        Passport::ignoreCsrfToken();
        Passport::tokensExpireIn($dateTimeNow->addHours(2));
        Passport::refreshTokensExpireIn($dateTimeNow->addMonth());
    }
}
