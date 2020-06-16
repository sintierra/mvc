<?php
namespace App\Models;

use App\Config;
use App\Mail;
use App\Token;
use Core\View;
use PDO;

class User extends \Core\Model 
{
    /** 
     *Mensajes de error
     * 
     * 
     * 
     * 
     *  
    */
    public $errors = [];

    /**
     * Constructor de Clase
     * @param array $data Valores de propiedad iniciales
     */
    public function __construct($data = []) //$data = [] los valores de $_POST
    {
        //Recorre en array $data y le asigna los valores como propiedades = valor al objeto
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };

    }


    /**
     * Guarda el modelo de usuario con los valores de propiedad 
     * 
     * @return void
     */
    public function save()
    {
        //Ejecuta el método Validate para validar que el input sea correcto
        $this->validate();

        //Si errors está vacío, por lo tanto el método validate no devolvió mensajes de error, ejecuta las operaciones de guardado en DB
        if (empty($this->errors)) {
            //Hashea el password que viene por _POST
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            //Instancia un nuevo token para la activacion
            $token = new Token();
            $hashed_token = $token->getHash();
            $this->activation_token = $token->getValue();

            //Crea la consulta SQL , conecta a la DB y prepara la sentencia
            $sql = 'INSERT INTO users 
                    (name, email, password_hash, activation_hash) 
                    VALUES 
                    (:name, :email, :password_hash, :activation_hash)';
            $db = static::getDB();
            $stmt = $db->prepare($sql);


            //Bindea los valores de _POST a las etiquetas de los valores de la sentencia   
            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);


            //Ejecuta la sentencia
            return $stmt->execute();
        }
        
        //Al terminar devuelve false en caso de que la validación fracase
        return false;
    }
    


    /**
     * Validador de Sign Up
     * Valida los valores de propiedades enviados, agregando mensajes de error de validacion al array de la propiedad $errors
     * 
     * @return void
     */
    public function validate()
    {
        // Nombre
        //Requerido
        if ($this->name == '') {
            $this->errors[] = 'El nombre es requerido';
        }

        // Dirección de E-mail

        //Devuelve el mensaje si el e-mail no es un e-mail valido
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'La dirección de e-mail es inválida. Reintentar';
        }
        //Si la función emailExists devuelve true, genera el error de e-mail en uso
        //El segundo parámetro de emailExists puede ser null o el ID de usuario si existe (ejemplo: el usuario esta registrado)
        if(static::emailExists($this->email, $this->id ?? null)) {
            $this->errors[] = 'La dirección de E-mail ya está en uso';
        }

        // Password

        if (isset($this->password)) {

            //Devuelve el mensaje si $password no tiene por lo menos 6 caracteres
             if (strlen($this->password) < 6) {
                $this->errors[] = 'La contraseña debe tener por lo menos 6 caracteres';
            }
            //Usa regexp para matchear si no encuentra por lo menos una letra
            if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
                $this->errors[] = 'La contraseña debe tener por lo menos una letra';
            }

            //Usa regexp para matchear si no encuentra por lo menos un numero
            if (preg_match('/.*\d+.*/i', $this->password) == 0) {
                $this->errors[] = 'La contraseña debe tener por lo menos una número';
            }
        }
           
    }


    /**
     * Revisa si ya existe en la db un usuario con un e-mail identico al especificado.
     * 
     * @param string $email Email a buscar en la DB
     * @param string $ignore_id Parametro opcional con el ID de usuario
     * 
     * @return boolean True si existe un e-mail en la DB con el e-mail especificado, false de otra forma
     */
    public static function emailExists($email, $ignore_id = null)
    {
        //Asigna a $user el modelo de usuario si lo encuentra
        $user = static::findByEmail($email);

        //Si lo encuentra
        if ($user) {
            //Y el ID del usuario no es igual a el ID ignorado, devuelve true
            if ($user->id != $ignore_id) {
                return true;
            }
        }

        //Si no lo encuentra
        return false;

    }

    /** 
     * Encuentra un Usuario por dirección de e-mail
     * 
     * @param string $email Dirección de Email a buscar
     * 
     * @return mixed Devuelve el objeto de usuario si lo encuentra, falso si no lo encuentra
     * 
    */
    public static function findByEmail($email)
    {
         //Selecciona las entradas donde email sea igual a :email
         $sql = 'SELECT * FROM users WHERE email = :email';

         //Conecta a la DB
         $db = static::getDB();
         //Prepara el stmt
         $stmt = $db->prepare($sql);
         //Bindea los parámetros :email a $email
         $stmt->bindValue(':email', $email, PDO::PARAM_STR);

         //Setea el método de entrega como clase
         $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

         //Ejecuta
         $stmt->execute();
         //Devuelve el objeto o false
         return $stmt->fetch();
 
    }

    /**
     * Autentica al usuario por e-mail y password
     * 
     * @param string $email Dirección de e-mail
     * @param string $password Contraseña
     * 
     * @return mixed Devuelve el objeto de usuario o false si falla la autenticación
     * 
     */

    public static function authenticate($email, $password)
    {
        // Busca el usuario basado en dirección de e-mail
        $user = static::findByEmail($email);

        //Si $user devuelve el objeto de usuario y el usuario está activado
        if ($user && $user->is_active) {
            //checkea el password
            if (password_verify($password, $user->password_hash)) {

                //Y devuelve el objeto de usuario terminando la función
                return $user;
            }
        } 
        return false;
    }


    /** 
     * Encuentra un Usuario por ID en la DB
     * 
     * @param string $id El ID de Usuario
     * 
     * @return mixed Devuelve el objeto de usuario si lo encuentra, falso si no lo encuentra
     * 
    */
    public static function findByID($id)
    {
        //Selecciona las entradas donde id sea igual a $id
        $sql = 'SELECT * FROM users WHERE id = :id';

        //Conecta a la DB
        $db = static::getDB();
        //Prepara el stmt
        $stmt = $db->prepare($sql);
        //Bindea los parámetros :id a $id
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        //Setea el método de entrega como clase
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        //Ejecuta
        $stmt->execute();
        //Devuelve el objeto o false
        return $stmt->fetch();
        

    }

    /**
     * Recuerda el login insertando un token único en la tabla remembered_logins
     * 
     * @return boolean True si el login se recordó de forma exitosa, false de otra forma
     */
    
    public function rememberLogin()
    {
        //instancia el nuevo token
        $token = new Token();
        //Hashea el token
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();

        //Setea el timestamp de expiración del token
        $this->expiry_timestamp  = time() + 60 * 60 * 24 * 30; // 30 días desde ahora
 
        
        //Consulta SQL que inserta en la DB el token
        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at) 
        VALUES (:token_hash, :user_id, :expires_at)';

        //Conecta a la DB
        $db = static::getDB();

        //Prepara el STMT
        $stmt = $db->prepare($sql);

        //bindea los params
        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

        //Ejecutamos el stmt y devolvemos
        return $stmt->execute();

    }

    /**
     * Envía las instrucciones de reseteo de password al e-mail especificado
     * 
     * 
     */
    public static function sendPasswordReset($email)
    {
        //Busca el usuario mediante e-mail
        $user = static::findByEmail($email);

        //Si devuelve el objeto modelo
        if ($user) {
            //Ejecuta el proceso de recuperar password, si devuelve true
            if ($user->startPasswordReset()) {
                
                //Envía el e-mail
                $user->sendPasswordResetEmail();
            }


        } 
    }

    /**
     * Inicia el proceso de recuperar password generando un token y una fecha de vencimiento
     * 
     */
    public function startPasswordReset()
    {
        //Instancia el token
        $token = new Token();

        //Hashea el token
        $hashed_token = $token->getHash();

        //Setea la variable password_reset_token con el token 
        $this->password_reset_token = $token->getValue();

        //Setea la fecha de vencimiento
        $expiry_timestamp = time() + 60 * 60 * 2; // 2 horas desde ahora
        
        //Crea la consulta
        $sql = 'UPDATE users 
                SET password_reset_hash = :token_hash,
                    password_reset_expiry = :expires_at
                WHERE ID = :id';

        //Conecta a la DB        
        $db = static::getDB();

        //Prepara el stmt
        $stmt = $db->prepare($sql);
        
        //Bindea los valores
        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        //Ejecuta y devuelve true o false si falla
        return $stmt->execute();


    }
    
    /**
     * Envía las instrucciones de reseteo de password por e-mail al usuario
     * 
     * @return void
     * 
     * 
     */
    protected function sendPasswordResetEmail()
    {
        //Formatea la URL con la accion Reset del controlador Password y el token
        $url = 'http://' . $_SERVER['HTTP_HOST'] . Config::ROOT_PATH . '/password/reset/' . $this->password_reset_token;

        //Carga los textos que serán enviados, en HTML y texto plano
        $text = View::getTemplate('Password/reset_email.txt', ['url' => $url]);
        $html = View::getTemplate('Password/reset_email.html', ['url' => $url]);

        //Envía el e-mail, por defecto en HTML
        Mail::send($this->email, "Restaurar Contraseña", $text, $html);

    }

    /**
     * Encuentra el User Model por el token de restaurar contraseña
     * 
     * 
     * @param string $token El token de restaurar contraseña
     * 
     * @return mixed El modelo de Usuario si encontrado, false si no fue encontrado y si el token expiró devuelve null
     */
    public static function findByPasswordReset($token)
    {

        $token = new Token($token);
        $hashed_token = $token->getHash();

        $sql = 'SELECT * FROM users WHERE password_reset_hash = :token_hash';

        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        
        $stmt->execute();

        $user = $stmt->fetch();

        if ($user) 
        {
            //Checkea que el token no haya expirado
            if (strtotime($user->password_reset_expiry) > time()) {
                return $user;
            }
        }
        //Si expiró devuelve NULL
    }

    /**
     * Restaura el password del usuario
     * 
     * @param string Password En Nuevo Password
     * @return boolean True si el password se actualizó de forma exitosa, false de otra forma
     */
    public function resetPassword($password)
    {
        //Asigna el password del input a la variable password
        $this->password = $password;

        //Llama el método validate()
        $this->validate();


        if (empty($this->errors)) {
            
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'UPDATE users
                    SET password_hash = :password_hash,
                        password_reset_hash = NULL,
                        password_reset_expiry = NULL
                    WHERE id = :id';
            $db = static::getDB();

            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            

            return $stmt->execute(); 

        }

        return false;
    }
    /**
     * Envía la activación de la cuenta del usuario
     * 
     * @return void
     */

    public function sendActivationEmail()
    {
        //Formatea la URL con la accion Reset del controlador Password y el token
        $url = 'http://' . $_SERVER['HTTP_HOST'] . Config::ROOT_PATH . '/signup/activate/' . $this->activation_token;

        //Carga los textos que serán enviados, en HTML y texto plano
        $text = View::getTemplate('Signup/activation_email.txt', ['url' => $url]);
        $html = View::getTemplate('Signup/activation_email.html', ['url' => $url]);

        //Envía el e-mail, por defecto en HTML
        Mail::send($this->email, "Activacion de Cuenta", $text, $html);

    }
    /**
     * Activa la cuenta
     * 
     * @param $value Token de activation
     * 
     * @return void
     */
    public static function activate($value)
    {
        $token = new Token($value);
        $hashed_token = $token->getHash();

        $sql = 'UPDATE users
                SET is_active = 1,
                activation_hash = NULL
                WHERE activation_hash = :hashed_token';
                
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);

        $stmt->execute();
    }


    /**
     * Actualiza el perfil de usuario
     * 
     * @param array Los datos a actualizar desde el formulario
     * @return boolean True si los datos se actualizaron, false de otra forma.
     */

    public function updateProfile($data)
    {
        $this->name = $data['name'];
        $this->email = $data['email'];

        //Solo valida y actualiza el password si fue solicitado
        if ($data['password'] != '') {
            $this->password = $data['password'];
        }
        

        $this->validate();
        
        //Si no hay errores de validación actualiza la DB
        if (empty($this->errors)) {
            
            $sql = 'UPDATE users
                    SET name = :name,
                        email = :email';

            //Agrega el password si está seteado
            if (isset($this->password)) {
                $sql .= ', password_hash = :password_hash';
            }


            $sql .= "\nWHERE id = :id";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

            if (isset($this->password)) {
                $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
                $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            }

            return $stmt->execute();

        }
        //Si hay errores de validacion
        return false;
    }
}