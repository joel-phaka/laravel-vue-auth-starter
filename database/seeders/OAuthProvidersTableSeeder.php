<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OAuthProvidersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (DB::table('oauth_providers')->count() === 0) {
            DB::table('oauth_providers')->insert([
                ['code' => 'google',    'name' => 'Google',    'created_at' => $now = now(), 'updated_at' => $now],
                ['code' => 'facebook',  'name' => 'Facebook',  'created_at' => $now = now(), 'updated_at' => $now],
                ['code' => 'apple',     'name' => 'Apple',     'created_at' => $now = now(), 'updated_at' => $now],
                ['code' => 'microsoft', 'name' => 'Microsoft', 'created_at' => $now = now(), 'updated_at' => $now],
                ['code' => 'twitter',   'name' => 'Twitter',   'created_at' => $now = now(), 'updated_at' => $now],
            ]);
        }
    }
}
