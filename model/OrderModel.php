<?php

/**
 * Class OrderModel
 * Date: 15/11/2017
 * Time: 21:35:09
 */
class OrderModel
{
    protected $zonesTable;
    protected $ordersTable;
    protected $storesTable;
    protected $orderItemsTable;
    protected $categoriesTable;
    protected $commentsTable;
    protected $incidencesTable;
    protected $incidenceCommentsTable;
    protected $incidenceItemsTable; // almacenamos los items para la nueva orden de entrega generada por la incidencia.
    protected $incidenceInternProductsTable; // almacenamos los productos que se deben fabricar
    protected $pdfsTable;
    protected $usersTable;
    protected $wpdb;

    function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->zonesTable = $wpdb->prefix . "delivery_zones";
        $this->ordersTable = $wpdb->prefix . "orders";
        $this->categoriesTable = $wpdb->prefix . "article_categories";
        $this->storesTable = $wpdb->prefix . "stores";
        $this->orderItemsTable = $wpdb->prefix . "order_items";
        $this->commentsTable = $wpdb->prefix . "order_comments";
        $this->incidencesTable = $wpdb->prefix . "incidences";
        $this->incidenceCommentsTable = $wpdb->prefix . "incidence_comments";
        $this->incidenceItemsTable = $wpdb->prefix . "incidence_items";
        $this->incidenceInternProductsTable = $wpdb->prefix . "incidence_intern_products";
        $this->pdfsTable = $wpdb->prefix . "pdfs";
        $this->usersTable = $wpdb->prefix . "users";
    }

    function getOrderByCriteria($criteria, $month, $year, $store)
    {
        $fields = "o.id as id, pdfid, code, customer, o.telephone, o.telephone2, o.email, deliveryzone, purchasedate, ";
        $fields .= "deliveryrange, deliverymonth, deliverydate, total, pendingpay, paymethod, status, store, storename, ";
        $fields .= "createdby, cancelled";
        $and = "";
        $filterstore = "";

        $filterdate = getFilterDate($month, $year, true, 'o', 'createdon');


        if ($store != "") {
            $filterstore = " AND store = " . $store;
        }

        $andfilter = $filterdate . $filterstore;

        if ($criteria != "") {
            $and = " AND (code = \"" . $criteria . "\" OR customer LIKE '%" . $criteria . "%' OR o.telephone = \"" . $criteria . "\")";
        }

        $query = "SELECT " . $fields . " FROM " . $this->ordersTable . " o, " . $this->storesTable . " s ";
        $query .= " WHERE o.store  = s.id $and " . $andfilter . " ORDER BY code ASC;";

        //echo $query;

        $orders = $this->wpdb->get_results($query);
        $orders = persist($orders, 'Order');

        return $orders;
    }

    function getIncompleteOrders()
    {
        $fields = "o.id as id, pdfid, code, customer, o.telephone, o.telephone2, o.email, deliveryzone, purchasedate, ";
        $fields .= "deliveryrange, deliverymonth, deliverydate, total, pendingpay, paymethod, status, store, storename, ";
        $fields .= "createdby, cancelled, totalitems, saveditems";

        $query = "SELECT " . $fields . " FROM " . $this->ordersTable . " o, " . $this->storesTable . " s ";
        $query .= " WHERE status < 2 AND (totalitems - saveditems) <> 0 AND o.store  = s.id ORDER BY code ASC;";

        //echo $query;

        $orders = $this->wpdb->get_results($query);
        $orders = persist($orders, 'Order');

        return $orders;
    }

    function wasOrderReuploaded($id) {
        $query = 'SELECT reuploaded, returned_on FROM ' . $this->pdfsTable . ' WHERE id = ' . $id;
        return $this->wpdb->get_results($query);
    }

    function getOrdersList()
    {
        $deliveryrange = $_POST['deliveryrange'];
        $status = $_POST['status'];
        $month = $_POST['month'];
        $year = $_POST['year'];
        $store = $_POST['store'];
        $deliveryzone = $_POST['deliveryzone'];
        $filterRange = "";

        $fields = "o.id as id, pdfid, pdfname, o.code, o.customer, o.telephone, o.telephone2, o.email, deliveryzone, purchasedate, ";
        $fields .= "deliveryrange, deliverymonth, deliverydate, o.total, pendingpay, pendingstatus, o.paymethod, o.status, store, ";
        $fields .= "storename, createdby, o.cancelled";
        if ($deliveryrange != "") {
            $filterRange = " AND deliveryrange = " . $deliveryrange;
        }

        $filterstore = "";
        $filterstatus = "";
        $filterdeliveryzone = "";


        $filterdate = getFilterDate(0, $year, true, 'o', 'createdon');

        // Todo el año
        if ($month == "all") {
            $filterDeliveryMonth = "";
        } else if ($month > 0) {
            $filterDeliveryMonth = " AND deliverymonth = " . $month;
        } else {
            $filterDeliveryMonth = " AND deliverymonth = " . date('m');
        }


        if ($store != "") {
            $filterstore = " AND store = " . $store;
        }

        if ($status != "") {
            if ($status == 2) {
                $filterstatus = " AND (o.status = 2  OR o.status = 3) ";
                $filterRange = "";
                $filterDeliveryMonth = "";
                $filterdate = getFilterDate($month, $year, true, 'o', 'deliverydate');
            } else {
                $filterstatus = " AND o.status = " . $status;
            }

        }

        if ($deliveryzone != "") {
            $filterdeliveryzone = " AND deliveryzone = " . $deliveryzone;
        }


        $filters = $filterRange . $filterstore . $filterstatus . $filterdeliveryzone . $filterdate . $filterDeliveryMonth;
        $pdfparameters = '&deliveryrange=' . $deliveryrange . '&status=' . $status . '&month=' . $month;
        $pdfparameters .= '&year=' . $year . '&store=' . $store . '&deliveryzone=' . $deliveryzone;


        $query = "SELECT " . $fields . " FROM " . $this->ordersTable . " o, " . $this->storesTable . " s, ";
        $query .= $this->pdfsTable . " p WHERE o.pdfid = p.id AND o.store  = s.id " . $filters;
        $query .= " ORDER BY code, deliveryzone, purchasedate ASC;";

        //echo $query;

        $orders = $this->wpdb->get_results($query);
        $orders = persist($orders, 'Order');

        // Guardamos los parámetros para la generación del PDF
        if($orders) {
            $orders[0]->setPdfparameters($pdfparameters);
        }


        return $orders;
    }

    /**
     * @param $pdfid
     * @return array
     *
     * Retorna el código de un pedido por pdfid. Lo usamos desde Ajax.
     */
    function getOrderByPdfId($pdfid)
    {
        $query = "SELECT code FROM " . $this->ordersTable . " WHERE pdfid = " . $pdfid;
        return $this->wpdb->get_results($query);
    }

    /**
     * @param $pdfid
     * @return array
     *
     * Retorna el ID de un pedido por pdfid.
     */
    function getOrderIdByPdfId($pdfid) {
        $query = "SELECT id FROM " . $this->ordersTable . " WHERE pdfid = " . $pdfid;
        return $this->wpdb->get_results($query);
    }

    /**
     * @param $code
     * @param $pdfid
     *
     * Actualiza el código (code) de un pedido cuando se cambia desde la tienda en la subida de PDF's.
     * Se usa desde Ajax.
     */
    function updateOrderCode($code, $pdfid)
    {
        $query = "UPDATE " . $this->ordersTable . " SET code = '" . $code . "' WHERE pdfid = " . $pdfid;
        $this->wpdb->query($query);
    }

    function getOrderData($onlyOrderData = false, $searchByCode = "")
    {
        // Deliverydate = fecha en la que se entregó el pedido.
        $id = $_REQUEST['id'];
        $fields = "o.id as id, code, pdfid, customer, o.telephone, o.telephone2, o.email, deliveryzone, purchasedate, ";
        $fields .= "deliveryrange, deliverymonth, deliverydate, total, pendingpay, pendingstatus, paymethod, paydate, ";
        $fields .= "status, store, storename, createdby, finishdeliveryfile, finishdeliverycreatedon, finishdeliverycreatedby, ";
        $fields .= "totalitems, saveditems";
        $query = "SELECT " . $fields . " FROM " . $this->ordersTable . " o, " . $this->storesTable . " s ";

        if ($searchByCode != "") {
            $filter = ' AND o.code = "' . $searchByCode . '"';
        } else {
            $filter = " AND o.id = " . $id;
        }

        $query .= " WHERE o.store  = s.id " . $filter;
        $order = $this->wpdb->get_results($query);

        $order = count($order) > 0 ? $order[0] : $order;

        if ($order) {
            $order = persist($order, 'Order');
            if (!$onlyOrderData) {
                $items = $this->wpdb->getAll($this->orderItemsTable, ' WHERE orderid = ' . $id);
                $interComments = $this->getComments($id, 0);
                $customerComments = $this->getComments($id, 1);
                $comments['interns'] = $interComments;
                $comments['customer'] = $customerComments;
                $order->setItems($items);
                $order->setComments($comments);

                //if ($order->getStatus() > 1) { // entregado o entregado con incidencia
                $incidences = $this->getIncidences($id, $order->getCode());
                $order->setIncidences($incidences);
                //
            }
        }

        return $order;
    }

    function getComments($orderid, $commentype)
    {
        $query = 'SELECT username, c.created_on, comment, readydelivery, delivered FROM ' . $this->commentsTable . " c, " . $this->usersTable . " u ";
        $where = ' WHERE orderid = ' . $orderid . ' AND comment_type = ' . $commentype . ' AND c.created_by = u.id';
        $orderby = ' ORDER BY created_on DESC';

        $query .= $where . $orderby;
        $comments = $this->wpdb->get_results($query);

        return $comments;
    }

    function saveComment()
    {
        global $user;
        $_POST['created_by'] = $user->getId();
        $_POST['created_on'] = date('Y-m-d H:i:s');
        $this->wpdb->save($this->commentsTable);
    }

    /**
     * @param $id
     * @return array
     *
     * Para incidencias de un pedido
     */
    function getIncidences($id, $code = 0)
    {
        $query = "SELECT i.id as id, orderid, incidencetype, description, incidencedate, status, pendingpay, created_on, fixed_on, ";
        $query .= " username FROM " . $this->incidencesTable . " i, " . $this->usersTable . " u WHERE (orderid = " . $id;
        $query .= " OR code = '" . $code . "') AND i.created_by = u.id";
        return $this->wpdb->get_results($query);
    }

    /**
     * Obtiene todas las incidencias tengan asociado un pedido o no
     */
    function getAllIncidences() {
        global $user;
        $month = isset($_POST['month']) ? $_POST['month'] : date('m');
        $year = isset($_POST['year']) ? $_POST['year'] : date('Y');
        $allyear = ($month == "all") ? true : false;
        $filterDate = getFilterDate($month, $year, true, "i", "incidencedate", $allyear);
        $filterStatus = '';

        // Si es por rango de fechas sustituímos el filtro anterior $filterDate
        if (!empty($_POST['from']) && !empty($_POST['to'])) {
            $from = implode('-', array_reverse(explode('/', $_POST['from'])));
            $to = implode('-', array_reverse(explode('/', $_POST['to'])));
            $filterDate = ' AND incidencedate BETWEEN "' . $from . '" AND "' . $to . '"';
        }

        if ($_POST['code'] != "") {
            $filterDate = ' AND id = ' . $_POST['code'];
        }

        if ($_POST['status'] != "all") {
            $filterStatus = ' AND status = ' . $_POST['status'];
        }

        $query = "SELECT * FROM " . $this->incidencesTable . " i";
        $where = " WHERE id > 0 " . $filterDate . $filterStatus;
        $incidences = $this->wpdb->get_results($query . $where);

        if ($incidences) {
            $pdfparameters = "";
            $pdfparameters = '&from=' . $_POST['from'] . '&to=' . $_POST['to'];
            $pdfparameters .= '&month=' . $_POST['month'] . '&year=' . $_POST['year'];
            $pdfparameters .= '&code=' . $_POST['code'] . '&status=' . $_POST['status'];
            $incidences[0]->pdfparameters = $pdfparameters;
            //pre($incidences);
        }

        //echo $query . $where;
        //pre($incidences);

        return $incidences;
    }

    function getIncidenceData($id)
    {
        $query = "SELECT i.id as id, orderid, code, store, deliveryzone, incidencetype, description, incidencetype, incidencedate, status, ";
        $query .= "pendingpay, created_on, fixed_on, observations, customer, dni, address, cp, city, provinceid,";
        $query .= " telephone, telephone2, email, username, deliverydate, seller, assembler FROM " . $this->incidencesTable . " i, " . $this->usersTable . " u ";
        $query .= " WHERE i.id = " . $id . " AND i.created_by = u.id";
        $result = $this->wpdb->get_results($query);

        if ($result) {
            $incidence = $result[0];
            $incidence->internProducts = $this->getInternProducts($incidence->id);
            $incidence->items = $this->getIncidenceItems($incidence->id);

            return $incidence;
        }

        return new stdClass();
    }

    function getIncidenceItems($id) {
        $query = 'SELECT * FROM ' . $this->incidenceItemsTable . ' WHERE incidenceid = ' . $id;
        return $this->wpdb->get_results($query);
    }

    function getInternProducts($id) {
        $query = 'SELECT * FROM ' . $this->incidenceInternProductsTable . ' WHERE incidenceid = ' . $id;
        return $this->wpdb->get_results($query);
    }

    function save_db_order($showmsg)
    {
        $_POST['purchasedate'] = from_calendar_to_date($_POST['purchasedate']);
        $_POST['paydate'] = from_calendar_to_date($_POST['paydate']); // Fecha en la que se paga el pendiente
        $_POST['total'] = str_ireplace(',', '.', $_POST['total']);
        $_POST['pendingpay'] = str_ireplace(',', '.', $_POST['pendingpay']);

        $response = $this->wpdb->save($this->ordersTable, $showmsg);

        if ($response['saved'] == 1) {
            // Actualizamos el campo orderexist a 1 en la tabla pdfs
            $this->updateOrderExist();
        }

        return $response;

    }

    function updateStatus($id, $status, $deliverydate = "")
    {
        $andDeliveryDate = "";
        if ($deliverydate != "") {
            $andDeliveryDate = " , deliverydate='" . $deliverydate . "'";
        }

        $query = "UPDATE " . $this->ordersTable . " SET status = " . $status . $andDeliveryDate . " WHERE id = " . $id;
        $this->wpdb->query($query);
    }

    /**
     * @param $showmsg
     * @param bool $deliveryFile
     * @return bool
     *
     * DeliveryFile a true indica que estamos cargando la última nota de entrega generada desde el almacén
     * y los campos a actualizar son: finishdeliveryfile ,finishdeliverycreatedon, finishdeliverycreatedby
     */
    function save_edit_db_order($showmsg, $deliveryFile = false)
    {
        if (!$deliveryFile) {
            $_POST['purchasedate'] = from_calendar_to_date($_POST['purchasedate']);
            $_POST['paydate'] = from_calendar_to_date($_POST['paydate']);
            $_POST['total'] = str_ireplace(',', '.', $_POST['total']);
            $_POST['pendingpay'] = str_ireplace(',', '.', $_POST['pendingpay']);

            $response = $this->wpdb->save_edit($this->ordersTable, $showmsg);
            // Actualizamos el campo orderexist a 1
            $this->updateOrderExist();
        } else {
            $response = $this->wpdb->save_edit($this->ordersTable, $showmsg);
        }

        return $response;

    }

    function updateOrderExist()
    {
        //$query = "UPDATE " . $this->pdfsTable . " SET orderexist = 1 WHERE code = '" . $_POST['code'] . "'";
        $query = "UPDATE " . $this->pdfsTable . " SET orderexist = 1 WHERE id = '" . $_POST['pdfid'] . "'";
        $this->wpdb->query($query);
    }

    function save_products()
    {
        $id = $_POST['id'];
        $saveds = 0;
        if ($this->existProductInOrder($id)) {
            $this->deleteItems($id);
        }

        foreach ($_POST['products'] as $key => $value) {
            if (!empty($_POST['products'][$key])) {
                $manufacturing = $_POST['manufacturings'][$key] != "" ? from_calendar_to_date($_POST['manufacturings'][$key]) : '0000-00-00';
                $finish = $_POST['finishes'][$key] != "" ? from_calendar_to_date($_POST['finishes'][$key]) : '0000-00-00';
                $store = $_POST['stores'][$key] != "" ? from_calendar_to_date($_POST['stores'][$key]) : '0000-00-00';
                $categoryId = $_POST['categories'][$key] != "" ? $_POST['categories'][$key] : 0;

                $query = 'INSERT INTO ' . $this->orderItemsTable . ' (orderid, productid, categoryid, ';
                $query .= 'manufacturing_in, finish_in, store_in) VALUES ';
                $query .= ' (' . $id . ', ' . $_POST['products'][$key] . ', ' . $categoryId . ', ';
                $query .= '"' . $manufacturing . '", ' . '"' . $finish . '", ' . '"' . $store . '");';
                $this->wpdb->query($query);

                $saveds++;
            }
        }

        // Actualizamos el total de productos guardados en el campo saveditems de ge_orders
        $query = 'UPDATE ' . $this->ordersTable . ' SET saveditems = ' . $saveds . ' WHERE id = ' . $id;
        $this->wpdb->query($query);

        // Actualizamos el campo orderexist a 1
        $this->updateOrderExist();

        $result['saved'] = $saveds > 0 ? 1 : 0;
        return $result;

    }

    function existProductInOrder($orderid)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->orderItemsTable . " WHERE orderid=$orderid";
        $exist = $this->wpdb->get_results($query);

        if ($exist[0]->total > 0) {
            return true;
        }

        return false;
    }

    function deleteItems($orderid)
    {
        $query = "DELETE FROM " . $this->orderItemsTable . " WHERE orderid=$orderid";
        $this->wpdb->query($query);
    }

    function getNewPdfs()
    {
        $filter = "";
        if (isset($_POST['filter'])) {
            $filter = $_POST['filter'];
            if ($filter == 'onlywithpdf') {
                $filter = ' AND pdfname <> "" ';
            } else if ($filter == 'pdfyetprinted') {
                $filter = ' AND pdf_yet_printed = 1 ';
            } else if ($filter == 'all') {
                $filter = '';
            }
        }

        $query = "SELECT p.id as id, p.saledate, p.code, p.customer, p.storeid, p.reuploaded, ";
        $query .= "p.returned_on, storename, image, pdfname, p.created_on, username, pdf_yet_printed ";
        $query .= " FROM " . $this->pdfsTable . " p, " . $this->usersTable . " u, " . $this->storesTable . " s";
        $query .= " WHERE p.cancelled = 0 AND p.created_by = u.id AND p.storeid = s.id AND saletype = 0 AND orderexist = 0";
        $query .= $filter . " ORDER BY code ASC";

        //echo $query;
        $pdfs = $this->wpdb->get_results($query);
        //pre($pdfs);

        return $pdfs;
    }

    function getOrdersWidget() {
        $query = "SELECT COUNT(*) total FROM " . $this->pdfsTable . " WHERE cancelled = 0 AND orderexist = 0 AND saletype = 0";
        //$query = "SELECT COUNT(*) total FROM " . $this->pdfsTable . " WHERE cancelled = 0 AND orderexist = 0 AND saletype = 0 AND id NOT IN (SELECT pdfid FROM ge_orders);";
        $orders = $this->wpdb->get_results($query);
        $total = $orders[0]->total;

        $query = "SELECT COUNT(*) total FROM " . $this->ordersTable . " WHERE status < 2 AND (totalitems - saveditems) <> 0";
        $incompleteds = $this->wpdb->get_results($query);
        $totalSaveds = $incompleteds[0]->total;

        $widget = array();

        if ($total == 0) {
            $widget['news'] =  '<span class="widget-cart-zero">' . $total . '</span>';
        } else {
            $widget['news'] = '<span class="widget-cart">' . $total . '</span>';
        }

        if ($totalSaveds == 0) {
            $widget['incompletes'] =  '<span class="widget-cart-zero">' . $totalSaveds . '</span>';
        } else {
            $widget['incompletes'] = '<span class="widget-cart">' . $totalSaveds . '</span>';
        }

        return $widget;

    }

    function restorePdf()
    {
        $_POST['status'] = 0;
        //$_POST['pdfname'] = "";
        $_POST['returned_on'] = date('Y-m-d H:i:s');
        $_POST['reuploaded'] = 1;
        $_POST['id'] = $_GET['id'];
        $_POST['orderexist'] = 0;
        $this->wpdb->save_edit($this->pdfsTable);
    }

    function getPdfInfo()
    {
        return $this->wpdb->getOneRow($this->pdfsTable, $_GET['pdfid']);
    }

    function getPdfName($id)
    {
        $query = "SELECT pdfname FROM " . $this->pdfsTable . " WHERE id = " . $id;
        return $this->wpdb->get_var($query);
    }

    function isOrderCreated($id)
    {
        $query = 'SELECT COUNT(*) AS total, id FROM ' . $this->ordersTable . " WHERE pdfid=" . $id;
        $exist = $this->wpdb->get_results($query);

        $orderExist[0] = false;
        $orderExist[1] = 0;

        if ($exist[0]->total > 0) {
            $orderExist[0] = true;
            $orderExist[1] = $exist[0]->id;

            return $orderExist;
        }

        return $orderExist;
    }

    function save_incidence()
    {
        return $this->wpdb->save($this->incidencesTable, false);
    }

    function save_edit_incidence()
    {
        return $this->wpdb->save_edit($this->incidencesTable, false);
    }

    function update_cancelled_order($whereField) {
        return $this->wpdb->save_edit($this->ordersTable, false, $whereField);
    }

    function save_incidence_comment()
    {
        return $this->wpdb->save($this->incidenceCommentsTable);
    }

    function getIncidenceComments()
    {
        $query = 'SELECT c.id as id, comment, username, c.created_on FROM ' . $this->incidenceCommentsTable . ' c, ';
        $query .= $this->usersTable . ' u WHERE c.created_by = u.id AND incidenceid = ' . $_POST['incidenceid'];
        $query .= ' ORDER BY created_on DESC';
        return $this->wpdb->get_results($query);
    }

    function existIncidenceItems($id) {
        $query = 'SELECT count(*) as total FROM ' . $this->incidenceItemsTable . ' WHERE incidenceid = ' . $id;
        $result = $this->wpdb->get_results($query);

        if ($result[0]->total > 0) {
            $query = 'DELETE FROM ' . $this->incidenceItemsTable . ' WHERE incidenceid = ' . $id;
            $this->wpdb->query($query);
        }
    }

    function saveIncidenceItems() {
        $id = $_POST['incidenceid'];
        // Comprobamos si existen item anteriores y los eliminamos
        $this->existIncidenceItems($id);

        foreach ($_POST['reference'] as $key => $value) {
            if ($value != "") {
                $reference = $value;
                $description = $_POST['description'][$key];
                $quantity = $_POST['quantity'][$key];
                $price = formatNumberToDB($_POST['price'][$key]);
                $discount = $_POST['discount'][$key];
                $query = 'INSERT INTO ' . $this->incidenceItemsTable . ' (incidenceid, reference, description, quantity, ';
                $query .= 'price, discount) VALUES ("' . $id . '", "' . $reference . '", "' . $description . '",';
                $query .=  '"' . $quantity . '", "' . $price . '", "' . $discount . '");';

                $this->wpdb->query($query);
            }
        }

        // Incidencia generada desde un pedido
        if ($_POST['orderid'] > 0) {
            $_POST['customer'] = "";
            $_POST['telephone'] = "";
            $_POST['telephone2'] = "";
            $_POST['email'] = "";
        }

        // Guardamos la observación para la entrega.
        // Aunque se recibe desde los items de la nota de entrega se guarda en la tabla incidences campo "observations".
        $query = 'UPDATE ' . $this->incidencesTable . ' SET customer = "' . $_POST['customer'] . '", dni = "' . $_POST['dni'] . '", address = "' . $_POST['address'] . '",';
        $query .= 'cp = "' . $_POST['cp'] . '", city = "' . $_POST['city'] . '", provinceid = "' . $_POST['provinceid'] . '", ';
        $query .= 'telephone = "' . $_POST['telephone'] . '", telephone2 = "' . $_POST['telephone2'] . '", email = "' . $_POST['email'] . '", ';
        $query .= 'deliverydate = "' . from_calendar_to_date($_POST['deliverydate']) . '", seller = "' . $_POST['seller'] . '", ';
        $query .= 'assembler = "' . $_POST['assembler'] . '", ';
        $query .= 'observations = "' . nl2br($_POST['observations']) . '" WHERE id = ' . $id;
        $this->wpdb->query($query);

        // Si llegamos al final devolvemos true
        return true;
    }

    function existInternProduct($id) {
        $query = 'SELECT count(*) as total FROM ' . $this->incidenceInternProductsTable . ' WHERE incidenceid = ' . $id;
        $result = $this->wpdb->get_results($query);

        if ($result[0]->total > 0) {
            $query = 'DELETE FROM ' . $this->incidenceInternProductsTable . ' WHERE incidenceid = ' . $id;
            $this->wpdb->query($query);
        }
    }

    function saveInternProducts() {
        $id = $_POST['incidenceid'];
        // Comprobamos si existen item anteriores y los eliminamos
        $this->existInternProduct($id);

        foreach ($_POST['products'] as $key => $value) {
            if ($value != "") {
                $productid = $value;
                $categoryid = $_POST['categories'][$key];
                $manufacturing = from_calendar_to_date($_POST['manufacturings'][$key]);
                $finish = from_calendar_to_date($_POST['finishes'][$key]);
                $store = from_calendar_to_date($_POST['stores'][$key]);
                $query = 'INSERT INTO ' . $this->incidenceInternProductsTable . ' (incidenceid, productid, categoryid, manufacturing_in, ';
                $query .= 'finish_in, store_in) VALUES ("' . $id . '", "' . $productid . '", "' . $categoryid . '",';
                $query .=  '"' . $manufacturing . '", "' . $finish . '", "' . $store . '");';

                $this->wpdb->query($query);
            }
        }

        // Si llegamos al final devolvemos true
        return true;
    }

    function getEmailsByProduct() {
        $month = isset($_POST['month']) ? $_POST['month'] : date('m');
        $year = isset($_POST['year']) ? $_POST['year'] : date('Y');
        $filterDate = getFilterDate($month, $year, true, "o", "purchasedate");

        // Si es por rango de fechas sustituímos el filtro anterior $filterDate
        if (!empty($_POST['from']) && !empty($_POST['to'])) {
            $from = implode('-', array_reverse(explode('/', $_POST['from'])));
            $to = implode('-', array_reverse(explode('/', $_POST['to'])));
            $filterDate = ' AND o.purchasedate BETWEEN "' . $from . '" AND "' . $to . '"';
        }

        $query = 'SELECT email, purchasedate, category FROM ' . $this->ordersTable . ' o, ' . $this->orderItemsTable . ' i, ';
        $query .= $this->categoriesTable . ' ca WHERE ca.id = i.categoryid AND email <> "" AND email NOT LIKE "%no tiene%" AND ';
        $query .= 'o.id = i.orderid AND categoryid = ' . $_POST['product'] . $filterDate . ' ORDER BY email ASC';
        $data = $this->wpdb->get_results($query);

        //echo $query;

        return $data;
    }

    function getAutocompleteIncidenceCode() {
        $keyword = $_POST['keyword'];

        $query = 'SELECT DISTINCT i.id, o.code, o.customer, incidencedate FROM ' . $this->ordersTable . ' o, ' . $this->incidencesTable . ' i';
        $query .= ' WHERE o.id = i.orderid AND (o.code LIKE "%' . $keyword . '%" OR o.customer LIKE "%' . $keyword . '%") LIMIT 10;';
        $codes = $this->wpdb->get_results($query);

        if (count($codes) == 0) {
            $query = 'SELECT id, customer, incidencedate FROM ' . $this->incidencesTable;
            $query .= ' WHERE id > 0 AND customer LIKE "%' . $keyword . '%" LIMIT 10;';
            $codes = $this->wpdb->get_results($query);
        }

        return $codes;
    }

    function getGeneralOrderView() {
        $object = new stdClass();
        $_POST['code'] = $_POST['maincode'];
        $controller = new StoreController();
        $parent = $controller->getPdfChildren();
        $object->parent = $parent;

        // Obtenemos los datos del pedido pero primero obtenemos el id del pedido por pdfid
        $ordeId = $this->getOrderIdByPdfId($_POST['code']);
        if ($ordeId) {
            $_REQUEST['id'] = $ordeId[0]->id;
            $object->order = $this->getOrderData();
        }

        return $object;
    }

    function pdfYetPrinted() {
        return $this->wpdb->save_edit($this->pdfsTable, false);
    }

    function createTestingData()
    {
        set_time_limit(120);
        $firstnames = array(
            'Antonio', 'Carlos', 'David', 'Juan', 'Pedro', 'Lucas', 'Kevin', 'Eduardo', 'Mathías', 'Francisco', 'Alfonso',
            'Daniel', 'Joaquín', 'Pablo', 'Julio', 'Iván', 'Emanuel', 'Jhonatan', 'Jhon', 'Victor', 'Manuel', 'Dario',
            'Linas', 'Mónica', 'Sonia', 'Carmen', 'Carmenza', 'Laura', 'Salomé', 'Julia', 'Inés', 'Auróra', 'Claudia',
            'Gloria', 'Johana', 'Cynthia', 'Elena', 'Débora', 'Natalia', 'Esperanza', 'Asunción', 'Francisca', 'Miriam', 'Olga'
        );

        $lastname = array(
            'Vélez', 'Nieto', 'Salazar', 'Marín', 'Torres', 'Nuñez', 'Santos', 'Peláez', 'Martinez', 'Martín', 'Bedoya',
            'Zamora', 'Carmona', 'Jaramillo', 'Córdoba', 'Frankfurth', 'Colonia', 'Maduro', 'Uribe', 'Pedrosa', 'Gómez', 'Jimenez',
            'Antioquia', 'Sucre', 'Amazonas', 'Ibagué', 'Dominguez', 'Atlanta', 'Montero', 'Agudo', 'Albero', 'Cano', 'Carpio'
        );

        for ($i = 1; $i < 12; $i++) {
            //for ($j = 0; $j < 2; $j++) {
            for ($h = 1; $h < 30; $h++) {
                $inimonth = date('2017-' . $i . '-' . $h . ' 09:00:00');
                $code = rand(100000, 999999);
                $customer = $firstnames[rand(0, 32)] . " " . $lastname[rand(0, 32)] . " " . $lastname[rand(0, 32)];
                $telephone = rand(910000000, 919999999);
                $purchasedate = strtotime('+' . $i . ' day', strtotime($inimonth));
                $purchasedate = date('Y-m-d H:i:s', $purchasedate);
                $storeid = rand(1, 3);
                $pdfname = "Pdf_" . $code . date('d_m_Y-H:i:s') . '.pdf';

                $total = rand(3000, 6000);
                $randdecimal = rand(1,9);
                $saletotal = $total . '.' . $randdecimal . '0';
                $pendigPay = $saletotal - (($saletotal)/100 * 30);

                $query = 'INSERT INTO ' . $this->pdfsTable . '(saledate, total, storeid, pdfname, created_on, created_by, status, orderexist) ';
                $query .= 'VALUES ("' . $purchasedate . '", "' . $saletotal . '", "' . $storeid . '", "' . $pdfname . '", "' . $purchasedate . '", "' . rand(5, 8) . '", 1, 1) ';
                $this->wpdb->query($query);
                $pdfid = $this->wpdb->insert_id;


                $query = 'INSERT INTO ' . $this->ordersTable . ' (code, pdfid, customer, telephone, deliveryzone, store, purchasedate,';
                $query .= 'deliveryrange, deliverymonth, total, pendingpay, paymethod, createdby, createdon) VALUES ("' . $code . '", "' . $pdfid . '", "' . $customer . '", "' . $telephone . '",';
                $query .= '"' . rand(1, 4) . '", "' . $storeid . '", "' . $purchasedate . '", "' . rand(0, 1) . '", "' . $i . '", ' . $saletotal . ', ' . $pendigPay . ', "' . rand(0, 2) . '", 4, "' . $purchasedate . '"); ';

                $this->wpdb->query($query);
                //echo $query . "<br>";
            }
            //}
        }

        confirmationMessage('Estructura de datos creada!');
    }

}