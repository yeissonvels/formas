<?php

/**
 * Created by PhpStorm.
 * User: yvelezs
 * Date: 14/11/2017
 * Time: 11:49
 */
class Store
{
    protected $id;
    protected $storename;
    protected $cif;
    protected $address;
    protected $telephone;
    protected $fax;
    protected $cel;
    protected $email;
    protected $web;
    protected $cp;
    protected $city;
    protected $provinceid;
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
    public function getStorename()
    {
        return $this->storename;
    }

    /**
     * @param mixed $storename
     */
    public function setStorename($storename)
    {
        $this->storename = $storename;
    }

    /**
     * @return mixed
     */
    public function getCif()
    {
        return $this->cif;
    }

    /**
     * @param mixed $cif
     */
    public function setCif($cif)
    {
        $this->cif = $cif;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * @return mixed
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param mixed $fax
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    /**
     * @return mixed
     */
    public function getCel()
    {
        return $this->cel;
    }

    /**
     * @param mixed $cel
     */
    public function setCel($cel)
    {
        $this->cel = $cel;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * @param mixed $web
     */
    public function setWeb($web)
    {
        $this->web = $web;
    }

    /**
     * @return mixed
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * @param mixed $cp
     */
    public function setCp($cp)
    {
        $this->cp = $cp;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getProvinceid()
    {
        return $this->provinceid;
    }

    /**
     * @param mixed $provinceid
     */
    public function setProvinceid($provinceid)
    {
        $this->provinceid = $provinceid;
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