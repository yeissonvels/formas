<?php

/**
 * Created by PhpStorm.
 * User: yvelezs
 * Date: 14/11/2017
 * Time: 12:05
 */
class ProvinceModel extends Province
{
    protected $provincetable;
    protected $wpdb;

    function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->provincetable = $wpdb->prefix . "provinces";
    }

    function getProvinces($persist = true) {
        $provinces = $this->wpdb->getAll($this->provincetable, "", " ORDER BY province ASC");

        if ($persist) {
            $provinces = persist($provinces, 'province');
        }


        pre($provinces);
        return $provinces;
    }
}