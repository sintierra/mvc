<?php
namespace App;

/**
 * Notificaciones Flash: Mensajes de una única vez renderizados en vistas.
 * Almacenados en la cookie de sesión para tener acceso entre sesiones.
 * 
 * PHP Version 7.3.6
 * 
 * 
 */
class Flash 
{
    public const PRIMARY = 'primary';
    
    
    /**
     * Mensaje de tipo success
     * 
     * 
     */
    public const SUCCESS = 'success';

    /**
     * Mensaje de tipo info
     * 
     * 
     */
    public const INFO = 'info';

     /**
     * Mensaje de tipo warning
     * 
     * 
     */
    public const WARNING = 'warning';

    public const DANGER = 'danger';


    /**
     * Agregar un mensaje
     * 
     * @param string $message El contenido del mensaje
     * @return void
     */
    public static function addMessage($message, $type = 'primary')
    {
        //Crea un array en la sesión si aún no existe
        if (! isset($_SESSION['flash_notifications'])) {
            $_SESSION['flash_notifications'] = [];
        

        }
        //Asigna el mensaje al array
        $_SESSION['flash_notifications'][] = [
            'body' => $message,
            'type' => $type,
        ];
        
    }

    /**
     * Trae los mensajes
     * 
     * @return mixed Un array con todos los mensajes, null si no existen
     */
    public static function getMessages()
    {
        if (isset($_SESSION['flash_notifications'])) {
            $messages = $_SESSION['flash_notifications'];
            unset($_SESSION['flash_notifications']);

            return $messages;
        }
    }



}