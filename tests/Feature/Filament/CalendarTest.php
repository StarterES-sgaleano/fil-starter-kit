<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
    }

    public function test_calendar_widget_can_be_created(): void
    {
        // Calendar widgets are available through the Guava Calendar package
        // This test verifies the widget base class is accessible
        $this->assertTrue(class_exists(\Guava\Calendar\Filament\CalendarWidget::class));
    }

    public function test_dashboard_loads_without_calendar_errors(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
    }
}
