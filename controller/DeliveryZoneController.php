<?php

/**
 * Created by PhpStorm.
 * User: yvelezs
 * Date: 11/12/2017
 * Time: 14:35
 */
class DeliveryZoneController extends DeliveryZoneModel
{
    protected $urls;
    protected $classTypeName = "DeliveryZone";

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
                'controller' => CONTROLLER . "&opt=show_delivery_zones",
                'friendly' => getFriendlyByType('show', $this->classTypeName),
            ),
            'new' => array(
                'controller' => CONTROLLER . "&opt=new_delivery_zone",
                'friendly' => getFriendlyByType('new', $this->classTypeName),
            ),
            'edit' => array(
                'controller' => CONTROLLER . "&opt=new_delivery_zone&id=",
                'friendly' => getFriendlyByType('new', $this->classTypeName, true),
            ),

        );

        $this->urls = $urls;
    }

    function getUrls() {
        return $this->urls;
    }

    function show_delivery_zones() {
        $tpl = VIEWS_PATH_CONTROLLER . "show_zones" . VIEW_EXT;
        $data = $this->getDeliveryZones();

        loadTemplate($tpl, $data, '', $this);
    }

    function new_delivery_zone() {
        $data = false;
        $tpl = VIEWS_PATH_CONTROLLER . "new_delivery_zone" . VIEW_EXT;

        if (isset($_GET['id'])) {
            $data = $this->getDeliveryZoneData();
        }

        loadTemplate($tpl, $data, '', $this);
    }
}