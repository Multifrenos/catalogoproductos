/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogoproductos de Soluciones Vigo.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero && Alberto Lago
 * @Descripcion	Javascript necesarios para paso2ReferenciCVersiones.php
 * */
 
var contadorAJAX;
var resultado = [];
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

function CochesObtenerRegistros(btnPulsado) {
	// Primero comprobamos que tengamos selecciona un fabricante.
    comprobar($('#IdFabricante').val())
    // No permito continuar si no hay fabricante seleccionado.
    if (fabricante !== "0") {
		var parametros = {
			'pulsado': 'CochesObtenerRegistros',
			'Fabricante': fabricante,
			'Buscar': btnPulsado
		};
		
		$.ajax({
				data: parametros,
				url: 'tareas.php',
				type: 'post',
				beforeSend: function () {
					$("#resultado").html('Obteniendo registros para luego buscar en su proceso determinado');
				},
				success: function (response) {
					finallinea =response['TotalReferenciasDistintas'];
					lineaintermedia = 0;
					console.log (' Pulsado es:'+btnPulsado);
					if (btnPulsado == 'IDrecambio') {
						$("#DistintasReferenPrincipales").html(finallinea); 
						$("#resultado").html('Obtenemos cantidad total de ID de Referencias Principales que tenemos que buscar....' + finallinea );
						if (finallinea >0 ) {
							// Ejecutamos CochesIDRecambioTemporal ciclo
							console.log('Ocultamos botton de RecambioID');
							$("#btn-IDRecambio").css("display", "none"); // Ocultamos por existe fabricante.
							//~ ciclo = setInterval(CochesIDRecambioTemporal,6000);
							CochesIDRecambioTemporal();
						}
					} else {
						$("#DistintasRefPrinSIDversion").html(finallinea); 
						$("#resultado").html('Obtenemos cantidad total de ID de Versiones que tenemos buscar ....' + finallinea);
						if (finallinea >0 ) {
							// Ejecutamos CochesIDRecambioTemporal ciclo
							//~ ciclo = setInterval(CochesIDRecambioTemporal,5000);
							console.log('Entre en finalinea de IDversiones');
							alert ( ' Ahora tenemos que crear funcion para buscar versiones coches '+finallinea);
							console.log('Ejecutamos resumen');
							CochesResumen();
							console.log ('Resultado de resumen');
							if (resultado['RegistroVistos'] != undefined) {
							alert( 'ver si entro ');
							$('#EstadoCubierto').html(resultado['RegistroVistos']);
							console.log (resultado['RegistroVistos']);
							console.log('Ejecutamos CochesIDresumen');
							CochesIDversiones();
							}
							
						}
					
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
						console.log('LineIntermedia despues resultado:'+lineaintermedia);
						console.log (' Ejecutamos nuevamente..CochesIDRecambioTemporal');
						CochesIDRecambioTemporal();
					}

				});
		} else {
			//~ clearInterval(ciclo); // Cancelamos ciclo...
			console.log('Mostramos botton ID version ');
			$("#btn-IDVersion").css("display", "block"); // Ocultamos por existe fabricante.

		}
	}
}
function CochesIDversiones(){
	// Lo que pretendemos es encontrar las IDVersion de la tabla temporal.
	// para ello necesitamos las conexion a la BD de coches.
	
	// No permito continuar si no hay fabricante seleccionado.
    if (fabricante !== "0") {
		var parametros = {
				'pulsado': 'CochesIDVersiones',
				'Fabricante': fabricante
			};
		$.ajax({
					data: parametros,
					url: 'tareas.php',
					type: 'post',
					beforeSend: function () {
						$("#resultado").html('Buscando versiones de la tabla referenciasCVersiones para anotar ID, espere por favor......<span><img src="./img/ajax-loader.gif"/></span>');
					},
					success: function (response) {
						var total = response['TotalRegistrosAfectados'];
						var totalID = response['TotalRegistrosIDRecambios'];
						var totalError = response['TotalRegistrosError'];
					
						var texto = 'Terminamos añalizar de 50 versiones distintas donde:<br/> Se cambian un total de'
						if (total !=undefined){
							texto = texto + ' ' +response['TotalRegistrosAfectados'];
						} else {
							texto = texto + ' 0';
						}
						texto =texto + '<br/> Total IDVersiones encontrados';
						if (totalID !=undefined){
							texto = texto + ' ' + response['TotalRegistrosIDRecambios'];
						} else {
							texto = texto + ' 0';
						}
						texto = texto +'<br/> Total errores encontrados';
						if (totalError !=undefined){
							texto = texto + ' ' + response['TotalRegistrosError'];
						} else {
							texto = texto + ' 0';
						}
						$("#resultado").html(texto);
						resultado = response;
						console.log(resultado);
						
						
					}

				});
	
	
	} else {
	   alert( 'Selecciona un fabricante por lo menos');	
	}

}


function CochesResumen() {
	console.log ( ' Ejecutamos funcion CocheResumen ' );
	var parametros = {
		'pulsado': 'CochesResumen',
	};
	$.ajax({
		data: parametros,
		url: 'tareas.php',
		type: 'post',
		beforeSend: function () {
			$("#resultado").html('Realizando resumen......<span><img src="./img/ajax-loader.gif"/></span>');
		},
		success: function (response) {
			$("#resultado").html('Terminado resumen ....');
			resultado = response;
			console.log( ' Queda recojer datos y mostralos en pantalla' );
			console.log(resultado);
		}

	});		
}

