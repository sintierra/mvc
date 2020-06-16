<?php

namespace App\Controllers;

use App\Config;
use Core\View;
use \App\Models\User;

/**
 * Controlador del Sign Up
 * 
 * 
 * PHP Version 7.4
 * 
 */

 class Signup extends \Core\Controller 
 {
    /**
     * Muestra la página de Signup
     * 
     * @return void
     */

     public function newAction()
     {
         //Renderizamos el template con el formulario
         View::renderTemplate('Signup/new.html');
     }



     /**
      * Registrar un nuevo Usuario
      *
      *
      * @return void
      */
     public function createAction()
     {
         //Instancia un nuevo objeto de la clase User pasando los parámetros vía _POST
         $user = new User($_POST); 

         //Ejecutamos el método de guardado de Models\User y si no devuelve false
         if ($user->save()) {

            //Enviamos el e-mail de activación
            $user->sendActivationEmail();

            //Redirecciona a página de suceso
            $this->redirect(Config::ROOT_PATH . '/signup/success');

           //Si save devolvió false vuelve a renderizar el template pasando $user 
         } else {
    
            View::renderTemplate('Signup/new.html', [
                'user' => $user
            ]);
         }
         

         
     }

     /**
      * Muestra la página de success del signup
      *
      *
      *@return void
      */
     public function successAction()
     {
         View::renderTemplate('Signup/success.html');
     }

    /**
     * Activa una nueva cuenta
     * 
     * 
     * 
     */
    public function activateAction()
    {
        User::activate($this->route_params['token']);

        $this->redirect(Config::ROOT_PATH . '/signup/activated');
        
    }

    /**
     * Muestra la página de activación exitosa
     * 
     * @return void
     */
    public function activatedAction()
    {
        View::renderTemplate('Signup/activated.html');
    }
 }