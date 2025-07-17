<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guardName = 'api';

        // 1. Create Super Admin User
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin User',
            'email' => 'super_admin@example.com',
            'password' => Hash::make('maryam123456'),
        ]);

        // Find the 'super_admin' role for the 'api' guard and assign it
        $superAdminRole = Role::where('name', 'super_admin')->where('guard_name', $guardName)->first();
        if ($superAdminRole) {
            $superAdmin->assignRole($superAdminRole);
        }


        // 2. Create Support User
        $supportUser = User::factory()->admin()->create([
            'name' => 'Support User',
            'email' => 'support@example.com',
            'password' => Hash::make('maryam123456'),
        ]);

        // Find the 'support' role for the 'api' guard and assign it
        $supportRole = Role::where('name', 'support')->where('guard_name', $guardName)->first();
        if ($supportRole) {
            $supportUser->assignRole($supportRole);
        }
    }
}
