<?php

namespace App\Controllers;
use \Core\View;
use App\Auth;
use App\Models\Bullet;
/**
     * Controlador de Bullets
     * 
     * PHP Version 7.3.6
     * 
     */
class Bullets extends Authenticated
{


    public function before()
    {
        //Llama el before() de la clase padre para no sobreescribir el método
        parent::before();

        //Trae el usuario logeado
        $this->user = Auth::getUser();
    }
    
    public function indexAction()
    {

        //Recorre el arreglo del objeto de usuario
        foreach ($this->user as $key => $value) {
            //Busca la ID del usuario
            if ($key == 'id') {
                //Si la encuentra la asigna a $user_id
                $user_id = $value;    
            }
            
        }
      
        //Ejecuta el método getAll con el user_id y lo almacena en $bullets
        $bullets = Bullet::getAll($user_id);
      
        
       
        //Renderiza el template con los bullets traídos del modelo
        View::renderTemplate('Bullets/index.html', 
            ['bullets' => $bullets]);
       
    }

    
}