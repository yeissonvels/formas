<?php

/**
 * Class OrderController
 * Date: 15/11/2017
 * Time: 21:35:09
 */
class OrderController extends OrderModel {
    protected $urls;
    protected $lastId;
    protected $classTypeName = "Order";

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
                'controller' => CONTROLLER . "&opt=search_order",
                'friendly' => getFriendlyByType('show', $this->classTypeName),
            ),
            'new' => array(
                'controller' => CONTROLLER . "&opt=new_order&id=",
                'friendly' => getFriendlyByType('new', $this->classTypeName),
            ),
            'new_frompdf' => array(
                'controller' => CONTROLLER . "&opt=new_order&pdfid=",
                'friendly' => getFriendlyByType('new_frompdf', $this->classTypeName),
            ),
            'edit_ajax' => array(
                'controller' => CONTROLLER . "order&opt=new_order&id=",
                'friendly' => getFriendlyByType('new', $this->classTypeName),
            ),
            'edit' => array(
                'controller' => CONTROLLER . "&opt=new_order&id=",
                'friendly' => getFriendlyByType('new', $this->classTypeName, true),
            ),
            'delete' => array(
                'controller' => CONTROLLER . "&opt=delete_order&id=",
                'friendly' => getFriendlyByType('delete', $this->classTypeName),
            ),
            'restore_pdf' => array(
                'controller' => CONTROLLER . "&opt=restore_pdf&id=",
                'friendly' => getFriendlyByType('delete', $this->classTypeName),
            ),
            'pdf_yet_printed' => array(
                'controller' => CONTROLLER . "&opt=pdf_yet_printed&id=",
                'friendly' => getFriendlyByType('delete', $this->classTypeName),
            ),
            'view_order' => array(
                'controller' => '?controller=order' . "&opt=view_order&id=",
                'friendly' => getFriendlyByType('delete', $this->classTypeName),
            ),
            'new_incidence' => array(
                'controller' => '?controller=order' . "&opt=new_incidence",
                'friendly' => getFriendlyByType('new_incidence', $this->classTypeName),
            ),
            'edit_incidence' => array(
                'controller' => '?controller=order' . "&opt=new_incidence&id=",
                'friendly' => getFriendlyByType('new_incidence', $this->classTypeName),
            ),
            'show_incidences' => array(
                'controller' => '?controller=order' . "&opt=show_incidences",
                'friendly' => getFriendlyByType('show_incidences', $this->classTypeName),
            ),
        );

        $this->urls = $urls;
    }

    function getUrls() {
        return $this->urls;
    }

    function new_order() {
        $data['data'] = false;
        $orderData = false;
        $comments = array();
        $storeController = new StoreController();

        // Si estamos creando el pedido a partir del código del pdf
        if (isset($_GET['pdfid'])) {
            $data['pdfinfo'] = $this->getPdfInfo();
            $comments = $storeController->getNewPdfComments($_GET['pdfid']);
        }

        // Estamos modificando el pedido
        if (!isset($_GET['pdfid'])) {
            $orderData = $this->getOrderData();
            $comments = $storeController->getNewPdfComments($orderData->getPdfid());
        }

        $data['comments'] = $comments;

        if ($orderData) {
            $data['data'] = $orderData;
            $data['pdfname'] = $this->getPdfName($orderData->getPdfid());
        }

        $controller = new ProductController();
        $products = $controller->getProducts(false);
        $categories = getCategories(false);
        $data['products'] = $products;
        $data['categories'] = $categories;
        $tpl = VIEWS_PATH_CONTROLLER . "new_order" . VIEW_EXT;
        loadTemplate($tpl, $data, '', $this);
    }

    function add_order_items() {
        $data = false;
        $tpl = VIEWS_PATH_CONTROLLER . "add_order_items" . VIEW_EXT;
        loadTemplate($tpl, $data);
    }

    function search_order() {
        $tpl = VIEWS_PATH_CONTROLLER . "search_order" . VIEW_EXT;
        loadTemplate($tpl, null, '', $this);
    }

    function orders_list() {
        $tpl = VIEWS_PATH_CONTROLLER . "orders_list" . VIEW_EXT;
        loadTemplate($tpl, null, '', $this);
    }

    function new_pdfs() {
        $tpl = VIEWS_PATH_CONTROLLER . "new_pdfs" . VIEW_EXT;
        $orders = $this->getNewPdfs();
        loadTemplate($tpl, $orders, '', $this);
    }

    function incomplete_orders() {
        $tpl = VIEWS_PATH_CONTROLLER . 'incomplete_orders' . VIEW_EXT;
        $orders = $this->getIncompleteOrders();

        loadTemplate($tpl, $orders, '', $this);
    }

    function dynamic_order($criteria, $month, $year, $store, $controller) {
        $tpl = VIEWS_PATH . $controller . "/dynamic_order" . VIEW_EXT;
        $data = $this->getOrderByCriteria($criteria, $month, $year, $store);
        loadTemplate($tpl, $data, '', $this);
    }

    function orderReuploaded($id) {
        return $this->wasOrderReuploaded($id);
    }

    function dynamic_orders_list() {
        if (isset($_GET['generatepdf'])) {
            foreach ($_GET as $key => $value) {
                $_POST[$key] = $value;
            }
        }
        $controller = $_POST['controller'];
        $tpl = VIEWS_PATH . $controller . "/dynamic_orders_list" . VIEW_EXT;
        $data = $this->getOrdersList();

        // Devuelve la variable $html
        include($tpl);

        if (!isset($_GET['generatepdf'])) {
            echo $html;
        } else {
            return $html;
        }

    }

    function dynamic_order_by_date($month, $controller) {
        $tpl = VIEWS_PATH . $controller . "/dynamic_order" . VIEW_EXT;
        $data = $this->getOrderByDate($month);
        loadTemplate($tpl, $data, '', $this);
    }

    /**
     * Recogemos el id de la orden
     */
    function save_order($showmsg = true) {
        return $this->save_db_order($showmsg);
    }

    function save_edit_order($showmsg = true) {
        return $this->lastId = $this->save_edit_db_order($showmsg);
    }

    function restore_pdf() {
        getIdFromRequestUri();

        if (isset($_GET['id'])) {
            $this->restorePdf();
        }

        confirmationMessage('Venta devuelta a la tienda!');
        $this->new_pdfs();
    }

    function view_order() {
        $data['data'] = false;
        $data['pdfinfo'] = $this->getPdfInfo();
        $orderData = $this->getOrderData();
        if ($orderData) {
            $data['data'] = $orderData;
        }
        $controller = new ProductController();
        $products = $controller->getProducts(false);
        $data['products'] = $products;
        $tpl = VIEWS_PATH_CONTROLLER . "view_order" . VIEW_EXT;
        loadTemplate($tpl, $data, '', $this);
    }

    function generateDeliveryNote() {
        $tpl = VIEWS_PATH . 'order/deliverynote' . VIEW_EXT;
        $_REQUEST['id'] = $_GET['order'];
        $incidence = $_GET['incidenceid'];
        if ($_GET['order'] > 0) {
            $order = $this->getOrderData();
        }

        $incidence = $this->getIncidenceData($incidence);
        $storecontroller = new StoreController();

        if ($_GET['order'] > 0) {
            $store = $storecontroller->getStoreData($order->getStore());
        } else {
            $store = $storecontroller->getStoreData($incidence->store);
        }

        /*pre($incidence);
        pre($store);*/

        include($tpl);

        return $html;
    }

    function show_incidences() {
        $data = false;
        $tpl = VIEWS_PATH_CONTROLLER . 'show_incidences' . VIEW_EXT;
        //$incidences = $this->getIncidences();
        loadTemplate($tpl, $data, '', $this);
    }

    function generatePdfIncidences() {
        foreach ($_GET as $key => $value) {
            $_POST[$key] = $value;
        }

        $incidences = $this->getAllIncidences();
        $controller = $this;
        $tpl = VIEWS_PATH . 'order/dymanic_incidences' . VIEW_EXT;
        // Retorna la variable $html
        include($tpl);

        return $html;
    }

    /**
     * Incidencias que no pertenecen a un pedido
     */
    function new_incidence() {

        $tpl = VIEWS_PATH . 'order/' . 'new_custom_incidence' . VIEW_EXT;
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $orderController = new OrderController();
            $ajaxincidence = $orderController->getIncidenceData($id);
            if(isset($ajaxincidence->orderid)) {
                $orderData = $this->getOrderDataByID($ajaxincidence->orderid);
                $ajaxincidence->orderData = $orderData;
            }

            $_POST['incidenceid'] = $id;
            $incidenceComments = $orderController->getIncidenceComments();
        } else {
            $orderController = new OrderController();
        }

        $controller = new ProductController();
        $products = $controller->getProducts(false);
        $categories = getCategories(false);
        $ajaxProducts = $products;
        $ajaxCategories = $categories;

        include ($tpl);
    }

    function getCustomerEmails() {
        $data = false;
        $tpl = VIEWS_PATH_CONTROLLER . 'customer_emails' . VIEW_EXT;
        loadTemplate($tpl, $data, '', $this);
    }

    function getCustomerEmailsList() {
        $tpl = VIEWS_PATH . 'order/dynamic_customer_emails' . VIEW_EXT;
        $data = $this->getEmailsByProduct();

        include($tpl);

        echo $html;
    }

    function downloadFile() {
        error_reporting(E_ALL);
        $files = array(
            'cus_emails' => HTTP_CUSTOMER_EMAILS
        );

        $file = $files['cus_emails'];

        /*header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . basename($file));

        readfile ($file);
        //exit();*/

        // Process download
        echo $file;

        if(file_exists($file)) {

            header('Content-Description: File Transfer');

            header('Content-Type: application/octet-stream');

            header('Content-Disposition: attachment; filename="'.basename($file).'"');

            header('Expires: 0');

            header('Cache-Control: must-revalidate');

            header('Pragma: public');

            header('Content-Length: ' . filesize($file));

            flush(); // Flush system output buffer

            readfile($file);

            exit;

        } else {
            echo "No";
        }
    }

    function generalOrderView() {
        $tpl = VIEWS_PATH_CONTROLLER . 'general_order_view' . VIEW_EXT;
        $data = $this->getGeneralOrderView();

        loadTemplate($tpl, $data, '', $this);
    }
}