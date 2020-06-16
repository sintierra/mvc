<?php
namespace Core;
/**
 * Ruteador
 * 
 * Php Version 7.3.6
 * 
 * 
 * 
 */

class Router { 
    /**
     * Array asociativo de las Rutas
     * @var array
     */

    protected $routes = [];

    /**
     * Array asociativo de los Parámetros de las Rutas
     * @var array
     */

    protected $params = [];

    /**
     * Agregar una ruta a la tabla de rutas
     * @param string $route La URL de la ruta
     * @param array $params Parametros (controlador, acción, etc.)
     * 
     * @return void
     */

    public function add($route, $params = [])
    {
        // Convierte la ruta a reg exp: pasa los forward slashes "/"
        $route = preg_replace('/\//', '\\/', $route);
    
        // Convierte las variables {variable} de ruta y las etiqueta
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
       
        // Convierte las variables con reg ex customizadas e.g. {id:\d+}
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
        
        // Agrega delimitadores al principio y fin de la string. Ademas del flag case insensitive.
        $route = '/^' . $route . '$/i';
        

        $this->routes[$route] = $params;
    }


    /**Trae todas las rutas de la tabla de rutas
     * 
     * @return array
     */
    public function getRoutes() 
    {

        return $this->routes; //Devuelve la propiedad routes
    
    }   

    /**Emparejador
     * Coincide la ruta con las rutas en la tabla de rutas, asignando la propiedad $params SI la ruta es encontrada.
     * @param string $url La URL de la ruta
     * 
     * @return boolean true si coincide, caso contrario false
     */

    public function match($url) 
    {
     
        foreach ($this->routes as $route => $params) 
        {
            if (preg_match($route, $url, $matches)) 
            {

            foreach ($matches as $key => $match) {
                if (is_string($key)) {
                    $params[$key] = $match;
                }
            }

            $this->params = $params;
            return true;
            }
        }

        return false;

    }
 

    /**Devuelve los parametros asignados a la ruta  
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }


     /**
     * Enviador
     * Este método envía la ruta URL, instanciando el objeto con la clase del {controlador} y corriendo
     * el método de la {acción}. Funciona mediante 3 condicionales anidados para verificar: Primero si la ruta existe,
     * luego si el controlador existe como clase
     * y por último si la acción es pública. En caso de que alguna no se cumpla envía un mensaje de error.
     *
     * @param string $url The route URL
     *
     * @return void
     */
    public function dispatch($url)
    {

        $url = $this->removeQueryStringVariables($url); //Remueve las variables de query string

        //Si el método match retorna true, guarda en $controller los parámetros agregados en el indice "controller". Luego convierte
        //el nombre del controlador a StudlyCaps
        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->convertToStudlyCaps($controller);
            $controller = $this->getNamespace() . $controller;

            //Si $controller convertido a StudlyCaps existe como clase, instancia el objeto como la clase contenida en $controller con los parámetros
            if (class_exists($controller)) {
                $controller_object = new $controller($this->params);

                //El mismo proceso se hace para la acción, convirtiendo la acción a CamelCase
                $action = $this->params['action'];
                $action = $this->convertToCamelCase($action);

                //La acción es un método, si la misma no incluye el sufijo "Action" o "action", instancia el objeto con dicha acción
                if (preg_match('/action$/i', $action) == 0) {
                    if (is_callable([$controller_object, $action])) {
                        $controller_object->$action();
    
                    } else {
                        
                        throw new \Exception("Método $action (en controlador $controller) no encontrado");
                    }
                    

                } else {

                    //Si incluye el sufijo "Action" o "action" se pide remover el sufijo para llamar la acción directamente.
                    //Esto previene un agujero de seguridad que permite llamar una acción sin estar logeado usando la URL /{accion}Action
                    throw new \Exception("Method $action in controller $controller cannot be called directly - remove the Action suffix to call this method");
                }   
            //Si el controlador convetido a StudlyCaps no existe, devuelve el mensaje de error personalizado
            } else {
                // echo "Controller class $controller not found";
                throw new \Exception("Clase Controlador $controller no encontrada");
            }
        //Si el método match no devolvió TRUE, devuelve el mensaje que no encontró la ruta
        } else {
             //echo 'No route matched.';
             throw new \Exception('No route matched.', 404);
        }
    }

    /**
     * Convierte la cadena de texto a StudlyCaps. 
     * La función str_replace dentro de ucwords reemplaza los "-" con espacios. 
     * La funcion ucwords convierte la primera letra de cada palabra (ahora separadas por espacios) a UperCase. 
     * La función str_replace luego del return junta la cadena eliminando los espacios.
     * e.g. post-authors => PostAuthors
     *
     * @param string $string La cadena a Convertir
     *
     * @return string La cadena convertida devuelta
     */
    protected function convertToStudlyCaps($string)
    {
 
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    /**
     * Convierte una cadena con guiones ('-') a camelCase. Ejecuta el método convertToStudlyCaps en la cadena y luego descapitaliza la primera letra.
     * e.g. add-new => addNew
     *
     * @param string $string La cadena a convertir
     *
     * @return string
     */
    protected function convertToCamelCase($string)
    {
        return lcfirst($this->convertToStudlyCaps($string));
    }

     /**
     * Elimina las query string variables de la URL (si hay). Como toda la query string se usa en la ruta,
     * cualquier variable al final debe ser removida antes de que la ruta sea emparejada (match) en la tabla de rutas.
     * Por ejemplo:
     *
     *   URL                           $_SERVER['QUERY_STRING']  Ruta
     *   -------------------------------------------------------------------
     *   localhost                     ''                        ''
     *   localhost/?                   ''                        ''
     *   localhost/?page=1             page=1                    ''
     *   localhost/posts?page=1        posts&page=1              posts
     *   localhost/posts/index         posts/index               posts/index
     *   localhost/posts/index?page=1  posts/index&page=1        posts/index
     *
     * A URL of the format localhost/?page (one variable name, no value) won't
     * work however. (NB. The .htaccess file converts the first ? to a & when
     * it's passed through to the $_SERVER variable).
     *
     * @param string $url The full URL
     *
     * @return string The URL with the query string variables removed
     */
    
     protected function removeQueryStringVariables($url)
    {   // URL Ejemplo: home/new=1&best=2
    
        //Si la URL no está vacia
        if ($url != '') {  


            $parts = explode('&', $url, 2); //convierte en array todo lo que está luego del & (con límite de 2 indices) y lo guarda en $parts
            // $parts = [0 => localhost/home/new=1]
            if (strpos($parts[0], '=') === false) {  //Si en parts[0], donde está la URL sin los parámetros variables NO HAY un (=)
                $url = $parts[0]; //devuelve la URL sin los parámetros variables
            } else {
                $url = ''; //sino asigna una cadena vacia a la URL
            
            }
        
        
        }

        return $url; 
    }



    public function getNamespace()
    {
        $namespace = 'App\Controllers\\';
        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        }

        return $namespace;
    }










}