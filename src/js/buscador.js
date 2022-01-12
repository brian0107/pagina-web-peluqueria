document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
});

function iniciarApp(){
    buscarPorFecha();
}

function buscarPorFecha() {
    const fechaInput = document.querySelector('#fecha');
    fechaInput.addEventListener('input', function(e) { //Evento que se dispara cuando se seleciona una fecha
        const fechaSeleccionada = e.target.value;
        
        window.location = `?fecha=${fechaSeleccionada}`; //Redireccionamos al usuario, creando un query string en la misma Url con la fecha seleccionada para mostrar las citas de esa fecha.
    });
}