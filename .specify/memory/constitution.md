<!--
SYNC IMPACT REPORT
==================
Version change: N/A → 1.0.0 (Initial constitution)
Modified principles: N/A (new document)
Added sections:
  - Core Principles (6 principles)
  - Technology Stack
  - Development Workflow
  - Governance
Removed sections: N/A
Templates requiring updates:
  - .specify/templates/plan-template.md ✅ (compatible - Constitution Check section exists)
  - .specify/templates/spec-template.md ✅ (compatible - FR requirements align)
  - .specify/templates/tasks-template.md ✅ (compatible - phase structure aligns)
Follow-up TODOs: None
-->

# Filament Starter Kit Constitution

## Core Principles

### I. Plugin-First Architecture

Every feature MUST be implemented through Filament plugins when available. The boilerplate serves as a curated, pre-configured collection of best-in-class plugins organized by functional layer:

-   **Security Layer**: Authentication, authorization, session management
-   **Integration Layer**: APIs, webhooks, external services
-   **UI Layer**: Components, widgets, visual enhancements
-   **Data Layer**: Import/export, settings, tagging

**Rationale**: Plugins provide battle-tested, maintainable solutions. Custom code MUST only be written when no suitable plugin exists or plugin customization is insufficient.

### II. Security by Default (NON-NEGOTIABLE)

All security features MUST be enabled and configured before any other functionality:

-   Role-based access control (Shield) MUST be configured first
-   Two-factor authentication MUST be available (optional for users, never disabled)
-   Session management MUST include auto-logout for inactive users
-   Password policies MUST enforce periodic renewal
-   API authentication MUST use Sanctum tokens

**Rationale**: Security is foundational. A boilerplate without proper security defaults creates vulnerable applications.

### III. Laravel & Filament Conventions

All code MUST follow Laravel 12 and Filament 4 conventions:

-   Use `php artisan make:*` commands for generating files
-   Prefer Eloquent over raw queries; use `Model::query()` over `DB::`
-   Form validation MUST use Form Request classes
-   Configuration MUST use `config()` helper, never `env()` directly outside config files
-   Livewire components MUST have single root elements and use `wire:key` in loops

**Rationale**: Convention over configuration reduces cognitive load and ensures consistency across projects built from this boilerplate.

### IV. Documentation-Driven Development

Every plugin integration MUST be documented before implementation:

-   Plugin purpose and capabilities in `docs/FILAMENT_PLUGINS.md`
-   Configuration decisions in `docs/decisions.md`
-   Detailed setup guide in `docs/plugins/<plugin-name>.md`
-   Status tracking: ✅ (configured), ⏳ (pending), 🟢 (use directly)

**Rationale**: The boilerplate's value is in its documentation. Users MUST understand what's included and how to use it.

### V. Minimal Custom Code

Custom code MUST be justified and minimal:

-   No custom implementations when plugins exist
-   No helper scripts for one-time operations
-   No hard-coded values; use configuration or environment variables
-   Prefer extending plugin functionality over replacing it

**Rationale**: Every line of custom code is maintenance burden. The boilerplate MUST remain lean and upgradeable.

### VI. Test Coverage for Custom Code

Any custom code added to the boilerplate MUST have corresponding tests:

-   Feature tests for Filament resources, pages, and widgets
-   Unit tests for custom services or utilities
-   Tests MUST use factories and seeders, not manual data setup
-   Tests MUST NOT be deleted or weakened without explicit approval

**Rationale**: Tests protect the boilerplate's stability across Laravel and Filament version upgrades.

## Technology Stack

The following versions are locked for this boilerplate:

| Component    | Version          | Notes                  |
| ------------ | ---------------- | ---------------------- |
| PHP          | 8.2+             | Required by Laravel 12 |
| Laravel      | 12.x             | Streamlined structure  |
| Filament     | 4.x              | Admin panel framework  |
| Livewire     | 3.x              | Reactive components    |
| Tailwind CSS | 4.x              | Styling framework      |
| Vite         | 7.x              | Asset bundling         |
| Database     | SQLite (default) | Configurable           |

**Dependency Policy**:

-   MUST NOT add dependencies without documented justification
-   MUST prefer official Filament plugins over third-party alternatives
-   MUST verify Filament 4 compatibility before adding any plugin

## Development Workflow

### Plugin Integration Process

1. **Research**: Document plugin in `docs/FILAMENT_PLUGINS.md` with ⏳ status
2. **Plan**: Add configuration steps to `docs/decisions.md`
3. **Document**: Create detailed guide in `docs/plugins/<plugin>.md`
4. **Implement**: Install, configure, and register plugin
5. **Test**: Verify functionality with feature tests
6. **Update**: Change status to ✅ in documentation

### Code Quality Gates

Before any commit:

-   Run `vendor/bin/pint --dirty` for code style
-   Run `composer run test` for test suite
-   Run `npm run build` for frontend assets
-   Verify no `env()` calls outside config files

### Commit Convention

```
<type>: <description>

Types: feat, fix, docs, style, refactor, test, chore
```

Examples:

-   `feat: add filament-auto-logout plugin`
-   `docs: document language-switch configuration`
-   `fix: correct Shield permission generation`

## Governance

This constitution supersedes all other development practices for this boilerplate.

### Amendment Process

1. Propose change with rationale
2. Document impact on existing plugins/code
3. Update all affected documentation
4. Increment version according to semver:
    - MAJOR: Principle removal or incompatible change
    - MINOR: New principle or section added
    - PATCH: Clarifications or typo fixes

### Compliance

-   All new features MUST pass Constitution Check before implementation
-   Violations MUST be documented in Complexity Tracking with justification
-   Use `AGENTS.md` for runtime development guidance

**Version**: 1.0.0 | **Ratified**: 2025-12-02 | **Last Amended**: 2025-12-02
