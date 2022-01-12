let paso = 1; //La seccion servicios se mostrara al entrar a la pagina citas
const pasoInicial = 1;
const pasoFinal = 3;

const cita = {
  //Objeto de cita que se guardara en la base de datos
  id: "",
  nombre: "",
  fecha: "",
  hora: "",
  servicios: [], //Arreglo de objetos con los datos de los servicios seleccionados por el cliente.
};
document.addEventListener("DOMContentLoaded", function () {
  iniciarApp();
});

function iniciarApp() {
  mostrarSeccion(); // Muestra y oculta las secciones. Función a llamar cada vez que se precione un tab
  tabs(); // Cambia la sección cuando se presionen los tabs
  botonesPaginador(); // Agrega o quita botones del paginador
  paginaAnterior(); // funcion boton anterior del paginador
  paginaSiguiente(); // funcion boton siguiente del paginador

  consultarAPI(); // Consulta la API en el backend de PHP para obtener los servicios y despues poder mostrarlos y seleccionarlos

  idCliente(); // Añade el id del cliente que inicio sesión, al objeto de cita
  nombreCliente(); // Añade el nombre del cliente que inicio sesión, al objeto de cita
  seleccionarFecha(); // Añade la fecha de la cita que el cliente selecciono, en el objeto de cita
  seleccionarHora(); // Añade la hora de la cita que el cliente selecciono, en el objeto cita

  mostrarResumen(); // Muestra el resumen de la cita
}

function mostrarSeccion() {
  // Ocultar la sección que tenga la clase de mostrar
  const seccionAnterior = document.querySelector(".mostrar");
  if (seccionAnterior) {
    seccionAnterior.classList.remove("mostrar"); //La primera vez ninguna seccion tendra la clase mostrar por eso decimos que si un elemento ya la contiene entonces se la quitamos.
  }
  // Mostrar la seccion con el paso perteneciente al tab presionado
  const seccion = document.querySelector(`#paso-${paso}`);
  seccion.classList.add("mostrar");
  // Quita la clase de actual al tab anterior
  const tabAnterior = document.querySelector(".actual");
  if (tabAnterior) {
    tabAnterior.classList.remove("actual");
  }
  // Resalta el tab actual
  const tab = document.querySelector(`[data-paso="${paso}"]`); //Sintaxis de selector de atributo ya que no es una clase o un id.
  tab.classList.add("actual");
}

function tabs() {
  const botones = document.querySelectorAll(".tabs button"); //Seleccionamos todos los botones dentro de la clase tabs
  botones.forEach((boton) => {
    boton.addEventListener("click", function (e) {
      paso = parseInt(e.target.dataset.paso); // Obtenemos el numero de la seccion a mostrar segun el boton que sea presionado gracias al atributo data-paso que creamos en cada boton.
      mostrarSeccion(); // Mostrar la seccion con el paso perteneciente al boton presionado
      botonesPaginador(); //Mostramos o ocultamos los botones del paginador cada vez que cambiamos de seccion
    });
  });
}

function botonesPaginador() {
  const paginaAnterior = document.querySelector("#anterior");
  const paginaSiguiente = document.querySelector("#siguiente");

  if (paso === 1) {
    paginaAnterior.classList.add("ocultar");
    paginaSiguiente.classList.remove("ocultar");
  } else if (paso === 3) {
    paginaAnterior.classList.remove("ocultar");
    paginaSiguiente.classList.add("ocultar");
    mostrarResumen(); // Si accedemos a la seccion resumen, verificamos que no falten datos en el objeto cita.
  } else {
    paginaAnterior.classList.remove("ocultar");
    paginaSiguiente.classList.remove("ocultar");
  }
  mostrarSeccion();
}
function paginaAnterior() {
  const paginaAnterior = document.querySelector("#anterior");
  paginaAnterior.addEventListener("click", function () {
    if (paso <= pasoInicial) return;
    paso--;
    botonesPaginador();
  });
}

function paginaSiguiente() {
  const paginaSiguiente = document.querySelector("#siguiente");
  paginaSiguiente.addEventListener("click", function () {
    if (paso >= pasoFinal) return;
    paso++;
    botonesPaginador();
  });
}

async function consultarAPI() {
  //Try catch previene que la aplicacion deje de funcionar si ocurre un error. Muestra el error pero la app sigue funcionando. Solo se usa en partes criticas ya que consume memoria.
  try {
    const url = "http://localhost:3000/api/servicios"; // Url donde se consulto la BD y el resultado se transformo en tipo Json para poder ser leido en JS.
    const respuesta = await fetch(url); //Consultamos nuestra Api que consulta la BD
    const servicios = await respuesta.json(); //Obtenemos los resultados como un array de Jsons. (Js interpreta esto como un array de objetos)
    mostrarServicios(servicios); //Mostramos los Servicios
  } catch (error) {
    console.log(error);
  }
}

function mostrarServicios(servicios) {
  servicios.forEach((servicio) => {
    const { id, nombre, precio } = servicio; // Creamos una variable con cada llave y valor del objeto.

    const nombreServicio = document.createElement("P"); //Creamos el Parrafo con el nombre
    nombreServicio.classList.add("nombre-servicio");
    nombreServicio.textContent = nombre;

    const precioServicio = document.createElement("P"); //Creamos el Parrafo con el precio
    precioServicio.classList.add("precio-servicio");
    precioServicio.textContent = `$${precio}`; //Usamos template string para agregar el simbolo de dinero al precio.

    const servicioDiv = document.createElement("DIV"); //Creamos el Div que contiene el parrafo de nombre y precio
    servicioDiv.classList.add("servicio");
    servicioDiv.dataset.idServicio = id;
    servicioDiv.onclick = function () {
      //Al dar click en un div de servicio se ejecuta la funcion seleccionarServicio.
      seleccionarServicio(servicio); //Le pasamos el objeto de servicio completo con todos los datos del servicio (id,nombre,precio) a la funcion seleccionarServicio.
    };
    servicioDiv.appendChild(nombreServicio); //Agregamos el parrafo de nombre al div
    servicioDiv.appendChild(precioServicio); //Agregamos el parrafo de precio al div

    document.querySelector("#servicios").appendChild(servicioDiv); //Agregamos el div del servicio al Div con el id servicios en la vista de cita que contendra todos los servicios.
  });
}

function seleccionarServicio(servicio) {
  const { id } = servicio; //Extraemos el id del servicio seleccionado
  const { servicios } = cita; //Extraemos el arreglo de servicios del objeto cita.

  //Identificar el servico al que se le da click
  const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);

  //Comprobar si un servicio ya fue agregado. Revisando si en el arreglo de la cita ya se encuentra agregado el servicio seleccionado.
  if (servicios.some((agregado) => agregado.id === id)) {
    // some() array method que comprueba si al menos un elemento del array cumple con la condición implementada por la función proporcionada.
    //Eliminamos el servicio del arreglo servicios del objeto cita
    cita.servicios = servicios.filter((agregado) => agregado.id !== id); //Si ya se encuentra agregado entonces eliminamos el servicio creando un nuevo arreglo unicamente con los servicios que no coincidan con el Id del servicio seleccionado.
    divServicio.classList.remove("seleccionado"); //Quitamos la clase "seleccionado" para desmarcar el div con el servicio al que se dio click.
  } else {
    // Agregamos el servicio al arreglo servicios del objeto Cita
    cita.servicios = [...servicios, servicio]; //con spread operator(...) creamos un nuevo array con los datos del arreglo servicios del objeto cita, pero agregandole el nuevo servicio seleccionado.
    divServicio.classList.add("seleccionado"); //Agregamos la clase "seleccionado" para marcar el div con el servicio al que se dio click.
  }
}
function idCliente() {
  cita.id = document.querySelector("#id").value;
}

function nombreCliente() {
  cita.nombre = document.querySelector("#nombre").value;
}

function seleccionarFecha() {
  const inputFecha = document.querySelector("#fecha");
  inputFecha.addEventListener("input", function (e) {
    //Evento que se dispara cuando seleccionamos una fecha en el input type date

    const dia = new Date(e.target.value).getUTCDay(); //Obtenemos el día seleccionado por el usuario gracias al objeto Date de js,  le pasamos la fecha que el usuario selecciono y obtenemos el día con la función getUTCDay(); //Devuelve el numero del dia, 0 es domingo.
    if ([6, 0].includes(dia)) {
      //includes es un array method que comprueba si un valor existe y recibe el valor a buscar. Entonces le pasamos el dia seleccionado por el usuario
      e.target.value = ""; // Si el dia seleccionado es sabado o domingo entonces reseteamos el campo
      cita.fecha = "";
      mostrarAlerta("Fines de semana no permitidos", "error", ".formulario");
    } else {
      //Si el dia seleccionado no es sabado ni domingo entonces obtenemos la fecha.
      cita.fecha = e.target.value; //Target es el elemento que disparo este evento, entonces obtenemos el valor del elemento que es la fecha seleccionada.
    }
  });
}

function seleccionarHora() {
  const inputHora = document.querySelector("#hora");
  inputHora.addEventListener("input", function (e) {
    const horaCita = e.target.value;
    const hora = horaCita.split(":")[0]; //split separa un string creando un array. En este caso separamos cuando haya : y seleccionamos la posicion 0 del array que sera la hora.

    if (hora < 10 || hora > 20) {
      // Solo permite horarios de 10am a 8pm
      e.target.value = "";
      cita.hora = "";
      mostrarAlerta("Hora no válida", "error", ".formulario");
    } else {
      cita.hora = e.target.value;
    }
  });
}

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true) {
  //Si no se agrega el 4to parametro por default es true
  // Previene que se generen más de 1 alerta
  const alertaPrevia = document.querySelector(".alerta");
  if (alertaPrevia) {
    //Si ya existe una alerta mostrandose
    alertaPrevia.remove(); //Eliminamos la alerta para generar otra
  }
  // Scripting para crear la alerta
  const alerta = document.createElement("DIV"); //Creamos un div
  alerta.textContent = mensaje; //Agregamos el texto de alerta
  alerta.classList.add("alerta"); //Agregamos la clase alerta que contiene estilos para mostrar la alerta
  alerta.classList.add(tipo); //Agregamos el tipo de alerta ("error": la pinta de rojo y "exito": la pinta de Verde).

  const referencia = document.querySelector(elemento); //Seleccionamos el elemento
  referencia.appendChild(alerta); // Agregamos la alerta al elemento

  if (desaparece) {
    //Si desaparece es true
    // Eliminar la alerta en 3 segundos
    setTimeout(() => {
      alerta.remove();
    }, 3000);
  }
}

function mostrarResumen() {
  const resumen = document.querySelector(".contenido-resumen"); //Seleccionamos la seccion resumen

  //Limpiar el contenido de Resumen
  while (resumen.firstChild) {
    //Mientras tenga elementos, se eliminaran
    resumen.removeChild(resumen.firstChild);
  }
  if (Object.values(cita).includes("") || cita.servicios.length === 0) {
    //Accedemos a todos los valores del objeto cita y verificamos que ninguno este vacio y tambien verificamos que el arreglo servicios no este vacío.
    mostrarAlerta(
      "Faltan datos de servicios, Fecha u Hora",
      "error",
      ".contenido-resumen",
      false
    );
    return;
  }

  // Ya hemos pasado la validación entonces podemos crear los elementos para mostrar el resumen
  const { nombre, fecha, hora, servicios } = cita;

  //Heading para Servicios en Resumen
  const headingServicios = document.createElement("H3");
  headingServicios.textContent = "Resumen de Servicios";
  resumen.appendChild(headingServicios);

  // Iterando en el array servicios y mostrando cada servicio
  servicios.forEach((servicio) => {
    const { precio, nombre } = servicio;

    //Crear el contenedor del servicio
    const contenedorServicio = document.createElement("DIV");
    contenedorServicio.classList.add("contenedor-servicio");

    //Crear el parrafo con el nombre del servicio
    const textoServicio = document.createElement("P");
    textoServicio.textContent = nombre;

    //Crear el parrafo con el precio del servicio
    const precioServicio = document.createElement("P");
    precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;

    //Agregar el nombre y el precio al contenedor
    contenedorServicio.appendChild(textoServicio);
    contenedorServicio.appendChild(precioServicio);

    //Agregar el contenedor del servicio al resumen
    resumen.appendChild(contenedorServicio);
  });

  //Heading para datos Cita en Resumen
  const headingCita = document.createElement("H3");
  headingCita.textContent = "Resumen de Cita";
  resumen.appendChild(headingCita);

  //Mostrar el Nombre del cliente
  const nombreCliente = document.createElement("P");
  nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

  //Mostrar la Fecha de la cita
  const fechaCita = document.createElement("P");
  const fechaFormateada = formatearFecha(fecha); //Transforma la fecha para la vista pero no la modifica en el objeto.
  fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;
  //Mostrar la Hora de la cita
  const horaCita = document.createElement("P");
  horaCita.innerHTML = `<span>Hora:</span> ${hora} Horas`;

  // Boton para Crear una cita
  const botonReservar = document.createElement("BUTTON");
  botonReservar.classList.add("boton");
  botonReservar.textContent = "Reservar Cita";
  botonReservar.onclick = reservarCita;

  //Agregar nombre, fecha y hora al resumen
  resumen.appendChild(nombreCliente);
  resumen.appendChild(fechaCita);
  resumen.appendChild(horaCita);
  resumen.appendChild(botonReservar);
}

function formatearFecha(fecha) {
  // Formatear la fecha en español
  const dateSplit = fecha.split("-"); //Creamos un array con los datos de la fecha. '2021-12-06' -> [2021, 12, 06]
  const fechaObj = new Date(dateSplit[0], dateSplit[1] - 1, dateSplit[2]); // el "-1" es necesario por que en el objeto date los meses empiezan con enero = 0. Necesitamos un objeto Date para acceder al método que transforma la hora a un string en español o el idioma que quieras.

  const opciones = {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  }; //Para definir dia de la semena largo y el mes largo
  const fechaFormateada = fechaObj.toLocaleDateString("es-MX", opciones); //Devulve una cadena con una representación de la fecha en un idioma en especifico, en este caso español, puedes cambiar a ingles con 'en-US'
  return fechaFormateada;
}

async function reservarCita() {
  const { fecha, hora, id, servicios } = cita;
  //Iteramos en el arreglo servicios que contiene objetos de servicios y obtenemos el id de cada servicio
  const idServicios = servicios.map((servicio) => servicio.id); //map retorna un nuevo arreglo con los id de los servicios.

  const datos = new FormData(); //Enviar datos en JS, hacia la Api en PHP
  //Agregamos los datos a FormData
  datos.append("fecha", fecha); //Datos para tbl citas
  datos.append("hora", hora);
  datos.append("usuarioId", id);
  datos.append("servicios", idServicios); //Datos para tbl citasservicios
  //console.log([...datos]);

  try { //Enviamos los datos de la cita a la API, si algo sale mal se ejecuta el Catch con el error y nuestra aplicación no dejara de funcionar.
    const url = "http://localhost:3000/api/citas"; //Ruta de la Api.
    const respuesta = await fetch(url, {
      method: "POST", //Enviamos via POST
      body: datos, //Detecta y envia los datos que tiene FormData, las llaves deben llamarse igual que las columnas de la tabla donde se insertaran los datos.
    });

   const resultado = await respuesta.json(); // Obtenemos la respuesta tipo Json 
   // console.log(resultado.resultado); //Retorna true si se inserto correctamente.

    //Alerta de exito
    if (resultado.resultado) {
      //.resultado es una variable que retornamos en activerecord cuando insertamos un registro, retorna true o false.
      Swal.fire({
        icon: "success",
        title: "Cita Creada",
        text: "Tu cita fue creada correctamente",
      }).then(() => {
        setTimeout(() => {
          window.location.reload(); // Cuando el usuario presione OK sobre la alerta recargamos la pagina 3 segundos despues para evitar citas duplicadas
        }, 3000);
      });
    }

  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Hubo un error al guardar la cita",
    });
  }
}

/*
NOTA 1: Si en un scriptin (crear HTML con JS) agregamos un evento a un elemento, en este
caso el evento onclick a un elemento creado para que cuando el usuario de click a un 
elemento creado se ejecute una función que RECIBE PARAMETROS. 

 *La manera correcta seria hacerlo con un Callback de esta manera: 
 servicioDiv.onclick = function() {  //Funciona como esperamos, solo al dar click en el elemento se activara la función
   seleccionarServicio(servicio);
 }
 *Esta seria una manera Incorrecta de hacerlo:
    servicioDiv.onclick = seleccionarServicio(servicio); // No funciona como esperamos, JS interpreta que debe ejecutar la función automaticamente cada vez que se crea el elemento. Por lo que nuestro evento onclick no funcionara.

 *En caso que la función  NO REQUIERA PARAMETROS, la manera correcta seria:
 botonReservar.onclick = reservarCita;

 *Esta seria la manera incorrecta de hacerlo:
 botonReservar.onlick = reservarCita(); // JS interpreta que debe ejecutar la función automaticamente cada vez que se crea el elemento.


NOTA 2: Los elementos en HTML al ser seleccionados con JS, Js los interpreta como
 objetos, es decir podemos acceder a los atributos del elemento seleccionado por medio
 de la sintaxis de objeto (.nombreAtributo) por ejemplo:
 const nombre = document.querySelector('#nombre').value;

NOTA 3: al enviar datos desde JS hacia una Api en PHP por medio de FormData
 no podemos ver los datos que se envian, pero podemos aplicar un truco para 
 verlos con spread operator:

 const datos = new FormData();
 datos.append('nombre','Juan'); 
 console.log([...datos]); //Tomamos una copia del FormData y la formateamos en un arreglo.

 */
