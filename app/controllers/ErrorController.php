<?php

namespace app\controllers;
use core\Controller;
use Exception;

class ErrorController extends Controller
{
    protected $exception = null;
    /**
     * @param Exception $exception
     */
    public function setException(Exception $exception)
    {
        $this->exception = $exception;
    }
    public function errorAction()
    {
        // sets the 404 header
        header("HTTP/1.0 404 Not Found");

        // sets the error to be rendered in the view
        $this->view->error = $this->exception->getMessage();

        // logs the error to the log
        error_log($this->view->error);
        error_log($this->exception->getTraceAsString());
    }
}

