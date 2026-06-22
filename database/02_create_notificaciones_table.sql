IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='notificaciones' and xtype='U')
CREATE TABLE notificaciones (
    id INT IDENTITY(1,1) PRIMARY KEY,
    usuario_id INT NULL,
    rol_destinatario VARCHAR(50) NULL,
    titulo VARCHAR(100) NOT NULL,
    mensaje TEXT NOT NULL,
    tipo VARCHAR(50) DEFAULT 'info', -- 'info', 'success', 'warning', 'error'
    link VARCHAR(255) NULL,
    leida BIT DEFAULT 0,
    fecha_creacion DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE INDEX idx_notif_usuario ON notificaciones(usuario_id);
CREATE INDEX idx_notif_rol ON notificaciones(rol_destinatario);
