<?php
namespace App\Controllers;

use \Core\View;
use \App\Models\User;
Use \App\Auth;
use App\Config;
use App\Flash;

/**
 * Controlador del Login
 *
 * 
 * PHP Version 7.3.6
 *  
 */

class Login extends \Core\Controller 
{
    /**
     * Muestra la página de logea
     * 
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Login/new.html');
    }

    /**
     * Logea al Usuario
     * 
     * @return void
     * 
     */
    public function createAction()
    {
        //Autentica los valores que llegan por POST. Si son correctos instancia el objeto $user con los parámetros, sino devuelve false
        $user = User::authenticate($_POST['email'], $_POST['password']);

        $remember_me = isset($_POST['remember_me']);
        
        //Si el usuario se autentica correctamente
        if ($user) {

            //Ejecuta el método estático login de la Auth
            Auth::login($user, $remember_me);

            //Notificación Flash
            Flash::addMessage('Ingresaste correctamente', FLASH::SUCCESS);

            //Redirecciona
            $this->redirect(Auth::getReturnToPage());

        } else {
            //Notificación Flash
            Flash::addMessage('Ingreso fallido, intente nuevamente.', Flash::DANGER);

            View::renderTemplate('Login/new.html', [
                'email' => $_POST['email'],
                'remember_me' => $remember_me
            ]);

        }
    }
    

    /**
     * Cierra la sesión del usuario
     * 
     * @return void
     * 
     */
    public function destroyAction()
    {
        //Cierra la sesión
        Auth::logout();

        //Redirije al método showLogoutMessageAction para mostrar el mensaje de cierre de sesión, ya que luego de ejecutar logout() se destruye la sesión
        $this->redirect(Config::ROOT_PATH . '/login/show-logout-message');
        
    }
    public function showLogoutMessageAction()
    {
         //Notificación Flash
         Flash::addMessage('Cerraste sesión correctamente', Flash::INFO);

         // Y redirige al Home
         $this->redirect(Config::ROOT_PATH);
    }
}