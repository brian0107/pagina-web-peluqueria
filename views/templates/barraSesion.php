<div class="barra"> <!--Cerrar sesión-->
    <p>Hola: <?php echo $nombre ?? ''; ?></p>
    <a class="boton" href="/logout">Cerrar Sesión</a> <!--Los enlaces se usan cuando la accion es navegar a otra pagina o navegar a una parte de la misma pagina-->
</div>

<?php if(isset($_SESSION['admin'])) { ?>
  <div class="barra-servicios">
      <a class="boton" href="/admin">Ver Citas</a>
      <a class="boton" href="/servicios">Ver Servicios</a>
      <a class="boton" href="/servicios/crear">Nuevo Servicio</a>
  </div>
<?php } ?>