<?php
namespace Model;

 //Modelo para tabla 'citas' con relacion uno a muchos, ya que una persona puede crear muchas citas.
class Cita extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'citas'; //Variables Estaticas para que al usar los metodos de la clase ActiveRecord, estos usen las variables de esta clase hija.
    protected static $columnasDB = ['id', 'fecha', 'hora', 'usuarioId']; //Para crear un arreglo asociativo donde las key seran las columnas de la BD y los valores seran los datos ingresados por el usuario en el objeto de esta clase. Y crear una consulta a la BD con los datos sanitizados
   
    public $id;
    public $fecha;
    public $hora;
    public $usuarioId;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;               //Id de la cita
        $this->fecha = $args['fecha'] ?? '';          //Fecha de la cita
        $this->hora = $args['hora'] ?? '';           //Hora de la cita
        $this->usuarioId = $args['usuarioId'] ?? '';//Id del usuario
    }


}