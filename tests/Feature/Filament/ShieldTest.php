<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ShieldTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::create(['name' => 'panel_user', 'guard_name' => 'web']);
    }

    public function test_super_admin_can_access_roles_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $response = $this->actingAs($admin)->get('/admin/shield/roles');

        $response->assertOk();
    }

    public function test_regular_user_cannot_access_roles_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/shield/roles');

        $response->assertForbidden();
    }

    public function test_panel_user_cannot_access_roles_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('panel_user');

        $response = $this->actingAs($user)->get('/admin/shield/roles');

        $response->assertForbidden();
    }

    public function test_super_admin_role_exists(): void
    {
        $this->assertTrue(Role::where('name', 'super_admin')->exists());
    }

    public function test_panel_user_role_exists(): void
    {
        $this->assertTrue(Role::where('name', 'panel_user')->exists());
    }
}
