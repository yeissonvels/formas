<?php
/**
 * Created by PhpStorm.
 * User: yvelez
 * Date: 14/03/16
 * Time: 10:04
 */

class MenuModel extends Menu {
    protected $menuTable;
    protected $menuItemsTable;
    protected $menuItemPermTable;
    protected $userPermissions;
    protected $wpdb;

    function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->menuTable = $wpdb->prefix . 'menus';
        $this->menuItemsTable = $wpdb->prefix . 'menu_items';
        $this->menuItemPermTable = $wpdb->prefix . 'menu_item_permissions';
        $this->setUserPermissions();
    }

    function getUserPerm() {
        return $this->userPermissions;
    }

    function setUserPermissions() {
        global $user;
        $this->userPermissions = $this->getUserMenuPermissions($user->getId());
    }

    function getMenuData() {
        $data = $this->wpdb->getOneRow($this->menuTable, $_GET['id']);
        $menu = persist($data, 'Menu');

        return $menu;
    }

    function getMenuItem() {
        $data = $this->wpdb->getOneRow($this->wpdb->prefix . 'menu_items', $_GET['id']);
        return $data;
    }

    function getNewMenuData($id) {
        $data = $this->wpdb->getAll($this->wpdb->prefix . 'menu_items', 'WHERE menuid=' . $id);

        return $data;
    }

    function save_menu_data() {
        // Desactivamos los otros menús (ya que sólo puede estar uno activo)
        if ($_POST['active'] == 1) {
            $query = 'UPDATE ' . $this->menuTable . ' SET active=0';
            $this->wpdb->query($query);
        }
        $this->wpdb->save($this->menuTable);
    }

    function save_edit_menu_data() {
        // Desactivamos los otros menús (ya que sólo puede estar uno activo)
        if ($_POST['active'] == 1) {
            $query = 'UPDATE ' . $this->menuTable . ' SET active=0';
            $this->wpdb->query($query);
        }
        $this->wpdb->save_edit($this->menuTable);
    }

    function buildOneRowPostData() {
        $copy = $_POST;
        unset($_POST);

        foreach($copy as $key => $value) {
            if (is_array($value)) {
                $_POST[$key] = $value[0];
            } else {
                $_POST[$key] = $value;
            }
        }

    }

    function getTotalItemsMenu() {
        $query = "SELECT count(*) as total FROM " . $this->menuItemsTable . " WHERE menuid=" . $_GET['id'];
        $result = $this->wpdb->get_results($query);

        return $result[0]->total;
    }

    function updateMenuIconInDB($menuElement, $iconName) {
        $query = 'UPDATE ' . $this->menuTable . ' SET icon="' . $iconName . '" WHERE id=' . $menuElement;
        $this->wpdb->query($query);
    }

    function updateMenuIconInDB2($menuElement, $iconName) {
        $query = 'UPDATE ' . $this->menuItemsTable . ' SET icon="' . $iconName . '" WHERE id=' . $menuElement;
        $this->wpdb->query($query);
    }

    function getMenus() {
        $menus = $this->wpdb->getAll($this->wpdb->prefix . 'menus');
        $menus = persist($menus, 'Menu');

        return $menus;
    }

    /**
     * @param $id
     * @return array
     *
     * Función para listar los items del menú dentro de la sección administrativa
     */
    function getMenuItems($id) {

        $menuItems = $this->wpdb->getAll($this->menuItemsTable, 'WHERE parent=0 AND menuid=' . $id, ' ORDER BY position ASC');
        $menuItems = persist($menuItems, 'MenuItem');

        foreach($menuItems as $item) {
            $childs = $this->wpdb->getAll($this->menuItemsTable, 'WHERE parent > 0 AND menuid=' . $id . ' AND parent=' . $item->getId(), ' ORDER BY position ASC');
            $item->setChilds(persist($childs, 'MenuItem'));

            // Creamos mas niveles de forma recursiva
            $this->recursiveChilds($item->getChilds(), $id);
        }

        return $menuItems;
    }


    /**
     * @param $id
     * @return array
     *
     * Función para obtener el menú que está activo
     */
    function getActiveMenu($id) {
  		if ($id == 0) {
  			$activeMenu = $this->wpdb->get_var('SELECT id FROM ' . $this->menuTable . ' WHERE active=1');
  			if (!$activeMenu) {
                $_SESSION["incompletemenu"] = true;
                errorMsg("No existe ningún menu por favor cree uno. ");
                if (isadmin() || isSuperAdmin()) {
                    confirmationMessage('<a href="?controller=menu&opt=new_menu">Crear menú</a>');
                }
            }
  		} else {
  			$activeMenu = $id;
  		}

  		if ($activeMenu != "") {
            $_SESSION['menuId'] = $activeMenu;

            $menuItems = $this->wpdb->getAll($this->menuItemsTable, 'WHERE menuid=' . $activeMenu . ' AND parent=0', ' ORDER by position ASC');
            $menuItems = persist($menuItems, 'MenuItem');

            foreach($menuItems as $item) {
                $childs = $this->wpdb->getAll($this->menuItemsTable,
                    'WHERE parent > 0 AND menuid=' . $activeMenu . ' AND parent=' . $item->getId(), ' ORDER BY position ASC');
                $item->setChilds(persist($childs, 'MenuItem'));

                // Creamos mas niveles de forma recursiva
                $this->recursiveChilds($item->getChilds(), $activeMenu);
            }

            if (count($menuItems) == 0) {
                errorMsg("No existen items en el menú. ");
                if (isadmin() || isSuperAdmin()) {
                    confirmationMessage('<a href="?controller=admin&opt=new_menu_item&id=' . $activeMenu . '">Crear items</a>');
                }
            } else {
                unset($_SESSION["incompletemenu"]);
            }

            return $menuItems;
        }
    }

    /**
     * @return mixed|null|string
     *
     * Retorna el id del menu activo
     */
    function getActiveMenuId() {
        return $this->wpdb->get_var('SELECT id FROM ' . $this->menuTable . ' WHERE active=1');
    }

    /**
     * @param $item
     *
     * Función que permite crear niveles de menú de forma recursiva
     */
    function recursiveChilds($item, $id) {
        if (count($item) > 0) {
            foreach ($item as $niv) {
                $subchilds = $this->wpdb->getAll($this->menuItemsTable, 'WHERE parent > 0 AND menuid=' . $id . ' AND parent=' . $niv->getId() . " ORDER BY position ASC;");
                $niv->setChilds(persist($subchilds, 'MenuItem'));
                $this->recursiveChilds($niv->getChilds(), $id);
            }
        }
    }

    function saveMenuItem() {
        $this->wpdb->save($this->menuItemsTable);
    }

    function editMenuItem() {
        unset($_POST['edit']);
        $this->wpdb->save_edit($this->menuItemsTable);
    }

    function getItemParents() {
        $query = 'SELECT id, label, position from ' . $this->menuItemsTable . ' WHERE menuid= ' . $_GET['id']
            . ' ORDER BY label ASC';
        return $this->wpdb->get_results($query);
    }

    function deleteMenuItem() {
        $id = $_GET['id'];
        $query = 'DELETE FROM ' . $this->menuItemsTable . ' WHERE id=' . $id . ' OR parent=' . $id;
        $this->wpdb->query($query);
    }

    function deleteUserMenuPermission($userid) {
        // Borramos los permisos anteriores
        $query = 'DELETE FROM ' . $this->menuItemPermTable . ' WHERE user_id=' . $userid;
        $this->wpdb->query($query);

    }

    function getUserMenuPermissions($userid) {
        $query = 'SELECT item_id FROM ' . $this->menuItemPermTable .  ' WHERE user_id=' . $userid;
        $data = $this->wpdb->get_results($query);
        $result = array();
        foreach ($data as $d) {
            $result[] = $d->item_id;
        }

        return $result;
    }

    /**
     * @param $id
     * @param $permission
     * @return bool
     *
     * Se usa para las opciones del menú
     */
    function canUseThisOption($id, $permission, $user, $json)
    {
        $can = false;
        // Si se ha dado permisos a un usuario en cuestión
        $permissions = $this->getUserPerm();
        // Permisos globales desde menu -> permisos
        $permissionsFromJson = $json;

        if ($permission == 0 || isSuperAdmin()) {
            $can = true;
        } else if (isset($permissionsFromJson->$id)){
            foreach ($permissionsFromJson->$id as $method) {
                $method = "get" . ucfirst($method);
                if (method_exists($user, $method)) {
                    if ($user->$method() == 1) {
                        $can = true;
                    }
                }
            }
        } else {
            if (in_array($id, $permissions)) {
                $can = true;
            }
        }

        return $can;
    }

    /**
     * @param $url
     * @return bool
     *
     * Se usa desde app para confirmar si es posible usar el controlador
     */
    function canIUseTheController($url) {
        global $user;
        $can = false;
        $permissions = getPermissionsObject();

        $query = 'SELECT id, permision FROM ' . $this->wpdb->prefix . 'menu_items WHERE link="' . $url . '" ';
        $data = $this->wpdb->get_row($query);

        if ($data) {
            $id = $data->id;
        } else {
            $id = -1;
        }


        if (($data != "" && $data->permision == 0) || isSuperAdmin()) {
            $can = true;
        } else if (isset($permissions->$id)) {
            foreach ($permissions->$id as $method) {
                $method = 'get' . ucfirst($method);
                if (method_exists($user, $method)) {
                    if ($user->$method() == 1) {
                       $can = true;
                    }
                }
            }

        } else {
            $query = 'SELECT * FROM ' . $this->menuItemsTable . ' it, ' . $this->menuItemPermTable . ' pe ';
            $query .= ' WHERE link="' . $url . '" AND it.id=item_id AND user_id=' . $user->getId();
            $data = $this->wpdb->get_results($query);

            if ($data) {
                $can = true;
            }
        }
		
        return $can;
    }

    function saveItemMenuPermission() {
        $this->wpdb->save($this->menuItemPermTable);
    }
}