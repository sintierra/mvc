<?php

/**
 * Front controller
 *
 * Php Version 7.3.6
 */

/**
 * Composer Autoload
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';     

/**
 * Errores y Excepciones
 * 
 * Setea los Error Handler y Exception Handler de Core\Error
 * 
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * Sesiones
 * 
 * 
 */
session_start();




//instanciamos el Router
$router = new Core\Router();


//Agregamos las rutas
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'login', 'action' => 'new']);
$router->add('logout', ['controller' => 'login', 'action' => 'destroy']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
$router->add('bullets', ['controller' => 'bullets', 'action' => 'index']);



/*
//Mostrar la tabla de rutas
echo '<pre>';
//var_dump($router->getRoutes());
echo htmlspecialchars(print_r($router->getRoutes(), true));
echo '</pre>';


/**
 * Coincidir la ruta
 * 
 * 
 

$url = $_SERVER['QUERY_STRING'];

if ($router->match($url)) {
     echo '<pre>';
     var_dump($router->getParams());
     echo '<pre>';
   
} else {
     echo "No route found for URL '$url'";
}
*/
$router->dispatch($_SERVER['QUERY_STRING']);