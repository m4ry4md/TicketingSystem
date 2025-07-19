<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define the guard name
        $guardName = 'web';

        // Create Permissions for the 'api' guard
        $permissions = [
            'view_tickets',
            'create_tickets',
            'reply_to_tickets',
            'edit_tickets',
            'delete_tickets',
            'change_ticket_status',
            'restore_deleted_tickets',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => $guardName,
            ]);
        }

        // Create Roles and assign permissions for the 'api' guard

        // 1. Support Role
        $supportRole = Role::create([
            'name' => 'support',
            'guard_name' => $guardName,
        ]);
        $supportRole->givePermissionTo([
            'view_tickets',
            'reply_to_tickets',
            'change_ticket_status',
        ]);

        // 2. Super Admin Role (Gets all 'api' guard permissions)
        $superAdminRole = Role::create([
            'name' => 'super_admin',
            'guard_name' => $guardName,
        ]);

        // Fetch all permissions for the api guard and assign them
        $apiPermissions = Permission::where('guard_name', $guardName)->get();
        $superAdminRole->givePermissionTo($apiPermissions);
    }
}
