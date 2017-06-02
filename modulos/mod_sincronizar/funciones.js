/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogoproductos - Funciones sincronizar.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero - SolucionesVigo
 * @Descripcion	Javascript necesarios para modulo sincronizar.
 * */
// Asignar variables
var lineaA = 0 , lineaF = 0 ;
var icono = '<span><img src="../../css/img/ajax-loader.gif"/></span>';
var iconoCorrecto = '<span class="glyphicon glyphicon-ok-sign"></span>';

// Funcion para mostrar la barra de proceso..
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
                $("#resultado").html('Terminamos de crear tablas......');
				// La posible respuestas de funcion son:
				// $response['Eliminados'] -> Numero de registros eliminados.
				$("#ObservaSincro").html('Limpiamos '+ response["Eliminados"] + ' registros');
				
				$("#ObservaSincro").append('<br/>'+response["Copiado"]['resultado'] + ' :' + response["Copiado"]['descripcion']);
				if ( response["Copiado"]['resultado'] === 'Correcto'){
					// Quiere decir que si se puede mostrar bottom... o no ... :-)
					$("#EstadoSincro").html(iconoCorrecto);
					
					console.log(' Ahora debería cambiar el icono menu ... y mostras botones');
				}
				console.log('Ver repuesta'+response['Eliminados'])
				console.log('Fin tarea');
				console.log(response);
            }

        });

}
