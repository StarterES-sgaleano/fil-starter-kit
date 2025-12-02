# Filament Starter Kit

A production-ready Laravel 12 + Filament 4 boilerplate with 18+ pre-configured plugins for building admin panels.

## Tech Stack

- **PHP:** 8.2+
- **Laravel:** 12.x
- **Filament:** 4.x
- **Livewire:** 3.x
- **Tailwind CSS:** 4.x
- **Database:** SQLite (default)

## Features

### 🔐 Security & Authentication

- **filament-shield** - Role-based access control with Spatie Permissions
- **filament-breezy** - 2FA, profile management, browser sessions, Sanctum tokens
- **filament-auto-logout** - Auto-logout after 15 minutes of inactivity with warning
- **filament-renew-password** - Password expiration (90 days) and admin-forced renewal

### 🌐 Internationalization

- **filament-language-switch** - Language switcher (ES/EN) with database persistence

### 📊 Data & Visualization

- **filament-excel** - Export to Excel/CSV
- **filament-excel-import** - Import from Excel
- **filament-apex-charts** - Interactive charts
- **guava/calendar** - Calendar widget
- **flowforge** - Kanban boards

### 🎨 UI Components

- **filament-quick-create** - Quick create menu
- **filament-badgeable-column** - Badge columns
- **filament-modal-relation-managers** - Modal relation managers
- **filament-layout-manager** - Drag-drop dashboard layouts
- **blade-fluentui-system-icons** - 4000+ FluentUI icons

### 🔌 Integrations

- **filament-api-service** - Auto REST API with OpenAPI docs
- **filament-webhook-server** - Outgoing webhooks
- **filament-webhook-client** - Incoming webhooks viewer
- **commentions** - Comments with @mentions and reactions

## Installation

```bash
# Clone the repository
git clone <repository-url>
cd fil-starter-kit

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations and seeders
php artisan migrate --seed

# Build assets
npm run build

# Setup Shield (roles & permissions)
php artisan shield:setup
```

## Development

```bash
# Start development server (runs server, queue, logs, and vite)
composer run dev

# Run tests
composer run test

# Code style
vendor/bin/pint
```

## Documentation

- [Plugin Documentation](docs/FILAMENT_PLUGINS.md) - Detailed plugin configuration
- [Architecture Decisions](docs/decisions.md) - Design decisions and rationale

## License

MIT License
