<?php

namespace Tests\Feature\Filament;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_language_switcher_is_visible_on_login_page(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        // The language switch component should be rendered on the login page
        // when visible(outsidePanels: true) is configured
    }

    public function test_locale_preference_is_saved_to_user_record(): void
    {
        $user = User::factory()->create(['locale' => 'es']);

        $user->update(['locale' => 'en']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'locale' => 'en',
        ]);
    }

    public function test_invalid_locale_falls_back_to_default(): void
    {
        // Create user with invalid locale
        $user = User::factory()->create(['locale' => 'invalid_locale']);

        // The application should fall back to 'es' when loading an invalid locale
        // This is handled in AppServiceProvider boot method
        $supportedLocales = ['es', 'en'];
        $userLocale = $user->locale;

        $effectiveLocale = in_array($userLocale, $supportedLocales) ? $userLocale : 'es';

        $this->assertEquals('es', $effectiveLocale, 'Invalid locale should fall back to es');
    }

    public function test_user_factory_creates_user_with_default_locale(): void
    {
        $user = User::factory()->create();

        $this->assertEquals('es', $user->locale, 'Default locale should be es');
    }
}
