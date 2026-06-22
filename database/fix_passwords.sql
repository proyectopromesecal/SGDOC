-- Actualizar contraseñas para que sean '123'
USE sigedoc;
GO

UPDATE usuarios SET password = '$2y$10$xEf6WiB5UAKkzf64aa4tle4y8y1STt3dFx00GVyZ.Z6IdeqJVaiAq' WHERE usuario IN ('admin', 'solicitante1', 'compras1', 'gerencia1');
GO

PRINT 'Contraseñas actualizadas a: 123';
GO
