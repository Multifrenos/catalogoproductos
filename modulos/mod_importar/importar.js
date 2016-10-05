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
		if ( parseInt(lineaActual) == parseInt(lineaF) ){
			alert ( 'terminamos' );
			clearInterval(ciclo);
				// Ahora deberíamos hacer una comprobación de como quedo la cosa.
				// es decir :
				//     -Comprobar cuantos registros añadio a la base de datos.
				//     -Comprobar si hay referencias repetidas, tanto RefDKM , como RefFabricante
				//     -Comprobar cuantos Fabricantes hay y cuanto hay que añadir a Fabricantes.
				// 	   -Comprobar cuantas Referencias de DKM hay y cuantos hay añadir.
			
           switch(fichero){
               case "ReferenciasCruzadas.csv":
                   window.location.href=' paso2ReferenciasCruzadas.php';
                   break;
               case "ListaPrecios.csv":
                    window.location.href = 'paso2ListaPrecios.php';
                break;
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
			data:  parametros,
			url:   'msql_csv.php',
			type:  'post',
			beforeSend: function () {
					$("#resultado").html("Procesando, espere por favor...");
			},
			success:  function (response) {
					$("#resultado").html(response);
					
			}
		});
}

