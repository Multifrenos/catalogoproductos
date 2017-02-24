/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogoproductos de Soluciones Vigo.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero
 * @Descripcion	Javascript necesarios para paso2ListaPrecios.php
 * */
 
 /* ===================== Funcion de ComprobarPaso2ListaPrecios   ========================*/
            // Llegamos a esta funcion al pulsar comprobar, si tabla tiene algún campo vacio.
            // Lo que hacemos :
            // 		1.- Comprobar si selecciono familias y fabricante.
            //		2.-	Contar registros tiene y cuantos tienes el estado vacio.
            // Y si hay vacios entonces iniciamos el ciclo, para que comprueb si son nuevos o existentes.
            // Si NO hay vacios , entonces solo presentamos resumen y presentamos bottom comprobar.
function ComprobarSeleccionFamFab (fabricante,familia)
{
	fa = familia;
	f = fabricante;
	// Lo primero comprobar que familia y fabricante selecciono alguno, ya no tiene 
	// sentido continuar si no selecciono nada.
	if (fa == 0) {
	  alert( "No selecciono familias");
	  return;	
	}
	if (f == 0) {
	  alert( "No selecciono fabricante");	
	  return;
	}
	// Desactivamos para evitar posibles cambio y errores:
	//		btn de comprobar
	//		select de fabricantes y familias
	document.getElementById("BtnComprobar").disabled = true;
	document.getElementById("IdFabricante").disabled = true;
	document.getElementById("IdFamilia").disabled = true;
	
	// Mostramos cuadro de Resumen.
	document.getElementById('CjaComprobar').style.display = 'block';
	resumen(nombretabla);
} 
 


/* =========================  Funcion de consulta  ========================================*/
// Comprobamos REFFABRICANTE PRINCIPAL de los registros con el Estado está vacio de la tabla listaprecios en BDRecambios/REFERENCIASCRUZADAS si existe la referencia
// 		-Si existe se pone en ESTADO = "existe"
// 		-NO existe se pone en ESTADO = "nuevo"
// El cambio ESTADO es en la tabla LISTAPRECIOS de BD IMPORTARRECAMBIOS.

function consulta(rs) {
// si la línea intermedia es menor que la línea final iniciamos el bucle
	var tabla = "listaprecios";
	if (b < a) {
		var parametros = {
			'nombretabla': tabla,
			'pulsado': 'comprobar',
			'idrecambio': rs[b].id,
			'linea': rs[b].linea,
			'fabricante': f
		};
	 
		$.ajax({
			
			url: 'tareas.php',
			type: 'post',
			datatype: 'json',
			data: parametros,
			beforeSend: function () {
				console.log ('Comprobando tabla '+nombretabla+' en tarea comprobar la referencia '+rs[b].id);
				$("#resultado").html('Combrobando si existe en BDRecambio/ReferenciaCruzadas el '+rs[b].id+' de la Referencia Fabricante...<span><img src="./img/ajax-loader.gif"/></span>');
			},
			success: function (response) {
			   $("#resultado").html('Buscado '+rs[b].id+' en BDRecambio/ReferenciaCruzadas.');
				respuesta = JSON.parse(response)
				n = n + (respuesta[0].n);
				e = e + (respuesta[0].e);
				console.log('linea'+rs[b].linea);
				console.log('nuevos='+n+' existentes='+e);
				
				$("#vacio").html(respuesta[0].t+"/"+a);
				$("#nuevos").html(n);
				$("#existentes").html(e);
				b++;

			}
		});


	} else {
		// cuando acaba el ciclo creamos el campo terminar y cerramos el bucle
		clearInterval(set);
		// Volvemos a poner valor ciclo en 0
		b = 0;
		// Volvemos ejecutar resumen 
		alert ('Volvemos hacer resumen');

		resumen(tabla);
	}

}
// Empezamos el ciclo de comprobar si es nuevo, existe o tiene un error
function ciclo(respuesta) {
	rs = respuesta ;
	set = setInterval("consulta(rs)", 100);
}
/* =========================  Funcion de resumen  ========================================*/
// Si en ComprabarPaso2Listaprecios NO encuentrar en la tabla listaprecios tiene registros con el estado VACIO, entonces nos trae aquí. 
// En donde realizamos RESUMEN, es decir comprueba cuantos registros hay y cuantos son nuevo o existentes.

function resumen (nombretabla){
	var tabla = "listaprecios";
	var parametros = {
		'nombretabla': tabla,
		'pulsado': 'contar'
		};
	 
		$.ajax({
			
			url: 'tareas.php',
			type: 'post',
			datatype: 'json',
			data: parametros,
			beforeSend: function () {
				$("#resultado").html('Contando y realizando RESUMEN...<span><img src="./img/ajax-loader.gif"/></span>');
			},
			success: function (response) {
			   // n  y e son una varible global
			   // donde n son los nuevos y e los existentes.
				var respuesta = JSON.parse(response);
				// Calculamos aquellos campos que tiene otro estado diferente a nuevo,existen
				var otroestado = respuesta['t']-respuesta['v']-respuesta['n']-respuesta['e'];
				// Cambiamos valor variable global (a) linea final;
				a = respuesta['v'];
				$("#total").html(respuesta['t']);
				$("#vacio").html(respuesta['v']);
				$("#nuevos").html(respuesta['n']);
				$("#existentes").html(respuesta['e']);
				$("#otrosEstados").html(otroestado);
				// No hay vacios mostramos botton terminar
				if ( a == 0 ){
					document.getElementById('Paso3').style.display = 'block';
					$("#resultado").html('Pulsa terminar si quieres crear los recambios nuevos,								\n o modificar los precios de los existentes.')
					return;
				} else {
				// Hay registros listadoprecios con el Estado en blanco por lo que iniciamos ciclo
				console.log ( response);
				$("#resultado").html('Iniciamos ciclo para comprobar cuales son nuevos o existentes de '+nombretabla);
				alert ('Encontramos registros vacios ('+ a +') empezamos ciclo' );
				var respuesta2= respuesta['LineasRegistro'];
				console.log ('ciclo (b) =' + b);
				console.log (respuesta2);
				ciclo(respuesta2);
				}
			}
		});
	
}


            // buscamos el total de referencias que vamos a lanzar el bucle y las añadimos temporal
            function paso3() {
                var parametros = {
                    'pulsado': 'verNuevos',
                    'fabricante': f
                };
                $.ajax({
                    data: parametros,
                    url: 'tareas.php',
                    type: 'post',
                    datatype: 'json',
                    beforeSend: function () {
                        $("#resultado").html("Iniciamos ciclo de proceso, espere por favor...");
                    },
                    success: function (response) {
                        // cubrimos la linea final y lanzamos el ciclo
                  
                        a = response.length;
                        rs = response;
						console.log(response);
                        console.log(' Voy a funcion anhadir');
                        $("#resultado").html("Obtenemos "+ a +" registros para añadir y modificar");
						alert( "Inicio de ciclo terminar \n"+ "Id Inicial:"+ b +"\n Id Final" + a);
                        // Vamos empezar ciclo, por lo que deactivamos botton de terminar , para que no de otra vez
						document.getElementById("BtnTerminar").disabled = true;
						console.log('rs'+ rs[b].coste);
						
                        cicloAnhadir(rs);

                    }
                });

            }
            
            // esta función va a hacer el paso definitivo:
            // 	1.- Comprueba que si es nuevo	
            //		1.1.- Crea uno nuevo recambio.
            // 	 	1.2.- Crea una relación recambio - familia
            //		1.3.- Crea una relacion referencia cruzada. 
            // 	2.- Si existe solo actualiza el coste
            function anhadirnuevos(rs) {
				console.log('B:'+ b);
				console.log('a:'+ a);
				
                if (b < a) {
					console.log('Estado:'+rs[b].estado);
                    var nombretabla = "recambios";
                    var parametros = {
                        'nombretabla': nombretabla,
                        'pulsado': 'anahirRecam',
                        'fabricante': f,
                        'familia': fa,
                        'coste': rs[b].coste,
                        'descrip': rs[b].des,
                        'referen': rs[b].ref,
                        'estado':rs[b].estado,
                        'idrecam': rs[b].id

                    };
                    //document.getElementById('resultado').innerHTML='Id='+ rs[b].id;
                    $.ajax({
                        data: parametros,
                        url: 'tareas.php',
                        type: 'post',
                        beforeSend: function () {
                           $("#resultado").html("Funcion anahdir ,"+ b +''+a);
                        },
                        success: function (response) {
                            document.getElementById('resultado').innerHTML='Repuesta RefFabriPrin='+ rs[b].ref;
                            console.log('Repuesta anahirNuevo:'+ response);
                            b++;
                            ProcesoBarra(b, a);
                        }

                    });

				}else{
					  clearInterval(set);
					  document.getElementById('resultado').innerHTML='Terminado el añadir recambios nuevos y cambio precio existente.';


				  b = 0;           
				}
            }
          
            
           // lanza el ciclo
            function cicloAnhadir(response) {
                console.log('rs'+ rs[b]);
                set = setInterval("anhadirnuevos(rs)", 500);

            } 
