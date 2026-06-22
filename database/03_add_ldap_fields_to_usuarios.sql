-- Migración: Agregar campos para soporte LDAP en la tabla usuarios
ALTER TABLE usuarios ADD nombre NVARCHAR(200) NULL;
ALTER TABLE usuarios ADD email NVARCHAR(255) NULL;
ALTER TABLE usuarios ADD departamento NVARCHAR(100) NULL;
ALTER TABLE usuarios ADD cargo NVARCHAR(100) NULL;
ALTER TABLE usuarios ADD tipo_auth NVARCHAR(20) DEFAULT 'LOCAL';
GO

-- Actualizar usuarios existentes a tipo LOCAL
UPDATE usuarios SET tipo_auth = 'LOCAL' WHERE tipo_auth IS NULL;
GO
