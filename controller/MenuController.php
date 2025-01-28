<?php
/**
 * Created by PhpStorm.
 * User: yvelez
 * Date: 7/03/16
 * Time: 15:40
 */

class MenuController extends MenuModel {
    protected $user;
    protected $manager;
    protected $friendlyUrls;
    protected $friendlyUrlsStatus;
    protected $urls;
    protected $classTypeName = "menu";
    protected $jsonPermissions;

    function __construct() {
        parent::__construct();
        global $user;
        $this->user = $user;
        $this->manager = new UserModel();
        $this->friendlyUrls = getFriendlyUrls();
        $this->friendlyUrlsStatus = friendlyUrlsStatus();
        $this->jsonPermissions = getPermissionsObject();
        $this->setUrls();
    }

    /**
     * Configuramos las urls del controlador
     * Controller: urls por defecto
     * Friendly: urls amigables
     */
    function setUrls() {
        $urls = array(
            'show' => array(
                'controller' => CONTROLLER . "&opt=show_menus",
                'friendly' => getFriendlyByType('show', $this->classTypeName),
            ),
            'new' => array(
                'controller' => CONTROLLER . "&opt=new_menu",
                'friendly' => getFriendlyByType('new', $this->classTypeName),
            ),
            'new_item' => array(
                'controller' => CONTROLLER . "&opt=new_menu_item&id=",
                'friendly' => getFriendlyByType('new_item', $this->classTypeName),
            ),
            'edit' => array(
                'controller' => CONTROLLER . "&opt=new_menu&id=",
                'friendly' => getFriendlyByType('new', $this->classTypeName),
            ),
            'delete' => array(
                'controller' => CONTROLLER . "&opt=delete_menu",
                'friendly' => getFriendlyByType('delete', $this->classTypeName),
            )
        );

        $this->urls = $urls;
    }

    function getUrls() {
        return $this->urls;
    }

    function show_menus() {
        $menus = $this->getMenus();
        $urls = $this->getUrls();

        include (VIEWS_PATH_CONTROLLER . 'show_menus' . VIEW_EXT);
    }

    function createMenu($id = 0) {
        $selectedLang = getLangGetLabelFunction();
        $mainMenu = $this->getActiveMenu($id);
        if ($mainMenu != 0) {
            $menu = '<nav class="navbar navbar-expand-lg bg-body-tertiary">' . PHP_EOL;;
            $menu .=      '<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">' . PHP_EOL;
            $menu .=          '<span class="navbar-toggler-icon"></span>' . PHP_EOL;
            $menu .=      '</button>' . PHP_EOL;
            $menu .=    '<a class="navbar-brand" href="#"></a>' . PHP_EOL;
            $menu .=    '<div class="collapse navbar-collapse" id="navbarSupportedContent">' . PHP_EOL;
            $menu .=        '<ul class="navbar-nav me-auto mb-2 mb-lg-0">' . PHP_EOL;

            foreach($mainMenu as $item) {
                $menu .= $this->createRecursiveMenuOptions($item, $selectedLang);
            }
            $menu .=        '</ul>' . PHP_EOL;
            $menu .=    '</div>' . PHP_EOL;
            $menu .= getCommentsWidget();
            $menu .= '</nav>' . PHP_EOL;

            $_SESSION['menu'] = $menu;
            $this->showSessionMenu();
        }
    }

    function createRecursiveMenuOptions($item, $selectedLang, $ulclass = "dropdown-menu", $liclass = "nav-item dropdown", $aclass = "nav-link") {
        $menu = '';
        $id = $item->getId();
        if(count($item->getChilds()) > 0) {
            if ($this->canUseThisOption($id, $item->getPermision(), $this->user, $this->jsonPermissions) && $item->getActive() > 0) {
                $label = $item->$selectedLang() != '' ? $item->$selectedLang() : $item->getLabel();
                $target = $item->target > 0 ? 'target="_blank"' : '';

                if ($item->show_label == 0) {
                    $label = '';
                }
                // Urls amigables
                if ($this->friendlyUrlsStatus == "ON" && $item->getLinkfriendly() != 0) {
                    foreach ($this->friendlyUrls as $friendly) {
                        if ($friendly->id == $item->getLinkfriendly()) {
                            $link = $friendly->urlfriendly;
                            break;
                        }
                    }
                } else {
                    $link = $item->getLink() ? $item->getLink() : '#';
                }

                $menu .= '<li class="' . $liclass . '">' . PHP_EOL;
                $menu .=    '<a class="nav-link dropdown-toggle" href="' . $link . '" ' . $target . ' role="button" id="dropdown' . $id . '" data-bs-toggle="dropdown" aria-expanded="false">' . $label . PHP_EOL;

                if($item->fontawesomeicon) {
                    $menu .= menuIcon($item->fontawesomeicon);
                } else if ($item->icon != "") {
                    $menu .= '<img src="' . UPLOADED_MENU_ICONS_PATH . 'menu' . $item->menuid . '/' . $item->icon . '" class="vert-align-middle">' . PHP_EOL; 
                }
                $menu .=    '</a>' . PHP_EOL;
                $menu .= '<ul class="' . $ulclass . '">' . PHP_EOL;
                if (count($item->getChilds()) > 0){
                    foreach ($item->getChilds() as $subLev) {
                        if ($this->canUseThisOption($subLev->getId(), $subLev->getPermision(), $this->user, $this->jsonPermissions) && $subLev->getActive() > 0) {
                            $menu .= $this->createRecursiveMenuOptions($subLev, $selectedLang, "dropdown-menu", " ", "dropdown-item");
                        }
                    }
                }
                $menu .= '</ul>' . PHP_EOL;
                $menu .= '</li>' . PHP_EOL;
            }
        } else {
            if ($this->canUseThisOption($item->getId(), $item->getPermision(), $this->user, $this->jsonPermissions) && $item->getActive() > 0) {
                $label = $item->$selectedLang() != '' ? $item->$selectedLang() : $item->getLabel();
                $target = $item->target > 0 ? 'target="_blank"' : '';

                if ($item->show_label == 0) {
                    $label = '';
                }
                // Urls amigables
                if ($this->friendlyUrlsStatus == "ON" && $item->getLinkfriendly() != 0) {
                    foreach ($this->friendlyUrls as $friendly) {
                        if ($friendly->id == $item->getLinkfriendly()) {
                            $link = $friendly->urlfriendly;
                            break;
                        }
                    }
                } else {
                    $link = $item->getLink() ? $item->getLink() : '#';
                }

                $menu .=  '<li class="' . $liclass . '">' . PHP_EOL;
                $menu .=        '<a class="' . $aclass . '" href="' . $link . '" ' . $target . '>' . $label . PHP_EOL;

                if($item->fontawesomeicon) {
                    $menu .= menuIcon($item->fontawesomeicon);
                } else if ($item->icon != "") {                    
                    $menu .= '<img src="' . UPLOADED_MENU_ICONS_PATH . 'menu' . $item->menuid . '/' . $item->icon . '" class="vert-align-middle">' . PHP_EOL;
                }
                $menu .=    '</a>' . PHP_EOL;
                $menu .=  '</li>' . PHP_EOL;
            }
        }

        return $menu;
    }

    function showSessionMenu() {
        echo $_SESSION['menu'];
    }

    function new_menu() {
        $data = false;
        getIdFromRequestUri();
        if (isset($_GET['id'])) {
            $data = $this->getMenuData();
        }

        $roles = $this->manager->getAllRoles();

        include (VIEWS_PATH_CONTROLLER . 'new_menu' . VIEW_EXT);
    }

    function new_menu_item() {
        getIdFromRequestUri();
        $roles = $this->manager->getAllRoles();
        $parents = $this->getItemParents();
        $friendlyUrls = getFriendlyUrls();
        $totalItems = $this->getTotalItemsMenu() + 1;

        include (VIEWS_PATH_CONTROLLER . 'new_menu_item' . VIEW_EXT);
    }

    function getMenuItemsForUserPermission() {
        return $this->getMenuItems($this->getActiveMenuId());
    }

    /**
     * Guarda un menú
     *
     */
    function save_menu() {
        $this->save_menu_data();
    ?>
        <script>
            $(document).ready(function() {
                //alert('Cargada!');
                updateMenu();
            });

        </script>
    <?php
    }

    /**
     * Actualiza un menú
     *
     */
    function save_edit_menu() {
        $this->save_edit_menu_data();
    ?>
        <script>
            $(document).ready(function() {
                //alert('Cargada!');
                updateMenu();
            });
        </script>
    <?php
    }

    function updateDinamicMenu() {
        // Al ser una llamada Ajax no se tiene acceso a  las constantes
        $menuItems = $this->getMenuItems($_GET['id']);
        require_once (VIEWS_PATH . $_GET['controller'] . '/' . 'menu_items' . VIEW_EXT);
    }

    function menu_privileges() {
        global $user;
        $permissions = getPermissionsObject();
        $template = VIEWS_PATH_CONTROLLER . 'menu_privileges' . VIEW_EXT;
        $items = $this->getMenuItemsForUserPermission();

        $data["permissions"] = $permissions;
        $data["items"] = $items;

        loadTemplate($template, $data);

    }
}