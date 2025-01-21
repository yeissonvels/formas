<?php

/**
 * Created by PhpStorm.
 * User: yvelezs
 * Date: 03/11/2017
 * Time: 12:42
 */
class UrlFriendly
{
    protected $id;

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
    protected $urlname;
    protected $description;
    protected $urlfriendly;
    protected $urlfriendlyedit;

    /**
     * @return mixed
     */
    public function getUrlfriendlyedit()
    {
        return $this->urlfriendlyedit;
    }

    /**
     * @param mixed $urlfriendlyedit
     */
    public function setUrlfriendlyedit($urlfriendlyedit)
    {
        $this->urlfriendlyedit = $urlfriendlyedit;
    }

    /**
     * @return mixed
     */
    public function getUrlname()
    {
        return $this->urlname;
    }

    /**
     * @param mixed $urlname
     */
    public function setUrlname($urlname)
    {
        $this->urlname = $urlname;
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
    public function getUrlfriendly()
    {
        return $this->urlfriendly;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param mixed $urlfriendly
     */
    public function setUrlfriendly($urlfriendly)
    {
        $this->urlfriendly = $urlfriendly;
    }

    /**
     * @return mixed
     */
    public function getControllername()
    {
        return $this->controllername;
    }

    /**
     * @param mixed $controllername
     */
    public function setControllername($controllername)
    {
        $this->controllername = $controllername;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }
    protected $controllername;
    protected $method;
    protected $type; // show | edit | delete | etc
}