# Queueable Bulk Actions Configuration

## Overview

Filament Queueable Bulk Actions processes bulk operations in the background using Laravel queues, providing real-time progress notifications and improved user experience.

## Prerequisites

Requires a working queue configuration. For development, database queues are recommended.

## Installation & Setup

### 1. Install Package
```bash
composer require bytexr/filament-queueable-bulk-actions "^4.0"
```

### 2. Publish and Run Migrations
```bash
php artisan vendor:publish --tag="queueable-bulk-actions-migrations"
php artisan migrate
```

### 3. Publish Configuration (Optional)
```bash
php artisan vendor:publish --tag="queueable-bulk-actions-config"
```

## Queue Configuration

### Configure Queue Driver
```bash
# Add to .env
QUEUE_CONNECTION=database
```

### Run Queue Worker
```bash
# For development
php artisan queue:work

# For production
php artisan queue:work --daemon
```

## Panel Provider Registration

```php
use Bytexr\QueueableBulkActions\QueueableBulkActionsPlugin;
use Bytexr\QueueableBulkActions\Enums\StatusEnum;

->plugins([
    QueueableBulkActionsPlugin::make()
        ->bulkActionModel(\Bytexr\QueueableBulkActions\Models\BulkAction::class)
        ->pollingInterval('5s')
        ->queue('database', 'default')
        ->colors([
            StatusEnum::QUEUED->value => 'slate',
            StatusEnum::IN_PROGRESS->value => 'info',
            StatusEnum::FINISHED->value => 'success',
            StatusEnum::FAILED->value => 'danger',
        ]),
])
```

## Creating Bulk Action Jobs

### 1. Create Job Class
```bash
php artisan make:job DeleteUserBulkActionJob
```

### 2. Extend BulkActionJob
```php
// app/Jobs/DeleteUserBulkActionJob.php
<?php

namespace App\Jobs;

use Bytexr\QueueableBulkActions\Filament\Actions\ActionResponse;
use Bytexr\QueueableBulkActions\Jobs\BulkActionJob;
use Illuminate\Foundation\Queue\Queueable;

class DeleteUserBulkActionJob extends BulkActionJob
{
    use Queueable;
    
    protected function action($record, ?array $data): ActionResponse
    {
        if ($record->isAdmin()) {
            return ActionResponse::make()
                ->failure()
                ->message('Admin users cannot be deleted');
        }
        
        $record->delete();
        
        return ActionResponse::make()
            ->success()
            ->message('User deleted successfully');
    }
}
```

### 3. Use in Resource
```php
// app/Filament/Resources/UserResource.php
use Bytexr\QueueableBulkActions\Filament\Actions\QueueableBulkAction;

->bulkActions([
    QueueableBulkAction::make('delete_users')
        ->label('Delete selected')
        ->job(DeleteUserBulkActionJob::class)
        ->requiresConfirmation(),
])
```

## ActionResponse Methods

### Success Response
```php
return ActionResponse::make()
    ->success()
    ->message('Operation completed successfully');
```

### Failure Response
```php
return ActionResponse::make()
    ->failure()
    ->message('Operation failed: reason');
```

### Custom Data
```php
return ActionResponse::make()
    ->success()
    ->message('Import completed')
    ->data(['imported_count' => 150]);
```

## Features

### Real-time Notifications
- Progress updates every 5 seconds (configurable)
- Status badges: Queued, In Progress, Finished, Failed
- Contextual notifications only on the initiating page
- Dismissible notifications with detailed information

### Bulk Action History
- All bulk actions are preserved for reference
- Access via BulkActionResource in the panel
- Detailed logs of success/failure rates
- Timestamp and user tracking

### Queue Management
- Configurable queue connections and names
- Support for different queue drivers (Redis, Database, etc.)
- Automatic retry on failure
- Customizable polling intervals

## Configuration Options

### Plugin Configuration
- `bulkActionModel`: Custom model for bulk actions
- `pollingInterval`: Update frequency (set to null to disable)
- `queue`: Queue connection and name
- `colors`: Custom status colors
- `renderHook`: Notification position

### Job Patterns
- Extend `BulkActionJob` for consistency
- Return `ActionResponse` for user feedback
- Access to `$record` and `$data` parameters
- Support for custom validation logic

## Performance Considerations

- Use appropriate queue drivers for production (Redis recommended)
- Monitor queue worker performance
- Consider job timeouts for long-running operations
- Implement proper error handling and retry logic

## Security

- Jobs inherit current user permissions
- Validate user permissions in job actions
- Consider rate limiting for bulk operations
- Audit trail preserved in bulk action history
