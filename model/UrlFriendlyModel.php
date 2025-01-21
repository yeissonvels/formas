<?php

/**
 * Created by PhpStorm.
 * User: yvelezs
 * Date: 03/11/2017
 * Time: 12:44
 */
class UrlFriendlyModel
{
    protected $table;
    protected $wpdb;

    function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $this->wpdb->prefix . "friendly_urls";
    }

    function getUrlData() {
        $data = $this->wpdb->getOneRow($this->table, $_GET['id']);
        $data = persist($data, 'UrlFriendly');

        return $data;
    }

    function saveUrl() {
        $this->wpdb->save($this->table);
    }

    function saveEditUrl() {
        $this->wpdb->save_edit($this->table);
    }

    function get_urls($persist = true) {
        $data = $this->wpdb->getAll($this->table);
        if ($persist) {
            $data = persist($data, 'UrlFriendly');
        }

        return $data;
    }
}