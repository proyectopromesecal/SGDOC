# 🚀 Plan de Modernización de Arquitectura — SIGEDOC

**Versión:** 1.0 | **Estado:** Completado | **Objetivo:** Profesionalización del Core

---

## 🎯 Objetivo General
Transformar la arquitectura actual del sistema hacia un modelo de **Middlewares** y **Servicios**, eliminando la redundancia de código, centralizando la seguridad y facilitando el mantenimiento a largo plazo.

---

## 🛠️ FASES DEL PROYECTO

### 🟦 FASE 1: Evolución del Router (Core)
*   [x] **M1-T1: Soporte de Middleware**: Modificar `App\Core\Router` para que acepte un array opcional de middlewares en el método `add()`.
*   [x] **M1-T2: Clase Base Middleware**: Crear la clase abstracta `App\Core\Middleware` que defina el contrato para todas las capas de seguridad.

### 🟩 FASE 2: Implementación de Capas de Seguridad
*   [x] **M2-T1: AuthMiddleware**: Capa encargada de verificar que el usuario tenga una sesión activa.
*   [x] **M2-T2: PermissionMiddleware**: Capa que recibe un parámetro (ej: `documentos_listar`) y valida contra la sesión si el usuario tiene acceso.
*   [x] **M2-T3: RoleMiddleware**: Capa para restricciones basadas exclusivamente en roles (ej: solo `Administrador`).
*   [x] **M2-T4: CsrfMiddleware**: Mover la validación CSRF global de `index.php` a una capa reutilizable.

### 🟨 FASE 3: Refactorización de la Capa de Controladores
*   [x] **M3-T1: Declaración en Rutas**: Actualizar todas las definiciones en `public/index.php` para incluir sus requisitos de seguridad.
*   [x] **M3-T2: Limpieza de Código (Sanitización)**: Eliminar todas las llamadas a `AuthController::verificarPermiso()` y `verificarAutenticacion()` dentro de los controladores.
*   [~] **M3-T3: BaseController**: Crear un controlador base que maneje respuestas comunes (JSON, redirecciones, renderizado de vistas). *(Diferido para futura iteración)*

### 🟧 FASE 4: Logging y Resiliencia
*   [x] **M4-T1: Integración de Logs**: Configurar logging en archivos (`logs/app.log`) para errores de sistema 500 y excepciones de base de datos.
*   [x] **M4-T2: Global Error Handler**: Implementar un manejador global que capture errores fatales y muestre la página de error personalizada sin exponer datos sensibles.

---

## 📊 BENEFICIOS TÉCNICOS

| Área | Beneficio |
|---|---|
| **Seguridad** | Cero riesgo de olvidar proteger una ruta; la seguridad es declarativa y centralizada. |
| **Mantenibilidad** | El código de los controladores se reduce en un ~30%, enfocándose solo en la lógica de negocio. |
| **Auditoría** | Es posible ver toda la matriz de acceso de la aplicación leyendo un solo archivo (`index.php`). |
| **Sostenibilidad** | Facilita la adición de nuevas funciones como APIs (REST) en el futuro. |

---

## ⏱️ ESTIMACIÓN DE ESFUERZO
*   **Fase 1 & 2:** 6 horas
*   **Fase 3:** 8 horas
*   **Fase 4:** 4 horas
*   **Total Estimado:** 18 horas de desarrollo.

---
*Documento generado por Antigravity para SIGEDOC v2.2.6*
