<?php
namespace App\Controllers;

use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Bitacora;
use App\Models\Departamento;

class UsuarioController {
    private $usuarioModel;
    private $rolModel;
    private $bitacoraModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->rolModel = new Rol();
        $this->bitacoraModel = new Bitacora();
        $this->departamentoModel = new Departamento();
    }

    public function index() {
        $pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($pagina < 1) $pagina = 1;
        $limite = 10;
        
        $usuarios = $this->usuarioModel->obtenerUsuariosNormales($pagina, $limite);
        $totalRegistros = $this->usuarioModel->contarUsuariosNormales();
        $totalPaginas = ceil($totalRegistros / $limite);

        $pendientesRaw = $this->usuarioModel->obtenerUsuariosPendientes();
        $pendientes = [];
        
        foreach ($pendientesRaw as $u) {
            // Trazabilidad de la solicitud desde bitácora
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT TOP 1 detalles, fecha, ip 
                                FROM bitacora 
                                WHERE accion = 'SOLICITUD_ACCESO' 
                                  AND (detalles LIKE :user_id OR detalles LIKE :user_name)
                                ORDER BY fecha DESC");
            $memo_id = '%ID: ' . $u['id'] . '%';
            $memo_name = '%' . $u['usuario'] . '%';
            $stmt->bindParam(':user_id', $memo_id);
            $stmt->bindParam(':user_name', $memo_name);
            $stmt->execute();
            $u['solicitud_info'] = $stmt->fetch(\PDO::FETCH_ASSOC);

            $pendientes[] = $u;
        }

        $roles = array_filter($this->rolModel->obtenerTodos(), function($r) {
            return $r['id'] != 6; // No permitir asignar el rol de pendiente manualmente
        });

        $departamentos = $this->departamentoModel->obtenerActivos();

        require_once VIEWS_PATH . '/usuarios/index.php';
    }

    public function aprobar($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rol_id = $_POST['rol_id'] ?? '';
            
            if (empty($rol_id)) {
                $_SESSION['error'] = 'Debe asignar un rol al aprobar el usuario.';
                header('Location: /usuarios');
                exit;
            }
            if ($this->usuarioModel->actualizar($id, ['rol_id' => $rol_id])) {
                $this->bitacoraModel->registrar(
                    $_SESSION['usuario_id'],
                    'USUARIO_APROBADO',
                    'Se aprobó el acceso al usuario ID: ' . $id . ' con el rol ID: ' . $rol_id
                );
                $_SESSION['success'] = 'Usuario aprobado correctamente.';
            } else {
                $_SESSION['error'] = 'Error al aprobar el usuario.';
            }
        }
        header('Location: /usuarios');
        exit;
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'usuario' => $_POST['usuario'] ?? '',
                'password' => $_POST['password'] ?? '',
                'rol_id' => $_POST['rol_id'] ?? '',
                'nombre' => $_POST['nombre'] ?? '',
                'email' => $_POST['email'] ?? '',
                'cargo' => $_POST['cargo'] ?? '',
                'departamento_id' => !empty($_POST['departamento_id']) ? $_POST['departamento_id'] : null
            ];

            if (empty($datos['usuario']) || empty($datos['password']) || empty($datos['rol_id'])) {
                $_SESSION['error'] = 'Todos los campos son obligatorios para nuevos usuarios.';
                header('Location: /usuarios');
                exit;
            }

            if (!empty($datos['departamento_id'])) {
                $dept = $this->departamentoModel->obtenerPorId($datos['departamento_id']);
                if ($dept) {
                    $datos['departamento'] = $dept['nombre'];
                }
            }

            // Manejar firma digital
            $firma = $this->handleFirmaUpload($datos['usuario']);
            if ($firma) {
                $datos['firma_digital'] = $firma;
            }

            if ($this->usuarioModel->crear($datos)) {
                $this->bitacoraModel->registrar(
                    $_SESSION['usuario_id'],
                    'USUARIO_CREADO',
                    'Se creó el usuario: ' . $datos['usuario']
                );
                $_SESSION['success'] = 'Usuario creado correctamente.';
            } else {
                $_SESSION['error'] = 'Error al crear el usuario.';
            }
        }
        header('Location: /usuarios');
        exit;
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            $datos = [
                'usuario' => $_POST['usuario'] ?? '',
                'password' => $_POST['password'] ?? '',
                'rol_id' => $_POST['rol_id'] ?? '',
                'nombre' => $_POST['nombre'] ?? '',
                'email' => $_POST['email'] ?? '',
                'cargo' => $_POST['cargo'] ?? '',
                'departamento_id' => !empty($_POST['departamento_id']) ? $_POST['departamento_id'] : null
            ];

            if (empty($id) || empty($datos['usuario']) || empty($datos['rol_id'])) {
                $_SESSION['error'] = 'ID, usuario y rol son obligatorios.';
                header('Location: /usuarios');
                exit;
            }

            if (!empty($datos['departamento_id'])) {
                $dept = $this->departamentoModel->obtenerPorId($datos['departamento_id']);
                if ($dept) {
                    $datos['departamento'] = $dept['nombre'];
                }
            } else {
                $datos['departamento'] = null;
            }

            // Manejar firma digital
            $firma = $this->handleFirmaUpload($datos['usuario']);
            if ($firma) {
                $datos['firma_digital'] = $firma;
            }

            if ($this->usuarioModel->actualizar($id, $datos)) {
                $this->bitacoraModel->registrar(
                    $_SESSION['usuario_id'],
                    'USUARIO_ACTUALIZADO',
                    'Se actualizó el usuario ID: ' . $id
                );
                $_SESSION['success'] = 'Usuario actualizado correctamente.';
            } else {
                $_SESSION['error'] = 'Error al actualizar el usuario.';
            }
        }
        header('Location: /usuarios');
        exit;
    }

    public function cambiarEstado($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /usuarios');
            exit;
        }

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Token de seguridad inválido.';
            header('Location: /usuarios');
            exit;
        }

        $usuario = $this->usuarioModel->obtenerPorId($id);
        if ($usuario) {
            $nuevoEstado = $usuario['status'] == 1 ? 0 : 1;
            if ($this->usuarioModel->cambiarEstado($id, $nuevoEstado)) {
                $this->bitacoraModel->registrar(
                    $_SESSION['usuario_id'],
                    'ESTADO_USUARIO_CAMBIADO',
                    'Se cambió el estado del usuario ID: ' . $id . ' a ' . ($nuevoEstado == 1 ? 'Activo' : 'Inactivo')
                );
                $_SESSION['success'] = 'Estado de usuario actualizado.';
            } else {
                $_SESSION['error'] = 'Error al cambiar estado.';
            }
        }
        header('Location: /usuarios');
        exit;
    }

    public function desactivarActivos() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /usuarios');
            exit;
        }

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Token de seguridad inválido.';
            header('Location: /usuarios');
            exit;
        }

        if ($this->usuarioModel->desactivarActivos()) {
            $this->bitacoraModel->registrar(
                $_SESSION['usuario_id'],
                'USUARIOS_DESACTIVADOS_MASIVO',
                'Se desactivaron masivamente todos los usuarios activos.'
            );
            $_SESSION['success'] = 'Todos los usuarios activos han sido desactivados.';
        } else {
            $_SESSION['error'] = 'Error al desactivar los usuarios.';
        }
        header('Location: /usuarios');
        exit;
    }

    private function handleFirmaUpload($usuarioNombre) {
        if (isset($_FILES['firma_digital']) && $_FILES['firma_digital']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['firma_digital'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            // Permitir certificados P12, llaves PEM y sellos gráficos PNG/JPG
            $allowed = ['p12', 'key', 'crt', 'pem', 'png', 'jpg', 'jpeg'];
            
            if (!in_array($ext, $allowed)) {
                return null;
            }

            $uploadDir = STORAGE_PATH . '/usuarios_firmas/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0700, true);
            }

            $fileName = $usuarioNombre . '_firma_' . time() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
                return $fileName;
            }
        }
        return null;
    }

    public function buscarLdap() {
        header('Content-Type: application/json');
        
        $termino = $_POST['termino'] ?? '';
        if (empty($termino)) {
            echo json_encode(['success' => false, 'error' => 'Término de búsqueda vacío']);
            return;
        }

        $ldapService = new \App\Services\LdapService();
        $safeTermino = ldap_escape($termino, "", LDAP_ESCAPE_FILTER);
        $filter = "(|(samaccountname=*$safeTermino*)(displayname=*$safeTermino*))";
        
        $bindUser = $_SESSION['ldap_user'] ?? null;
        $bindPass = $_SESSION['ldap_pass'] ?? null;
        $usuarios = $ldapService->buscarUsuarios($filter, $bindUser, $bindPass);
        
        if ($usuarios !== false) {
            echo json_encode(['success' => true, 'usuarios' => $usuarios]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al buscar en LDAP. Compruebe la conexión.']);
        }
    }

    public function activarLdap() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $data = [
            'usuario' => $_POST['usuario'] ?? '',
            'password' => '', // LDAP maneja la contraseña
            'rol_id' => $_POST['rol_id'] ?? 1,
            'nombre' => $_POST['nombre'] ?? '',
            'email' => $_POST['email'] ?? '',
            'departamento' => $_POST['departamento'] ?? '',
            'cargo' => $_POST['cargo'] ?? ''
        ];
        
        if (!empty($data['usuario'])) {
            $existing = $this->usuarioModel->obtenerPorUsuario($data['usuario']);
            if ($existing) {
                $_SESSION['error'] = 'El usuario ya existe localmente.';
            } else {
                // Sincronizar departamento con catálogo
                $departamentoTexto = $data['departamento'];
                $departamentoId = null;
                if (!empty($departamentoTexto)) {
                    $db = \App\Core\Database::getInstance()->getConnection();
                    $stmtDept = $db->prepare("SELECT id FROM departamentos WHERE nombre = :nombre");
                    $stmtDept->execute(['nombre' => $departamentoTexto]);
                    $deptRow = $stmtDept->fetch(\PDO::FETCH_ASSOC);
                    
                    if ($deptRow) {
                        $departamentoId = $deptRow['id'];
                    } else {
                        // Crear el departamento dinámicamente si no existe
                        $stmtInsertDept = $db->prepare("INSERT INTO departamentos (nombre, activo) VALUES (:nombre, 1)");
                        $stmtInsertDept->execute(['nombre' => $departamentoTexto]);
                        $departamentoId = $db->lastInsertId();
                    }
                }
                $data['departamento_id'] = $departamentoId;

                if ($this->usuarioModel->crear($data)) {
                    $_SESSION['mensaje'] = 'Usuario activado exitosamente desde LDAP';
                    $this->bitacoraModel->registrar(
                        $_SESSION['usuario_id'],
                        'USUARIO_ACTIVADO_LDAP',
                        'Se activó el acceso para: ' . $data['usuario']
                    );
                } else {
                    $_SESSION['error'] = 'Error al registrar el usuario en la base de datos.';
                }
            }
        }
        
        header('Location: /usuarios');
        exit;
    }

    public function sincronizarLdap() {
        header('Content-Type: application/json');
        
        try {
            // Aumentar el tiempo máximo de ejecución para procesar 1000+ usuarios
            set_time_limit(300);

            $ldapService = new \App\Services\LdapService();
            $bindUser = defined('LDAP_BIND_USER') ? LDAP_BIND_USER : null;
            $bindPass = defined('LDAP_BIND_PASS') ? LDAP_BIND_PASS : null;
            
            // 1. Descargar TODOS los usuarios del LDAP
            $ldapData = $ldapService->buscarUsuariosPaginados("(&(objectCategory=person)(objectClass=user))", $bindUser, $bindPass);
            
            if ($ldapData === false) {
                throw new \Exception("No se pudo conectar al LDAP o recuperar los usuarios.");
            }

            // Deduplicar usuarios de LDAP por samaccountname (usuario) para evitar duplicados
            $ldapDataFiltered = [];
            $seenUsernames = [];
            foreach ($ldapData as $l) {
                if (empty($l['usuario'])) continue;
                $usernameLower = strtolower($l['usuario']);
                if (isset($seenUsernames[$usernameLower])) {
                    continue;
                }
                $seenUsernames[$usernameLower] = true;
                $ldapDataFiltered[] = $l;
            }
            $ldapData = $ldapDataFiltered;

            // 2. Obtener usuarios actuales de la BD (indexados por username para búsqueda rápida)
            $usuariosDbRaw = $this->usuarioModel->obtenerTodos();
            $usuariosDb = [];
            foreach ($usuariosDbRaw as $u) {
                if (!empty($u['usuario'])) {
                    $usuariosDb[strtolower($u['usuario'])] = $u;
                }
            }

            $db = \App\Core\Database::getInstance()->getConnection();
            $creados = 0;
            $actualizados = 0;
            $desactivados = 0;
            $ldapUsernames = []; // Lista negra/blanca de control

            // 3. Procesar cada usuario del LDAP
            foreach ($ldapData as $l) {
                if (empty($l['usuario'])) continue;
                
                $username = strtolower($l['usuario']);
                $ldapUsernames[] = $username; // Lo agregamos a la lista de "vivos"
                
                $departamento_id = null;
                
                // Mapeo/Creación de Departamento si viene en el AD
                if (!empty($l['departamento'])) {
                    $stmtDept = $db->prepare("SELECT id FROM departamentos WHERE nombre = :nombre");
                    $stmtDept->execute(['nombre' => $l['departamento']]);
                    $deptRow = $stmtDept->fetch(\PDO::FETCH_ASSOC);
                    
                    if ($deptRow) {
                        $departamento_id = $deptRow['id'];
                    } else {
                        $stmtInsertDept = $db->prepare("INSERT INTO departamentos (nombre, activo) VALUES (:nombre, 1)");
                        $stmtInsertDept->execute(['nombre' => $l['departamento']]);
                        $departamento_id = $db->lastInsertId();
                    }
                }

                // Verificar si ya existe en SIGEDOC
                if (isset($usuariosDb[$username])) {
                    $u = $usuariosDb[$username];
                    
                    // No modificar al super admin (id 1)
                    if ($u['id'] == 1) continue;

                    $updateData = [];
                    if (!empty($l['nombre']) && $u['nombre'] !== $l['nombre']) $updateData['nombre'] = $l['nombre'];
                    if (!empty($l['email']) && $u['email'] !== $l['email']) $updateData['email'] = $l['email'];
                    if (!empty($l['cargo']) && $u['cargo'] !== $l['cargo']) $updateData['cargo'] = $l['cargo'];
                    
                    // Si el LDAP trajo un departamento válido y es distinto al actual
                    if ($departamento_id !== null && $u['departamento_id'] != $departamento_id) {
                        $updateData['departamento_id'] = $departamento_id;
                        $updateData['departamento'] = $l['departamento'];
                    }
                    
                    // Si el usuario estaba inactivo (status 0) en BD pero reapareció en LDAP, lo reactivamos
                    if (isset($u['status']) && $u['status'] == 0) {
                        $this->usuarioModel->cambiarEstado($u['id'], 1);
                        $actualizados++;
                    }

                    if (!empty($updateData)) {
                        // Mantener el rol que ya tiene
                        $updateData['rol_id'] = $u['rol_id'];
                        $this->usuarioModel->actualizar($u['id'], $updateData);
                        $actualizados++;
                    }
                } else {
                    // No existe en BD, lo creamos como "Pendiente de Acceso" (rol 6)
                    $this->usuarioModel->crear([
                        'usuario' => $l['usuario'],
                        'password' => bin2hex(random_bytes(16)), // Contraseña aleatoria, debe usar AD para entrar
                        'rol_id' => 6, // Pendiente de Acceso
                        'nombre' => $l['nombre'],
                        'email' => $l['email'],
                        'departamento' => $l['departamento'],
                        'cargo' => $l['cargo'],
                        'tipo_auth' => 'LDAP',
                        'departamento_id' => $departamento_id
                    ]);
                    $creados++;
                }
            }

            // 4. Desactivar usuarios locales que ya no existen en el LDAP
            foreach ($usuariosDb as $username => $u) {
                // Excepciones: Root admin, o si ya está inactivo, o si es una cuenta local manual
                if ($u['id'] == 1 || $u['status'] == 0 || $u['tipo_auth'] !== 'LDAP') {
                    continue;
                }
                
                // Si el usuario de BD no vino en la lista masiva del LDAP, lo desactivamos
                if (!in_array($username, $ldapUsernames)) {
                    $this->usuarioModel->cambiarEstado($u['id'], 0);
                    $desactivados++;
                }
            }
            
            echo json_encode([
                'success' => true, 
                'creados' => $creados,
                'actualizados' => $actualizados,
                'desactivados' => $desactivados,
                'total_ldap' => count($ldapData)
            ]);

        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error'   => $e->getMessage(),
                'file'    => $e->getFile() . ':' . $e->getLine()
            ]);
        }
    }
}
