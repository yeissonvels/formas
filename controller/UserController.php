<?php
/**
 * Created by PhpStorm.
 * User: yeisson Velez
 * Date: 2/03/15
 * Time: 11:41
 */

class UserController extends UserModel {
    protected $user;
    protected $urls = array();
    protected $classTypeName = "user";

    function __construct() {
        global $user;
        parent::__construct();
        $this->setUrls();
        $this->user = $user;
    }

    /**
     * Configuramos las urls del controlador
     * Controller: urls por defecto
     * Friendly: urls amigables
     */
    function setUrls() {
        $urls = array(
            'show' => array(
                'controller' => CONTROLLER . "&opt=show_users",
                'friendly' => getFriendlyByType('show', $this->classTypeName),
            ),
            'new' => array(
                'controller' => CONTROLLER . "&opt=new_user",
                'friendly' =>  getFriendlyByType('new', $this->classTypeName),
            ),
            'edit' => array(
                'controller' => CONTROLLER . "&opt=new_user&id=",
                'friendly' =>  getFriendlyByType('new', $this->classTypeName, true),
            ),
            'delete' => array(
                'controller' => CONTROLLER . "&opt=delete_user&id=",
                'friendly' =>  getFriendlyByType('delete', $this->classTypeName),
            )
        );

        $this->urls = $urls;
    }

    function show_users() {
        $user = $this->user;
        $users = $this->getUsers();
        include (VIEWS_PATH_CONTROLLER . 'show_users.html.php');
    }

    function new_user() {
        $data = false;
        // Obtiene el id desde el REQUEST_URI y lo setea en $_GET
        getIdFromRequestUri();
        if (isset($_GET['id'])) {
            if ($_GET['id'] > 1) {
                $data = $this->getUserData();
            } else {
                echo errorMsg(trans('not_privileges'));
                return false;
            }

        }

        $roles = $this->getAllRoles();

        include (VIEWS_PATH_CONTROLLER . 'new_user.html.php');
    }

    function login() {
        $mensaje = $this->getUser();
        // Si la obtención del usuario es exitosa no pasa por la línea siguiente (se hace un header location)
        $this->login_form($mensaje);
    }

    function logout() {
		foreach ($_SESSION as $key => $ses) {
			unset($_SESSION[$key]);
		}

		//setcookie('user','',time()-100);

        echo '<script>';
		//echo 	'document.cookie = "user= ; expires = Thu, 01 Jan 1970 00:00:00 GMT"';
        echo    'location.href="' . HTTP_HOST . '"';
        echo '</script>';
    }

    /**
     * @param string $msg
     *
     * El login no tiene controlador en la url por ello debemos indicar la ruta a la vista de forma manual.
     */
    function login_form($msg = "") {
    	$path = "";
        include ($path . VIEWS_PATH . 'user/login_form.html.php');
    }

    function getSessionDuration() {
        if (isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            return $user->getLastLogin();
        }
        return null;
    }

    function change_pass_form() {
        $msg = '';
        include(VIEWS_PATH_CONTROLLER . 'change_password_form.html.php');
    }

    function delete_user() {
        $this->deleteUserDB();
        $this->show_users();
    }
    
    function setUserMenuPermissions($id) {
        global $user;
        $getLabelFunction = getLangGetLabelFunction();
        $controller = new MenuController();
        $items = $controller->getMenuItemsForUserPermission();
        $permissions = $controller->getUserMenuPermissions($id);

        require_once(VIEWS_PATH_CONTROLLER . 'menu_controllers' . VIEW_EXT);
    }

    function getUsersPdf() {
        $users = $this->getUsers();

        $text = "<table border='0'>";
        $text .=    "<tr>";
        $text .=        "<td>ID</td>";
        $text .=        "<td>Usuario</td>";
        $text .=        "<td>Email</td>";
        $text .=        "<td>Fecha de registro</td>";
        $text .=    "</tr>";

        foreach ($users as $user) {
            $text .=    "<tr>";
            $text .=        "<td>" . $user->getId() . "</td>";
            $text .=        "<td>" . $user->getUserlogin() . "</td>";
            $text .=        "<td>" . $user->getUseremail() . "</td>";
            $text .=        "<td>" . americaDate($user->getUserregistered(), false) . "</td>";
            $text .=    "</tr>";
        }
        $text .= "</table>";
        return $text;
    }
	
	function loginAsOther() {
		$users = json_decode(file_get_contents(JSON_USERS));
		
		if (isset($_POST['user'])) {
			$user = $this->getUserData($_POST['user']);
			$_SESSION['user'] = $user;
			redirectToIndex();
		}
		
		echo '<form action="" method="post">';
		echo '<select name="user" class="form-select">';
		foreach($users as $user) {
			echo '<option value="' . $user->id . '">' . $user->username . '</option>';
		}
		echo '</select>';
		echo '<input type="submit" value="Enviar">';
		echo '</form>';
	}
}