<?php

/**
 * Created by PhpStorm.
 * User: yvelezs
 * Date: 03/10/2017
 * Time: 8:47
 */
class Error
{
    public $errors = array();

    public function __construct()
    {

    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    public function addError($error) {
        $errors = $this->getErrors();
        array_push($errors, $error);
        $this->setErrors($errors);
    }

    function getAppErrors() {
        $errors = $this->getErrors();
        include(VIEWS_PATH_COMMON . "errors.html.php");
    }

}