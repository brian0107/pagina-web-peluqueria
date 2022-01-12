<?php

namespace Controllers;

use Model\AdminCita;
use MVC\Router;

class AdminController {
    public static function index( Router $router ) {
    /*Cuando un usuario se autentica en el login, loginController busca el usuario 
    y crea una sesión con la información de ese usuario */

        if(!isset($_SESSION)){ //Iniciamos la sesion  para acceder a los datos del administrador y mostrarlos en la vista
            session_start();
        }

        isAdmin(); //Verificamos que sea admin

        $fecha = $_GET['fecha'] ?? date('Y-m-d'); //Fecha seleccionada por el usuario, si no hay entonces genera la fecha actual.
        $fechas = explode('-', $fecha); //Creamos un array con los datos de la fecha para utilizar checkdate
        if( !checkdate( $fechas[1], $fechas[2], $fechas[0]) ){ //checkdate valida y retorna true si la fecha en la url es válida
            header('Location: /404');
        }

        // Consultar la base de datos con las citas del dia actual
        $consulta = "SELECT citas.id, citas.hora, CONCAT( usuarios.nombre, ' ', usuarios.apellido) as cliente, ";
        $consulta .= " usuarios.email, usuarios.telefono, servicios.nombre as servicio, servicios.precio  ";
        $consulta .= " FROM citas  ";
        $consulta .= " LEFT OUTER JOIN usuarios ";
        $consulta .= " ON citas.usuarioId=usuarios.id  ";
        $consulta .= " LEFT OUTER JOIN citasservicios ";
        $consulta .= " ON citasservicios.citaId=citas.id ";
        $consulta .= " LEFT OUTER JOIN servicios ";
        $consulta .= " ON servicios.id=citasservicios.servicioId ";
        $consulta .= " WHERE fecha =  '${fecha}' ";
        
        $citas = AdminCita::SQL($consulta); //metodo que consulta la BD y retorna un array de objetos con todas las citas

        $router->render('admin/index', [
            'nombre' => $_SESSION['nombre'],
            'citas' => $citas,
            'fecha' => $fecha
        ]);
    }
}