<?php

namespace core;

use stdClass;

class View
{

    protected $content = "";
    protected $layout = 'layout';

    protected $viewEnabled = true;
    protected $layoutEnabled = true;

    protected $data = array();
    protected $javascripts = '';

    public $settings = null;

    public function __construct()
    {
        $this->settings = new stdClass();
    }

    /**
     * @param $viewScript
     */
    protected function _renderViewScript($viewScript)
    {
        ob_start();

        include(ROOT_PATH . '/app/views/scripts/' . $viewScript);

        $this->content = ob_get_clean();
    }

    /**
     * @return string
     */
    public function content()
    {
        return $this->content;
    }

    /**
     * @param $viewScript
     */
    public function render($viewScript)
    {
        if ($viewScript && $this->viewEnabled) {
            $this->_renderViewScript($viewScript);
        }

        if ($this->_isLayoutDisabled()) {
            echo $this->content;
        }
        else {
            include(ROOT_PATH . '/app/views/layouts/' . $this->_getLayout() . '.php');
        }
    }

    /**
     * Renders the given data as json
     * @param mixed $data
     */
    public function renderJson($data)
    {
        $this->disableView();
        $this->disableLayout();

        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');

        echo json_encode($data);
    }

    protected function _getLayout()
    {
        return $this->layout;
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;

        if ($layout) {
            $this->_enableLayout();
        }
    }

    public function disableLayout()
    {
        $this->layoutEnabled = false;
    }

    public function disableView()
    {
        $this->viewEnabled = false;
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return null;
    }

    /**
     * @return string
     */
    public function baseUrl()
    {
        return WEB_ROOT;
    }

    /**
     * @param $script
     */
    public function appendScript($script)
    {
        $this->javascripts .= '<script type="text/javascript" src="'.$script.'"></script>' ."\n";
    }

    public function printScripts()
    {
        echo $this->javascripts;
    }

    protected function _enableLayout()
    {
        $this->layoutEnabled = true;
    }

    /**
     * @return bool
     */
    protected function _isLayoutDisabled()
    {
        return !$this->layoutEnabled;
    }
}