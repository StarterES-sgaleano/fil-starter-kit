# filament-auto-logout Configuration

Plugin para cerrar automáticamente la sesión de usuarios inactivos.

## Instalación

```bash
composer require niladam/filament-auto-logout
php artisan filament-auto-logout:install
```

## Configuración Básica

### Registro en AdminPanelProvider

```php
use Niladam\FilamentAutoLogout\AutoLogoutPlugin;

->plugins([
    AutoLogoutPlugin::make(),
])
```

### Configuración Personalizada

```php
use Carbon\Carbon;
use Filament\Support\Colors\Color;
use Niladam\FilamentAutoLogout\AutoLogoutPlugin;

->plugins([
    AutoLogoutPlugin::make()
        ->color(Color::Emerald)                              // Color del badge (default: Stone)
        ->icon('heroicon-o-arrow-right-start-on-rectangle')  // Icono (default: heroicon-o-clock)
        ->disableIf(fn () => auth()->id() === 1)             // Deshabilitar para super admin
        ->logoutAfter(Carbon::SECONDS_PER_MINUTE * 5)        // Logout después de 5 minutos
        ->withoutWarning()                                   // Sin advertencia previa
        ->withoutTimeLeft()                                  // Sin mostrar tiempo restante
        ->timeLeftText('Sesión expira en...')                // Texto personalizado
])
```

## Archivo de Configuración

Publicar configuración:

```bash
php artisan vendor:publish --tag="filament-auto-logout-config"
```

### config/filament-auto-logout.php

```php
use Carbon\Carbon;
use Filament\View\PanelsRenderHook;

return [
    // Habilitar/deshabilitar el plugin
    'enabled' => env('FILAMENT_AUTO_LOGOUT_ENABLED', true),

    // Tiempo de inactividad antes del logout (en segundos)
    // Default: 15 minutos
    'duration_in_seconds' => env('FILAMENT_AUTO_LOGOUT_DURATION_IN_SECONDS', Carbon::SECONDS_PER_MINUTE * 15),

    // Segundos antes de mostrar la advertencia
    // Default: 30 segundos
    'warn_before_in_seconds' => env('FILAMENT_AUTO_LOGOUT_WARN_BEFORE_IN_SECONDS', 30),

    // Mostrar badge con tiempo restante
    'show_time_left' => env('FILAMENT_AUTO_LOGOUT_SHOW_TIME_LEFT', true),

    // Texto del badge
    'time_left_text' => env('FILAMENT_AUTO_LOGOUT_TIME_LEFT_TEXT', 'Time left:'),

    // Ubicación del badge (render hook)
    'location' => env('FILAMENT_AUTO_LOGOUT_LOCATION', PanelsRenderHook::GLOBAL_SEARCH_BEFORE),
];
```

## Variables de Entorno

```env
FILAMENT_AUTO_LOGOUT_ENABLED=true
FILAMENT_AUTO_LOGOUT_DURATION_IN_SECONDS=900
FILAMENT_AUTO_LOGOUT_WARN_BEFORE_IN_SECONDS=30
FILAMENT_AUTO_LOGOUT_SHOW_TIME_LEFT=true
FILAMENT_AUTO_LOGOUT_TIME_LEFT_TEXT="Tiempo restante:"
```

## Traducciones

```bash
php artisan vendor:publish --tag="filament-auto-logout-translations"
```

## Integración con Breezy

Este plugin complementa a `filament-breezy` para una gestión completa de sesiones:

-   **Breezy:** Gestión de sesiones de navegador (ver/cerrar sesiones activas)
-   **Auto-logout:** Cierre automático por inactividad

## Notas

-   Funciona con múltiples pestañas del navegador (sincroniza el estado)
-   El contador se reinicia con cualquier actividad del usuario
-   Compatible con Filament 4.x
