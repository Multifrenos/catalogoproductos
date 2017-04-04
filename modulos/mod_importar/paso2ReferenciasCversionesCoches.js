var contadorAJAX;
var resultado;
function crearTablas() {
     var parametros = {
        'pulsado': 'CochesCrearTablas'
    };
        $.ajax({
            data: parametros,
            url: 'tareas.php',
            type: 'post',
            beforeSend: function () {
                $("#resultado").html('Creando tablas, espere por favor......<span><img src="./img/ajax-loader.gif"/></span>');
            },
            success: function (response) {
                $("#resultado").html('Terminamos de crear tablas......');
				resultado = response;
            }

        });
}

function CochesInsertTemporal() {
     var parametros = {
        'pulsado': 'CochesInsertTemporal'
    };
        $.ajax({
            data: parametros,
            url: 'tareas.php',
            type: 'post',
            beforeSend: function () {
                $("#resultado").html('Insertando datos tablas importar coches, espere por favor......<span><img src="./img/ajax-loader.gif"/></span>');
            },
            success: function (response) {
                $("#resultado").html('Terminamos de insertar tablas......');
				resultado = response;
            }

        });
}

function CochesUpdateTemporal() {
     var parametros = {
        'pulsado': 'CochesUpdateTemporal'
    };
        $.ajax({
            data: parametros,
            url: 'tareas.php',
            type: 'post',
            beforeSend: function () {
                $("#resultado").html('Insertando datos tablas importar coches, espere por favor......<span><img src="./img/ajax-loader.gif"/></span>');
            },
            success: function (response) {
                $("#resultado").html('Terminamos de insertar tablas......');
				resultado = response;
            }

        });
}
