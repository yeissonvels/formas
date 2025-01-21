<?php

/**
 * Class CustomerModel
 * Date: 05/10/2017
 * Time: 09:37:27
 */
class CustomerModel extends Customer {
    protected $customersTable;
    protected $provincesTable;
    protected $wpdb;

    function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->customersTable = $wpdb->prefix . "customers";
        $this->provincesTable = $wpdb->prefix . "provinces";
    }

    function getCustomers() {
        global $user;
        $query = "SELECT *, c.id as id FROM " . $this->customersTable . " c, " . $this->provincesTable . " p ";
        $query .= " WHERE c.provinceid  = p.id";

        $customers = $this->wpdb->get_results($query);
        $customers = persist($customers, 'Customer');
        return $customers;
    }

    function getCustomersByCriteria($criteria) {
        $query = "SELECT *, c.id as id FROM " . $this->customersTable . " c, " . $this->provincesTable . " p ";
        $query .= " WHERE c.provinceid  = p.id AND (name LIKE '%" . $criteria . "%' OR telephone LIKE '%" . $criteria . "%')";
        $customers = $this->wpdb->get_results($query);
        $customers = persist($customers, 'Customer');

        return $customers;
    }

    function getCustomerData($id = 0) {
        $userId = $_GET['id'];

        if ($id > 0){
            $userId = $id;
        }
        $data = $this->wpdb->getOneRow($this->customersTable, $userId);
        $user = persist($data, 'Customer');

        return $user;
    }

    function save_customer() {
        global $user;
        // Si no vamos a cambiar la contraseña
        /*$_POST['password'] = md5($_POST['password']);
        $_POST['user_id'] = $user->getId();*/
        $this->wpdb->save($this->customersTable);
    }

    function save_edit_customer() {
        // Si no vamos a cambiar la contraseña
        /*if (empty($_POST['password'])) {
            unset($_POST['password']);
        } else {
            $_POST['password'] = md5($_POST['password']);
        }*/
        $this->wpdb->save_edit($this->customersTable);
    }
}