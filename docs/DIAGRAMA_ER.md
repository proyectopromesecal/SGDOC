# 📊 Diagrama Entidad-Relación — SIGEDOC

Este diagrama representa la estructura de datos consolidada en `schema_master_sqlserver.sql`.

```mermaid
erDiagram
    ROLES ||--o{ USUARIOS : "asigna_a"
    ROLES ||--o{ ROL_PERMISOS : "contiene"
    PERMISOS ||--o{ ROL_PERMISOS : "pertenece_a"
    
    USUARIOS ||--o{ DOCUMENTOS : "crea"
    USUARIOS ||--o{ BITACORA : "genera_logs"
    USUARIOS ||--o{ NOTIFICACIONES : "recibe"
    USUARIOS ||--o{ NOTAS_PROYECTO : "escribe"
    USUARIOS ||--o{ SEGUIMIENTO_DOCUMENTOS : "registra_accion"
    
    DOCUMENTOS ||--o{ DOCUMENTOS_ADJUNTOS : "tiene"
    DOCUMENTOS ||--o{ SEGUIMIENTO_DOCUMENTOS : "posee_historial"

    ROLES {
        int id PK
        varchar nombre
    }

    PERMISOS {
        int id PK
        varchar nombre
        varchar descripcion
        varchar modulo
    }

    ROL_PERMISOS {
        int rol_id PK, FK
        int permiso_id PK, FK
    }

    USUARIOS {
        int id PK
        varchar usuario
        varchar password
        nvarchar nombre
        nvarchar email
        nvarchar departamento
        nvarchar cargo
        nvarchar tipo_auth
        int rol_id FK
        varchar firma_digital
        tinyint status
        datetime fecha_registro
    }

    DOCUMENTOS {
        varchar id PK
        text descripcion
        varchar tipo
        varchar ruta_original
        varchar ruta_firmado
        varchar estado
        varchar prioridad
        int usuario_id FK
        datetime fecha_creacion
        datetime fecha_finalizacion
    }

    DOCUMENTOS_ADJUNTOS {
        int id PK
        varchar documento_id FK
        varchar nombre_archivo
        datetime fecha_creacion
    }

    SEGUIMIENTO_DOCUMENTOS {
        int id PK
        varchar documento_id FK
        int usuario_id FK
        nvarchar estado_anterior
        nvarchar estado_nuevo
        nvarchar accion
        nvarchar detalles
        datetime fecha_movimiento
    }

    BITACORA {
        int id PK
        int usuario_id FK
        varchar accion
        text detalles
        varchar ip
        datetime fecha
    }

    NOTIFICACIONES {
        int id PK
        int usuario_id FK
        varchar rol_destinatario
        nvarchar departamento
        varchar titulo
        text mensaje
        varchar tipo
        varchar link
        bit leida
        datetime fecha_creacion
    }

    NOTAS_PROYECTO {
        int id PK
        nvarchar titulo
        nvarchar contenido
        int autor_id FK
        nvarchar color_tag
        datetime fecha_creacion
    }

    CONFIGURACION {
        varchar clave PK
        text valor
        varchar descripcion
    }

    LOGIN_ATTEMPTS {
        varchar ip_address PK
        varchar usuario PK
        int intentos
        datetime ultimo_intento
    }
```

## Notas del Esquema
1. **Identificadores**: Se utiliza `id` autoincremental en casi todas las tablas, excepto en `DOCUMENTOS` donde el ID es un código institucional (ej. `DOC-2026-001`).
2. **Seguridad**: La tabla `LOGIN_ATTEMPTS` es fundamental para el rate-limiting implementado en la v2.2.6.
3. **Flujo**: `SEGUIMIENTO_DOCUMENTOS` permite auditoría completa del ciclo de vida de un acta.
