<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        // Create Super Admin Role
        $role = Role::firstOrCreate(['name' => 'Super Admin']);

        // Create All Permissions (example list — customize as needed)
        $permissions = [
            'dashboard.view',

            'user.create',
            'user.view',
            'user.edit',
            'user.delete',

            'lead.create',
            'lead.view',
            'lead.edit',
            'lead.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to Super Admin
        $role->syncPermissions(Permission::all());

        // Create Super Admin User
        $user = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),
            ]
        );

        // Assign Role
        $user->assignRole($role);
    }
}