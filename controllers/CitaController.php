<?php

namespace Controllers;

use MVC\Router;
//Controlador que redirige al archivo para crear las citas.
class CitaController {
    public static function index (Router $router) {
        /*Cuando un usuario se autentica en el login, loginController busca el usuario
             y crea una sesión con la información de ese usuario */

         
        if(!isset($_SESSION)) { //Iniciamos la sesion  para acceder a los datos del cliente y mostrarlos en la vista
            session_start(); 
        }

        isAuth(); //Antes de mostrar la vista, verificamos que se haya iniciado sesión

        $router->render('cita/index', [
            'nombre' => $_SESSION['nombre'], //Pasamos el nombre del usuario a la vista para mostrarlo en el formulario
            'id' => $_SESSION['id'] // Pasamos el id del usuario a la vista para agregarlo en el formulario a un input hidden
        ]);
    }
}