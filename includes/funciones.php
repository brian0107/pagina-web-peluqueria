<?php

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

//Funcion para calcular el Total a Pagar (video 541)
function esUltimo(string $actual, string $proximo) : bool{
    if($actual !== $proximo) { //Si el id de la cita actual es diferente al proximo, entonces ese es el ultimo elemento de una cita.
        return true; //Retornamos true
    }else {
        return false;
    }
}

// Funcion que revisa que el usuario esta autenticado
function isAuth() : void { 
    //Si la llave de login no existe o no esta como true
    if(!isset($_SESSION['login'])){  
        header('Location: /');  //Redirigimos al login
    }
}
// Funcion para verificar que sea un admin
function isAdmin() : void{
    if(!isset($_SESSION['admin'])){  
        header('Location: /');  //Redirigimos al login
    }
}