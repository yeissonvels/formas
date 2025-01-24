<?php

/**
 * Created by PhpStorm.
 * User: yvelezs
 * Date: 16/11/2017
 * Time: 20:32
 */
class ProductController extends ProductModel
{
    protected $urls;
    protected $classTypeName = "Product";

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
    function setUrls()
    {
        $urls = array(
            'show' => array(
                'controller' => CONTROLLER . "&opt=show_products",
                'friendly' => getFriendlyByType('show', $this->classTypeName),
            ),
            'show_finishes' => array(
                'controller' => CONTROLLER . "&opt=show_finishes",
                'friendly' => getFriendlyByType('show_finishes', $this->classTypeName),
            ),
            'show_store_products' => array(
                'controller' => CONTROLLER . "&opt=show_store_products",
                'friendly' => getFriendlyByType('show_store_products', $this->classTypeName),
            ),
            'new_store_product' => array(
                'controller' => CONTROLLER . "&opt=new_store_product",
                'friendly' => getFriendlyByType('new_store_product', $this->classTypeName),
            ),
            'edit_store_product' => array(
                'controller' => CONTROLLER . "&opt=new_store_product&id=",
                'friendly' => getFriendlyByType('new_store_product', $this->classTypeName),
            ),
            'new' => array(
                'controller' => CONTROLLER . "&opt=new_product",
                'friendly' => getFriendlyByType('new', $this->classTypeName),
            ),
            'new_finish' => array(
                'controller' => CONTROLLER . "&opt=new_finish",
                'friendly' => getFriendlyByType('new_finish', $this->classTypeName),
            ),
            'edit' => array(
                'controller' => CONTROLLER . "&opt=new_product&id=",
                'friendly' => getFriendlyByType('new', $this->classTypeName),
            ),
            'edit_finish' => array(
                'controller' => CONTROLLER . "&opt=new_finish&id=",
                'friendly' => getFriendlyByType('new_finish', $this->classTypeName),
            )
        );
        $this->urls = $urls;
    }

    function getUrls() {
        return $this->urls;
    }

    function show_products() {
        $tpl = VIEWS_PATH_CONTROLLER . 'show_products' . VIEW_EXT;
        $products = $this->getProducts(true, false);

        loadTemplate($tpl, $products, '', $this);
    }

    function new_product() {
        $data = false;
        $tpl = VIEWS_PATH_CONTROLLER . 'new_product' . VIEW_EXT;
        getIdFromRequestUri();

        if (isset($_REQUEST['id'])) {
            $data = $this->getProductData();
        }

        loadTemplate($tpl, $data, '', $this);
    }
	
	function new_store_product() {
		$data = false;
        $tpl = VIEWS_PATH_CONTROLLER . 'new_store_product' . VIEW_EXT;
        getIdFromRequestUri();

        if (isset($_REQUEST['id'])) {
            $data = $this->getStoreProductData();
        }

        loadTemplate($tpl, $data, '', $this);
	}
	
	function show_finishes() {
		$tpl = VIEWS_PATH_CONTROLLER . 'show_finishes' . VIEW_EXT;
        $finishes = $this->getFinishes(true, false);

        loadTemplate($tpl, $finishes, '', $this);
	}
	
	/*
	 * Nuevo acabado
	 */
	function new_finish() {
		$data = false;
        $tpl = VIEWS_PATH_CONTROLLER . 'new_finish' . VIEW_EXT;
        getIdFromRequestUri();

        if (isset($_REQUEST['id'])) {
            $data = $this->getFinishData();
        }

        loadTemplate($tpl, $data, '', $this);
	}

	function show_store_products() {
		$tpl = VIEWS_PATH_CONTROLLER . 'show_store_products' . VIEW_EXT;
        $products = $this->getStoreProducts(true, false);

        loadTemplate($tpl, $products, '', $this);
	}
}