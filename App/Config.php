<?php
namespace App;
/**
 * Configuración de la Aplicación
 * 
 * Php Version 7.3.6
 * 
 * 
 */

 class Config 
 {
    /**
     * Host de la DB
     * 
     * @var string
     */

     const DB_HOST = 'localhost';

     /**
      * Nombre de la DB
      * @var string
      */
     const DB_NAME = 'mvc';

    /**
     * Usuario de la DB
     * 
     * @var string
     */
     const DB_USER = 'root';
    /**
     * Pass de la DB
     * 
     * @var string
     */
     const DB_PASSWORD = '';

     /**
      * Muestra u oculta mensajes de errores en la pantalla
      * 
      * @var boolean
      */
    const SHOW_ERRORS = true;

      /**
       * Secret Key  
       * Usada para generar hashes seguros
       * 
       * 
       *  
       * 
       */     
    const SECRET_KEY = 'gnDsg9h0aB0vsxqcVnfhTeaCRe3Pm5CA';


    /**
    * Php Mailer host 
    *
    *
    */
    const PHP_MAILER_HOST = 'localhost';
    /**
    * Php Mailer Port 
    *
    *
    */
    const PHP_MAILER_PORT = 2525;

   /**
    * Constante de la ruta raíz
    *
    *
    */
    const ROOT_PATH = '/mvc';
 }