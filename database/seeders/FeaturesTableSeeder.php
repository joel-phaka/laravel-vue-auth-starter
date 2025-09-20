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
            Feature::setMany([
                ['name' => 'social_login', 'enabled' => false],
                ['name' => 'recaptcha', 'enabled' => true],
                ['name' => 'oauth', 'enabled' => false, 'hidden' => true],
                ['name' => 'token_auth', 'enabled' => false, 'hidden' => true],
                ['name' => 'user_registration', 'enabled' => true],
                ['name' => 'password_reset', 'enabled' => true],
                ['name' => 'email_verification', 'enabled' => true],
                ['name' => 'phone_number_verification', 'enabled' => false],
                ['name' => 'otp_verification', 'enabled' => false],
            ]);
        }
    }
}
