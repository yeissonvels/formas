<?php
/**
 * Created by PhpStorm.
 * User: yvelez
 * Date: 14/03/16
 * Time: 10:51
 */

class AdminController {
    /*function show_menus() {
        $menuModel = new MenuModel();
        $menus = $menuModel->getMenus();

        include (VIEWS_PATH_CONTROLLER . 'show_menus.html.php');
    }*/

    function new_menu() {
        $menu = new MenuController();
        $menu->new_menu();
    }

    function new_menu_item() {
        $menu = new MenuController();
        $menu->new_menu_item();
    }

    function save_menu() {
        $menu = new MenuController();
        $menu->save_menu();
    }

    function save_edit_menu() {
        $menu = new MenuController();
        $menu->save_edit_menu();
    }

    function menu_privileges() {
        global $user;
        $permissions = getPermissionsObject();
        $template = VIEWS_PATH_CONTROLLER . 'menu_privileges' . VIEW_EXT;
        $getLabelFunction = getLangGetLabelFunction();
        $controller = new MenuController();
        $items = $controller->getMenuItemsForUserPermission();

        if (file_exists($template)) {
            require_once($template);
        } else {
            errorMsg("No existe la plantilla '" . $template . "'");
        }

    }

    function autoloadGenerate() {
        $salida = shell_exec('composer dump-autoload');
        confirmationMessage("Se ha ejecutado el comando 'composer dump-autoload'");
        echo "<pre>$salida</pre>";
    }

    function truncateTables() {
        /*global $wpdb;

        $tables = array(
            'orders',
            'order_items',
            'order_comments',
            'pdfs',
            'incidences',
            'incidence_comments',
            'incidence_items',
            'incidence_intern_products',
        );

        foreach ($tables as $table) {
            $query = 'TRUNCATE TABLE ' . $wpdb->prefix . $table;
            $wpdb->query($query);
            confirmationMessage('Truncada tabla ' . $table);
        }

        unlink('uploaded-files');
        confirmationMessage('Eliminado el directorio uploaded-files');*/
        confirmationMessage('Función desabilitada');
    }
	
	function show_access() {
		global $wpdb;
		$tpl = VIEWS_PATH_CONTROLLER . "app_access" . VIEW_EXT;
		$query = "SELECT * FROM " . $wpdb->prefix . "app_access ORDER BY id DESC";
		$access = $wpdb->getAll($wpdb->prefix . "app_access", "" , "ORDER BY id DESC");
		loadTemplate($tpl, $access, "", $this);
	}

    function info() {
        phpinfo();
    }

}