<?php
namespace App\Controllers;

/**
 * Controlador Base para controladores que requieran autenticación
 * 
 * 
 * 
 * 
 */
abstract class Authenticated extends \Core\Controller
{
     /**
     * Método Before
     */
    protected function before()
    {
        //Cuando el objeto es instanciado, ejecuta el método requireLogin.
        $this->requireLogin();
    }
}