-- ============================================================
-- SIGEDOC - Master Database Schema (SQL Server)
-- Versión Unificada v2.2.6
-- ============================================================

-- USE sigedoc;
-- GO

-- 1. Tabla de Migraciones (Control de Versiones)
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'migrations')
BEGIN
    CREATE TABLE migrations (
        id INT PRIMARY KEY IDENTITY(1,1),
        migration VARCHAR(255) NOT NULL,
        executed_at DATETIME DEFAULT GETDATE()
    );
END
GO

-- 2. Tabla de Roles
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'roles')
BEGIN
    CREATE TABLE roles (
        id INT PRIMARY KEY IDENTITY(1,1),
        nombre VARCHAR(50) NOT NULL
    );
END
GO

-- 3. Tabla de Permisos
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'permisos')
BEGIN
    CREATE TABLE permisos (
        id INT PRIMARY KEY IDENTITY(1,1),
        nombre VARCHAR(100) UNIQUE NOT NULL,
        descripcion VARCHAR(255),
        modulo VARCHAR(50)
    );
END
GO

-- 4. Tabla de Relación Rol-Permisos
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'rol_permisos')
BEGIN
    CREATE TABLE rol_permisos (
        rol_id INT NOT NULL,
        permiso_id INT NOT NULL,
        PRIMARY KEY (rol_id, permiso_id),
        FOREIGN KEY (rol_id) REFERENCES roles(id),
        FOREIGN KEY (permiso_id) REFERENCES permisos(id)
    );
END
GO

-- 5. Tabla de Usuarios
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'usuarios')
BEGIN
    CREATE TABLE usuarios (
        id INT PRIMARY KEY IDENTITY(1,1),
        usuario VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        nombre NVARCHAR(200) NULL,
        email NVARCHAR(255) NULL,
        departamento NVARCHAR(100) NULL,
        cargo NVARCHAR(100) NULL,
        tipo_auth NVARCHAR(20) DEFAULT 'LOCAL',
        rol_id INT,
        firma_digital VARCHAR(255) NULL,
        status TINYINT DEFAULT 1,
        fecha_registro DATETIME DEFAULT GETDATE(),
        FOREIGN KEY (rol_id) REFERENCES roles(id)
    );
END
GO

-- 6. Tabla de Documentos
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'documentos')
BEGIN
    CREATE TABLE documentos (
        id VARCHAR(50) PRIMARY KEY,
        descripcion TEXT,
        tipo VARCHAR(100),
        ruta_original VARCHAR(255),
        ruta_firmado VARCHAR(255),
        estado VARCHAR(50) DEFAULT 'SOLICITADO',
        prioridad VARCHAR(20) DEFAULT 'Normal',
        usuario_id INT,
        fecha_creacion DATETIME DEFAULT GETDATE(),
        fecha_finalizacion DATETIME NULL,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    );
END
GO

-- 7. Tabla de Adjuntos
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'documentos_adjuntos')
BEGIN
    CREATE TABLE documentos_adjuntos (
        id INT PRIMARY KEY IDENTITY(1,1),
        documento_id VARCHAR(50) NOT NULL,
        nombre_archivo VARCHAR(255) NOT NULL,
        fecha_creacion DATETIME DEFAULT GETDATE(),
        FOREIGN KEY (documento_id) REFERENCES documentos(id)
    );
END
GO

-- 8. Tabla de Seguimiento
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'seguimiento_documentos')
BEGIN
    CREATE TABLE seguimiento_documentos (
        id INT PRIMARY KEY IDENTITY(1,1),
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

-- 9. Tabla de Bitácora
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

-- 10. Tabla de Notificaciones
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'notificaciones')
BEGIN
    CREATE TABLE notificaciones (
        id INT IDENTITY(1,1) PRIMARY KEY,
        usuario_id INT NULL,
        rol_destinatario VARCHAR(50) NULL,
        departamento NVARCHAR(100) NULL,
        titulo VARCHAR(100) NOT NULL,
        mensaje TEXT NOT NULL,
        tipo VARCHAR(50) DEFAULT 'info',
        link VARCHAR(255) NULL,
        leida BIT DEFAULT 0,
        fecha_creacion DATETIME DEFAULT GETDATE(),
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    );
END
GO

-- 11. Tabla de Notas del Proyecto
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'notas_proyecto')
BEGIN
    CREATE TABLE notas_proyecto (
        id INT IDENTITY(1,1) PRIMARY KEY,
        titulo NVARCHAR(200) NOT NULL,
        contenido NVARCHAR(MAX) NOT NULL,
        autor_id INT NOT NULL,
        color_tag NVARCHAR(7) DEFAULT '#007281',
        fecha_creacion DATETIME DEFAULT GETDATE(),
        FOREIGN KEY (autor_id) REFERENCES usuarios(id)
    );
END
GO

-- 12. Tabla de Configuración
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'configuracion')
BEGIN
    CREATE TABLE configuracion (
        clave VARCHAR(100) PRIMARY KEY,
        valor TEXT NULL,
        descripcion VARCHAR(255) NULL
    );
END
GO

-- 13. Tabla de Intentos de Login (Rate-Limiting)
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'login_attempts')
BEGIN
    CREATE TABLE login_attempts (
        ip_address VARCHAR(45) NOT NULL,
        usuario VARCHAR(50) NOT NULL,
        intentos INT DEFAULT 1,
        ultimo_intento DATETIME DEFAULT GETDATE(),
        PRIMARY KEY (ip_address, usuario)
    );
END
GO

IF NOT EXISTS (SELECT 1 FROM configuracion WHERE clave = 'app_name')
BEGIN
    INSERT INTO configuracion (clave, valor, descripcion) VALUES 
    ('app_name', 'SIGEDOC', 'Nombre del sistema'),
    ('max_file_size', '100', 'Tamaño máximo de archivo en MB'),
    ('path_originales', 'storage/documentos/originales/', 'Ruta para archivos originales'),
    ('path_firmados', 'storage/documentos/firmados/', 'Ruta para archivos firmados');
END
GO

-- Versión de esquema
IF NOT EXISTS (SELECT 1 FROM migrations WHERE migration = 'v2.2.6_master_schema')
BEGIN
    INSERT INTO migrations (migration) VALUES ('v2.2.6_master_schema');
END
GO
