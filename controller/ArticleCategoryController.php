<?php

/**
 * Created by PhpStorm.
 * User: yvelezs
 * Date: 22/11/2017
 * Time: 13:00
 */
class ArticleCategoryController extends ArticleCategoryModel
{
    protected $urls;
    protected $classTypeName = "ArticleCategory";

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
                'controller' => CONTROLLER . "&opt=show_categories",
                'friendly' => getFriendlyByType('show', $this->classTypeName),
            ),
            'new' => array(
                'controller' => CONTROLLER . "&opt=new_category",
                'friendly' => getFriendlyByType('new', $this->classTypeName),
            ),
            'edit' => array(
                'controller' => CONTROLLER . "&opt=new_category&id=",
                'friendly' => getFriendlyByType('new', $this->classTypeName),
            )
        );
        $this->urls = $urls;
    }

    function getUrls() {
        return $this->urls;
    }

    function show_categories() {
        $tpl = VIEWS_PATH_CONTROLLER . 'show_categories' . VIEW_EXT;
        $categories = $this->getCategories(true, false);

        loadTemplate($tpl, $categories, '', $this);
    }

    function new_category() {
        $data = false;
        $tpl = VIEWS_PATH_CONTROLLER . 'new_category' . VIEW_EXT;
        getIdFromRequestUri();

        if (isset($_REQUEST['id'])) {
            $data = $this->getCategoryData();
        }

        loadTemplate($tpl, $data, '', $this);
    }

    function createJsonCategories() {
        $data = $this->getCategories(false);
        file_put_contents(JSON_CATEGORIES, json_encode($data));
        confirmationMessage('Creado JSON de categorias');
    }

    function save_category() {
        $this->save();
        $this->createJsonCategories();
    }

    function save_edit_category() {
        $this->save_edit();
        $this->createJsonCategories();
    }
}