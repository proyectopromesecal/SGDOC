# Pendientes: Flujo de Aprobación de Encargados de Departamento

**Estado actual:** ✅ _Todo el código Backend, Frontend y SQL está escrito y guardado en los archivos._

Para que el modelo y la base de datos se sincronicen y podamos utilizar el sistema con el nuevo flujo intermedio de autorizaciones, dejaste pendiente ejecutar estos últimos pasos. Puedes seguirlos cuando retomes el proyecto:

### 1. Actualizar la Base de Datos
He creado un pequeño script en la raíz de tu proyecto que actualizará el `ENUM` de los estados en la base de datos y registrará automáticamente el nuevo rol llamado "Encargado de Departamento" sin sobreescribir tus datos anteriores.
* Cuando estés listo, simplemente navega a:
  **`http://localhost/SIGEDOC/public/update_db.php`** 
  *(o la URL equivalente en tu servidor de desarrollo, apuntando al archivo `update_db.php`)*
* Deberás leer en pantalla: `¡Migración de base de datos terminada con éxito!`.

### 2. Dar de alta a los Usuarios de Prueba (Roles)
Una vez que la base de datos haya incorporado el nuevo rol, necesitarás simular el circuito. Accede al sistema con la cuenta de **Administrador** y:
1. Asegúrate de que el **usuario Solicitante** que vas a probar (quien fungirá como Secretaria) tenga un **Departamento** específico asignado en su tarjeta de perfil (Ejemplo: `Tecnología` o `Contabilidad`).
2. Crea de la misma manera un nuevo usuario y asígnale el **Rol**: `Encargado de Departamento`.
3. **⚠️ IMPORTANTE:** El usuario "Encargado de Departamento" deberá tener exactamente el **mismo nombre en su propiedad de 'Departamento'** que la Secretaria, para que el sistema le devuelva solo las solicitudes originadas en esa dependencia.

### 3. Prueba de Flujo Completo End-to-End
Una vez completados los pasos 1 y 2, puedes iniciar sesión con las diferentes cuentas y comprobar el viaje de un expediente a través del ciclo de vida actualizado:
* **Paso A:** Inicia sesión con la cuenta *Solicitante* y crea una solicitud. (Estado: `SOLICITADO`).
* **Paso B:** Cierra sesión, entra con el *Encargado de Departamento* y aprueba en la sección "Documentos". (Pasa a estado: `AUTORIZADO_DEPARTAMENTO`).
* **Paso C:** Cierra sesión e ingresa como *Compras*. Deberías encontrar el documento listo para Aprobar y Firmar digitalmente con OpenSSL (Pasa a estado: `APROBADO_COMPRAS`).
* **Paso D:** Ingresa como *Gerencia* y aplica la Autorización Final (`AUTORIZADO`).

---
_Puedes eliminar este archivo y el archivo `update_db.php` una vez hayas validado que tu sistema funciona de manera exitosa._
