<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearSettingsCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-settings-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the cached application settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        clear_settings_cache();
        $this->info('Settings cache cleared.');

        return self::SUCCESS;
    }
}
