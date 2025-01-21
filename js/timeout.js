// Control de inactividad 

var totalTiempo = 1800; // Tiempo en segundos 1 hora: 3600 seg. 30 min: 1800
var url = '?controller=user&opt=logout'
if (urlfriendlystatus == "ON") {
    url = '/cerrar-sesion/';
}

function updateReloj() {
    //document.getElementById('CuentaAtras').innerHTML = "Redireccionando en "+totalTiempo+" segundos";
    if (totalTiempo == 0) {
        window.location = url;

    } else {
        /* Restamos un segundo al tiempo restante */
        if (totalTiempo)
            totalTiempo -= 1;

        /* Ejecutamos nuevamente la función al pasar 1000 milisegundos (1 segundo) */

        setTimeout("updateReloj()", 1000);
    }
}

//updateReloj();
