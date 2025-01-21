<?php

/**
 * Created by PhpStorm.
 * User: yvelezs
 * Date: 22/11/2017
 * Time: 13:00
 */
class ArticleCategoryModel extends ArticleCategory
{
    protected $categoriesTable;
    protected $wpdb;

    function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->categoriesTable =  $wpdb->prefix . "article_categories";
    }

    function getCategoryData() {
        $data = $this->wpdb->getOneRow($this->categoriesTable, $_GET['id']);
        return persist($data, 'ArticleCategory');
    }

    function getCategories($persist = true, $onlyactive = true) {
        $where = '';
        if ($onlyactive) {
            $where = ' WHERE active = 1';
        }
        $categories = $this->wpdb->getAll($this->categoriesTable, $where , ' ORDER BY category asc');

        if ($persist) {
            $categories = persist($categories, 'ArticleCategory');
        }

        return $categories;
    }

    function save() {
        $this->wpdb->save($this->categoriesTable);
    }

    function save_edit() {
        $this->wpdb->save_edit($this->categoriesTable);
    }
}