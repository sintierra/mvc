<?php
namespace App\Controllers;

use App\Auth;
use App\Config;
use App\Flash;
use Core\View;

/**
 * Controlador de Perfiles
 * 
 * 
 * 
 */
class Profile extends Authenticated
{
    /**
     * Metodo Before
     * 
     * 
     */
    public function before()
    {
        //Llama el before() de la clase padre para no sobreescribir el método
        parent::before();

        //Trae el usuario logeado
        $this->user = Auth::getUser();
    }

    /**
     * Muestra el perfil
     * 
     * 
     * @return void
     * 
     */
    public function showAction()
    {
        //Renderiza el perfil de usuario pasando los datos del objeto de usuario como parámetros
        View::renderTemplate('Profile/show.html', [
            'user' => $this->user
        ]);
    }

    /**
     * Muestra el fomulario para editar el perfil
     * 
     * @return void
     * 
     */
    public function editAction()
    {
        //Renderiza la vista del formulario de edición pasando los datos del objeto de usuario como parámetros
        View::renderTemplate('Profile/edit.html', [
            'user' => $this->user
        ]);
    }

    /**
     * Actualiza el perfil con los datos del formulario
     * 
     * 
     */
    public function updateAction()
    {

        //Llama al método para actualizar. Si devuelve true
        if ($this->user->updateProfile($_POST)) {

            //Envía una notificación flash
            Flash::addMessage('Cambios guardados');

            //Redirecciona al perfil nuevamente para visualizar los datos
            $this->redirect(Config::ROOT_PATH . '/profile/show');
        } else {
            View::renderTemplate('Profile/edit.html', [
                'user' => $this->user
            ]);
        }
    }
}