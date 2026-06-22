-- Estructura de Base de Datos para SIGEDOC
-- SQL Server
-- PROMESE/CAL

-- Crear base de datos si no existe
IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = 'sigedoc')
BEGIN
    CREATE DATABASE sigedoc;
END
GO

USE sigedoc;
GO

-- Tabla de roles
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'roles')
BEGIN
    CREATE TABLE roles (
        id INT PRIMARY KEY IDENTITY(1,1),
        nombre VARCHAR(50) NOT NULL
    );
END
GO

-- Tabla de usuarios
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'usuarios')
BEGIN
    CREATE TABLE usuarios (
        id INT PRIMARY KEY IDENTITY(1,1),
        usuario VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        rol_id INT,
        status TINYINT DEFAULT 1,
        FOREIGN KEY (rol_id) REFERENCES roles(id)
    );
END
GO

-- Tabla de documentos
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'documentos')
BEGIN
    CREATE TABLE documentos (
        id VARCHAR(50) PRIMARY KEY,
        descripcion TEXT,
        tipo VARCHAR(100),
        ruta_original VARCHAR(255),
        ruta_firmado VARCHAR(255),
        estado VARCHAR(20) DEFAULT 'SOLICITADO' CHECK (estado IN ('SOLICITADO', 'APROBADO_COMPRAS', 'AUTORIZADO', 'RECHAZADO', 'DIGITALIZADO')),
        prioridad VARCHAR(20) DEFAULT 'Normal' CHECK (prioridad IN ('Baja', 'Normal', 'Alta', 'Crítica')),
        usuario_id INT,
        fecha_creacion DATETIME DEFAULT GETDATE(),
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    );
END
GO

-- Tabla de bitácora
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'bitacora')
BEGIN
    CREATE TABLE bitacora (
        id INT PRIMARY KEY IDENTITY(1,1),
        usuario_id INT,
        accion VARCHAR(255),
        detalles TEXT,
        ip VARCHAR(45),
        fecha DATETIME DEFAULT GETDATE(),
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    );
END
GO

PRINT 'Estructura de base de datos SIGEDOC creada exitosamente';
GO
