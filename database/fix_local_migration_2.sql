-- ============================================================
-- SIGEDOC - Migración Correctiva LOCAL 2
-- Agrega columnas faltantes descubiertas durante la ejecución
-- ============================================================

USE sigedoc;
GO

-- Columna departamento_id en usuarios
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='usuarios' AND COLUMN_NAME='departamento_id')
BEGIN
    ALTER TABLE usuarios ADD departamento_id INT NULL;
    PRINT 'Columna departamento_id agregada a usuarios.';
    
    -- Agregar la Foreign Key
    ALTER TABLE usuarios 
    ADD CONSTRAINT FK_usuarios_departamento 
    FOREIGN KEY (departamento_id) REFERENCES departamentos(id);
    PRINT 'FK_usuarios_departamento creada.';
END
ELSE
    PRINT 'Columna departamento_id ya existe en usuarios.';
GO

PRINT '=== Migración correctiva LOCAL 2 completada ===';
GO
