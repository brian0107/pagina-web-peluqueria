<?php
namespace Model;
 //Modelo para tabla 'usuarios' con relacion uno a uno, ya que una persona puede tener una única cuenta.
class Usuario extends ActiveRecord {

    // Base de datos
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 
    'telefono', 'admin', 'confirmado', 'token'];

    public $id; 
    public $nombre; 
    public $apellido; 
    public $email; 
    public $password; 
    public $telefono; 
    public $admin; 
    public $confirmado; 
    public $token; 

    public function __construct($args = []){ //Toma argumentos pero por default es una array vacio.
        $this->id = $args['id'] ?? null; //Autoincremental por eso lo dejamos null.
        $this->nombre = $args['nombre'] ?? ''; //Varchar por eso string vacio.
        $this->apellido= $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? '0'; //Boolean (0 = false)
        $this->confirmado = $args['confirmado'] ?? '0';
        $this->token= $args['token'] ?? '';

    }

    // Mensajes de validación para la creación de una cuenta
    public function validarNuevaCuenta() {
        if(!$this->nombre){
            self::$alertas['error'][] = 'El Nombre es Obligatorio'; // al arreglo de alertas le creamos una llave "error" y como valor definimos un arreglo con el mensaje de alerta
        }

        if(!$this->apellido) {
            self::$alertas['error'][] = 'El Apellido es Obligatorio'; //a la llave de error que es un arreglo le añadimos mas mensajes de alerta
        }

        //preg_match sirve para usar expresiones regulares, el primer parámetro es la expresion regular y el segundo es lo que se va a revisar. '/[0-9]{10}/' quiere decir que es una extención fija de 10 digitos y solo acepta numeros del 0-9.
        if(!preg_match('/[0-9]{10}/', $this->telefono)) {
            self::$alertas['error'][] = ' No ingresaste un teléfono o el formato no es válido'; 
        }
        
        //preg_match sirve para usar expresiones regulares, el primer parámetro es la expresion regular y el segundo es lo que se va a revisar.
        if(!preg_match('/^[A-z0-9\\._-]+@[A-z0-9][A-z0-9-]*(\\.[A-z0-9_-]+)*\\.([A-z]{2,6})$/', $this->email) ) {
            self::$alertas['error'][] = 'No ingresaste un email o el formato no es Válido'; 
        }

        if(strlen($this->password) < 6) { //Validar password al menos 6 caracteres
            self::$alertas['error'][] = 'El password es obligatorio y debe contener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    public function validarLogin(){
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio'; //a la llave de error que es un arreglo le añadimos un mensaje de alerta.
        }

        if(!$this->password) {
            self::$alertas['error'][] = 'El Password es Obligatorio'; //a la llave de error que es un arreglo le añadimos mas mensajes de alerta
        }
       return self::$alertas;
    }

    public function validarEmail() {
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio'; //a la llave de error que es un arreglo le añadimos un mensaje de alerta.
        }
        return self::$alertas;
    }

    public function validarPassword() {
        if(!$this->password) {
            self::$alertas['error'][] = 'El Password es Obligatorio'; //a la llave de error que es un arreglo le añadimos mas mensajes de alerta
        }
        if(strlen($this->password) < 6) { //Validar password de al menos 6 caracteres
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    //Revisa si el usuario ya existe
    public function existeUsuario() {
        $query = "SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1"; 
        //Como a estas alturas hemos instanciado el objeto, el usuario ya está en memoria y estamos en la clase, utilizamos $this->email.
        $resultado = self::$db->query($query);
        
        if($resultado->num_rows) { //Si obtenemos resultado en num_rows entonces ya existe el usuario. Utilizamos sintaxis de objeto por que resultado retorna un objeto de mysqli donde se encuentra num_rows.
            self::$alertas['error'][] = 'El Usuario ya esta registrado';
        }
        return $resultado;
    }

    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT); 
    }

    public function crearToken() {
        $this->token = uniqid(); //Función para generar un id único (ideal para crear un token)
    }

     public function comprobarPasswordAndVerificado($password)
    {
        $resultado = password_verify($password, $this->password); //Verifica que coincidan las contraseñas retorna true o false. Recibe el password ingresado por el usuario y el password hasheado de la base de datos
        if(!$resultado || !$this->confirmado) {
            self::$alertas['error'][] = 'Password Incorrecto o tu Cuenta no ha sido Confirmada';
        } else {
            return true;
        }
        // NOTA: con $this accedemos al objeto de Usuario obtenido de la base de datos anteriormente en el controlador dentro del modelo (la misma clase), mientras que en el controlador accedemos con el nombre de la variable con la cual creamos la instancia de la clase ( $usuario ).
    }

}