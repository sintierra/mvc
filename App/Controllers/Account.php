<?php
namespace App\Controllers;

use \App\Models\User;

/**
 * 
 * Controlador de Cuentas
 * 
 * 
 * PHP Version 7.3.6
 * 
 * 
 */

class Account extends \Core\Controller 
{
    /**
     * 
     * Valida si el e-mail está disponible (AJAX) en un nuevo registro
     * 
     * 
     * @return void
     * 
     */
    public function validateEmail()
    {
        /*
            Ejecuta el método emailExists con los parámetros:
            @param email El email desde el formulario mediante AJAX
            @param ignore_id o NULL El id ignorado o null si no se envía el mismo.
        */
        $is_valid = ! User::emailExists($_GET['email'], $_GET['ignore_id'] ?? null);
        header('Content-Type: application/json');
        echo json_encode($is_valid);
    }



}