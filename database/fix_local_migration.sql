-- ============================================================
-- SIGEDOC - Migración Correctiva LOCAL
-- Agrega tablas y columnas faltantes en la BD local
-- ============================================================

-- 1. Tabla departamentos
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'departamentos')
BEGIN
    CREATE TABLE departamentos (
        id INT PRIMARY KEY IDENTITY(1,1),
        nombre NVARCHAR(150) NOT NULL,
        codigo NVARCHAR(50) NULL,
        descripcion NVARCHAR(255) NULL,
        estado TINYINT DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE()
    );
    PRINT 'Tabla departamentos creada.';
END
ELSE
    PRINT 'Tabla departamentos ya existe.';
GO

-- 2. Tabla tipos_solicitudes
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'tipos_solicitudes')
BEGIN
    CREATE TABLE tipos_solicitudes (
        id INT PRIMARY KEY IDENTITY(1,1),
        nombre NVARCHAR(150) NOT NULL,
        descripcion NVARCHAR(255) NULL,
        estado TINYINT DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE()
    );
    PRINT 'Tabla tipos_solicitudes creada.';
END
ELSE
    PRINT 'Tabla tipos_solicitudes ya existe.';
GO

-- 3. Columnas faltantes en documentos
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='documentos' AND COLUMN_NAME='tipo_solicitud_id')
BEGIN
    ALTER TABLE documentos ADD tipo_solicitud_id INT NULL;
    PRINT 'Columna tipo_solicitud_id agregada a documentos.';
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='documentos' AND COLUMN_NAME='departamento_destino_id')
BEGIN
    ALTER TABLE documentos ADD departamento_destino_id INT NULL;
    PRINT 'Columna departamento_destino_id agregada a documentos.';
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='documentos' AND COLUMN_NAME='departamento_origen_id')
BEGIN
    ALTER TABLE documentos ADD departamento_origen_id INT NULL;
    PRINT 'Columna departamento_origen_id agregada a documentos.';
END
GO

-- 4. Columna departamento faltante en notificaciones
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='notificaciones' AND COLUMN_NAME='departamento')
BEGIN
    ALTER TABLE notificaciones ADD departamento NVARCHAR(100) NULL;
    PRINT 'Columna departamento agregada a notificaciones.';
END
GO

-- 5. Columna descripcion faltante en configuracion
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='configuracion' AND COLUMN_NAME='descripcion')
BEGIN
    ALTER TABLE configuracion ADD descripcion VARCHAR(255) NULL;
    PRINT 'Columna descripcion agregada a configuracion.';
END
GO

-- 6. Columna estado faltante en usuarios (error log junio 9)
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='usuarios' AND COLUMN_NAME='estado')
BEGIN
    ALTER TABLE usuarios ADD estado NVARCHAR(50) DEFAULT 'ACTIVO';
    PRINT 'Columna estado agregada a usuarios.';
END
GO

-- 7. Foreign Keys para documentos (después de crear las tablas)
IF NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_TYPE='FOREIGN KEY' AND TABLE_NAME='documentos' AND CONSTRAINT_NAME='FK_documentos_tipo_solicitud'
)
BEGIN
    ALTER TABLE documentos 
    ADD CONSTRAINT FK_documentos_tipo_solicitud 
    FOREIGN KEY (tipo_solicitud_id) REFERENCES tipos_solicitudes(id);
    PRINT 'FK tipo_solicitud_id creado.';
END
GO

IF NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_TYPE='FOREIGN KEY' AND TABLE_NAME='documentos' AND CONSTRAINT_NAME='FK_documentos_depto_destino'
)
BEGIN
    ALTER TABLE documentos 
    ADD CONSTRAINT FK_documentos_depto_destino 
    FOREIGN KEY (departamento_destino_id) REFERENCES departamentos(id);
    PRINT 'FK departamento_destino_id creado.';
END
GO

IF NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_TYPE='FOREIGN KEY' AND TABLE_NAME='documentos' AND CONSTRAINT_NAME='FK_documentos_depto_origen'
)
BEGIN
    ALTER TABLE documentos 
    ADD CONSTRAINT FK_documentos_depto_origen 
    FOREIGN KEY (departamento_origen_id) REFERENCES departamentos(id);
    PRINT 'FK departamento_origen_id creado.';
END
GO

-- 8. Datos iniciales de configuracion si faltan
IF NOT EXISTS (SELECT 1 FROM configuracion WHERE clave = 'app_name')
BEGIN
    INSERT INTO configuracion (clave, valor, descripcion) VALUES 
    ('app_name', 'SIGEDOC', 'Nombre del sistema'),
    ('max_file_size', '100', 'Tamaño máximo de archivo en MB'),
    ('path_originales', 'storage/documentos/originales/', 'Ruta para archivos originales'),
    ('path_firmados', 'storage/documentos/firmados/', 'Ruta para archivos firmados');
    PRINT 'Datos iniciales de configuracion insertados.';
END
GO

-- 9. Registro en tabla de migraciones
IF NOT EXISTS (SELECT 1 FROM migrations WHERE migration = 'fix_local_missing_tables_v1')
BEGIN
    INSERT INTO migrations (migration) VALUES ('fix_local_missing_tables_v1');
END
GO

PRINT '=== Migración correctiva LOCAL completada ===';
GO
