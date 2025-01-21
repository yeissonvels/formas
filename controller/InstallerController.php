<?php

/**
 * Class InstallerController
 * Date: 06/10/2017
 * Time: 12:28:19
 */
class InstallerController extends InstallerModel {
    function __construct() {
        parent::__construct();
    }

    function show_installers()
    {
        $template = VIEWS_PATH_CONTROLLER . 'show_installers' . VIEW_EXT;
        if (file_exists($template)) {
            $installers = $this->getInstallers();
            include ($template);
        } else {
            errorMsg("No existe la plantilla: " . $template);
        }
    }

    function new_installer() {
        $data = false;
        $template = VIEWS_PATH_CONTROLLER . 'new_installer' . VIEW_EXT;
        if (isset($_GET['id'])) {
            $data = $this->getInstallerData();
        }
        if (file_exists($template)) {
            include ($template);
        } else {
            errorMsg("No existe la plantilla: " . $template);
        }
    }
}