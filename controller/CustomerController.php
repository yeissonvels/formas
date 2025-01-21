<?php
/**
 * Class CustomerController
 * Date: 05/10/2017
 * Time: 09:37:26
 */
class CustomerController extends CustomerModel {
    protected $urls;
    protected $classTypeName = "customer";

    function __construct() {
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
                'controller' => CONTROLLER . "&opt=show_customers",
                'friendly' => getFriendlyByType('show', $this->classTypeName),
            ),
            'new' => array(
                'controller' => CONTROLLER . "&opt=new_customer",
                'friendly' =>  getFriendlyByType('new', $this->classTypeName),
            ),
            'edit' => array(
                'controller' => CONTROLLER . "&opt=new_customer&id=",
                'friendly' =>  getFriendlyByType('new', $this->classTypeName, true),
            ),
            'delete' => array(
                'controller' => CONTROLLER . "&opt=delete_customer",
                'friendly' =>  getFriendlyByType('delete', $this->classTypeName),
            )
        );

        $this->urls = $urls;
    }

    function getUrls() {
        return $this->urls;
    }

    function show_customers() {
        global $user;
        $template = VIEWS_PATH_CONTROLLER . 'show_customers' . VIEW_EXT;

        /*if (file_exists($template)) {
            $customers = $this->getCustomers();
            include ($template);
        } else {
            errorMsg("No existe la plantilla: " . $template);
        }*/
        $data = $this->getCustomers();
        loadTemplate($template, $data, '', $this);
    }

    function dynamic_customers($criteria, $controller) {
        $tpl = VIEWS_PATH . $controller . "/dynamic_customers" . VIEW_EXT;
        $data = $this->getCustomersByCriteria($criteria);
        loadTemplate($tpl, $data, '', $this);
    }

    function new_customer() {
        $data = false;
        // Obtiene el id desde el REQUEST_URI y lo setea en $_GET
        getIdFromRequestUri();
        if (isset($_GET['id'])) {
            $data = $this->getCustomerData();
        }

        $template = VIEWS_PATH_CONTROLLER . 'new_customer' . VIEW_EXT;
        loadTemplate($template, $data, '', $this);
    }
}