<?php

namespace core;



class Controller
{
    public $view = null;
    protected $request = null;
    protected $action = null;
    protected $namedParameters = array();

    public function init()
    {
        $this->view = new View();

        $this->view->settings->action = $this->action;
        $this->view->settings->controller = strtolower(str_replace('Controller', '', get_class($this)));
    }

    public function beforeFilters()
    {
        // no standard filers
    }

    public function afterFilters()
    {
        // no standard filers
    }

    /**
     * @param string $action
     */
    public function execute($action = 'index')
    {
        $this->action = $action;

        $this->init();

        $this->beforeFilters();

        $actionToCall = $action.'Action';
        $this->$actionToCall();

        $this->afterFilters();

        $this->view->render($this->getViewScript($action));
    }

    /**
     * @param $action
     * @return string
     */
    protected function getViewScript($action)
    {
        $controller = $this->getClassName();
        $script = strtolower(substr($controller, 0, -10) . '/' . $action . '.php');

        return $script;
    }


    /**
     * @return string
     */
    private function getClassName() {
        $path = explode('\\', get_class($this));
        return array_pop($path);
    }

    /**
     * @return string
     */
    protected function _baseUrl()
    {
        return WEB_ROOT;
    }

    /**
     * @return null|Request
     */
    public function getRequest()
    {
        if ($this->request == null) {
            $this->request = new Request();
        }

        return $this->request;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    protected function _getParam($key, $default = null)
    {
        if (isset($this->_namedParameters[$key])) {
            return $this->namedParameters[$key];
        }

        return $this->getRequest()->getParam($key, $default);
    }

    /**
     * @return array
     */
    protected function _getAllParams()
    {
        return array_merge($this->getRequest()->getAllParams(), $this->namedParameters);
    }

    /**
     * @param $key
     * @param $value
     */
    public function addNamedParameter($key, $value)
    {
        $this->namedParameters[$key] = $value;
    }

    /**
     * @return bool
     */
    public function loadData()
    {
        $data = $this->_getAllParams();
        if(!$data)
            return false;

        return true;
    }

}