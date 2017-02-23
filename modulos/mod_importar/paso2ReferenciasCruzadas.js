/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogoproductos de Soluciones Vigo.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero
 * @Descripcion	Javascript necesario para paso2ReferenciasCruzadas.php
 * */
 
 // Buscamos los campos que tengan menos 2 caracteres para no analizar
function modifestadofab() {
	
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
			console.log(response.toSource()); // Cuando recibimos un objeto, lo vemos asi..
			if ( response.conexion == 'correcto'){
			console.log('Numero Items:'+ response.NItems);
			console.log('Numero Items Error Campo 2:' + response.Menos2C);
			$("#total").html(response.NItems);
			$("#campVa").html(response.Menos2C);
			
			} 
			
			$("#resultado").html("Termino de buscar campos con solo 2 caracterres....");
			console.log("Success de modifestado");
			buscProvee();


		}

	});
}


function buscProvee() {
	var nombretabla = "referenciascruzadas";
	var parametros = {
		'nombretabla': nombretabla,
		'pulsado': 'BuscarErrorFab'

	};

	$.ajax({
		data: parametros,
		url: 'tareas.php',
		type: 'post',
		datatype: 'json',
		beforeSend: function () {
			$("#resultado").html("Creamos array con los distintos fabricantes que hay importacion, espere por favor...");
		},
		success: function (response) {
			// cubrimos la linea final y lanzamos el ciclo
			$("#resultado").html("Terminado de crear Array con los distintos fabricantes y lanzamos ciclo ...");
			lineafinal = response.length;
			console.log("Fabricantes encontrados en importacion:" + lineafinal);
		  
			ciclofabricante(response);
		}
	});
}

function fabricexist() {
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
				console.log ( "LineaFinal:" + lineafinal);
				console.log ( "LineaIntermedia:" + lineaIntermedia);
				console.log ("Repuesta:"+ response);
				if (response == 'No'){
				fabricanteserror = fabricanteserror + 1;
				console.log ( "Fabricantes con error:" + fabricanteserror);	
				$("#fabcruDes").html(fabricanteserror);
				}
				$("#fabcru").html(lineaIntermedia); // Indicamos fabricantes analizados.

				lineaIntermedia++;
				$("#resultado").html("Resultado de "+ respuesta[lineaIntermedia].Fabr_Recambio );
				ProcesoBarra(lineaIntermedia, lineafinal);
			}

		});

	} else {
		clearInterval(ciclodefunciones);
		lineaIntermedia = 0;
		resumenresul();

	}

}
;

function ciclofabricante(response) {
	respuesta = response;

	ciclodefunciones = setInterval(fabricexist, 500);

}
function resumenresul() {
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
			$("#compFichero span").remove();
			$("#cmp").css("display", "block");
			$("#fabcru").html(lineafinal); // Total fabricantes 
			$("#campVa").html(vacio);
			$("#Rfabcru").html(fab);
			$("#RegBlanco").html(validos);
			$("#resultado").html("COMPLETADO PASO2 (REFERENCIAS CRUZADAS). Selecciona fabricante principal y seguir comprobando...");
		}

	});

}


// FUNCIONES QUE SE EJECUTAN DESPUES COMPROBAR Y SELECCIONAR 
// Funciones que se ejecutan despues de seleccionar comprobar
			// Se obtiene registros que su estado="" de tabla referenciascruzadas de importar
			// para:
			// 		1.- Buscar si existe la referencia_principal
			// 			[NO EXISTE] Entonces añadimos a  campo estado como ERROR[Referencia Principal]
			//			[SI EXISTE] Entonces buscamos la referencia cruzada si existe o no , ojo siempre
			//		comparandolo con el fabricante.



function finalizar(fabri) {
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

function ComprobarPaso2RefCruzadas() {
	// En esta funcion obtenemos los 400 registros primeros que tengan ESTADO=""
	// Tambien ten encuenta que a esta funcion se llama desde:
	// 		funcion finalizar-> Cuando pulsamos btn comprobar...
	//		function grabar() --> Ajax response si ya leyo los 400 registros....
	
	// Obtenemos de tabla BDImportar-Registroscruzados los que tenga el estado ""
	finallinea = $("#RegBlanco").html();
	
	if (fabricante == 0) {
		alert("Selecciona un Fabricante");
	} else {
		var parametros = {
			'pulsado': 'contarVacioscruzados'
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
					// Creamos array con los datos $BDImportar-referenciascruzadas
					// Por ejemplo:
					// [{	id:"A110049",
					//	 	linea:"1413", 
					//		F_rec:"QUINTON HA",
					// 		Ref_F:"WF8232"}
					// y así con 399 registros mas... 
					arrayConsulta = response;
					console.log("************ RESPUESTA AJAX FUNCION ComprobarPaso2RefCruzadas *********")
					console.log("Numero registros obtenidos "+response.length);
					$("#fin").html("Hemos obtenido "+ response.length + " con ESTADO en BLANCO,Llevamos " + lineabarra +" de " + finallinea +" procesando...");
					
					grabar();
					
				} else {
					 $("#fin").html("Hemos terminado ya que hay" +  response.length + " con estado en blanco.");
					 alert ( "Terminamos");
				}

			}
		});

	} // Fin else fabricante no es 0
}

function grabar() {

   var parametros = {

		'pulsado': 'comprobar2cruz',
		'idrecambio': arrayConsulta[intermedia].id,
		'linea': arrayConsulta[intermedia].linea,
		'fabricante': fabricante,
		'Ref_fa': arrayConsulta[intermedia].Ref_F,
		'Fab_ref': arrayConsulta[intermedia].F_rec
	};
			console.log("******* FUNCION GRABAR ENVIAMOS AJAX *********************");
			console.log("que id es "+arrayConsulta[intermedia].id);
			console.log("que linea es "+arrayConsulta[intermedia].linea);
			console.log("que fabricante es "+fabricante);
			console.log("que ref_fa es "+arrayConsulta[intermedia].Ref_F);
			console.log("que fab_ref es "+arrayConsulta[intermedia].F_rec);
	$.ajax({
		data: parametros,
		url: 'tareas.php',
		type: 'post',
		datatype: 'json',
		beforeSend: function () {
			textoMostrar = "Grabar()- Comprobando Referencia:"+arrayConsulta[intermedia].Ref_F;
			textoMostrar = textoMostrar + "\n Fabricantes cruzado es:" + arrayConsulta[intermedia].F_rec;
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
			if (intermedia == (arrayConsulta.length)-1) {
				intermedia=0;
				console.log("Obtener nuevamente array en ComprobarPaso2RefCruzadas");
				ComprobarPaso2RefCruzadas();
			} else {
				console.log("Continuamos con ciclo de grabar..");
				intermedia++;
				lineabarra++;
				BarraProceso(lineabarra,finallinea);
				
				console.log("Numero que vamos en este ciclo: "+intermedia);
				console.log("Maximo ciclo: "+arrayConsulta.length);
				console.log("linea final de barra proceso: "+finallinea);
				console.log("linea actual de barra proceso: "+lineabarra);

				grabar();
			}
		}
	});
}

