# filament-renew-password Configuration

Plugin para forzar la renovación de contraseñas según criterios configurables.

## Instalación

```bash
# Filament v4 (versión 3.x del plugin)
composer require yebor974/filament-renew-password

# Publicar y ejecutar migraciones
php artisan vendor:publish --tag="filament-renew-password-migrations"
php artisan migrate
```

### Migración Generada

Añade a la tabla `users`:

-   `last_renew_password_at` (timestamp) - Última renovación de contraseña
-   `force_renew_password` (boolean) - Forzar renovación en próximo login

## Configuración Básica

### Registro en AdminPanelProvider

```php
use Yebor974\Filament\RenewPassword\RenewPasswordPlugin;

->plugins([
    RenewPasswordPlugin::make(),
])
```

## Modos de Renovación

### 1. Renovación Periódica (Expiración)

Fuerza renovación cada X días:

```php
RenewPasswordPlugin::make()
    ->passwordExpiresIn(days: 30)  // Cada 30 días
```

Con columna personalizada:

```php
RenewPasswordPlugin::make()
    ->passwordExpiresIn(days: 30)
    ->timestampColumn('custom_password_renewed_at')
```

### 2. Renovación Forzada (Por Administrador)

Útil para contraseñas temporales o primer login:

```php
RenewPasswordPlugin::make()
    ->forceRenewPassword()
```

Con columna personalizada:

```php
RenewPasswordPlugin::make()
    ->forceRenewPassword(forceRenewColumn: 'must_change_password')
```

### 3. Ambos Modos Combinados

```php
RenewPasswordPlugin::make()
    ->passwordExpiresIn(days: 90)
    ->forceRenewPassword()
```

## Configuración del Modelo User

### Implementar Contrato e Interfaz

```php
use Yebor974\Filament\RenewPassword\Contracts\RenewPasswordContract;
use Yebor974\Filament\RenewPassword\Traits\RenewPassword;

class User extends Authenticatable implements FilamentUser, RenewPasswordContract
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;
    use RenewPassword;

    protected $fillable = [
        'name',
        'email',
        'password',
        'last_renew_password_at',
        'force_renew_password',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_renew_password_at' => 'datetime',
            'force_renew_password' => 'boolean',
        ];
    }
}
```

### Criterio Personalizado (Opcional)

Si necesitas lógica personalizada:

```php
class User extends Authenticatable implements RenewPasswordContract
{
    public function needRenewPassword(): bool
    {
        // Lógica personalizada
        return $this->password_changed_at === null
            || $this->password_changed_at->diffInDays(now()) > 90;
    }
}
```

## Personalización Avanzada

### Página de Renovación Personalizada

```php
RenewPasswordPlugin::make()
    ->renewPage(CustomRenewPassword::class)
```

### URI de Ruta (para i18n)

```php
RenewPasswordPlugin::make()
    ->routeUri('contrasena/renovar')  // Default: 'password/renew'
```

## Traducciones

```bash
php artisan vendor:publish --tag="filament-renew-password-translations"
```

## Uso en Seeders/Factories

### Forzar Renovación al Crear Usuario

```php
// En UserFactory o Seeder
User::create([
    'name' => 'New User',
    'email' => 'user@example.com',
    'password' => Hash::make('temporary123'),
    'force_renew_password' => true,  // Forzar cambio en primer login
]);
```

### Resetear Timestamp al Cambiar Contraseña

El trait `RenewPassword` maneja esto automáticamente cuando el usuario renueva su contraseña.

## Integración con Shield

Para administradores que crean usuarios:

1. Crear usuario con contraseña temporal
2. Marcar `force_renew_password = true`
3. El usuario debe cambiar contraseña en primer login

## Notas

-   Compatible con Filament v4 (usar versión 3.x del plugin)
-   Requiere migración para columnas adicionales
-   Complementa a `filament-breezy` para gestión completa de contraseñas
