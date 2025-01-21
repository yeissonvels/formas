<?php

/**
 * Created by PhpStorm.
 * User: yvelezs
 * Date: 14/11/2017
 * Time: 12:04
 */
class ProvinceController extends ProvinceModel
{
    function __construct()
    {
        parent::__construct();
    }

    function updateJson() {
        $provinces = $this->getProvinces(false);
        file_put_contents(JSON_PROVINCES, json_encode($provinces));
    }
}