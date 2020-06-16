<?php
namespace Core;

use PDO;
use App\Config;

/**
 * Modelo BASE
 * 
 * Php Version 7.3.6
 * 
 */

abstract class Model {
    /**
     * Modelo Base
     * Trae la conexión PDO para no tener que repetir el método en todas las requests y guardar la conexión
     * 
     * @return mixed
     * 
     * 
     */



protected static function getDB()
{
    static $db = null; //valor de DB null

    if ($db === null) { // Si es null, setea los valores de conexión
        /* hardcoded
        $host = 'localhost';
        $dbname = 'mvc';
        $username = 'root';
        $password = '';
        
        try {
            //$db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password); //instancia el objeto de datos con los valores
            
            $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8'; // Conexión implementado los 
            $db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);

        } catch (PDOException $e) { //si no puede instanciar el objeto devuelve la excepcion de PDO con el error
            echo $e->getMessage();
        }
        */

        $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8';
        $db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);

        // Lanza una excepción cuando ocurre un error
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
    }

    return $db; //devuelve la conexión si el try es correcto
}
}