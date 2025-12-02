# Plugin Configuration Contracts

**Feature**: 002-security-plugins-integration  
**Date**: 2025-12-02

## Overview

This document defines the configuration contracts for the three plugins being integrated. These are not REST API contracts but rather configuration specifications that MUST be followed.

---

## 1. AutoLogoutPlugin Configuration Contract

**Location**: `AdminPanelProvider.php`

```php
AutoLogoutPlugin::make()
    ->logoutAfter(int $seconds)           // REQUIRED: Timeout in seconds
    ->disableIf(Closure $callback)        // OPTIONAL: Condition to disable
    ->color(Color $color)                 // OPTIONAL: Badge color
    ->icon(string $icon)                  // OPTIONAL: Badge icon
    ->withoutWarning()                    // OPTIONAL: Disable warning
    ->withoutTimeLeft()                   // OPTIONAL: Hide countdown
    ->timeLeftText(string $text)          // OPTIONAL: Custom label
```

### Required Configuration

| Method          | Value        | Type          |
| --------------- | ------------ | ------------- |
| `logoutAfter()` | 900 (15 min) | int (seconds) |

### Environment Variables

| Variable                                      | Default | Description           |
| --------------------------------------------- | ------- | --------------------- |
| `FILAMENT_AUTO_LOGOUT_ENABLED`                | true    | Enable/disable plugin |
| `FILAMENT_AUTO_LOGOUT_DURATION_IN_SECONDS`    | 900     | Timeout duration      |
| `FILAMENT_AUTO_LOGOUT_WARN_BEFORE_IN_SECONDS` | 30      | Warning time          |

---

## 2. RenewPasswordPlugin Configuration Contract

**Location**: `AdminPanelProvider.php`

```php
RenewPasswordPlugin::make()
    ->passwordExpiresIn(int $days)                    // OPTIONAL: Periodic renewal
    ->forceRenewPassword(?string $column = null)      // OPTIONAL: Admin-forced renewal
    ->timestampColumn(string $column)                 // OPTIONAL: Custom column name
    ->renewPage(string $pageClass)                    // OPTIONAL: Custom page
    ->routeUri(string $uri)                           // OPTIONAL: Custom route
```

### Required Configuration

| Method                 | Value     | Type       |
| ---------------------- | --------- | ---------- |
| `passwordExpiresIn()`  | 90        | int (days) |
| `forceRenewPassword()` | (no args) | void       |

### Database Contract

| Column                   | Type      | Nullable | Default |
| ------------------------ | --------- | -------- | ------- |
| `last_renew_password_at` | timestamp | yes      | null    |
| `force_renew_password`   | boolean   | no       | false   |

### Model Contract

```php
// User MUST implement
interface RenewPasswordContract
{
    public function needRenewPassword(): bool;
}

// User MUST use trait
trait RenewPassword
{
    public function needRenewPassword(): bool { /* ... */ }
}
```

---

## 3. LanguageSwitch Configuration Contract

**Location**: `AppServiceProvider.php`

```php
LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
    $switch
        ->locales(array $locales)                     // REQUIRED: Available locales
        ->labels(array $labels)                       // OPTIONAL: Custom labels
        ->flags(array $flags)                         // OPTIONAL: Flag images
        ->flagsOnly()                                 // OPTIONAL: Hide text
        ->visible(bool $inside, bool $outside)        // OPTIONAL: Visibility
        ->outsidePanelRoutes(array $routes)           // OPTIONAL: Outside routes
        ->outsidePanelPlacement(Placement $placement) // OPTIONAL: Position
        ->renderHook(string $hook)                    // OPTIONAL: Render location
        ->excludes(array $panelIds)                   // OPTIONAL: Exclude panels
        ->circular()                                  // OPTIONAL: Round badges
        ->displayLocale(string $locale)               // OPTIONAL: Label language
});
```

### Required Configuration

| Method      | Value        | Type  |
| ----------- | ------------ | ----- |
| `locales()` | ['es', 'en'] | array |

### Recommended Configuration

| Method      | Value                                  | Rationale           |
| ----------- | -------------------------------------- | ------------------- |
| `labels()`  | ['es' => 'Español', 'en' => 'English'] | User-friendly names |
| `visible()` | outsidePanels: true                    | Show on login page  |

### Event Contract

```php
// Event dispatched on locale change
BezhanSalleh\FilamentLanguageSwitch\Events\LocaleChanged

// Event properties
$event->locale  // string: The new locale code
```

### Database Contract

| Column   | Type       | Nullable | Default |
| -------- | ---------- | -------- | ------- |
| `locale` | string(10) | no       | 'es'    |

### Theme Contract

```css
/* MUST be added to theme.css */
@source '../../../../vendor/bezhansalleh/filament-language-switch/resources/views/**/*.blade.php';
```

---

## Validation Checklist

Before considering implementation complete:

-   [ ] AutoLogoutPlugin registered in AdminPanelProvider
-   [ ] RenewPasswordPlugin registered in AdminPanelProvider
-   [ ] LanguageSwitch configured in AppServiceProvider
-   [ ] User model implements RenewPasswordContract
-   [ ] User model uses RenewPassword trait
-   [ ] Migration for password columns executed
-   [ ] Migration for locale column executed
-   [ ] Theme CSS updated with @source directive
-   [ ] Assets rebuilt with `npm run build`
