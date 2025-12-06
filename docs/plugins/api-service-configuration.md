# API Service Configuration

## Overview

Filament API Service automatically generates REST API endpoints for all Filament resources with OpenAPI/Swagger documentation.

## Installation & Setup

### 1. Install Package
```bash
composer require rupadana/filament-api-service
```

### 2. Publish Configuration
```bash
php artisan vendor:publish --tag=api-service-config
```

### 3. Install Laravel API (if not already done)
```bash
php artisan install:api --no-interaction
```

## Panel Provider Registration

```php
use Rupadana\ApiService\ApiServicePlugin;

->plugins([
    ApiServicePlugin::make()
        ->middleware([
            // Custom middleware
        ]),
])
```

## Configuration Options

### Recommended Configuration (config/api-service.php)

```php
return [
    'prefix' => 'api',
    'middleware' => ['api'],
    'resources' => [
        'enabled' => true,
        'except' => [
            // Resources to exclude from API generation
        ],
    ],
    'documentation' => [
        'enabled' => true,
        'title' => env('APP_NAME') . ' API',
        'version' => '1.0.0',
        'description' => 'Auto-generated API documentation',
    ],
];
```

## Features

- **Auto-generated Endpoints**: CRUD operations for all resources
- **OpenAPI Documentation**: Interactive API docs
- **Authentication Support**: Works with Sanctum tokens
- **Customizable**: Exclude resources, add middleware
- **Versioning**: Built-in API versioning support

## Usage Examples

### Resource Registration
All Filament resources are automatically registered as API endpoints:

```
GET    /api/users
POST   /api/users
GET    /api/users/{id}
PUT    /api/users/{id}
DELETE /api/users/{id}
```

### Authentication
Use Sanctum tokens for API authentication:

```bash
curl -H "Authorization: Bearer {token}" \
     -H "Content-Type: application/json" \
     https://your-app.com/api/users
```

## Integration with Breezy

API Service integrates seamlessly with Breezy's Sanctum token management for secure API access.
