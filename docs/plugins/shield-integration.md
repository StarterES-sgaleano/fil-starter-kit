# Shield Permission Integration Pattern

## Overview

This document describes how Filament Shield integrates with the application to provide role-based access control (RBAC).

## Configuration

Shield is configured in `config/filament-shield.php` with the following key settings:

### Super Admin

The `super_admin` role is configured to use Laravel's gate system for authorization:

```php
'super_admin' => [
    'enabled' => true,
    'name' => 'super_admin',
    'define_via_gate' => true,
    'intercept_gate' => 'before',
],
```

When `define_via_gate` is `true`, Shield registers a gate "before" hook that grants all permissions to users with the `super_admin` role. This is the recommended approach as it:

1. Eliminates the need to explicitly assign all permissions to super_admin
2. Automatically grants access to new resources without reconfiguration
3. Uses Laravel's native authorization system

### Panel User

A basic `panel_user` role is available for users who need panel access without specific permissions:

```php
'panel_user' => [
    'enabled' => true,
    'name' => 'panel_user',
],
```

## User Model Requirements

The `User` model must implement the following:

```php
use Filament\Models\Contracts\FilamentUser;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasRoles;
    
    public function canAccessPanel(Panel $panel): bool
    {
        return true; // Or implement custom logic
    }
}
```

## Permission Naming Convention

Shield generates permissions using the format:

```
{action}:{subject}
```

Where:
- `action` is one of: `viewAny`, `view`, `create`, `update`, `delete`, `restore`, `forceDelete`
- `subject` is the model name in PascalCase

Example: `ViewAny:Role`, `Create:User`, `Delete:Permission`

## Generating Permissions

Run the following command to generate permissions for all resources:

```bash
php artisan shield:generate --all --no-interaction
```

This will:
1. Create permissions for all Filament resources
2. Create view permissions for pages and widgets
3. Generate policies for resources

## Testing Shield Integration

```php
public function test_super_admin_can_access_roles_page(): void
{
    $admin = User::factory()->create();
    $admin->assignRole('super_admin');

    $response = $this->actingAs($admin)->get('/admin/shield/roles');

    $response->assertOk();
}

public function test_regular_user_cannot_access_roles_page(): void
{
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/admin/shield/roles');

    $response->assertForbidden();
}
```

## Navigation Group

Shield resources are grouped under "Administración" in the navigation:

```php
'shield_resource' => [
    'navigation_group' => 'Administración',
    // ...
],
```

## Related Files

- `config/filament-shield.php` - Shield configuration
- `config/permission.php` - Spatie Permission configuration
- `app/Models/User.php` - User model with HasRoles trait
- `app/Policies/RolePolicy.php` - Generated role policy
- `database/seeders/ShieldSeeder.php` - Role seeder
