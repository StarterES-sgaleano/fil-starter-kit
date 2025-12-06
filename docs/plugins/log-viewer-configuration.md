# Log Viewer Configuration

## Overview

Filament Log Viewer allows you to view and manage Laravel logs directly from the Filament admin panel without requiring SSH access.

## Installation & Setup

### 1. Install Package
```bash
composer require achyutn/filament-log-viewer
```

### 2. Publish Configuration (Optional)
```bash
php artisan vendor:publish --tag=filament-log-viewer-config
```

## Panel Provider Registration

```php
use AchyutN\FilamentLogViewer\FilamentLogViewer;

->plugins([
    FilamentLogViewer::make(),
])
```

## Configuration Options

### File Size Limit

Configure maximum log file size to prevent performance issues:

```php
// config/filament-log-viewer.php
return [
    'max_log_file_size' => env('LOG_MAX_SIZE_KB', 2048), // 2MB default
];
```

### Environment Variables

```env
LOG_MAX_SIZE_KB=20480  # 20MB for production
```

## Features

### Table Columns
- **Log Level**: Badge with color mapping (error, warning, info, debug)
- **Environment**: Application environment (local, production, etc.)
- **File**: Log file name (laravel.log, etc.)
- **Message**: Short summary of the log entry
- **Occurred**: Human-readable date/time

### Filters
- **Log Levels**: Filter by error, warning, info, debug levels
- **Date Range**: Select specific date ranges
- **Toggle Columns**: Show/hide environment and file columns

### Mail Preview
If logs contain mail messages, preview them directly from the table interface.

## Usage

Access logs at `/logs` in your Filament panel. The interface provides:

1. **Real-time viewing**: No need to SSH into server
2. **Search functionality**: Find specific log entries
3. **Stack traces**: Click to view full error details
4. **Download options**: Export log files if needed

## Security Considerations

- Log viewer respects Laravel's file permissions
- Only users with appropriate Filament permissions can access
- Consider file size limits for production environments
- May want to restrict access to super_admin users only

## Performance Tips

- Set appropriate `max_log_file_size` for your environment
- Regular log rotation recommended for high-traffic applications
- Consider using log aggregation services for production scaling
