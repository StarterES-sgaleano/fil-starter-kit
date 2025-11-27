# API Contracts

Este directorio contiene las especificaciones de contratos API para los plugins de Filament.

## Estructura

```
contracts/
├── README.md           # Este archivo
├── shield-api.yaml     # Shield roles/permissions API
└── webhook-api.yaml    # Webhook client API
```

## Notas

Los contratos API se generarán automáticamente cuando:

1. **filament-api-service** sea configurado para generar APIs de Resources
2. Se creen los primeros Resources (Asset, License, etc.)

Por ahora, los plugins principales usan rutas internas de Filament:

| Plugin | Ruta Base | Tipo |
|--------|-----------|------|
| Shield | /admin/shield/* | Panel Routes |
| Breezy | /admin/my-profile | Panel Routes |
| Webhook | /api/webhooks/* | API Routes |
| API Service | /api/{resource}/* | REST API |

## Webhook API (Existente)

El plugin `filament-webhook-client` ya está configurado con rutas API habilitadas.

Endpoint base: `POST /api/webhooks/{name}`

Ver `webhook-api.yaml` para especificación completa.
