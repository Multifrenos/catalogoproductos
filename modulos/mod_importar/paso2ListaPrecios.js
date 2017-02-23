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
 
 
 function ComprobarPaso2ListaPrecios(fabricante, familia) {
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
                alert(' Nombre tabla:'+nombretabla)
                var parametros = {
                    'nombretabla': nombretabla,
                    'pulsado': 'contarVacios'
                };
                $.ajax({
                    data: parametros,
                    url: 'tareas.php',
                    type: 'post',
                    datatype: 'json',
                    beforeSend: function () {
                        $("#resultado").html('Comprobando el campo ESTADO tabla, que este vacio y obteniendo ID y Lineas.<span><img src="./img/ajax-loader.gif"/></span>');
                    },
                    success: function (response) {
						// Convertimos a objeto response.
						var respuesta = JSON.parse(response);
						if (respuesta.length == 0) {
                            // Al buscar en contar registros en tabla listaprecios ;
                            // no encuentrar ningún registro con el estado vacio.
                            alert("En BDimportarRecambio la tabla "+ nombretabla + "\n no tiene ningún registro con su estado en vacio \n por lo que no se hace comprobación.");
                            // Ejecutamos funcio que cuenta ( nuevos, existentes y erroneos. )
	                        resumen(nombretabla);
                            document.getElementById('Paso3').style.display = 'block';

                        } else {
							// Registros vacio.
							a = respuesta.length;
							alert ( "Hemos encontrador "+ a + " vacios");	
                            // cargamos en la variable a el final de linea que es el total de registros del array
                            // iniciamos el ciclo
                            ciclo(respuesta);
                        }

                    }
                });

            }
            
           
			/* =========================  Funcion de consulta  ========================================*/
            // Comprobamos REFFABRICANTE PRINCIPAL de los registros con el Estado está vacio de la tabla listaprecios en BDRecambios/REFERENCIASCRUZADAS si existe la referencia
            // 		-Si existe se pone en ESTADO = "existe"
            // 		-NO existe se pone en ESTADO = "nuevo"
            // El cambio ESTADO es en la tabla LISTAPRECIOS de BD IMPORTARRECAMBIOS.
            
            function consulta(rs) {
            // si la línea intermedia es menor que la línea final iniciamos el bucle
                if (b < a) {
                    var nombretabla = "listaprecios";
                    var parametros = {
                        'nombretabla': nombretabla,
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
							console.log(rs)
                            $("#resultado").html('Combrobando si existe en BDRecambio/ReferenciaCruzadas el '+rs[b].id+' de la Referencia Fabricante...<span><img src="./img/ajax-loader.gif"/></span>');
                        },
                        success: function (response) {
                           $("#resultado").html('Buscado '+rs[b].id+' en BDRecambio/ReferenciaCruzadas.');
                            respuesta = JSON.parse(response)
                            n = n + (respuesta[0].n);
                            e = e + (respuesta[0].e);
							console.log('n='+n+' e='+e)
                            
                         
                            
                            $("#vacio").html(respuesta[0].t+"/"+a);
                            $("#nuevos").html(n);
                            $("#existentes").html(e);
                            b++;

                        }
                    });


                } else {
                    // cuando acaba el ciclo creamos el campo terminar y cerramos el bucle
                    document.getElementById('Paso3').style.display = 'block';
					$("#resultado").html('Pulsa terminar si quieres crear los recambios nuevos,										\n o modificar los precios de los existentes.')
                    clearInterval(set);
                    b = 0;

                }

            }
            // Empezamos el ciclo de comprobar si es nuevo, existe o tiene un error
            function ciclo(respuesta) {
				rs = respuesta ;
                set = setInterval("consulta(rs)", 200);
            }
            /* =========================  Funcion de resumen  ========================================*/
            // Si en ComprabarPaso2Listaprecios NO encuentrar en la tabla listaprecios tiene registros con el estado VACIO, entonces nos trae aquí. 
            // En donde realizamos RESUMEN, es decir comprueba cuantos registros hay y cuantos son nuevo o existentes.
            
            function resumen (nombretabla){
				var nombretabla = "listaprecios";
				var parametros = {
					'nombretabla': nombretabla,
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
                       
                            $("#total").html(response['t']);
                            $("#vacio").html(response['v']);
                            $("#nuevos").html(response['n']);
                            $("#existentes").html(response['e']);
                            $("#resultado").html('Pulsa terminar si quieres crear los recambios nuevos,								\n o modificar los precios de los existentes.')
                            return;

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

                        console.log(' Voy a funcion anhadir');
                        $("#resultado").html("Coste:<pre>"+rs[b].estado+"</pre>Numero filas que devuelve verNuevasRef ="+ a);
						alert( "Inicio de ciclo terminar \n"+ "Id Inicial:"+ b +"\n Id Final" + a);
                        
                        anhadir(rs);

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
				//~ console.log('Referencia:');
				//~ console.log(rs[b].ref);
				console.log('Estado:');
				console.log(rs[b].estado);
				//~ console.log('Condicional de anhadirnuevos');

                if (b < a) {

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
                            b++;
                            document.getElementById('resultado').innerHTML='Repuesta RefFabriPrin='+ rs[b].ref;
                            console.log('Repuesta anahirNuevo:'+ b);
                            
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
            function anhadir(response) {
				// Vamos empezar ciclo, por lo que deactivamos botton de terminar , para que no de otra vez
				document.getElementById("BtnTerminar").disabled = true;

                b = 1;
                console.log('rs'+ rs[b]);
                set = setInterval("anhadirnuevos(rs)", 1000);

            } 
