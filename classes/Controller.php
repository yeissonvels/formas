<?php

/**
 * Created by PhpStorm.
 * User: yvelez
 * Date: 15/11/2016
 * Time: 11:49
 */
class Controller {
    protected $id;
    protected $controller_name;
    protected $old_controllername;
    protected $description;
    protected $created_by;
    protected $created_on;

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
    public function getControllerName()
    {
        return $this->controller_name;
    }

    /**
     * @param mixed $controller_name
     */
    public function setControllerName($controller_name)
    {
        $this->controller_name = $controller_name;
    }

    /**
     * @return mixed
     */
    public function getOldControllername()
    {
        return $this->old_controllername;
    }

    /**
     * @param mixed $old_controllername
     */
    public function setOldControllername($old_controllername)
    {
        $this->old_controllername = $old_controllername;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * @param mixed $created_by
     */
    public function setCreatedBy($created_by)
    {
        $this->created_by = $created_by;
    }

    /**
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->created_on;
    }

    /**
     * @param mixed $created_on
     */
    public function setCreatedOn($created_on)
    {
        $this->created_on = $created_on;
    }
    
}