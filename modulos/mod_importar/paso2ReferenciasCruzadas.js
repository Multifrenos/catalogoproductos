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
		clearInterval(ciclo);
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
	ciclo = setInterval(fabricexist, 200);
}

// * -------------------------------------------------------------* //
function resumenresul() {
	// Se ejecuta:  Al terminar carga la pagina ( Al inicio ) y en varios procesos mas.
	// Objetivo: El objetivo es cubrir datos, y comprobar el estado de todos los datos que necesitamos.
	// 			Es realmente el que controla muchos de los procesos.
	// Devuelve: Inicia y para procesos.
	
	var parametros = {
		'pulsado': 'resumen'
	};
	$.ajax({
		data: parametros,
		url: 'tareas.php',
		type: 'post',
		datatype: 'json',
		beforeSend: function () {
			$("#resultado").html('Realizando resumen fichero importar ReferenciasCruzadas, espere por favor...<img src="./img/ajax-loader.gif"/>');
		},
		success: function (response) {
			$("#resultado").html('Terminado resumen');
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
			if ( eval($("#Bfabcru").text()) > 0) {
				console.log('En resumen encontramos que falta algun fabricante por buscar su ID');
				// Sigue faltando algun fabricante por buscar.
				alert ('¿ Faltaran '+ +  $("#Bfabcru").text() +'algún fabricante por buscar ?');
				$("#resultado").html("Aun no termino el proceso encontrar Fabricantes...");
				DistintoFabCruzTemporal();
				return; //Para que no continue si no termina....
			} 
			// Ahora no dejo continuar si no tiene selecciona un fabricante, para evitar problemas.
			if (fabricante ==0 || fabricante === undefined){
				console.log('Mostramos botton de comprobar fabricante principal y return-> Debería dectenerse'); 
				$("#cmp").css("display", "block");
				return;
			} else {
				$("#cmp").css("display", "none"); // Ocultamos por existe fabricante.
			}
			// Si llega aquí es que tiene fabricante cruzados buscados con ID
			// Tiene seleccionado un Fabricante principal.
			// Mostramos informacion de Referencias Principales.
			// Se muestra información de referencias principales 
			console.log( 'En resumenresul() ->Si llega aquí:');
			console.log(' 1.- Que tienes seleccionado un fabricante.');
			console.log(' 2.- Que no hay ningún fabricante cruzado sin buscar si existe y su ID');
			$("#RefPrincipales").html(response.RefPrinEncontradas);// Referencias distintas encontradas en tabla
			$("#RefPrinPendIDRecam").html(response.RefPrinPendIDRecam);// Referencias Pendientes buscar IDRecambio
			if (response.RefPrinPendIDRecam == response.RefPrinEncontradas ) {
				// Distintas Referencias principales tiene su estado vacio y su IDRecambio es 0.
				console.log('Hay referencias principales sin comprobar que sin existe');
				$("#RefPrincipalesIDRecam").html('?');
				$("#RefPrincDescartadas").html('?');
				$("#Error23").html('?');
				$("#resultado").html("Estamos PASO2 y terminamos de comprobar FABRICANTES. Selecciona fabricante principal para seguir...");
			} else {
				$("#RefPrincipalesIDRecam").html(response.RefPrinYAIDRecam);// Referencias YA encontrado Recambio
				$("#RefPrincDescartadas").html(response.NRefPrinNOenc);// Referencias NO se encontro Recambio
				$("#Error23").html(response.error23); // Registros por referencias descartadas	
			}
			if (response.RefPrinPendIDRecam >0 ){
				// Quiere decir que ya se busco Referencias principales.
				console.log('Compruebo si faltan registros por comprobar las Referencias principales');
				// Quiere decir que aun no estan todas cubiertas.
				alert( ' Pusla "Comprobar Referencias Principales para continuar?');
				$("#ComprobarRefPrin").css("display", "block"); // Mostramos botton Comprobar existen Referencias Principales
				// Ejecutamos funcion obtener arrayConsulta.
				ObtenerReferenciasPrincipales('proceso2');				
				return; // No permito continuar hasta que termine...	
			}
			// Quiere decir que ya busco todas Referencia Principales por lo que no hay registros con la siguiente condiccion
			// No hay registros con esta condicion `Estado` = '' AND `RecambioID` <>0 AND `IdFabricaCruzado`<>0
			console.log(' Ya no deberían faltar Referencias principales sin ID o estado = [ERROR P2-23]:Referencia Principal no existe.');
			$("#NuevRefCruzada").html(response.NuevRefCruzada);// Referencias cruzadas nuevas para crear.
			$("#ExisteRefFaltaCruce").html(response.ExisteRefFaltaCruce);// Referencias existen, falta comprobar cruce.
			$("#ComprobarRefPrin").css("display", "none"); // Ocultamos botton Comprobar existen Referencias Principales
			if (response.NItemsEstadoBlanco > 0) {
				// Aun no se puede pasar al proceso 4 ya que no busco todas referencias cruzadas.
				// Ya que tendría que poner el estado como:
				// 		Nuevo -> Registros que no existen en BDRecambios/tabla referencias cruzadas.
				// 		Existe referencia cruzadas -> Pero tiene pendiente comprobar si existe cruce.
				console.log('Falta por controlar alguna referencia cruzada');
				$("#cmp").css("display", "none");
				$("#nuevoExiste").css("display", "block");
				$("#resultado").html('PASO3 - : Pulsa el Bottom de Comprobar si Referencias Curzadas son Nuevas o Existen.');
				alert( 'Pulsa bottom de Nuevo o Existe \n Y empazaremos a ver que referencias cruzdas va se nuevas o no');
				return;
			} else {
				console.log('Debería tener todas las referencias cruzadas como Nuevas o Existente.');
				// Quiere decir que no hay registros para comprobar, por lo que debemos comprobar cruces.
				if (response.ExisteRefFaltaCruce > 0 ) {
					// Quiere decir que hay que comprobar si existe el cruce.
					alert( "Ahora comprobamos si existen los cruces [PENDIENTE]...");
				
				}
				// Ahora debemos obtener de nuevo ArrayConsulta pero que tenga Estado con Existe referencia cruzada
				ObtenerReferenciasPrincipales('proceso4');
				console.log('Ya volvimos a resumenresul() al mismo punto, con '+arrayConsulta['NItems'] + ' registros que nos sabemos si existe cruce');
				
				
			}
			// Ahora ya no hay registros en blanco.
			if (arrayConsulta['proceso']=='proceso4'){
					// Quiere decir que existe referencias que no se comprobo que si existe cruce
					alert(' Proceso4:'+arrayConsulta['proceso']+' Debería ir procesoInicioCiclo para que este ejecute ciclo correspondiente.');
			} 
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
	} else {
		// Dehabilito opción de cambiar fabricante principal
		$('#IdFabricante').prop('disabled', true);
		resumenresul()
	}
};
// * -------------------------------------------------------------* //
function ObtenerReferenciasPrincipales(proceso) {
	// Se ejecuta:  Esta funcion se ejecuta desde resumenresul() y al pulsar bottom [Comprobar Nuevo o Existe]
	// Objetivo: 	Se obtiene un array con Referencias Principales:
	//					1.- (proceso2) -> Si viene resumen, las referencias son distintas y con el estado = '' y IDFabricante <>0
	// 					2.- (proceso3) -> Si pulsa bottom, son registros con referencias con el estado='' y  IDRecambio <>0
	//
	// Devuelve: Array en JSON ,con los campos que se le indica en cadad caso.
	// INICIALIZAMOS VARIABLES
	finallinea = 0;
	intermedia = 0;
	// Ahora montamos el condicional según quien lo ejecuto la funcion.
	console.log ('Estamos en ObtenerReferenciaPrincipal y parametro ' + proceso);
	if (fabricante !== 0) {
		var parametros = {
			'pulsado': 'ObtenerReferenciasPrincipales',
			'condicional' : proceso
		};
		$.ajax({
			data: parametros,
			url: 'tareas.php',
			type: 'post',
			datatype: 'json',
			beforeSend: function () {
				console.log ('Obteniendo array con registros que tiene estado en blanco');
				$("#resultado").html('Obteniendo arrayConsulta para procesar<img src="./img/ajax-loader.gif"/>');
			},
			success: function (response) {
				arrayConsulta = response;
				console.log("Termino ObtenerReferenciasPrincipales()");
				console.log("Numero registros obtenidos "+arrayConsulta['NItems']);
				if (arrayConsulta['NItems'] !== 0) {
					// Quiere decir existen registro para analizar
					finallinea = arrayConsulta['NItems'];
					var proceso = arrayConsulta['proceso'];
					if (proceso == "proceso2") {
						$("#resultado").html('Iniciamos ciclo cicloReferenciaPrincipa() con nºRegistro:'+arrayConsulta['NItems']);
						console.log('Estamos en PASO2-Proceso 2 :Iniciamos ciclo CicloComprReferenciaPrincipal');
						procesoInicioCiclo();
						return;
					}
					if (proceso == "proceso3"){
						// Aquí tiene que ir a procesoInicioCiclo
						console.log('Estamos en PASO2-Proceso 2 :Iniciamos ciclo CicloComprReferenciaPrincipal');
						alert(' Proceso3:'+arrayConsulta['proceso']+' Debería ir procesoInicioCiclo para que este ejecute ciclo correspondiente.');
						procesoInicioCiclo();
						return; // para que vuelva ejecutar resumen...
					}
					if (proceso == "proceso4"){
						// Ahora debería iniciar ciclo de comprobar cruce
						console.log(' Se obtiene '+arrayConsulta['NItems']+' de arrayConsulta con los registros existen pero no comprobamos si existe cruce');
						$("#resultado").html('Se obtiene '+arrayConsulta['NItems']+' de arrayConsulta del proceso ' + arrayConsulta['proceso'] +' con los registros existen pero no comprobamos si existe cruce');
						procesoInicioCiclo();
						return;
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
	var ItemsEnviar = [] ;
		if ( intermedia <= arrayConsulta['NItems']) {
			for (i = 0; i < 200; i++) {  
				if (intermedia <= arrayConsulta['NItems']){
				// Montamos array para enviar por AJAX
				ItemsEnviar[i] = arrayConsulta[intermedia];
				intermedia = intermedia + 1;
				}
			}
			ProcesoBarra(intermedia, finallinea);
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
					console.log("******* RESPUESTA AJAX DE cicloReferenciaPrincipal() *************************");
					console.log('Consulta'+ response['Consulta1']);
					console.log("Errores:" + response['RegistrosErrorRefPrincipal']);
					textoMostrar = "Terminados revisar los " +intermedia+" referencias, encontradas \n "+ response['RegistrosErrorRefPrincipal']+" registros que no existen referencias.";
					$("#resultado").html(textoMostrar);
					
				}
			});
		} else {
			console.log('termino ciclo de comprobar Referencia Principales');
			clearInterval(ciclo);
			// Quiere decir que termino... comprobar si existen las REFERENCIAS PRINCIPAL.
			textoMostrar = "¡¡ TERMINAMOS REVISAR REFERENCIAS, SI EXISTEN !! \n";
			textoMostrar = textoMostrar + " REALIZAMOS RESUMEN."
			$("#resultado").html(textoMostrar);
			// Es mejor esperar un poco antes de hacer resumen ya que puede que no este todos UPDATE terminado.
			// Si no devuelve array es que ya no hay vacios, por lo que se termino.
			// Hacemos resumen de nuevo
			resumenresul();
			
		}
}

function procesoInicioCiclo() {
	// Se ejecuta:  Se llega aquí desde Resumenresul() pero teniendo encuenta que de varios procesos
	// Objetivo: Deberíamos tener datos arrayConsulta y desde aquí controlamos que ciclo iniciamos.
	// Devuelve: Datos pero no son necesarios para el proceso.	
	console.log ('Estoy en Proceso Inicio Ciclo');
	// Lo primero comprobar que tenga datos ArrayConsulta
	if (arrayConsulta != undefined){
		// Quiere decir que hay arrayConsulta , que esta definido... 
		console.log( 'Ahora definimos con finallinea es arrayConsulta[NItems]:' + finallinea );
		finallinea = arrayConsulta['NItems'];
		console.log( arrayConsulta );
		console.log( 'Ahora definimos con intermedio: 0');
		intermedia = 0;
		// Hay NItems entonces debemos comprobar en que proceso estamos, para selecionar que setIntervla ejecutamos.
		if (finallinea > 0 ) {
			if (arrayConsulta['proceso'] == 'proceso2'){
			console.log('Iniciamos ciclo cicloReferenciaPrincipal estamos en ArrayConsulta[proceso]:'+ arrayConsulta['proceso']);
			alert ( 'Iniciamos ciclo cicloReferenciaPrincipal ya estamos en ArrayConsulta[proceso]:'+ arrayConsulta['proceso']);
			ciclo = setInterval(cicloReferenciaPrincipal,1000);
			}
			if (arrayConsulta['proceso'] == 'proceso3'){
			console.log('Iniciamos ciclo cicloNuevoExisteCruce ya estamos en ArrayConsulta[proceso]:'+ arrayConsulta['proceso']);
			alert ( 'Iniciamos ciclo cicloNuevoExisteCruce ya estamos en ArrayConsulta[proceso]:'+ arrayConsulta['proceso']);
			// El tiempo espera para enviar solicitudes, tenemos que medirlo ya que segun el peso y servidor puede tardar mas o menos.
			var tiempotarda= (finallinea/40);
			
			ciclo = setInterval(cicloNuevoExisteCruce, tiempotarda);
			}
			if (arrayConsulta['proceso'] == 'proceso4'){
			console.log('Iniciamos cicloAnhadirCruce() ya estamos en ArrayConsulta[proceso]:'+ arrayConsulta['proceso']);
			alert ( 'Iniciamos cicloAnhadirCruce() ya estamos en ArrayConsulta[proceso]:'+ arrayConsulta['proceso']);
			ciclo = setInterval(cicloAnhadirCruce,500);
			}
		}	
	}
}

function cicloNuevoExisteCruce (){
	// Se ejecuta:  desde setInterval() desde procesoInicioCiclo())
	// Objetivo: Cambia el estado aquellas referencias cruzadas que sea NUEVAS o EXISTE, recuerda que se ejecuta cada cierto tiempo que indica setInterval. Ademas recuerda que puede estar limitado el servidor en recibir variables, por eso hace así.
	// Devuelve: Datos pero no son necesarios para el proceso.
	
	console.log( 'Creamos condicional mientras intermedio sea menor o igual arrayConsulta[NItems]');
	var ItemsEnviar = [];
	console.log('Linea actual:' +intermedia+' es menor o igual que final:' + finallinea);
	if ( intermedia <= arrayConsulta['NItems']) {
		for (i = 0; i < 200; i++) {  
			if (intermedia <= arrayConsulta['NItems']){
			// Montamos array para enviar por AJAX
			ItemsEnviar[i] = arrayConsulta[intermedia];
			intermedia = intermedia + 1;
			}
		}
	console.log(' Mostramos barra proceso')
	ProcesoBarra(intermedia, finallinea);
	console.log(' Creamos ItemsEnviar')
	console.log(JSON.stringify(ItemsEnviar));
	console.log("Fabricante "+fabricante);
	
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
				textoMostrar = "Comprobando Referencias cruzadass sison NUEVA o EXISTEN";
				$("#resultado").html(textoMostrar);
			},
			success: function (response) {
				console.log('Sumamos los que hemos cambiado');
				textoMostrar = "Llevamos ya revisados " +intermedia+" de los "+ finallinea +" \n que son las referencias distintas que aun tienen Estado en blanco.";
				$("#resultado").html(textoMostrar);
				// Ahora obtenemos el datos que tiene
				var TNuevos = $("#NuevRefCruzada").text();
				var TNuevoCruces = $("#NuevoCruce").text();
				var TExisten = $("#NExisteCruce").text();
				// Ahora hacemos la comprobacion para saber si falta por hacer o no.
				var suma = 0;
				var Nnuevo =parseInt(TNuevos);
				var NnuevoCruce =parseInt(TNuevoCruces);
				var Nexisten = parseInt(TExisten) ;
				// Ahora hacemos la lógica.
				if ( Nnuevo > 0 ) {
					Nnuevo = Nnuevo + response.resultado[1];
					console.log('Entro Nnuevo');
				} else {
					Nnuevo = response.resultado[1];
					console.log('NO ENTRO Nnuevo');
				}
				if ( NnuevoCruce > 0 ) {
					NnuevoCruce = NnuevoCruce + response.resultado[2];
					console.log('Entro NnuevoCruce');
				} else {
					NnuevoCruce = response.resultado[2];
					console.log('NO ENTRO NnuevoCruce');
				}
				if ( Nexisten > 0 ) {
					Nexisten = Nexisten + response.resultado[3];
					console.log('Entro Nexisten');
				} else {
					Nexisten = response.resultado[3];
					console.log('NO ENTRO Nexisten');
				}
				suma = Nexisten + NnuevoCruce + Nnuevo;
				console.log('suma de Nuevo +Existen +NuevoCruce:' + suma); 
				$("#NuevRefCruzada").html(Nnuevo );
			} 
			
		});
	
	} else {// Quiere decir que intermedia es mayo que finallinea por lo se termino.
				// Quiere decir que termino el ciclo..
				// Recuerda ciclo es una varible publica.
				// Ademas la igualamos setInterval(cicloNuevoExisteCruce, 500)
				console.log('Entro en else fin de ciclo =cicloNuevoExisteCruce');
				console.log(' Intermedia:'+intermedia);
				console.log(' FinalLinea.'+finallinea);
				clearInterval(ciclo); 
				// Volvemos a realizar resumen
				resumenresul();
	}
}

function cicloAnhadirCruce(){
	// Se ejecuta:  desde setInterval() desde procesoInicioCiclo())
	// Objetivo: El objetivo es comprobar si existe el cruce 
	//				1.- Si existe , entonces se cambia Estado = '[ACTUALIZADO] EXISTE CRUCE Y REFERENCIA PRINCIPAL'
	//				y añado datos ID cruce y IDRefCruz
	// 				2.- Si no existe se añade el CRUCE, Estado = '[CREADO CRUCE]
	// Devuelve: Datos pero no son necesarios para el proceso.
	
	console.log( 'Ciclo AnhardirCruce mientras intermedio sea menor o igual arrayConsulta[NItems]');
	var ItemsEnviar = [];
	console.log('Linea actual:' +intermedia+' es menor o igual que final:' + finallinea);
	if ( intermedia <= arrayConsulta['NItems']) {
		for (i = 0; i < 200; i++) {  
			if (intermedia <= arrayConsulta['NItems']){
			// Montamos array para enviar por AJAX
			ItemsEnviar[i] = arrayConsulta[intermedia];
			intermedia = intermedia + 1;
			}
		}
	console.log(' Mostramos barra proceso')
	ProcesoBarra(intermedia, finallinea);
	console.log(' Creamos ItemsEnviar')
	console.log(JSON.stringify(ItemsEnviar));
	console.log("Fabricante "+fabricante);
	
	// Ahora enviamos datos a funcion por Ajax
	var parametros = {
			'pulsado': 'AnhadirCruce',
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
				console.log('Respuesta cicloAnhadirCruce');
				textoMostrar = "Ya comprobamos si existe cruce los registros " +intermedia+" de los "+ finallinea +" \n que teniamos para comprobar.";
				$("#resultado").html(textoMostrar);
				// Ahora obtenemos el datos que tiene
				var TNuevos = $("#NuevRefCruzada").text();
				var TNuevoCruces = $("#NuevoCruce").text();
				var TExisten = $("#NExisteCruce").text();
				// Ahora hacemos la comprobacion para saber si falta por hacer o no.
				var suma = 0;
				var Nnuevo =parseInt(TNuevos);
				var NnuevoCruce =parseInt(TNuevoCruces);
				var Nexisten = parseInt(TExisten) ;
				// Ahora hacemos la lógica.
				if ( Nnuevo > 0 ) {
					Nnuevo = Nnuevo + response.resultado[1];
					console.log('Entro Nnuevo');
				} else {
					Nnuevo = response.resultado[1];
					console.log('NO ENTRO Nnuevo');
				}
				if ( NnuevoCruce > 0 ) {
					NnuevoCruce = NnuevoCruce + response.resultado[2];
					console.log('Entro NnuevoCruce');
				} else {
					NnuevoCruce = response.resultado[2];
					console.log('NO ENTRO NnuevoCruce');
				}
				if ( Nexisten > 0 ) {
					Nexisten = Nexisten + response.resultado[3];
					console.log('Entro Nexisten');
				} else {
					Nexisten = response.resultado[3];
					console.log('NO ENTRO Nexisten');
				}
				suma = Nexisten + NnuevoCruce + Nnuevo;
				console.log('suma de Nuevo +Existen +NuevoCruce:' + suma); 
				$("#NuevRefCruzada").html(Nnuevo );
			} 
			
		});
	
	} else {// Quiere decir que intermedia es mayo que finallinea por lo se termino.
				// Quiere decir que termino el ciclo..
				// Recuerda ciclo es una varible publica.
				// Ademas la igualamos setInterval(cicloNuevoExisteCruce, 500)
				console.log('Entro fin cicloAnhadirCruce');
				console.log(' Intermedia:'+intermedia);
				console.log(' FinalLinea.'+finallinea);
				clearInterval(ciclo); 
				// Volvemos a realizar resumen
				resumenresul();
	}
}
