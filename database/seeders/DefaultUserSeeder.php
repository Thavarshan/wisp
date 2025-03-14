<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (DB::table('roles')->count() <= 0) {
            $this->call(RolePermissionSeeder::class);
        }

        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'Super-Admin',
            'email' => 'super-admin@cerberus.io',
            'password' => Hash::make('password'),
            'organisation_id' => Organisation::getDefault()->id,
        ]);

        $user->assignRole(Role::SUPER_ADMIN->value);

        // $user->createPersonalTeam();
    }
}
