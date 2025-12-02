# Feature Specification: Security & i18n Plugins Integration

**Feature Branch**: `002-security-plugins-integration`  
**Created**: 2025-12-02  
**Status**: Draft  
**Input**: User description: "Implement and configure filament-auto-logout, filament-renew-password, and filament-language-switch plugins"

## User Scenarios & Testing _(mandatory)_

### User Story 1 - Auto Logout for Inactive Sessions (Priority: P1)

As an administrator, I want users to be automatically logged out after a period of inactivity so that unattended sessions don't pose a security risk.

**Why this priority**: Security is NON-NEGOTIABLE per constitution. Auto-logout prevents unauthorized access from unattended workstations.

**Independent Test**: Can be tested by logging in, waiting for the configured timeout, and verifying the session is terminated.

**Acceptance Scenarios**:

1. **Given** a user is logged in and idle for 15 minutes, **When** the timeout expires, **Then** the user is logged out and redirected to login page
2. **Given** a user has multiple browser tabs open, **When** they are active in one tab, **Then** all tabs remain logged in (activity syncs)
3. **Given** a user is about to be logged out, **When** 30 seconds remain, **Then** a warning notification is displayed

---

### User Story 2 - Password Renewal Enforcement (Priority: P1)

As an administrator, I want to enforce password renewal policies so that users maintain strong, up-to-date credentials.

**Why this priority**: Password hygiene is a security requirement per constitution. Stale passwords are a vulnerability.

**Independent Test**: Can be tested by creating a user with an expired password timestamp and verifying they are forced to change it on login.

**Acceptance Scenarios**:

1. **Given** a user's password is older than 90 days, **When** they log in, **Then** they are redirected to the password renewal page
2. **Given** an admin creates a new user with a temporary password, **When** the user logs in for the first time, **Then** they are forced to change their password
3. **Given** a user renews their password, **When** they complete the form, **Then** the timestamp is updated and they can access the panel

---

### User Story 3 - Language Switching (Priority: P2)

As a user, I want to switch the panel locale so that I can work in my preferred language.

**Why this priority**: Internationalization improves user experience but is not a security requirement.

**Independent Test**: Can be tested by switching language and verifying all UI elements update to the selected locale.

**Acceptance Scenarios**:

1. **Given** a user is on the login page, **When** they select a different language, **Then** the login page displays in that language
2. **Given** a user is logged in, **When** they change language, **Then** their preference is persisted to the database
3. **Given** a user logs in again, **When** the panel loads, **Then** it displays in their previously selected language

---

### Edge Cases (Resolved)

-   **Auto-logout with unsaved form**: Rely on the 30-second warning; user is responsible for saving. No custom form state preservation (Plugin-First principle).
-   **Password renewal + 2FA flow**: 2FA verification MUST complete first, then password renewal if needed. Full authentication required before account changes.
-   **Invalid locale preference**: Silently fall back to default locale (es). No error shown, graceful degradation.

## Requirements _(mandatory)_

### Functional Requirements

-   **FR-001**: System MUST automatically log out users after 15 minutes of inactivity
-   **FR-002**: System MUST display a warning 30 seconds before auto-logout
-   **FR-003**: System MUST sync activity state across browser tabs
-   **FR-004**: System MUST force password renewal every 90 days
-   **FR-005**: System MUST allow admins to force password renewal for specific users
-   **FR-006**: System MUST add `last_renew_password_at` and `force_renew_password` columns to users table
-   **FR-007**: System MUST provide language switching in the admin panel
-   **FR-008**: System MUST persist user language preference to database
-   **FR-009**: System MUST support Spanish (es) and English (en) locales initially
-   **FR-010**: System MUST display language switcher on login page
-   **FR-011**: System MUST complete 2FA verification before password renewal redirect
-   **FR-012**: System MUST fall back to default locale (es) if user's saved locale is unavailable

### Key Entities

-   **User**: Extended with `locale`, `last_renew_password_at`, `force_renew_password` attributes
-   **Session**: Managed by auto-logout plugin (no custom entity needed)

## Clarifications

### Session 2025-12-02

-   Q: What should happen when auto-logout triggers while a user has unsaved form data? → A: Rely on 30-second warning; user responsible for saving (Option B)
-   Q: How should password renewal interact with 2FA during login flow? → A: 2FA first, then password renewal if needed (Option A)
-   Q: What should happen if user's saved locale is for a removed language? → A: Silently fall back to default locale es (Option B)

## Success Criteria _(mandatory)_

### Measurable Outcomes

-   **SC-001**: Users are logged out within 5 seconds of the 15-minute timeout expiring
-   **SC-002**: 100% of users with passwords older than 90 days are prompted to renew on login
-   **SC-003**: Language preference persists across sessions with 100% reliability
-   **SC-004**: All three plugins pass feature tests without errors
-   **SC-005**: Documentation updated with ✅ status for all three plugins
