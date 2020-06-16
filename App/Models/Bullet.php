<?php
namespace App\Models;
use PDO;


/**
 * Modelo de Bullets
 * 
 * 
 */
class Bullet extends \Core\Model
{
   

    /**
     * Trae todos los bullets en un array
     * 
     * @param $id Id del usuario
     * @return array
     */
    public static function getAll($id)
    {
        $user_id = $id;

        $sql = 'SELECT name, text FROM
                bullets WHERE user_id = :user_id';
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        //$stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results;


    }

    
}