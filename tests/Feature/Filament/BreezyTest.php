<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BreezyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_access_profile_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/my-profile');

        $response->assertOk();
    }

    public function test_guest_cannot_access_profile_page(): void
    {
        $response = $this->get('/admin/my-profile');

        $response->assertRedirect('/admin/login');
    }

    public function test_profile_page_shows_user_name(): void
    {
        $user = User::factory()->create(['name' => 'Test User']);

        $response = $this->actingAs($user)->get('/admin/my-profile');

        $response->assertOk();
        $response->assertSee('Test User');
    }
}
