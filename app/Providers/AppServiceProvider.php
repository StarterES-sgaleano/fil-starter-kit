<?php

namespace App\Providers;

use BezhanSalleh\LanguageSwitch\Events\LocaleChanged;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The supported locales for the application.
     */
    protected array $supportedLocales = ['es', 'en'];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureLanguageSwitch();
        $this->loadUserLocale();
    }

    /**
     * Configure the Filament Language Switch plugin.
     */
    protected function configureLanguageSwitch(): void
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales($this->supportedLocales)
                ->labels([
                    'es' => 'Español',
                    'en' => 'English',
                ])
                ->visible(outsidePanels: true);
        });

        // Persist locale preference to database when changed
        Event::listen(function (LocaleChanged $event) {
            if (auth()->check()) {
                auth()->user()->update(['locale' => $event->locale]);
            }
        });
    }

    /**
     * Load the user's locale preference on boot.
     * Falls back to default 'es' if locale is invalid (FR-012).
     */
    protected function loadUserLocale(): void
    {
        if (auth()->check() && auth()->user()->locale) {
            $userLocale = auth()->user()->locale;

            // Fall back to 'es' if user's locale is not in supported locales
            $effectiveLocale = in_array($userLocale, $this->supportedLocales)
                ? $userLocale
                : 'es';

            app()->setLocale($effectiveLocale);
        }
    }
}
