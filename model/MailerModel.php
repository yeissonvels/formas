<?php
/**
 * Created by PhpStorm.
 * User: yvelez
 * Date: 23/01/2025
 * Time: 19:16
 */

class MailerModel {
    protected $mailerConfigTable;
    protected $wpdb;

    function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->mailerConfigTable = $wpdb->prefix . 'mailer_config';
    }

    function getWPDB() {
        return $this->wpdb;
    }

    function getMailerConfig() {
        $query = "SELECT * FROM " . $this->mailerConfigTable . " WHERE status = 1";
        return $this->wpdb->get_row($query);
    }
}