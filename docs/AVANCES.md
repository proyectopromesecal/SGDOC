# Avances del Proyecto SIGEDOC

## [2026-03-13] Integración LDAP y Flujo de Acceso
- **Autenticación LDAP**: Conexión exitosa con el dominio `promese.promesecal.gob.do`.
- **Shadow Users**: Implementación de creación automática de usuarios al primer login.
- **Campos Extendidos**: Migración de la tabla `usuarios` para incluir Nombre, Email, Departamento y Cargo desde el AD.
- **Rol Restringido**: Creación del rol "Pendiente de Acceso" para nuevos usuarios.
- **Portal de Solicitud**: Diseño e implementación de una interfaz premium para que nuevos usuarios soliciten acceso a Mesa de Ayuda.
- **Seguridad**: Implementación de middleware que bloquea el acceso al sistema hasta que el usuario sea aprobado por un administrador.

## [2026-03-13] Auditoría de Producción y Seguridad (Hardening)
- **Variables de Entorno**: Implementación de `.env` para proteger credenciales.
- **Protección CSRF**: Blindaje global de todos los formularios del sistema.
- **Sesiones Seguras**: Regeneración de ID de sesión y cookies protegidas (HttpOnly, SameSite).
- **Control de Errores**: Logging profesional en servidor y ocultamiento de errores en producción.
- **Envío de Solicitudes**: Implementación de flujo `mailto` con respaldo de bitácora.

## [2026-03-17] Seguimiento y Trazabilidad de Documentos
- **Historial Cronológico**: Implementación de la tabla `seguimiento_documentos` para registrar cada movimiento.
- **Línea de Vida (Timeline)**: Nueva interfaz visual premium en los detalles del expediente que muestra de dónde salió el documento, quién lo procesó y dónde se encuentra.
- **Registro Automático**: Integración en el controlador para capturar hitos en la creación, aprobación, autorización, rechazo y corrección.
- **Control de Cierre**: Adición de campo de fecha de finalización para expedientes autorizados.

## Estado del Proyecto
- [x] Panel de administración para aprobar solicitudes de acceso.
- [x] Módulo de gestión de roles y permisos avanzado con selección múltiple.
- [x] Auditoría de Seguridad para Producción completada.
- [x] Módulo de Trazabilidad y Seguimiento (Local).
- [ ] Integración de PHPMailer para envíos automáticos (Fase 2).
