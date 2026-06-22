# SIGEDOC - Sistema de Gestión de Documentos
## PROMESE/CAL — v2.2.6

Sistema modular de gestión del ciclo de vida de documentos con firma digital y flujo de aprobaciones departamentales.

> **Estado actual:** Auditoría completada el 2026-04-22. Se identificaron **2 vulnerabilidades críticas** y **4 hallazgos altos** pendientes de corrección antes de producción. Ver [Plan de Trabajo](docs/PLAN_TRABAJO.md).

---

## 📋 Características

- **Gestión de Documentos**: Creación, visualización y seguimiento de documentos
- **Firma Digital**: Implementación de firma digital usando OpenSSL
- **Flujo de Aprobaciones**: Proceso de aprobación por departamentos (Compras → Gerencia)
- **Control de Acceso**: Sistema de roles (Solicitante, Compras, Gerencia, Administrador)
- **Bitácora de Auditoría**: Registro completo de todas las acciones del sistema
- **Interfaz Moderna**: Diseño responsivo con identidad visual de PROMESE/CAL

---

## 🛠️ Requisitos del Sistema

- **PHP**: 7.4 o superior
- **MySQL**: 5.7 o superior
- **Extensiones PHP**:
  - PDO
  - OpenSSL
  - mbstring
  - fileinfo
- **Servidor Web**: Apache o Nginx

---

## 📦 Instalación

### 1. Clonar o copiar el proyecto

```bash
cd c:\Temp\SIGEDOC
```

### 2. Configurar la base de datos

Editar el archivo `config.php` con las credenciales de tu base de datos:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'sigedoc');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
```

### 3. Crear la base de datos

Ejecutar los siguientes scripts SQL en orden:

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/init.sql
```

### 4. Generar llaves de firma digital

Ejecutar el siguiente script PHP para generar las llaves RSA:

```bash
php maintenance/generar_llaves.php
```

### 5. Configurar permisos

```bash
chmod -R 755 storage/
chmod -R 755 public/
chmod 600 storage/keys/private.key
```

### 6. Configurar el servidor web

**Apache (.htaccess ya incluido)**

Asegurarse de que `mod_rewrite` esté habilitado.

**Nginx**

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
    }
}
```

---

## 👥 Usuarios de Prueba

Después de ejecutar `init.sql`, tendrás los siguientes usuarios disponibles:

| Usuario | Contraseña | Rol |
|---------|-----------|-----|
| admin | password123 | Administrador |
| solicitante1 | password123 | Solicitante |
| compras1 | password123 | Compras |
| gerencia1 | password123 | Gerencia |

---

## 🔐 Roles y Permisos

### Solicitante
- Crear documentos
- Ver sus propios documentos
- Descargar documentos

### Compras
- Ver todos los documentos
- Aprobar documentos en estado "SOLICITADO"
- Firmar digitalmente documentos aprobados
- Rechazar documentos

### Gerencia
- Ver todos los documentos
- Autorizar documentos en estado "APROBADO_COMPRAS"
- Rechazar documentos

### Administrador
- Acceso completo al sistema
- Gestión de usuarios
- Acceso a bitácora completa
- Configuración del sistema

---

## 📁 Estructura del Proyecto

```
SIGEDOC/
├── app/                  # Lógica del Sistema (MVC)
│   ├── Controllers/      # Controladores (AuthController, DocumentoController...)
│   ├── Core/             # Clases base (Database, Router, FirmaDigital, Security)
│   ├── Libs/             # Librerías externas (PHPMailer)
│   ├── Models/           # Modelos de datos
│   └── Services/         # Servicios (LdapService, MailService)
├── config.php            # Configuración general del sistema
├── database/             # Scripts y migraciones SQL
├── docs/                 # Documentación, manuales y plan de trabajo
├── maintenance/          # Scripts de setup y mantenimiento (no desplegar en prod)
├── public/               # Punto de entrada web (index.php, assets, .htaccess)
├── scratch/              # Scripts de prueba locales (no para producción)
├── storage/              # Archivos subidos y llaves (acceso protegido)
└── views/                # Plantillas PHP de la interfaz
```

---

## 🔄 Flujo de Aprobación de Documentos

1. **SOLICITADO**: El solicitante crea un documento
2. **APROBADO_COMPRAS**: Compras aprueba y firma digitalmente el documento
3. **AUTORIZADO**: Gerencia autoriza el documento (estado final)
4. **RECHAZADO**: Compras o Gerencia puede rechazar el documento

---

## 🔒 Firma Digital

El sistema utiliza OpenSSL para implementar firma digital:

- **Algoritmo**: RSA 2048 bits
- **Hash**: SHA-256
- **Formato**: JSON con contenido codificado en Base64

### Verificar Firma

El sistema permite verificar la autenticidad de documentos firmados, mostrando:
- Validez de la firma
- Fecha de firma
- Algoritmo utilizado
- Documento original

---

## 🎨 Identidad Visual

El sistema utiliza los colores corporativos de PROMESE/CAL:

- **Primario**: #0066CC (Azul)
- **Secundario**: #003D7A (Azul oscuro)
- **Éxito**: #28A745 (Verde)
- **Peligro**: #DC3545 (Rojo)
- **Advertencia**: #FFC107 (Amarillo)

---

## 📱 Responsive Design

El sistema es completamente responsivo y funciona en:
- Escritorio (1920px+)
- Tablets (768px - 1024px)
- Móviles (320px - 767px)

---

## 🔧 Mantenimiento

### Backup de Base de Datos

```bash
mysqldump -u root -p sigedoc > backup_sigedoc_$(date +%Y%m%d).sql
```

### Backup de Documentos

```bash
tar -czf backup_documentos_$(date +%Y%m%d).tar.gz storage/documentos/
```

### Logs

Los logs del sistema se registran en la tabla `bitacora` de la base de datos.

---

## 🐛 Solución de Problemas

### Error de conexión a la base de datos
- Verificar credenciales en `config.php`
- Asegurar que MySQL esté ejecutándose
- Verificar que la base de datos `sigedoc` exista

### Error al subir archivos
- Verificar permisos en `storage/documentos/`
- Verificar configuración de `upload_max_filesize` en `php.ini`

### Error de firma digital
- Verificar que las llaves existan en `storage/keys/`
- Verificar permisos de las llaves (600 para private.key)
- Verificar que la extensión OpenSSL esté habilitada

---

## 📄 Licencia

© 2026 PROMESE/CAL - Todos los derechos reservados

---

## 👨‍💻 Soporte

Para soporte técnico, contactar al departamento de TI de PROMESE/CAL.

---

## 🚀 Roadmap & Plan de Trabajo

### 🔥 Pendientes Críticos (Inmediato)
- [ ] **[S1-T1]** Agregar `.env` al `.gitignore` y limpiar historial Git
- [ ] **[S1-T2]** Crear `.env.example` sin valores reales
- [ ] **[S1-T3]** Sanitizar inputs LDAP con `ldap_escape()` (LDAP Injection)
- [ ] **[S1-T4]** Mover cambio de estado de usuario de GET → POST + CSRF

### 🔧 Bugs a Corregir (Esta semana)
- [ ] **[S2-T1]** Fix variables no declaradas en `DocumentoController::actualizar()`
- [ ] **[S2-T2]** Reactivar rutas del módulo Archivo Digital (comentadas en `index.php`)
- [ ] **[S2-T3]** Corregir estado anterior en seguimiento de aprobación Compras
- [ ] **[S2-T4]** Fix `rol_id` hardcodeado en aprobación de usuarios

### 🗄️ Base de Datos (Semana 2)
- [ ] **[S3-T1]** Crear `schema_master_sqlserver.sql` unificado con todas las migraciones
- [ ] **[S3-T3]** Diagrama ER de la base de datos actual
- [ ] **[S3-T4]** Implementar tabla `migrations` para control de versiones

### 🏋️ Calidad & Infraestructura (Semana 3-4)
- [ ] **[S4-T3]** Paginación en listado de documentos
- [ ] **[S4-T4]** Rate-limiting en endpoint de login
- [ ] **[S5-T1]** Migrar TailwindCSS de CDN a compilado local
- [ ] **[S5-T2]** Introducir Composer para gestión de dependencias
- [ ] **[S5-T3]** Páginas de error 404/403 personalizadas

> 📄 Ver el [Plan de Trabajo completo con auditoría](docs/PLAN_TRABAJO.md)

---

**Desarrollado para PROMESE/CAL** | Auditoría: 2026-04-22
