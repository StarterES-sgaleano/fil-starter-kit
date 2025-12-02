<?php

namespace App\Providers\Filament;

use Carbon\Carbon;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Guava\Calendar\CalendarPlugin;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Relaticle\Flowforge\FlowforgePlugin;
use Rupadana\ApiService\ApiServicePlugin;
use Awcodes\QuickCreate\QuickCreatePlugin;
use Filament\Http\Middleware\Authenticate;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Niladam\FilamentAutoLogout\AutoLogoutPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Marjose123\FilamentWebhookServer\WebhookPlugin;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Yebor974\Filament\RenewPassword\RenewPasswordPlugin;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->plugins([
                // Security Layer (FR-1)
                FilamentShieldPlugin::make(),
                BreezyCore::make()
                    ->enableBrowserSessions()
                    ->enableSanctumTokens()
                    ->myProfile(
                        hasAvatars: true,
                    ),

                // Auto Logout - 15 minutes timeout with 30-second warning (plugin default)
                AutoLogoutPlugin::make()
                    ->logoutAfter(\Carbon\CarbonInterface::SECONDS_PER_MINUTE * 15),

                // Password Renewal - 90 days expiration + admin-forced renewal
                RenewPasswordPlugin::make()
                    ->passwordExpiresIn(days: 90)
                    ->forceRenewPassword(),

                // Integration Layer (FR-2)
                ApiServicePlugin::make(),
                WebhookPlugin::make()
                    ->enableApiRoutes()
                    ->keepLogs()
                    ->enablePlugin(),

                // UI Layer (FR-4)
                QuickCreatePlugin::make(),
                CalendarPlugin::make(),
                FlowforgePlugin::make(),
                FilamentApexChartsPlugin::make(),
            ]);
    }
}
