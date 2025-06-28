<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Helpers\Utils;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (DB::table('roles')->count() === 0) {

            $roles = UserRole::cases();
            $insertData = [];

            foreach ($roles as $role) {
                $insertData[] = [
                    'name' => strtolower($role->name),
                    'level' => $role->value,
                    'created_at' => $now = now(),
                    'updated_at' => $now,
                ];
            }

            if (!empty($insertData)) {
                DB::table('roles')->insert($insertData);
            }
        }
    }
}
