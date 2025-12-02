<?php

namespace Tests\Feature\Filament;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Yebor974\Filament\RenewPassword\RenewPasswordPlugin;

class RenewPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set the current panel for testing
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_renew_password_plugin_is_registered(): void
    {
        $panel = Filament::getCurrentPanel();
        $plugins = $panel->getPlugins();

        $hasRenewPasswordPlugin = collect($plugins)->contains(
            fn ($plugin) => $plugin instanceof RenewPasswordPlugin
        );

        $this->assertTrue($hasRenewPasswordPlugin, 'RenewPasswordPlugin should be registered in the admin panel');
    }

    public function test_user_with_expired_password_needs_renewal(): void
    {
        $user = User::factory()->withExpiredPassword()->create();

        $this->assertTrue(
            $user->needRenewPassword(),
            'User with password older than 90 days should need renewal'
        );
    }

    public function test_user_with_force_renew_password_needs_renewal(): void
    {
        $user = User::factory()->mustRenewPassword()->create();

        $this->assertTrue(
            $user->needRenewPassword(),
            'User with force_renew_password=true should need renewal'
        );
    }

    public function test_user_with_fresh_password_does_not_need_renewal(): void
    {
        $user = User::factory()->create([
            'last_renew_password_at' => now(),
            'force_renew_password' => false,
        ]);

        $this->assertFalse(
            $user->needRenewPassword(),
            'User with fresh password should not need renewal'
        );
    }
}
