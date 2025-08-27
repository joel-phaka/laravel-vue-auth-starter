<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuthProvidersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (DB::table('auth_providers')->count() === 0) {
            DB::table('auth_providers')->insert([
                ['name' => 'google',    'created_at' => $now = now(), 'updated_at' => $now],
                ['name' => 'facebook',  'created_at' => $now = now(), 'updated_at' => $now],
                ['name' => 'apple',     'created_at' => $now = now(), 'updated_at' => $now],
                ['name' => 'microsoft', 'created_at' => $now = now(), 'updated_at' => $now],
                ['name' => 'twitter',   'created_at' => $now = now(), 'updated_at' => $now],
            ]);
        }
    }
}
