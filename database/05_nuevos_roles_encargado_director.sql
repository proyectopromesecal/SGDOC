-- ============================================================
-- SIGEDOC - Migración 05: Nuevos Roles Encargado y Director
-- ============================================================
-- Este script agrega los roles 'Encargado de Area' y
-- 'Director de Area', asignándoles los mismos permisos
-- que tiene el rol 'Solicitante' (crear/ver/editar documentos).
-- ============================================================

USE sigedoc;
GO

PRINT '=== Iniciando migración de nuevos roles ===';
GO

-- ------------------------------------------------------
-- 1. Insertar rol: Encargado de Area
-- ------------------------------------------------------
IF NOT EXISTS (SELECT 1 FROM roles WHERE nombre = 'Encargado de Area')
BEGIN
    INSERT INTO roles (nombre) VALUES ('Encargado de Area');
    PRINT 'Rol [Encargado de Area] creado.';
END
ELSE
    PRINT 'Rol [Encargado de Area] ya existe. Se omite.';
GO

-- ------------------------------------------------------
-- 2. Insertar rol: Director de Area
-- ------------------------------------------------------
IF NOT EXISTS (SELECT 1 FROM roles WHERE nombre = 'Director de Area')
BEGIN
    INSERT INTO roles (nombre) VALUES ('Director de Area');
    PRINT 'Rol [Director de Area] creado.';
END
ELSE
    PRINT 'Rol [Director de Area] ya existe. Se omite.';
GO

-- ------------------------------------------------------
-- 3. Asignar permisos de Solicitante a los nuevos roles
--    Permisos: dashboard_ver, documentos_listar,
--              documentos_crear, documentos_editar, documentos_ver
-- ------------------------------------------------------
DECLARE @encargadoId INT = (SELECT id FROM roles WHERE nombre = 'Encargado de Area');
DECLARE @directorId  INT = (SELECT id FROM roles WHERE nombre = 'Director de Area');

-- Permisos de nivel Solicitante/Secretaria
DECLARE @permisosTarget TABLE (nombre VARCHAR(100));
INSERT INTO @permisosTarget VALUES
    ('dashboard_ver'),
    ('documentos_listar'),
    ('documentos_crear'),
    ('documentos_editar'),
    ('documentos_ver');

-- Asignar permisos a: Encargado de Area
INSERT INTO rol_permisos (rol_id, permiso_id)
SELECT @encargadoId, p.id
FROM permisos p
INNER JOIN @permisosTarget pt ON p.nombre = pt.nombre
WHERE NOT EXISTS (
    SELECT 1 FROM rol_permisos rp
    WHERE rp.rol_id = @encargadoId AND rp.permiso_id = p.id
);

PRINT 'Permisos asignados a [Encargado de Area].';

-- Asignar permisos a: Director de Area
INSERT INTO rol_permisos (rol_id, permiso_id)
SELECT @directorId, p.id
FROM permisos p
INNER JOIN @permisosTarget pt ON p.nombre = pt.nombre
WHERE NOT EXISTS (
    SELECT 1 FROM rol_permisos rp
    WHERE rp.rol_id = @directorId AND rp.permiso_id = p.id
);

PRINT 'Permisos asignados a [Director de Area].';
GO

-- ------------------------------------------------------
-- 4. Verificación final
-- ------------------------------------------------------
SELECT
    r.nombre AS rol,
    p.nombre AS permiso,
    p.modulo
FROM roles r
INNER JOIN rol_permisos rp ON r.id = rp.rol_id
INNER JOIN permisos p      ON p.id = rp.permiso_id
WHERE r.nombre IN ('Encargado de Area', 'Director de Area')
ORDER BY r.nombre, p.modulo, p.nombre;
GO

PRINT '=== Migración 05 completada correctamente ===';
GO
