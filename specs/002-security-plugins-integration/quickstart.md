# Quickstart: Security & i18n Plugins Integration

**Feature**: 002-security-plugins-integration  
**Date**: 2025-12-02

## Prerequisites

-   PHP 8.2+
-   Composer
-   Node.js & npm
-   Existing fil-starter-kit installation with Shield and Breezy configured

## Installation Steps

### Step 1: Install Packages

```bash
# Install all three plugins
composer require niladam/filament-auto-logout
composer require yebor974/filament-renew-password
composer require bezhansalleh/filament-language-switch

# Run auto-logout installer
php artisan filament-auto-logout:install
```

### Step 2: Publish and Run Migrations

```bash
# Publish renew-password migrations
php artisan vendor:publish --tag="filament-renew-password-migrations"

# Create locale migration
php artisan make:migration add_locale_to_users_table

# Run all migrations
php artisan migrate
```

### Step 3: Update User Model

Edit `app/Models/User.php`:

```php
<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Yebor974\Filament\RenewPassword\Contracts\RenewPasswordContract;
use Yebor974\Filament\RenewPassword\Traits\RenewPassword;

class User extends Authenticatable implements FilamentUser, RenewPasswordContract
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;
    use RenewPassword;

    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
        'last_renew_password_at',
        'force_renew_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_renew_password_at' => 'datetime',
            'force_renew_password' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
```

### Step 4: Update AdminPanelProvider

Edit `app/Providers/Filament/AdminPanelProvider.php`:

```php
<?php

namespace App\Providers\Filament;

use Carbon\Carbon;
// ... existing imports ...
use Niladam\FilamentAutoLogout\AutoLogoutPlugin;
use Yebor974\Filament\RenewPassword\RenewPasswordPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            // ... existing configuration ...
            ->plugins([
                // Existing plugins...
                FilamentShieldPlugin::make(),
                BreezyCore::make()
                    ->enableBrowserSessions()
                    ->enableSanctumTokens()
                    ->myProfile(hasAvatars: true),

                // NEW: Auto Logout
                AutoLogoutPlugin::make()
                    ->logoutAfter(Carbon::SECONDS_PER_MINUTE * 15),

                // NEW: Password Renewal
                RenewPasswordPlugin::make()
                    ->passwordExpiresIn(days: 90)
                    ->forceRenewPassword(),

                // ... other existing plugins ...
            ]);
    }
}
```

### Step 5: Configure Language Switch

Edit `app/Providers/AppServiceProvider.php`:

```php
<?php

namespace App\Providers;

use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use BezhanSalleh\FilamentLanguageSwitch\Events\LocaleChanged;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Configure Language Switch
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['es', 'en'])
                ->labels([
                    'es' => 'Español',
                    'en' => 'English',
                ])
                ->visible(outsidePanels: true);
        });

        // Persist locale preference to database
        Event::listen(function (LocaleChanged $event) {
            if (auth()->check()) {
                auth()->user()->update(['locale' => $event->locale]);
            }
        });

        // Load user's locale preference
        if (auth()->check() && auth()->user()->locale) {
            app()->setLocale(auth()->user()->locale);
        }
    }
}
```

### Step 6: Update Theme CSS

Edit `resources/css/filament/admin/theme.css`:

```css
@import "/vendor/filament/filament/resources/css/theme.css";

@source '../../../../vendor/bezhansalleh/filament-language-switch/resources/views/**/*.blade.php';

/* Add any other existing @source directives */
```

### Step 7: Build Assets

```bash
npm run build
```

### Step 8: Clear Caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Verification

### Test Auto-Logout

1. Log in to the admin panel
2. Observe the "Time left" badge in the header
3. Wait for 15 minutes (or temporarily reduce timeout for testing)
4. Verify you are logged out with a notification

### Test Password Renewal

1. Create a test user with expired password:
    ```bash
    php artisan tinker
    >>> User::factory()->withExpiredPassword()->create(['email' => 'test@example.com'])
    ```
2. Log in as the test user
3. Verify you are redirected to the password renewal page

### Test Language Switch

1. Go to the login page
2. Click the language switcher
3. Select "English"
4. Verify the login page displays in English
5. Log in and verify the preference persists

## Troubleshooting

### Auto-logout not working

-   Check browser console for JavaScript errors
-   Verify plugin is registered in AdminPanelProvider
-   Run `php artisan filament-auto-logout:install` again

### Password renewal not triggering

-   Verify migration ran: `php artisan migrate:status`
-   Check `last_renew_password_at` column exists
-   Verify `RenewPassword` trait is added to User model

### Language switch not visible

-   Verify theme.css has the @source directive
-   Run `npm run build` to rebuild assets
-   Check AppServiceProvider configuration
