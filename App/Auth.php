<?php
namespace App;

use App\Models\User;
use App\Models\RememberedLogin;


/**
 * Autenticación
 * 
 * PHP Versión 7.3.6
 * 
 * 
 */
class Auth
{
    /**
     * Inicio de sesión del usuario
     * 
     * 
     *@param User $user  El User Model
     *
     *@return void
     */

    public static function login($user, $remember_me)
    {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user->id;

        if ($remember_me) {
            if($user->rememberLogin()) {
                setcookie('remember_me', $user->remember_token, $user->expiry_timestamp, '/');
            }
        }

    }

    /**
     * Cierre de sesión del usuario
     *
     * 
     * @return void
     */
    public static function logout()
    {
        // Unset de todos los valores de $_SESSION
        $_SESSION = [];

        // Si las cookies de inicio de sesión están seteadas, las resetea
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Luego destruye la sesión
        session_destroy();

        static::forgetLogin();
    }

   

    /**
     * Recuerda la página originalmente solicitada en la sesión
     * 
     * @return void
     * 
     */
    public static function rememberRequestedPage()
    {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    }

    /**
     * Trae la página originalmente solicitada en la sesión, o por defecto devuelve al home
     * 
     * 
     * @return void
     */
    public static function getReturnToPage()
    {
        return $_SESSION['return_to'] ?? Config::ROOT_PATH;
    }

    /**
     * Trae el usuario actualmente logeado desde la cookie de sessión del método rememberRequestedPage
     * 
     * @return mixed El User Model o null si no está logeado
     * 
     * 
     */
    public static function getUser()
    {
        //Si la cookie de inicio de sesión del usuario está seteada el método devuelve el objeto de usuario o false
        if (isset($_SESSION['user_id'])) {
            return User::findById($_SESSION['user_id']);

        } else {

                //Si no está seteada busca la cookie remember_me para logear desde ahí
                return static::loginFromRememberCookie();
        }
    }

    /**
     * Logea al usuario desde la cookie remember_me
     * 
     * @return mixed El modelo User si encuentra la cookie o null si no la encuentra.
     */
    protected static function loginFromRememberCookie(){
        $cookie = $_COOKIE['remember_me'] ?? false;

        if ($cookie) {
            //Setea la variable $remember_login, llama al método findByToken pasando el valor de la cookie
            $remembered_login = RememberedLogin::findByToken($cookie);
            
            //Si $remember_login es true Y $remember_login llamando el método hasExpired es false
            if ($remembered_login && ! $remembered_login->hasExpired()) {

                //Guarda en $user el ID devuelto por getUser
                $user = $remembered_login->getUser();

                //Llama el método estático login
                static::login($user, false);

                //Devuelve el user model
                return $user;
            }
        }
    
        
    }

    /**
     * Olvida el login recordado, si está presente
     * 
     * @return void
     * 
     * 
     */
    protected static function forgetLogin()
    {
        //Busca si la cookie remember_me está seteada, sino devuelve false
        $cookie = $_COOKIE['remember_me'] ?? false;

        //Si está seteada
        if ($cookie) {

            // ejecuta el método findByToken con el token y lo almacena en $remembered_login 
            $remembered_login = RememberedLogin::findByToken($cookie);
            
            //Si encuentra el token
            if ($remembered_login) {

                $remembered_login->delete();

            }

            setcookie('remember_me', '', time() - 3600, '/'); //setea la fecha de expiración en el pasado
        }
    }










}