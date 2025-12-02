# Data Model: Security & i18n Plugins Integration

**Feature**: 002-security-plugins-integration  
**Date**: 2025-12-02

## Entity Changes

### User Model Updates

The `User` model requires the following additions:

#### New Columns

| Column                   | Type       | Default | Nullable | Purpose                            |
| ------------------------ | ---------- | ------- | -------- | ---------------------------------- |
| `locale`                 | string(10) | 'es'    | No       | User's preferred language          |
| `last_renew_password_at` | timestamp  | null    | Yes      | Last password renewal date         |
| `force_renew_password`   | boolean    | false   | No       | Admin-forced password renewal flag |

#### New Interfaces

```php
use Yebor974\Filament\RenewPassword\Contracts\RenewPasswordContract;

class User extends Authenticatable implements FilamentUser, RenewPasswordContract
```

#### New Traits

```php
use Yebor974\Filament\RenewPassword\Traits\RenewPassword;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;
    use RenewPassword; // NEW
}
```

#### Updated $fillable

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'locale',                    // NEW
    'last_renew_password_at',    // NEW
    'force_renew_password',      // NEW
];
```

#### Updated casts()

```php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_renew_password_at' => 'datetime',  // NEW
        'force_renew_password' => 'boolean',      // NEW
    ];
}
```

---

## Migrations

### Migration 1: Password Renewal Columns

**File**: `database/migrations/XXXX_XX_XX_XXXXXX_add_renew_password_columns_to_users_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_renew_password_at')->nullable()->after('password');
            $table->boolean('force_renew_password')->default(false)->after('last_renew_password_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_renew_password_at', 'force_renew_password']);
        });
    }
};
```

### Migration 2: Locale Column

**File**: `database/migrations/XXXX_XX_XX_XXXXXX_add_locale_to_users_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('locale', 10)->default('es')->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }
};
```

---

## State Transitions

### Password Renewal States

```
[Normal] ──(90 days pass)──> [Expired] ──(user renews)──> [Normal]
    │                            │
    │                            └──(login attempt)──> [Forced to Renew Page]
    │
    └──(admin sets force_renew)──> [Force Renewal] ──(user renews)──> [Normal]
```

### Session States (Auto-Logout)

```
[Active] ──(user activity)──> [Active] (timer reset)
    │
    └──(14:30 idle)──> [Warning Shown] ──(30s pass)──> [Logged Out]
                            │
                            └──(user activity)──> [Active] (timer reset)
```

---

## Validation Rules

### Password Renewal

-   `last_renew_password_at` MUST be updated when password changes
-   `force_renew_password` MUST be reset to `false` after successful renewal
-   New password MUST differ from current password (handled by plugin)

### Locale

-   `locale` MUST be one of configured locales: `['es', 'en']`
-   Default MUST be `'es'` for new users

---

## Factory Updates

### UserFactory

```php
public function definition(): array
{
    return [
        'name' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'email_verified_at' => now(),
        'password' => static::$password ??= Hash::make('password'),
        'remember_token' => Str::random(10),
        'locale' => 'es',                           // NEW
        'last_renew_password_at' => now(),          // NEW
        'force_renew_password' => false,            // NEW
    ];
}

// NEW: State for expired password
public function withExpiredPassword(): static
{
    return $this->state(fn (array $attributes) => [
        'last_renew_password_at' => now()->subDays(91),
    ]);
}

// NEW: State for forced renewal
public function mustRenewPassword(): static
{
    return $this->state(fn (array $attributes) => [
        'force_renew_password' => true,
    ]);
}
```

---

## Seeder Updates

### DatabaseSeeder

Ensure test users have valid password renewal timestamps:

```php
// Super admin with fresh password
User::factory()->create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'last_renew_password_at' => now(),
    'force_renew_password' => false,
    'locale' => 'es',
]);
```
