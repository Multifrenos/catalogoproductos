/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogoproductos de Soluciones Vigo.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero
 * @Descripcion	Javascript necesario para paso2ReferenciasCruzadas.php
 * */
 
// * -------------------------------------------------------------* //
 function modifestadofab() {
	// Se ejecuta: Al terminar cargar.
	// Objetivo: Comprueba que no hay registros con nombre Fabricante o referencia con menos 2 caracteres
	// Estado ='[ERROR P2-21]:CampoVacio'
	// Devuelve:
	// 		({
	// 				conexion:"correcto" -> Donde indica si es correcto o no la conexion
	//				NItems:[numero] ->	Donde indica la cantidad de item que tiene.
	// 				Menos2C:[numero] -> Donde indica la cantidad ROW que se cambiaron.
	//		})
	var parametros = {
		'pulsado': 'BuscarError'

	};
	$.ajax({
		data: parametros,
		url: 'tareas.php',
		type: 'post',
		beforeSend: function () {
			$("#resultado").html("Buscando campos con solo 2 caracterres, espere por favor...");
		},
		success: function (response) {
			console.log(response.toSource()); // La forma de ver cuando recibes un objeto.
			if ( response.conexion == 'correcto'){
			console.log('Numero Items:'+ response.NItems);
			console.log('Numero Items Error Campo 2:' + response.RegistrosMenos2C);
			$("#resultado").html("Termino de buscar campos con solo 2 caracterres....");
			console.log("Success de modifestado");
			$("#EstadoBlanco").html(response.NItems);
			$("#campVa").html(response.RegistrosMenos2C);
			$("#FabrError21").html(response.FabricanteMenos2C);

			//Iniciamos funcion para comprobar si fabricante cruzado (proveedor) existe 
			DistintoFabCruzTemporal();
			} else {
			// Quiere decir que fallo la conexion por lo que no  continuamos
			alert('[ERROR PRG 1- Paso2ReferenciasCruzadas]\n Error de conexion con Referencias Cruzadas de BDRecambios\n no puede continuar.')
			return;
			}


		}

	});
}

// * -------------------------------------------------------------* //
function DistintoFabCruzTemporal() {
	// Se ejecuta:  Si es correcto modifestadofab()
	// Objetivo:	1 peticion AJAX ->Obtenemos array total fabricantes que hay importar/referenciasCruzadas
	//				2 peticion AJAX-> Obtenemos array con fabricantes que Estado ='' y IDFabricante = 0 
	// Devuelve:
	
	
	var nombretabla = "referenciascruzadas";
	// Obtenemos total de fabricantes.
	var parametros = {
		'nombretabla': nombretabla,
		'pulsado': 'DistintoFabCruzTemporal',
		'condicional': ""
	};
	$.ajax({
		//	1 peticion AJAX -> total fabricantes que hay importar/referenciasCruzadas
		data: parametros,
		url: 'tareas.php',
		type: 'post',
		datatype: 'json',
		beforeSend: function () {
			$("#resultado").html("Creamos array con los distintos fabricantes que hay importacion, espere por favor...");
		},
		success: function (response) {
			// cubrimos la linea final y lanzamos el ciclo
			$("#resultado").html("Ya tenemos los distintos fabricantes de la tabla temporal y lanzamos ciclo ...");
			lineafinal = response.length;
			$("#Totfabcru").html(response.length);
			console.log("Fabricantes encontrados en importacion:" + lineafinal);
			alert('Fabricantes Encontrados '+ lineafinal);

		}
	});
	
	// Obtenemos fabricantes que están sin analizar.
	var parametros = {
		'nombretabla': nombretabla,
		'pulsado': 'DistintoFabCruzTemporal',
		'condicional': "IdFabricanteRec= 0 and Estado = ''"
	};
	$.ajax({
		// 2 peticion AJAX-> Obtenemos array con fabricantes que Estado ='' y IDFabricante = 0
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
				console.log(response.toSource());
			}
			console.log("Fabricantes con estado en blanco:" + lineafinal);
			alert( ' Iniciamos ciclofabricante para \n '+ lineafinal + ' Fabricantes');
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
	// Se ejecuta:  
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
				console.log ("Repuesta:"+ response);
				fabricanteserror = fabricanteserror + 1;
				console.log ( "Error:" + fabricanteserror + 'en fabricante ' + respuesta[lineaIntermedia].Fabr_Recambio );	
				$("#FabrError22").html(fabricanteserror);
				}
				var fabTratados = lineaIntermedia+1 ; // Ya que empieza en el 0
				$("#fabcru").html(lineafinal+'/'+fabTratados); // Indicamos fabricantes analizados.
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
		lineaIntermedia = 0;
		resumenresul();

	}

}
// * -------------------------------------------------------------* //

function ciclofabricante(response) {
	// Se ejecuta:  Si es correcto modifestadofab()
	// Objetivo: 
	// Devuelve: NADA
	
	respuesta = response;

	ciclodefunciones = setInterval(fabricexist, 200);

}
// * -------------------------------------------------------------* //

function resumenresul() {
	// Se ejecuta:  Si es correcto modifestadofab()
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
			var vacio = response[0].e;// Registros que tiene error campo ( 2 caracteres)
			var fab = response[0].f; // Registros de fabricantes cruzados no correctos
			var validos = response[0].c; // Registros que vamos a comprobar.
			var fabNoEncontrado = response[0].FabNo; // Fabricantes buscados y no encontrados.
			$("#fabcru").html(lineafinal); // Total fabricantes 
			$("#campVa").html(vacio);
			$("#Rfabcru").html(fab);
			$("#RegBlanco").html(validos);
			$("#FabrError22").html(fabNoEncontrado);

			$("#resultado").html("COMPLETADO PASO2 (REFERENCIAS CRUZADAS). Selecciona fabricante principal y seguir comprobando...");
			// Mostramos botton de comprobar.
			$("#cmp").css("display", "block");
		}

	});

}



// * -------------------------------------------------------------* //
function finalizar(fabri) {
	// Se ejecuta:  Despues pulsar comprobar.
	// Objetivo: Comprueba que se selecciono un fabricante, si selecciono entonces va ComprobarPaso2RefCruzadas()
	// Devuelve: NADA
	
	fabricante = fabri;
	if (fabricante == 0) {
		alert("Selecciona un Fabricante");
	} else {
		// Informamos que ahora si va añadir y que no hay vuelta atrás.
		var respuestaConf = confirm('Vamos empezar añadir registros a BDRecambios,\n\ ahora si que no hay vuelta atrás, por lo que es conveniente tener una copia de seguridad de BDRecambios\n\ Estas seguro');
		if (respuestaConf == true) {
			ComprobarPaso2RefCruzadas();
		}
	}

};
// * -------------------------------------------------------------* //
function ComprobarPaso2RefCruzadas() {
	// Se ejecuta:  Viene si es correcto finalizar()
	// Objetivo: 	1.- Primero comprueba si pulso Fabricante
	// 			  	2.- Obtiene el valor #RegBlanco, para poner como linea final.( Proceso Barra) 
	//			  	3.- Obtiene un array con 400 registro de máximo que tienen el estado = '' y IDFabricante
	// Devuelve: Array en JSON ,por ejemplo:
					// [{	id:"A110049",
					//	 	linea:"1413", 
					//		F_rec:"QUINTON HA",
					// 		Ref_F:"WF8232"}
					// y así con 399 registros mas..
	finallinea = $("#RegBlanco").html();
	
	if (fabricante == 0) {
		alert("Selecciona un Fabricante");
	} else {
		var parametros = {
			'pulsado': 'ObtenerVacioscruzados'
		};
		$.ajax({
			data: parametros,
			url: 'tareas.php',
			type: 'post',
			datatype: 'json',
			beforeSend: function () {
				console.log ('Obtenemos array con resgistros que tiene estado en blanco');
				$("#fin").html("Obteniendo registros con ESTADO en BLANCO, espere por favor...");

			},
			success: function (response) {

				if (response.length != 0) {
					// Nos devuelve array con los datos JSON $BDImportar-referenciascruzadas 
					arrayConsulta = response;
					console.log("************ RESPUESTA AJAX FUNCION ComprobarPaso2RefCruzadas *********")
					console.log("Numero registros obtenidos "+arrayConsulta['NItems']);
					$("#fin").html("Hemos obtenido "+ arrayConsulta['NItems'] + " con ESTADO en BLANCO,Llevamos " + lineabarra +" de " + finallinea +" procesando...");
					console.log(arrayConsulta);
					grabar();
					
				} else {
					 // Si no devuelve array es que ya no hay vacios, por lo que se termino.
					 $("#fin").html("Hemos terminado ya que hay" +  arrayConsulta['NItems'] + " con estado en blanco.");
					 alert ( "Terminamos");
				}

			}
		});

	} // Fin else fabricante no es 0
}
// * -------------------------------------------------------------* //
function grabar() {
	// Se ejecuta:  Si array tiene datos ComprobarPaso2RefCruzadas()
	// Objetivo: 
	// Devuelve:
	console.log('intermedia:' +intermedia);
	console.log('RefProveedor:' +arrayConsulta[intermedia].RefProveedor);
	var parametros = {

		'pulsado': 'grabarCruzadas',
		'idrecambio':arrayConsulta[intermedia].RefProveedor,
		'linea': arrayConsulta[intermedia].linea,
		'fabricante': fabricante,
		'Ref_fa': arrayConsulta[intermedia].Fabr_Recambio,
		'Fab_ref': arrayConsulta[intermedia].Ref_Fabricante
	};
			console.log("******* FUNCION GRABAR ENVIAMOS AJAX *********************");
			console.log("que id es "+arrayConsulta[intermedia].RefProveedor);
			console.log("que linea es "+arrayConsulta[intermedia].linea);
			console.log("que fabricante es "+fabricante);
			console.log("que ref_fa es "+arrayConsulta[intermedia].Fabr_Recambio);
			console.log("que fab_ref es "+arrayConsulta[intermedia].Ref_Fabricante);
	$.ajax({
		data: parametros,
		url: 'tareas.php',
		type: 'post',
		datatype: 'json',
		beforeSend: function () {
			textoMostrar = "Grabar()- Comprobando Referencia:"+arrayConsulta[intermedia].Ref_Fabricante;
			textoMostrar = textoMostrar + "\n Fabricantes cruzado es:" + arrayConsulta[intermedia].Fabr_Recambio;
			$("#resultado").html(textoMostrar);

		},
		success: function (response) {
			console.log("******* RESPUESTA AJAX DE GRABAR *************************");
			//~ console.log( arrayConsulta[intermedia].id);
			//~ console.log( arrayConsulta[intermedia].linea);
			//~ console.log( fabricante);
			//~ console.log("ref cruzada "+ arrayConsulta[intermedia].Ref_F);
			//~ console.log("prof cruzado"+ arrayConsulta[intermedia].F_rec);
			//~ console.log("****************");
			
			console.log("Respuesta:" + response[0].respuesta);
			console.log("Busqueda:" + response[0].busqueda);

			// Este if es el que hace ciclo.. es decir
			// Vuelve ejecutar la misma funcion mientras la 
			// variable global var intermedia no llege al final de arrayConsulta.length
			console.log("Maximo ciclo: "+ arrayConsulta['NItems'] );

			if (intermedia == (arrayConsulta['NItems']-1)) {
				intermedia=0;
				console.log("Obtener nuevamente array en ComprobarPaso2RefCruzadas");
				ComprobarPaso2RefCruzadas();
			} else {
				console.log("Continuamos con ciclo de grabar..");
				intermedia++;
				lineabarra++;
				ProcesoBarra(lineabarra,finallinea);
				
				console.log("Numero que vamos en este ciclo: "+intermedia);
				console.log("linea final de barra proceso: "+finallinea);
				console.log("linea actual de barra proceso: "+lineabarra);

				grabar();
			}
		}
	});
}

