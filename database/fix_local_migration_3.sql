-- ============================================================
-- SIGEDOC - Migración Correctiva LOCAL 3
-- Cambiar nombre de columna estado a activo en departamentos y tipos_solicitudes
-- ============================================================

USE sigedoc;
GO

IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='departamentos' AND COLUMN_NAME='estado')
BEGIN
    EXEC sp_rename 'departamentos.estado', 'activo', 'COLUMN';
    PRINT 'Columna estado renombrada a activo en departamentos.';
END
GO

IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='tipos_solicitudes' AND COLUMN_NAME='estado')
BEGIN
    EXEC sp_rename 'tipos_solicitudes.estado', 'activo', 'COLUMN';
    PRINT 'Columna estado renombrada a activo en tipos_solicitudes.';
END
GO

PRINT '=== Migración correctiva LOCAL 3 completada ===';
GO
