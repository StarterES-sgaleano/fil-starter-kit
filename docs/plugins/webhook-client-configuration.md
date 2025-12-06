# Webhook Client Configuration

## Overview

Filament Webhook Client provides a panel to view and manage incoming webhooks processed by Spatie Laravel Webhook Client.

## Prerequisites

Requires Spatie Laravel Webhook Client to be installed and configured first.

## Installation & Setup

### 1. Install Spatie Webhook Client
```bash
composer require spatie/laravel-webhook-client
```

### 2. Publish and Run Migrations
```bash
php artisan vendor:publish --provider="Spatie\WebhookClient\WebhookClientServiceProvider" --tag="webhook-client-migrations"
php artisan migrate
```

### 3. Install Filament Plugin
```bash
composer require tapp/filament-webhook-client
```

### 4. Publish Configuration
```bash
php artisan vendor:publish --tag="filament-webhook-client-config"
```

## Panel Provider Registration

```php
use Tapp\FilamentWebhookClient\FilamentWebhookClientPlugin;

->plugins([
    FilamentWebhookClientPlugin::make(),
])
```

## Configuration

### Webhook Client Configuration (config/webhook-client.php)

```php
return [
    'configs' => [
        'default' => [
            'name' => 'default',
            'signing_secret' => env('WEBHOOK_SIGNING_SECRET'),
            'signature_header_name' => 'Signature',
            'signature_validator' => \Spatie\WebhookClient\SignatureValidator\DefaultSignatureValidator::class,
            'webhook_profile' => \Spatie\WebhookClient\WebhookProfile\ProcessEverythingWebhookProfile::class,
            'webhook_model' => \Spatie\WebhookClient\Models\WebhookCall::class,
            'process_webhook_job' => \App\Jobs\ProcessWebhookJob::class,
        ],
    ],
];
```

### Environment Variables

```env
WEBHOOK_SIGNING_SECRET=your-secret-key-here
```

## Features

### Webhook Viewing
- **Incoming Requests**: View all received webhook calls
- **Request Details**: Headers, payload, and metadata
- **Processing Status**: Success/failure tracking
- **Error Logs**: Detailed error information for failed webhooks

### Filtering Options
- **By Status**: Filter successful/failed attempts
- **By Date**: Date range filtering
- **By Endpoint**: Filter by webhook endpoint
- **Search**: Search through webhook payloads

### Retry Management
- **Manual Retry**: Retry failed webhook processing
- **Retry History**: Track retry attempts
- **Automatic Retry**: Configure retry policies

## Creating Webhook Processors

### Custom Job Class
```php
// app/Jobs/ProcessWebhookJob.php
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob as SpatieProcessWebhookJob;
use Spatie\WebhookClient\Models\WebhookCall;

class ProcessWebhookJob extends SpatieProcessWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(WebhookCall $webhookCall): void
    {
        // Process webhook payload
        $data = json_decode($webhookCall->payload, true);
        
        // Your custom logic here
        logger('Webhook processed', ['data' => $data]);
    }
}
```

## Security Considerations

- Validate webhook signatures using signing secrets
- Implement proper authentication for webhook endpoints
- Rate limiting for webhook endpoints
- Sanitize webhook payloads before processing

## Monitoring

- Monitor webhook processing queues
- Set up alerts for failed webhook attempts
- Regular cleanup of old webhook logs
- Performance monitoring for webhook processing times

## Integration Tips

- Use with Webhook Server for full webhook management
- Combine with Activity Log for webhook audit trails
- Consider webhook retries for external service integrations
- Implement proper error handling for unreliable external services
