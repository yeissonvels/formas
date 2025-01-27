<?php

class ConfiguratorModel {
    protected $mailerConfigTable;
    protected $wpdb;

    function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->mailerConfigTable =  $wpdb->prefix . "mailer_config";
    }

    function getMailerConfig($id = 0) {
        return $this->wpdb->getOneRow($this->mailerConfigTable, $id);
    }

    function save_edit_config() {
        $this->wpdb->save_edit($this->mailerConfigTable);
    }
}