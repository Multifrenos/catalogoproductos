var contadorAJAX;

function crearTablas() {
    contadorAJAX = 0;
    if ($('#chkTMarcas').prop('checked')) {
        ejecutarConsulta('tmarcas', 'chkTMarcas');
        $("#resultado").html($("#resultado").html() + '<BR>' + 'tmarcas OK');
        contadorAJAX += 1;
    }
    if ($('#chkTCombustibles').prop('checked')) {
        ejecutarConsulta('tcombustibles', 'chkTCombustibles');
        $("#resultado").html($("#resultado").html() + '<BR>' + 'tcombustibles OK');
        contadorAJAX += 1;
    }

    if ($('#chkTModelos').prop('checked')) {
        ejecutarConsulta('tmodelos', 'chkTModelos');
        $("#resultado").html($("#resultado").html() + '<BR>' + 'tmodelos OK');
        contadorAJAX += 1;
    }
    if ($('#chkTVersiones').prop('checked')) {
        ejecutarConsulta('tversiones', 'chkTVersiones');
        $("#resultado").html($("#resultado").html() + '<BR>' + 'tversion OK');
        contadorAJAX += 1;
    }
}


//   "privacidad": $('input:checkbox[id=chkprivacidad]:checked').val() ? true : false

function crearRelaciones() {
    contadorAJAX = 0;

    if ($('#chkMarcas').prop('checked')) {
        ejecutarConsulta('marcas', 'chkMarcas');
        $("#resultado").html($("#resultado").html() + '<BR>' + 'marcas OK');
        contadorAJAX += 1;
    }
    if ($('#chkCombustibles').prop('checked')) {
        ejecutarConsulta('combustibles', 'chkCombustibles');
        $("#resultado").html($("#resultado").html() + '<BR>' + 'combustibles OK');
        contadorAJAX += 1;
    }

    if ($('#chkModelos').prop('checked')) {
        ejecutarConsulta('modelos', 'chkModelos');
        $("#resultado").html($("#resultado").html() + '<BR>' + 'modelos OK');
        contadorAJAX += 1;
    }
    if ($('#chkVersiones').prop('checked')) {
        ejecutarConsulta('versiones', 'chkVersiones');
        $("#resultado").html($("#resultado").html() + '<BR>' + 'version OK');
        contadorAJAX += 1;
    }

}

function ejecutarConsulta(nombreConsulta, cajaCheck) {
    var hayerror = false;
    var parametros = {
        "consulta": nombreConsulta
    };
    if ($('#' + cajaCheck).prop('checked')) {
        $.ajax({
//        async: false, // Carga peticiones de forma sincrono , no asincrono.
            data: parametros,
            url: 'ajaxPaso2Coches.php',
            type: 'post',
            beforeSend: function () {
                $("#esperando").html('Creando tablas, espere por favor......<span><img src="./img/ajax-loader.gif"/></span>');
            },
            success: function (response) {
                if (response) {
                    var obj = jQuery.parseJSON(response);
                    lista = '';
                    $.each(obj, function (key, value) {
                        if (value[0] !== 0) {
                            lista += '<li >';
                            lista += value[1]
                            lista += '</li>';
                        }
                    });
                    if (lista === '') {
                        $('#' + cajaCheck).prop('checked', true);
                    } else {
                        lista = '<ul class="menuli">' + lista;
                        lista = '<div class="table-responsive">' + lista;
                        lista += '</ul>';
                        lista += '</div>';
                        $("#resultado").html(lista);
                        hayerror = true;
                    }
                }
                contadorAJAX -= 1;
                if (contadorAJAX == 0) {
                    $("#esperando").html('');
                }
            }

        });
    }
    return hayerror;

}


