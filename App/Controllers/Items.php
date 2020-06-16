<?php
namespace App\Controllers;

use App\Auth;
use \Core\View;

/**
 * Controlador de Items (ejemplo)
 * 
 * PHP Version 7.3.6
 * 
 */
class Items extends Authenticated
{
   

    /**
     * Index de Items
     * 
     * 
     * @return void
     */
    public function IndexAction()
    {

        //Renderiza el template HTML
        View::renderTemplate('Items/index.html');

    }




}