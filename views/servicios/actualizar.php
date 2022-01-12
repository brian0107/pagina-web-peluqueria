<h1 class="nombre-pagina" >Actualizar Servicio</h1>
<p class="descripcion-pagina">Modifica los valores del formulario</p>

<?php include_once __DIR__ . '/../templates/barraSesion.php'; ?>
<?php include_once __DIR__ . '/../templates/alertas.php'; ?>

<form method="POST" class="formulario"> <!--Omitimos el action para respetar el id asignado en la url-->

    <?php include_once __DIR__ . '/formulario.php'; ?>

<input type="submit" class="boton" value="Actualizar Servicio">

</form>