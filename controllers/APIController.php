<?php

namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;
//Api consumida por JS (Envia y Recibe datos del app.js)
//Con la Api separamos el Fronted del Backend nos retorna respuestas Json, por lo que no requiere el render de router para mostrar datos
class APIController {

    public static function index() {

        $servicios = Servicio::all(); //Obtenemos un array de objetos con todos los servicios de la BD
      
        echo json_encode($servicios); //Convertimos la respuesta a Json para poder ser interpretamos en JS como objetos.
    }

    public static function guardar() {

        //Creamos el objeto de Cita que tiene una forma entonces solo toma los valores de las llaves de POST que necesita (fecha, hora, usuarioId).
        $cita = new Cita($_POST); 
       
        // Almacena la Cita y devuelve el ID otorgado a la cita
        $resultado = $cita->guardar(); 

        //Obtenemos el id de la cita
        $id = $resultado['id']; 

        //Creamos un arreglo que contenga los id de los servicios obtenidos
        $idServicios = explode(",", $_POST['servicios']);  // explode convierte un string en un arreglo, recibe un separador y el string.
    
        /*Iteramos el arreglo creado y por cada id encontrado insertaremos un registro
         en citasservicios con el id de la Cita y el id del servicio.*/
        foreach($idServicios as $idServicio) {
            $args = [
                'citaId' => $id,
                'servicioId' => $idServicio
            ];
            $citaServicio = new CitaServicio($args); //Creamos el objeto de CitaServicio
            $citaServicio->guardar(); // Insertamos en la BD
        }
        // Retornamos una respuesta
        echo json_encode(['resultado' => $resultado]); //Retornamos como Json el array asociativo
    }

    public static function eliminar (){
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            //Leemos el id
            $id = $_POST['id'];
            //Encontramos el registro
            $cita = Cita::find($id);
            //Eliminamos el registro
            $cita->eliminar();
            header('Location:' . $_SERVER['HTTP_REFERER']); //HTTP_REFERER redirije a la url de donde veniamos (anterior)
        }
    }
}
/*
Js no puede consultar una base de datos pero si lo puede hacer por medio de una api que puede estar
hecha en cualquier lenguaje(En este caso PHP), exportar los datos de la consulta a Json
y en Js consumirlo con fetch y por ultimo con scriptin de js (Crear elementos HTML y asignarle los valores del Json) 
podemes imprimir los resultados en pantalla.

Para enviar datos de Js a PHP utilizamos FETCH con el metodo POST y por medio de un
FormData enviamos la información.
 */
