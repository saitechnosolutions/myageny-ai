<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web'],
            ['display_name' => 'Super Admin', 'description' => 'Full CRM access across all modules.']
        );

        Permission::ensureCrmPermissions();

        $role->syncPermissions(Permission::withoutGlobalScopes()->get());

        $user = User::updateOrCreate(
            ['email' => 'admin@myagency.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),
            ]
        );

        $user->syncRoles([$role]);
    }
}
