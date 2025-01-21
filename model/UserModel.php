<?php
/**
 * Created by PhpStorm.
 * User: yvelez
 * Date: 2/03/16
 * Time: 11:39
 */

class UserModel extends User {
    protected $userTable;
    protected $userRolesTable;
    protected $appAccessTable;
    protected $wpdb;

    function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->userTable = $wpdb->prefix . 'users';
        $this->userRolesTable = $wpdb->prefix . 'user_roles';
        $this->appAccessTable = $wpdb->prefix . 'app_access';
    }

    function getWPDB() {
        return $this->wpdb;
    }

    function getUsers() {
        $users = $this->wpdb->getAll($this->userTable, 'where id > 1', 'ORDER BY username ASC');
        $users = persist($users, 'User');

        return $users;
    }

    function getOnlyIdUsernameUsers() {
        $query = 'SELECT id, username, userstore, userrepository, usermanager, useraccounting FROM ';
        $query .=  $this->userTable . ' WHERE id <> 1 AND active = 1 AND deleted = 0';
        $query .= ' ORDER BY username ASC';
        return $this->wpdb->get_results($query);
    }

    function getUserData($id = 0) {
        // Si id vale 0 es porque estamos pasando el parámetro por GET.
        if ($id == 0) {
            $userId = $_GET['id'];
        }

        // Pasamos el parámetro id (si estamos cambiando los datos de usuario save_edit)
        if ($id > 0){
            $userId = $id;
        }

        $data = $this->wpdb->getOneRow($this->userTable, $userId);
        $user = persist($data, 'User');

        return $user;
    }

    function existEmail($email) {
        $query = 'SELECT * FROM ' . $this->userTable . ' WHERE user_email="' . $email . '" LIMIT 1';
        $data = $this->wpdb->get_results($query);
        if (count($data) > 0) {
            return true;
        }

        return false;
    }

    function existUsername($username) {
        $query = 'SELECT * FROM ' . $this->userTable . ' WHERE username="' . $username. '" LIMIT 1';
        $data = $this->wpdb->get_results($query);
        if (count($data) > 0) {
            return true;
        }

        return false;
    }

    function getUser() {
        $mensaje = "";
        if (!empty($_POST['user_login']) && !empty($_POST['user_pass'])) {

            $query = 'SELECT * FROM ' . $this->userTable . ' WHERE username="' . $_POST['user_login'] . '" AND user_pass="' . md5($_POST['user_pass']) . '"';
            $user = $this->wpdb->get_row($query);

            if ($user) {
                $user = persist($user, 'User');
            }

            if (count($user) > 0 && $user->deleted == 0) {
                if ($user->active > 0) {
                	/*if (isAdmin()) {
                		print_r($user);
						exit;
                	}*/
                    $this->updateLastLogin($user->getId());
                    $_SESSION['user'] = $user;
					//$cookieTime = time()+60*60*24*30;
					//setcookie('user', $user->id, $cookieTime);
					createCookie("user", $user->id, 30);
                    $mensaje = 'El usuario ' . $user->username. ' hizo login el ' . date('d/m/Y H:i:s');
                    $mensaje .= ' en ' . $_SERVER['SERVER_NAME'] . '.<br> Acceso desde ' . $_SERVER['REMOTE_ADDR'];
                    //$this->saveAppAccess($user->getId());
                    //sendMail('jeixuxspn@gmail.com', 'Login en el administrador', $mensaje);
                    //new Mailer('jeixuxspn@gmail.com', 'Login en el administrador', $mensaje);
                    // Usamos javascript para evitar (Warning: Cannot modify header information - headers already sent)
                    
                    /*if (isAdmin()) {
                    	pre($user);
                    }*/

                    if (!$_SERVER['HTTP_REFERER']) {
                        redirectToIndex();
                    } else if (strpos($_SERVER['HTTP_REFERER'], LOGOUT_URL) !== false || strpos($_SERVER['HTTP_REFERER'], "cerrar-sesion") !== false){
                        // Si el HTTP_REFERER es a logout redireccionamos a index
                        redirectToIndex();
                    } else {
                        //header('location: ' . $_SERVER['HTTP_REFERER'] . '');
                        redirectToIndex();
                    }
                } else {
                    $mensaje = errorMsg(trans('account_inactive'), false);
                }
            } else if (count($user) > 0 && $user->deleted == 1) {
                $mensaje = errorMsg(trans('login_error'), false);
            } else {
                $mensaje = errorMsg(trans('login_error'), false);
            }

            return $mensaje;
        }
    }

    function saveAppAccess($userId) {
        unset($_POST['user_login']);
        unset($_POST['user_pass']);
        $_POST['user'] = $userId;
        $_POST['accessdate'] = date('Y-m-d H:i:s');
        $_POST['ip'] = $_SERVER['REMOTE_ADDR'];
        $_POST['remotehost'] = isset($_SERVER["REMOTE_HOST"]) ?  $_SERVER["REMOTE_HOST"] : gethostbyaddr($_SERVER["REMOTE_ADDR"]);

        $this->wpdb->save($this->appAccessTable, false);
    }

    function checkIsSuperAdmin($roles) {
        $superAdmin = false;
        foreach ($roles as $role) {
            if ($role->getId() == 3) {
                $superAdmin = true;
            }
        }

        return $superAdmin;
    }

    function updateLastLogin($id) {
        $query = 'UPDATE ' . $this->userTable . ' SET last_login="' . date('Y-m-d H:i:s') . '" WHERE id=' . $id;
        $this->wpdb->query($query);
    }

    function set_new_password() {
        $user = $_SESSION['user'];

        $query = 'UPDATE '.$this->userTable . ' SET user_pass="'.md5($_POST['password']) .'" WHERE id='.$user->getId();
        $this->wpdb->query($query);
        confirmationMessage(trans('password_changed'));
    }

    function setUsertypes() {
        if (isset($_POST['usertype']) && $_POST['usertype'] == 0) {  // Usuario de tienda
            $_POST['userstore'] = 1;
            $_POST['userrepository'] = 0;
            $_POST['usermanager'] = 0;
            $_POST['useraccounting'] = 0;
        } else if (isset($_POST['usertype']) && $_POST['usertype'] == 1) {   // Usuario de almacén
            $_POST['userstore'] = 0;
            $_POST['storeid'] = 0;
            $_POST['usermanager'] = 0;
            $_POST['userrepository'] = 1;
            $_POST['useraccounting'] = 0;
        } else if (isset($_POST['usertype']) && $_POST['usertype'] == 2) { // Usuario Jefe
            $_POST['userstore'] = 0;
            $_POST['storeid'] = 0;
            $_POST['userrepository'] = 0;
            $_POST['usermanager'] = 1;
            $_POST['useraccounting'] = 0;
        } else if (isset($_POST['usertype']) && $_POST['usertype'] == 3) { // Usuario contabilidad
            $_POST['userstore'] = 0;
            $_POST['storeid'] = 0;
            $_POST['userrepository'] = 0;
            $_POST['usermanager'] = 0;
            $_POST['useraccounting'] = 1;
        }
    }

    function save_user() {
        $_POST['user_pass'] = md5($_POST['user_pass']);

        $this->setUsertypes();

        unset($_POST['usertype']);
        $this->wpdb->save($this->userTable);
        $this->createUsersJson();
    }

    function save_edit_user() {
        $userId = $_POST['id'];
        // 0 => Tienda, 1 => Almacén, 2 => Jefe

        $this->setUsertypes();

        unset($_POST['usertype']);

        // Si no vamos a cambiar la contraseña
        if (empty($_POST['user_pass'])) {
            unset($_POST['user_pass']);
        } else {
            $_POST['user_pass'] = md5($_POST['user_pass']);
        }

        $user = $this->getUserData($_POST['id']);
        // if vamos a restaurar un usuario eliminado
        if ($user->deleted == 1 && $_POST['deleted'] == 0) {
            getRestorePostData($_POST['id']);
        }

        $this->wpdb->save_edit($this->userTable);
        $this->createUsersJson();
    }

    function createUsersJson() {
        $users = $this->getOnlyIdUsernameUsers();
        $json = json_encode($users);

        file_put_contents(JSON_USERS, $json);
        confirmationMessage('Actualizado archivo de usuarios');
    }

    function getUserRoles($userId) {
        global $config;
        // No usamos una tabla para guardar los roles, si no, un array de configuración (conf/site_parameters)
        /*$query = 'SELECT *, usro.role_id as id FROM ' . $this->userRolesTable . ' usro JOIN ' . $this->rolesTable . ' ro ON ';
        $query .= 'usro.role_id=ro.id AND user_id=' . $userId;*/

        // Si tomamos los permisos del usuario de la tabla user_roles pero es necesario asignar un valor al campo role_name
        $query  = 'SELECT role_id as id FROM ' . $this->userRolesTable;
        $query .= ' WHERE user_id=' . $userId;

        $data = $this->wpdb->get_results($query);

        // Asignamos el valor para role_name
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]->role_name = $config['roles'][$data[$i]->id];
        }

        return persist($data, 'Role');
    }

    function getAllRoles() {
        // Los roles se encuentran en el archivo conf/site_parameters.php
        global $config;
        global $translator;
        $arrayProf = array();

        $lenguages = $translator->languages;
        $selectedLang = $lenguages[$_SESSION['lang']];

        //return persist($this->wpdb->getAll($this->rolesTable), new Role());
        foreach ($config['roles'] as $key => $value) {
            $pro = new stdClass();
            $pro->id = $key;
            $pro->role_name = $value[$selectedLang];
            array_push($arrayProf, $pro);
        }

        return persist($arrayProf, 'Role');
    }

    function updateRoles($user, $roles) {
        $query = 'DELETE FROM ' . $this->userRolesTable . ' WHERE user_id=' . $user;
        $this->wpdb->query($query);

        if ($roles) {
            foreach ($roles as $role) {
                $query = 'INSERT INTO ' . $this->userRolesTable . '(user_id, role_id)';
                $query .= 'VALUES (' . $user . ', ' . $role . ')';

                $this->wpdb->query($query);
            }
        }
    }

    function deleteUserDB() {
        $this->wpdb->deleteRow($this->userTable, "");
    }
	
	function loginAsOtherUser() {
		global $user;
		if (isset($_SESSION['adm'])) {
			$_SESSION['user'] = $_SESSION['adm'];
			unset($_SESSION['adm']);
		} else {
			$us = $this->getUserData($_POST['userid']);
			$_SESSION['adm'] = $user;
			$_SESSION['user'] = $us;
		}
		
		redirectToIndex();
	}
}