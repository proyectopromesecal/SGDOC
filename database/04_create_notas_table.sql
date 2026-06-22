-- Tabla para las notas de avance del proyecto
CREATE TABLE notas_proyecto (
    id INT IDENTITY(1,1) PRIMARY KEY,
    titulo NVARCHAR(200) NOT NULL,
    contenido NVARCHAR(MAX) NOT NULL,
    autor_id INT NOT NULL,
    color_tag NVARCHAR(7) DEFAULT '#007281',
    fecha_creacion DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (autor_id) REFERENCES usuarios(id)
);
GO
