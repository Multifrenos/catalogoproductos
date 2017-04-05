/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogoproductos de Soluciones Vigo.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero && Alberto Lago
 * @Descripcion	Javascript necesarios para paso2ReferenciCVersiones.php
 * */
 
var contadorAJAX;
var resultado;
var fabricante = 0;
var finallinea = 0;
var lineaintermedia = 0;
var ciclo;
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
// * -------------------------------------------------------------* //
function comprobar(fabri) {
	// Se ejecuta: Solo se ejecuta al pulsar bottom comprobar.
	// Objetivo: Comprueba que se selecciono un fabricante
	// Devuelve: NADA
	fabricante = fabri;
	if (fabricante == 0) {
		alert("Selecciona un Fabricante, gracias");
		return;
		// No permito continuar.
	} 
	
	// Dehabilito opción de cambiar fabricante principal
	$('#IdFabricante').prop('disabled', true);
	
};


function CochesObtenerRefProveedorTemporal() {
    // Primero comprobamos que tengamos selecciona un fabricante.
    comprobar($('#IdFabricante').val())
    // No permito continuar si no hay fabricante seleccionado.
    if (fabricante !== "0") {
		// Ocultamos bottom para que no pulsemo otra vez
		alert('Entro distinto 0');
		$("#btn-IDRecambio").css("display", "none"); // Ocultamos por existe fabricante.
		var parametros = {
			'pulsado': 'CochesObtenerRefProveedorTemporal',
			'Fabricante': fabricante
		};
			$.ajax({
				data: parametros,
				url: 'tareas.php',
				type: 'post',
				beforeSend: function () {
					$("#resultado").html('Buscando Referencias Principales para anotar ID, espere por favor......<span><img src="./img/ajax-loader.gif"/></span>');
				},
				success: function (response) {
					finallinea =response['TotalReferenciasDistintas'];
					lineaintermedia = 0;
					$("#DistintasReferenPrincipales").html(finallinea); 
					$("#resultado").html('Terminamos de ID de Referencias Principales ....' + resultado);
					if (finallinea >0 ) {
						// Ejecutamos CochesIDRecambioTemporal ciclo
						ciclo = setInterval(CochesIDRecambioTemporal,5000);
					}
						
				}

				

			});
	}
}











function CochesIDRecambioTemporal() {
    // No permito continuar si no hay fabricante seleccionado.
    if (fabricante !== "0") {
		// Mostramos y actualizamos barra
		ProcesoBarra(lineaintermedia, finallinea);
		if (finallinea > lineaintermedia ) {
			
			var parametros = {
				'pulsado': 'CochesIDRecambioTemporal',
				'Fabricante': fabricante
			};
				$.ajax({
					data: parametros,
					url: 'tareas.php',
					type: 'post',
					beforeSend: function () {
						$("#resultado").html('Buscando Referencias Principales para anotar ID, espere por favor......<span><img src="./img/ajax-loader.gif"/></span>');
					},
					success: function (response) {
						$("#resultado").html('Terminamos de ID de Referencias Principales ....');
						resultado = response;
						lineaintermedia = lineaintermedia + response['TotalReferenciasDistintas'];
						console.log('LineIntermedia depues resultado:'+lineaintermedia);
					}

				});
		} else {
			clearInterval(ciclo); // Cancelamos ciclo...
			
			}
	}
}
