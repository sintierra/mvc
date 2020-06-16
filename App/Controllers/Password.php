<?php
namespace App\Controllers;

use App\Models\User;
use \Core\View;

/**
 * Controlador de claves
 * 
 * PHP Version 7.3.6
 * 
 */

class Password extends \Core\Controller
{
    /**
     * Muestra la página de Olvidé mi Contraseña
     * 
     * @return void
     */
    public function forgotAction()
    {
        View::renderTemplate('Password/forgot.html');
    }

    /**
     * Envía el link de restablecer contraseña al e-mail suministrado
     * 
     * 
     */
    public function requestResetAction()
    {
        //Metodo de User Model para traer el e-mail
        User::sendPasswordReset($_POST['email']);

        View::renderTemplate('Password/reset_requested.html');

    }

    /**
     * Muestra el formulario de reseto de password al ser solicitado por el usuario mediante email
     * 
     * @return void
     * 
     */
    public function resetAction() 
    {
        //Asigna a $token el valor de los parámetros de URL
        $token = $this->route_params['token'];

        //Llama el método getUserOrExit para buscar el modelo de usuario con el token y lo asigna a $user. Si no lo encuentra devuelve null y termina el script
        $user = $this->getUserOrExit($token);
        
        //Al devolver el modelo de usuario renderiza el template de reseteo de password
        View::renderTemplate('Password/reset.html', [
            'token' => $token
        ]);
        
    }

    /**
     * Procesa la acción de restauración de contraseña del usuario
     * 
     * @return void
     */
    public function resetPasswordAction()
    {
        //El fomulario de restaurar password almacena el token en un input hidden. El método lo trae y lo almacena el $token
        $token = $_POST['token'];

        //Llama el método getUserOrExit con el token, si encuentra el modelo de usuario
        $user = $this->getUserOrExit($token);

        if ($user->resetPassword($_POST['password'])) {

            View::renderTemplate('Password/reset_success.html');

        } else {
            View::renderTemplate('Password/reset.html', [
                'token' => $token,
                'user' => $user
            ]);

            
        }
        
    }

    /**
     * Encuentra el modelo asociado con el token de restaurar password o finaliza la solicitud con un mensaje 
     * 
     * @param string $token El token de restaurar contraseña
     * 
     * @return mixed El objeto de usuario si encontrado y el token no expiró, null de otra forma
     * 
     */
    public function getUserOrExit($token)
    {
        $user = User::findByPasswordReset($token);

        if ($user) {
            
            return $user;

        } else {

            View::renderTemplate('Password/token_expired.html');
            exit;
        }
    }


}