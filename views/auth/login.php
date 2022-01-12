<h1 class="nombre-pagina">Login</h1> <!--Titulo Pagina-->
<p class="descripcion-pagina">Inicia Sesión con tus Datos</p> <!--Descripcion-->

<?php include_once __DIR__ . "/../templates/alertas.php" ?>

<form class="formulario" method="POST" action="/"> <!--Formulario-->

    <div class="campo">
        <label for="email">Email</label>
        <input type="email" id="email" placeholder="Tu Email" name="email">
    </div>

    <div class="campo">
        <label for="password">Password</label>
        <input type="password" id="password" placeholder="Tu Password" name="password">
    </div>

    <input type="submit" class="boton" value="Iniciar Sesión">

</form>

<div class="acciones"> <!--Opciones de crear cuenta y reestablecer contraseña-->
    <a href="/crear-cuenta">¿Aún no tienes una cuenta? Crear una</a>
    <a href="/olvide">¿Olvidaste tu password?</a>
</div>