# Research: Filament Plugins Configuration

**Feature**: 001-filament-plugins-configuration  
**Date**: 2025-11-27  
**Status**: Complete

## Summary

This document consolidates research findings for configuring 18 Filament PHP plugins. All plugin repositories have been analyzed and configuration requirements documented.

---

## Plugin Analysis by Configuration Level

### Level 1: CRITICAL (Must configure first)

#### 1.1 filament-shield (bezhansalleh/filament-shield)

**Decision**: Use as base permission system  
**Rationale**: Provides role/permission infrastructure used by other plugins  
**Alternatives Considered**: 
- Raw Spatie Permission (rejected: no Filament UI integration)
- Custom solution (rejected: reinventing the wheel)

**Configuration Requirements**:
```bash
php artisan vendor:publish --tag="filament-shield-config"
php artisan shield:setup --no-interaction
```

**Model Changes**:
```php
// User.php
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable implements FilamentUser
{
    use HasRoles;
}
```

**Panel Registration**:
```php
FilamentShieldPlugin::make()
    ->gridColumns(['default' => 1, 'sm' => 2, 'lg' => 3])
    ->sectionColumnSpan(1)
```

---

#### 1.2 filament-breezy (jeffgreco13/filament-breezy)

**Decision**: Enable 2FA optional, enable Sanctum tokens  
**Rationale**: Flexible security without forcing all users to use 2FA  
**Alternatives Considered**:
- Forced 2FA (rejected: too restrictive for boilerplate)
- No 2FA (rejected: losing security feature)

**Configuration Requirements**:
```bash
php artisan breezy:install --no-interaction
```

**Model Changes**:
```php
// User.php
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
class User extends Authenticatable
{
    use TwoFactorAuthenticatable;
}
```

**Panel Registration**:
```php
BreezyCore::make()
    ->myProfile(shouldRegisterUserMenu: true, shouldRegisterNavigation: false)
    ->enableTwoFactorAuthentication(force: false)
    ->enableSanctumTokens()
```

---

### Level 2: HIGH (Requires migrations/models)

#### 2.1 filament-api-service (rupadana/filament-api-service)

**Decision**: Enable with panel prefix, integrate with Shield  
**Rationale**: Automatic API generation with proper authorization  
**Alternatives Considered**:
- Manual API controllers (rejected: more code to maintain)
- No API prefix (rejected: could conflict with web routes)

**Configuration Requirements**:
```bash
php artisan vendor:publish --tag=api-service-config
php artisan install:api --no-interaction
```

**Panel Registration**:
```php
ApiServicePlugin::make()
```

**Config (api-service.php)**:
```php
'route' => ['panel_prefix' => true],
'use-spatie-permission-middleware' => true,
```

---

#### 2.2 filament-webhook-client (tapp/filament-webhook-client)

**Decision**: Install for receiving webhooks  
**Rationale**: Complements existing webhook-server for bidirectional webhook support  
**Prerequisite**: spatie/laravel-webhook-client must be configured

**Configuration Requirements**:
```bash
php artisan vendor:publish --provider="Spatie\WebhookClient\WebhookClientServiceProvider" --tag="webhook-client-migrations"
php artisan migrate
php artisan vendor:publish --tag="filament-webhook-client-config"
```

**Panel Registration**:
```php
FilamentWebhookClientPlugin::make()
```

---

#### 2.3 commentions (kirschbaum-development/commentions)

**Decision**: Enable with subscriptions for notifications  
**Rationale**: Full comment functionality with @mentions and reactions  
**Alternatives Considered**:
- Simple comments without mentions (rejected: losing key feature)

**Configuration Requirements**:
```bash
php artisan vendor:publish --tag="commentions-migrations"
php artisan migrate
```

**Model Changes**:
```php
// User.php
use Kirschbaum\Commentions\Contracts\Commenter;
class User extends Authenticatable implements Commenter {}

// Commentable models
use Kirschbaum\Commentions\HasComments;
use Kirschbaum\Commentions\Contracts\Commentable;
class Asset extends Model implements Commentable
{
    use HasComments;
}
```

---

### Level 3: MEDIUM (Panel registration only)

#### 3.1 filament-apex-charts (leandrocfe/filament-apex-charts)

**Decision**: Register plugin, create sample chart widget  
**Rationale**: Dashboard visualization for asset statistics

**Panel Registration**:
```php
FilamentApexChartsPlugin::make()
```

**Usage**:
```bash
php artisan make:filament-apex-charts AssetStatsChart
```

---

#### 3.2 filament-quick-create (awcodes/filament-quick-create)

**Decision**: Enable with slideOver and keybindings  
**Rationale**: Quick access to create forms

**Panel Registration**:
```php
QuickCreatePlugin::make()
    ->slideOver()
    ->keyBindings(['command+shift+c', 'ctrl+shift+c'])
```

**Theme CSS**:
```css
@source '../../../../vendor/awcodes/filament-quick-create/resources/**/*.blade.php';
```

---

#### 3.3 guava/calendar

**Decision**: Configure for maintenance scheduling  
**Rationale**: Visual calendar for asset maintenance events

**Configuration Requirements**:
```bash
php artisan filament:assets
```

**Theme CSS**:
```css
@source '../../../../vendor/guava/calendar/resources/**/*';
@import '../../../../vendor/guava/calendar/resources/css/theme.css';
```

---

#### 3.4 flowforge (relaticle/flowforge)

**Decision**: Create Kanban for asset workflow  
**Rationale**: Visual workflow for asset lifecycle (new → in-use → maintenance → retired)

**Usage**:
```bash
php artisan flowforge:make-board AssetWorkflowBoard --model=Asset
```

---

### Level 4: LOW (Direct usage, no panel config)

#### 4.1 filament-excel (pxlrbt/filament-excel)

**Decision**: Add export to all main resources  
**Usage**:
```php
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
->bulkActions([ExportBulkAction::make()])
```

---

#### 4.2 filament-excel-import (eighty9nine/filament-excel-import)

**Decision**: Add import with validation to main resources  
**Usage**:
```php
use EightyNine\ExcelImport\ExcelImportAction;
ExcelImportAction::make()
    ->slideOver()
    ->validateUsing(['name' => 'required', 'serial_number' => 'required|unique:assets'])
    ->sampleExcel(sampleData: [...], fileName: 'assets-sample.xlsx')
```

---

#### 4.3 filament-badgeable-column (awcodes/filament-badgeable-column)

**Decision**: Use for status badges in tables  
**Theme CSS**:
```css
@source '../../../../vendor/awcodes/filament-badgeable-column/resources/**/*.blade.php';
```

---

#### 4.4 filament-modal-relation-managers (guava/filament-modal-relation-managers)

**Decision**: Use for opening relations in modals  
**Theme CSS**:
```css
@source '../../../../vendor/guava/filament-modal-relation-managers/resources/**/*';
```

---

#### 4.5 filament-layout-manager (asosick/filament-layout-manager)

**Decision**: Use for customizable dashboard layouts  
**Usage**: Extend `LayoutManagerPage` for dashboard pages

---

#### 4.6 spatie-laravel-settings-plugin

**Decision**: Create GeneralSettings page  
**Usage**:
```bash
php artisan make:settings-migration CreateGeneralSettings
php artisan make:filament-settings-page ManageGeneral
```

---

#### 4.7 spatie-laravel-tags-plugin

**Decision**: Use for asset categorization  
**Usage**:
```php
SpatieTagsInput::make('tags')->type('asset-categories')
```

---

## Theme CSS Complete Configuration

```css
/* resources/css/filament/admin/theme.css */

/* Plugin sources for Tailwind */
@source '../../../../vendor/awcodes/filament-quick-create/resources/**/*.blade.php';
@source '../../../../vendor/awcodes/filament-badgeable-column/resources/**/*.blade.php';
@source '../../../../vendor/guava/calendar/resources/**/*';
@source '../../../../vendor/guava/filament-modal-relation-managers/resources/**/*';
@source '../../../../vendor/jeffgreco13/filament-breezy/resources/**/*.blade.php';

/* Calendar theme integration */
@import '../../../../vendor/guava/calendar/resources/css/theme.css';
```

---

## Configuration Order Summary

| Order | Plugin | Type | Migration | Model Changes |
|-------|--------|------|-----------|---------------|
| 1 | filament-shield | Panel | Yes | HasRoles |
| 2 | filament-breezy | Panel | Yes | TwoFactorAuthenticatable |
| 3 | filament-api-service | Panel | No | - |
| 4 | filament-webhook-client | Panel | Yes (Spatie) | - |
| 5 | commentions | - | Yes | Commenter interface |
| 6 | filament-apex-charts | Panel | No | - |
| 7 | filament-quick-create | Panel | No | - |
| 8 | guava/calendar | - | No | Eventable interface |
| 9 | flowforge | - | No | - |
| 10-18 | Others | Direct use | No | - |

---

## Open Questions (Resolved)

| Question | Resolution |
|----------|------------|
| filament-excel-import repo location | Confirmed: eighty9nine/filament-excel-import |
| guava/filament-icons status | 404 but already installed, works via blade-icons |
| filament-webhook-server | Already configured in AdminPanelProvider |

---

*Research completed: 2025-11-27*
