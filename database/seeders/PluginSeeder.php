<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder for Filament plugin default data.
 *
 * This seeder is responsible for creating default data required
 * by various Filament plugins such as Shield roles/permissions.
 */
class PluginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            // ShieldSeeder::class, // Uncomment after Shield is configured
        ]);
    }
}
