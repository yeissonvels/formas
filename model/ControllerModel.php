<?php

/**
 * Created by PhpStorm.
 * User: yvelez
 * Date: 15/11/2016
 * Time: 11:48
 */
class ControllerModel extends Controller {
    protected $controllerTable;
    protected $userTable;
    private $wpdb;

    function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->controllerTable = $wpdb->prefix . 'controllers';
        $this->userTable = $wpdb->prefix . 'users';
    }

    function getControllers() {
        $query = 'SELECT *, con.id as id, user_nicename as created_by FROM ' . $this->controllerTable . ' con,' . $this->userTable . ' us WHERE con.created_by=us.id';
        $data = $this->wpdb->get_results($query);
        return persist($data, 'Controller');
    }

    function getControllerData() {
        $data = $this->wpdb->getOneRow($this->controllerTable, $_GET['id']);
        return persist($data, 'Controller');
    }

    function getControllerDataByName($name) {
        $query = 'SELECT * FROM ' . $this->controllerTable . ' WHERE controller_name="' . $name . '"';
        $data = $this->wpdb->get_results($query);

        return persist($data, 'Controller');
    }

    function save_controller() {
        $this->wpdb->save($this->controllerTable);
    }

    function save_edit_controller() {
        $this->wpdb->save_edit($this->controllerTable);
    }
}