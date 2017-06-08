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
	//No es un proceso que tarde mucho  por lo que no mostramos barra proceso. 
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
				if ( response["Copiado"]['resultado'] === 'Correcto'){
					// Quiere decir que podemos ejecutar el contar registros,
					// esta funcion no podemos hacerla antes....
					ContarProductoVirtuemart()
					// Tb cambiamos icono .. como correcto.
					$("#EstadoSincro").html(iconoCorrecto);
					console.log(' Ahora debería cambiar el icono menu ... y mostras botones');
				}
				console.log('Ver repuesta'+response['Eliminados']);
				console.log('Fin tarea');
				console.log(response);
            }

        });

}


function ContarProductoVirtuemart() {
	// Contamos registros de virtuemart y asignamos valor a varible global TotalProductosVirtuemart
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
				//~ TotalProductosVirtuemart = 250;

				$('#ObservacionesReferencias').html ('Encontramos '+ TotalProductosVirtuemart + ' productos en virtuemart');
				console.log(TotalProductosVirtuemart);
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
		// Esta funcion es un bucle hasta que la variables LimiteActual > TotalProductos .
		// Recuerda que al cargar la pagina se indica en variable TotalProductosVirtuemart cuantos registros tiene virtuemart.
		// Para controlar el paso que tenemos hacer y si podemos hacerlo :
		//      Variable Global: 
		//			respuesta['VistaVirtuemart'] -> false , es que no respondio correcto la vista.
		//	 		paso_actual-> que toma el valor del parametro paso. 
		//				Es 1 para hacer vista
		// 				Es 2 cuando ya podemos hacer consulta.
		paso_actual = paso;
		console.log('--------------EJECTUAMOS PROCESOS PARA COMPROBAR -------------');
		console.log('Contador de pasada:' + contador);
		console.log('Total productos:'+TotalProductosVirtuemart);
		console.log('LimiteActual:'+LimiteActual);
		console.log('Paso:' +paso );
		if (LimiteActual < TotalProductosVirtuemart){
			// Ahora ejecutamos la funcion de crear vistas , pero solo creamos la vista "virtuemart"
			// con limite 100 mas ...
			if (respuesta['VistaVirtuemart'] == false && paso === 1) {
				ComprobarRefVirtPaso1();
				paso_actual = 2;
				// Enviamos Ciclo para que espere unos segundo antes de continuar.
				setTimeout(CicloComprobarRef,1000);
				// no permitimos continuar...
				return;
			}
			// Si llega hasta aquí es que dio tiene paso=2 y respuesta['VistaVirtuemart'] -> debería ser "true"
			if ( paso === 2) {
				if (respuesta['VistaVirtuemart'] == true ){
				// Quiere decir , que tenemos la vista con limite.
				// cambiamos el valor de respuesta['VistaVirtuemart'] para que no vuelva ejecutarlo.
				respuesta['VistaVirtuemart'] = false;
				contador= 0; 
				// Ahora hacemos la consulta para identificar si están bien las referencias
				// tanto la referencia ID recambio como la Referencia del fabricante cruzada.
			    ComprobarRefVirtPaso2();
   				}
				if (respuesta['BuscarError'] === true){
					// Ahora ya tenemos resultado Busqueda Error
					// Ponemos valores para vuelva repetir el ciclo.
					paso_actual = 1;
					respuesta['BuscarError'] = false;
					contador = 0;
				}	
				
					
			}
			// Enviamos Ciclo para que espere unos segundo antes de continuar.
			setTimeout(CicloComprobarRef,5000);
			console.log('Antes barra proceso LIMITE ACTUAL:'+LimiteActual)
			console.log('Antes barra proceso LIMITE FINAL:'+LimiteActual)
			BarraProceso(LimiteActual,TotalProductosVirtuemart);
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
				$("#f-erroresCsv").css("display", "none");
			}
		}
		
}
function ComprobarRefVirtPaso1(){
	console.log('Ejecutando COMPROBARREFVIRTPASO1- CREAR VISTA');
	
	LimiteFinal = LimiteActual + 100;
	if (LimiteFinal > TotalProductosVirtuemart) {
		LimiteFinal = TotalProductosVirtuemart
	}
	console.log('Nuevo valor de limiteFinal:'+LimiteFinal);
	console.log('Producto Actual (LimiteActual):'+ LimiteActual);
	console.log('Hasta el Producto (LimiteFinal):'+LimiteFinal);
	var limite = [LimiteActual,LimiteFinal ];
	var vistas = ["virtuemart"];
	console.log(' Desde ComprobarRefVirtPaso1 ejecutamos CrearVistaInicio');
	CrearVistaInicio(vistas,limite );
	// Ahora el LimiteActual ya es igual limiteFinal + 1
	LimiteActual = LimiteFinal +1 // Si limitefinal fuera igual TotalProductos saldría del bucle.
	
}

function ComprobarRefVirtPaso2(){
	console.log('Ejecutando COMPROBARREFVIRTPASO2- BUSCAR ERROR');
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
function CicloComprobarRef() {
	// El objetivo de esta funcion esperar 10 segundo a respuesta.
	contador = contador +1;
	$("#resultado").html('Esperando respuesta intento:'+ contador);
	// Solo hacemos 10 intentos ... 
	if (contador<20){
		ComprobarRefVirtuemart(paso_actual);
	}
}

