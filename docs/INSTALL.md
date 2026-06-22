# SIGEDOC - Guía de Instalación Rápida

## Pasos de Instalación

### 1. Configurar Base de Datos

```bash
# Crear la base de datos
mysql -u root -p -e "CREATE DATABASE sigedoc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Ejecutar schema
mysql -u root -p sigedoc < database/schema.sql

# Ejecutar datos iniciales
mysql -u root -p sigedoc < database/init.sql
```

### 2. Configurar Aplicación

Editar `config.php` con tus credenciales:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'sigedoc');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
```

### 3. Generar Llaves de Firma Digital

```bash
php maintenance/generar_llaves.php
```

### 4. Configurar Permisos (Linux/Mac)

```bash
chmod -R 755 storage/
chmod 600 storage/keys/private.key
```

### 5. Configurar Servidor Web

#### Apache
El archivo `.htaccess` ya está configurado. Solo asegúrate de tener `mod_rewrite` habilitado.

#### Nginx
Agregar esta configuración a tu archivo de sitio:

```nginx
server {
    listen 80;
    server_name sigedoc.local;
    root /ruta/a/SIGEDOC/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

### 6. Acceder al Sistema

Abrir en el navegador:
```
http://localhost/SIGEDOC/public/
```

O si configuraste un virtual host:
```
http://sigedoc.local
```

## Usuarios de Prueba

| Usuario | Contraseña | Rol |
|---------|-----------|-----|
| admin | password123 | Administrador |
| solicitante1 | password123 | Solicitante |
| compras1 | password123 | Compras |
| gerencia1 | password123 | Gerencia |

## Verificación

1. Iniciar sesión con usuario `admin` / `password123`
2. Verificar que el dashboard cargue correctamente
3. Crear un documento de prueba
4. Verificar el flujo de aprobación

## Solución de Problemas

### Error de conexión a BD
- Verificar credenciales en `config.php`
- Verificar que MySQL esté corriendo
- Verificar que la base de datos exista

### Error 500
- Verificar logs de PHP
- Verificar permisos de directorios
- Verificar que todas las extensiones PHP estén instaladas

### No se pueden subir archivos
- Verificar permisos en `storage/documentos/`
- Verificar `upload_max_filesize` en `php.ini`

## Próximos Pasos

1. Cambiar las contraseñas de los usuarios de prueba
2. Crear usuarios reales del sistema
3. Configurar backup automático
4. Personalizar el logo en `public/images/`

---

¡Sistema listo para usar!
