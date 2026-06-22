# Documento Base para Acta de Entrega y Recepción - SIGEDOC

Este documento contiene la información base técnica y operativa para formalizar la entrega del Sistema Integrado de Gestión Documental (SIGEDOC). Los campos marcados con corchetes `[ ]` y cursiva deben ser completados por la institución.

---

### 1. Identificación formal del área propietaria (dueño funcional)
**Técnicamente:** El sistema automatiza el flujo de **Compras y Gerencia**.  
**Definición sugerida:** El dueño funcional principal sería la **Dirección Administrativa/Financiera o el Departamento de Compras**, mientras que la propiedad técnica recae sobre el **Departamento de TI de PROMESE/CAL**. 
*(Pendiente: Confirmar y escribir el nombre exacto del departamento).*

### 2. Responsables para la administración funcional y operativa
**En la plataforma:**  
- **Administración Operativa (Técnica):** Usuarios con el rol de `Administrador` (tienen acceso a gestión de usuarios, configuración y bitácora completa de auditoría).
- **Administración Funcional:** Usuarios con los roles de `Gerencia` y `Compras`, quienes validan y firman digitalmente los documentos.
*(Pendiente: Designar los nombres y cargos específicos de quienes ocuparán estos roles maestros).*

### 3. Responsables del soporte técnico y mantenimiento
El mantenimiento correctivo y evolutivo posterior a la puesta en producción estará a cargo del **Departamento de Tecnología de la Información (TI) de PROMESE/CAL**. 
*(Pendiente: Será necesario asignar a un analista o equipo de desarrollo específico internamente).*

### 4. Procedimiento para la gestión de incidencias
Al ser una herramienta interna, la gestión de solicitudes de soporte (ej. problemas con firmas digitales, olvido de contraseñas, errores técnicos) debe integrarse con la **Mesa de Ayuda (Help Desk)** actual de PROMESE/CAL. Los usuarios finales reportarán incidencias a través del canal oficial de tickets de la institución.

### 5. Acuerdos de Nivel de Servicio (SLA)
*(Pendiente: El equipo de TI y el dueño funcional deben consensuar los siguientes tiempos de respuesta referenciales)*:
- **Alta criticidad (Caída del sistema o servidor):** *[Ej. 2 a 4 horas]*
- **Media criticidad (Bugs menores o reinicio de perfiles):** *[Ej. 24 a 48 horas]*
- **Baja criticidad (Mejoras y evolutivos):** *[Según planificación de TI]*

### 6. Responsables de infraestructura, respaldos y monitoreo
- **Infraestructura actual:** El aplicativo está contenerizado con Docker y desplegado en el servidor de producción bajo la IP `10.70.69.15`.
- **Responsabilidad:** El monitoreo y mantenimiento del servidor recae sobre la **División de Infraestructura y Servidores** de TI.
- **Respaldos (Backups):** El sistema cuenta con scripts manuales, pero se requiere integrarlo a las políticas de copias de seguridad automatizadas (ej. Veeam) a nivel de servidor de PROMESE/CAL.

### 7. Identificación de licencias a renovar
**El sistema base está libre de licenciamiento comercial directo**. Utiliza un stack 100% open-source / gratuito:
- **Backend:** PHP 8.1
- **Servidor Web:** Apache
- **BBDD:** MySQL / SQL Server 
- **Infraestructura:** Docker
*(No hay licencias de la aplicación per se que requieran renovación futura. Las licencias del Sistema Operativo Windows Server u otros recaen en infraestructura general).*

### 8. Validación de los criterios de aceptación
Los criterios técnicos ya definidos y validados en el entorno de producción incluyen:
- Transición exitosa del flujo documental: Solicitado → Aprobado (Compras) → Autorizado (Gerencia).
- Generación y validación correcta de **Firmas Digitales (OpenSSL - RSA 2048)**.
- Registro inmutable en la bitácora de auditoría para todas las acciones.
- Mitigación de hallazgos de seguridad (LDAP injection, CSRF).

### 9. Representantes para la reunión de cierre
*(Pendiente: Especificar nombres. Se sugiere incluir a:)*
- **Líder del Proyecto Técnico:** *[Nombre del líder/arquitecto de TI]*
- **Líder de Infraestructura:** *[Encargado del servidor 10.70.69.15]*
- **Dueños Funcionales:** *[Representantes autorizados de Compras y Gerencia]*

### 10. Documento de recepción y aceptación formal
El **"Acta de Entrega y Recepción a Producción"** final (basada en la plantilla estándar de la institución) debe adjuntar este documento, además de:
- El Checklist Técnico final (`docs/CHECKLIST.md`).
- La URL del repositorio central: `https://github.com/proyectopromesecal/SGDOC`
- Firma física o digital de las partes descritas en el punto 9, concluyendo satisfactoriamente el período de validación.
