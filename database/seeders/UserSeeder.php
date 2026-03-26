<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin Role (if not exists)
        $role = Role::firstOrCreate(['name' => 'Super Admin']);

        // Create User
        $user = User::updateOrCreate(
            ['email' => 'admin@myagency.com'], // unique check
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678') // change password
            ]
        );

        // Assign Role
        $user->assignRole($role);
    }
}