-- Script de Inicialización de SIGEDOC
-- Ejecutar este script después de crear la base de datos

USE sigedoc;

-- Limpiar datos existentes (solo para desarrollo)
DELETE FROM bitacora;
DELETE FROM documentos;
DELETE FROM usuarios;
DELETE FROM roles;

-- Resetear auto_increment
ALTER TABLE roles AUTO_INCREMENT = 1;
ALTER TABLE usuarios AUTO_INCREMENT = 1;
ALTER TABLE bitacora AUTO_INCREMENT = 1;

-- Insertar roles
INSERT INTO roles (nombre) VALUES 
('Solicitante'),
('Compras'),
('Gerencia'),
('Administrador');

-- Insertar usuarios de prueba
-- Contraseña para todos: password123
INSERT INTO usuarios (usuario, password, rol_id, status) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, 1),
('solicitante1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1),
('compras1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 1),
('gerencia1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 1);

-- Insertar documentos de ejemplo
INSERT INTO documentos (id, descripcion, tipo, ruta_original, estado, usuario_id) VALUES
('DOC-2026-001', 'Solicitud de compra de equipos médicos', 'Solicitud de Compra', 'DOC-2026-001_ejemplo.pdf', 'SOLICITADO', 2),
('DOC-2026-002', 'Orden de compra de medicamentos', 'Orden de Compra', 'DOC-2026-002_ejemplo.pdf', 'APROBADO_COMPRAS', 2),
('DOC-2026-003', 'Contrato de servicios de limpieza', 'Contrato', 'DOC-2026-003_ejemplo.pdf', 'AUTORIZADO', 2),
('DOC-2026-004', 'Factura de proveedor XYZ', 'Factura', 'DOC-2026-004_ejemplo.pdf', 'RECHAZADO', 2);

-- Insertar registros de bitácora de ejemplo
INSERT INTO bitacora (usuario_id, accion, detalles, ip) VALUES
(1, 'LOGIN', 'Inicio de sesión exitoso', '127.0.0.1'),
(2, 'CREAR_DOCUMENTO', 'Documento creado: DOC-2026-001', '127.0.0.1'),
(3, 'APROBAR_DOCUMENTO', 'Documento aprobado por Compras: DOC-2026-002', '127.0.0.1'),
(4, 'AUTORIZAR_DOCUMENTO', 'Documento autorizado por Gerencia: DOC-2026-003', '127.0.0.1'),
(3, 'RECHAZAR_DOCUMENTO', 'Documento rechazado: DOC-2026-004', '127.0.0.1');

-- Mostrar resumen
SELECT 'Roles creados:' as Resumen, COUNT(*) as Total FROM roles
UNION ALL
SELECT 'Usuarios creados:', COUNT(*) FROM usuarios
UNION ALL
SELECT 'Documentos de ejemplo:', COUNT(*) FROM documentos
UNION ALL
SELECT 'Registros de bitácora:', COUNT(*) FROM bitacora;

-- Mostrar usuarios y sus credenciales
SELECT 
    u.usuario,
    r.nombre as rol,
    'password123' as contraseña,
    CASE WHEN u.status = 1 THEN 'Activo' ELSE 'Inactivo' END as estado
FROM usuarios u
INNER JOIN roles r ON u.rol_id = r.id
ORDER BY u.id;
