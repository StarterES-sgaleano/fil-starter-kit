# Research: Security & i18n Plugins Integration

**Feature**: 002-security-plugins-integration  
**Date**: 2025-12-02

## Plugin Compatibility Research

### 1. filament-auto-logout

**Decision**: Use `niladam/filament-auto-logout` latest version

**Rationale**:

-   Confirmed Filament 4 compatibility (v2.x of plugin)
-   No additional dependencies required
-   Multi-tab synchronization built-in
-   Configurable via environment variables

**Alternatives Considered**:

-   Custom JavaScript implementation → Rejected (violates Plugin-First principle)
-   Session timeout via Laravel config only → Rejected (no warning, no multi-tab sync)

**Installation Command**:

```bash
composer require niladam/filament-auto-logout
php artisan filament-auto-logout:install
```

**Configuration Decisions**:
| Setting | Value | Rationale |
|---------|-------|-----------|
| Duration | 15 minutes | Balance between security and UX |
| Warning | 30 seconds | Enough time to save work |
| Show time left | true | Transparency for users |
| Location | GLOBAL_SEARCH_BEFORE | Visible but not intrusive |

---

### 2. filament-renew-password

**Decision**: Use `yebor974/filament-renew-password` v3.x (Filament 4 compatible)

**Rationale**:

-   Explicit Filament 4 support in v3.x
-   Supports both periodic and forced renewal
-   Provides trait and interface for User model
-   Customizable renewal page

**Alternatives Considered**:

-   Custom middleware for password age check → Rejected (reinventing the wheel)
-   Breezy password features only → Rejected (no periodic renewal support)

**Installation Command**:

```bash
composer require yebor974/filament-renew-password
php artisan vendor:publish --tag="filament-renew-password-migrations"
php artisan migrate
```

**Configuration Decisions**:
| Setting | Value | Rationale |
|---------|-------|-----------|
| Password expires in | 90 days | Industry standard for enterprise |
| Force renew enabled | true | Allows admin to force renewal |
| Timestamp column | last_renew_password_at | Default, no customization needed |
| Force column | force_renew_password | Default, no customization needed |

**User Model Changes Required**:

-   Implement `RenewPasswordContract` interface
-   Add `RenewPassword` trait
-   Add columns to `$fillable`: `last_renew_password_at`, `force_renew_password`
-   Add casts for datetime and boolean

---

### 3. filament-language-switch

**Decision**: Use `bezhansalleh/filament-language-switch` v4.x

**Rationale**:

-   Same author as filament-shield (trusted)
-   Extensive customization options
-   Event-based locale persistence
-   Works inside and outside panels (login page)

**Alternatives Considered**:

-   Laravel Localization middleware only → Rejected (no UI, no persistence)
-   Custom Livewire component → Rejected (violates Plugin-First principle)

**Installation Command**:

```bash
composer require bezhansalleh/filament-language-switch
```

**Configuration Decisions**:
| Setting | Value | Rationale |
|---------|-------|-----------|
| Locales | ['es', 'en'] | Spanish primary, English secondary |
| Visible outside panels | true | Language switch on login page |
| Persist to database | true | User preference survives sessions |
| Render hook | panels::global-search.after | Consistent with other UI elements |

**User Model Changes Required**:

-   Add `locale` column (string, 10 chars, default 'es')
-   Add to `$fillable`

**AppServiceProvider Configuration Required**:

-   Configure LanguageSwitch in `boot()` method
-   Listen to `LocaleChanged` event for persistence

---

## Integration Order

Based on dependencies and constitution requirements:

1. **filament-renew-password** (FIRST)

    - Requires migration
    - User model changes needed before other plugins
    - Security priority per constitution

2. **filament-auto-logout** (SECOND)

    - No dependencies on other plugins
    - Simple registration in AdminPanelProvider
    - Security priority per constitution

3. **filament-language-switch** (THIRD)
    - Requires locale column (separate migration)
    - AppServiceProvider configuration
    - Lower priority (P2 vs P1)

---

## Theme CSS Updates Required

All plugins requiring theme updates:

```css
/* resources/css/filament/admin/theme.css */

/* Existing sources... */

/* Language Switch */
@source '../../../../vendor/bezhansalleh/filament-language-switch/resources/views/**/*.blade.php';
```

Note: `filament-auto-logout` and `filament-renew-password` do not require theme updates.

---

## Potential Conflicts

### Auto-logout vs Unsaved Forms

-   **Risk**: User loses unsaved work on auto-logout
-   **Mitigation**: 30-second warning gives time to save
-   **Future**: Consider `beforeunload` event integration (out of scope)

### Renew Password vs 2FA (Breezy)

-   **Risk**: Password renewal might bypass 2FA
-   **Mitigation**: Both plugins use Filament's authentication flow
-   **Verification**: Test login → 2FA → password renewal flow

### Language Switch Persistence

-   **Risk**: Locale not loaded on subsequent requests
-   **Mitigation**: Load user locale in AppServiceProvider boot
-   **Implementation**: Check `auth()->user()->locale` and set app locale

---

## NEEDS CLARIFICATION Resolution

All technical unknowns have been resolved through plugin documentation research:

| Unknown                  | Resolution                                    |
| ------------------------ | --------------------------------------------- |
| Filament 4 compatibility | ✅ All plugins confirmed compatible           |
| Migration requirements   | ✅ Documented above                           |
| User model changes       | ✅ Documented above                           |
| Theme requirements       | ✅ Only language-switch needs theme update    |
| Configuration approach   | ✅ Environment variables + code configuration |
