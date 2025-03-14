<?php

namespace Database\Seeders;

use App\Models\Organisation;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect(config('authorisation'))->each(function (array $resources, string $role) {
            $role = Role::firstOrCreate(['name' => $role], [
                'name' => $role,
                'organisation_id' => Organisation::getDefault()->id,
            ]);

            if (blank($resources)) {
                return;
            }

            collect($resources)->each(function (array $permissions, string $resource) use ($role) {
                if (blank($permissions)) {
                    return;
                }

                collect($permissions)->each(function ($permission) use ($role, $resource) {
                    $permission = Permission::firstOrCreate([
                        'name' => $permission.':'.$resource,
                    ]);
                    $role->givePermissionTo($permission);
                });
            });
        });
    }
}
