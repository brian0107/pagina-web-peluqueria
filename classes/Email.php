<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;



class Email
{

    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token) //Recibe el email al que le enviaremos el correo de confirmación, asi como el nombre y el token
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion()
    {

        //Crear el objeto de email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '3d32d57ef520a6';
        $mail->Password = 'ef622af605314f';

        $mail->setFrom('cuentas@appsalon.com'); //Quien envia el email
        $mail->addAddress('cuentas@appsalon.com', 'appsalon.com'); //Quien recibe
        $mail->Subject = 'Confirma tu cuenta'; //Asunto

        // Set HTML
        $mail->isHTML(TRUE); //Decimos que vamos a utilizar HTML
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has creado tu cuenta
        en App Salon, solo debes confirmarla presionando el siguiente enlace</p>";
        $contenido .= "Presiona aquí: <a href='http://localhost:3000/confirmar-cuenta?token=" .
            $this->token . "'>Confirmar Cuenta</a>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje</p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;

        //Enviar el mail
        $mail->send();
    }

    public function enviarInstrucciones()
    {

        //Crear el objeto de email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '3d32d57ef520a6';
        $mail->Password = 'ef622af605314f';

        $mail->setFrom('cuentas@appsalon.com'); //Quien envia el email
        $mail->addAddress('cuentas@appsalon.com', 'appsalon.com'); //Quien recibe
        $mail->Subject = 'Reestablece tu Password'; //Asunto

        // Set HTML
        $mail->isHTML(TRUE); //Decimos que vamos a utilizar HTML
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has solicitado 
         reestablecer tu password, sigue el siguiente enlace para hacerlo.</p>";
        $contenido .= "Presiona aquí: <a href='http://localhost:3000/recuperar?token=" .
            $this->token . "'>Reestablecer Password</a>";
        $contenido .= "<p>Si tu no solicitaste este cambio, puedes ignorar el mensaje</p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;

        //Enviar el mail
        $mail->send();
    }
}
