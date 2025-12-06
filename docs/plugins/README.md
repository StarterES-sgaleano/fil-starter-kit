# Plugin Configuration Files

This directory contains detailed configuration instructions for each Filament plugin in the boilerplate.

## 📋 Available Configuration Files

### 🔐 Security & Authentication
- **[shield-integration.md](./shield-integration.md)** - Role-based permissions system
- **[breezy-configuration.md](./breezy-configuration.md)** - 2FA, profile management, and Sanctum tokens
- **[auto-logout-config.md](./auto-logout-config.md)** - Automatic session timeout
- **[renew-password-config.md](./renew-password-config.md)** - Password renewal policies

### 🌐 Internationalization
- **[language-switch-config.md](./language-switch-config.md)** - Multi-language support with flags

### 📊 Data & Export
- **[excel-usage.md](./excel-usage.md)** - Excel/CSV export functionality
- **[excel-import-usage.md](./excel-import-usage.md)** - Excel import with validation

### 📈 Visualization & UI
- **[badgeable-column-usage.md](./badgeable-column-usage.md)** - Badge columns for tables
- **[layout-manager-usage.md](./layout-manager-usage.md)** - Customizable dashboard layouts
- **[modal-relation-usage.md](./modal-relation-usage.md)** - Modal-based relation managers

### 🔌 Integrations & API
- **[api-service-configuration.md](./api-service-configuration.md)** - Auto-generated REST API endpoints
- **[webhook-client-configuration.md](./webhook-client-configuration.md)** - Incoming webhook management

### 🔍 Monitoring & Logs (New)
- **[log-viewer-configuration.md](./log-viewer-configuration.md)** - Laravel log viewer in admin panel
- **[activity-log-configuration.md](./activity-log-configuration.md)** - Model activity tracking with Spatie

### ⚡ Performance & Bulk Actions (New)
- **[queueable-bulk-actions-configuration.md](./queueable-bulk-actions-configuration.md)** - Queue-based bulk operations

## 📖 How to Use

Each configuration file contains:
- **Installation steps** - Composer commands and setup
- **Configuration options** - Panel provider registration and settings
- **Code examples** - Ready-to-use implementation patterns
- **Best practices** - Security and performance considerations
- **Integration notes** - How plugins work together

## 🏗️ Architecture

The documentation is structured to separate:
- **Decisions** (`../decisions.md`) - WHY we made certain choices
- **Configuration** (`./`) - HOW to implement each plugin

This separation makes it easier to:
1. Understand the reasoning behind architectural decisions
2. Find specific implementation details when needed
3. Maintain and update documentation independently

## 🚀 Quick Start

1. Start with **[shield-integration.md](./shield-integration.md)** and **[breezy-configuration.md](./breezy-configuration.md)** (critical security setup)
2. Configure integrations in **[api-service-configuration.md](./api-service-configuration.md)**
3. Add UI components based on your project needs
4. Set up monitoring with **[log-viewer-configuration.md](./log-viewer-configuration.md)** and **[activity-log-configuration.md](./activity-log-configuration.md)**

---

*For the architectural decisions behind these plugin choices, see [../decisions.md](../decisions.md)*
