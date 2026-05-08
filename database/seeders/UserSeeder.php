<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin Role (if not exists)
        $role = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web'],
            ['display_name' => 'Super Admin', 'description' => 'Full CRM access across all modules.']
        );

        Permission::ensureCrmPermissions();
        $role->syncPermissions(Permission::withoutGlobalScopes()->get());

        // Create User
        $user = User::updateOrCreate(
            ['email' => 'admin@myagency.com'], // unique check
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678') // change password
            ]
        );

        // Assign Role
        $user->syncRoles([$role]);
    }
}
