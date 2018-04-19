<?php

namespace core;


class Request
{
    /**
     * @return bool
     */
    public function isPost()
    {
        return ($_SERVER['REQUEST_METHOD'] == 'POST' ? true : false);
    }

    /**
     * @return bool
     */
    protected function _isGet()
    {
        return ($_SERVER['REQUEST_METHOD'] == 'GET' ? true : false);
    }

    /**
     * @param $key
     * @param null $default
     * @return null
     */
    public function getParam($key, $default = null)
    {
        if ($this->isPost()) {
            if(isset($_POST[$key])) {
                return htmlentities($_POST[$key]);
            }
        }
        else if ($this->_isGet()) {
            if(isset($_GET[$key])) {
                return htmlentities($_GET[$key]);
            }
        }

        return $default;
    }

    /**
     * @return mixed
     */
    public function getAllParams()
    {
        $data = [];
        if ($this->isPost()) {
            $data =  $_POST;
        }
        else if ($this->_isGet()) {
            $data =  $_GET;
        }

        if($data) {
            foreach ($data as $key => $value) {
                $data[$key] = htmlentities($value);
            }
            return $data;
        }

        return array();
    }

    /**
     * @param $key
     * @return bool
     */
    public function getPost($key)
    {
        if ($this->isPost()) {
            if(isset($_POST[$key])) {
                return htmlentities($_POST[$key]);
            }
        }
        return false;
    }

}