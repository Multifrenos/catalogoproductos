/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogoproductos - Funciones sincronizar.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero - SolucionesVigo
 * @Descripcion	Javascript necesarios para modulo sincronizar.
 * */

var contador = 0;
var paso_actual = 0;
var TotalProductosVirtuemart;
var respuesta = [];
respuesta['VistaVirtuemart'] = false; // Variable que utilizamos para saber si termino de hacer vista.
respuesta['BuscarError'] = false; // Variable que utilizamos para saber si termino de busqueda errores..
respuesta["Sincronizar"] = 'Sinhacer' ; // Variable que utilizao para controlar si se copio tabla virtuemart a BDRecambios.
var errorReferencias = []; // Guardamos en array las referencias que están mal.
var ContError = 0;
var LimiteActual = 0;
var LimiteFinal = 0;
var icono = '<span><img src="../../css/img/ajax-loader.gif"/></span>';
var iconoCorrecto = '<span class="glyphicon glyphicon-ok-sign"></span>';

// Funcion para mostrar la barra de proceso..
function BarraProceso(lineaA,lineaF) {
	// Esta fucion debería ser una funcion comun , por lo que se debería cargar el javascript comun y ejecutar...
	// Script para generar la barra de proceso.
	// Esta barra proceso se crea con el total de lineas y empieza mostrando la lineas
	// que ya estan añadidas.
	// NOTA:
	// lineaActual no puede ser 0 ya genera in error, por lo que debemos sustituirlo por uno
	if (lineaA == 0 ) {
		lineaA = 1;
	}
	if (lineaF == 0) {
	 alert( 'Linea Final es 0 ');
	 return;
	}
	var progreso =  Math.round(( lineaA *100 )/lineaF);
	$('#bar').css('width', progreso + '%');
	// Añadimos numero linea en resultado.
	document.getElementById("bar").innerHTML = progreso + '%';  // Agrego nueva linea antes 
	return;
	
}



function Sincronizar (){
	//Funcion para obtener los IDWeb de BDVirtuemart en BDRecambios.
	//No es un proceso que tarde mucho  por lo que no mostramos barra proceso
	// y lo ejecutamos al principio.
    var parametros = {
        'pulsado': 'sincronizar'
    };
        $.ajax({
            data: parametros,
            url: 'tareas.php',
            type: 'post',
            beforeSend: function () {
                $("#resultado").html('Creando tablas, espere por favor......'+ icono);
            },
            success: function (response) {
                $("#resultado").html('Terminamos de crear tabla virtuemart_products en BDRecambios......');
				// La posible respuestas de funcion son:
				// $response['Eliminados'] -> Numero de registros eliminados.
				$("#ObservaSincro").html('Limpiamos '+ response["Eliminados"] + ' registros');
				$("#ObservaSincro").append('<br/>'+response["Copiado"]['resultado'] + ' :' + response["Copiado"]['descripcion']);
				console.log('Valor de Copiado es' + response["Copiado"]['resultado']);
				respuesta["Sincronizar"] = response["Copiado"]['resultado'];
				console.log('Ver repuesta'+response['Eliminados']);
				console.log('Fin tarea');
				// Ahora ejecutamos contar.
				Contar()
            }

        });

}


function Contar() {
	// Contamos registros de virtuemart y asignamos valor a varible global TotalProductosVirtuemart
	// pero solo podemos contar si el resultado de sincronizar es correcto.
	// Mientras respuesta["Sincronizar"] = 'Sinhacer' esto quiere decir que no termino , por lo que 
	// se repite esta funcion durante 20 veces.
	// NOTA:
	// Al terminar ejectuamos la funcion CrearVistas , aunque creamos las dos vistas y completas,
	// digo aunque, porque una no haría falta crearla ahora.
	if (respuesta["Sincronizar"] === 'Correcto') {
		// Ejecutamos la funcion Contar, por lo que el contador lo ponemos en 0
		contador = 0;
		var parametros = {
			'pulsado': 'ContarProductoVirtuemart'
		};
			$.ajax({
				data: parametros,
				url: 'tareas.php',
				type: 'post',
				beforeSend: function () {
					$("#resultado").html('Contando productos que hay virtuemart, espere por favor......'+ icono);
				},
				success: function (response) {
					$("#resultado").html('Terminamos de contar productos de virtuemart......');
					// Ponemos valor a la variable publica de NumeroProductos...
					TotalProductosVirtuemart = response;
					//~ TotalProductosVirtuemart = 250; // Solo para debug , asi no tenemos que hacer todo el proceso..
					$('#ObservacionesReferencias').html ('Encontramos '+ TotalProductosVirtuemart + ' productos en virtuemart');
					console.log('TotalProductosVirtuemart:'+ TotalProductosVirtuemart);
					console.log('No se porque la respuesta no se ve firebug pero si la obtiene....');
					// Ahora creamos o replazamos vistas ....si hay datos claro.
					if (TotalProductosVirtuemart >0 ){
						// Quiere decir que si se puede mostrar bottom... o no ... :-)
						$('#capa-botones').css("display", "block");
						// Creamos array de las dos vistas y creamos vistas......
						var vistas = ["virtuemart","vista_recambio"];
						CrearVistaInicio(vistas );
					}
					
					console.log('Fin tarea ContarVirtuemart');
					//~ console.log(response);
				}

			});
	} else {
		if (respuesta["Sincronizar"] === 'Error') {
			// Quiere decir que respondio sincronizar pero hubo un Error
			// por lo que no continuamos.
			return;
		}
		// Esto quiere decir que la respuesta["Sincronizar"] = 'Sinhacer'
		// entonces aun no contesto el servidor la funcion sincronizar por lo que
		// se va Ciclo('Contar')
		Ciclo('Contar');
	}
	
}
function CrearVistaInicio (vistas,limite) {
// Esta funcion la cargamos despues de contarRegistros, ya que si hay resultado de productos en virtuemart, creamos la vista.
	// Montamos arrays para enviar
	if (limite == undefined) {
		// ya en la primera llamada a la funcion no mandamos limite..
		var limite = [0,TotalProductosVirtuemart];
	}
	var parametros = {
        'pulsado': 'CrearVistas',
        'vistas':vistas,
        'limite':limite,
    };
        $.ajax({
            data: parametros,
            url: 'tareas.php',
            type: 'post',
            beforeSend: function () {
                $("#resultado").html('Creando Vistas o Replazando......'+ icono);
            },
            success: function (response) {
                $("#resultado").html('Terminamos de crear vistas o replazarlas.');
				// Comprobamos si fue todo ok.
				if (response['ViewVirtuemart']['consulta'] == true){
					// El resultado de está consulta tenemos que ponerlo en variable global
					// ya que lo necesitamos en funcion ComprobarRefVirtuemart
					respuesta['VistaVirtuemart'] = response['ViewVirtuemart']['consulta']
					// Solo añadimos la primera vez..
					if (LimiteFinal == 0){
						$('#ObservacionesReferencias').append('<br/> Se creo correcta vista virtuemart');
						// Ponemos como false porque sino al ejecutar ComprobarRefVirtuemart no genera la vista con limite.
						// y solo ponemos cuando no empezo a hacerlo..
						respuesta['VistaVirtuemart']= false;
					}
					console.log('Se creo correcta la vista virtuemart');
				}
				if (response['ViewRecambio']['consulta'] == true){
					// Solo añadimos la primera vez..
					if (LimiteFinal == 0){
						$('#ObservacionesReferencias').append('<br/> Se creo correcta vista recambios');
					}
					console.log('Se creo correcta la vista recambios');
					
				}
				console.log('Fin tarea CrearVistas');
				console.log(response);
            }

        });	
	
	
	
}
function ComprobarRefVirtuemart(paso){
		// Esta funcion es un bucle mientras se cumpla que variables LimiteActual > TotalProductos.
	
		// Recuerda que al cargar la pagina se indica en variable TotalProductosVirtuemart cuantos registros tiene virtuemart.
		// Utilizamos varias variables globales para :
		//  var contador -> que para controlar cuantas veces pasamos sin ejecutar 
		// 				Tiene valor 0 al inicio, sin haber pasado nunca.
		// 				Esta variable se incrementa cada vez que pasas por CicloComprobar
		// 				Vuelve a tener valor 1 cuando realizar una tarea.
		
		//  var respuesta['VistaVirtuemart'] -> Para controlas si se hizo o no la vista.
		//	var paso_actual-> que toma el valor del parametro paso, con ella controlamos si tenemos que hacer vista. 
		//				Es 1 para hacer vista
		// 				Es 2 cuando ya podemos hacer consulta.
		
		paso_actual = paso;
		console.log('--------------EJECTUAMOS PROCESOS PARA COMPROBAR -------------');
		console.log('Valor respuesta[VistaVirtuemart]'+ respuesta['VistaVirtuemart']);
		console.log('Contador de pasada:' + contador);
		console.log('Total productos:'+TotalProductosVirtuemart);
		console.log('LimiteActual:'+LimiteActual);
		console.log('Paso:' +paso );
		BarraProceso(LimiteActual,TotalProductosVirtuemart);
		if (LimiteActual < TotalProductosVirtuemart){
			// Ahora ejecutamos la funcion de crear vistas , pero solo creamos la vista "virtuemart"
			// con limite 100 mas ...
			if (paso === 1){
				// Quiere decir que vamos realizar la vista primero.
				if (respuesta['VistaVirtuemart'] === false ) {
					// Al pulsar el botton esta la variable respuesta['VistaVirtuemart'] == false , fijo.. 
					// Ya que en la funcion vistas , la ponemos false si el limite final es 0.
					LimiteFinal = LimiteActual + 100;
					if (LimiteFinal > TotalProductosVirtuemart) {
						// Esto es para evitar enviar un limite mayor al numero registros.
						LimiteFinal = TotalProductosVirtuemart
					}
					console.log('Nuevo valor de limiteFinal:'+LimiteFinal);
					var limite = [LimiteActual,LimiteFinal ];
					var vistas = ["virtuemart"];
					console.log(' Ahora creamos la vista Virtuemart pero con limite' + LimiteActual +','+ LimiteFinal);
					CrearVistaInicio(vistas,limite );
					// Ahora el LimiteActual ya es igual limiteFinal + 1
					LimiteActual = LimiteFinal +1 // Si limitefinal fuera igual TotalProductos saldría del bucle.
					// Ahora cambiamos el valor paso_actual para esperar resultado.			
					paso_actual = 2;
					contador= 0; 

				} else {
					// Quiere decir el paso_actual es 1 pero no termino la funcion anterior.
					if (respuesta['BuscarError'] === true){
						// Ponemos para entre....
						respuesta['VistaVirtuemart'] = false;
						f='BuscarError';
						console.log('Ultimo ciclo, ya que va entrar en Vista');
						window.setTimeout(function(){ Ciclo(f);},500);
						// Le ponemos mas tiempo, ya que la busqueda tarda mas..
						return;
					}
				}
				// Enviamos Ciclo para que espere unos segundo antes de continuar.
				// Reinicio contador ya que empezamos a contar.
				console.log('Entro PASO 1 pero NO en respuesta[VistaVirtuemart]=false');
				f='Esperar';
				window.setTimeout(function(){ Ciclo(f);},1000);
				// no permitimos continuar...
				return;
			}  
			// Si llega hasta aquí es que dio tiene paso=2 y respuesta['VistaVirtuemart'] -> debería ser "true"
			if ( paso === 2) {
				if (respuesta['VistaVirtuemart'] === true){
				// Termino de crear la Vista con limite.
				// Ahora hacemos la consulta para identificar si están bien las referencias
				// tanto la referencia ID recambio como la Referencia del fabricante cruzada.
			    respuesta['BuscarError'] = false;// Reinicio respuesta.. para espere respuesta..
			    BuscarError();
			    paso_actual = 1 ; // ya debería ser uno... ya que es el punto siguiente hacer...
			    // Enviamos Ciclo para que espere unos segundo antes de continuar.
				// Reinicio contador ya que empezamos a contar.
				contador= 0; 
				f='BuscarError';
				window.setTimeout(function(){ Ciclo(f);},2000);
			    // no permitimos continuar...
				return;
			    } 	
				
					
			}
			// Enviamos Ciclo para que espere unos segundo antes de continuar.
			f='BuscarError';
			console.log('No entro en paso 2');
			window.setTimeout(function(){ Ciclo(f);},1000);
			
			if (errorReferencias.length> 0) {
				// Quiere decir que hay ya errores..
				$("#EstadoReferencias").html('Error:'+ errorReferencias.length);
			}
			return;
		} else {
			// Quiere decir que ya termino.
			// Ahora comprobamos si hubo errores
			if (errorReferencias.length> 0) {
				$("#resultado").html('Hubo errores en el proceso COMPROBAR REFERENCIAS .');
				$("#EstadoReferencias").html('Error:'+ errorReferencias.length);
				$("#f-erroresCsv").css("display", "block");
				// Ahora metemos datos 
				objetoDatos = document.getElementById("DatosErrores");
				// dar el valor que ha recibido la función
				objetoDatos.value = JSON.stringify(errorReferencias);
			
			} else {
				$("#resultado").html('COMPROBACION DE REFERENCIAS CORRECTA.');
				$("#EstadoReferencias").html('Error:'+ errorReferencias.length);
				$("#f-revisarRef").css("display", "none");
			}
		}
		
}

function BuscarError(){
	console.log('------ FUNCION DE BUSCAR ERROR --------------');
	var parametros = {
		'pulsado': 'BuscarErrorRefVirtuemart'
	};
	$.ajax({
		data: parametros,
		url: 'tareas.php',
		type: 'post',
		beforeSend: function () {
			$("#resultado").html('Buscando posibles errores en Referencias (ID y ReferenciasCruzadas)');
		},
		success: function (response) {
			$("#resultado").html('Terminamos buscar errores en referencias cruzadas.');
			// Variable global si... 
			respuesta['BuscarError'] =true;
			console.log('Fin tarea BuscarErrorRefVirtuemart');
			Nerrores = response.length;
			Nitem = errorReferencias.length
			console.log('Numero items en errorReferencias:'+ Nitem);
			console.log('Numero errores:'+ Nerrores);
		    for (i = 0; i < Nerrores ; i++) {  
				errorReferencias[Nitem] = response[i];
				Nitem++ ;
			 }
			console.log(errorReferencias);
		}
	});	
}

function Ciclo(f) {
	// El objetivo de esta funcion volver a ejectuar la funcion
	// y intentarlo 20 veces, si fuera necesario.
	// Si fallara , mostraría un error diciendo que funcion no respondió.
	contador = contador +1;
	$("#resultado").html('Esperando respuesta intento:'+ contador +' funcion:'+f);
	// Solo hacemos 20 intentos ... 
	if (contador<20){
		// Ahora comprobamos la funcion que llamo al esta.
		switch(f) {
			case 'Contar':
				ContarProductoVirtuemart();
				break;
			case 'BuscarError':
				ComprobarRefVirtuemart(paso_actual);
				break;
			case 'Esperar':
				ComprobarRefVirtuemart(paso_actual);
				break;
		} 
	} else {
		console.log(' Hubo un error porque lo intento 20 veces....')
		$("#resultado").html('Error lo intento 20 veces, funcion' +f);

	}
}
