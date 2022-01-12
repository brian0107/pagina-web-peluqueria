<!--Archivo PHP que tiene un archivo JS asociado-->
<h1 class="nombre-pagina">Crear Nueva Cita</h1>
<p class="descripcion-pagina">Elige tus servicios y coloca tus datos</p>

<!-- Incluimos la barra para cerrar sesión-->
<?php include_once __DIR__ . '/../templates/barraSesion.php'; ?>

<div id="app">
    <nav class="tabs">
        <button type="button" data-paso="1">Servicios</button> <!--Los botones se usan cuando la accion es realizar algo en la misma pagina-->
        <button type="button" data-paso="2">Información Cita</button>
        <button type="button" data-paso="3">Resumen</button>
    </nav>

    <div id="paso-1" class="seccion">
        <h2>Servicios</h2>
        <p class="text-center">Elige tus servicios a continuación</p>
        <div id="servicios" class="listado-servicios"></div> <!--Con Js consultamos la BD en php, la exportamos a Json e insertamos en este div los datos.-->
    </div>

    <div id="paso-2" class="seccion">
        <h2>Tus Datos y Cita</h2>
        <p class="text-center">Coloca tus datos y fecha de tu cita</p>

        <form class="formulario">

            <div class="campo">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" placeholder="Tu Nombre" value=" <?php echo $nombre ?? ''; ?>" disabled>
            </div>

            <div class="campo">
                <label for="fecha">Fecha</label>
                <input type="date" id="fecha" min="<?php echo date('Y-m-d');?>"> 
                <!-- strtotime('+1 day') como segundo parametro dentro del metodo date() hace que no se pueda seleccionar el dia actual, solo el siguiente. Si se coloca '-' solo el anterior-->
            </div>
            
            <div class="campo">
                <label for="hora">Hora</label>
                <input type="time" id="hora">
            </div>

            <input type="hidden" id="id" value="<?php echo $id; ?>"> <!--Para agregar el id del cliente al objeto de cita y poder insertar en la tabla cita ya que pide id del cliente. -->
        </form>

    </div>

    <div id="paso-3" class="seccion contenido-resumen"> </div> <!--Con Js insertamos en este div los datos de la cita creada.-->

    <div class="paginacion">
        <button id="anterior" class="boton">&laquo; Anterior</button>
        <button id="siguiente" class="boton">Siguiente &raquo;</button>
    </div>

</div>

<?php //Definimos la variable script que se imprimira en layout al visitar esta pagina ya que el codigo JS es referente a los elementos de esta pagina.
$script = "
<script src='//cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<script src='build/js/app.js'></script>
";
 ?>