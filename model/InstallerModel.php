<?php

/**
 * Class InstallerModel
 * Date: 06/10/2017
 * Time: 12:28:19
 */
class InstallerModel {
    protected $wpdb;
    protected $installerTable;

    function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->installerTable = $wpdb->prefix . "users";
    }

    function getInstallers() {
        global $user;
        $onlyMyInstallers = "";
        if ($user->getAdmin() != 1) {
            $onlyMyInstallers = " AND work_for=" . $user->getId();
        }
        $where = " WHERE installer=1 " . $onlyMyInstallers;
        $installers = $this->wpdb->getAll($this->installerTable, $where);
        $installers = persist($installers, 'Installer');

        return $installers;
    }

    function getInstallerData() {
        $id = $_GET['id'];

        $data = $this->wpdb->getOneRow($this->installerTable, $id);
        $user = persist($data, 'Installer');

        return $user;
    }

    function save_installer() {
        global $user;
        $_POST['user_pass'] = md5($_POST['user_pass']);
        $_POST['work_for'] = $user->getId();
        $_POST['installer'] = 1;

        $this->wpdb->save($this->installerTable);
    }

    function save_edit_installer() {
        // Si no vamos a cambiar la contraseña
        if (empty($_POST['user_pass'])) {
            unset($_POST['user_pass']);
        } else {
            $_POST['user_pass'] = md5($_POST['user_pass']);
        }
        $this->wpdb->save_edit($this->installerTable);
    }
}