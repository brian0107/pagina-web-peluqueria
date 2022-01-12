<?php
namespace Model;
//Modelo para la tabla 'citasservicios' con relacion muchos a muchos, ya que muchas citas pueden usar muchos servicios. 
class CitaServicio extends ActiveRecord {
    protected static $tabla = 'citasServicios';
    protected static $columnasDB = ['id', 'citaId', 'servicioId']; 

    public $id;
    public $citaId;
    public $servicioId;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->citaId = $args['citaId'] ?? '';
        $this->servicioId = $args['servicioId'] ?? '';
    }
    

}