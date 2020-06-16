<?php
namespace App;

/**
 * Tokens únicos y aleatorios
 * 
 * 
 */

 class Token
 {
     /**
    *El valor del token
    *
    *@var array
    *
    * 
    */
    protected $token;


    /**
     * Constructor del Token
     * 
     * @return void
     * 
     */
    public function __construct($token_value = null)
    {
        if ($token_value) {

            $this->token = $token_value;

        } else {

        $this->token = bin2hex(random_bytes(16)); //16 bytes = 128 bits = 32 hex chars

        }
    }


    /**
     * Trae el valor del Token
     * 
     * @return string El valor
     */
    public function getValue()
    {
        return $this->token;
    }

    /**
     * Trae el token hasheado
     * 
     * @return string EL token hasheado
     * 
     */
    public function getHash()
    {
        return hash_hmac('sha256', $this->token, \App\Config::SECRET_KEY); //sha256 = 64 chars
    }

 }