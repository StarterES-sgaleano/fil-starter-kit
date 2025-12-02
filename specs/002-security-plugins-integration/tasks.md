# Tasks: Security & i18n Plugins Integration

**Input**: Design documents from `/specs/002-security-plugins-integration/`
**Prerequisites**: plan.md ✅, spec.md ✅, research.md ✅, data-model.md ✅, contracts/ ✅, quickstart.md ✅

**Tests**: Feature tests included per SC-004 requirement ("All three plugins pass feature tests without errors")

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

-   **[P]**: Can run in parallel (different files, no dependencies)
-   **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
-   Include exact file paths in descriptions

## Path Conventions

-   **Laravel Filament**: `app/`, `database/`, `resources/`, `tests/` at repository root
-   Providers in `app/Providers/`
-   Models in `app/Models/`
-   Migrations in `database/migrations/`
-   Tests in `tests/Feature/Filament/`

---

## Phase 1: Setup (Package Installation)

**Purpose**: Install all three plugins via Composer

-   [x] T001 Install filament-auto-logout package via `composer require niladam/filament-auto-logout`
-   [x] T002 Install filament-renew-password package via `composer require yebor974/filament-renew-password`
-   [x] T003 Install filament-language-switch package via `composer require bezhansalleh/filament-language-switch`
-   [x] T004 Run auto-logout installer via `php artisan filament-auto-logout:install`

**Checkpoint**: All packages installed, ready for configuration

---

## Phase 2: Foundational (Database & Model Updates)

**Purpose**: Database migrations and User model updates that ALL user stories depend on

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

-   [x] T005 Publish renew-password migrations via `php artisan vendor:publish --tag="filament-renew-password-migrations"`
-   [x] T006 Create locale migration via `php artisan make:migration add_locale_to_users_table`
-   [x] T007 Implement locale migration in database/migrations/XXXX_add_locale_to_users_table.php
-   [x] T008 Run all migrations via `php artisan migrate`
-   [x] T009 Add RenewPasswordContract interface to app/Models/User.php
-   [x] T010 Add RenewPassword trait to app/Models/User.php
-   [x] T011 Add new columns to $fillable array in app/Models/User.php
-   [x] T012 Add new casts to casts() method in app/Models/User.php
-   [x] T013 [P] Update UserFactory with new fields in database/factories/UserFactory.php
-   [x] T014 [P] Add withExpiredPassword() state to database/factories/UserFactory.php
-   [x] T015 [P] Add mustRenewPassword() state to database/factories/UserFactory.php

**Checkpoint**: Foundation ready - user story implementation can now begin

---

## Phase 3: User Story 1 - Auto Logout (Priority: P1) 🎯 MVP

**Goal**: Users are automatically logged out after 15 minutes of inactivity with 30-second warning

**Independent Test**: Log in, wait for timeout (or reduce for testing), verify logout and redirect

### Tests for User Story 1

-   [x] T016 [P] [US1] Create AutoLogoutTest feature test in tests/Feature/Filament/AutoLogoutTest.php
-   [x] T017 [P] [US1] Add test: plugin is registered in admin panel in tests/Feature/Filament/AutoLogoutTest.php
-   [x] T018 [P] [US1] Add test: auto-logout configuration values are correct in tests/Feature/Filament/AutoLogoutTest.php

### Implementation for User Story 1

-   [x] T019 [US1] Register AutoLogoutPlugin in app/Providers/Filament/AdminPanelProvider.php
-   [x] T020 [US1] Configure logoutAfter(900) for 15-minute timeout in AdminPanelProvider.php
-   [x] T021 [US1] Verify warning displays 30 seconds before logout and multi-tab sync works (plugin-provided behavior)

**Checkpoint**: Auto-logout functional - users logged out after 15 min idle with warning

---

## Phase 4: User Story 2 - Password Renewal (Priority: P1)

**Goal**: Users with passwords older than 90 days are forced to renew; admins can force renewal

**Independent Test**: Create user with expired password, log in, verify redirect to renewal page

### Tests for User Story 2

-   [x] T022 [P] [US2] Create RenewPasswordTest feature test in tests/Feature/Filament/RenewPasswordTest.php
-   [x] T023 [P] [US2] Add test: user with expired password is redirected to renewal page
-   [x] T024 [P] [US2] Add test: user with force_renew_password=true is redirected to renewal page
-   [x] T025 [P] [US2] Add test: user with fresh password can access panel normally

### Implementation for User Story 2

-   [x] T026 [US2] Register RenewPasswordPlugin in app/Providers/Filament/AdminPanelProvider.php
-   [x] T027 [US2] Configure passwordExpiresIn(90) for 90-day expiration in AdminPanelProvider.php
-   [x] T028 [US2] Configure forceRenewPassword() to enable admin-forced renewal in AdminPanelProvider.php
-   [x] T029 [US2] Verify 2FA completes before password renewal redirect (FR-011)

**Checkpoint**: Password renewal functional - expired passwords force renewal, admin can force

---

## Phase 5: User Story 3 - Language Switch (Priority: P2)

**Goal**: Users can switch panel language; preference persists to database

**Independent Test**: Switch language on login page, log in, verify preference persists across sessions

### Tests for User Story 3

-   [x] T030 [P] [US3] Create LanguageSwitchTest feature test in tests/Feature/Filament/LanguageSwitchTest.php
-   [x] T031 [P] [US3] Add test: language switcher is visible on login page
-   [x] T032 [P] [US3] Add test: locale preference is saved to user record
-   [x] T033 [P] [US3] Add test: invalid locale falls back to default 'es' (FR-012)

### Implementation for User Story 3

-   [x] T034 [US3] Add @source directive for language-switch to resources/css/filament/admin/theme.css
-   [x] T035 [US3] Configure LanguageSwitch locales in app/Providers/AppServiceProvider.php
-   [x] T036 [US3] Configure LanguageSwitch labels in app/Providers/AppServiceProvider.php
-   [x] T037 [US3] Configure visible(outsidePanels: true) in app/Providers/AppServiceProvider.php
-   [x] T038 [US3] Add LocaleChanged event listener to persist preference in AppServiceProvider.php
-   [x] T039 [US3] Add locale loading from user on boot in AppServiceProvider.php
-   [x] T040 [US3] Implement fallback to 'es' for invalid locales in AppServiceProvider.php
-   [x] T041 [US3] Rebuild assets via `npm run build`

**Checkpoint**: Language switch functional - users can switch language, preference persists

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Documentation updates and final validation

-   [x] T042 [P] Update docs/FILAMENT_PLUGINS.md - change status from ⏳ to ✅ for all three plugins
-   [x] T043 [P] Update docs/decisions.md - mark checklist items as complete
-   [x] T044 Run all tests via `composer run test`
-   [x] T045 Run code style check via `vendor/bin/pint --dirty`
-   [x] T046 Clear caches via `php artisan config:clear && php artisan cache:clear`
-   [x] T047 Validate quickstart.md steps manually

---

## Dependencies & Execution Order

### Phase Dependencies

-   **Setup (Phase 1)**: No dependencies - can start immediately
-   **Foundational (Phase 2)**: Depends on Setup completion - BLOCKS all user stories
-   **User Stories (Phase 3-5)**: All depend on Foundational phase completion
    -   US1 and US2 are both P1 priority - can run in parallel
    -   US3 is P2 priority - can run after or parallel with US1/US2
-   **Polish (Phase 6)**: Depends on all user stories being complete

### User Story Dependencies

-   **User Story 1 (Auto Logout)**: Can start after Phase 2 - No dependencies on other stories
-   **User Story 2 (Password Renewal)**: Can start after Phase 2 - No dependencies on other stories
-   **User Story 3 (Language Switch)**: Can start after Phase 2 - No dependencies on other stories

### Within Each User Story

-   Tests MUST be written and FAIL before implementation
-   Plugin registration before configuration
-   Configuration before verification
-   Story complete before moving to next priority

### Parallel Opportunities

-   T013, T014, T015 can run in parallel (different methods in same file - no merge conflicts)
-   T016, T017, T018 can run in parallel (same test file, different test methods)
-   T022-T025 can run in parallel (same test file, different test methods)
-   T030-T033 can run in parallel (same test file, different test methods)
-   T042, T043 can run in parallel (different documentation files)
-   All three user stories can run in parallel after Phase 2

---

## Parallel Example: Phase 2 Foundational

```bash
# Sequential (migrations must run in order):
T005 → T006 → T007 → T008

# Then parallel (different parts of User model/factory):
T009, T010, T011, T012  # User model updates (same file - sequential)
T013, T014, T015        # Factory updates (can be parallel - different methods)
```

## Parallel Example: All User Stories

```bash
# After Phase 2 completes, all stories can start in parallel:
Developer A: Phase 3 (US1 - Auto Logout)
Developer B: Phase 4 (US2 - Password Renewal)
Developer C: Phase 5 (US3 - Language Switch)
```

---

## Implementation Strategy

### MVP First (User Stories 1 + 2)

1. Complete Phase 1: Setup (install packages)
2. Complete Phase 2: Foundational (migrations + User model)
3. Complete Phase 3: User Story 1 (Auto Logout) - Security P1
4. Complete Phase 4: User Story 2 (Password Renewal) - Security P1
5. **STOP and VALIDATE**: Both security features working
6. Deploy/demo security MVP

### Full Delivery

1. Complete MVP above
2. Complete Phase 5: User Story 3 (Language Switch) - i18n P2
3. Complete Phase 6: Polish & Documentation
4. Final validation and deployment

---

## Notes

-   [P] tasks = different files, no dependencies
-   [Story] label maps task to specific user story for traceability
-   Each user story is independently completable and testable
-   Verify tests fail before implementing
-   Commit after each task or logical group
-   Stop at any checkpoint to validate story independently
-   All plugins are configuration-only - no custom code beyond model traits
