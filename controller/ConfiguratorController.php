<?php

class ConfiguratorController extends ConfiguratorModel {
    protected $classTypeName = "configurator";
    protected $urls = array();

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
            'mailer_configurator' => array(
                'controller' => CONTROLLER . "&opt=mailer_configurator",
                'friendly' => getFriendlyByType('show', $this->classTypeName),
            )
        );

        $this->urls = $urls;
    }

    function mailer_configurator() {
        $data = $this->getMailerConfig(1);
        include(VIEWS_PATH_CONTROLLER . "mailer_configurator" . VIEW_EXT);
    }
}