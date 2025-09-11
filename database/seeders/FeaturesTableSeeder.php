<?php

namespace Database\Seeders;

use App\Support\Feature;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeaturesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (DB::table('features')->count() === 0) {
            Feature::set('social_login', false);
            Feature::set('recaptcha', true);
            Feature::set('oauth', false, ['hidden' => true]);
            Feature::set('token_auth', false, ['hidden' => true]);
            Feature::set('user_registration', true);
            Feature::set('password_reset', true);
        }
    }
}
