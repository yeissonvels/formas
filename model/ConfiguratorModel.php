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
        $this->inactiveAllConfigurations();
        $this->wpdb->save_edit($this->mailerConfigTable);
    }

    function getMailerConfigurations() {
        $query = "SELECT * FROM " . $this->mailerConfigTable;
        return $this->wpdb->get_results($query);
    }

    function inactiveAllConfigurations() {
        $query = "UPDATE " . $this->mailerConfigTable . " SET status = 0";
        $this->wpdb->query($query);
    }
}