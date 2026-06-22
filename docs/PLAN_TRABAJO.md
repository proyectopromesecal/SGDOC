# 🔍 Auditoría & Plan de Trabajo — SIGEDOC
**Fecha:** 2026-04-22 | **Versión Auditada:** 2.2.6

---

## Resumen Ejecutivo

El proyecto SIGEDOC es una aplicación PHP MVC funcional con un flujo de aprobación documental bien estructurado. Sin embargo, la auditoría reveló **2 vulnerabilidades críticas de seguridad** que deben corregirse de inmediato antes de cualquier despliegue en producción, además de bugs de código y deuda técnica significativa.

---

## 🚨 HALLAZGOS CRÍTICOS (Prioridad Inmediata)

### CRIT-01 — Credenciales en texto plano en `.env` + no está en `.gitignore`

| Campo | Detalle |
|---|---|
| **Archivo** | `.env` (líneas 16, 31) |
| **Riesgo** | Exposición total de credenciales en producción y en el repositorio Git |
| **Evidencia** | `SMTP_PASS=Farmacia1234` en texto plano. Credenciales BD producción comentadas pero visibles en el historial. `.gitignore` excluye `config.php` pero **NO excluye `.env`**. |
| **Impacto** | Cualquier `git push` publica usuario, contraseña SMTP y la URL del servidor de producción. |

### CRIT-02 — Inyección LDAP (LDAP Injection)

| Campo | Detalle |
|---|---|
| **Archivos** | `app/Services/LdapService.php:72`, `app/Controllers/UsuarioController.php:199` |
| **Riesgo** | Un atacante puede manipular las consultas al Directorio Activo |
| **Evidencia** | `$filter = "(samaccountname=$usuario)";` — input sin sanitizar. `$filter = "(|(samaccountname=*$termino*)...)";` — mismo problema en búsqueda. |
| **Impacto** | Bypass de autenticación, extracción de datos del AD, escalada de privilegios. |

---

## ⚠️ HALLAZGOS ALTOS (Corregir antes de producción)

### HIGH-01 — Bug: Variables no declaradas en `actualizar()` → Error 500 silencioso

| Campo | Detalle |
|---|---|
| **Archivo** | `app/Controllers/DocumentoController.php` líneas 622-625 |
| **Evidencia** | `$archivo` y `$config` son usadas sin declararse. Causa Error 500 cuando el solicitante intenta corregir un documento rechazado. |

### HIGH-02 — Módulo de Archivo Digital desactivado (Rutas comentadas)

| Campo | Detalle |
|---|---|
| **Archivo** | `public/index.php` líneas 56-58 |
| **Evidencia** | Las rutas `/documentos/digitalizados`, `/archivo-digital` y `/documentos/guardar_digitalizado` están comentadas. El controlador existe pero es inaccesible. |

### HIGH-03 — Cambio de estado de usuario con petición GET (sin protección CSRF)

| Campo | Detalle |
|---|---|
| **Archivo** | `public/index.php:86`, `app/Controllers/UsuarioController.php:146` |
| **Evidencia** | `GET /usuarios/estado/{id}` muta datos. Los GET no pasan la validación CSRF global (solo POST). Un enlace externo puede desactivar usuarios. |

### HIGH-04 — Schema SQL principal desactualizado y fragmentado

| Campo | Detalle |
|---|---|
| **Archivo** | `database/schema.sql` |
| **Evidencia** | El `schema.sql` es MySQL y le faltan ~10 columnas usadas en código (`nombre`, `email`, `departamento`, `firma_digital`, `es_digitalizado`, `comentario_rechazo`, etc.). El schema real está repartido en 5 archivos de migración sin un maestro unificado. |

---

## 🔶 HALLAZGOS MEDIOS (Deuda Técnica)

### MED-01 — Sin limitación de intentos en Login (riesgo de Fuerza Bruta)
- `AuthController.php` no implementa rate-limiting ni bloqueo por intentos fallidos en cuentas locales.

### MED-02 — Estado anterior incorrecto en seguimiento de Compras
- `DocumentoController.php:414`: se registra `'SOLICITADO'` como estado anterior al aprobar Compras, cuando el estado real es `'AUTORIZADO_DEPARTAMENTO'`. La trazabilidad muestra datos erróneos.

### MED-03 — Dos modelos de usuario duplicados (`User.php` vs `Usuario.php`)
- `app/Models/User.php` (947 bytes) parece ser un vestigio sin uso junto al completo `Usuario.php` (4857 bytes).

### MED-04 — TailwindCSS cargado desde CDN (no apto para producción)
- Todas las vistas usan el CDN de Tailwind Play que está deprecado para producción y depende de conectividad externa.

### MED-05 — Sin archivo `.env.example` para onboarding
- No existe guía de variables de entorno requeridas. Un nuevo desarrollador no sabe qué debe configurar.

### MED-06 — `rol_id` por defecto hardcodeado a `9` en aprobación de usuarios
- `UsuarioController.php:57`: `$rol_id = $_POST['rol_id'] ?? 9;` — El ID 9 puede no existir en la BD, causando error de FK.

---

## 🔵 HALLAZGOS BAJOS (Mejoras)

| ID | Descripción |
|---|---|
| LOW-01 | Sin Composer — PHPMailer incluido manualmente, sin control de versiones de dependencias |
| LOW-02 | `views/dashboard.php` y `views/login.php` sueltos en la raíz de vistas (hay subdirectorios para ellos) |
| LOW-03 | Sin paginación en `Documento::obtenerTodos()` — retorna todos los registros sin LIMIT |
| LOW-04 | `scratch/` no está en `.gitignore` — scripts de prueba quedarán en el repo |
| LOW-05 | Sin páginas de error 404/403 personalizadas en el router |

---

## ✅ ASPECTOS POSITIVOS

| | Hallazgo |
|---|---|
| ✅ | CSRF implementado globalmente para POST en `index.php` |
| ✅ | Todas las consultas SQL usan PDO Prepared Statements (sin SQL Injection) |
| ✅ | `htmlspecialchars()` aplicado consistentemente en todas las vistas (sin XSS) |
| ✅ | Sesiones endurecidas (`httponly`, `samesite`, `secure`) en `config.php` |
| ✅ | `session_regenerate_id(true)` en el login |
| ✅ | Contraseñas con `password_hash()` / `password_verify()` |
| ✅ | Separación clara de responsabilidades (MVC bien estructurado) |
| ✅ | Bitácora de auditoría en cada acción crítica del sistema |
| ✅ | Flujo de aprobación departamental correctamente implementado en backend y frontend |

---

## 📋 PLAN DE TRABAJO

### 🏁 SPRINT 1 — Seguridad Crítica (Semana 1)
> ⚠️ Requisito obligatorio antes de cualquier despliegue en producción

| # | Tarea | Archivo(s) | Esfuerzo |
|---|---|---|---|
| S1-T1 | Agregar `.env` al `.gitignore` + ejecutar `git rm --cached .env` | `.gitignore` | 30 min |
| S1-T2 | Crear `.env.example` con variables sin valores reales | `.env.example` (nuevo) | 1 h |
| S1-T3 | Sanitizar input LDAP con `ldap_escape()` en búsqueda y autenticación | `LdapService.php:72`, `UsuarioController.php:199` | 2 h |
| S1-T4 | Convertir ruta cambio-estado a POST y agregar CSRF token | `index.php:86`, `UsuarioController.php:146` | 1 h |
- [x] **[S1-T1]** Agregar `.env` al `.gitignore` y limpiar historial Git
- [x] **[S1-T2]** Crear `.env.example` sin valores reales
- [x] **[S1-T3]** Sanitizar inputs LDAP con `ldap_escape()` (LDAP Injection)
- [x] **[S1-T4]** Mover cambio de estado de usuario de GET → POST + CSRF

### 🔧 Sprint 2 — Corrección de Bugs (Semana 1-2)
- [x] **[S2-T1]** Fix variables no declaradas en `DocumentoController::actualizar()`
- [x] **[S2-T2]** Reactivar rutas del módulo Archivo Digital (comentadas en `index.php`)
- [x] **[S2-T3]** Corregir estado anterior en seguimiento de aprobación Compras
- [x] **[S2-T4]** Fix `rol_id` hardcodeado en aprobación de usuarios

### 🗄️ Sprint 3 — Base de Datos (Semana 2)
- [x] **[S3-T1]** Crear `schema_master_sqlserver.sql` unificado con todas las migraciones
- [x] **[S3-T2]** Mover `schema.sql` (MySQL legacy) a `docs/` como referencia historica
- [x] **[S3-T3]** Diagrama ER de la base de datos actual (Documentación)
- [x] **[S3-T4]** Implementar tabla `migrations` para control de versiones del schema

---

### 🏋️ SPRINT 4 — Calidad de Código (Semana 2-3)

| # | Tarea | Archivo(s) | Esfuerzo |
|---|---|---|---|
- [x] **[S4-T1]** Eliminar `User.php` (modelo duplicado)
- [x] **[S4-T2]** Agregar `scratch/` y `maintenance/` al `.gitignore`
- [x] **[S4-T3]** Implementar paginación en `Documento::obtenerTodos()`
- [x] **[S4-T4]** Agregar rate-limiting básico en el endpoint de login
- [x] **[S4-T5]** Mover `views/dashboard.php` y `views/login.php` a subdirectorios correctos

---

### 🚀 SPRINT 5 — Infraestructura (Semana 3-4)

| # | Tarea | Archivo(s) | Esfuerzo |
|---|---|---|---|
- [x] **[S5-T1]** Configurar base para Tailwind CLI (package.json, config)
- [x] **[S5-T2]** Introducir `composer.json` con PSR-4 autoloader
- [x] **[S5-T3]** Agregar páginas de error 404/403 personalizadas
- [x] **[S5-T4]** Configurar pipeline CI básico (GitHub Actions)

---

## 📊 Resumen

| Severidad | Cantidad | Acción |
|---|---|---|
| 🚨 Crítico | 2 | Corregir HOY |
| ⚠️ Alto | 4 | Esta semana |
| 🔶 Medio | 6 | Próximas 2 semanas |
| 🔵 Bajo | 5 | Backlog |

**Esfuerzo total estimado:** ~35 horas (~2 semanas con dedicación parcial)

---

*Generado por auditoría de código — SIGEDOC v2.2.6 — 2026-04-22*
