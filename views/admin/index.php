<h1 class="nombre-pagina">Panel de Administración</h1>

<!-- Incluimos la barra para cerrar sesión-->
<?php include_once __DIR__ . '/../templates/barraSesion.php'; ?>

<h2>Buscar Citas</h2>
<!--Buscador de citas-->
<div class="busqueda">
    <form class="formulario">
        <div class="campo">
            <label for="fecha">Fecha</label>
            <input type="date" id="fecha" name="fecha" value="<?php echo $fecha; ?>" > 
        </div>
    </form>
</div>

<?php 
    if(count($citas) === 0) { //count cuenta los elementos de un array
        echo "<h2>No hay Citas en esta Fecha</h2>"; //Si no hay citas en la fecha seleccionada entonces mostramos un mensaje
    }
?>

<!--Mostrar las citas-->
<div id="citas-admin">
    <ul class="citas"> <!--Hacemos un listado con las citas-->
        <?php
            $idCitaImprimida = 0;
        foreach ($citas as $key => $cita) { // $key es la posicion del objeto en el arreglo y $cita es el objeto
           
            if ($cita->id !==  $idCitaImprimida ) { //Solo imprimimos cuando el id de cita es diferente. Evitamos la repeticion de ciertos datos con el mismo id de cita
           
                $total = 0; // La variable total se reiniciara a 0 cuando el id de cita es diferente
       ?>
                <li>
                    <!--Creamos un elemento de lista por cada id de cita diferente encontrado-->
                    <p>ID: <span><?php echo $cita->id; ?></span></p>
                    <p>Hora: <span><?php echo $cita->hora; ?></span></p>
                    <p>Cliente: <span><?php echo $cita->cliente; ?></span></p>
                    <p>Email: <span><?php echo $cita->email; ?></span></p>
                    <p>Teléfono: <span><?php echo $cita->telefono; ?></span></p>
                    <h3>Servicios</h3>
        <?php
                $idCitaImprimida = $cita->id;  //Obtenemos el id de la cita imprimida para comprobar en el IF

            } // Fin del IF EVITA REPETICION DE DATOS
                $total += $cita->precio; //En cada iteracion sumamos el precio del servicio al total
       ?>
                <p class="servicio"><?php echo $cita->servicio . " " . $cita->precio; ?></p> <!--En cada iteración imprimimos el nombre y precio del servicio-->
                
        <?php
                $actual = $cita->id; //Id cita posicion actual
                $proximo = $citas[$key + 1]->id ?? 0; //Id cita siguiente posicion

                if( esUltimo($actual, $proximo) ) { //Si estamos en el ultimo servicio de una cita-->
       ?> 
                <p class="total">Total: <span>$ <?php echo $total; ?></span></p> <!--Mostramos el precio total de los servicios-->
                 
                <!--Eliminar la cita-->
                <form action="/api/eliminar" method="POST">
                    <input type="hidden" name="id" value="<?php echo $cita->id; ?>">
                    <input type="submit" class="boton-eliminar" value="Eliminar">
                </form>
        <?php
             } //Fin del IF 
         } // Fin de Foreach 
       ?>
    </ul>
</div>

<?php $script = "<script src='build/js/buscador.js'></script>" ?>