<?php
namespace App\Models;
use PDO;
use App\Token;

/**
 * Modelo de Inicio de Sesión Recordados
 * 
 * 
 * 
 */

 class RememberedLogin extends \Core\Model
 {
    /**
     * Encuentra un modelo de inicio de sesión recordado por el token
     * 
     * @return mixed Objeto de inicio de sesión recordado o false
     * 
     */
    public static function findByToken($token) 
    {
        //Instancia el objeto
        $token = new Token($token);

        $token_hash = $token->getHash();

        //Crea la Consulta SQL que busca el hash
        $sql = 'SELECT * FROM remembered_logins WHERE token_hash = :token_hash';

        //Conecta a la DB
        $db = static::getDB();

        //Prepara el STMT
        $stmt = $db->prepare($sql);

        //bindea los params
        $stmt->bindValue(':token_hash', $token_hash, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
         




    }


    /**
     * Trae el modelo de usuario asociado con este login recordado
     * 
     * 
     * @return Usuario El modelo de usuario
     * 
     */

    public function getUser()
    {
        return User::findByID($this->user_id);
    }


    /**
     * Busca en la DB si el token recordado ha expirado o no, basado la hora del sistema actual
     * 
     * @return boolean True si ha expirado, false si no
     * 
     */
    public function hasExpired() 
    {
        return strtotime($this->expires_at) < time();
    }



    /**
     * Elimina de la DB el login recordado al ejecutar el logout
     * 
     * @return void
     */

    public function delete() 
    {
        
        $sql = 'DELETE FROM remembered_logins WHERE token_hash = :token_hash';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $this->token_hash, PDO::PARAM_STR);

        $stmt->execute();

    }
 }