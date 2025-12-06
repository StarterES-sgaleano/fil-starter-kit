# Breezy Authentication Configuration

## Overview

Filament Breezy provides enhanced authentication features including 2FA, profile management, and API tokens.

## Installation & Setup

### 1. Install Package

```bash
composer require jeffgreco13/filament-breezy
```

### 2. Run Installer

```bash
php artisan breezy:install --no-interaction
```

## Model Configuration

### User Model Changes (for 2FA)

Add the TwoFactorAuthenticatable trait to your User model:

```php
// app/Models/User.php
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use TwoFactorAuthenticatable;

    // For custom avatar
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }
}
```

## Panel Provider Registration

Register Breezy in your AdminPanelProvider:

```php
use Jeffgreco13\FilamentBreezy\BreezyCore;

->plugins([
    BreezyCore::make()
        ->myProfile(
            shouldRegisterUserMenu: true,
            shouldRegisterNavigation: false,
            hasAvatars: false,
            slug: 'my-profile'
        )
        ->enableTwoFactorAuthentication(
            force: false, // Set to true to force 2FA for all users
            action: 'App\Filament\Actions\EnableTwoFactorAuthentication'
        )
        ->enableSanctumTokens(
            permissions: ['read', 'write', 'admin']
        ),
])
```

## Theme Configuration

Update your theme.css to include Breezy's views:

```css
@source '../../../../vendor/jeffgreco13/filament-breezy/resources/**/*.blade.php';
```

## Features

- **Two-Factor Authentication**: Optional or mandatory 2FA
- **Profile Management**: Customizable user profile pages
- **Sanctum Tokens**: API token management
- **Avatar Support**: Custom avatar handling
- **Browser Sessions**: Manage active sessions

## Configuration Options

### Two-Factor Authentication
- `force`: Boolean to require 2FA for all users
- `action`: Custom action class for 2FA enable/disable

### Profile Management
- `shouldRegisterUserMenu`: Add profile to user menu
- `shouldRegisterNavigation`: Add profile to navigation
- `hasAvatars`: Enable avatar support
- `slug`: URL slug for profile page

### Sanctum Tokens
- `permissions`: Array of available token permissions
