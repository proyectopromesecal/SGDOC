<?php
namespace App\Services;

class LdapService {
    private $host;
    private $port;
    private $baseDn;
    private $domain;
    private $bindUser;
    private $bindPass;

    public function __construct() {
        $this->host = defined('LDAP_HOST') ? LDAP_HOST : 'promese.promesecal.gob.do';
        $this->port = defined('LDAP_PORT') ? LDAP_PORT : 389;
        $this->baseDn = defined('LDAP_BASE_DN') ? LDAP_BASE_DN : 'DC=promese,DC=promesecal,DC=gob,DC=do';
        $this->domain = defined('LDAP_DOMAIN') ? LDAP_DOMAIN : 'promese.promesecal.gob.do';
        $this->bindUser = defined('LDAP_BIND_USER') ? LDAP_BIND_USER : '';
        $this->bindPass = defined('LDAP_BIND_PASS') ? LDAP_BIND_PASS : '';
    }

    /**
     * Intentar autenticar contra el Directorio Activo
     * 
     * @param string $usuario
     * @param string $password
     * @return array|bool Datos del usuario si tiene éxito, false si falla
     */
    public function autenticar($usuario, $password) {
        if (empty($usuario) || empty($password)) {
            return false;
        }

        // Sanitizar el nombre de usuario para prevenir LDAP Injection (caracteres especiales o null bytes)
        $usuarioSeguro = ldap_escape($usuario, "", LDAP_ESCAPE_DN);

        // Algunos servidores AD requieren el formato usuario@dominio o DOMINIO\usuario
        // En base a seguridad.php de horasextras, parece que el usuario se pasa tal cual,
        // pero sincronizar_ad.php sugiere usuario@dominio
        $ldapUser = (strpos($usuarioSeguro, '@') === false) ? $usuarioSeguro . '@' . $this->domain : $usuarioSeguro;

        $ds = @ldap_connect($this->host, $this->port);
        if (!$ds) {
            return false;
        }

        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);

        // Intentar el bind (autenticación)
        $bind = @ldap_bind($ds, $ldapUser, $password);

        if ($bind) {
            // Si el bind fue exitoso, intentamos obtener información extendida
            $userData = $this->getUserInfo($ds, $usuario);
            @ldap_unbind($ds);
            
            if ($userData) {
                return $userData;
            }
            
            // Si no pudimos obtener info extendida, devolvemos al menos el usuario
            return [
                'usuario' => $usuario,
                'nombre' => $usuario,
                'email' => '',
                'departamento' => '',
                'cargo' => ''
            ];
        }

        @ldap_unbind($ds);
        return false;
    }

    /**
     * Obtener información extendida del usuario desde LDAP
     */
    private function getUserInfo($ds, $usuario) {
        $safeUser = ldap_escape($usuario, "", LDAP_ESCAPE_FILTER);
        $filter = "(samaccountname=$safeUser)";
        $attributes = ['displayname', 'mail', 'department', 'title', 'samaccountname'];
        $search = @ldap_search($ds, $this->baseDn, $filter, $attributes);
        
        if ($search) {
            $entries = ldap_get_entries($ds, $search);
            if ($entries['count'] > 0) {
                return [
                    'usuario' => $entries[0]['samaccountname'][0] ?? $usuario,
                    'nombre' => $entries[0]['displayname'][0] ?? $usuario,
                    'email' => $entries[0]['mail'][0] ?? '',
                    'departamento' => $entries[0]['department'][0] ?? '',
                    'cargo' => $entries[0]['title'][0] ?? ''
                ];
            }
        }
        return null;
    }

    /**
     * Buscar múltiples usuarios en el AD
     */
    public function buscarUsuarios($filter = "(objectClass=user)", $bindUser = null, $bindPass = null) {
        $ds = @ldap_connect($this->host, $this->port);
        if (!$ds) return false;

        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);

        // Determinar credenciales
        $user = !empty($bindUser) ? $bindUser : $this->bindUser;
        $pass = !empty($bindPass) ? $bindPass : $this->bindPass;

        if (!empty($user) && !empty($pass)) {
            $ldapUser = (strpos($user, '@') === false) ? $user . '@' . $this->domain : $user;
            $bind = @ldap_bind($ds, $ldapUser, $pass);
        } else {
            $bind = @ldap_bind($ds);
        }

        if (!$bind) {
            @ldap_unbind($ds);
            return false;
        }

        $attributes = ['samaccountname', 'displayname', 'mail', 'department', 'title'];
        $search = @ldap_search($ds, $this->baseDn, $filter, $attributes);
        
        $results = [];
        if ($search) {
            $entries = ldap_get_entries($ds, $search);
            for ($i = 0; $i < $entries['count']; $i++) {
                $results[] = [
                    'usuario' => $entries[$i]['samaccountname'][0] ?? '',
                    'nombre' => $entries[$i]['displayname'][0] ?? '',
                    'email' => $entries[$i]['mail'][0] ?? '',
                    'departamento' => $entries[$i]['department'][0] ?? '',
                    'cargo' => $entries[$i]['title'][0] ?? ''
                ];
            }
        }
        @ldap_unbind($ds);
        return $results;
    }

    /**
     * Extraer TODOS los usuarios del AD usando paginación
     */
    public function buscarUsuariosPaginados($filter = "(&(objectCategory=person)(objectClass=user))", $bindUser = null, $bindPass = null) {
        $ds = @ldap_connect($this->host, $this->port);
        if (!$ds) return false;

        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);

        $user = !empty($bindUser) ? $bindUser : $this->bindUser;
        $pass = !empty($bindPass) ? $bindPass : $this->bindPass;
        $ldapUser = (strpos($user, '@') === false) ? $user . '@' . $this->domain : $user;
        
        $bind = @ldap_bind($ds, $ldapUser, $pass);
        if (!$bind) {
            @ldap_unbind($ds);
            return false;
        }

        $attributes = ['samaccountname', 'displayname', 'mail', 'department', 'title'];
        $results = [];
        $cookie = '';
        $pageSize = 500;

        do {
            $controls = [
                [
                    'oid' => LDAP_CONTROL_PAGEDRESULTS,
                    'value' => ['size' => $pageSize, 'cookie' => $cookie]
                ]
            ];

            $search = @ldap_search($ds, $this->baseDn, $filter, $attributes, 0, 0, 0, LDAP_DEREF_NEVER, $controls);
            
            if (!$search) break;

            $entries = ldap_get_entries($ds, $search);
            
            for ($i = 0; $i < $entries['count']; $i++) {
                $sam = $entries[$i]['samaccountname'][0] ?? '';
                if (!empty($sam)) {
                    $results[] = [
                        'usuario' => $sam,
                        'nombre' => $entries[$i]['displayname'][0] ?? $sam,
                        'email' => $entries[$i]['mail'][0] ?? '',
                        'departamento' => $entries[$i]['department'][0] ?? '',
                        'cargo' => $entries[$i]['title'][0] ?? ''
                    ];
                }
            }

            @ldap_parse_result($ds, $search, $errcode, $matcheddn, $errmsg, $referrals, $ctrls);
            $cookie = $ctrls[LDAP_CONTROL_PAGEDRESULTS]['value']['cookie'] ?? '';

        } while ($cookie !== null && $cookie != '');

        @ldap_unbind($ds);
        return $results;
    }
}
