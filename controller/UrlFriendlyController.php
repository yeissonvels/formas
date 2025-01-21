<?php

/**
 * Created by PhpStorm.
 * User: yvelezs
 * Date: 03/11/2017
 * Time: 12:43
 */
class UrlFriendlyController extends UrlFriendlyModel
{
    protected $urls;
    protected $classTypeName = "urlFriendly";

    function __construct()
    {
        parent::__construct();
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
                'controller' => CONTROLLER . "&opt=show_urls",
                'friendly' => getFriendlyByType('show', $this->classTypeName),
            ),
            'new' => array(
                'controller' => CONTROLLER . "&opt=new_friendly",
                'friendly' => getFriendlyByType('new', $this->classTypeName),
            ),
            'edit' => array(
                'controller' => CONTROLLER . "&opt=new_friendly&id=",
                'friendly' => getFriendlyByType('new', $this->classTypeName, true),
            ),
            'delete' => array(
                'controller' => CONTROLLER . "&opt=delete_friendly&id=",
                'friendly' => getFriendlyByType('delete', $this->classTypeName),
            )
        );

        $this->urls = $urls;
    }

    function getUrls() {
        return $this->urls;
    }

    /**
     * Nueva url amigable
     */
    function new_friendly() {
        $data = false;
        getIdFromRequestUri();
        $controllers = $this->getControllers();
        if (isset($_GET['id'])) {
            $data = $this->getUrlData();
        }

        $friendly["data"] = $data;
        $friendly["controllers"] = $controllers;

        $tpl = VIEWS_PATH_CONTROLLER . "new_friendly" . VIEW_EXT;

        loadTemplate($tpl, $friendly, '', $this);

    }

    /**
     * Formulario para activar o desactivar las urls amigables
     */

    function toggleFriendlyUrls($msg = "") {
        $data = false;
        if (file_exists(CONFIG_FRIENDLY_URLS)) {
            $data = json_decode(file_get_contents(CONFIG_FRIENDLY_URLS));
        }

        $tpl = VIEWS_PATH_CONTROLLER . "config_friendly_urls" . VIEW_EXT;
        loadTemplate($tpl, $data, $msg);
    }

    /**
     * Activa y desactiva las urls amigables globalmente.
     *
     */
    function set_friendly_urls_config() {
        if (isset($_POST['status'])) {
            if ($_POST['status'] == "ON") {
                unsetMenu();
                $data = array("status" => $_POST['status']);
                $data = json_encode($data);
                file_put_contents(CONFIG_FRIENDLY_URLS, $data);
                $this->createHtaccess($_POST['status']);
                //$this->toggleFriendlyUrls(confirmationMessage("Urls amigables activadas!"));
                redirectToIndex("Urls amigables activadas!");
            } else {
                unsetMenu();
                $data = array("status" => $_POST['status']);
                $data = json_encode($data);
                file_put_contents(CONFIG_FRIENDLY_URLS, $data);
                $this->createHtaccess($_POST['status']);
                //$this->toggleFriendlyUrls(confirmationMessage("Urls amigables desactivadas!"));
                redirectToIndex("Urls amigables desactivadas!");
            }
        }
    }

    /**
     * @param $mod
     * Crea el fichero .htaccess
     */
    function createHtaccess($mod) {
        $content  = "RewriteEngine on" . PHP_EOL;
        $content .= "RewriteCond %{REQUEST_FILENAME} !-f" . PHP_EOL;
        $content .= "RewriteCond %{REQUEST_FILENAME} !-d" . PHP_EOL;
        $content .= "RewriteRule ^(.*)$ /index.php [NC,L,QSA]" . PHP_EOL;

        if ($mod == "ON") {
            file_put_contents(".htaccess", $content);
        } else {
            file_put_contents(".htaccess", "");
        }

    }

    function show_urls() {
        $urls = $this->get_urls();
        $tpl = VIEWS_PATH_CONTROLLER . "show_urls" . VIEW_EXT;
        $data['dataurls'] = $urls;
        $data['urls'] = $this->urls;

        loadTemplate($tpl, $data);
    }

    function save_url() {
        $this->saveUrl();
        $this->update_urls_config();
        unsetMenu();
        $this->show_urls();
    }

    function save_edit_url() {
        $this->saveEditUrl();
        $this->update_urls_config();
        unsetMenu();
        $this->show_urls();
    }

    /**
     * Actualiza el fichero de urls amigables
     */
    function update_urls_config() {
        $urls = $this->get_urls(false);
        $urls = json_encode($urls);
        file_put_contents(FRIENDLY_URLS, $urls);
    }

    function getClassMethods($class) {
        $methods = get_class_methods($class);
        asort($methods);

        return $methods;
    }

    function getControllers() {
        $auxControllers = getFilesfromDirectory("controller");
        $controllers = array();
        foreach ($auxControllers as $controller) {
            if ($controller != "index.php") {
                $controllers[] = lcfirst(str_replace("Controller.php", "", $controller));
            }
        }

        asort($controllers);
        return $controllers;
    }
}