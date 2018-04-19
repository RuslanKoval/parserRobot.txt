<?php

namespace core;

use app\controllers\ErrorController;
use app\services\User;
use Exception;

class Router
{
    public function execute($routes)
    {

        try {
            $controller = null;
            $action = null;

            $routeFound = $this->_getSimpleRoute($routes, $controller, $action);

            if (!$routeFound) {
                $routeFound = $this->_getParameterRoute($routes, $controller, $action);
            }

            if (!$routeFound || $controller == null || $action == null) {
                throw new Exception('no route added for ' . $_SERVER['REQUEST_URI']);
            }
            else {
                $controller->execute($action);
            }
        }
        catch(Exception $exception) {
            $controller = new ErrorController();
            $controller->setException($exception);
            $controller->execute('error');
        }
    }

    /**
     * @param $route
     * @return int
     */
    public function hasParameters($route)
    {
        return preg_match('/(\/:[a-z]+)/', $route);
    }

    /**
     * @return array|string
     */
    protected function _getUri()
    {
        $uri = explode('?',$_SERVER['REQUEST_URI']);
        $uri = $uri[0];
        $uri = substr($uri, strlen(WEB_ROOT));

        return $uri;
    }

    /**
     * @param $routes
     * @param $controller
     * @param $action
     * @return bool
     */
    protected function _getSimpleRoute($routes, &$controller, &$action)
    {
        $uri = $this->_getUri();

        if (isset($routes[$uri])) {
            $routeFound = $routes[$uri];
        }
        else if(isset($routes[$uri . '/'])) {
            $routeFound = $routes[$uri . '/'];
        }
        else {
            $uri = substr($uri, 0, -1);
            $routeFound = isset($routes[$uri]) ? $routes[$uri] : false;
        }

        if ($routeFound) {
            list($name, $action) = explode('#', $routeFound);

            $controller = $this->_initializeController($name);

            return true;
        }

        return false;
    }

    /**
     * @param $routes
     * @param $controller
     * @param $action
     * @return bool
     */
    protected function _getParameterRoute($routes, &$controller, &$action)
    {
        $uri = $this->_getUri();

        foreach ($routes as $route => $path) {
            if ($this->hasParameters($route)) {
                $uriParts = explode('/:', $route);

                $pattern = '/^';
                //$pattern .= '\\'.($uriParts[0] == '' ? '/' : $uriParts[0]);
                if ($uriParts[0] == '') {
                    $pattern .= '\\/';
                }
                else {
                    $pattern .= str_replace('/', '\\/', $uriParts[0]);
                }

                foreach (range(1, count($uriParts)-1) as $index) {
                    $pattern .= '\/([a-zA-Z0-9]+)';
                }

                $pattern .= '[\/]{0,1}$/';

                $namedParameters = array();
                $match = preg_match($pattern, $uri, $namedParameters);
                if ($match) {
                    list($name, $action) = explode('#', $path);

                    $controller = $this->_initializeController($name);

                    foreach (range(1, count($namedParameters)-1) as $index) {
                        $controller->addNamedParameter(
                            $uriParts[$index],
                            $namedParameters[$index]
                        );
                    }

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function _initializeController($name)
    {
        $controller = ucfirst($name) . 'Controller';

        return ClassFactory::factory($controller);
    }
}