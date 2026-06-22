-- Migración: Tabla de seguimiento y campos adicionales de cierre
-- SIGEDOC
USE sigedoc;
GO

IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'seguimiento_documentos')
BEGIN
    CREATE TABLE seguimiento_documentos (
        id INT IDENTITY(1,1) PRIMARY KEY,
        documento_id VARCHAR(50) NOT NULL,
        usuario_id INT NOT NULL,
        estado_anterior NVARCHAR(50),
        estado_nuevo NVARCHAR(50),
        accion NVARCHAR(255),
        detalles NVARCHAR(MAX),
        fecha_movimiento DATETIME DEFAULT GETDATE(),
        FOREIGN KEY (documento_id) REFERENCES documentos(id),
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    );
END
GO

IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('documentos') AND name = 'fecha_finalizacion')
BEGIN
    ALTER TABLE documentos ADD fecha_finalizacion DATETIME NULL;
END
GO

PRINT 'Tabla de seguimiento creada y tabla de documentos actualizada.';
GO
