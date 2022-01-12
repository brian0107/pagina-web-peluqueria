<?php
namespace Model;
class ActiveRecord {
/*
NOTAS:

Protected hace que la variable/función se puede acceder desde la clase que las define y también desde cualquier otra clase que herede de ella.

Una variable/funcion estática es accesible sin la necesidad de instanciar la clase en la que se encuentra. Accedemos por medio de la clase de esta forma: Class::VariableOrMethod

Si accedemos a una propiedad estática con self:: (accedemos a la propiedad la clase principal), si lo hacemos con static:: (accedemos a la propiedad de la clase hija)
*/

// Base dE Datos
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];

    // Alertas y Mensajes
    protected static $alertas = [];
    
    // Definir la conexión a la BD - includes/database.php
    public static function setDB($database) {
        self::$db = $database;
    }

    public static function setAlerta($tipo, $mensaje) {
        static::$alertas[$tipo][] = $mensaje; //Agregamos una alerta al arreglo de alertas de la clase hija
    }

    // Validación
    public static function getAlertas() {
        return static::$alertas;
    }

    public function validar() { //Metodo posiblemente inservible
        static::$alertas = [];
        return static::$alertas;
    }

    // Consulta SQL para crear un objeto en Memoria
    public static function consultarSQL($query) {
        // Consultar la base de datos
        $resultado = self::$db->query($query);

        // Iterar los resultados
        $array = [];
        while($registro = $resultado->fetch_assoc()) { //fetch_assoc retorna un arreglo asociativo con los datos de un registro de la BD
            $array[] = static::crearObjeto($registro);
        }

        // liberar la memoria
        $resultado->free();

        // retornar los resultados
        return $array;
    }

    // Crea el objeto en memoria que es igual al de la BD
    protected static function crearObjeto($registro) {
        $objeto = new static;
        foreach($registro as $key => $value ) {
            if(property_exists( $objeto, $key  )) {
                $objeto->$key = $value;
            }
        }
        return $objeto;
    }

    // Identificar y unir los atributos de la BD para sanitizarlos antes de enviarlos a la BD.
    public function atributos() {
        $atributos = [];
        foreach(static::$columnasDB as $columna) { //Con static hacemos referencia a la variable de la clase hija. Con self:: hacemos referencia a la variable de esta clase.
            if($columna === 'id') continue; //Excluimos el Id ya que no necesitamos esa columna al insertar(es AUTO_INCREMENT) o actualizar (por que va en el WHERE)
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    // Sanitizar los datos antes de guardarlos en la BD (Solo usamos este metodo al crear o actualizar usuario que es cuando recibimos datos por el usuario)
    public function sanitizarAtributos() {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach($atributos as $key => $value ) {
            $sanitizado[$key] = self::$db->escape_string($value);
        }
        return $sanitizado;
    }

    // Sincroniza BD con Objetos en memoria
    public function sincronizar($args=[]) { 
        foreach($args as $key => $value) {
          if(property_exists($this, $key) && !is_null($value)) {
            $this->$key = $value;
          }
        }
    }

    // Registros - CRUD
    public function guardar() {
        $resultado = '';
        if(!is_null($this->id)) {
            // actualizar
            $resultado = $this->actualizar();
        } else {
            // Creando un nuevo registro
            $resultado = $this->crear();
        }
        return $resultado;
    }

    // Todos los registros
    public static function all() {
        $query = "SELECT * FROM " . static::$tabla;
        $resultado = self::consultarSQL($query); //Retorna un array de objetos
        return $resultado;
    }

    // Busca un registro por su id
    public static function find($id) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE id = ${id}";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ; //Devuelve el primer objeto del arreglo (array[0])
    }

    // Obtener Registros con cierta cantidad
    public static function get($limite) {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT ${limite}";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ); //Devuelve el primer objeto del arreglo (array[0])
    }
    //Busca un registro dinamicamente
    public static function where($columna, $valor) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE ${columna} = '${valor}'";
        $resultado = self::consultarSQL($query); //Devuleve un array de objetos(cada objeto un registro de la consulta)
        return array_shift( $resultado ); //Obtenemos el primer objeto del arreglo (array[0])
    }

    //Consulta Plana de SQL (Utilizar cuando los métodos del modelo no son suficientes)
    public static function SQL($query) {
        $resultado = self::consultarSQL($query); //Devuleve un array de objetos(cada objeto un registro de la consulta)
        return $resultado; 
    }

    // crea un nuevo registro
    public function crear() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Insertar en la base de datos
        $query = " INSERT INTO " . static::$tabla . " ( ";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES (' "; 
        $query .= join("', '", array_values($atributos));
        $query .= " ') ";

        //return json_encode(['query' => $query]); //Para debuguear el query por medio de postman o en la consola de JS cuando separamos el front del backend por medio de una Api(video 530).
        
        // Resultado de la consulta
        $resultado = self::$db->query($query);
        return [
           'resultado' =>  $resultado,
           'id' => self::$db->insert_id //Devuelve el id autogenerado (AUTO_INCREMENT) que se utilizó en la última consulta. // Devuelve el ID generado por una query (normalmente INSERT) en una tabla con una columna que tenga el atributo AUTO_INCREMENT. Si no se enviaron declaraciones INSERT o UPDATE a través de esta conexión, o si la tabla modificada no tiene una columna con el atributo AUTO_INCREMENT, esta función devolverá cero.
        ];
    }

    // Actualizar el registro
    public function actualizar() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Iterar para ir agregando cada campo de la BD
        $valores = [];
        foreach($atributos as $key => $value) {
            $valores[] = "{$key}='{$value}'";
        }

        // Consulta SQL
        $query = "UPDATE " . static::$tabla ." SET ";
        $query .=  join(', ', $valores ); //Une elementos de un array en un string
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' "; //Escapa los caracteres especiales de una cadena para usarla en una sentencia SQL, tomando en cuenta el conjunto de caracteres actual de la conexión
        $query .= " LIMIT 1 "; 

        // Actualizar BD
        $resultado = self::$db->query($query);
        return $resultado;
    }

    // Eliminar un Registro por su ID
    public function eliminar() {
        $query = "DELETE FROM "  . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        $resultado = self::$db->query($query);
        return $resultado;
    }

}