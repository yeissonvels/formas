<?php

/**
 * Created by PhpStorm.
 * User: yvelezs
 * Date: 14/11/2017
 * Time: 12:04
 */
class Province
{
    protected $id;
    protected $province;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * @param mixed $province
     */
    public function setProvince($province)
    {
        $this->province = $province;
    }

}