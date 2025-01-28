<?php
$server_name = $_SERVER['SERVER_NAME'];
$httpHost = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $server_name;
$sever_path = explode('/',$_SERVER['PHP_SELF']);
$global_directory = isset($sever_path[1]) ? $sever_path[1] : "";
$localhost_complement = isset($sever_path[2]) ? $sever_path[2] : "";

define('LOCALHOST', $_SERVER["SERVER_ADDR"] == "127.0.0.1" ||  $_SERVER["REMOTE_ADDR"] == "192.168.0.157" || $_SERVER["REMOTE_ADDR"] == "192.168.0.154" ? true : false);
define('HTTP_HOST', $httpHost);
define('ABSOLUTE_PATH', __DIR__ . '/');
define('GLOBAL_DIRECTORY', $global_directory);
define('CONFIG_PATH', ABSOLUTE_PATH . 'conf/');
define('JSON_PATH', CONFIG_PATH . 'json/');
define('CLASSES_PATH', ABSOLUTE_PATH . 'classes/');
define('CONTROLLERS_PATH', ABSOLUTE_PATH . 'controller/');
define('MODELS_PATH', ABSOLUTE_PATH . 'model/');
define('VIEWS_PATH', ABSOLUTE_PATH . 'views/');
define('VIEWS_PATH_COMMON', VIEWS_PATH . 'common/');
// Determina si están activas las friendly urls
define('CONFIG_FRIENDLY_URLS', JSON_PATH . "config_friendly_urls.json");
define('FRIENDLY_URLS', JSON_PATH . "friendly_urls.json");
define('JSON_PROVINCES', JSON_PATH . "provinces.json");
define('JSON_STORES', JSON_PATH . "stores.json");
define('JSON_ZONES', JSON_PATH . "zones.json");
define('JSON_CATEGORIES', JSON_PATH . "categories.json");
define('JSON_USERS', JSON_PATH . "users.json");
define('JSON_FINISHES', JSON_PATH . "finishes.json");
define('IVA', 21);

// IMPORTANTE: para que funcionen las constantes por AJAX y donde se usa $_GET (por ejemplo en el botón editar),
// es necesario pasar la variable controller como parámetro, de lo contrario no funciona (no existiría esa variable).
// $.ajax({
//      url: 'miurl',
//      controller: <?php CONSTANTE

$getController = isset($_GET['controller']) ? $_GET['controller'] : '';

// Manejo de friendly urls
if($getController == "") {
    include("classes/App.php");
    $friendly = App::matchFriendly();
    if ($friendly != "") {
        $getController = $friendly->controllername;
    }
}

define('VIEWS_PATH_CONTROLLER', VIEWS_PATH . strtolower($getController) . '/');
define('VIEW_EXT', ".html.php");
define('CONTROLLER', '?controller=' . $getController);
define('FORM_CONTROLLER', $getController);
define('IMAGES_DIR', ABSOLUTE_PATH . 'images/');
define('IMAGES_PATH', HTTP_HOST . '/images/');
define('ICONS_DIR', IMAGES_DIR . 'icons/');
define('ICONS_PATH', IMAGES_PATH . 'icons/');
define('FILES_PATH', ABSOLUTE_PATH . 'files');
define('HTTP_FILES', HTTP_HOST . '/files/');
define('TXT_CUSTOMERS', 'correos_clientes.doc');
if (!is_dir(FILES_PATH)) {
    mkdir(FILES_PATH);
}


define('GENERATED_FILES_PATH', FILES_PATH . '/generated-files/');
define('HTTP_GENERATED_FILES', HTTP_FILES . 'generated-files/');
define('CUSTOMER_EMAILS', GENERATED_FILES_PATH . TXT_CUSTOMERS);
define('HTTP_CUSTOMER_EMAILS', HTTP_GENERATED_FILES . TXT_CUSTOMERS);
define('PDF_DIR', '/uploaded-files/pdfs/');
define('DELIVERY_FILES_DIR', '/uploaded-files/lastdeliveryfiles/');
define('UPLOADES_IMG_DIR', '/uploaded-files/images/');
if (!is_dir(GENERATED_FILES_PATH)) {
    mkdir(GENERATED_FILES_PATH);
}


define('UPLOADED_MENU_ICONS', ICONS_DIR . 'menu_icons/');
define('UPLOADED_MENU_ICONS_PATH', ICONS_PATH . 'menu_icons/');
define('LOGO_PDF', 'logo-formas-small.png');
if (!is_dir(UPLOADED_MENU_ICONS)) {
    mkdir(UPLOADED_MENU_ICONS);
}

define('PERMISSIONS_FILE', JSON_PATH . "permissions.json");

// URLS

define('LOGOUT_URL', '?controller=user&opt=logout');
define('CSS_VERSION', '1.0.1');

?>