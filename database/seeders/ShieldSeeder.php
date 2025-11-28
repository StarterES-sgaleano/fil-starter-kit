<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ShieldSeeder extends Seeder
{
    /**
     * Seed the default Shield roles and permissions.
     */
    public function run(): void
    {
        // Create the super_admin role if it doesn't exist
        $superAdmin = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web']
        );

        // Create the panel_user role if it doesn't exist
        $panelUser = Role::firstOrCreate(
            ['name' => 'panel_user', 'guard_name' => 'web']
        );

        $this->command->info('Shield roles seeded successfully.');
    }
}
