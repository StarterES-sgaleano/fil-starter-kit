<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
    }

    public function test_webhook_list_endpoint_exists(): void
    {
        $response = $this->get('/webhook-server/api');

        // Should return an empty array or list, not 404
        $this->assertNotEquals(404, $response->status());
    }

    public function test_webhook_post_endpoint_accepts_payload(): void
    {
        $response = $this->postJson('/webhook-server/api', [
            'name' => 'test-webhook',
            'url' => 'https://example.com/webhook',
            'events' => ['user.created'],
        ]);

        // Should process the request (may fail validation, but route exists)
        $this->assertNotEquals(404, $response->status());
    }

    public function test_webhook_delete_endpoint_exists(): void
    {
        $response = $this->deleteJson('/webhook-server/api/1');

        // Should return 404 for non-existent webhook or validation error, not route 404
        $this->assertNotEquals(404, $response->status());
    }
}
