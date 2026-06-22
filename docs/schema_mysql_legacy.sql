-- Estructura de Base de Datos para SIGEDOC
-- PROMESE/CAL

CREATE DATABASE IF NOT EXISTS sigedoc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sigedoc;

CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL -- Solicitante, Encargado de Departamento, Compras, Gerencia, Admin
);

CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol_id INT,
    status TINYINT DEFAULT 1,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
);

CREATE TABLE documentos (
    id VARCHAR(50) PRIMARY KEY, -- ID Único solicitado
    descripcion TEXT,
    tipo VARCHAR(100),
    ruta_original VARCHAR(255),
    ruta_firmado VARCHAR(255),
    estado ENUM('SOLICITADO', 'AUTORIZADO_DEPARTAMENTO', 'APROBADO_COMPRAS', 'AUTORIZADO', 'RECHAZADO', 'DIGITALIZADO') DEFAULT 'SOLICITADO',
    prioridad ENUM('Baja', 'Normal', 'Alta', 'Crítica') DEFAULT 'Normal',
    usuario_id INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE bitacora (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    accion VARCHAR(255),
    detalles TEXT,
    ip VARCHAR(45),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Datos iniciales
INSERT INTO roles (nombre) VALUES ('Solicitante'), ('Encargado de Departamento'), ('Compras'), ('Gerencia'), ('Administrador');
