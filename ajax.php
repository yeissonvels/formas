<?php
error_reporting(0);
include('functions.php');

class AjaxRequest
{
    protected $wpdb;
    protected $user;

    function __construct()
    {
        if (is_user_logged_in()) {
            global $user;
            $this->wpdb = new WPDB();
            $this->user = $user;
            $this->readRequest();
        } else {
            redirectToIndex();
        }

    }

    function readRequest()
    {
        if (isset($_GET['op'])) {
            $request = $_GET['op'];
            $options = array(
                'updateMenu' => 'updateMenu',
                'updateDynamicMenu' => 'updateDynamicMenu',
                'getMenuItemData' => 'getMenuItemData',
                'updateMenuParents' => 'updateMenuParents',
                'deleteMenuItem' => 'deleteMenuItem',

            );
            // Llamamos a la funcion de forma dinamica
            if (method_exists($this, $options[$request])) {
                $method = $options[$request];
                $this->$method();
            } else {
                echo "No existe la función: " . $request;
            }

        } else if (isset($_POST['op']) || isset($_POST['opt'])) {
            $request = isset($_POST['op']) ? $_POST['op'] : $_POST['opt'];
            $options = array(
                'savepdf' => 'savePdf',
                'uploadIcon' => 'uploadIcon',
                'uploadIcon2' => 'uploadIcon2',
                'saveMenuItem' => 'saveMenuItem',
                'uploadFiles' => 'uploadFiles',
                'uploadPhoto' => 'uploadPhoto',
                'deleteFileFromDir' => 'deleteFileFromDir',
                'listDir' => 'listFilesFromDir',
                'saveUserPermissions' => 'saveUserPermissions',
                'comprobateUsername' => 'comprobateUsername',
                'comprobateEmail' => 'comprobateEmail',
                'saveMenuPermissions' => 'saveMenuPermissions',
                'getClassMethods' => 'getClassMethods',
                'getCustomers' => 'getCustomers',
                'getOrder' => 'getOrder',
                'getOrdersList' => 'getOrdersList',
                'save_order' => 'save_order',
                'save_edit_order' => 'save_edit_order',
                'save_products' => 'save_products',
                'saveComment' => 'saveComment',
                'saveSale' => 'saveSale',
                'updateSale' => 'updateSale',
                'checkPdfCode' => 'checkPdfCode',
                'savePdfCode' => 'savePdfCode',
                'updatePdfCode' => 'updatePdfCode',
                'uploadOrderImage' => 'uploadOrderImage',
                'uploadOrderPdf' => 'uploadOrderPdf',
                'getIncidenceData' => 'getIncidenceData',
                'save_incidence' => 'save_incidence',
                'saveIncidenceComment' => 'saveIncidenceComment',
                'saveIncidenceItems' => 'saveIncidenceItems',
                'saveInternProducts' => 'saveInternProducts',
                'unlinkFile' => 'unlinkFile',
                'loadOrdersWidget' => 'loadOrdersWidget',
                'searchSales' => 'searchSales',
                'getAutocompleteCode' => 'getAutocompleteCode',
                'getAutocompleteMainCode' => 'getAutocompleteMainCode',
                'getAutocompleteIncidenceCode' => 'getAutocompleteIncidenceCode',
                'getAutocompleteProduct' => 'getAutocompleteProduct',
                'saveOrderPay' => 'saveOrderPay',
                'updateOrderPay' => 'updateOrderPay',
                'saveTotalValidation' => 'saveTotalValidation',
                'saveValidationPayment' => 'saveValidationPayment',
                'getValidationPaymentData' => 'getValidationPaymentData',
                'deleteTotalValidation' => 'deleteTotalValidation',
                'deleteValidation' => 'deleteValidation',
                'searchEmails' => 'searchEmails',
                'uploadLastDeliveryDocument' => 'uploadLastDeliveryDocument',
                'searchIncidences' => 'searchIncidences',
                'cancelSale' => 'cancelSale',
                'savecommissionPayment' => 'savecommissionPayment',
                'deleteCommission' => 'deleteCommission',
                'savePdfComment' => 'savePdfComment',
                'pdfYetPrinted' => 'pdfYetPrinted',
                'adjustPendingPay' => 'adjustPendingPay',
                'getAdjustPendingPayData' => 'getAdjustPendingPayData',
                'updateAdjustPendingPay' => 'updateAdjustPendingPay'
            );

            if (method_exists($this, $options[$request])) {
                $method = $options[$request];
                $this->$method();
            } else {
                echo "No existe la función: " . $request;
            }
        }

    }

    function saveMenuItem()
    {
        $model = new MenuModel();

        if ($_POST['id'] > 0) {
            $model->editMenuItem();
        } else {
            unset($_POST['id']); // Quitamos la variable id que vale 0, ya que vamos a guardar y se genera por auto_increment
            $model->saveMenuItem();
        }
    }

    function updateDynamicMenu()
    {
        $model = new MenuController();
        $model->updateDinamicMenu();
    }

    /**
     * Función usada para recuperar los datos de un item del nuevo menu
     */
    function getMenuItemData()
    {
        $model = new MenuModel();
        $data = $model->getMenuItem();

        echo json_encode($data);
    }

    function updateMenu()
    {
        $menu = new MenuController();

        $menu->createMenu();
    }

    function deleteMenuItem()
    {
        $model = new MenuModel();
        $model->deleteMenuItem();

    }

    function updateMenuParents()
    {
        $controller = new MenuController();
        $parents = $controller->getItemParents();

        echo '<option value="0">Root</option>';
        foreach ($parents as $pa) {
            echo '<option value="' . $pa->id . '">' . $pa->label . '</option>';
        }
    }

    function saveApartmentIncomeFile()
    {
        apartmentIncomeFile();
    }

    function saveJettaPaymentFile()
    {
        $controller = new JettaPaymentController();
        $controller->saveJettaPaymentFile();
    }

    function savePdf()
    {
        uploadPdf();
    }

    function uploadIcon()
    {
        uploadIcon();
    }

    function uploadIcon2()
    {
        uploadIcon2();
    }

    function getTruckIncomes()
    {
        $user = $this->user;
        $criteria = $_GET['search'];

        $income = new IncomeModel();

        $result = $income->getIncomesByAjaxSearch($criteria);
        $view_path = getGetValue('controller_view_path');

        include($view_path . '/show_incomes.html.php');
        incomesComplementTable($result, $criteria);
    }

    function saveAdvancePayment()
    {
        $model = new AdvanceController();
        $model->saveAdvancePayment();
    }

    function uploadFiles($dir, $id, $pdfname = "", $showmsg)
    {
        require_once "classes/UploadFile.php";
        $directory = "";

        // dir puede ser una cadena images, pdf o un array 0 => 'images', 1 => 'secondary'
        if (is_array($dir)) {
            $directory = $dir[0];
        } else {
            $directory = $dir;
        }

        $options = array(
            'dirs' => array(
                0 => 'uploaded-files',
                1 => $directory,
                2 => $id,
            )
        );

        // Si queremos tener un directorio más
        if (is_array($dir)) {
            if (isset($dir[1])) {
                array_push($options['dirs'], $dir[1]);
            }
        }

        $loader = new LoadFile($options);
        return $loader->uploadFiles(false, $id, $pdfname, $showmsg);

    }

    function uploadPhoto()
    {
        require_once "classes/UploadFile.php";

        $options = array(
            'dirs' => array(
                0 => 'nande',
                1 => 'images'
            )
        );

        $loader = new LoadFile($options);
        $loader->uploadFiles(true);

    }

    function deleteFileFromDir()
    {
        $file = $_POST['file'];
        $dir = $_POST['dir'];

        if (file_exists($file)) {
            unlink($file);
            listDirectory($dir, true);
        }
    }

    /**
     * Permite listar el contenido de un directorio desde una llamada ajax
     */
    function listFilesFromDir()
    {
        $dir = $_POST['dir'];
        listDirectory($dir, true);
    }

    function saveUserPermissions()
    {
        $controller = new $_POST['controller']();
        $aux = $_POST;
        unset($_POST);
        $controller->deleteUserMenuPermission($aux['userid']);
        foreach ($aux['ids'] as $menuitem) {
            $_POST['item_id'] = $menuitem;
            $_POST['user_id'] = $aux['userid'];
            $_POST['created_by'] = $aux['createdby'];
            $controller->saveItemMenuPermission();
        }
    }

    function saveMenuPermissions()
    {
        $permissions = array();
        $ids = $_REQUEST['ids'];

        foreach ($ids as $id) {
            $aux = explode("#", $id);
            $permissions[$aux[0]][] = $aux[1];
        }

        $permissions = json_encode($permissions);
        file_put_contents(PERMISSIONS_FILE, $permissions);
    }

    function comprobateUsername()
    {
        $username = $_POST['username'];
        $controller = new $_POST['controller']();

        if ($controller->existUsername($username)) {
            echo "si";
        } else {
            echo "no";
        }
    }

    function comprobateEmail()
    {
        $email = $_POST['email'];
        $controller = new $_POST['controller']();
        if ($controller->existEmail($email)) {
            echo "si";
        } else {
            echo "no";
        }
    }

    function getClassMethods() {
        $html = "";
        $controller = new UrlFriendlyController();
        $classname = ucfirst($_REQUEST['classname']) . "Controller";
        echo $classname . "<br>";
        $methods = $controller->getClassMethods($classname);
        foreach ($methods as $method) {
            $html .= '<option value="' . $method . '">' . $method . '</option>';
        }

        echo $html;
    }

    function getCustomers() {
        $controllername = $_POST['controller'];
        $controller = new CustomerController();
        $criteria = $_POST['criteria'];
        $controller->dynamic_customers($criteria, $controllername);
    }

    /**
     * Busca los pedidos por código, nombre de cliente, mes, año, tienda
     */
    function getOrder() {
        $controllername = $_POST['controller'];
        $controller = new OrderController();
        $criteria = $_POST['criteria'];
        $month = $_POST['month'];
        $year = $_POST['year'];
        $store = $_POST['store'];

        $controller->dynamic_order($criteria, $month, $year, $store, $controllername);

    }

    /**
     * Obtiene un listado con los pedidos por filtros
     */
    function getOrdersList() {
        $controller = new OrderController();
        $controller->dynamic_orders_list();
    }

    function save_edit_order() {
        $controller = new OrderController();
        if (isset($_POST['total'])) {
            $_POST['total'] = formatNumberToDB($_POST['total']);
        }
        if (isset($_POST['pendingpay'])) {
            $_POST['pendingpay'] = formatNumberToDB($_POST['pendingpay']);
        }
        $response = $controller->save_edit_order(false);
        echo json_encode($response);
    }

    function save_order() {
        $controller = new OrderController();
        if (isset($_POST['total'])) {
            $_POST['total'] = formatNumberToDB($_POST['total']);
        }
        if (isset($_POST['pendingpay'])) {
            $_POST['pendingpay'] = formatNumberToDB($_POST['pendingpay']);
        }
        $response = $controller->save_order(false);
        unset($controller);
        echo json_encode($response);
    }

    function save_products() {
        $controller = new OrderController();
        $response = $controller->save_products();
        echo json_encode($response);
    }

    /**
     * Desde esta función podemos guardar los comentarios internos, comentarios de clientes y también
     * registrar un comentario por cada cambio de estado del pedido. Listo para entrega o Entregado.
     */
    function saveComment() {
        $commenttype = $_POST['comment_type'];
        $deliverydate = from_calendar_to_date($_POST['deliverydate']);
        $status = $_POST['statuscomment'];
        unset($_POST['statuscomment']);
        unset($_POST['deliverydate']);
        $orderid = $_POST['orderid'];

        if ($status == 1) {
            $_POST['readydelivery'] = 1;
        } else if ($status == 2 || $status == 3) {
            $_POST['delivered'] = 1;
        }

        $controller = new OrderController();
        $controller->saveComment();

        // Actualizamos el estado del pedido en la base de datos se guarda 0 por defecto
        // Nota: Ya se hace desde la propia función js saveComment con un trigger $('#btnsaveorder').trigger('click');
        if ($status > 0) {
            $controller->updateStatus($orderid, $status, $deliverydate);
        }

        $result = $controller->getComments($orderid, $commenttype);
        $order = new Order();
        if ($commenttype == 0) {
            $comments['interns'] = $result;
            $order->setComments($comments);
            $data['data'] = $order;
            include (VIEWS_PATH . 'order/order_comments' . VIEW_EXT);
        } else {
            $comments['customer'] = $result;
            $order->setComments($comments);
            $data['data'] = $order;
            include (VIEWS_PATH . 'order/customer_comments' . VIEW_EXT);
        }

    }

    function saveSale() {
        global $user;
        //$_POST['salecomment'] = strip_tags(str_ireplace('"', "''", $_POST['salecomment']));
        $_POST['saledate'] = from_calendar_to_date($_POST['saledate']);
        $_POST['storeid'] = $user->getStoreid();
        $_POST['created_on'] = date('Y-m-d H:i:s');
        $_POST['created_by'] = $user->getId();
        // El formulario de guardado y actualización es el mismo, en el guardado el id viene vacío.
        if (isset($_POST['id'])) {
            unset($_POST['id']);
        }

        $controller = new StoreController();
        if ($_POST['saletype'] != 0) {
            $code = $controller->getCodeByParentId();
            $_POST['code'] = $code;
        }
        $result = $controller->saveSale();
        echo json_encode($result);
    }

    function updateSale() {
        //$_POST['salecomment'] = strip_tags(str_ireplace('"', "''", $_POST['salecomment']));
        $_POST['saledate'] = from_calendar_to_date($_POST['saledate']);
        $controller = new StoreController();
        if ($_POST['saletype'] != 0) {
            $code = $controller->getCodeByParentId();
            $_POST['code'] = $code;
        } else {
            $_POST["parentcode"] = "";
        }
        $result = $controller->updateSale();
        // Verificamos si han cambiado el código del pdf y si existe un pedido asociado actualizamos el código
        $pdfid = $_POST['id'];
        $code = $_POST['code'];
        $controller = new OrderController();
        $orderinfo = $controller->getOrderByPdfId($pdfid);
        if ($orderinfo) {
            if ($orderinfo[0]->code != $code) {
                $controller->updateOrderCode($code, $pdfid);
            }
        }
        echo json_encode($result);
    }

    /**
     * fieldtype: puede contener los valores (code o id)
     * code usada por checkCode y id por checkParentCode
     */
    function checkPdfCode() {
        $code = $_POST['code'];
        $fieldname = $_POST['fieldname'];
        $controller = new StoreController();

        if ($controller->checkPdfCode($code, $fieldname)) {
            echo "si";
        } else {
            echo "no";
        }
    }

    function savePdfCode() {
        global $user;
        $_POST['storeid'] = $user->getStoreid();
        $_POST['created_on'] = date('Y-m-d H:i:s');
        $_POST['created_by'] = $user->getId();
        // Guardamos los datos del pdf
        $controller = new StoreController();
        $result = $controller->savePdfCode();
        echo json_encode($result);
    }

    function updatePdfCode() {
        $pdfid = $_POST['id'];
        $code = $_POST['code'];
        $controller = new StoreController();
        $result = $controller->updatePdfCode();
        // Verificamos si han cambiado el código del pdf y si existe un pedido asociado actualizamos el código
        $controller = new OrderController();
        $orderinfo = $controller->getOrderByPdfId($pdfid);
        if ($orderinfo) {
            if ($orderinfo[0]->code != $code) {
                $controller->updateOrderCode($code, $pdfid);
            }
        }
        echo json_encode($result);
    }

    function uploadOrderPdf() {
        $alloweds = array('pdf', 'PDF', 'doc', 'docx');
        $dir = "pdfs";
        $orderid = $_POST['orderid'];
        $time = date('H_i_s');
        $uploadedOn = date('d_m_Y-' . $time);

        $aux = $_FILES['file-0']['name'];
        $fileExt = explode('.', $aux);
        $fileExt = $fileExt[1];

        // Antes la nota de entrega era solamente en PDF, ahora puede ser doc, docx
        $pdfname = "Propuesta_de_pedido_" . $orderid . "-" . $uploadedOn . "." . $fileExt;

        // Si no es pdf mostramos un error
        if (!in_array($fileExt, $alloweds)) {
            errorMsg("Error al subir el archivo! ¿Es correcto el formato (PDF, DOC, DOCX)?");
            exit;
        }

        // Se trata de la última nota de entrega generada desde el almacén
        if (isset($_POST['last'])) {
            $dir = 'lastdeliveryfiles';
            $pdfname = "Nota_de_entrega_" . $orderid . "." . $fileExt;
        }

        $response = $this->uploadFiles($dir, $orderid, $pdfname, false);
        unset($_POST['orderid']);
        unset($_POST['code']);

        if ($response) {
            // Icono dinámico
            $icons = array(
                'rtf' => 'word',
                'doc' => 'word',
                'docx' => 'word',
                'pdf' => 'pdf',
            );
            if (isset($_POST['last'])) {
                global $user;
                $html = confirmationMessage('Archivo cargado correctamente: ', false);
                $path = '/uploaded-files/' . $dir . '/' . $orderid . '/' . $pdfname;
                $html .= '<a href="' . $path . '" target="_blank">' . icon($icons[$fileExt], false) . '</a><br><br>';
                unset($_POST['comment']);
                unset($_POST['last']);
                $_POST['id'] = $orderid;
                $_POST['finishdeliveryfile'] = $pdfname;
                $_POST['finishdeliverycreatedon'] = date('Y-m-d H:i:s');
                $_POST['finishdeliverycreatedby'] = $user->getId();
                $controller = new OrderController();
                $controller->save_edit_db_order(false, true);
                echo $html;
            } else {
                $controller = new StoreController();
                // Obtenemos los comentarios anteriores
                //$oldComment = $controller->getPdfComments($orderid);
                $existPdf = $controller->getPdfUploadDate($orderid);

                // Guardamos la fecha en la que se subió la nota de entrega (sólo la primera vez)
                if ($existPdf == '0000-00-00 00:00:00') {
                    $_POST['pdf_uploaded_on'] = date('Y-m-d ' . str_ireplace('_', ':', $time));
                }

                $_POST['id'] = $orderid;
                $_POST['pdfname'] = $pdfname;

                // Nos vale la función updatePdfCode para actualizar el nombre del pdf
                $controller->updatePdfCode();

                $html = 'Archivo cargado correctamente: ';
                $path = '/uploaded-files/' . $dir . '/' . $orderid . '/' . $pdfname;
                $html .= '<a href="' . $path . '" target="_blank">' . icon($icons[$fileExt], false) . '</a><br><br>';
                //$html .= '<input type="button" class="btn btn-success" value="Enviar PDF al almacén" onclick="confirmSend(' . $orderid . ')">';
                confirmationMessage($html);
                $config = array(
                    'divresponse' => 'ajax-content',
                    'excludes' => array($pdfname)
                );
                echo '<div>';
                echo '<b>Otras versiones de la propuesta de pedido:</b> <br>';
                listDirectory('uploaded-files/' . $dir . '/' . $orderid, true, $config);
                echo '</div>';
            }
        } else {
            errorMsg("Error al subir el archivo! ¿Es correcto el formato?");
        }
    }

    /**
     * En principio se había pensado para imágenes pero ahora es posible subir rt, doc, docx
     */
    function uploadOrderImage() {
        $alloweds = array('png', 'PNG', 'jpg', 'JPG', 'doc', 'docx', 'rtf', 'pdf');
        $docs = array('doc', 'docx', 'rtf', 'pdf');
        $imageType = $_POST['imageType'];
        unset($_POST['imageType']);

        if ($imageType == 'primary') {
            $dir = "images";
        } else {
            array_push($alloweds, 'rtf');
            array_push($alloweds, 'doc');
            array_push($alloweds, 'docx');
            $dir = array('images', 'secondary');
        }

        $orderid = $_POST['orderid'];
        $aux = $_FILES['file-0']['name'];
        $fileExt = explode('.', $aux);
        $fileExt = $fileExt[1];

        // Si no es imagen mostramos un error
        if (!in_array($fileExt, $alloweds)) {
            if ($imageType == 'primary') {
                $result['sucess'] = 0;
                $result['html'] = errorMsg("Error al subir el archivo! ¿Es correcto el formato (png, jpg)?", false);
                echo json_encode($result);
            } else {
                errorMsg("Error al subir el archivo! ¿Es correcto el formato (png, jpg)?");
            }
            exit;
        }


        $iName = 'Imagen_';
        $fExt = getFileExtension($fileExt);
        if (in_array($fExt, $docs)) {
            $iName = 'Documento_';
        }


        $imageName = $iName . $orderid . "-" . date('d_m_Y-H_i_s') . "." . $fileExt;
        $response = $this->uploadFiles($dir, $orderid, $imageName, false);
        unset($_POST['orderid']);

        if ($response) {
            // La imagen principal la guardamos en la raiz images/orderid
            if ($imageType == "primary") {
                $controller = new StoreController();
                // Obtenemos los comentarios anteriores
                $_POST['id'] = $orderid;
                $_POST['image'] = $imageName;

                // Nos vale la función updatePdfCode para actualizar el nombre del pdf
                $controller->updatePdfCode();

                $html = 'Archivo cargado correctamente: ';
                $path = '/uploaded-files/' . $dir . '/' . $orderid . '/' . $imageName;
                $html .= '<a href="' . $path . '" target="_blank">' . icon('image', false) . '</a><br><br>';
                //$html .= '<input type="button" class="btn btn-success" value="Enviar PDF al almacén" onclick="confirmSend(' . $orderid . ')">';

                $result['success'] = 1;
                $result['html'] = confirmationMessage($html, false);
                echo json_encode($result);
            } else {
                // La imagen principal la guardamos en la raiz images/orderid/secondary
                $config = array(
                    'divresponse' => $_POST['divresponse'],
                    'excludes' => array()
                );
                listDirectory('uploaded-files/images/' . $orderid . '/secondary', true, $config);
            }
        } else {
            $result['sucess'] = 0;
            $result['html'] = errorMsg("Error al subir el archivo! ¿Es correcto el formato (png, jpg)?", false);
        }

    }

    function getIncidenceData() {
        $id = $_POST['id'];
        $_GET['orderid'] = $_POST['orderid'];

        $tpl = VIEWS_PATH . 'order/' . 'new_incidence' . VIEW_EXT;
        if ($id > 0) {
            $controller = new OrderController($id);
            $ajaxincidence = $controller->getIncidenceData($id);

            $_POST['incidenceid'] = $id;
            $incidenceComments = $controller->getIncidenceComments();
        }

        $controller = new ProductController();
        $products = $controller->getProducts(false);
        $categories = getCategories(false);
        $ajaxProducts = $products;
        $ajaxCategories = $categories;

        include ($tpl);
    }

    function save_incidence() {
        $_POST['incidencedate'] = from_calendar_to_date($_POST['incidencedate']);
        $_POST['fixed_on'] = from_calendar_to_date($_POST['fixed_on']);
        $controller = new OrderController();

        $_POST['pendingpay'] = formatNumberToDB($_POST['pendingpay']);

        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $result = $controller->save_edit_incidence();
        } else {
            global $user;
            $_POST['created_by'] = $user->getId();
            $result = $controller->save_incidence();
        }

        //if ($_POST['orderid'] == 0) {
            $order = new Order();
            $incidences = $controller->getIncidences($_POST['orderid']);
            $order->setIncidences($incidences);
            $data['data'] = $order;
            include (VIEWS_PATH . 'order/incidences' . VIEW_EXT);
            $result['html'] = $htmlincidences;
        //}

        echo json_encode($result);

    }

    function saveIncidenceComment() {
        global $user;
        $tpl = VIEWS_PATH . 'order/incidence_comments' . VIEW_EXT;
        $_POST['created_on'] = date('Y-m-d H:i:s');
        $_POST['created_by'] = $user->getId();
        $controller = new OrderController();
        $result = $controller->save_incidence_comment();
        $incidenceComments = $controller->getIncidenceComments();
        include ($tpl);
    }

    /**
     * Los productos de la incidencia que se deben fabricar
     */
    function saveInternProducts() {
        $response['saved'] = 0;
        $controller = new OrderController();
        $result = $controller->saveInternProducts();

        if ($result) {
            $response['saved'] = 1;
        }

        echo json_encode($response);
    }

    /**
     * Los items para la nota de entrega
     */
    function saveIncidenceItems() {
        $response['saved'] = 0;
        $controller = new OrderController();
        $result = $controller->saveIncidenceItems();
        $id = $_POST['incidenceid'];
        $pdfName = $_POST['incidencetype'] == 0 ? 'Incidencia_' . $id : 'Entrega_parcial_' . $id;
        $url = '/mpdf60.php?controller=order&opt=generateDeliveryNote&order=' . $_POST['orderid'];
        $url .= '&incidenceid=' . $_POST['incidenceid'] . '&pdfName=' . $pdfName;

        $html = '<a href="' . $url . '" class="cursor-pointer" disabled="disabled" target="_blank" style="font-size: 40px;">' . icon('pdf') . '</a>';

        if ($result) {
            $response['saved'] = 1;
            $response['link'] = $html;
        }

        echo json_encode($response);
    }

    function unlinkFile() {
        $file = $_POST['file'];
        $path = $_POST['path'];
        $config = array(
            'divresponse' => $_POST['divresponse'],
            'excludes' => array()
        );
        if (file_exists($file)) {
            unlink($file);
            echo json_encode(array("response" => "Ok"));
        }
    }

    function loadOrdersWidget() {
        $controller = new OrderController();
        $widget = $controller->getOrdersWidget();
        echo json_encode($widget);
    }

    function searchSales() {
        $tpl = VIEWS_PATH . "store/dynamic_pdfs" . VIEW_EXT;
        $controller = new StoreController();
		
		// Si es enero o febrero como a los empleados les ocultamos el filtro de año,
		// en el selector les hemos activado noviembre y diciembre para que puedan acceder
		// a ventas del año anterior
		if (date('m') < 3) {
			if (!userWithPrivileges() && $_POST['month'] >= 11) {
				$_POST['year'] = date('Y') - 1;
			}
		}
		
        if ($_POST['code'] != "") {
            $data = $controller->getPdfChildren();
        } else {
            $data = $controller->getPdfs();
        }


        include($tpl);
    }

    function getAutocompleteCode() {
        $noCancelled = false;
        if (isset($_POST['nocancelled'])) {
            $noCancelled = true;
        }
        $controller = new StoreController();
        $results = $controller->getAutocompleteCode($noCancelled);

        ?>
        <ul id="code-list" class="list-group">
        <?php
        if (count($results) > 0) {
            foreach($results as $result) {
                $code = $result->code . ' (' . americaDate($result->saledate, false) . ') ' . $result->customer;
                ?>
                <li class="list-group-item cursor-pointer" onClick="selectCode('<?php echo $result->id; ?>', '<?php echo $code; ?>');"><?php echo $code; ?></li>
            <?php } ?>
        <?php } else { ?>
            <li class="list-group-item cursor-pointer"><span class="red-color">Sin resultados</span></li>
        <?php } ?>
        </ul><?php
    }
	
	function getAutocompleteProduct() {
		$noCancelled = false;
		$autoid = $_POST['id'];
        if (isset($_POST['nocancelled'])) {
            $noCancelled = true;
        }
        $controller = new ProductController();
        $results = $controller->getAutocompleteCode($noCancelled);

        ?>
        <ul id="code-list" class="list-group">
        <?php
        if (count($results) > 0) {
            foreach($results as $result) {
                $code = $result->productname;
                ?>
                <li class="list-group-item cursor-pointer" onClick='selectCode("<?php echo $autoid ?>", <?php echo json_encode($result); ?>);'><?php echo $code; ?></li>
            <?php } ?>
        <?php } else { ?>
            <li class="list-group-item cursor-pointer"><span class="red-color">Sin resultados</span></li>
        <?php } ?>
        </ul><?php
	}

    /**
     * Para el buscador de la home
     */
    function getAutocompleteMainCode() {
        $controller = new StoreController();
        $results = $controller->getAutocompleteMainCode();

        ?>
        <ul id="code-list" class="list-group">
        <?php
        if (count($results) > 0) {
            foreach($results as $result) {
                $code = $result->code . ' (' . americaDate($result->saledate, false) . ') ' . $result->customer;
                ?>
                <li class="list-group-item cursor-pointer" onClick="selectCodeMain('<?php echo $result->id; ?>', '<?php echo $code; ?>');"><?php echo $code; ?></li>
            <?php } ?>
        <?php } else { ?>
            <li class="list-group-item cursor-pointer"><span class="red-color">Sin resultados</span></li>
        <?php } ?>
        </ul><?php
    }

    function getAutocompleteIncidenceCode() {
        $controller = new OrderController();
        $results = $controller->getAutocompleteIncidenceCode();

        ?>
        <ul id="code-list" class="list-group">
        <?php
        if (count($results) > 0) {
            foreach($results as $result) {
                $code = "";
                if (isset($result->code)) {
                    $code = $result->code;
                }
                $myCode = $code . ' (' . americaDate($result->incidencedate, false) . ') ' . $result->customer;
                ?>
                <li class="list-group-item cursor-pointer" onClick="selectCode('<?php echo $result->id; ?>', '<?php echo $myCode; ?>');"><?php echo $myCode; ?></li>
            <?php } ?>
        <?php } else { ?>
            <li class="list-group-item cursor-pointer"><span class="red-color">Sin resultados</span></li>
        <?php } ?>
        </ul><?php
    }

    function saveOrderPay() {
        global $user;
        $controller = new StoreController();
        $_GET['id'] = $_POST['parentcode'];
        $parentData = $controller->getPdfData();
        $_POST['saletype'] = 2;
        $_POST['saledate'] = from_calendar_to_date($_POST['saledate']);
        //$_POST['storeid'] = $user->getStoreid();
        $_POST['storeid'] = $parentData->storeid;
        $_POST['created_on'] = date('Y-m-d H:i:s');
        $_POST['created_by'] = $user->getId();
        $_POST['code'] = $parentData->code;
        // El formulario de guardado y actualización es el mismo, en el guardado el id viene vacío.
        if (isset($_POST['id'])) {
            unset($_POST['id']);
        }

        /*pre($_POST);
        exit;*/

        //$code = $controller->getCodeByParentId();
        //$_POST['code'] = $code;
        $result = $controller->saveSale();
        echo json_encode($result);
    }


    function updateOrderPay() {
        $_POST['saledate'] = from_calendar_to_date($_POST['saledate']);
        $controller = new StoreController();
        $code = $controller->getCodeByParentId();
        $_POST['code'] = $code;
        $result = $controller->updateSale();
        echo json_encode($result);
    }

    /**
     * Confirmación de total por parte de contabilidad
     */
    function saveTotalValidation() {
        global $user;
        $userId = $user->getId();
        $response = array();
        $_POST['total_checked_system_date'] = date('Y-m-d H:i:s');
        $_POST['total_checked_by'] = $userId;
        $auxCheckedOn = $_POST['total_checked_on'];
        $_POST['total_checked_on'] = from_calendar_to_date($_POST['total_checked_on']);
        $controller = new StoreController();
        // Nos sirve updatePdfCode porque hace un save_edit en DB.
        $result = $controller->updatePdfCode();
        if ($result['updated'] == 1) {
            $response['updated'] = $result['updated'];
            $title = 'Validado por ' . getUsername($userId) . ' el ' . $auxCheckedOn . '<br>';
            $title .= '<b>Nota: </b>' . $_POST['total_checked_note'];
            $html = '<a class="withqtip cursor-pointer" title="' . $title . '">' . icon('checked') . ' ' . icon('comments') . '</a>';
            $html .= '<br><a class="cursor-pointer red-color" onclick="deleteTotalValidation(' . $_POST['id'] . ');">Eliminar' . icon('delete') . '</a>';
            $response['html'] = $html;
        } else {
            $response['result'] = 'Error al realizar la validación';
        }


        echo json_encode($response);

    }

    /**
     * Confirmación de pagos por parte de contabilidad
     */
    function saveValidationPayment() {
        global $user;
        $userId = $user->getId();
        $response = array();
        $_POST['accounting_checked_system_date'] = date('Y-m-d H:i:s');
        $_POST['accounting_checked_by'] = $userId;
        $auxCheckedOn = $_POST['accounting_checked_on'];
        $_POST['accounting_checked_on'] = from_calendar_to_date($_POST['accounting_checked_on']);
        $controller = new StoreController();
        // Nos sirve updatePdfCode porque hace un save_edit en DB.
        $result = $controller->updatePdfCode();
        if ($result['updated'] == 1) {
            $response['updated'] = $result['updated'];
            $title = 'Validado por ' . getUsername($userId) . ' el ' . $auxCheckedOn . '<br>';
            $title .= '<b>Nota: </b>' . $_POST['accounting_checked_note'];
            $html = '<a class="withqtip cursor-pointer" title="' . $title . '">' . icon('checked') . ' ' . icon('comments') . '</a>';
            $html .= '<br><a class="cursor-pointer" data-target="#checkPayment" data-toggle="modal" onclick="changeValidate(' . $_POST['id'] . ');">Cambiar' . icon('edit') . '</a>';
            $html .= '<br><a class="cursor-pointer red-color" onclick="deleteValidation(' . $_POST['id'] . ');">Eliminar' . icon('delete') . '</a>';
            $response['html'] = $html;
        } else {
            $response['result'] = 'Error al realizar la validación';
        }


        echo json_encode($response);

    }

    function savecommissionPayment() {
        global $user;
        $userId = $user->getId();
        $response = array();
        $_POST['commissionpayed'] = 1;
        $_POST['commission_validated_by'] = $userId;
        $auxCheckedOn = $_POST['commission_payed_on'];
        $_POST['commission_payed_on'] = from_calendar_to_date($_POST['commission_payed_on']);
        $controller = new StoreController();
        // Nos sirve updatePdfCode porque hace un save_edit en DB.
        $result = $controller->updatePdfCode();

        if (isset($result['updated']) && $result['updated'] == 1) {
            $response['updated'] = $result['updated'];
            $title = 'Por <b>' . getUsername($userId) . '</b> el ' . $auxCheckedOn . '<br>';
            $html = icon('checked', false) . '<span class="green-color"> Propuesta validada</span> ';
            $html .= '<a class="withqtip cursor-pointer" title="' . $title . '">' . icon('comments') . '</a>';
            $html .= '<br><a class="cursor-pointer red-color" onclick="deletecommission(' . $_POST['id'] . ');">Eliminar' . icon('delete') . '</a>';
            $response['html'] = $html;
        } else if (isset($result['duplicated']) && $result['duplicated']) {
            $response['duplicated'] = $result['duplicated'];
        }else {
            $response['result'] = 'Error al realizar la validación';
        }


        echo json_encode($response);
    }

    function getValidationPaymentData() {
        $controller = new StoreController();
        $data = $controller->getValidationPaymentData();
        $data->accounting_checked_on = americaDate($data->accounting_checked_on, false);
        echo json_encode($data);
    }

    function deleteTotalValidation() {
        $_POST['total_checked_system_date'] = '0000-00-00 00:00:00';
        $_POST['total_checked_on'] = '0000-00-00 00:00:00';
        $_POST['total_checked_by'] = 0;
        $_POST['total_checked_note'] = '';
        $controller = new StoreController();
        // Nos sirve updatePdfCode porque hace un save_edit en DB.
        $result = $controller->updatePdfCode();
        if ($result['updated'] == 1) {
            $html =  '<span class="red-color">No validado<br>';
            $html .= '<input type="button" value="Validar total" class="btn btn-success" style="font-size: 8px;" data-target="#checkTotal" data-toggle="modal" onclick="setTotalId(' . $_POST['id'] . ')">';
            $response['updated'] = $result['updated'];
            $response['html'] = $html;
        } else {
            $response['result'] = 'Error al realizar la validación';
        }
        echo json_encode($response);
    }

    function deleteValidation() {
        $_POST['accounting_checked_system_date'] = '0000-00-00 00:00:00';
        $_POST['accounting_checked_on'] = '0000-00-00 00:00:00';
        $_POST['accounting_checked_by'] = 0;
        $_POST['accounting_checked_note'] = '';
        $controller = new StoreController();
        // Nos sirve updatePdfCode porque hace un save_edit en DB.
        $result = $controller->updatePdfCode();
        if ($result['updated'] == 1) {
            $html =  '<span class="red-color">No validado<br>';
            $html .= '<input type="button" value="Validar pago" class="btn btn-success" style="font-size: 8px;" data-target="#checkPayment" data-toggle="modal" onclick="setPaymentId(' . $_POST['id'] . ')">';
            $response['updated'] = $result['updated'];
            $response['html'] = $html;
        } else {
            $response['result'] = 'Error al realizar la validación';
        }
        echo json_encode($response);
    }

    function deleteCommission() {
        $_POST['commissionpayed'] = 0;
        $_POST['commission_payed_on'] = '0000-00-00 00:00:00';
        $_POST['commission_validated_by'] = '';
        $controller = new StoreController();
        // Nos sirve updatePdfCode porque hace un save_edit en DB.
        $result = $controller->updatePdfCode();
        if ($result['updated'] == 1) {
            $html =  '<span class="red-color">No validada<br>';
            $html .= '<input type="button" value="Validar propuesta" class="btn btn-success" style="font-size: 8px;" data-target="#checkcommission" data-toggle="modal" onclick="setcommissionId(' . $_POST['id'] . ')">';
            $response['updated'] = $result['updated'];
            $response['html'] = $html;
        } else {
            $response['result'] = 'Error al realizar la validación';
        }
        echo json_encode($response);
    }

    function searchEmails() {
        $controller = new OrderController();
        $controller->getCustomerEmailsList();
    }

    function searchIncidences() {
        $tpl = VIEWS_PATH . 'order/dymanic_incidences' . VIEW_EXT;
        $controller = new OrderController();
        $incidences = $controller->getAllIncidences();
        //pre($incidences);
        include($tpl);
    }

    function cancelSale() {
        global $user;
        $_POST['cancelled'] = 1;
        $_POST['cancelled_by'] = $user->getId();
        $_POST['cancelled_on'] = date('Y-m-d H:i:s');
        $saleId = $_POST['saleid'];
        $saleType = $_POST['saletype'];
        unset($_POST['saleid']);
        unset($_POST['saletype']);
        $controller = new StoreController();
        // Pasando 'code' alteramos el campo where por defecto (id) de las actualizaciones
        // Si es una variación sólo borramos esa
        if ($saleType == 1) {
            $_POST['id'] = $saleId;
            $result = $controller->updateSale('id');
        } else {
            // Si es una venta eliminamos la venta y sus variaciones
            $result = $controller->updateSale('code');
        }

        unset($_POST['cancelled_by']);
        unset($_POST['cancelled_on']);
        unset($_POST['cancell_reason']);

        // Sólo si es una venta
        if ($saleType == 0) {
            $controller = new OrderController();
            // Actualizamos el pedido a cancelado
            $controller->update_cancelled_order('code');
        }

        echo json_encode($result);
    }

    function savePdfComment() {
        global $user;
        $tpl = VIEWS_PATH . 'store/pdf_comments' . VIEW_EXT;
        $_POST['created_by'] = $user->getId();
        $_POST['created_on'] = date('Y-m-d H:i:s');

        $controller = new StoreController();
        $controller->saveComment();

        $data = new stdClass();
        $_GET['id'] = $_POST['pdfid'];
        $data->comments =  $controller->getNewPdfComments();
        include ($tpl);
    }

    function pdfYetPrinted() {
        $_POST['pdf_yet_printed'] = 1;
        $controller = new OrderController();
        echo json_encode($controller->pdfYetPrinted());
    }

    function adjustPendingPay() {
        global $user;
        unset($_POST['id']);
        $_POST['saletype'] = 3;
        $_POST['created_by'] = $user->getId();
        $_POST['created_on'] = date('Y-m-d H:i:s');
        $myDate = from_calendar_to_date($_POST['pending_payed_on']);
        $_POST['pending_payed_on'] = $myDate;
        $_POST['saledate'] = $myDate;

        $controller = new StoreController();
		$pdfData = $controller->getPdfData($_POST['parentcode']);
        // Importante guardar la tienda porque el ajuste se realiza desde contabilidad (usuario sin tienda)
        $_POST['storeid'] = $pdfData->storeid;
        $result = $controller->saveSale();
        echo json_encode($result);

    }

    function getAdjustPendingPayData() {
        $controller = new StoreController();
        $data = $controller->getAdjustPendingPayData();
        $data->saledate = americaDate($data->saledate, false);
        $data->payed = numberFormat($data->payed, true, 2);
        echo json_encode($data);
    }

    function updateAdjustPendingPay() {
        $controller = new StoreController();
		$pdfData = $controller->getPdfData($_POST['parentcode']);
        /*pre($_POST);
		pre($pdfData);
        exit;*/
        // Importante guardar la tienda porque el ajuste se realiza desde contabilidad (usuario sin tienda)
        $_POST['storeid'] = $pdfData->storeid;
		$_POST['pending_payed_on'] = from_calendar_to_date($_POST['pending_payed_on']);
        $result = $controller->updateSale();
        echo json_encode($result);
    }
}

new AjaxRequest();

?>