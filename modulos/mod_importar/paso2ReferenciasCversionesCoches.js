/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogoproductos de Soluciones Vigo.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero && Alberto Lago
 * @Descripcion	Javascript necesarios para paso2ReferenciCVersiones.php
 * */
 

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
		// En esta funcio lo unico que obtenemos es la linea final, ya que según lo que pulsemos obtenemos uno u otra dato.
		// No vale para nada mas que para eso...
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
					// Ahora creamos swich
					
					switch (btnPulsado) {
						case 'IDrecambio':
							$("#DistintasReferenPrincipales").html(finallinea); 
							$("#resultado").html('Obtenemos cantidad total de ID de Referencias Principales que tenemos que buscar....' + finallinea );
							if (finallinea >0 ) {
								// Ejecutamos CochesIDRecambioTemporal ciclo
								console.log('Ocultamos botton de RecambioID');
								$("#btn-IDRecambio").css("display", "none"); // Ocultamos por existe fabricante.
								//~ ciclo = setInterval(CochesIDRecambioTemporal,6000);
								CochesIDRecambioTemporal();
							}
							break;
						case 'IDversion':
							$("#DistintasRefPrinSIDversion").html(finallinea); 
							$("#resultado").html('Obtenemos cantidad total de ID de Versiones que tenemos buscar ....' + finallinea);
							if (finallinea >0 ) {
								// Ejecutamos CochesIDRecambioTemporal ciclo
								//~ ciclo = setInterval(CochesIDRecambioTemporal,5000);
								console.log('Entre en finalinea de IDversiones');
								console.log( 'Antes obtener valor, finallinea es'+finallinea);
								finallinea = parseInt($("#TotalRegistros").text());
								lineaintermedia = parseInt($("#EstadoCubierto").text());
								console.log('Ejecutamos CochesIDresumen');
								CochesIDversiones();
							}
							break;
						case 'NuevoExiste':
							console.log('Ahora vamos ejecutar Nuevo y existente');
							// La linea final no vale la que obtenemos por la que ponemos la que si vale.
							finallinea = parseInt($("#RegCIDsEstadoBlanco").text());
							lineaintermedia = 0;
							console.log('lineafinal'+finallinea);
							console.log('Intermedia de Nuevo-Existe'+lineaintermedia);
							if (finallinea >0 ) {
								CochesNuevaExiste();
							}
							
							break;
					}
					
				}

				

			});
	}
	


}



function CochesIDRecambioTemporal() {
    // Esta funcion la ejecuta: 
    // No permito continuar si no hay fabricante seleccionado.
    if (fabricante !== "0") {
		// Ocultamos el btn , para que el usuario no pueda volver a pulsarlo.
		$("#btn-IDVersion").css("display", "none");
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
			console.log('No hay IDRecambios sin buscar. ');
			CochesResumen('FinIDRecambios');
			
		}
	ProcesoBarra(lineaintermedia, finallinea);
	}
}
function CochesIDversiones(){
	// Lo que pretendemos es encontrar las IDVersion de la tabla temporal.
	// para ello necesitamos las conexion a la BD de coches.
	console.log( 'Ahora en CochesIDVersiones');
	console.log(' finallinea:'+finallinea);
	console.log(' lineaIntermedia:'+lineaintermedia);
	ProcesoBarra(lineaintermedia, finallinea);
	// Ahora compruebo que sea inferior.
	if (finallinea > lineaintermedia) {						

	// No permito continuar si no hay fabricante seleccionado.
		if (fabricante !== "0") {
			// Ocultamos btn-IDRecmabios para que no lo vuelva ejecutar.
			console.log('Deberiamos ocultar botton');
			$("#btn-IDVersion").css("display", "none"); // Ocultamos por no hay datos a analizar.
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
							$("#EstadoCubierto").html(response['RegistroVistos']); 
							lineaintermedia = response['RegistroVistos'];
							console.log('Linea Intermedia : '+ response['RegistroVistos']);
							console.log('Linea final : '+ finallinea);
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
							//~ console.log(resultado);
							console.log( ' Repetimos CochesIDVersiones ');
							CochesIDversiones();
						}

					});
		
		
		} else {
		   alert( 'Selecciona un fabricante por lo menos');	
		}
	} else {
		alert ( 'Terminamos de buscar IDversiones ');
		CochesResumen('FinIDversiones');
		
	}

}


function CochesResumen(paso) {
	console.log ( ' Ejecutamos funcion CocheResumen ' );
	var parametros = {
		'pulsado': 'CochesResumen',
		'Paso' : paso
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
			// Obtenemos datos de resultado:
			// Cuantos registros tiene la tabla.
			$("#TotalRegistros").html(resultado['TotalRegistro']);

				// Error de [ERROR P2-23]:Referencia Principal no existe.
				$("#NItemError0").html(resultado['Errores'][0]);
			
				// Error de no encontrada version
				$("#NItemError1").html(resultado['Errores'][1]);
			
				// Error de Marca o Modelo
				$("#NItemError2").html(resultado['Errores'][2]);
				

			
			
				// Cantidad de registros con estado o idRecambios o idVersiones cubierto.
				$("#EstadoCubierto").html(resultado['RegistroVistos']);
			
				// Quiere decir que hay Registros sin IDRecambio puesto.
				$("#DistintasReferenPrincipales").html(resultado[0]['TotalReferenciasDistintas']);
				// Ademas vamos comprobar si es mayor cero para mostrar el btn sino no se muestra por defecto
				if (resultado[0]['TotalReferenciasDistintas'] >0) {
					$("#btn-IDRecambio").css("display", "block"); // Mostramos por hay datos a analizar.
				} else {
					$("#btn-IDRecambio").css("display", "none"); // Ocultamos por no hay datos a analizar.

				}
				// Quiere decir que hay Registros con IDRecambio puesto.
				$("#NItemIDRecambio").html(resultado[0]['RefDistintasConID']);

			// Cubrimos los datos de la tercera fila de tabla.
			
			
				// Quiere decir que hay Registros con IDVersiones puesto.
				$("#DistintasRefPrinSIDversion").html(resultado[1]['TotalReferenciasDistintas']);

				if (resultado[1]['TotalReferenciasDistintas'] >0 && resultado[0]['TotalReferenciasDistintas'] === 0) {
					$("#btn-IDVersion").css("display", "block"); // Mostramos por hay datos a analizar.
				} else {
					$("#btn-IDVersion").css("display", "none"); // Ocultamos por no hay datos a analizar.

				}
				// Quiere decir que hay Registros con IDVersiones puesto.
				$("#NItemIDVersiones").html(resultado[1]['RefDistintasConID']);

				// Quiere decir que hay Registros con IDVersiones puesto.
				$("#NItemVersionesCIDVersiones").html(resultado['NVersionesDifCIDversion']);

				// Controlamos si mostramos btn-AñadirRelaciones . 
				// ya que si tiene registros el valor:RegistroCIDs que es
				// el numero registros que tienen estado en blanco y ademas tienes (IDRecambio y IDversion)
				if (resultado['RegistroCIDs'] === 0) {
					$("#btn-Relaciones").css("display", "none"); // No se muestra ya que queda registros por analizar.
				} else {
					$('#RegCIDsEstadoBlanco').html(resultado['RegistroCIDs']);
					$('#DuplicadosIDVersiones').html(resultado['EstadoFinal']['Duplicado']);
					$("#RegDuplicadosDescartados").html(resultado['EstadoFinal']['RegDuplicadoDescartados']);
					$("#btn-Relaciones").css("display", "block"); // Ocultamos por no hay datos a analizar.
					
				}

			console.log(resultado);
			// Activamos capa de botones ( div) 
			$("#capa-botones").css("display", "block"); // Ocultamos por existe fabricante.

		}

	});		
}

function CochesNuevaExiste(){
	// Funcion que comprobamos si existe el cruce o si va se nuevo.
	// En esta función simplemente cubrimos estado de registro como nuevo o si existe.
	// Es un bucle mientras no cubra el Estado de todos los registros 
	// 1.- Primero comprobamos que tengamos selecciona un fabricante.
	if (fabricante !== "0") {
		// ejecutamos funcion de php.
		console.log('Ejecutamos CochesNuevosExiste');
		var parametros = {
		'pulsado': 'CochesNuevaExiste',
		};
		$.ajax({
			data: parametros,
			url: 'tareas.php',
			type: 'post',
			beforeSend: function () {
				$("#resultado").html('Comprobamos que las relaciones son Nuevas o Existentes de los primeros 500 registros.......<span><img src="./img/ajax-loader.gif"/></span>');
			},
			success: function (response) {
				console.log ('Respondio funcion php de CocheNuevaExite');
				// Ahora compruebo si no hay duplicados.
				if (response['EstadoFinal']['Duplicado'] > 0 ) {
					// Quiere decir que hay duplicados que no arreglaron.
					console.log('Entro en duplicado, por lo que no volvemos a ejecutar CochesNuevaExiste');
					$('#DuplicadosIDVersiones').html(response['EstadoFinal']['Duplicado']);
					$("#RegDuplicadosDescartados").html(response['EstadoFinal']['RegDuplicadoCambiados']);
					// Para mostrar la barra de proceso hay que restar los registros que quedann con lo que teníamos.
					lineaintermedia= finallinea-response['RegistroCIDs'];
					console.log('finallinea:'+ finallinea);
					console.log('lineaintermedia:'+ lineaintermedia);
					console.log(response);
					ProcesoBarra(lineaintermedia, finallinea);
					CochesNuevaExiste();
					return;
				} else {
					// Quiere decir que no hay duplicados.
					$('#DuplicadosIDVersiones').html(response['EstadoFinal']['Duplicado']);
					$("#RegDuplicadosDescartados").html(response['EstadoFinal']['RegDuplicadoCambiados']);
				}
				
				console.log(response);
			}
		});
	
	}  else {
		   alert( 'Selecciona un fabricante es necesario');	
	}
	ProcesoBarra(lineaintermedia, finallinea);


}
