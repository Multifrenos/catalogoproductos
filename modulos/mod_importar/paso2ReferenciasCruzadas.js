/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogoproductos de Soluciones Vigo.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero
 * @Descripcion	Javascript necesario para paso2ReferenciasCruzadas.php
 * */
 

// * -------------------------------------------------------------* //
function DistintoFabCruzTemporal() {
	// Se ejecuta:  Si es correcto resumenresul()
	// Objetivo:	Obtenemos array con fabricantes que Estado ='' y IDFabricante = 0 
	// Devuelve:
	
	var nombretabla = "referenciascruzadas";
		// Obtenemos fabricantes que están sin analizar.
		var parametros = {
			'nombretabla': nombretabla,
			'pulsado': 'DistintoFabCruzTemporal',
			'condicional': "IdFabricaCruzado= 0 and Estado = ''"
		};
		$.ajax({
			data: parametros,
			url: 'tareas.php',
			type: 'post',
			datatype: 'json',
			beforeSend: function () {
				$("#resultado").html("Creamos array con los fabricantes falta por comprobar si existen,espere por favor...");
			},
			success: function (response) {
				// cubrimos la linea final y lanzamos el ciclo
				$("#resultado").html("Ya tenemos los distintos fabricantes de la tabla temporal y lanzamos ciclo ...");
				lineafinal = 0 ;
				if (response.length > 0) {
					lineafinal = response.length;
					$("#fabcru").html(response.length);
					console.log('length response.'+response.length);
				}
				console.log("Fabricantes con estado en blanco:" + lineafinal);
				if (response.length > 0) {
				ciclofabricante(response);
				} else {
					// Quiere decir que no encontro fabricantes con su estado ='' y idFabricante en 0
					// así permitimos continuar paso 3
					lineaIntermedia = 0;
					resumenresul();
				}
			}
		});

}
// * -------------------------------------------------------------* //
function fabricexist() {
	// Se ejecuta: En setInterval ciclofabricante  
	// Objetivo: 
	// Devuelve:
	if (lineaIntermedia < lineafinal) {
		var parametros = {
			'pulsado': 'comPro',
			'fabricante': respuesta[lineaIntermedia].Fabr_Recambio
		};
		$.ajax({
			data: parametros,
			url: 'tareas.php',
			type: 'post',
			beforeSend: function () {
				$("#resultado").html("Estamos en ciclo,buscando " + respuesta[lineaIntermedia].Fabr_Recambio );
			},
			success: function (response) {
				console.log ( "Fabricante:" + respuesta[lineaIntermedia].Fabr_Recambio );
				console.log ( "LineaFinal:" + lineafinal + ' LineaIntermedia:' + lineaIntermedia);
				// Recuerda que array respuesta empieza en 0, por eso nunca va tener valor lineafinal");
				if (response == 'No'){
				fabricanteserror = fabricanteserror + 1;
				console.log ( "Error:" + fabricanteserror + 'en fabricante ' + respuesta[lineaIntermedia].Fabr_Recambio );	
				$("#FabrError22").html(fabricanteserror);
				}
				var fabTratados = lineaIntermedia+1 ; // Ya que empieza en el 0
				$("#Bfabcru").html(lineafinal+'/'+fabTratados); // Indicamos fabricantes analizados.
				$("#resultado").html("Resultado de "+ respuesta[lineaIntermedia].Fabr_Recambio );
				lineaIntermedia++;
				ProcesoBarra(lineaIntermedia, lineafinal);
			}
		});
	} else {
		// Terminamos el ciclo de control de fabricante, es decir
		// En tabla IMPORTARRECAMBIOS deberíamos tener el IDFabricante en todos aquellos fabricantes que existen o
		// en ESTADO = [ERROR P2-22]:FABRICANTE cruzado no existe.
		clearInterval(ciclodefunciones);
		// Si va muy rápido las peticiones puede fallar el insert, por lo que es conveniente revisar si todos
		// registros tienen IDFabricante o ESTADO no existe.
		console.log('Termino comprobar fabricante');
		lineaIntermedia = 0;
		alert('Termino comprobacion de fabricante \n Ahora realizamos resumen.');
		resumenresul();
	}
}

// * -------------------------------------------------------------* //

function ciclofabricante(response) {
	// Se ejecuta:  Si es correcto DistintoFabCruzTemporal()
	// Objetivo: 
	// Devuelve: NADA
	respuesta = response;
	ciclodefunciones = setInterval(fabricexist, 200);
}

// * -------------------------------------------------------------* //
function resumenresul() {
	// Se ejecuta:  Varias veces y al terminar carga la pagina ( Al inicio )
	// Objetivo: 
	// Devuelve:
	
	var parametros = {
		'pulsado': 'resumen'
	};
	$.ajax({
		data: parametros,
		url: 'tareas.php',
		type: 'post',
		datatype: 'json',
		beforeSend: function () {
			$("#resultado").html("Realizando resumen fichero importar ReferenciasCruzadas, espere por favor...");
		},
		success: function (response) {
			// Añadimos valores a span
			$("#campVa").html(response.error21);// Registros que tiene error campo ( 2 caracteres)
			$("#Rfabcru").html(response.error22);// Registros de fabricantes cruzados no correctos
			$("#RegBlanco").html(response.NItemsEstadoBlanco); // Registros que tiene el Estado = ''.
			$("#RegBlancoCRecambio").html(response.NItemsCRecambio); // Registros que tiene Estado = '' and IDrecmabio <>0
			$("#FabrError22").html(response.FabNoEncontrado); // Fabricantes buscados y no encontrados.
			$("#Bfabcru").html(response.FabNoBuscado); // Fabricantes aun NO buscados (aun).
			$("#Yafabcru").html(response.FabYaBuscado); //Fabricantes aun YA buscados.
			$("#Totfabcru").html(response.Totalfabcru); //Total de Fabricantes encontrados.
			$("#FabrError21").html(response.FabError21); // Fabricantes descartados por error 21
			console.log('Compruebo que la suma fabricantes buscados,no buscados y encontrados de el total fabricantes');
			if ( eval($("#Bfabcru").text()) > 0) {
				// Sigue faltando algun fabricante por buscar.
				alert ('¿ Faltaran '+ +  $("#Bfabcru").text() +'algún fabricante por buscar ?');
				$("#resultado").html("Aun no termino el proceso encontrar Fabricantes...");
				DistintoFabCruzTemporal();
			} else {
				// Quiere decir que no hay fabricantes que no se buscaron por lo que 
				$("#RefPrincipales").html(response.RefPrinEncontradas);// Referencias distintas encontradas en tabla
				$("#RefPrinPendIDRecam").html(response.RefPrinPendIDRecam);// Referencias Pendientes buscar IDRecambio
				$("#RefPrincipalesIDRecam").html(response.RefPrinYAIDRecam);// Referencias YA encontrado Recambio
				$("#RefPrincDescartadas").html(response.NRefPrinNOenc);// Referencias NO se encontro Recambio
				$("#Error23").html(response.error23); // Registros por referencias descartadas
				console.log('Compruebo que la Referencias principales tienen Error o IDRecambio');
				if (response.RefPrinYAIDRecam == 0 || response.NRefPrinNOenc == 0 ) {
					// Quiere decir que NO ya Referencias principales con ID o con estado mal.
					$("#RefPrincDescartadas").html('?');
					$("#RefPrincipalesIDRecam").html('?');
					$("#Error23").html('?');
					$("#resultado").html("Estamos PASO2 y terminamos de comprobar FABRICANTES. Selecciona fabricante principal para seguir...");

				}
				console.log('Compruebo que la Referencias principales le faltan registro por comprobar');
				if (response.RefPrinPendIDRecam > 0 ) {
					// Quiere decir que aun no estan todas cubiertas.
					alert( ' Hay referencias Principales pendiente comprobar si tiene IDRecambio');
					if (fabricante ==0 || fabricante === undefined){
						// Mostramos botton de comprobar para que puede seleccionar .
						$("#cmp").css("display", "block");
					} else {
						// Quiere decir que ya se selecciono fabricante entonces ejectuamos
						// ObtenerReferenciaPrincipales() enviando que paso2 , ya que aun no terminamos de  buscar todas
						// Referencias Principales.
						ObtenerReferenciasPrincipales('paso2');	
					}
			


				} else {
					// Quiere decir que ya busco todos los fabricantes, ya busco todas las referencias principales.
					// Entonces los registros hay en la tabla :
					// SELECT * FROM `referenciascruzadas` WHERE `Estado` = '' AND `RecambioID` <>0 AND `IdFabricaCruzado`<>0
					// Son los registros que vamos a procesas y Referencias distintas Con IDRecambio 
					// Vamos añadir NUEVOS y EXISTENTES.
					$("#resultado").html("Ya termino comprobar la REFERENCIAS PRINCIPALES, ahora empezamos PASO3 ( GRABAR).");
					P3NuevoExiste();
				}
				
			}
			
		}
	});
}


// * -------------------------------------------------------------* //
function comprobar(fabri) {
	// Se ejecuta:  Despues pulsar comprobar.
	// Objetivo: Comprueba que se selecciono un fabricante
	// Devuelve: NADA
	
	fabricante = fabri;
	if (fabricante == 0) {
		alert("Selecciona un Fabricante");
	} else {
		resumenresul()
	}
};
// * -------------------------------------------------------------* //
function ObtenerReferenciasPrincipales(paso) {
	// Se ejecuta:  Viene si es correcto finalizar()
	// Objetivo: 	1.- Obtiene un array con Referencias Principales distintas que el estado = '' y IDFabricante <>0
	// Devuelve: Array en JSON ,por ejemplo:
					// [{	RefProveedor:"A110049",
	// INICIALIZAMOS VARIABLES
	finallinea = 0;
	intermedia = 0;
	// Ahora montamos el condicional según quien lo ejecuto la funcion.
	console.log ('Estamos en ObtenerReferenciaPrincipal y parametro ' + paso);
	if (fabricante !== 0) {
		var parametros = {
			'pulsado': 'ObtenerReferenciasPrincipales',
			'condicional' : paso
		};
		$.ajax({
			data: parametros,
			url: 'tareas.php',
			type: 'post',
			datatype: 'json',
			beforeSend: function () {
				console.log ('Obteniendo array con registros que tiene estado en blanco');
			},
			success: function (response) {
				arrayConsulta = response;
				console.log("Respuesta funcion ObtenerReferenciasPrincipales():")
				console.log("Numero registros obtenidos "+arrayConsulta['NItems']);
				if (arrayConsulta['NItems'] !== 0 && paso == "paso2") {
					// Solo ejecutamos cicloComprReferenciaPrincipal si estamos en paso 2
					finallinea = arrayConsulta['NItems'];
					// Anotamos referencias encontradas.
					$("#RefPrincipalesIDRecam").html(arrayConsulta['NItems']);
					console.log('Estamos en PASO2 :Iniciamos ciclo CicloComprReferenciaPrincipal');
					cicloComprobarRefPrincipal = setInterval(cicloReferenciaPrincipal,1000);
				} else {
					// Puede suceder que:
					// 			- Termino el ciclo
					// 			- Que venga parametro PASO3 es decir que viene P3NuevoExiste
					console.log("Termino ObtenerReferenciasPrincipales()");
					if (paso == "paso3"){
						// Aquí tiene que volver a P3NuevoExiste
						P3NuevoExiste();
						return; // para que vuelva ejecutar resumen...
					}
					console.log("Vamos resumenresul()");
					resumenresul();
				}
			}
		});
	} // Fin else fabricante no es 0
}

// * -------------------------------------------------------------* //
function cicloReferenciaPrincipal() {
	// Se ejecuta:  Si array tiene datos ObtenerReferenciasPrincipales()
	// Objetivo: 	Separar el arrayConsulta y hacer peticion de grabar esos 200
	//				Recuerda que puede estar limitado el servidor en recibir variables, por eso hace así.
	// Devuelve:
	ProcesoBarra(intermedia, finallinea);
	var ItemsEnviar = [] ;
		if ( intermedia < arrayConsulta['NItems']) {
			for (i = 0; i < 200; i++) {  
				if (intermedia < arrayConsulta['NItems']){
				// Montamos array para enviar por AJAX
				ItemsEnviar[i] = arrayConsulta[intermedia];
				intermedia = intermedia + 1;
				}
			}
			
			console.log("Fabricante "+fabricante);
			console.log('Linea actual:' +intermedia);
			console.log('final:' + finallinea);
			console.log('Enviamos Referencias Principales:');
			console.log(JSON.stringify(ItemsEnviar)); // Mostramos en consola lo contiene ItemsEnviar
			var parametros = {
				'pulsado': 'BuscarRecambioPrincipal',
				'condicional': 'Si', // Quiere que 
				'Fabricante':fabricante,
				'ArrayVacios':ItemsEnviar
				
			};
			$.ajax({
				data: parametros,
				url: 'tareas.php',
				type: 'post',
				datatype: 'json',
				beforeSend: function () {
					textoMostrar = "Comprobando Referencias principales si EXISTEN";
					$("#resultado").html(textoMostrar);

				},
				success: function (response) {
					console.log("******* RESPUESTA AJAX DE GRABAR *************************");
					console.log('Consulta'+ response['Consulta1']);
					console.log("Errores:" + response['RegistrosErrorRefPrincipal']);
					textoMostrar = "Terminados revisar los " +intermedia+" referencias, encontradas \n "+ response['RegistrosErrorRefPrincipal']+" registros que no existen referencias.";
					$("#resultado").html(textoMostrar);
				}
			});
		} else {
			clearInterval(cicloComprobarRefPrincipal);
			// Quiere decir que termino... comprobar si existen las REFERENCIAS PRINCIPAL.
			textoMostrar = "¡¡ TERMINAMOS REVISAR REFERENCIAS, SI EXISTEN !! \n";
			textoMostrar = textoMostrar + " REALIZAMOS RESUMEN."
			$("#resultado").html(textoMostrar);
			// Es mejor esperar un poco antes de hacer resumen ya que puede que no este todos UPDATE terminado.
			// Si no devuelve array es que ya no hay vacios, por lo que se termino.
			// Hacemos resumen de nuevo
			resumenresul();
			// Empezamos paso 3 P3NuevoExiste()empre
			P3NuevoExiste();
		}
}

function P3NuevoExiste() {
		// Ya no deberíamos tener ninguna Referencia Principal para Analizar, 
		// Comprobamos que existe fabricante, si no existe
		console.log ('Estoy en P3NuevoExite()');
		if (fabricante === 0 || fabricante === undefined) {
		console.log( 'Tuviste que saltar a este paso sin hacer Paso 2, por lo que te muestro solo botton comprobar');
		alert("¡¡ Peligro no hay fabricante !! \n En su momento seleccionas te un fabricante \ ¿Te acuerdas cual era ? ");
		$("#cmp").css("display", "block");
		return ;
		// Ocultas botton de comprobar y mostramos el grabar.
		
		}  
		// Comprobamos que realmente sea ese el fabricante del que ya buscam
		$("#cmp").css("display", "none");
		$("#nuevoExiste").css("display", "block");
		$("#resultado").html("PASO3: Vamos a crear Nuevas Referencias Cruzadas, nuevos cruces y cambiar fecha actualizacion en los existentes.");
		alert( 'Pulsa grabar para empezar,\n teniendo encuenta que ahora si graba en BDRecambios\n ten cuidado con el fabricante');
		// Obtenemos el dato de Distintos Ref_Principales ( Aquellas que tienen estado vacio y ID_Fabricante <>0 )
		// este datos es el que vamos utilizar para poner  
		
		var RefDistintaPrincipal = 	$("#RefPrincipalesIDRecam").text();
		alert('PASO 3: fabricante es '+fabricante);
		console.log( "PASO:3 Comprobamos que fabricante es correcto");
		if (arrayConsulta != undefined){
		finallinea = arrayConsulta['NItems'];
		intermedia = 0;
		cicloNuevoExisteCruce();
		}
}

function cicloNuevoExisteCruce (){
	ProcesoBarra(intermedia, finallinea);
	var ItemsEnviar = [];
		if ( intermedia < arrayConsulta['NItems']) {
			for (i = 0; i < 200; i++) {  
				if (intermedia < arrayConsulta['NItems']){
				// Montamos array para enviar por AJAX
				ItemsEnviar[i] = arrayConsulta[intermedia];
				intermedia = intermedia + 1;
				}
			}
		}
		console.log('Fin cicloNuevoexisteCruce()');
		console.log(JSON.stringify(ItemsEnviar));
		console.log("Fabricante "+fabricante);
		console.log('Linea actual:' +intermedia);
		console.log('final:' + finallinea);
		// Ahora enviamos datos a funcion por Ajax
		var parametros = {
				'pulsado': 'NuevoExisteCruce',
				'Fabricante':fabricante,
				'ArrayVacios':ItemsEnviar
				
			};
			$.ajax({
				data: parametros,
				url: 'tareas.php',
				type: 'post',
				datatype: 'json',
				beforeSend: function () {
					textoMostrar = "Comprobando Referencias principales si EXISTEN";
					$("#resultado").html(textoMostrar);
				},
				success: function (response) {
					console.log('Respuesta'+ response['Respuesta']);
					textoMostrar = "Terminados revisar los " +intermedia+" referencias, encontradas \n "+ response['RegistrosErrorRefPrincipal']+" registros que no existen referencias.";
					$("#resultado").html(textoMostrar);
				}
			});
		
		
		
}
