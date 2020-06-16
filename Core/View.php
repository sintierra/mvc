<?php

namespace Core;

/**
 * Vista
 *
 * Php Version 7.3.6
 */
class View
{

    /**
     * Renderiza una Vista
     *
     * @param string $view  El archivo de vista
     * @param array $args  Array asociativo de data para mostrar en la vista (opcional)
     *
     * @return void
     */
    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);

        $file = "../App/Views/$view";  // relative to Core directory

        if (is_readable($file)) {
            require $file;
        } else {
             //echo "$file not found";
             throw new \Exception("$file no encontrado");
        }
    }
    
    /**
     * Render a view template using Twig
     *
     * @param string $template  The template file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function renderTemplate(string $template, array $args = [])
    {
       echo static::getTemplate($template, $args);
    }

    /**
     * Trae el contenido de un template usando twig
     *
     * @param string $template  El archivo de Template
     * @param array $args  Array asociativo de los datos a pasar en la vista (opcional)
     *
     * @return void
     */
    public static function getTemplate(string $template, array $args = [])
    {
        static $twig = null;
 
        //Si Twig no está configurado
        if ($twig === null)
        {
            //Crea la instancia de Twig
            $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/App/Views');

            //Ejecuta el entorno de Twig en las vistas
            $twig = new \Twig\Environment($loader);

            //Agrega la variable global de ROOT
            $twig->addGlobal('root_path', \App\Config::ROOT_PATH);
            
            //Agrega la variable global 'current_user' con el método getUser() de la clase User
            $twig->addGlobal('current_user', \App\Auth::getUser());

            //Agrega una variable global 'flash_messages' con el método getMessages() de la clase Flash
            $twig->addGlobal('flash_messages', \App\Flash::getMessages());

    



        }
 
        return $twig->render($template, $args);
    }


}