<?php
foreach ($alertas as $key => $mensajes) : // Con este foreach iteramos sobre alertas para acceder a cada llave que es el tipo de alerta y su valor que sera un arreglo.
    foreach ($mensajes as $mensaje) : //Con este foreach iteramos sobre el valor ya que es un arreglo con mensajes de alerta.
?>
    <div class="alerta <?php echo $key; ?>"><?php echo $mensaje; ?></div> <!--Creamos un div que mostrara la alerta en el html, agregamos la clase alerta y otra clase que sera dinámica segun sea el tipo de alerta obtenido.-->
        
    <?php
    endforeach;
endforeach;

    ?>