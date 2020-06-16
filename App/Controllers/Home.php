<?php
namespace App\Controllers;

use App\Auth;
use \Core\View;

/**
 * Home controller
 *
 * Php Version 7.3.6
 */
class Home extends \Core\Controller
{
/**
     * Before filter
     *
     * @return void
     */
    protected function before()
    {
       
    }

    /**
     * After filter
     *
     * @return void
     */
    protected function after()
    {
        
    }

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        //\App\Mail::send('example@example.com', 'Test', 'Esto es una prueba');
        View::renderTemplate('Home/index.html');    
    }
}   