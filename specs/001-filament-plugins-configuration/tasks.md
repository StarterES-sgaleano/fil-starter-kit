# Tasks: Filament Plugins Configuration

**Input**: Design documents from `/specs/001-filament-plugins-configuration/`  
**Prerequisites**: plan.md ✅, spec.md ✅, research.md ✅, data-model.md ✅, contracts/ ✅, quickstart.md ✅

**Tests**: Included as requested in spec.md (NFR-2: Testing requirement)

**Organization**: Tasks are grouped by functional requirement to enable incremental delivery.

## Format: `[ID] [P?] [FR] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[FR]**: Functional requirement this task belongs to (FR1, FR2, FR3, FR4, FR5)
- Include exact file paths in descriptions

## Path Conventions

- **Laravel/Filament**: Standard Laravel structure
- `app/`, `config/`, `database/`, `tests/` at repository root
- Filament resources in `app/Filament/`

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Project initialization and basic structure for plugin configuration

- [ ] T001 Verify all 18 plugins are installed via `composer show | grep filament`
- [ ] T002 [P] Create `database/seeders/PluginSeeder.php` for default plugin data
- [ ] T003 [P] Create `tests/Feature/Filament/` directory structure for plugin tests

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core security infrastructure that MUST be complete before ANY other plugin can be configured

**⚠️ CRITICAL**: No plugin work can begin until Shield is configured (provides permissions for other plugins)

### Shield Installation (Required First)

- [ ] T004 Publish Shield config with `php artisan vendor:publish --tag=filament-shield-config --no-interaction`
- [ ] T005 Run `php artisan shield:install --no-interaction` to install Shield
- [ ] T006 Run `php artisan migrate` to create permission tables
- [ ] T007 Update `config/filament-shield.php` with navigation group 'Administración' in `config/filament-shield.php`
- [ ] T008 Add `HasRoles` trait and `FilamentUser` interface to `app/Models/User.php`
- [ ] T009 Create `database/seeders/ShieldSeeder.php` with super_admin role
- [ ] T010 Run `php artisan shield:generate --all --no-interaction` to generate base permissions

### Breezy Installation (Depends on Shield)

- [ ] T011 Publish Breezy config with `php artisan vendor:publish --tag=filament-breezy-config --no-interaction`
- [ ] T012 Update `config/filament-breezy.php` with 2FA and profile settings
- [ ] T013 Register `BreezyCore` plugin in `app/Providers/Filament/AdminPanelProvider.php`
- [ ] T014 Register `FilamentShieldPlugin` in `app/Providers/Filament/AdminPanelProvider.php`
- [ ] T014b [FR1] Document Shield permission integration pattern in `docs/plugins/shield-integration.md`

### Foundation Tests

- [ ] T015 [P] Create `tests/Feature/Filament/ShieldTest.php` with super_admin access test
- [ ] T016 [P] Create `tests/Feature/Filament/BreezyTest.php` with profile page test
- [ ] T017 Run `php artisan test --filter=Shield` to verify Shield configuration
- [ ] T018 Run `php artisan test --filter=Breezy` to verify Breezy configuration

**Checkpoint**: Security foundation ready - plugin implementation can now begin in parallel

---

## Phase 3: FR-2 - API Layer (Priority: P1) 🎯 MVP

**Goal**: Enable REST API generation and webhook handling for external integrations

**Independent Test**: API tokens page accessible at `/admin/api-tokens`, webhooks receivable at `/api/webhooks/{name}`

### Tests for FR-2

- [ ] T019 [P] [FR2] Create `tests/Feature/Filament/ApiServiceTest.php` with token page test
- [ ] T020 [P] [FR2] Create `tests/Feature/Filament/WebhookTest.php` with webhook endpoint test

### Implementation for FR-2

- [ ] T021 [FR2] Publish API Service config with `php artisan vendor:publish --tag=filament-api-service-config --no-interaction`
- [ ] T022 [FR2] Update `config/filament-api-service.php` with navigation settings and route configuration
- [ ] T023 [FR2] Verify WebhookPlugin is registered in `app/Providers/Filament/AdminPanelProvider.php`
- [ ] T024 [FR2] Publish Commentions config with `php artisan vendor:publish --tag=commentions-config --no-interaction`
- [ ] T025 [FR2] Publish Commentions migrations with `php artisan vendor:publish --tag=commentions-migrations --no-interaction`
- [ ] T026 [FR2] Run `php artisan migrate` to create comments table
- [ ] T027 [FR2] Update `config/commentions.php` with User model and notification settings
- [ ] T028 [FR2] Run `php artisan test --filter=ApiService` to verify API configuration
- [ ] T029 [FR2] Run `php artisan test --filter=Webhook` to verify Webhook configuration

**Checkpoint**: API Layer complete - external integrations now possible

---

## Phase 4: FR-4 - UI Components (Priority: P2)

**Goal**: Enable dashboard charts, quick create, calendar, and enhanced table features

**Independent Test**: Dashboard shows chart widget, Quick Create button in header, Calendar widget functional

### Tests for FR-4

- [ ] T030 [P] [FR4] Create `tests/Feature/Filament/QuickCreateTest.php` with plugin visibility test
- [ ] T031 [P] [FR4] Create `tests/Feature/Filament/CalendarTest.php` with widget accessibility test

### Implementation for FR-4

- [ ] T032 [P] [FR4] Register `QuickCreatePlugin` in `app/Providers/Filament/AdminPanelProvider.php`
- [ ] T033 [P] [FR4] Register `CalendarPlugin` in `app/Providers/Filament/AdminPanelProvider.php`
- [ ] T034 [FR4] Publish FlowForge config with `php artisan vendor:publish --tag=flowforge-config --no-interaction`
- [ ] T035 [FR4] Publish FlowForge migrations with `php artisan vendor:publish --tag=flowforge-migrations --no-interaction`
- [ ] T036 [FR4] Run `php artisan migrate` to create workflows tables
- [ ] T037 [FR4] Create sample chart widget `app/Filament/Widgets/SampleChart.php` using ApexCharts
- [ ] T038 [FR4] Run `php artisan test --filter=QuickCreate` to verify Quick Create
- [ ] T039 [FR4] Run `php artisan test --filter=Calendar` to verify Calendar

**Checkpoint**: UI Components complete - enhanced admin panel ready

---

## Phase 5: FR-3 - Data Management (Priority: P3)

**Goal**: Enable Excel export/import and model comments functionality

**Independent Test**: Export action visible on table headers, Import action functional, Comments visible on infos

### Tests for FR-3

- [ ] T040 [P] [FR3] Create `tests/Feature/Filament/ExcelExportTest.php` with export action test
- [ ] T041 [P] [FR3] Create `tests/Feature/Filament/ExcelImportTest.php` with import action test

### Implementation for FR-3

- [ ] T042 [FR3] Publish Excel Import config with `php artisan vendor:publish --tag=filament-excel-import-config --no-interaction`
- [ ] T043 [FR3] Publish Excel Import migrations with `php artisan vendor:publish --tag=filament-excel-import-migrations --no-interaction`
- [ ] T044 [FR3] Run `php artisan migrate` to create import tables (if any)
- [ ] T045 [FR3] Create example usage documentation in `docs/plugins/excel-usage.md`
- [ ] T046 [FR3] Run `php artisan test --filter=ExcelExport` to verify Excel Export
- [ ] T047 [FR3] Run `php artisan test --filter=ExcelImport` to verify Excel Import

**Checkpoint**: Data Management complete - import/export ready for resources

---

## Phase 6: FR-5 - Settings & Utilities (Priority: P4)

**Goal**: Configure remaining utility plugins for enhanced functionality

**Independent Test**: Badgeable columns render correctly, Modal relation managers work, Layout customizable

### Implementation for FR-5

- [ ] T048 [P] [FR5] Create example `BadgeableColumn` usage in `docs/plugins/badgeable-column-usage.md`
- [ ] T049 [P] [FR5] Create example `ModalRelationManager` usage in `docs/plugins/modal-relation-usage.md`
- [ ] T050 [P] [FR5] Document `LayoutManager` configuration in `docs/plugins/layout-manager-usage.md`
- [ ] T051 [FR5] Verify all utility plugins are auto-discovered by Filament
- [ ] T051b [FR5] Configure `filament/spatie-laravel-settings-plugin` - publish config and create Settings class
- [ ] T051c [FR5] Configure `filament/spatie-laravel-tags-plugin` - add `HasTags` trait to models requiring tagging
- [ ] T051d [FR5] Verify `filament-exceptions`, `advanced-tables`, `support-bubble` auto-register correctly

**Checkpoint**: All utility plugins documented and ready for use

---

## Phase 7: Polish & Cross-Cutting Concerns

**Purpose**: Final verification and documentation

- [ ] T052 Run `./vendor/bin/pint --dirty` to format all PHP files
- [ ] T053 Run `php artisan test` to verify all tests pass
- [ ] T054 Update `app/Providers/Filament/AdminPanelProvider.php` with final plugin order and comments
- [ ] T055 Create `docs/PLUGIN_CONFIGURATION_COMPLETE.md` summary document
- [ ] T056 Run quickstart.md verification checklist
- [ ] T057 [P] Update theme.css with all plugin @source directives if needed
- [ ] T058 Git commit with message "feat(plugins): Complete Filament plugins configuration"

---

## Dependencies & Execution Order

### Phase Dependencies

```
Phase 1: Setup ──────────────────┐
                                 ▼
Phase 2: Foundational ───────────┤ (BLOCKS ALL)
         (Shield + Breezy)       │
                                 ▼
         ┌───────────────────────┼───────────────────────┐
         │                       │                       │
         ▼                       ▼                       ▼
Phase 3: FR-2 (API)    Phase 4: FR-4 (UI)    Phase 5: FR-3 (Data)
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 ▼
                     Phase 6: FR-5 (Utilities)
                                 │
                                 ▼
                     Phase 7: Polish
```

### Critical Path

1. **T004-T010**: Shield MUST be installed first (provides permission system)
2. **T011-T014**: Breezy depends on Shield being installed
3. **T015-T018**: Tests verify foundation before proceeding
4. **All other phases**: Can proceed in parallel after Phase 2

### Within Each Phase

- Config publish before config updates
- Migrations before features
- Tests after implementation
- Format code after all changes

### Parallel Opportunities

```bash
# Phase 1 - All can run in parallel:
T002, T003

# Phase 2 - Tests can run in parallel:
T015, T016

# Phase 3 - Tests can run in parallel:
T019, T020

# Phase 4 - Plugin registrations can run in parallel:
T030, T031, T032, T033

# Phase 5 - Tests can run in parallel:
T040, T041

# Phase 6 - All docs can run in parallel:
T048, T049, T050
```

---

## Parallel Example: Phase 4 (UI Components)

```bash
# Launch tests in parallel:
Task T030: "Create tests/Feature/Filament/QuickCreateTest.php"
Task T031: "Create tests/Feature/Filament/CalendarTest.php"

# Launch plugin registrations in parallel:
Task T032: "Register QuickCreatePlugin in AdminPanelProvider.php"
Task T033: "Register CalendarPlugin in AdminPanelProvider.php"
```

---

## Implementation Strategy

### MVP First (Phase 1-3 Only)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational (Shield + Breezy) - **CRITICAL**
3. Complete Phase 3: API Layer
4. **STOP and VALIDATE**: Test API endpoints work
5. Deploy/demo if ready

### Incremental Delivery

1. Setup + Foundational → Security ready
2. Add API Layer → Test independently → Deploy (MVP!)
3. Add UI Components → Test independently → Deploy
4. Add Data Management → Test independently → Deploy
5. Add Utilities → Test independently → Deploy
6. Polish → Final release

### Single Developer Strategy

Execute phases sequentially in priority order:
1. Phase 1 → Phase 2 (mandatory)
2. Phase 3 (API - most business value)
3. Phase 4 (UI - user experience)
4. Phase 5 (Data - import/export)
5. Phase 6 (Utilities - nice to have)
6. Phase 7 (Polish)

---

## Task Summary

| Phase | Tasks | Parallel | Priority |
|-------|-------|----------|----------|
| 1. Setup | T001-T003 | 2/3 | Required |
| 2. Foundational | T004-T018 + T014b | 2/16 | **CRITICAL** |
| 3. FR-2 (API) | T019-T029 | 2/11 | P1 - MVP |
| 4. FR-4 (UI) | T030-T039 | 4/10 | P2 |
| 5. FR-3 (Data) | T040-T047 | 2/8 | P3 |
| 6. FR-5 (Utils) | T048-T051d | 3/7 | P4 |
| 7. Polish | T052-T058 | 1/7 | Final |

**Total Tasks**: 62  
**Parallelizable**: 16 (26%)  
**Estimated Time**: 4-6 hours (sequential)

---

## Notes

- [P] tasks = different files, no dependencies, can run simultaneously
- [FR] label maps task to functional requirement from spec.md
- Shield MUST be configured first - it provides the permission system for all other plugins
- Verify tests fail before implementing (TDD approach per project guidelines)
- Commit after each phase or logical group
- Stop at any checkpoint to validate functionality independently
- Run `./vendor/bin/pint --dirty` before finalizing any PHP changes
