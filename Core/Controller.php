<?php

namespace Core;
use App\Auth;
use App\Config;
use App\Flash;

/**
 * Controlador base
 * No puede instanciar objetos, solo ser usada para extender clases (otros controladores)
 *
 * PHP version 7.3.6
 */
abstract class Controller 
{

    /**
     * Parametros de la ruta coincidida
     * @var array
     */
    protected $route_params = [];

    /**
     * Constructor de la clase
     *
     * @param array $route_params  Parametros de la ruta
     *
     * @return void
     */
    public function __construct($route_params)
    {
        $this->route_params = $route_params;
    }

    /**
     * __CALL
     * Método mágico llamado cuando un método inaccesible (private) o INEXISTENTE es
     * llamado en un objeto de esta clase. Usado para ejecutar métodos de filtro antes y despues de ejecutar el método de acción.
     * Todos los métodos de acción tienen el sufijo Action:
     * e.g. indexAction, showAction etc.
     *
     * @param string $name  Nombre del método
     * @param array $args Argumentos pasados al método 
     *
     * @return void
     */
    public function __call($name, $args)
    {
        $method = $name . 'Action';

        //Si $metodo ($nameAction) existe en el objeto instanciado
        if (method_exists($this, $method)) 
        { 
            /*
            *Si el método before() no devuelve falso, 
            El objeto llama el método en $method con los parámetros en $args y Ejecuta el método after(). 
            Si devuelve falso termina el método.
            *
            */           
                if ($this->before() !== false) { 
                    call_user_func_array([$this, $method], $args); 
                    $this->after(); 
                } 

        } else { //Si no fue encontrado el método
            //echo "Method $method not found in controller " . get_class($this);  

            //Exception handler
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    /**
     * Filtro Before - llamado antes del método de acción
     *
     * @return void
     */
    protected function before()
    {
    }

    /**
     * Filtro After() - llamado después del método de acción
     *
     * @return void
     */
    protected function after()
    {
    }

    /**
     * Redirige a un URL usando la variable global $_SERVER y el código HTTP 303
     * 
     * @param string $url La Url Relativa
     * 
     * @return void
     */
    public function redirect($url)
    {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
        exit;
    }


    /**
     * Requiere al usuario logearse antes de acceder a la página solicitada.
     * Recuerda la página solicitada para luego redirigir al logearse
     * 
     * 
     */
    public function requireLogin()
    {
        //Si no está logeado
        if (!Auth::getUser()) {
            //Agrega el mensaje flash

            
            //Flash::addMessage('Por favor ingrese para acceder a la página solicitada'); 
            //Comentado la asignación sin key=>value porque no toma la notificación, descomentar y comentar esta si no funciona

            Flash::addMessage('Por favor ingrese para acceder a la página solicitada', Flash::INFO);
            
        

            //Recuerda la URL solicitada
            Auth::rememberRequestedPage();

            //Y redirecciona a la ruta de login
            $this->redirect(Config::ROOT_PATH . '/login');
            
        }
    }




}