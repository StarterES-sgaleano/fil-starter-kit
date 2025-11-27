# Quickstart Guide: Filament Plugins Configuration

## Prerequisites

✅ PHP 8.4  
✅ Laravel 12  
✅ Filament 4  
✅ All 18 plugins installed via Composer  
✅ SQLite database configured  

---

## Quick Setup (5 minutes)

### Step 1: Security Foundation

```bash
# Shield: Roles & Permissions
php artisan vendor:publish --tag=filament-shield-config --no-interaction
php artisan shield:install --no-interaction
php artisan migrate

# Generate permissions for existing resources
php artisan shield:generate --all --no-interaction

# Breezy: 2FA & Profile
php artisan vendor:publish --tag=filament-breezy-config --no-interaction
```

### Step 2: Update User Model

```php
// app/Models/User.php
<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
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
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
```

### Step 3: Register Plugins

```php
// app/Providers/Filament/AdminPanelProvider.php

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Awcodes\FilamentQuickCreate\QuickCreatePlugin;
use Guava\Calendar\CalendarPlugin;

// In panel() method:
->plugins([
    FilamentShieldPlugin::make(),
    BreezyCore::make()
        ->myProfile(
            shouldRegisterUserMenu: true,
            hasAvatars: true,
        )
        ->enableTwoFactorAuthentication(),
    QuickCreatePlugin::make()
        ->slideOver(),
    CalendarPlugin::make(),
    // WebhookPlugin already configured
])
```

### Step 4: Create Super Admin

```bash
php artisan make:filament-user

# When prompted:
# Name: Admin
# Email: admin@example.com
# Password: password

# Then assign super_admin role
php artisan tinker
>>> User::first()->assignRole('super_admin');
```

### Step 5: Verify Installation

```bash
php artisan serve
# or use Laravel Herd
```

Visit: https://it-assets-manager.test/admin

**Verify:**
- [ ] Login works
- [ ] Shield menu visible (Roles, Permissions)
- [ ] Profile accessible from user menu
- [ ] 2FA option available

---

## Full Configuration (All Phases)

### Phase 1: Security (Required)

```bash
# Already done in quick setup
```

### Phase 2: API & Integration

```bash
# API Service
php artisan vendor:publish --tag=filament-api-service-config --no-interaction

# Commentions
php artisan vendor:publish --tag=commentions-config --no-interaction
php artisan vendor:publish --tag=commentions-migrations --no-interaction
php artisan migrate
```

### Phase 3: Panel Enhancement

```bash
# FlowForge
php artisan vendor:publish --tag=flowforge-config --no-interaction
php artisan vendor:publish --tag=flowforge-migrations --no-interaction
php artisan migrate
```

### Phase 4: Utility Features

No additional setup required. Features enabled when used in Resources.

---

## Plugin Usage Examples

### Shield: Protect a Resource

```php
// app/Filament/Resources/AssetResource.php
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class AssetResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }
}
```

### Breezy: Custom Profile Fields

```php
// AdminPanelProvider.php
BreezyCore::make()
    ->myProfile(
        shouldRegisterUserMenu: true,
        hasAvatars: true,
    )
    ->myProfileComponents([
        'personal_info' => MyPersonalInfo::class,
    ])
```

### ApexCharts: Create Chart Widget

```bash
php artisan make:filament-widget AssetsChart --chart
```

```php
// app/Filament/Widgets/AssetsChart.php
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class AssetsChart extends ApexChartWidget
{
    protected static ?string $chartId = 'assetsChart';
    
    protected function getOptions(): array
    {
        return [
            'chart' => ['type' => 'donut', 'height' => 300],
            'series' => [44, 55, 13],
            'labels' => ['Laptops', 'Monitors', 'Phones'],
        ];
    }
}
```

### Excel Export: Add to Table

```php
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;

public static function table(Table $table): Table
{
    return $table
        ->columns([...])
        ->headerActions([
            ExportAction::make(),
        ]);
}
```

### Commentions: Add to Infolist

```php
use Parallax\FilamentCommentions\Infolists\Components\CommentsEntry;

public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            CommentsEntry::make('comments'),
        ]);
}
```

---

## Common Commands

```bash
# Regenerate permissions after creating resources
php artisan shield:generate --all --no-interaction

# Clear caches
php artisan optimize:clear

# Run tests
php artisan test

# Format code
./vendor/bin/pint --dirty
```

---

## Troubleshooting

### "Permission does not exist"

```bash
php artisan shield:generate --all --no-interaction
php artisan optimize:clear
```

### 2FA not showing

Ensure `filament-breezy` config has:
```php
'enable_2fa' => true,
'show_2fa_page' => true,
```

### Plugin not appearing in panel

Check `AdminPanelProvider.php` has the plugin registered in `->plugins([])`.

### API routes not working

Ensure `filament-api-service` has:
```php
'route' => [
    'panel_prefix' => true,
],
```

---

## Next Steps

1. **Create your first Resource**
   ```bash
   php artisan make:filament-resource Asset --generate
   ```

2. **Generate API for Resource**
   ```bash
   php artisan api:generate Asset
   ```

3. **Add comments to Resource**
   - Add `HasComments` trait to model
   - Add `CommentsEntry` to infolist

4. **Create dashboard widgets**
   ```bash
   php artisan make:filament-widget StatsOverview --stats-overview
   ```

---

**Quick Reference URLs:**

| Feature | URL |
|---------|-----|
| Admin Panel | /admin |
| Profile | /admin/my-profile |
| Roles | /admin/shield/roles |
| API Docs | /admin/api (after setup) |

---

**Version**: 1.0  
**Last Updated**: Phase 1 Planning
