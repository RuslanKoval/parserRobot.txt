<?php
use core\Router;

//error_reporting(0);


define('WEB_ROOT', substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], '/index.php')));
define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
session_start();

include(ROOT_PATH . '/config/routes.php');
require __DIR__ . '/../vendor/autoload.php';


$router = new Router();
$router->execute($routes);
