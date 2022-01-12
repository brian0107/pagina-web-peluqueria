<?php

namespace Model;

class Servicio extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'servicios';
    protected static $columnasDB = ['id', 'nombre', 'precio'];

    public $id;
    public $nombre;
    public $precio;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->precio = $args['precio'] ?? '';
    }

    public function validar()
    {
        if(!$this->nombre) { //Validar que el campo nombre no este vacio
            self::$alertas['error'][] = 'El nombre del servicio es obligatorio';
        }

        if(!$this->precio) { //Validar que el campo precio no este vacio
            self::$alertas['error'][] = 'El precio del servicio es obligatorio';
        }

        if(!is_numeric($this->precio)) { //Validar que el precio sea un numero
            self::$alertas['error'][] = 'Formato de precio no válido';
        }
        
        return self::$alertas;
    }
}