// JavaSCRIPT para modulo de importar de Catalogo de productos.
function BarraProceso(lineaA,lineaF) {
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
function bucleProceso (lineaF,linea,fichero) {
	// Este Script es el que utilizamos para que se ejecute cada cierto tiempo.
	// es decir, es llamado con setInterval("bucleProceso(lineaF,lineaActual)",20000);
	// desde cicloproceso()
	// Donde:
	//		- Iniciamos el intervalos de lineas que vamos a tratar.
	// 		- consultaDatos()
	//		- BarraProceso()
	// Y ademas controlamos si termino las lineas, por lo terminamos setInterval.
	
	if (parseInt(linea) < parseInt(lineaF)) {
		diferencia = parseInt(lineaF) - parseInt(linea)
		if (parseInt(diferencia) >400 ) {
			lineaActual = parseInt(linea) + 400;
			diferencia= 400; // Para utilizar en bucle
		} else {
			// Como ya no hay tanto registros ( 400) ponemos solo la diferencia
			lineaActual = parseInt(linea) + parseInt(diferencia) +1;
		}
	// Iniciamos proceso Barra;
	consultaDatos(linea,lineaActual,fichero);

	BarraProceso(lineaActual,lineaF);
	
	// Ahora si ya son iguales los linea y lineaF entonces terminamos ciclo
		if ( (parseInt(lineaActual)-1) == parseInt(lineaF) ){
			clearInterval(ciclo);
				// Ya terminamos esta paso, por lo que vamos a redireccionar los 
				// paso2 de cada fichero.
				// Pero solo redireccionamos deberíamos redireccionar si esta correcta la 
				// Importación, aunque le damos la opción al cliente.
			var respuestaContinuar = confirm('Terminamos, redireccionamos a PASO 2 de cada fichero'+'<br/>'+'¿ Quiere continuar ?');
			if (respuestaContinuar == true) {
				switch(fichero){
					case "ReferenciasCruzadas.csv":
						window.location.href='paso2ReferenciasCruzadas.php';
						break;
					case "ListaPrecios.csv":
						window.location.href='paso2ListaPrecios.php';
						break;
				}
			}
		}
	}
}

function consultaDatos(linea,lineaF,fichero) {
	// Script que utilizamos para ejecutar funcion de php , para importar datos a MySql.
	// Recuerda que importamos a BD y tabla temporar, me falta un proceso importación final.
	var parametros = {
	"lineaI" : linea,
	"lineaF" : lineaF,
	"Fichero" : fichero
			};
	$.ajax({
			// async:false, // Carga peticiones de forma sincrono , no asincrono.
            // cache:false, // No lo texteo lo suficiente , pero pienso que repite registros y lo hace mal...( por defecto es true)
			data:  parametros,
			url:   'msql_csv.php',
			type:  'post',
			beforeSend: function () {
					$("#resultado").html('Subiendo linea '+ linea + 'hasta '+ lineaF + ', espere por favor......<span><img src="./img/ajax-loader.gif"/></span>');
			},
			success:  function (response) {
					// Cuando se recibe un array con JSON tenemos que parseJSON
					var resultado =  $.parseJSON(response)
					$("#resultado").html('Linea Inicio:'+resultado['Inicio']+'<br/>'
									+'Linea Final:'+resultado['Final']+'<br/>'
									);
					// Si hay un mal insert deberiamos contarlos y anotarlo aqui.
					if (resultado['Resultado'] != "Correcto el insert" ) {
					// Primero cambiamos la clase , para poner advertencia.
					$('#ErrorInsert').addClass('alert alert-danger');
					$("#ErrorInsert").html('<strong>Error INSERT </strong>'+'<br/>'+' Ver console de javascript, error fichero de msql_csv.php');
					console.log("Responde");
					console.log(response.toString());
					}
					

			}
		});
}

