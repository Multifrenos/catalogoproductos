/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogoproductos de Soluciones Vigo.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero
 * @Descripcion	Javascript necesarios para recibircvs.php
 * */

// Función que inicia el ciclo de proceso, para 
// añadir datos mysql, el intervalo de tiempo
// puede modificarse en función servidor y hardware que se tenga.
function cicloProcesso() {
	console.log('Iniciamo cicloProceso una petición del ciclo cada 1 seg.');
	bucleProceso(lineaF, lineaActual, fichero);
	// En la instrucción anterior "bucleProceso(lineaF, lineaActual, fichero)"
	// realizamos el primer proceso, antes de empezar el ciclo.
	// No se sabe cuanto tiempo tarda en realizar INSERT de los 400 registros,
	// tambien habría que tener en cuenta que si el fichero es muy grande cada vez tardará mas, ya 
	// tiene que recorrer más lineas para montar el INSERT
	// Se estudio la posibilidad de hacerlo forma sincrono la peticiones AJAX, pero descarto: http://ayuda.svigo.es/index.php/programacion-2/javascript/176-peticiones-ajax-sincrono-o-asincrono
	//  Al utilizar setInterval() crea un ciclo ejecutando la funcion cada ms que le indiquemos.
	// 		- 	Empieza contar el tiempo y realiza petición:
	ciclo = setInterval("bucleProceso(lineaF,lineaActual,fichero)", 500);

}

// Función que al pulsar en Importar a MySql pone 
// valores a las variables.
// Y empezamos a EJECUTAR cicloProceso() me modo temporal.
function valoresProceso(valorCaja1, valorCaja2,nombretabla) {
	var respuestaConf = confirm('Si tiene datos la tabla temporal \n se va a Borrar su Contenido \n ¿Estas seguro? ');
	if (respuestaConf == true) {
		var parametros = {
			'nombretabla': nombretabla,
			'pulsado': 'borrarContenido'
		};
		$.ajax({
			data: parametros,
			url: 'tareas.php',
			type: 'post',
			beforeSend: function () {
				$("#resultado").html('Borrando '+nombretabla+', espere por favor...<span><img src="./img/ajax-loader.gif"/></span>');
			},
			success: function (response) {
				console.log(response);
				lineaF = valorCaja2;
				var lineaI = valorCaja1;
				lineaActual = lineaI;
				alert('Valores que tenemos ahora: \n ' + 'Linea Actual ' + lineaActual + ' \nLinea Final: ' + lineaF + '\nFichero:' + fichero);
				// Iniciar ciclo proceso. ;

			 cicloProcesso();

			}
		});
	}

}
