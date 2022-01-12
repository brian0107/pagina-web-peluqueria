<h1 class="nombre-pagina">Recuperar Password</h1>
<p class="descripcion-pagina">Coloca tu nuevo password a continuación</p>

<?php include_once __DIR__ . "/../templates/alertas.php" ?>

<?php if($error) return; //Detenemos la ejecución?>

<form class="formulario" method="POST"> <!--Descartamos el action ya que tenemos un token y no queremos que se elimine. De igual forma enviara los datos a la pagina actual -->

    <div class="campo">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Tu Nuevo Password">
    </div>

    <input type="submit" class="boton" value="Guardar Nuevo Password">

</form>

<div class="acciones">
    <!--Opciones de crear cuenta y reestablecer contraseña-->
    <a href="/">¿Ya tienes una cuenta? Inicia Sesión</a>
    <a href="/crear-cuenta">¿Aún no tienes una cuenta? Crear Una</a>
</div>