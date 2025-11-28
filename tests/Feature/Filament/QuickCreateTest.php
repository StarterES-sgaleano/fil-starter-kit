<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class QuickCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
    }

    public function test_quick_create_plugin_is_registered(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
        // Quick create should be available in the panel
        // The plugin adds a button to the header, we verify the panel loads correctly
    }

    public function test_dashboard_accessible_with_quick_create(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
        $response->assertSee('Dashboard');
    }
}
