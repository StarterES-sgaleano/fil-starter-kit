# filament-language-switch Configuration

Plugin para cambiar el idioma de la aplicación en paneles Filament.

## Instalación

```bash
composer require bezhansalleh/filament-language-switch
```

### Actualizar theme.css

```css
@source '../../../../vendor/bezhansalleh/filament-language-switch/resources/views/**/*.blade.php';
```

Reconstruir assets:

```bash
npm run build
```

## Configuración Básica

### En AppServiceProvider

```php
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['es', 'en', 'fr']);
        });
    }
}
```

## Configuración Avanzada

### Configuración Completa

```php
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use BezhanSalleh\FilamentLanguageSwitch\Enums\Placement;

LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
    $switch
        // Locales soportados
        ->locales(['es', 'en', 'fr', 'pt_BR'])

        // Etiquetas personalizadas
        ->labels([
            'es' => 'Español',
            'en' => 'English',
            'fr' => 'Français',
            'pt_BR' => 'Português (BR)',
        ])

        // Banderas (opcional)
        ->flags([
            'es' => asset('flags/spain.svg'),
            'en' => asset('flags/usa.svg'),
            'fr' => asset('flags/france.svg'),
        ])

        // Visibilidad
        ->visible(
            insidePanels: true,
            outsidePanels: true
        )

        // Rutas fuera del panel donde mostrar
        ->outsidePanelRoutes([
            'filament.admin.auth.login',
            'filament.admin.auth.register',
        ])

        // Posición fuera del panel
        ->outsidePanelPlacement(Placement::TopRight)

        // Render hook dentro del panel
        ->renderHook('panels::global-search.after')

        // Excluir paneles específicos
        ->excludes(['api'])

        // Estilo circular
        ->circular();
});
```

### Solo Banderas (Sin Texto)

```php
LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
    $switch
        ->locales(['es', 'en', 'fr'])
        ->flags([
            'es' => asset('flags/spain.svg'),
            'en' => asset('flags/usa.svg'),
            'fr' => asset('flags/france.svg'),
        ])
        ->flagsOnly();
});
```

## Persistir Preferencia del Usuario

### Escuchar Evento LocaleChanged

```php
use BezhanSalleh\FilamentLanguageSwitch\Events\LocaleChanged;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(function (LocaleChanged $event) {
            if (auth()->check()) {
                auth()->user()->update(['locale' => $event->locale]);
            }
        });
    }
}
```

### Cargar Preferencia del Usuario

En un middleware o en `AppServiceProvider`:

```php
public function boot(): void
{
    // Cargar locale del usuario autenticado
    if (auth()->check() && auth()->user()->locale) {
        app()->setLocale(auth()->user()->locale);
    }
}
```

### Migración para Columna Locale

```bash
php artisan make:migration add_locale_to_users_table
```

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('locale', 10)->nullable()->after('email');
    });
}
```

## Archivos de Idioma

### Estructura Recomendada

```
lang/
├── es/
│   ├── auth.php
│   ├── pagination.php
│   ├── passwords.php
│   └── validation.php
├── en/
│   └── ...
└── fr/
    └── ...
```

### Publicar Traducciones de Laravel

```bash
php artisan lang:publish
```

## Ubicaciones Disponibles (Render Hooks)

-   `panels::global-search.before`
-   `panels::global-search.after` (default)
-   `panels::user-menu.before`
-   `panels::user-menu.after`

## Posiciones Fuera del Panel

```php
use BezhanSalleh\FilamentLanguageSwitch\Enums\Placement;

Placement::TopLeft      // default
Placement::TopCenter
Placement::TopRight
Placement::BottomLeft
Placement::BottomCenter
Placement::BottomRight
```

## Integración con el Boilerplate

### Idiomas Recomendados para España

```php
LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
    $switch
        ->locales(['es', 'en', 'ca', 'eu', 'gl'])
        ->labels([
            'es' => 'Español',
            'en' => 'English',
            'ca' => 'Català',
            'eu' => 'Euskara',
            'gl' => 'Galego',
        ]);
});
```

## Notas

-   Requiere custom theme para estilos
-   El namespace cambió en v4: `BezhanSalleh\FilamentLanguageSwitch`
-   Compatible con Filament v4
-   Funciona con múltiples paneles
