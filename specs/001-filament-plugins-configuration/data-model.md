# Data Model: Filament Plugins Configuration

## Overview

Este documento define las entidades de base de datos requeridas por los plugins de Filament configurados en el proyecto IT Assets Manager.

---

## Plugin: Filament Shield (Spatie Permission)

### Table: `roles`

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| id | bigint | NO | Primary key |
| name | string(255) | NO | Role name (unique per guard) |
| guard_name | string(255) | NO | Guard name (default: 'web') |
| created_at | timestamp | YES | Creation timestamp |
| updated_at | timestamp | YES | Last update timestamp |

**Indexes**:
- UNIQUE: `roles_name_guard_name_unique` (name, guard_name)

**Default Roles**:
```
- super_admin (full access, bypasses all gates)
- panel_user (basic panel access)
```

### Table: `permissions`

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| id | bigint | NO | Primary key |
| name | string(255) | NO | Permission name (unique per guard) |
| guard_name | string(255) | NO | Guard name (default: 'web') |
| created_at | timestamp | YES | Creation timestamp |
| updated_at | timestamp | YES | Last update timestamp |

**Indexes**:
- UNIQUE: `permissions_name_guard_name_unique` (name, guard_name)

**Permission Naming Convention**:
```
Resource permissions: view_asset, view_any_asset, create_asset, update_asset, delete_asset
Page permissions: page_Dashboard
Widget permissions: widget_AssetsChart
```

### Table: `model_has_permissions`

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| permission_id | bigint | NO | FK to permissions.id |
| model_type | string(255) | NO | Model class (App\Models\User) |
| model_id | bigint | NO | Model ID |

**Indexes**:
- PRIMARY: (permission_id, model_id, model_type)
- INDEX: `model_has_permissions_model_id_model_type_index`

### Table: `model_has_roles`

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| role_id | bigint | NO | FK to roles.id |
| model_type | string(255) | NO | Model class (App\Models\User) |
| model_id | bigint | NO | Model ID |

**Indexes**:
- PRIMARY: (role_id, model_id, model_type)
- INDEX: `model_has_roles_model_id_model_type_index`

### Table: `role_has_permissions`

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| permission_id | bigint | NO | FK to permissions.id |
| role_id | bigint | NO | FK to roles.id |

**Indexes**:
- PRIMARY: (permission_id, role_id)

---

## Plugin: Filament Breezy

### Table: `users` (Modifications)

Breezy modifica la tabla `users` existente:

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| two_factor_secret | text | YES | Encrypted 2FA secret |
| two_factor_recovery_codes | text | YES | Encrypted recovery codes |
| two_factor_confirmed_at | timestamp | YES | When 2FA was confirmed |

**Note**: Si se usa Laravel Sanctum (ya instalado), los tokens se guardan en `personal_access_tokens`.

---

## Plugin: Laravel Sanctum (API Tokens)

### Table: `personal_access_tokens`

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| id | bigint | NO | Primary key |
| tokenable_type | string(255) | NO | Model class |
| tokenable_id | bigint | NO | Model ID |
| name | string(255) | NO | Token name |
| token | string(64) | NO | Hashed token (unique) |
| abilities | text | YES | JSON array of abilities |
| last_used_at | timestamp | YES | Last usage timestamp |
| expires_at | timestamp | YES | Expiration timestamp |
| created_at | timestamp | YES | Creation timestamp |
| updated_at | timestamp | YES | Last update timestamp |

**Indexes**:
- UNIQUE: `personal_access_tokens_token_unique` (token)
- INDEX: `personal_access_tokens_tokenable_type_tokenable_id_index`

---

## Plugin: Commentions

### Table: `comments`

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| id | bigint | NO | Primary key |
| commentable_type | string(255) | NO | Polymorphic model class |
| commentable_id | bigint | NO | Polymorphic model ID |
| user_id | bigint | YES | FK to users.id (null for guests) |
| parent_id | bigint | YES | FK to comments.id for replies |
| body | text | NO | Comment content |
| is_approved | boolean | NO | Approval status (default: true) |
| created_at | timestamp | YES | Creation timestamp |
| updated_at | timestamp | YES | Last update timestamp |

**Indexes**:
- INDEX: `comments_commentable_type_commentable_id_index`
- INDEX: `comments_user_id_index`
- INDEX: `comments_parent_id_index`

**Relationships**:
```php
// In any model with comments
public function comments(): MorphMany
{
    return $this->morphMany(Comment::class, 'commentable');
}
```

---

## Plugin: Webhook Client

### Table: `webhook_calls`

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| id | bigint | NO | Primary key |
| name | string(255) | NO | Webhook name/identifier |
| url | string(255) | NO | Target URL |
| headers | json | YES | Request headers |
| payload | json | YES | Request payload |
| exception | text | YES | Exception message if failed |
| response | json | YES | Response body |
| status_code | integer | YES | HTTP status code |
| created_at | timestamp | YES | Creation timestamp |
| updated_at | timestamp | YES | Last update timestamp |

**Indexes**:
- INDEX: `webhook_calls_name_index`
- INDEX: `webhook_calls_created_at_index`

---

## Plugin: FlowForge

### Table: `workflows`

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| id | bigint | NO | Primary key |
| name | string(255) | NO | Workflow name |
| description | text | YES | Workflow description |
| model_type | string(255) | NO | Target model class |
| trigger | string(255) | NO | Trigger type (created, updated, etc.) |
| conditions | json | YES | Trigger conditions |
| actions | json | NO | Workflow actions |
| is_active | boolean | NO | Active status (default: true) |
| run_count | integer | NO | Times executed (default: 0) |
| last_run_at | timestamp | YES | Last execution time |
| created_at | timestamp | YES | Creation timestamp |
| updated_at | timestamp | YES | Last update timestamp |

**Indexes**:
- INDEX: `workflows_model_type_index`
- INDEX: `workflows_is_active_index`

### Table: `workflow_runs`

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| id | bigint | NO | Primary key |
| workflow_id | bigint | NO | FK to workflows.id |
| model_type | string(255) | NO | Affected model class |
| model_id | bigint | NO | Affected model ID |
| status | string(255) | NO | Run status (success, failed, pending) |
| started_at | timestamp | NO | Execution start time |
| completed_at | timestamp | YES | Execution end time |
| output | json | YES | Execution output/logs |
| error | text | YES | Error message if failed |
| created_at | timestamp | YES | Creation timestamp |
| updated_at | timestamp | YES | Last update timestamp |

**Indexes**:
- INDEX: `workflow_runs_workflow_id_index`
- INDEX: `workflow_runs_model_type_model_id_index`
- INDEX: `workflow_runs_status_index`

---

## Plugin: Excel Import

### Table: `import_batches` (Optional)

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| id | bigint | NO | Primary key |
| user_id | bigint | NO | FK to users.id |
| model_type | string(255) | NO | Target model class |
| file_name | string(255) | NO | Original filename |
| total_rows | integer | NO | Total rows in file |
| processed_rows | integer | NO | Processed rows count |
| failed_rows | integer | NO | Failed rows count |
| status | string(255) | NO | Batch status |
| errors | json | YES | Error details |
| created_at | timestamp | YES | Creation timestamp |
| updated_at | timestamp | YES | Last update timestamp |

**Note**: Esta tabla es opcional y solo se crea si se habilita el tracking de importaciones.

---

## Entity Relationships Diagram

```
┌─────────────────┐       ┌─────────────────┐
│     users       │       │     roles       │
├─────────────────┤       ├─────────────────┤
│ id              │◄──────┤ model_id        │
│ name            │       │ model_type      │
│ email           │       │ role_id ────────┼──►
│ two_factor_*    │       └─────────────────┘
└────────┬────────┘              │
         │                       │
         │                       ▼
         │               ┌─────────────────┐
         │               │   permissions   │
         │               ├─────────────────┤
         │               │ id              │
         │               │ name            │
         │               │ guard_name      │
         │               └─────────────────┘
         │
         ▼
┌─────────────────┐       ┌─────────────────┐
│    comments     │       │    workflows    │
├─────────────────┤       ├─────────────────┤
│ id              │       │ id              │
│ commentable_*   │       │ name            │
│ user_id ────────┼───────│ model_type      │
│ parent_id       │       │ actions         │
│ body            │       └────────┬────────┘
└─────────────────┘                │
                                   │
                                   ▼
                          ┌─────────────────┐
                          │  workflow_runs  │
                          ├─────────────────┤
                          │ id              │
                          │ workflow_id     │
                          │ model_*         │
                          │ status          │
                          └─────────────────┘
```

---

## Migration Order

1. **Spatie Permission** (Shield dependency)
   - `create_permission_tables.php`
   
2. **Sanctum** (Breezy dependency)
   - Already exists: `create_personal_access_tokens_table.php`
   
3. **Breezy 2FA columns**
   - `add_two_factor_columns_to_users_table.php`
   
4. **Commentions**
   - `create_comments_table.php`
   
5. **Webhook Client**
   - `create_webhook_calls_table.php`
   
6. **FlowForge**
   - `create_workflows_table.php`
   - `create_workflow_runs_table.php`

---

## Validation Rules

### Roles
```php
'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
'guard_name' => ['required', 'string', 'max:255'],
```

### Permissions
```php
'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
'guard_name' => ['required', 'string', 'max:255'],
```

### Comments
```php
'body' => ['required', 'string', 'max:10000'],
'parent_id' => ['nullable', 'exists:comments,id'],
```

### Workflows
```php
'name' => ['required', 'string', 'max:255'],
'model_type' => ['required', 'string'],
'trigger' => ['required', 'string', 'in:created,updated,deleted'],
'actions' => ['required', 'array', 'min:1'],
```

---

## State Transitions

### Workflow Run Status
```
pending → running → success
                 → failed
```

### Import Batch Status
```
pending → processing → completed
                    → failed
                    → partial (some rows failed)
```

---

**Document Version**: 1.0  
**Created**: Phase 1 Planning  
**Last Updated**: $(date +%Y-%m-%d)
