<?php

/**
 * Created by PhpStorm.
 * User: yvelezs
 * Date: 14/11/2017
 * Time: 12:24
 */
class StoreController extends StoreModel
{
    protected $urls;
    protected $classTypeName = "store";

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
                'controller' => CONTROLLER . "&opt=show_stores",
                'friendly' => getFriendlyByType('show', $this->classTypeName),
            ),
            'new' => array(
                'controller' => CONTROLLER . "&opt=new_store",
                'friendly' =>  getFriendlyByType('new', $this->classTypeName),
            ),
            'edit' => array(
                'controller' => CONTROLLER . "&opt=new_store&id=",
                'friendly' =>  getFriendlyByType('new', $this->classTypeName, true),
            ),
            'delete' => array(
                'controller' => CONTROLLER . "&opt=delete_store",
                'friendly' =>  getFriendlyByType('delete', $this->classTypeName),
            ),
            'upload_pdf' => array(
                'controller' => CONTROLLER . "&opt=upload_pdf",
                'friendly' =>  getFriendlyByType('upload_pdf', $this->classTypeName),
            ),
            'ajax_edit_pdf' => array(
                'controller' => "?controller=store&opt=upload_pdf&id=",
                'friendly' =>  getFriendlyByType('upload_pdf', $this->classTypeName),
            ),
            'edit_pdf' => array(
                'controller' => CONTROLLER . "&opt=upload_pdf&id=",
                'friendly' =>  getFriendlyByType('upload_pdf', $this->classTypeName),
            ),
            'add_pay' => array(
                'controller' => "?controller=store&opt=add_pay&id=",
                'friendly' =>  getFriendlyByType('upload_pdf', $this->classTypeName),
            ),
            'show_pdfs' => array(
                'controller' => CONTROLLER . "&opt=show_pdfs",
                'friendly' =>  getFriendlyByType('show_pdfs', $this->classTypeName),
            ),
            'send_pdf' => array(
                'controller' => CONTROLLER . "&opt=send_pdf&id=",
                'friendly' =>  getFriendlyByType('show_pdfs', $this->classTypeName),
            ),
        );

        $this->urls = $urls;
    }

    function getUrls() {
        return $this->urls;
    }

    function show_stores() {
        $tpl = VIEWS_PATH_CONTROLLER . "show_stores" . VIEW_EXT;
        $stores = $this->getStores();
        loadTemplate($tpl, $stores, '', $this);
    }

    function createStoresFile($update = false) {
        $stores = $this->getStores(false);
        file_put_contents(JSON_STORES, json_encode($stores));
        if ($update) {
            confirmationMessage('Modificado archivo de tiendas!');
        } else {
            confirmationMessage('Creado archivo de tiendas!');
        }
    }

    function new_store() {
        $tpl = VIEWS_PATH_CONTROLLER . "new_store" . VIEW_EXT;
        $data = false;
        // Obtiene el id desde el REQUEST_URI y lo setea en $_GET
        getIdFromRequestUri();
        if (isset($_GET['id'])) {
            $data = $this->getStoreData();
        }

        loadTemplate($tpl, $data, '', $this);
    }

    function save_store() {
        $this->save_store_db();
        $this->createStoresFile();
    }

    function save_edit_store() {
        $this->save_edit_store_db();
        $this->createStoresFile(true);
    }

    function upload_pdf() {
        $data = false;
        $parent = array();
        // Obtiene el id desde el REQUEST_URI y lo setea en $_GET
        getIdFromRequestUri();
        if (isset($_GET['id'])) {
            $data = $this->getPdfData();
            if ($data->saletype == 1) {
                $parent = $this->getPdfData($data->parentcode);
            }
        }

        if ($data) {
            $data->parent = $parent;
        }


        $tpl = VIEWS_PATH_CONTROLLER . "upload_pdf" . VIEW_EXT;
        loadTemplate($tpl, $data, '', $this);
    }

    function add_pay() {
        $data = false;
        $parent = array();
        // Obtiene el id desde el REQUEST_URI y lo setea en $_GET
        getIdFromRequestUri();
        if (isset($_GET['id'])) {
            $data = $this->getPdfData();
            $parent = $this->getPdfData($data->parentcode);
        }

        if ($data) {
            $data->parent = $parent;
        }


        $tpl = VIEWS_PATH_CONTROLLER . "add_pay" . VIEW_EXT;
        loadTemplate($tpl, $data, '', $this);
    }

    function show_pdfs($msg = "") {
        $pdfs = [];
        $tpl = VIEWS_PATH_CONTROLLER . "show_pdfs" . VIEW_EXT;
        loadTemplate($tpl, $pdfs, $msg, $this);
    }
	
	function getPdfSales() {
		$tpl = VIEWS_PATH_CONTROLLER . "pdf_sales" . VIEW_EXT;
		
        foreach ($_GET as $key => $value) {
            $_POST[$key] = $value;
        }
        
		$data = $this->getPdfs();
		// Devuelve la variable html
		include($tpl);
		
		return $html;
	}

	function getPdfChildrenSales() {
        //getPdfChildren
        $tpl = VIEWS_PATH_CONTROLLER . "pdf_sales" . VIEW_EXT;

        foreach ($_GET as $key => $value) {
            $_POST[$key] = $value;
        }

        $data = $this->getPdfChildren();
        // Devuelve la variable html
        include($tpl);

        return $html;
    }

    function send_pdf() {
        $msg = "PDF enviado correctamente al almacén";
        getIdFromRequestUri();
        $this->updatePdfStatus();
        $this->show_pdfs($msg);
    }

    function checkPdfCode($code, $fieldname) {
        return $this->existPdfByCode($code, $fieldname);
    }

    function getPdfStores() {
        $stores = $this->getStores();

        $css = getPdfTableStyle();
        $th = $css->th;
        $tr1 = $css->trmodo1;
        $td1 = $css->tdmodo1;
        $tr2 = $css->trmodo2;
        $td2 = $css->trmodo2;

        $html = '<table>';
        $html .=    '<tr style="' . $tr1 . '" >';
        $html .=        '<th style="' . $th . '" >Id</th>';
        $html .=        '<th style="' . $th . '">Nombre</th>';
        $html .=        '<th style="' . $th . '">Dirección</th>';
        $html .=        '<th style="' . $th . '">CP.</th>';
        $html .=        '<th style="' . $th . '">Localidad</th>';
        $html .=        '<th style="' . $th . '">Provincia</th>';
        $html .=        '<th style="' . $th . '">Email</th>';
        $html .=        '<th style="' . $th . '">Teléfono</th>';
        $html .=        '<th style="' . $th . '">Fax</th>';
        $html .=        '<th style="' . $th . '">Móvil</th>';
        $html .=    '</tr>';
        foreach ($stores as $store) {
            $html .=    '<tr ' . $tr1 . ' >';
            $html .=        '<td style="' . $td1 . '" >' . $store->getId() . '</td>';
            $html .=        '<td style="' . $td1 . '">' . $store->getStorename() . '</td>';
            $html .=        '<td style="' . $td1 . '">' . $store->getAddress() . '</td>';
            $html .=        '<td style="' . $td1 . '">' . $store->getCp() . '</td>';
            $html .=        '<td style="' . $td1 . '">' . $store->getCity() . '</td>';
            $html .=        '<td style="' . $td1 . '">' . $store->getProvince() . '</td>';
            $html .=        '<td style="' . $td1 . '">' . $store->getEmail() . '</td>';
            $html .=        '<td style="' . $td1 . '">' . $store->getTelephone() . '</td>';
            $html .=        '<td style="' . $td1 . '">' . $store->getFax() .  '</td>';
            $html .=        '<td style="' . $td1 . '">' . $store->getCel() . '</td>';
            $html .=    '</tr>';
        }

        $html .=    '</table>';

        /*echo $html;
        exit;*/

        return $html;
    }

    function getPdfStores2() {
        $stores = $this->getStores();
        //$pdfCss = getPdfCss();
        $tableCss = 'style="border: 1px solid #eceeef; border-collapse: collapse; background-color: transparent;"';
        $thCss = 'style="border: 1px solid #eceeef; padding: 1px; background-color: #CE612C; height: 20px; text-align: center;"';
        $tdCss = 'style="border: 1px solid #eceeef; padding: 1px; height: 20px;"';

        $html = '<table ' . $tableCss . ' >';
        $html .=    '<tr>';
        $html .=        '<th ' . $thCss . '>Id</th>';
        $html .=        '<th ' . $thCss . '>Nombre</th>';
        $html .=        '<th ' . $thCss . '>Dirección</th>';
        $html .=        '<th ' . $thCss . '>CP.</th>';
        $html .=        '<th ' . $thCss . '>Localidad</th>';
        $html .=        '<th ' . $thCss . '>Provincia</th>';
        $html .=        '<th ' . $thCss . '>Email</th>';
        $html .=        '<th ' . $thCss . '>Teléfono</th>';
        $html .=        '<th ' . $thCss . '>Fax</th>';
        $html .=        '<th ' . $thCss . '>Móvil</th>';
        $html .=    '</tr>';
        foreach ($stores as $store) {
            $html .=    '<tr>';
            $html .=        '<td ' . $tdCss . '>' . $store->getId() . '</td>';
            $html .=        '<td ' . $tdCss . '>' . $store->getStorename() . '</td>';
            $html .=        '<td ' . $tdCss . '>' . $store->getAddress() . '</td>';
            $html .=        '<td ' . $tdCss . '>' . $store->getCp() . '</td>';
            $html .=        '<td ' . $tdCss . '>' . $store->getCity() . '</td>';
            $html .=        '<td ' . $tdCss . '>' . $store->getProvince() . '</td>';
            $html .=        '<td ' . $tdCss . '>' . $store->getEmail() . '</td>';
            $html .=        '<td ' . $tdCss . '>' . $store->getTelephone() . '</td>';
            $html .=        '<td ' . $tdCss . '>' . $store->getFax() .  '</td>';
            $html .=        '<td ' . $tdCss . '>' . $store->getCel() . '</td>';
            $html .=    '</tr>';
        }

        $html .=    '</table>';

        /*echo $html;
        exit;*/

        return $html;
    }

    function getPdfStores3() {
        $stores = $this->getStores();
        $html = '<head>';
        $html .=    '<link rel=\'stylesheet\' href=\'/css/bootstrap.min.css\' type=\'text/css\' media=\'all\'/>';
        $html .= '</head>';
        $html .= '<table class="table table-bordered">';
        $html .=    '<tr>';
        $html .=        '<th >Id</th>';
        $html .=        '<th>Nombre</th>';
        $html .=        '<th>Dirección</th>';
        $html .=        '<th>CP.</th>';
        $html .=        '<th>Localidad</th>';
        $html .=        '<th>Provincia</th>';
        $html .=        '<th>Email</th>';
        $html .=        '<th>Teléfono</th>';
        $html .=        '<th>Fax</th>';
        $html .=        '<th>Móvil</th>';
        $html .=    '</tr>';
        foreach ($stores as $store) {
            $html .=    '<tr>';
            $html .=        '<td>' . $store->getId() . '</td>';
            $html .=        '<td>' . $store->getStorename() . '</td>';
            $html .=        '<td>' . $store->getAddress() . '</td>';
            $html .=        '<td>' . $store->getCp() . '</td>';
            $html .=        '<td>' . $store->getCity() . '</td>';
            $html .=        '<td>' . $store->getProvince() . '</td>';
            $html .=        '<td>' . $store->getEmail() . '</td>';
            $html .=        '<td>' . $store->getTelephone() . '</td>';
            $html .=        '<td>' . $store->getFax() .  '</td>';
            $html .=        '<td>' . $store->getCel() . '</td>';
            $html .=    '</tr>';
        }

        $html .=    '</table>';

        echo $html;
        exit;

        return $html;
    }

	function restoreManual() {
		if (isset($_GET['id'])) {
			$this->restore_manual();
			confirmationMessage("Venta restaurada!");
		}
	}
	
	function new_store_order() {
		$data = false;
        $parent = array();
        // Obtiene el id desde el REQUEST_URI y lo setea en $_GET
        getIdFromRequestUri();
        if (isset($_GET['id'])) {
            $data = $this->getPdfData();
            if ($data->saletype == 1) {
                $parent = $this->getPdfData($data->parentcode);
            }
        }

        if ($data) {
            $data->parent = $parent;
        }


        $tpl = VIEWS_PATH_CONTROLLER . "new_store_order" . VIEW_EXT;
        loadTemplate($tpl, $data, '', $this);
	}
	
	function getPdfsToDelete() {
		$tpl = VIEWS_PATH_CONTROLLER . 'pdfs_to_delete' . VIEW_EXT;
		//echo $tpl;
		$orders = $this->getOrdersToDelete();
		include($tpl);
	}
}