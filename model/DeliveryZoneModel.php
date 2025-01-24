<?php

/**
 * Created by PhpStorm.
 * User: yvelezs
 * Date: 11/12/2017
 * Time: 14:36
 */
class DeliveryZoneModel extends DeliveryZone
{
    protected $zonesTable;
    protected $wpdb;

    function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->zonesTable = $wpdb->prefix . "delivery_zones";
    }

    function getDeliveryZones($persist = true)
    {
        $zones = $this->wpdb->getAll($this->zonesTable, '', 'ORDER BY zone ASC');
        if ($persist) {
            $zones = persist($zones, 'DeliveryZone');
        }

        return $zones;
    }

    function getDeliveryZoneData() {
        $storeId = $_GET['id'];

        $data = $this->wpdb->getOneRow($this->zonesTable, $storeId);
        $user = persist($data, 'DeliveryZone');

        return $user;
    }

    function save_zone() {
        $this->wpdb->save($this->zonesTable);
        $this->createZonesFile();
    }

    function save_edit_zone() {
        $this->wpdb->save_edit($this->zonesTable);
        $this->createZonesFile();
    }

    function createZonesFile() {
        $zones = $this->getDeliveryZones(false);
        file_put_contents(JSON_ZONES, json_encode($zones));

        confirmationMessage('Archivo de zonas actualizado!');
    }
}