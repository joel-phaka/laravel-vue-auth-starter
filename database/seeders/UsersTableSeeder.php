<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (DB::table('users')->count() === 0) {
            $superAdminRole = DB::table('roles')
                ->select(['id'])
                ->where('name', UserRole::SUPER_ADMIN->name)
                ->where('level', UserRole::SUPER_ADMIN->value)
                ->first();

            if (!!$superAdminRole) {
                DB::table('users')->insert([
                    'role_id' => $superAdminRole->id,
                    'first_name' => "Super",
                    'last_name' => "Admin",
                    'email' => "admin@foo.com",
                    'email_verified_at' => now(),
                    'password' => Hash::make('pA$s3wOrd5'),
                    'remember_token' => null,
                    'status' => UserStatus::ACTIVE,
                    'created_at' => $now = now(),
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
