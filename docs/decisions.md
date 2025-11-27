# Decisions.md - Plugin Configuration Plan

Este documento detalla las decisiones de configuración para los 18 plugins de Filament instalados, organizados por orden de configuración basado en dependencias.

---

## Resumen Ejecutivo

### Plugins por Nivel de Configuración

| Nivel | Plugins | Descripción |
|-------|---------|-------------|
| **CRÍTICO** | filament-shield, filament-breezy | Requieren configuración obligatoria antes de usar el panel |
| **ALTO** | filament-api-service, commentions, filament-webhook-client | Requieren migraciones y/o traits en modelos |
| **MEDIO** | filament-apex-charts, filament-quick-create, guava/calendar, flowforge | Requieren registro en Panel Provider |
| **BAJO** | Resto de plugins | Solo uso directo sin configuración previa |

---

## Fase 1: Configuración Crítica de Seguridad

### 1.1 filament-shield (bezhansalleh/filament-shield)

**Prioridad:** 🔴 CRÍTICA - Debe configurarse PRIMERO

**Razón:** Define el sistema de permisos que afecta a todos los demás recursos.

#### Pasos de Configuración:

```bash
# 1. Publicar configuración
php artisan vendor:publish --tag="filament-shield-config"

# 2. Ejecutar setup (crea roles, permisos, super_admin)
php artisan shield:setup --no-interaction
```

#### Cambios en Modelo User:

```php
// app/Models/User.php
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasRoles;
    
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole('super_admin') || $this->hasAnyPermission([/* ... */]);
    }
}
```

#### Registro en AdminPanelProvider:

```php
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;

->plugins([
    FilamentShieldPlugin::make()
        ->gridColumns(['default' => 1, 'sm' => 2, 'lg' => 3])
        ->sectionColumnSpan(1)
        ->checkboxListColumns(['default' => 1, 'sm' => 2, 'lg' => 4])
        ->resourceCheckboxListColumns(['default' => 1, 'sm' => 2]),
])
```

**Decisión:** Usar el rol `super_admin` por defecto para administradores completos.

---

### 1.2 filament-breezy (jeffgreco13/filament-breezy)

**Prioridad:** 🔴 CRÍTICA - Segundo en configurarse

**Razón:** Proporciona autenticación 2FA y página de perfil.

#### Pasos de Configuración:

```bash
# 1. Ejecutar instalador
php artisan breezy:install --no-interaction
```

#### Cambios en Modelo User (si 2FA habilitado):

```php
// app/Models/User.php
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use TwoFactorAuthenticatable;
    
    // Para avatar personalizado
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }
}
```

#### Registro en AdminPanelProvider:

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
            force: false // No forzar 2FA a todos los usuarios
        )
        ->enableSanctumTokens(
            permissions: ['create', 'view', 'update', 'delete']
        ),
])
```

#### Actualizar theme.css:

```css
@source '../../../../vendor/jeffgreco13/filament-breezy/resources/**/*.blade.php';
```

**Decisión:** Habilitar 2FA opcional (no forzado), permitir tokens Sanctum para API.

---

## Fase 2: Configuración de API e Integraciones

### 2.1 filament-api-service (rupadana/filament-api-service)

**Prioridad:** 🟠 ALTA

**Razón:** Proporciona API RESTful automática para todos los resources.

#### Pasos de Configuración:

```bash
# 1. Publicar configuración
php artisan vendor:publish --tag=api-service-config

# 2. Ejecutar install:api de Laravel (si no se ha hecho)
php artisan install:api --no-interaction
```

#### Registro en AdminPanelProvider:

```php
use Rupadana\ApiService\ApiServicePlugin;

->plugins([
    ApiServicePlugin::make()
        ->middleware([
            // Middlewares personalizados
        ]),
])
```

#### Configuración Recomendada (config/api-service.php):

```php
return [
    'route' => [
        'panel_prefix' => true, // Prefijo /api/admin/
        'use_resource_middlewares' => true,
    ],
    'tenancy' => [
        'enabled' => false,
        'awareness' => false,
    ],
    'use-spatie-permission-middleware' => true, // Integración con Shield
];
```

**Decisión:** Mantener prefijo de panel, integrar con Shield para permisos API.

---

### 2.2 filament-webhook-client (tapp/filament-webhook-client)

**Prioridad:** 🟠 ALTA

**Razón:** Requiere instalación previa de spatie/laravel-webhook-client.

#### Pasos de Configuración:

```bash
# 1. Instalar y configurar Spatie Webhook Client primero
composer require spatie/laravel-webhook-client
php artisan vendor:publish --provider="Spatie\WebhookClient\WebhookClientServiceProvider" --tag="webhook-client-migrations"
php artisan migrate

# 2. Publicar config del plugin
php artisan vendor:publish --tag="filament-webhook-client-config"
```

#### Registro en AdminPanelProvider:

```php
use Tapp\FilamentWebhookClient\FilamentWebhookClientPlugin;

->plugins([
    FilamentWebhookClientPlugin::make(),
])
```

**Decisión:** Ya tenemos filament-webhook-server instalado, el client complementa para recibir webhooks.

---

### 2.3 commentions (kirschbaum-development/commentions)

**Prioridad:** 🟠 ALTA

**Razón:** Requiere migraciones y traits en modelos.

#### Pasos de Configuración:

```bash
# 1. Publicar migraciones
php artisan vendor:publish --tag="commentions-migrations"

# 2. Ejecutar migraciones
php artisan migrate

# 3. (Opcional) Publicar configuración
php artisan vendor:publish --tag="commentions-config"
```

#### Cambios en Modelo User:

```php
// app/Models/User.php
use Kirschbaum\Commentions\Contracts\Commenter;

class User extends Authenticatable implements Commenter
{
    // Ya debe tener HasRoles de Shield
}
```

#### En Modelos Comentables:

```php
// Ejemplo: app/Models/Asset.php
use Kirschbaum\Commentions\HasComments;
use Kirschbaum\Commentions\Contracts\Commentable;

class Asset extends Model implements Commentable
{
    use HasComments;
}
```

**Decisión:** Configurar comentarios con suscripciones habilitadas para notificaciones.

---

## Fase 3: Plugins de Panel (Registro en Provider)

### 3.1 filament-apex-charts (leandrocfe/filament-apex-charts)

**Prioridad:** 🟡 MEDIA

**Razón:** Solo requiere registro en panel.

#### Registro en AdminPanelProvider:

```php
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;

->plugins([
    FilamentApexChartsPlugin::make(),
])
```

#### Crear Widget de Ejemplo:

```bash
php artisan make:filament-apex-charts AssetStatsChart
```

**Decisión:** Crear charts para dashboard con estadísticas de assets.

---

### 3.2 filament-quick-create (awcodes/filament-quick-create)

**Prioridad:** 🟡 MEDIA

#### Registro en AdminPanelProvider:

```php
use Awcodes\QuickCreate\QuickCreatePlugin;

->plugins([
    QuickCreatePlugin::make()
        ->sort(true)
        ->slideOver()
        ->keyBindings(['command+shift+c', 'ctrl+shift+c']),
])
```

#### Actualizar theme.css:

```css
@source '../../../../vendor/awcodes/filament-quick-create/resources/**/*.blade.php';
```

**Decisión:** Habilitar slide-over y keybindings para acceso rápido.

---

### 3.3 guava/calendar

**Prioridad:** 🟡 MEDIA

**Razón:** Requiere custom theme y assets.

#### Pasos de Configuración:

```bash
# Publicar assets
php artisan filament:assets
```

#### Actualizar theme.css:

```css
@source '../../../../vendor/guava/calendar/resources/**/*';
@import '../../../../vendor/guava/calendar/resources/css/theme.css';
```

**Decisión:** Usar para visualización de mantenimientos programados de assets.

---

### 3.4 flowforge (relaticle/flowforge)

**Prioridad:** 🟡 MEDIA

#### Crear Kanban Board:

```bash
php artisan flowforge:make-board AssetWorkflowBoard --model=Asset
```

**Decisión:** Implementar para flujos de trabajo de gestión de assets (nuevo, en uso, en mantenimiento, retirado).

---

## Fase 4: Plugins de Uso Directo (Sin Configuración Panel)

### 4.1 filament-excel (pxlrbt/filament-excel)

**Tipo:** Uso en Resources

```php
// En cualquier Resource table
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

->bulkActions([
    ExportBulkAction::make(),
])
```

**Decisión:** Añadir exportación a todos los resources principales.

---

### 4.2 filament-excel-import (eighty9nine/filament-excel-import)

**Tipo:** Uso en Resources (Header Action)

**Repositorio:** https://github.com/eighty9nine/filament-excel-import

**Requisito:** Modelos deben tener `$fillable` definido para Mass Assignment.

```php
// En ListRecords de cualquier Resource
use EightyNine\ExcelImport\ExcelImportAction;

protected function getHeaderActions(): array
{
    return [
        ExcelImportAction::make()
            ->color("primary")
            ->slideOver()
            ->validateUsing([
                'name' => 'required',
                'serial_number' => 'required|unique:assets',
            ])
            ->sampleExcel(
                sampleData: [
                    ['name' => 'Laptop HP', 'serial_number' => 'ABC123'],
                ],
                fileName: 'assets-sample.xlsx',
            ),
        Actions\CreateAction::make(),
    ];
}

// Para RelationManagers
use EightyNine\ExcelImport\Tables\ExcelImportRelationshipAction;

->headerActions([
    ExcelImportRelationshipAction::make()
        ->slideOver()
        ->color('primary'),
])
```

**Decisión:** Añadir importación con validación y archivo de ejemplo a resources principales.

---

### 4.3 filament-badgeable-column (awcodes/filament-badgeable-column)

**Tipo:** Uso en Tables

#### Actualizar theme.css:

```css
@source '../../../../vendor/awcodes/filament-badgeable-column/resources/**/*.blade.php';
```

```php
use Awcodes\BadgeableColumn\Components\Badge;
use Awcodes\BadgeableColumn\Components\BadgeableColumn;

BadgeableColumn::make('name')
    ->suffixBadges([
        Badge::make('status')
            ->label(fn($record) => $record->status)
            ->color(fn($record) => match($record->status) {
                'active' => 'success',
                'maintenance' => 'warning',
                default => 'gray',
            }),
    ])
```

---

### 4.4 filament-modal-relation-managers (guava/filament-modal-relation-managers)

**Tipo:** Uso en Actions

#### Actualizar theme.css:

```css
@source '../../../../vendor/guava/filament-modal-relation-managers/resources/**/*';
```

```php
use Guava\FilamentModalRelationManagers\Actions\RelationManagerAction;

->actions([
    RelationManagerAction::make('view-components')
        ->label('Components')
        ->relationManager(ComponentRelationManager::make())
        ->compact(),
])
```

---

### 4.5 filament-layout-manager (asosick/filament-layout-manager)

**Tipo:** Para páginas personalizables

```bash
php artisan make:filament-page DashboardPage
```

```php
use Asosick\FilamentLayoutManager\Pages\LayoutManagerPage;

class DashboardPage extends LayoutManagerPage
{
    protected function getComponents(): array
    {
        return [
            AssetStatsChart::class,
            RecentAssetsWidget::class,
        ];
    }
}
```

---

### 4.6 Plugins Spatie Oficiales

#### spatie-laravel-settings-plugin

**Uso:** Crear Settings Pages

```bash
php artisan make:settings-migration CreateGeneralSettings
php artisan make:filament-settings-page ManageGeneral
```

#### spatie-laravel-tags-plugin

**Uso:** En formularios

```php
use Filament\Forms\Components\SpatieTagsInput;

SpatieTagsInput::make('tags')
    ->type('asset-categories'),
```

---

## Orden de Configuración Final

1. ✅ **filament-shield** - Sistema de permisos base
2. ✅ **filament-breezy** - Autenticación y perfil
3. ⏳ **filament-api-service** - API REST
4. ⏳ **filament-webhook-client** - Webhooks entrantes
5. ⏳ **commentions** - Sistema de comentarios
6. ⏳ **filament-apex-charts** - Gráficos
7. ⏳ **filament-quick-create** - Creación rápida
8. ⏳ **guava/calendar** - Calendario
9. ⏳ **flowforge** - Kanban
10. ✅ **filament-webhook-server** - Ya configurado
11. 🟢 **Resto** - Uso directo sin configuración

---

## AdminPanelProvider.php - Configuración Completa

```php
<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Session\Middleware\StartSession;
// ... otros imports

// Plugins
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Rupadana\ApiService\ApiServicePlugin;
use Awcodes\QuickCreate\QuickCreatePlugin;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Tapp\FilamentWebhookClient\FilamentWebhookClientPlugin;
use Marjose123\FilamentWebhookServer\WebhookPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->plugins([
                // 1. Seguridad
                FilamentShieldPlugin::make()
                    ->gridColumns(['default' => 1, 'sm' => 2, 'lg' => 3])
                    ->sectionColumnSpan(1),
                
                // 2. Autenticación
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true,
                        shouldRegisterNavigation: false,
                    )
                    ->enableTwoFactorAuthentication(force: false)
                    ->enableSanctumTokens(),
                
                // 3. API
                ApiServicePlugin::make(),
                
                // 4. Webhooks
                WebhookPlugin::make()
                    ->enableApiRoutes()
                    ->keepLogs()
                    ->enablePlugin(),
                FilamentWebhookClientPlugin::make(),
                
                // 5. UI
                QuickCreatePlugin::make()
                    ->slideOver()
                    ->keyBindings(['command+shift+c', 'ctrl+shift+c']),
                    
                // 6. Charts
                FilamentApexChartsPlugin::make(),
            ])
            // ... resto de configuración
    }
}
```

---

## Checklist de Configuración

- [ ] Ejecutar `php artisan shield:setup`
- [ ] Ejecutar `php artisan breezy:install`
- [ ] Añadir trait `HasRoles` al User
- [ ] Añadir trait `TwoFactorAuthenticatable` al User
- [ ] Ejecutar `php artisan install:api`
- [ ] Publicar migraciones de commentions
- [ ] Configurar theme.css con todos los @source
- [ ] Registrar todos los plugins en AdminPanelProvider
- [ ] Crear factories/seeders para roles y permisos
- [ ] Ejecutar `vendor/bin/pint --dirty`
- [ ] Ejecutar tests

---

*Documento generado para el proyecto IT Assets Manager Boilerplate*
*Última actualización: Enero 2025*
