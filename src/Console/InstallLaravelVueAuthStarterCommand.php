<?php

namespace JoelPhaka\LaravelVueAuthStarter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class InstallLaravelVueAuthStarterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-vue-auth-starter:install {--force : Overwrite existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Laravel Vue Auth Starter boilerplate';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Installing Laravel Vue Auth Starter...');
        $this->newLine();

        // Step 1: Publish all boilerplate files
        $this->info('📁 Publishing boilerplate files...');
        $this->call('vendor:publish', [
            '--tag' => 'laravel-vue-auth-starter',
            '--force' => $this->option('force')
        ]);
        $this->newLine();

        // Step 2: Install Passport first (creates OAuth tables with proper keys)
        $this->info('🔐 Installing Laravel Passport...');
        $this->info('   This creates OAuth tables with proper encryption keys');
        $this->call('passport:install');
        $this->newLine();

        // Step 3: Run migrations (Passport tables will be skipped automatically)
        $this->info('🗄️  Running database migrations...');
        $this->info('   Note: Passport tables will be skipped (already exist)');
        $this->call('migrate', ['--force' => true]);
        $this->newLine();

        // Step 4: Generate application key if not exists
        if (!config('app.key')) {
            $this->info('🔑 Generating application key...');
            $this->call('key:generate');
            $this->newLine();
        }

        // Step 5: Install npm dependencies
        $this->info('📦 Installing npm dependencies...');
        exec('npm install');
        $this->newLine();

        // Step 6: Build assets
        $this->info('🏗️  Building assets...');
        exec('npm run build');
        $this->newLine();

        $this->info('✅ Laravel Vue Auth Starter installed successfully!');
        $this->newLine();

        $this->info('📋 Next steps:');
        $this->info('   1. Configure your .env file with database and social login settings');
        $this->info('   2. Set up OAuth applications in your social provider dashboards');
        $this->info('   3. Run: php artisan serve');
        $this->info('   4. Visit: http://localhost:8000');
        $this->newLine();

        $this->info('📚 Documentation: https://github.com/joel-phaka/laravel-vue-auth-starter');
        $this->info('🐛 Issues: https://github.com/joel-phaka/laravel-vue-auth-starter/issues');
    }
}
