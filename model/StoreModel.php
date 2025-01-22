<?php

/**
 * Created by PhpStorm.
 * User: yvelezs
 * Date: 14/11/2017
 * Time: 12:24
 */
class StoreModel extends Store
{
    protected $storestable;
    protected $provincestable;
    protected $pdfsTable;
    protected $estimatesTable;
    protected $commentsTable;
    protected $estimateCommentsTable;
    protected $ordersTable;
    protected $usersTable;
    protected $wpdb;

    function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->storestable = $wpdb->prefix . "stores";
        $this->provincestable = $wpdb->prefix . "provinces";
        $this->pdfsTable = $wpdb->prefix . "pdfs";
        $this->estimatesTable = $wpdb->prefix . "estimates";
        $this->commentsTable = $wpdb->prefix . "pdf_comments";
        $this->estimateCommentsTable = $wpdb->prefix . "estimate_comments";
        $this->ordersTable = $wpdb->prefix . "orders";
        $this->usersTable = $wpdb->prefix . "users";
    }

    function getStores($persist = true) {
        $query = "SELECT *, s.id AS id FROM " . $this->storestable . " s, " . $this->provincestable . " p";
        $query .= " WHERE s.provinceid = p.id ORDER BY storename ASC";
        $stores = $this->wpdb->get_results($query);

        if ($persist) {
            $stores = persist($stores, 'store');
        }

        return $stores;
    }

    function getStoreData($id = 0) {
        if ($id > 0){
            $storeId = $id;
        } else {
            $storeId = $_GET['id'];
        }

        $data = $this->wpdb->getOneRow($this->storestable, $storeId);
        $user = persist($data, 'Store');

        return $user;
    }

    function save_store_db() {
        $this->wpdb->save($this->storestable);
    }

    function save_edit_store_db() {
        $this->wpdb->save_edit($this->storestable);
    }

    function existPdfByCode($code, $fieldname = "code") {
        $query = 'SELECT count(*) as total FROM ' . $this->pdfsTable . ' WHERE ' . $fieldname . ' ="' . $code . '"';
        $result = $this->wpdb->get_results($query);

        if ($result[0]->total > 0) {
            return true;
        }

        return false;
    }

    function getPdfChildren() {
        global $user;
        $results = array();
        $parent = $this->wpdb->getOneRow($this->pdfsTable, $_POST['code']);
        $query = 'SELECT * FROM ' . $this->pdfsTable . ' WHERE parentcode = ' . $parent->id . ' ORDER BY created_on ASC';
        $children = $this->wpdb->get_results($query);

        $results[0] = $parent;
        if (userWithPrivileges()) {
            $pdfparameters = '&code=' . $_POST['code'];
            $results[0]->pdfparameters = $pdfparameters;
        }
        $results = array_merge($results, $children);

        if (userWithPrivileges()) {
            //pre($results);
        }

        return $results;
    }

    function getPdfs()
    {
        global $user;
        $month = isset($_POST['month']) ? $_POST['month'] : date('m');
        $year = isset($_POST['year']) ? $_POST['year'] : date('Y');
        $allyear = false;
        if ($month == "all") {
            $allyear = true;
        }
        $filterDate = getFilterDate($month, $year, true, "p", "saledate", $allyear);
        $filterSaleType = "";
        $saletype = $_POST["saletype"];
        if ($saletype != "all") {
            $filterSaleType = ' AND saletype = ' . $saletype;
        }

        $filterUser = "";
        $filterCommission = "";

        // Si es por rango de fechas sustituímos el filtro anterior $filterDate
        if (!empty($_POST['from']) && !empty($_POST['to'])) {
            $from = implode('-', array_reverse(explode('/', $_POST['from'])));
            $to = implode('-', array_reverse(explode('/', $_POST['to'])));
            $filterDate = ' AND saledate BETWEEN "' . $from . '" AND "' . $to . '"';
        }

        if (userWithPrivileges()) {
            // Ventas de todos los usuarios
            $firstfilter = ' p.created_by=u.id';
            $filterStore = "";
            if (isset($_POST['store'])) {
                if ($_POST['store'] != "" && $_POST['store'] != "all") {
                    $filterStore = ' AND p.storeid=' . $_POST['store'];
                }
            }

            if (!empty($_POST['user'])) {
                $filterUser = ' AND created_by = ' . $_POST['user'];
            }

            if (isset($_POST['commission']) && $_POST['commission'] == "yes") {
                $filterCommission = ' AND pdfname <>  "" AND saletype = 0 AND commissionpayed = 0';
            }

            // Concatenamos el filtro de usuario en filterDate
            $filterDate .= $filterUser . $filterCommission;
        } else {
            // Sólo las ventas de mi usuario (tienda)
            $firstfilter = 'p.created_by = ' . $user->getId() . ' AND p.created_by=u.id';
            $filterStore = ' AND p.storeid=' . $user->getStoreid();
        }

        $query = "SELECT p.id as id, p.saletype, p.storeid, p.code, p.parentcode, customer, saledate, total, p.comment, ";
        $query .= "total_checked_by, total_checked_on, total_checked_note, total_checked_system_date, payed, paymethod, pdfname, ";
        $query .= "pdf_uploaded_on, commissionpayed, commission_payed_on, commission_validated_by, image, username, p.created_on, status, p.accounting_checked_by, p.accounting_checked_on, p.accounting_checked_note, ";
        $query .= "p.accounting_checked_system_date, cancelled, cancelled_by, cancelled_on, cancell_reason, orderexist FROM ";
        $query .= $this->pdfsTable . " p, " . $this->usersTable . " u ";
        $where = " WHERE " . $firstfilter. " " . $filterStore . $filterDate . $filterSaleType;
        $orderby = " ORDER BY code ASC, created_on ASC";
        $pdfs = $this->wpdb->get_results($query . $where . $orderby);

        if (userWithPrivileges() && $pdfs) {
            $pdfparameters = '&commission=' . $_POST['commission'] . '&from=' . $_POST['from'] . '&to=' . $_POST['to'];
            $pdfparameters .= '&month=' . $_POST['month'] . '&year=' . $_POST['year'] . '&store=' . $_POST['store'];
            $pdfparameters .= '&user=' . $_POST['user'] . '&saletype=' . $_POST['saletype'];
            $pdfs[0]->pdfparameters = $pdfparameters;
            //pre($pdfs);
        }
        
        if ($filterCommission != "") {
            foreach ($pdfs as $pdf) {
            	$variation = new stdClass();
                $query = "SELECT SUM(total) as positive FROM " . $this->pdfsTable . ' WHERE parentcode = ' . $pdf->id . ' AND total > 0';
                $variations = $this->wpdb->get_var($query);
                $variation->positive = $variations;
				$query = "SELECT SUM(total) as negative FROM " . $this->pdfsTable . ' WHERE parentcode = ' . $pdf->id . ' AND total < 0';
                $variations = $this->wpdb->get_var($query);
				$variation->negative = $variations;
				
                $pdf->variations =  $variation;
            }
        }

        //echo $query . $where . $orderby;

        return $pdfs;
    }

    function getPdfComments($id) {
        return $this->wpdb->getOneField($this->pdfsTable, 'comment', ' WHERE id = ' . $id);
    }

    function getPdfUploadDate($id) {
        return $this->wpdb->getOneField($this->pdfsTable, 'pdf_uploaded_on', ' WHERE id = ' . $id);
    }
	
	function checkIfSaleExist() {
        $query = 'SELECT * FROM ' . $this->pdfsTable . ' WHERE saletype = 0 AND code = "' . $_POST['code'] . '" ';
        $exist = $this->wpdb->get_results($query);

        // Verificamos si el código no cambia
        if (isset($_POST['id'])) {
            $code = $this->getCodeByParentId($_POST['id']);
            if ($code == $_POST["code"]) {
                return false;
            }
        }

        if (count($exist) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     * Guarda los datos de la venta desde el almacén
     */
    function saveSale() {
        if (isset($_POST['total'])) {
            $_POST['total'] = formatNumberToDB($_POST['total']);
        }
        if (isset($_POST['payed'])) {
            $_POST['payed'] =  formatNumberToDB($_POST['payed']);
        }

        if ($_POST['saletype'] == 1) { // Variaciones
            return $this->wpdb->save($this->pdfsTable, false);
        } else if ($_POST['opt'] == "saveOrderPay" || $_POST['opt'] == "adjustPendingPay") { // Entrega a cuenta
            return $this->wpdb->save($this->pdfsTable, false);
        } else if ($_POST['saletype'] == 0 && !$this->checkIfSaleExist()) {
            return $this->wpdb->save($this->pdfsTable, false);
        } else if ($this->checkIfSaleExist()){
            $result = array();
            $result['codeduplicated'] = "1";
            return $result;
        }

    }

    function saveEstimate() {
        if (isset($_POST['total'])) {
            $_POST['total'] = formatNumberToDB($_POST['total']);
        }

        $response = $this->wpdb->save($this->estimatesTable, false);
        $lastId = $this->wpdb->insert_id;
        $response['pdfurl'] = $this->getEstimatePdfUrl($lastId, $_POST['code']);
        return $response;
    }

    function getMyTotalEstimates($createdBy) {
        return $this->wpdb->get_var("SELECT COUNT(*) FROM " . $this->estimatesTable . " WHERE created_by = " . $createdBy);
    }

    function getEstimateCode($user) {
        $totalEst = $this->getMyTotalEstimates($user->getId());
        return getEstimateCode($totalEst, $user);
    }

    function getCodeByParentId($id = 0) {
        $myId = $_POST['parentcode'];
        if ($id != 0) {
            $myId = $id;
        }
        return $this->wpdb->get_var("SELECT code FROM " . $this->pdfsTable . " WHERE id = " . $myId);
    }

    /**
     * @return bool
     * Guarda los datos de la venta desde el almacén
     */
    function updateSale($whereField = 'id') {
        if (isset($_POST['total'])) {
            $_POST['total'] = formatNumberToDB($_POST['total']);
        }
        if (isset($_POST['payed'])) {
            $_POST['payed'] =  formatNumberToDB($_POST['payed']);
        }

        if ($_POST['saletype'] == 1) {
            return $this->wpdb->save_edit($this->pdfsTable, false, $whereField);
        } else if ($_POST['opt'] == "updateOrderPay" || $_POST['opt'] == "updateAdjustPendingPay") { // Entrega a cuenta
            return $this->wpdb->save_edit($this->pdfsTable, false, $whereField);
        } else if ($_POST['saletype'] == 0 && !$this->checkIfSaleExist()) {
            return $this->wpdb->save_edit($this->pdfsTable, false, $whereField);
        } else if ($this->checkIfSaleExist()){
            $result = array();
            $result['codeduplicated'] = "1";
            return $result;
        }
    }

    /**
     * @return bool
     * Guarda el código del pdf
     */
    function savePdfCode() {
        return $this->wpdb->save($this->pdfsTable, false);
    }

    /**
     * @return bool
     * Actualiza el código del pdf. Se hace por ajax.
     */
    function updatePdfCode() {
        return $this->wpdb->save_edit($this->pdfsTable, false);
    }

    /**
     * @param int $parent
     * @return null|object
     *
     * Puede obtener los datos del pdf o del padre (parentcode)
     */
    function getPdfData($parent = 0) {

        if ($parent == 0) {
            $data = $this->wpdb->getOneRow($this->pdfsTable, $_GET['id']);
            $data->comments = $this->getNewPdfComments();
        } else {
            $query = 'SELECT code, customer, saledate, storeid FROM ' . $this->pdfsTable . ' WHERE id = ' . $parent;
            $data = $this->wpdb->get_row($query);
            $data->comments = array();
        }

        return $data;
    }

    function getAdjustPendingPayData() {
        $query = 'SELECT saledate, payed, paymethod, comment FROM ' . $this->pdfsTable . ' WHERE parentcode = ' . $_POST['parentid'];
        $query .= ' AND saletype = 3';

        $data = $this->wpdb->get_results($query);
        return $data[0];
    }

    function getNewPdfComments($id = 0, $table = "", $wherefield = "pdfid") {
        $commentTable = $table != "" ? $table : $this->commentsTable;
        if ($id > 0) {
            $_GET['id'] = $id;
        }

        $query = 'SELECT c.id as id, created_by, created_on, comment, username FROM ' .  $commentTable . ' c, ';
        $query .= $this->usersTable . ' u WHERE ' . $wherefield . ' = ' . $_GET['id'] . ' AND c.created_by = u.id';
        $query .= ' ORDER BY created_on DESC';

        return $this->wpdb->get_results($query);
    }

    function updatePdfStatus() {
        $_POST['status'] = 1;
        $_POST['id'] = $_GET['id'];
        $this->wpdb->save_edit($this->pdfsTable);
    }

    /**
     * @param bool $nocancelled
     * @return array
     * Si vamos a guardar una variación filtramos los que no estén cancelados
     */
    function getAutocompleteCode($nocancelled = false) {
        global $user;
        $keyword = $_POST['keyword'];
        $cancelledFilter = '';
        if ($nocancelled) {
            $cancelledFilter = ' AND cancelled = 0';
        }
        $createdByFilter = 'created_by = ' . $user->getId();
		
		/*
		 * Actualización: 01/03/19.
		 * Paco solicita que otro usuario perteneciente a la tienda pueda gestionar los pedidos de compañeros.
		 * 
		 * 13/05/19 Se vuelve a quitar porque Paco ya no le gusta que otros vean lo de otros.
		 */
		 
        if (isadmin() || $user->getUsermanager() == 1 || $user->getUseraccounting() == 1 || $user->getUserrepository() == 1) {
            $createdByFilter = 'id > 0';
        }/* else {
        	 $createdByFilter = 'id > 0';
        }*/
		
        $query = 'SELECT id, code, saledate, customer FROM ' . $this->pdfsTable;
        $query .= ' WHERE ' . $createdByFilter . ' ' . $cancelledFilter . ' AND saletype = 0';
        $query .= ' AND (code LIKE "%' . $keyword . '%" OR customer LIKE "%' . $keyword . '%") LIMIT 3;';
        if ($user->getId() == 1) {
            //echo $query;
        }
        //echo $query;
        $codes = $this->wpdb->get_results($query);

        return $codes;
    }

    function getAutocompleteMainCode() {
        $keyword = $_POST['keyword'];
        /*$createdByFilter = 'created_by = ' . $user->getId();
        if (isadmin() || $user->getUsermanager() == 1 || $user->getUseraccounting() == 1 || $user->getUserrepository() == 1) {
            $createdByFilter = 'id > 0';
        }*/

        // Realizamos primero búsqueda por código de pedido o nombre de cliente
        $query = 'SELECT id, code, saledate, customer FROM ' . $this->pdfsTable;
        $query .= ' WHERE id > 0 AND saletype = 0';
        $query .= ' AND (code LIKE "%' . $keyword . '%" OR customer LIKE "%' . $keyword . '%") LIMIT 3;';
        //echo $query;
        $codes = $this->wpdb->get_results($query);

        // Si no obtenemos resultados buscamos por teléfono
        if (count($codes) == 0) {
            $query = 'SELECT pdfid as id, purchasedate as saledate, code, customer, telephone FROM ' . $this->ordersTable . ' WHERE telephone = "' . $keyword . '"';
            $codes = $this->wpdb->get_results($query);
        }
        //echo $query;

        return $codes;
    }

    function getValidationPaymentData() {
        $query = 'SELECT accounting_checked_by, accounting_checked_on, accounting_checked_note FROM ' . $this->pdfsTable;
        $query .= ' WHERE id = ' . $_POST['id'];

        return $this->wpdb->get_row($query);
    }

    function saveComment($aTable = "") {
        $commentTable = $this->commentsTable;
        if ($aTable != "") {
            $commentTable = $aTable;
        }

        $this->wpdb->save($commentTable);
    }
	
	function restore_manual() {
		$query = 'UPDATE ' . $this->pdfsTable . ' SET cancelled = 0, cancelled_by = 0, cancelled_on = "0000-00-00 00:00:00 ", ';
		$query .=  ' cancell_reason = "" WHERE id = ' . $_GET['id'];
		$this->wpdb->query($query);
		
	}
	
	function getOrdersToDelete() {
		$query = 'SELECT id, code, total, created_on FROM ' . $this->pdfsTable . ' WHERE saletype = 0';
		$query .= ' ORDER BY code ASC';
		$orders = $this->wpdb->get_results($query);
		return $orders;
	}
	
	function deleteOrderByAdmin() {
		//pre($_POST);
		foreach ($_POST['orders'] as $order) {
			$query = 'DELETE FROM ' . $this->pdfsTable . ' WHERE id = ' . $order . ' OR parentcode = ' . $order . ';';
			$queryOrderId = 'SELECT id FROM ' . $this->ordersTable . ' WHERE pdfid = ' . $order;
 			$orderId = $this->wpdb->get_var($queryOrderId);
			$queryOrder = 'DELETE FROM ' . $this->ordersTable . ' WHERE pdfid = ' . $order;
			$queryMsgs = 'DELETE FROM ' . $this->commentsTable . ' WHERE pdfid = ' . $order;
			
			echo $orderId . '<br>';
			$queryOrderComments = 'DELETE FROM ge_order_comments WHERE orderid = ' . $orderId;
			$queryOrderItems = 'DELETE FROM ge_order_items WHERE orderid = ' . $orderId;
			$queryIncidences = 'DELETE FROM ge_incidences WHERE orderid = ' . $orderId;
			
			echo $query . '<br>';
			echo $queryOrder . '<br>';
			echo $queryMsgs . '<br>';
			echo $queryOrderComments . '<br>';
			echo $queryOrderItems . '<br>';
			echo $queryIncidences . '<br>';
		}
	}

    function getEstimates() {
        global $user;
        $month = isset($_POST['month']) ? $_POST['month'] : date('m');
        $year = isset($_POST['year']) ? $_POST['year'] : date('Y');
        $allyear = false;
        if ($month == "all") {
            $allyear = true;
        }
        $filterDate = getFilterDate($month, $year, true, "p", "saledate", $allyear);

        $filterUser = "";
        $filterCommission = "";

        // Si es por rango de fechas sustituímos el filtro anterior $filterDate
        if (!empty($_POST['from']) && !empty($_POST['to'])) {
            $from = implode('-', array_reverse(explode('/', $_POST['from'])));
            $to = implode('-', array_reverse(explode('/', $_POST['to'])));
            $filterDate = ' AND saledate BETWEEN "' . $from . '" AND "' . $to . '"';
        }

        if (userWithPrivileges()) {
            // Ventas de todos los usuarios
            $firstfilter = ' p.created_by=u.id';
            $filterStore = "";
            if (isset($_POST['store'])) {
                if ($_POST['store'] != "" && $_POST['store'] != "all") {
                    $filterStore = ' AND p.storeid=' . $_POST['store'];
                }
            }

            if (!empty($_POST['user'])) {
                $filterUser = ' AND created_by = ' . $_POST['user'];
            }

            if (isset($_POST['commission']) && $_POST['commission'] == "yes") {
                $filterCommission = ' AND pdfname <>  "" AND saletype = 0 AND commissionpayed = 0';
            }

            // Concatenamos el filtro de usuario en filterDate
            $filterDate .= $filterUser . $filterCommission;
        } else {
            // Sólo las ventas de mi usuario (tienda)
            $firstfilter = 'p.created_by = ' . $user->getId() . ' AND p.created_by=u.id';
            $filterStore = ' AND p.storeid=' . $user->getStoreid();
        }

        $query = "SELECT *, p.id as id FROM ";
        $query .= $this->estimatesTable . " p, " . $this->usersTable . " u ";
        $where = " WHERE " . $firstfilter. " " . $filterStore . $filterDate;
        $orderby = " ORDER BY code ASC, created_on ASC";
        $estimates = $this->wpdb->get_results($query . $where . $orderby);

        if (userWithPrivileges() && $estimates) {
            $pdfparameters = '&commission=' . ($_POST['commission'] ?? "") . '&from=' . $_POST['from'] . '&to=' . $_POST['to'];
            $pdfparameters .= '&month=' . $_POST['month'] . '&year=' . $_POST['year'] . '&store=' . $_POST['store'];
            $pdfparameters .= '&user=' . $_POST['user'];
            $estimates[0]->pdfparameters = $pdfparameters;
            //pre($estimates);
        }

        foreach($estimates as $estimate) {
            $estimate->comments = [];
            $estimate->comments = $this->getEstimateComments($estimate->id);
        }
        
        //echo $query . $where . $orderby;

        return $estimates;

    }

    function getEstimatePdfUrl($id, $code) {
        $urlpdf = '/mpdf60.php?controller=store&legalText=getEstimateFooterLegalText';
        $urlpdf .= '&nofooterdate&opt=getPdfEstimate&id=' . $id;
        $urlpdf .= '&logowidth=150&pdfName=Presupuesto_' . $code;
        return $urlpdf;
    }

    function getEstimateCommentsTable() {
        return $this->estimateCommentsTable;
    }

    function getEstimateComments($estimateId) {
        $query = "SELECT * FROM " . $this->estimateCommentsTable . " e JOIN " . $this->usersTable .  " u  ON e.created_by = u.id WHERE estimateid = $estimateId";
        echo $query;
        return $this->wpdb->get_results($query);
    }

    function canIseeTheInitialSale($storeId) {
        global $user;
        if(userWithPrivileges() || $user->getStoreId() == $storeId) {
            return true;
        }

        return false;
    }

    function getEstimateData($id = 0) {
        $estimateId = $id > 0 ? $id : $_GET['id'];
        $data = $this->wpdb->getOneRow($this->estimatesTable, $estimateId);
        $data->creator = getUsername($data->created_by); // Ahora lo usamos (getPdfStoreOrder)
        $data->comments = $this->getNewPdfComments($estimateId, $this->estimateCommentsTable, "estimateid");
        //$data->products = $this->getEstimateProducts();
        $data->products = [];

        return $data;
    }

    function updateEstimate($whereField = 'id') {
        $response = $this->wpdb->save_edit($this->estimatesTable, false, $whereField);
        //$response['pdfurl'] = $this->getEstimatePdfUrl($_POST['id'], $_POST['code']);
        return $response;
    }

    function checkIfAnotherEstimateRegisteredWithTel($tel) {
        $query = 'SELECT * FROM ' . $this->estimatesTable . ' WHERE tel="' . $tel. '" LIMIT 1';
        $data = $this->wpdb->get_results($query);
        if (count($data) > 0) {
            return true;
        }

        return false;
    }
}