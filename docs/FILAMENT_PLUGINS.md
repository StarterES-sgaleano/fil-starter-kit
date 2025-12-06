# Filament Plugins Documentation

Este documento lista todos los plugins de Filament instalados en este proyecto boilerplate, organizados por categoría.

## 🔐 Seguridad y Autenticación

### filament-breezy

- **Paquete:** `jeffgreco13/filament-breezy`
- **Descripción:** Añade autenticación de dos factores (2FA), página de perfil personalizable, gestión de sesiones de navegador, y soporte para tokens de Laravel Sanctum.
- **Documentación:** <https://github.com/jeffgreco13/filament-breezy>

### filament-shield

- **Paquete:** `bezhansalleh/filament-shield`
- **Descripción:** Gestión completa de roles y permisos usando Spatie Laravel Permission. Genera permisos automáticamente para resources, pages y widgets de Filament.
- **Documentación:** <https://github.com/bezhanSalleh/filament-shield>

### filament-auto-logout ✅

- **Paquete:** `niladam/filament-auto-logout`
- **Descripción:** Cierra automáticamente la sesión de usuarios inactivos. Funciona con múltiples pestañas del navegador. Muestra un contador de tiempo restante y notificación de advertencia antes del cierre de sesión.
- **Características:**
  - Tiempo de inactividad configurable (por defecto 15 minutos)
  - Advertencia antes del logout (por defecto 30 segundos)
  - Badge visual con tiempo restante
  - Soporte multi-tab (sincroniza entre pestañas)
  - Colores e iconos personalizables
  - Posibilidad de deshabilitar para usuarios específicos
- **Dependencias:** Ninguna adicional
- **Documentación:** <https://github.com/niladam/filament-auto-logout>

### filament-renew-password ✅

- **Paquete:** `yebor974/filament-renew-password`
- **Descripción:** Fuerza a los usuarios a renovar su contraseña según criterios configurables: expiración periódica o forzado por administrador.
- **Características:**
  - Renovación periódica (cada X días)
  - Forzar renovación en primer login (útil para contraseñas temporales)
  - Columnas personalizables (`last_renew_password_at`, `force_renew_password`)
  - Página de renovación personalizable
  - URI de ruta configurable para i18n
- **Dependencias:** Requiere migración para añadir columnas a tabla `users`
- **Documentación:** <https://github.com/yebor974/filament-renew-password>

---

## 🔍 Monitorización y Logs

### filament-log-viewer ✅

- **Paquete:** `achyutn/filament-log-viewer`
- **Descripción:** Visualiza y gestiona los logs de Laravel directamente desde el panel de Filament. Permite filtrar, buscar y monitorear errores y eventos de la aplicación.
- **Características:**
  - Interfaz intuitiva para visualizar logs de Laravel
  - Filtros por nivel de log (error, warning, info, etc.)
  - Búsqueda y paginación de entradas de log
  - Acceso directo a logs sin necesidad de SSH
- **Dependencias:** Ninguna adicional
- **Documentación:** <https://github.com/achyutkneupane/filament-log-viewer>

### filament-activity-log ✅

- **Paquete:** `pxlrbt/filament-activity-log`
- **Descripción:** Integración con Spatie Laravel Activitylog para mostrar un registro de actividades de modelos Eloquent en el panel de Filament.
- **Características:**
  - Página dedicada para visualizar actividades de modelos
  - Integración automática con el trait `LogsActivity` de Spatie
  - Filtrado por modelo, usuario y tipo de actividad
  - Vista detallada de cambios en los registros
- **Dependencias:** Requiere `spatie/laravel-activitylog` y tema personalizado
- **Configuración:** Necesita importar estilos CSS en tema personalizado: `@import '../../../../vendor/pxlrbt/filament-activity-log/resources/css/styles.css';`
- **Documentación:** <https://github.com/pxlrbt/filament-activity-log>

---

## ⚡ Acciones Masivas y Colas

### filament-queueable-bulk-actions ✅

- **Paquete:** `bytexr/filament-queueable-bulk-actions`
- **Descripción:** Ejecuta acciones masivas en colas para mejorar el rendimiento y la experiencia del usuario con notificaciones en tiempo real del progreso.
- **Características:**
  - Procesamiento de acciones masivas en segundo plano
  - Notificaciones en tiempo real del progreso de las operaciones
  - Interfaz para ver historial de acciones masivas ejecutadas
  - Configuración flexible de colas y conexiones
  - Estados personalizables (queued, in_progress, finished, failed)
- **Dependencias:** Requiere migración y configuración de colas
- **Configuración:** Necesita migraciones `php artisan vendor:publish --tag="queueable-bulk-actions-migrations"` y registro del plugin con opciones personalizables
- **Documentación:** <https://github.com/bytexr/filament-queueable-bulk-actions>

---

## 🌐 Internacionalización

### filament-language-switch ✅

- **Paquete:** `bezhansalleh/filament-language-switch`
- **Descripción:** Selector de idioma versátil para paneles Filament. Permite cambiar el locale de la aplicación con soporte para banderas, etiquetas personalizadas y múltiples ubicaciones.
- **Características:**
  - Configuración de locales soportados
  - Evento `LocaleChanged` para persistir preferencia del usuario
  - Visible dentro y fuera de paneles (login, registro, etc.)
  - Soporte para banderas (imágenes SVG)
  - Modo solo banderas o banderas + texto
  - Etiquetas localizadas automáticas o personalizadas
  - Exclusión de paneles específicos
  - Render hook configurable
  - Estilo circular opcional
- **Dependencias:** Ninguna adicional (requiere custom theme)
- **Documentación:** <https://github.com/bezhanSalleh/filament-language-switch>

---

## 📊 Datos y Excel

### filament-excel

- **Paquete:** `pxlrbt/filament-excel`
- **Descripción:** Exportación de datos a Excel/CSV desde tablas de Filament. Soporta exportaciones en bloque, personalización de columnas, formatos y exportaciones en cola.
- **Documentación:** <https://github.com/pxlrbt/filament-excel>

### filament-excel-import

- **Paquete:** `eightynine/filament-excel-import`
- **Descripción:** Importación de datos desde archivos Excel. Incluye validación de datos, archivo de ejemplo descargable, hooks before/after import, procesamiento personalizado y soporte para relaciones. Usa maatwebsite/laravel-excel internamente.
- **Uso:** `ExcelImportAction::make()` en header actions de ListRecords
- **Documentación:** <https://github.com/eighty9nine/filament-excel-import>

---

## 📈 Visualización y Gráficos

### filament-apex-charts

- **Paquete:** `leandrocfe/filament-apex-charts`
- **Descripción:** Integración de ApexCharts para crear widgets de gráficos interactivos (líneas, barras, áreas, pie, donut, radar, etc.).
- **Documentación:** <https://github.com/leandrocfe/filament-apex-charts>

### calendar

- **Paquete:** `guava/calendar`
- **Descripción:** Widget de calendario para Filament basado en vkurko/calendar. Soporta vistas día/semana/mes, eventos arrastrables y personalizables.
- **Documentación:** <https://github.com/GuavaCZ/calendar>

### flowforge (Kanban)

- **Paquete:** `relaticle/flowforge`
- **Descripción:** Tableros Kanban con drag-and-drop para gestión visual de tareas y flujos de trabajo.
- **Documentación:** <https://github.com/Relaticle/flowforge>

---

## 🎨 Interfaz de Usuario y Campos

### filament-badgeable-column

- **Paquete:** `awcodes/filament-badgeable-column`
- **Descripción:** Columna de tabla que muestra badges/etiquetas junto al contenido principal.
- **Documentación:** <https://github.com/awcodes/filament-badgeable-column>

### filament-quick-create

- **Paquete:** `awcodes/filament-quick-create`
- **Descripción:** Menú desplegable en la navegación para crear rápidamente nuevos registros de cualquier resource.
- **Documentación:** <https://github.com/awcodes/filament-quick-create>

### filament-modal-relation-managers

- **Paquete:** `guava/filament-modal-relation-managers`
- **Descripción:** Permite abrir relation managers en modales en lugar de dentro de la página del recurso.
- **Documentación:** <https://github.com/GuavaCZ/filament-modal-relation-managers>

### filament-layout-manager

- **Paquete:** `asosick/filament-layout-manager`
- **Descripción:** Permite a los usuarios personalizar y reorganizar el layout de widgets en el dashboard con drag-and-drop.
- **Documentación:** <https://github.com/asosick/filament-layout-manager>

---

## 💬 Social y Colaboración

### commentions

- **Paquete:** `kirschbaum-development/commentions`
- **Descripción:** Sistema de comentarios con @menciones y reacciones para modelos Eloquent, integrado con Filament.
- **Documentación:** <https://github.com/kirschbaum-development/commentions>

---

## 🔌 Integraciones y API

### filament-api-service

- **Paquete:** `rupadana/filament-api-service`
- **Descripción:** Genera automáticamente endpoints REST API para tus resources de Filament. Incluye documentación OpenAPI/Swagger.
- **Documentación:** <https://github.com/rupadana/filament-api-service>

### filament-webhook-server

- **Paquete:** `marjose123/filament-webhook-server`
- **Descripción:** Envía webhooks desde tu aplicación Filament. Proporciona una página para gestionar webhooks salientes con historial de logs, selección de modelos y eventos (created, updated, deleted). Soporta payloads personalizados implementando la interfaz `Webhookable`.
- **Configuración:** Registrado automáticamente en `AdminPanelProvider` con `WebhookPlugin::make()`
- **Documentación:** <https://github.com/MarJose123/filament-webhook-server>

### filament-webhook-client

- **Paquete:** `tapp/filament-webhook-client`
- **Descripción:** Panel para visualizar y gestionar webhooks entrantes. Requiere `spatie/laravel-webhook-client`.
- **Documentación:** <https://github.com/TappNetwork/filament-webhook-client>

---

## ⚙️ Configuración y Utilidades

### spatie-laravel-settings-plugin

- **Paquete:** `filament/spatie-laravel-settings-plugin`
- **Descripción:** Plugin oficial de Filament para crear páginas de configuración usando Spatie Laravel Settings.
- **Documentación:** <https://filamentphp.com/plugins/filament-spatie-settings>

### spatie-laravel-tags-plugin

- **Paquete:** `filament/spatie-laravel-tags-plugin`
- **Descripción:** Plugin oficial de Filament para campos de etiquetas usando Spatie Laravel Tags. Incluye TagsInput y TagsColumn.
- **Documentación:** <https://filamentphp.com/plugins/filament-spatie-tags>

---

## 🎯 Iconos

### filament-icons

- **Paquete:** `guava/filament-icons`
- **Descripción:** Herramienta para instalar y generar clases Enum de cualquier pack de iconos Blade.
- **Documentación:** <https://github.com/GuavaCZ/filament-icons>

### blade-fluentui-system-icons

- **Paquete:** `codeat3/blade-fluentui-system-icons`
- **Descripción:** Pack de iconos FluentUI de Microsoft (4000+ iconos) para usar con Blade Icons.
- **Uso:** `<x-fluentui-home-24-o />` o `@svg('fluentui-home-24-o')`
- **Documentación:** <https://github.com/AstroNie/blade-fluentui-system-icons>

---

## 📦 Dependencias Base

Estos paquetes fueron instalados automáticamente como dependencias de los plugins:

| Paquete                         | Uso                                                     |
| ------------------------------- | ------------------------------------------------------- |
| `spatie/laravel-permission`     | Sistema de roles/permisos (usado por Shield)            |
| `spatie/laravel-settings`       | Sistema de settings (usado por Settings Plugin)         |
| `spatie/laravel-tags`           | Sistema de etiquetas (usado por Tags Plugin)            |
| `spatie/laravel-webhook-client` | Cliente de webhooks (usado por Webhook Client)          |
| `spatie/laravel-webhook-server` | Servidor de webhooks (usado por Webhook Server)         |
| `spatie/laravel-activitylog`    | Sistema de activity log (usado por Activity Log Plugin) |
| `laravel/sanctum`               | Autenticación API (usado por Breezy y API Service)      |
| `maatwebsite/excel`             | Importación/exportación Excel (usado por Excel plugins) |
| `livewire/livewire`             | Base de Filament                                        |

---

## 🚀 Instalación de Shield

Para configurar completamente filament-shield, ejecuta:

```bash
php artisan shield:install
```

Esto creará las migraciones de permisos y configurará el super admin.

---

## 📝 Notas

- **Versión de Filament:** v4.x
- **PHP Mínimo:** 8.2
- Todos los plugins están configurados para funcionar con el panel `admin` por defecto.
- Para usar FluentUI icons, el prefijo es `fluentui-` seguido del nombre del icono.

---

_Documentación generada automáticamente como parte del boilerplate._
