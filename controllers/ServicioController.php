<?php

namespace Controllers;

use Model\Servicio;
use MVC\Router;

class ServicioController {
    public static function index (Router $router){
        //METODO PARA MOSTRAR LOS SERVICOS
        if(!isset($_SESSION)){
            session_start();
        }

        isAdmin(); //protegemos la ruta

        $servicios = Servicio::all();

        $router->render('servicios/index', [
            'nombre' => $_SESSION['nombre'],
            'servicios' => $servicios
        ]);
    }

    public static function crear (Router $router){
        //METODO PARA CREAR SERVICIOS
        if(!isset($_SESSION)){
            session_start();
        }

        isAdmin(); //protegemos la ruta
        
        $servicio = new Servicio; //Creamos una instancia vacia para autocompletar
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $servicio->sincronizar($_POST); //Sincroniza el objeto en memoria con los datos del post
           
            $alertas = $servicio->validar();

            if(empty($alertas)) {
                $servicio->guardar();
                header('Location: /servicios');
            }

        }

        $router->render('servicios/crear', [
            'nombre' => $_SESSION['nombre'],
            'servicio' => $servicio,
            'alertas' => $alertas
        ]);
    }

    public static function actualizar (Router $router){
        //METODO PARA ACTUALIZAR SERVICIOS
        if(!isset($_SESSION)){
            session_start();
        }

        isAdmin(); //protegemos la ruta

        if(!is_numeric($_GET['id'])) return; //Si id no es numero, detenemos el programa
       
        $servicio = Servicio::find($_GET['id']); //Encontramos el Servicio con el id de la url
        $alertas = [];
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $servicio->sincronizar($_POST); //Sincroniza el objeto en memoria con los datos del post
            
            $alertas = $servicio->validar();

            if(empty($alertas)) {
                $servicio->guardar();
                header('Location: /servicios');
            }
        }

        $router->render('servicios/actualizar', [
            'nombre' => $_SESSION['nombre'],
            'servicio' => $servicio,
            'alertas' => $alertas
        ]);
    }

    public static function eliminar (){
        //METODO PARA ELIMINAR SERVICIOS
        if(!isset($_SESSION)){
            session_start();
        }

        isAdmin(); //protegemos la ruta
    
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $id = $_POST['id']; //Leemos el Id enviado por el form eliminar
            $servicio = Servicio::find($id); //Buscamos el servicio
            $servicio->eliminar(); //Eliminamos el servicio
            header('Location: /servicios');
        }
    }
}