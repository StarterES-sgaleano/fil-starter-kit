# Filament Plugins Documentation

Este documento lista todos los plugins de Filament instalados en este proyecto boilerplate, organizados por categoría.

## 🔐 Seguridad y Autenticación

### filament-breezy
- **Paquete:** `jeffgreco13/filament-breezy`
- **Descripción:** Añade autenticación de dos factores (2FA), página de perfil personalizable, gestión de sesiones de navegador, y soporte para tokens de Laravel Sanctum.
- **Documentación:** https://github.com/jeffgreco13/filament-breezy

### filament-shield
- **Paquete:** `bezhansalleh/filament-shield`
- **Descripción:** Gestión completa de roles y permisos usando Spatie Laravel Permission. Genera permisos automáticamente para resources, pages y widgets de Filament.
- **Documentación:** https://github.com/bezhanSalleh/filament-shield

---

## 📊 Datos y Excel

### filament-excel
- **Paquete:** `pxlrbt/filament-excel`
- **Descripción:** Exportación de datos a Excel/CSV desde tablas de Filament. Soporta exportaciones en bloque, personalización de columnas, formatos y exportaciones en cola.
- **Documentación:** https://github.com/pxlrbt/filament-excel

### filament-excel-import
- **Paquete:** `eightynine/filament-excel-import`
- **Descripción:** Importación de datos desde archivos Excel. Incluye validación de datos, archivo de ejemplo descargable, hooks before/after import, procesamiento personalizado y soporte para relaciones. Usa maatwebsite/laravel-excel internamente.
- **Uso:** `ExcelImportAction::make()` en header actions de ListRecords
- **Documentación:** https://github.com/eighty9nine/filament-excel-import

---

## 📈 Visualización y Gráficos

### filament-apex-charts
- **Paquete:** `leandrocfe/filament-apex-charts`
- **Descripción:** Integración de ApexCharts para crear widgets de gráficos interactivos (líneas, barras, áreas, pie, donut, radar, etc.).
- **Documentación:** https://github.com/leandrocfe/filament-apex-charts

### calendar
- **Paquete:** `guava/calendar`
- **Descripción:** Widget de calendario para Filament basado en vkurko/calendar. Soporta vistas día/semana/mes, eventos arrastrables y personalizables.
- **Documentación:** https://github.com/GuavaCZ/calendar

### flowforge (Kanban)
- **Paquete:** `relaticle/flowforge`
- **Descripción:** Tableros Kanban con drag-and-drop para gestión visual de tareas y flujos de trabajo.
- **Documentación:** https://github.com/Relaticle/flowforge

---

## 🎨 Interfaz de Usuario y Campos

### filament-badgeable-column
- **Paquete:** `awcodes/filament-badgeable-column`
- **Descripción:** Columna de tabla que muestra badges/etiquetas junto al contenido principal.
- **Documentación:** https://github.com/awcodes/filament-badgeable-column

### filament-quick-create
- **Paquete:** `awcodes/filament-quick-create`
- **Descripción:** Menú desplegable en la navegación para crear rápidamente nuevos registros de cualquier resource.
- **Documentación:** https://github.com/awcodes/filament-quick-create

### filament-modal-relation-managers
- **Paquete:** `guava/filament-modal-relation-managers`
- **Descripción:** Permite abrir relation managers en modales en lugar de dentro de la página del recurso.
- **Documentación:** https://github.com/GuavaCZ/filament-modal-relation-managers

### filament-layout-manager
- **Paquete:** `asosick/filament-layout-manager`
- **Descripción:** Permite a los usuarios personalizar y reorganizar el layout de widgets en el dashboard con drag-and-drop.
- **Documentación:** https://github.com/asosick/filament-layout-manager

---

## 💬 Social y Colaboración

### commentions
- **Paquete:** `kirschbaum-development/commentions`
- **Descripción:** Sistema de comentarios con @menciones y reacciones para modelos Eloquent, integrado con Filament.
- **Documentación:** https://github.com/kirschbaum-development/commentions

---

## 🔌 Integraciones y API

### filament-api-service
- **Paquete:** `rupadana/filament-api-service`
- **Descripción:** Genera automáticamente endpoints REST API para tus resources de Filament. Incluye documentación OpenAPI/Swagger.
- **Documentación:** https://github.com/rupadana/filament-api-service

### filament-webhook-server
- **Paquete:** `marjose123/filament-webhook-server`
- **Descripción:** Envía webhooks desde tu aplicación Filament. Proporciona una página para gestionar webhooks salientes con historial de logs, selección de modelos y eventos (created, updated, deleted). Soporta payloads personalizados implementando la interfaz `Webhookable`.
- **Configuración:** Registrado automáticamente en `AdminPanelProvider` con `WebhookPlugin::make()`
- **Documentación:** https://github.com/MarJose123/filament-webhook-server

### filament-webhook-client
- **Paquete:** `tapp/filament-webhook-client`
- **Descripción:** Panel para visualizar y gestionar webhooks entrantes. Requiere `spatie/laravel-webhook-client`.
- **Documentación:** https://github.com/TappNetwork/filament-webhook-client

---

## ⚙️ Configuración y Utilidades

### spatie-laravel-settings-plugin
- **Paquete:** `filament/spatie-laravel-settings-plugin`
- **Descripción:** Plugin oficial de Filament para crear páginas de configuración usando Spatie Laravel Settings.
- **Documentación:** https://filamentphp.com/plugins/filament-spatie-settings

### spatie-laravel-tags-plugin
- **Paquete:** `filament/spatie-laravel-tags-plugin`
- **Descripción:** Plugin oficial de Filament para campos de etiquetas usando Spatie Laravel Tags. Incluye TagsInput y TagsColumn.
- **Documentación:** https://filamentphp.com/plugins/filament-spatie-tags

---

## 🎯 Iconos

### filament-icons
- **Paquete:** `guava/filament-icons`
- **Descripción:** Herramienta para instalar y generar clases Enum de cualquier pack de iconos Blade.
- **Documentación:** https://github.com/GuavaCZ/filament-icons

### blade-fluentui-system-icons
- **Paquete:** `codeat3/blade-fluentui-system-icons`
- **Descripción:** Pack de iconos FluentUI de Microsoft (4000+ iconos) para usar con Blade Icons.
- **Uso:** `<x-fluentui-home-24-o />` o `@svg('fluentui-home-24-o')`
- **Documentación:** https://github.com/AstroNie/blade-fluentui-system-icons

---

## 📦 Dependencias Base

Estos paquetes fueron instalados automáticamente como dependencias de los plugins:

| Paquete | Uso |
|---------|-----|
| `spatie/laravel-permission` | Sistema de roles/permisos (usado por Shield) |
| `spatie/laravel-settings` | Sistema de settings (usado por Settings Plugin) |
| `spatie/laravel-tags` | Sistema de etiquetas (usado por Tags Plugin) |
| `spatie/laravel-webhook-client` | Cliente de webhooks (usado por Webhook Client) |
| `spatie/laravel-webhook-server` | Servidor de webhooks (usado por Webhook Server) |
| `laravel/sanctum` | Autenticación API (usado por Breezy y API Service) |
| `maatwebsite/excel` | Importación/exportación Excel (usado por Excel plugins) |
| `livewire/livewire` | Base de Filament |

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

*Documentación generada automáticamente como parte del boilerplate.*
