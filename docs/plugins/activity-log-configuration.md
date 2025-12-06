# Activity Log Configuration

## Overview

Filament Activity Log integrates with Spatie Laravel Activitylog to provide a comprehensive audit trail of model changes within the Filament admin panel.

## Prerequisites

This plugin requires Spatie Laravel Activitylog to be installed and configured first.

## Installation & Setup

### 1. Install Spatie Activitylog
```bash
composer require spatie/laravel-activitylog
```

### 2. Publish and Run Migrations
```bash
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan migrate
```

### 3. Install Filament Plugin
```bash
composer require pxlrbt/filament-activity-log
```

## Model Configuration

### Add LogsActivity Trait

Add the trait to models you want to track:

```php
// app/Models/User.php
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use LogsActivity;
    
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    
    protected static $logAttributes = ['name', 'email'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
}
```

### Activity Log Options

```php
// Available configuration options
protected static $logAttributes = ['*'];              // Log all attributes
protected static $logOnlyDirty = true;                // Only log changed attributes
protected static $submitEmptyLogs = false;            // Don't log if no changes
protected static $logName = 'user';                   // Custom log name
protected static $ignoreChangedAttributes = ['password']; // Exclude attributes
```

## Page Creation

### Create Activity Log Page
```bash
php artisan make:filament-page ActivityLogPage --resource=UserResource
```

```php
// app/Filament/Pages/ActivityLogPage.php
use Filament\Pages\Page;
use Spatie\Activitylog\Models\Activity;

class ActivityLogPage extends Page
{
    protected static string $view = 'filament.pages.activity-log';
    
    public function mount(): void
    {
        $this->authorize('viewAny', Activity::class);
    }
}
```

## Theme Configuration

Update your theme.css to include Activity Log styles:

```css
@source '../../../../vendor/pxlrbt/filament-activity-log/resources/css/styles.css';
```

## Features

### Activity Display
- **Model Changes**: Track create, update, delete operations
- **Attribute Details**: Show before/after values
- **User Tracking**: Automatically log the user who made changes
- **Timestamp**: Precise change timestamps

### Filtering Options
- **By Model**: Filter activities by specific model type
- **By User**: Filter by the user who performed actions
- **By Date**: Date range filtering
- **By Action Type**: Create, update, delete filters

### Custom Events
```php
// Log custom activities
activity()
    ->performedOn($user)
    ->causedBy($authUser)
    ->withProperties(['custom' => 'data'])
    ->log('Custom action performed');
```

## Configuration Options

### Activity Log Configuration (config/activitylog.php)

```php
return [
    'enabled' => env('ACTIVITY_LOGGER_ENABLED', true),
    'default_log_name' => 'default',
    'log_name' => null,
    'subject_returns_soft_deleted_models' => true,
    'causer_returns_soft_deleted_models' => true,
];
```

## Security Considerations

- Activity logs contain sensitive data about model changes
- Restrict access to appropriate user roles
- Consider log retention policies for compliance
- Implement proper authorization checks on activity log pages

## Performance Tips

- Use `logOnlyDirty` to reduce database storage
- Set up log cleanup jobs for old activities
- Consider excluding sensitive attributes from logging
- Monitor activity log table size in production
