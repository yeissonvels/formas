<?php

/**
 * Created by PhpStorm.
 * User: yvelezs
 * Date: 16/11/2017
 * Time: 20:32
 */
class ProductModel extends Product
{
    protected $productsTable;
	protected $storeProductsTable; // Nueva funcionalidad productos tienda
	protected $finishTable;
    protected $wpdb;

    function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->productsTable = $wpdb->prefix . "products";
		$this->storeProductsTable = $wpdb->prefix . "storeproducts"; 
		$this->finishTable = $wpdb->prefix . "finishes";

    }

    function getProductData() {
        $data = $this->wpdb->getOneRow($this->productsTable, $_GET['id']);
        return persist($data, 'Product');
    }
	
	function getStoreProductData() {
		return $this->wpdb->getOneRow($this->storeProductsTable, $_GET['id']);
	}

    function getProducts($persist = true, $onlyactive = true) {
        $where = '';
        // Sólo productos activos
        if ($onlyactive) {
            $where = ' WHERE active = 1';
        }

        $products = $this->wpdb->getAll($this->productsTable, $where, 'ORDER BY productname ASC');

        if ($persist) {
            $products = persist($products, 'Product');
        }

        return $products;
    }
	
	function getStoreProducts($persist = true, $onlyactive = true) {
		$where = '';
        // Sólo productos activos
        if ($onlyactive) {
            $where = ' WHERE active = 1';
        }

        $products = $this->wpdb->getAll($this->storeProductsTable, $where, 'ORDER BY productname ASC');

        /*if ($persist) {
            $products = persist($products, 'Product');
        }*/

        return $products;
	}
	
	function getFinishes($persist = true, $onlyactive = true) {
        $where = '';
        // Sólo productos activos
        if ($onlyactive) {
            $where = ' WHERE active = 1';
        }

        $finishes = $this->wpdb->getAll($this->finishTable, $where, 'ORDER BY finishname ASC');

        if ($persist) {
            //$finishes = persist($products, 'Product');
        }

        return $finishes;
    }

    function save_product() {
        $_POST['productname'] = strtoupper($_POST['productname']);
        $this->wpdb->save($this->productsTable);
    }

    function save_edit_product() {
        $_POST['productname'] = strtoupper($_POST['productname']);
        $this->wpdb->save_edit($this->productsTable);
    }
	
	/**
	 * Para los acabados
	 */
	function save_finish() {
		$_POST['createdon'] = date('Y-m-d H:i:s');
		$this->wpdb->save($this->finishTable);
		$this->updateJsonFinishes();
	}
	
	function getFinishData($id = 0) {
		if ($id == 0) {
			$id = $_GET['id'];
		}
		return $this->wpdb->getOneRow($this->finishTable, $id);
	}
	
	/**
	 * Para los acabados
	 */
	function save_edit_finish() {
		$this->wpdb->save_edit($this->finishTable);
		$this->updateJsonFinishes();
	}
	
	function updateJsonFinishes() {
		$finishes = $this->getFinishes();
		file_put_contents(JSON_FINISHES, json_encode($finishes));
		confirmationMessage("JSON de acabados actualizado!");
	}
	
	function getJsonFinishes() {
		$finishes = file_get_contents(JSON_FINISHES);
		return json_decode($finishes);
	}
	
	function save_store_product() {
		$this->wpdb->save($this->storeProductsTable);
	}
	
	function save_edit_store_product() {
		$this->wpdb->save_edit($this->storeProductsTable);
	}
	
	function getAutocompleteCode($nocancelled = false) {
        global $user;
        $keyword = $_POST['keyword'];
        $cancelledFilter = '';
		
        if ($nocancelled) {
            $cancelledFilter = ' AND active = 1';
        }        
		
        $query = 'SELECT sp.id, productname, reference, sp.price as price  FROM ' . $this->storeProductsTable . ' sp, ';
		$query .= $this->finishTable . ' fn WHERE sp.finishid = fn.id' ;
        $query .= $cancelledFilter;
        $query .= ' AND (productname LIKE "%' . $keyword . '%" OR reference LIKE "%' . $keyword . '%" OR finishname LIKE "%' . $keyword . '%") LIMIT 10;';
        //echo $query;
        $codes = $this->wpdb->get_results($query);
		
        return $codes;
    }
}