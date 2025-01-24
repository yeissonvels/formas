<?php
/**
 * Created by PhpStorm.
 * User: yvelez
 * Date: 26/02/16
 * Time: 13:33
 */

class Config {
    protected $host;

    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    protected $user;

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    protected $password;

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    protected $dbname;

    /**
     * @return string
     */
    public function getDbname()
    {
        return $this->dbname;
    }

    protected $prefix;

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    protected $defaultTimeZone;

    /**
     * @return string
     */
    public function getDefaultTimeZone()
    {
        return $this->defaultTimeZone;
    }

    function __construct($file) {
        // $config es un array que se encuentra dentro del archivo de configuración $file
        include($file);

        $this->host = $config['host'];
        $this->user = $config['user'];
        $this->password = $config['password'];
        $this->dbname = $config['dbname'];
        $this->prefix = $config['prefix'];
        $this->defaultTimeZone = $config['defaultTimeZone'];
    }

}