<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Salón</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/build/css/app.css">
    <!--Necesario tener la / al principio de la ruta para que busque desde la raiz
    del proyecto, por que si una ruta tiene mas de 2 niveles de profundidad
    (/servicios/crear) tendriamos un problema para encontrar los estilos (Video 550)-->
</head>

<body>

    <div class="contenedor-app">
        <div class="imagen"></div>
        <div class="app">
            <?php echo $contenido; ?>
        </div>
    </div>

    <?php echo $script ?? ''; //Si se define la variable en el archivo de contenido entonces la imprime ?> 

</body>

</html>