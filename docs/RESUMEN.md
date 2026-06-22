# SIGEDOC - Resumen del Proyecto

## 📊 Estado del Proyecto: ✅ COMPLETADO

---

## 🎯 Descripción General

SIGEDOC es un sistema modular de gestión del ciclo de vida de documentos desarrollado para PROMESE/CAL. Implementa firma digital y flujo de aprobaciones departamentales siguiendo la arquitectura MVC.

---

## ✨ Características Implementadas

### 1. **Autenticación y Autorización**
- ✅ Sistema de login con sesiones
- ✅ 4 roles de usuario (Solicitante, Compras, Gerencia, Administrador)
- ✅ Control de acceso basado en roles
- ✅ Registro de actividad en bitácora

### 2. **Gestión de Documentos**
- ✅ Creación de documentos con upload de archivos
- ✅ Listado con filtros por estado
- ✅ Vista detallada de documentos
- ✅ Descarga de documentos originales y firmados
- ✅ Validación de IDs únicos

### 3. **Flujo de Aprobación**
- ✅ Estado: SOLICITADO → APROBADO_COMPRAS → AUTORIZADO
- ✅ Posibilidad de rechazo en cualquier etapa
- ✅ Visualización del flujo de trabajo
- ✅ Permisos según rol para cada acción

### 4. **Firma Digital**
- ✅ Generación de llaves RSA 2048 bits
- ✅ Firma digital con OpenSSL (SHA-256)
- ✅ Verificación de firmas
- ✅ Almacenamiento seguro de llaves
- ✅ Documentos firmados en formato JSON

### 5. **Bitácora de Auditoría**
- ✅ Registro de todas las acciones
- ✅ Captura de IP y timestamp
- ✅ Visualización de actividad reciente
- ✅ Filtros por usuario y fecha

### 6. **Dashboard**
- ✅ Estadísticas de documentos por estado
- ✅ Documentos recientes
- ✅ Actividad del sistema
- ✅ Información personalizada por rol

### 7. **Interfaz de Usuario**
- ✅ Diseño moderno y responsivo
- ✅ Colores de identidad visual PROMESE/CAL
- ✅ Navegación intuitiva
- ✅ Alertas y notificaciones
- ✅ Compatible con móviles y tablets

---

## 📁 Estructura del Proyecto

```
SIGEDOC/
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php          # Autenticación
│   │   ├── DashboardController.php     # Dashboard
│   │   └── DocumentoController.php     # Gestión de documentos
│   ├── Models/
│   │   ├── Usuario.php                 # Modelo de usuarios
│   │   ├── Documento.php               # Modelo de documentos
│   │   └── Bitacora.php                # Modelo de bitácora
│   └── Core/
│       ├── Database.php                # Conexión a BD (Singleton)
│       ├── Router.php                  # Enrutador con parámetros
│       └── FirmaDigital.php            # Sistema de firma digital
├── config.php                          # Configuración general
├── database/
│   ├── schema.sql                      # Estructura de BD
│   └── init.sql                        # Datos iniciales
├── public/
│   ├── css/
│   │   └── styles.css                  # Estilos completos
│   ├── js/
│   │   └── main.js                     # JavaScript
│   ├── .htaccess                       # Configuración Apache
│   └── index.php                       # Front controller
├── storage/
│   ├── documentos/                     # Documentos subidos
│   ├── keys/                           # Llaves de firma
│   └── .htaccess                       # Protección de acceso
├── views/
│   ├── auth/
│   │   └── login.php                   # Vista de login
│   ├── dashboard/
│   │   └── index.php                   # Vista de dashboard
│   ├── documentos/
│   │   ├── listar.php                  # Listado de documentos
│   │   ├── crear.php                   # Formulario de creación
│   │   └── ver.php                     # Detalle de documento
│   └── partials/
│       ├── header.php                  # Header reutilizable
│       └── sidebar.php                 # Sidebar reutilizable
├── .gitignore                          # Archivos a ignorar
├── generar_llaves.php                  # Script de generación de llaves
├── INSTALL.md                          # Guía de instalación rápida
└── README.md                           # Documentación completa
```

---

## 🔧 Tecnologías Utilizadas

- **Backend**: PHP 7.4+
- **Base de Datos**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Seguridad**: OpenSSL para firma digital
- **Arquitectura**: MVC (Model-View-Controller)
- **Servidor Web**: Apache/Nginx

---

## 👥 Usuarios de Prueba

| Usuario | Contraseña | Rol | Permisos |
|---------|-----------|-----|----------|
| admin | password123 | Administrador | Acceso completo |
| solicitante1 | password123 | Solicitante | Crear y ver sus documentos |
| compras1 | password123 | Compras | Aprobar y firmar documentos |
| gerencia1 | password123 | Gerencia | Autorizar documentos |

---

## 🔐 Seguridad Implementada

1. **Autenticación**:
   - Contraseñas hasheadas con bcrypt
   - Sesiones seguras
   - Protección contra SQL injection (PDO prepared statements)

2. **Autorización**:
   - Control de acceso basado en roles
   - Verificación de permisos en cada acción
   - Validación de propiedad de documentos

3. **Firma Digital**:
   - Llaves RSA 2048 bits
   - Hash SHA-256
   - Almacenamiento seguro de llaves privadas

4. **Protección de Archivos**:
   - Directorio storage protegido con .htaccess
   - Validación de tipos de archivo
   - Límite de tamaño de archivos (10MB)

---

## 📊 Base de Datos

### Tablas Implementadas:

1. **roles**: Definición de roles del sistema
2. **usuarios**: Usuarios con autenticación
3. **documentos**: Gestión de documentos
4. **bitacora**: Auditoría de acciones

### Relaciones:
- usuarios → roles (Many-to-One)
- documentos → usuarios (Many-to-One)
- bitacora → usuarios (Many-to-One)

---

## 🎨 Diseño Visual

### Paleta de Colores (PROMESE/CAL):
- **Primario**: #0066CC (Azul)
- **Secundario**: #003D7A (Azul oscuro)
- **Éxito**: #28A745 (Verde)
- **Peligro**: #DC3545 (Rojo)
- **Advertencia**: #FFC107 (Amarillo)

### Características de UI:
- Diseño responsivo (mobile-first)
- Sidebar colapsable
- Tablas responsivas
- Formularios validados
- Alertas auto-ocultables

---

## 🚀 Instalación

### Requisitos Previos:
- PHP 7.4+ con extensiones: PDO, OpenSSL, mbstring, fileinfo
- MySQL 5.7+
- Apache/Nginx

### Pasos:
1. Configurar base de datos (ejecutar schema.sql e init.sql)
2. Editar config.php con credenciales
3. Ejecutar generar_llaves.php
4. Configurar permisos de directorios
5. Acceder vía navegador

Ver **INSTALL.md** para instrucciones detalladas.

---

## 📈 Flujo de Trabajo

```
┌─────────────┐
│ Solicitante │ Crea documento
└──────┬──────┘
       │
       ▼
┌─────────────────┐
│   SOLICITADO    │
└──────┬──────────┘
       │
       ▼ Aprueba
┌─────────────┐
│   Compras   │ Firma digitalmente
└──────┬──────┘
       │
       ▼
┌──────────────────┐
│ APROBADO_COMPRAS │
└──────┬───────────┘
       │
       ▼ Autoriza
┌─────────────┐
│  Gerencia   │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│ AUTORIZADO  │ ✓ Finalizado
└─────────────┘
```

---

## 📝 Archivos Clave

### Backend:
- `app/Core/Database.php`: Conexión singleton a BD
- `app/Core/Router.php`: Enrutador con soporte de parámetros
- `app/Core/FirmaDigital.php`: Sistema de firma digital
- `app/Controllers/DocumentoController.php`: Lógica de documentos
- `app/Models/Documento.php`: Modelo de datos de documentos

### Frontend:
- `public/css/styles.css`: Estilos completos del sistema
- `public/js/main.js`: Funcionalidades JavaScript
- `views/documentos/ver.php`: Vista detallada con flujo de trabajo

### Configuración:
- `config.php`: Configuración general
- `public/.htaccess`: Rewrite rules
- `storage/.htaccess`: Protección de archivos

---

## ✅ Testing Checklist

- [x] Login con diferentes roles
- [x] Creación de documentos
- [x] Aprobación por Compras (con firma)
- [x] Autorización por Gerencia
- [x] Rechazo de documentos
- [x] Descarga de documentos
- [x] Verificación de firma digital
- [x] Registro en bitácora
- [x] Filtros de documentos
- [x] Responsividad en móvil
- [x] Validación de formularios
- [x] Control de permisos por rol

---

## 🔮 Próximas Mejoras (Roadmap)

### Versión 1.1:
- [ ] Notificaciones por email
- [ ] Exportación de reportes PDF
- [ ] Gráficos estadísticos
- [ ] Búsqueda avanzada
- [ ] Historial de cambios

### Versión 1.2:
- [ ] API REST
- [ ] App móvil nativa
- [ ] Integración Active Directory
- [ ] Firma digital múltiple
- [ ] Plantillas de documentos

---

## 📞 Soporte

Para soporte técnico, contactar al departamento de TI de PROMESE/CAL.

---

## 📄 Licencia

© 2026 PROMESE/CAL - Todos los derechos reservados

---

**Desarrollado con ❤️ para PROMESE/CAL**

**Versión**: 1.0.0  
**Fecha**: Febrero 2026  
**Estado**: Producción Ready ✅
