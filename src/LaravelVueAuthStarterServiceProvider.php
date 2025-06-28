<?php

namespace JoelPhaka\LaravelVueAuthStarter;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;

class LaravelVueAuthStarterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Core application files (most likely to be customized)
        $this->publishes([
            __DIR__.'/../stubs/app/Controllers' => app_path('Http/Controllers'),
            __DIR__.'/../stubs/app/Models' => app_path('Models'),
            __DIR__.'/../stubs/app/Http/Middleware' => app_path('Http/Middleware'),
            __DIR__.'/../stubs/app/Http/Requests' => app_path('Http/Requests'),
        ], 'laravel-vue-auth-starter-app');

        // Configuration files (usually need customization)
        $this->publishes([
            __DIR__.'/../stubs/config/auth.php' => config_path('auth.php'),
            __DIR__.'/../stubs/config/passport.php' => config_path('passport.php'),
            __DIR__.'/../stubs/config/sanctum.php' => config_path('sanctum.php'),
            __DIR__.'/../stubs/config/services.php' => config_path('services.php'),
        ], 'laravel-vue-auth-starter-config');

        // Database files (usually safe to update)
        $this->publishes([
            __DIR__.'/../stubs/database/migrations' => database_path('migrations'),
            __DIR__.'/../stubs/database/seeders' => database_path('seeders'),
        ], 'laravel-vue-auth-starter-database');

        // Routes (often customized)
        $this->publishes([
            __DIR__.'/../stubs/routes/api.php' => base_path('routes/api.php'),
            __DIR__.'/../stubs/routes/web.php' => base_path('routes/web.php'),
        ], 'laravel-vue-auth-starter-routes');

        // Frontend resources (often customized)
        $this->publishes([
            __DIR__.'/../stubs/resources/js' => resource_path('js'),
            __DIR__.'/../stubs/resources/css' => resource_path('css'),
            __DIR__.'/../stubs/resources/sass' => resource_path('sass'),
        ], 'laravel-vue-auth-starter-frontend');

        // Public assets (usually safe to update)
        $this->publishes([
            __DIR__.'/../stubs/public' => public_path(),
        ], 'laravel-vue-auth-starter-assets');

        // Project configuration files (one-time setup)
        $this->publishes([
            __DIR__.'/../stubs/.env.example' => base_path('.env.example'),
            __DIR__.'/../stubs/vite.config.js' => base_path('vite.config.js'),
            __DIR__.'/../stubs/package.json' => base_path('package.json'),
        ], 'laravel-vue-auth-starter-config-files');

        // Everything together (for initial installation)
        $this->publishes([
            __DIR__.'/../stubs/app' => app_path(),
            __DIR__.'/../stubs/config' => config_path(),
            __DIR__.'/../stubs/database' => database_path(),
            __DIR__.'/../stubs/routes' => base_path('routes'),
            __DIR__.'/../stubs/resources' => resource_path(),
            __DIR__.'/../stubs/public' => public_path(),
            __DIR__.'/../stubs/.env.example' => base_path('.env.example'),
            __DIR__.'/../stubs/vite.config.js' => base_path('vite.config.js'),
            __DIR__.'/../stubs/package.json' => base_path('package.json'),
        ], 'laravel-vue-auth-starter');

        $this->commands([
            Console\InstallLaravelVueAuthStarterCommand::class,
        ]);
    }
}
