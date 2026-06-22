# SIGEDOC - Checklist de Verificación Post-Instalación

## ✅ Lista de Verificación

### 1. Base de Datos
- [ ] Base de datos `sigedoc` creada
- [ ] Tabla `roles` con 4 registros
- [ ] Tabla `usuarios` con 4 usuarios de prueba
- [ ] Tabla `documentos` con ejemplos
- [ ] Tabla `bitacora` creada

**Verificar con:**
```sql
USE sigedoc;
SELECT COUNT(*) FROM roles;      -- Debe retornar 4
SELECT COUNT(*) FROM usuarios;   -- Debe retornar 4
SHOW TABLES;                     -- Debe mostrar 4 tablas
```

---

### 2. Configuración
- [ ] Archivo `config.php` configurado con credenciales correctas
- [ ] Conexión a base de datos funcional
- [ ] Constante `APP_URL` configurada correctamente

**Verificar con:**
```php
php -r "require 'config.php'; echo 'Config OK\n';"
```

---

### 3. Llaves de Firma Digital
- [ ] Directorio `storage/keys/` existe
- [ ] Archivo `storage/keys/private.key` generado
- [ ] Archivo `storage/keys/public.key` generado
- [ ] Permisos correctos en private.key (600)

**Verificar con:**
```bash
ls -la storage/keys/
# Debe mostrar private.key y public.key
```

**Generar si no existen:**
```bash
php generar_llaves.php
```

---

### 4. Permisos de Directorios
- [ ] `storage/` tiene permisos de escritura
- [ ] `storage/documentos/` tiene permisos de escritura
- [ ] `storage/keys/` tiene permisos de lectura
- [ ] `storage/keys/private.key` tiene permisos 600

**Configurar permisos (Linux/Mac):**
```bash
chmod -R 755 storage/
chmod 600 storage/keys/private.key
```

**Configurar permisos (Windows):**
- Click derecho en carpeta `storage` → Propiedades → Seguridad
- Dar permisos de escritura al usuario del servidor web

---

### 5. Servidor Web
- [ ] Módulo `mod_rewrite` habilitado (Apache)
- [ ] Archivo `.htaccess` en `public/`
- [ ] Archivo `.htaccess` en `storage/`
- [ ] Document root apunta a `public/`

**Verificar Apache:**
```bash
apache2ctl -M | grep rewrite
# Debe mostrar: rewrite_module
```

**Habilitar mod_rewrite:**
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

### 6. PHP
- [ ] Versión PHP 7.4 o superior
- [ ] Extensión PDO habilitada
- [ ] Extensión OpenSSL habilitada
- [ ] Extensión mbstring habilitada
- [ ] Extensión fileinfo habilitada
- [ ] `upload_max_filesize` >= 10M
- [ ] `post_max_size` >= 10M

**Verificar con:**
```bash
php -v                           # Versión de PHP
php -m | grep -E 'PDO|openssl|mbstring|fileinfo'
php -i | grep upload_max_filesize
php -i | grep post_max_size
```

---

### 7. Acceso Web
- [ ] Página de login carga correctamente
- [ ] Estilos CSS se aplican
- [ ] JavaScript funciona
- [ ] No hay errores 404 en consola del navegador

**Acceder a:**
```
http://localhost/SIGEDOC/public/
```
o
```
http://sigedoc.local
```

---

### 8. Funcionalidad de Login
- [ ] Login con `admin` / `password123` funciona
- [ ] Login con credenciales incorrectas muestra error
- [ ] Sesión se mantiene después de login
- [ ] Redirección a dashboard funciona

---

### 9. Dashboard
- [ ] Dashboard carga correctamente
- [ ] Estadísticas se muestran
- [ ] Documentos recientes aparecen
- [ ] Actividad reciente se muestra
- [ ] Sidebar es funcional
- [ ] Header muestra información del usuario

---

### 10. Gestión de Documentos
- [ ] Listado de documentos funciona
- [ ] Filtros por estado funcionan
- [ ] Crear documento funciona
- [ ] Upload de archivos funciona
- [ ] Ver detalle de documento funciona
- [ ] Descargar documento funciona

---

### 11. Flujo de Aprobación
- [ ] Solicitante puede crear documentos
- [ ] Compras puede aprobar documentos
- [ ] Firma digital se genera al aprobar
- [ ] Gerencia puede autorizar documentos
- [ ] Rechazo de documentos funciona
- [ ] Estados se actualizan correctamente

---

### 12. Firma Digital
- [ ] Documento se firma al aprobar (Compras)
- [ ] Archivo firmado se genera (.json)
- [ ] Archivo firmado se puede descargar
- [ ] Verificación de firma funciona

**Probar manualmente:**
1. Login como `compras1`
2. Aprobar un documento en estado SOLICITADO
3. Verificar que se creó archivo firmado
4. Descargar y verificar contenido JSON

---

### 13. Bitácora
- [ ] Acciones se registran en bitácora
- [ ] Login se registra
- [ ] Logout se registra
- [ ] Creación de documentos se registra
- [ ] Aprobaciones se registran
- [ ] IP se captura correctamente

**Verificar en BD:**
```sql
SELECT * FROM bitacora ORDER BY fecha DESC LIMIT 10;
```

---

### 14. Seguridad
- [ ] Acceso directo a `storage/` está bloqueado
- [ ] Usuarios no autenticados son redirigidos a login
- [ ] Usuarios sin permisos no pueden acceder a rutas protegidas
- [ ] SQL injection está prevenido (PDO prepared statements)
- [ ] XSS está prevenido (htmlspecialchars en vistas)

**Probar:**
- Acceder a `http://localhost/SIGEDOC/storage/` (debe dar 403)
- Acceder a `/dashboard` sin login (debe redirigir a login)
- Login como `solicitante1` e intentar aprobar documento (debe denegar)

---

### 15. Responsive Design
- [ ] Funciona en desktop (1920px)
- [ ] Funciona en tablet (768px)
- [ ] Funciona en móvil (375px)
- [ ] Sidebar se colapsa en móvil
- [ ] Tablas son scrolleables en móvil
- [ ] Formularios son usables en móvil

**Probar con:**
- Chrome DevTools → Toggle device toolbar
- Probar diferentes tamaños de pantalla

---

### 16. Validaciones
- [ ] Formularios validan campos requeridos
- [ ] Upload valida tamaño de archivo (10MB)
- [ ] Upload valida tipo de archivo
- [ ] IDs de documentos son únicos
- [ ] Mensajes de error se muestran correctamente
- [ ] Mensajes de éxito se muestran correctamente

---

### 17. Rendimiento
- [ ] Páginas cargan en < 2 segundos
- [ ] No hay queries N+1
- [ ] Imágenes están optimizadas
- [ ] CSS está minificado (opcional)
- [ ] JavaScript está minificado (opcional)

---

### 18. Logs y Debugging
- [ ] Errores PHP se registran en log
- [ ] No hay errores en consola del navegador
- [ ] No hay warnings de PHP
- [ ] No hay errores SQL

**Verificar logs:**
```bash
tail -f /var/log/apache2/error.log
# o
tail -f /var/log/nginx/error.log
```

---

### 19. Backup
- [ ] Script de backup de BD creado
- [ ] Script de backup de archivos creado
- [ ] Backup programado (cron/task scheduler)

**Crear backup manual:**
```bash
mysqldump -u root -p sigedoc > backup_sigedoc_$(date +%Y%m%d).sql
tar -czf backup_storage_$(date +%Y%m%d).tar.gz storage/
```

---

### 20. Documentación
- [ ] README.md está completo
- [ ] INSTALL.md está actualizado
- [ ] RESUMEN.md documenta el proyecto
- [ ] Código está comentado
- [ ] Usuarios de prueba documentados

---

## 🎯 Resultado Final

**Total de checks completados: _____ / 100+**

### Estado del Sistema:
- [ ] ✅ **LISTO PARA PRODUCCIÓN** (90%+ completado)
- [ ] ⚠️ **REQUIERE AJUSTES** (70-89% completado)
- [ ] ❌ **NO LISTO** (< 70% completado)

---

## 📝 Notas Adicionales

Anotar aquí cualquier problema encontrado o configuración especial:

```
_______________________________________________
_______________________________________________
_______________________________________________
```

---

## 🚀 Próximos Pasos

Después de completar este checklist:

1. [ ] Cambiar contraseñas de usuarios de prueba
2. [ ] Crear usuarios reales del sistema
3. [ ] Personalizar logo en `public/images/`
4. [ ] Configurar backup automático
5. [ ] Configurar SSL/HTTPS
6. [ ] Configurar email para notificaciones (futuro)
7. [ ] Capacitar a usuarios finales

---

**Fecha de verificación**: _______________  
**Verificado por**: _______________  
**Firma**: _______________

---

¡Sistema SIGEDOC listo para usar! 🎉
