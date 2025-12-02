# Implementation Plan: Security & i18n Plugins Integration

**Branch**: `002-security-plugins-integration` | **Date**: 2025-12-02 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `/specs/002-security-plugins-integration/spec.md`

## Summary

Integrate three Filament plugins to enhance security and internationalization:

1. **filament-auto-logout** - Automatic session termination after inactivity
2. **filament-renew-password** - Enforce periodic password renewal
3. **filament-language-switch** - Multi-language support with user preference persistence

All plugins follow the Plugin-First Architecture principle and enhance the Security Layer per constitution requirements.

## Technical Context

**Language/Version**: PHP 8.2+ / Laravel 12.x / Filament 4.x  
**Primary Dependencies**:

-   `niladam/filament-auto-logout` (no additional deps)
-   `yebor974/filament-renew-password` v3.x (requires migration)
-   `bezhansalleh/filament-language-switch` v4.x (requires custom theme)

**Storage**: SQLite (default) - requires migration for User model columns  
**Testing**: PHPUnit 11.x with Filament testing utilities  
**Target Platform**: Web (Laravel Herd / Valet / Sail)  
**Project Type**: Laravel Filament Admin Panel (single project)  
**Performance Goals**: N/A (plugin configuration, no custom logic)  
**Constraints**: Must maintain Filament 4 compatibility  
**Scale/Scope**: 3 plugins, 2 migrations, 1 model update, 3 provider configurations

## Constitution Check

_GATE: Must pass before Phase 0 research. Re-check after Phase 1 design._

| Principle                            | Status  | Notes                                                 |
| ------------------------------------ | ------- | ----------------------------------------------------- |
| I. Plugin-First Architecture         | ✅ PASS | All features via plugins, no custom code              |
| II. Security by Default              | ✅ PASS | Auto-logout + password renewal = security enhancement |
| III. Laravel & Filament Conventions  | ✅ PASS | Using artisan migrations, config helpers              |
| IV. Documentation-Driven Development | ✅ PASS | Docs created before implementation                    |
| V. Minimal Custom Code               | ✅ PASS | Only User model trait/interface additions             |
| VI. Test Coverage for Custom Code    | ✅ PASS | Feature tests for plugin functionality                |

**Gate Result**: ✅ ALL GATES PASSED - Proceed to Phase 0

## Project Structure

### Documentation (this feature)

```text
specs/002-security-plugins-integration/
├── plan.md              # This file
├── spec.md              # Feature specification
├── research.md          # Phase 0 output
├── data-model.md        # Phase 1 output
├── quickstart.md        # Phase 1 output
└── tasks.md             # Phase 2 output (/speckit.tasks)
```

### Source Code (repository root)

```text
app/
├── Models/
│   └── User.php                    # Add RenewPassword trait + locale
├── Providers/
│   ├── AppServiceProvider.php      # LanguageSwitch configuration
│   └── Filament/
│       └── AdminPanelProvider.php  # Register all 3 plugins

config/
└── (plugin configs use defaults - no published files needed)

database/
└── migrations/
    ├── XXXX_add_renew_password_columns_to_users.php
    └── XXXX_add_locale_to_users_table.php

resources/
└── css/
    └── filament/
        └── admin/
            └── theme.css           # Add @source for language-switch

tests/
└── Feature/
    └── Filament/
        ├── AutoLogoutTest.php
        ├── RenewPasswordTest.php
        └── LanguageSwitchTest.php

docs/
├── FILAMENT_PLUGINS.md             # Update status to ✅
├── decisions.md                    # Already updated
└── plugins/
    ├── auto-logout-config.md       # Already created
    ├── renew-password-config.md    # Already created
    └── language-switch-config.md   # Already created
```

**Structure Decision**: Laravel Filament standard structure. Plugins register in AdminPanelProvider, language switch configures in AppServiceProvider per plugin requirements.

## Complexity Tracking

> No violations - all implementations follow Plugin-First Architecture
