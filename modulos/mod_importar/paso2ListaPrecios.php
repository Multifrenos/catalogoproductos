<!DOCTYPE html>
<html>
    <head>
        <?php
// Reinicio variables
        $htmloptiones = '';
        $htmlfamilias = '';
        include './../../head.php';
        include ("./../mod_conexion/conexionBaseDatos.php");
        ?>
      
    </head>

    <body>
        <?php
        include './../../header.php';
        ?>
        <div class="container">
            <div class="col-md-12 text-center">
                <h2>Paso 2 - Lista Precios: Seleccionar Familia ,Fabricante y buscar referencia </h2>
            </div>

            <div class="col-md-6">
                <form class="form-horizontal" role="form" action="action_page.php">
                    <div class="form-group">
                        <legend>Seleccion Familia y Fabricante que acabas subir listado</legend>
                    </div>
                    <div class="form-group">
                        <?php
                        // Realizamos consulta de Fabricantes
                        $consultaFabricantes = mysqli_query($BDRecambios, "SELECT `id`,`Nombre` FROM `fabricantes_recambios` ORDER BY `Nombre`");
                        // Ahora montamos htmlopciones
                        while ($fila = $consultaFabricantes->fetch_assoc()) {
                            $htmloptiones.='<option value="' . $fila["id"] . '">' . $fila["Nombre"] . '</option>\n';
                        }
                        $consultaFabricantes->close();
                        ?>
                        <label class="control-label col-md-4">Fabricante</label>
                        <select name="fabricante" id="IdFabricante">
                            <option value="0">Seleccione Fabricante</option>
                            <?php echo $htmloptiones; ?>
                        </select>
                    </div>
                    <div class="form-group">
						<?php // Realizamos consulta de Fabricantes
						$consultaFamilias = mysqli_query($BDRecambios,"SELECT `id`,`id_Padre`,`Familia_es` FROM `familias_recambios` ORDER BY `Familia_es`");
                        // Ahora montamos htmlopciones
                        while ($fila = $consultaFamilias->fetch_assoc()) {
							if ($fila["id_Padre"]== 0){
								$familia = $fila["Familia_es"];
								} else {
								$familia = '--> '.$fila["Familia_es"];
                            }
                            $htmlfamilias.='<option value="'.$fila["id"].'">'.$familia.'</option>';
						}
                        $consultaFamilias->close();
                        ?>
                        <label class="control-label col-md-4">Familia a la que quieres añadir</label>
                        <select name="familia" id="IdFamilia">
                            <option value="0">Seleccione Familia</option>
                            <?php echo $htmlfamilias; ?>
                        </select>
                    </div>

                    <div class="form-group align-right">
                        <input id="BtnComprobar" type="button" href="javascript:;" onclick="ComprobarPaso2ListaPrecios($('#IdFabricante').val(), $('#IdFamilia').val());return false;" value="Comprobar"/>
                    </div>
                </form>
                <div id="CjaComprobar" style="display:none;">
                <h3>Resumen de comprobación</h3>
                <p>Total de Registros tabla:<span id="total"></span></p>
                <p>Registros con Estado Vacio: <span id="vacio"></span></p>
                <p>Recambios Nuevos: <span id="nuevos"></span></p>
                <p>Recambios Existentes: <span id="existentes"></span></p>
                </div>
                <div id="Paso3" style="display:none;">
					<div class='form-group align-right'>
						<h2>PASO 3</h2>
						<input id="BtnTerminar" type='button' href='javascript:;' onclick='paso3();return false;' value='terminar'/>
					</div>
                </div>
                <div class="col-md-12">
					<div id="bar" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
						0 % completado
					</div>
                </div>
                <div id="resultado"></div>

            </div>

        </div>

        <script>
			/* =====================  DEFINIMOS VARIABLES GLOBALES DE JAVASCRIPT ===================== */
            // Referencias existentes
            var e = 0;
            // Referencias nuevas
            var n = 0;
            // linea intermedia
            var b = 0;
            // ciclos
            var set;
            // linea final
            var a;
            // id fabricance
            var f;
            // array de ajax
            var rs;
            // id familia
            var fa;
			// Nombre de la tabla 
            var nombretabla = "listaprecios";
            
            
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
               
                var parametros = {
                    'nombretabla': nombretabla,
                    'pulsado': 'contarVacios'
                };
                $.ajax({
                    data: parametros,
                    url: 'funciones.php',
                    type: 'post',
                    datatype: 'json',
                    beforeSend: function () {
                        $("#resultado").html('Comprobando  familias y recambio, tb tiene regisros vacios la tabla lista precios,...<span><img src="./img/ajax-loader.gif"/></span>');
                    },
                    success: function (response) {
						if (response.length == 0) {
                            // Al buscar en contar registros en tabla listaprecios ;
                            // no encuentrar ningún registro con el estado vacio.
                            alert("En BDimportarRecambio la tabla "+ nombretabla + "\n no tiene ningún registro con su estado en vacio \n por lo que no se hace comprobación.");
                            // Contamos registros que tiene la tabla. ( nuevos, existentes y erroneos. )
	
                            resumen(nombretabla);
                            document.getElementById('Paso3').style.display = 'block';

                        } else {
							// Registros vacio.
							a = response.length;
							alert ( "Hemos encontrador"+ a + " vacios");	
                            // cargamos en la variable a el final de linea que es el total de registros del array
                            // iniciamos el ciclo
                            ciclo(response);
                        }

                    }
                });

            }
            
           
			/* =========================  Funcion de consulta  ========================================*/
            // Encontramos que la tabla listaprecios tiene registros con el estado VACIO, entonces 
            // comprobamos en la tabla REFERENCIASCRUZADAS de BD de RECAMBIOS, si existe la referencia
            // 		-Si existe se pone en ESTADO = "existe"
            // 		-NO existe se pone en ESTADO = "nuevo"
            // Estos cambios son el campor ESTADO de la tabla LISTAPRECIOS de BD IMPORTARRECAMBIOS.
            
            function consulta() {
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
                        
                        url: 'funciones.php',
                        type: 'post',
                        datatype: 'json',
                        data: parametros,
                        beforeSend: function () {
                            $("#resultado").html('Cubriendo estado de tabla temporal...<span><img src="./img/ajax-loader.gif"/></span>');
                        },
                        success: function (response) {
                           
                            n = n + (response[0].n);
                            e = e + (response[0].e);
                            
                         
                            
                            $("#vacio").html(response[0].t+"/"+a);
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
            function ciclo(response) {
				rs = response ;
                set = setInterval("consulta()", 500);
            }
            /* =========================  Funcion de resumen  ========================================*/
            // Si en CompravarPaso2Listaprecios NO encuentrar en la tabla listaprecios tiene registros con el estado VACIO, entonces nos trae aquí. 
            // En donde realizamos RESUMEN, es decir comprueba cuantos registros hay y cuantos son nuevo o existentes.
            
            function resumen (nombretabla){
				var nombretabla = "listaprecios";
				var parametros = {
					'nombretabla': nombretabla,
                    'pulsado': 'contar'
                    };
                 
                    $.ajax({
                        
                        url: 'funciones.php',
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
                    url: 'funciones.php',
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
            
            // esta función va a hacer el paso definitivo que es el siguientes:
            // si es nuevo crea el articulo en recambios  a continuación cubre la relación recambio - familia
            // para terminar el proceso de nuevo la relaccion referenciascruzadas
            // existe actualiza el coste
            function anhadirnuevos(rs) {
				console.log('Estamos funcion (anhadirnuevos)');
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
                        url: 'funciones.php',
                        type: 'post',
                        beforeSend: function () {
                           $("#resultado").html("Funcion anahdir ,"+ b +''+a);
                        },
                        success: function (response) {
                            b++;
                            document.getElementById('resultado').innerHTML='Repuesta RefFabriPrin='+ rs[b].ref;
                            console.log('Repuesta anahirNuevo:'+ b);
                            
                            BarraProceso(b, a);
                        }

                    });

                }else{
          clearInterval(set);
          document.getElementById('resultado').innerHTML='Terminado el añadir recambios nuevos y cambio precio existente.';


          b = 0;           
        }
            }
            // JavaSCRIPT para modulo de importar de Catalogo de productos.
            function BarraProceso(lineaA, lineaF) {
                // Script para generar la barra de proceso.
                // Esta barra proceso se crea con el total de lineas y empieza mostrando la lineas
                // que ya estan añadidas.
                // NOTA:
                // lineaActual no puede ser 0 ya genera in error, por lo que debemos sustituirlo por uno
                if (lineaA == 0) {
                    lineaA = 1;
                }
                if (lineaF == 0) {
                    alert('Linea Final es 0 ');
                    return;
                }
                var progreso = Math.round((lineaA * 100) / lineaF);

                $('#bar').css('width', progreso + '%');
                // Añadimos numero linea en resultado.
                document.getElementById("bar").innerHTML = progreso + '%';  // Agrego nueva linea antes 
                return;

            }
            
            
           // lanza el ciclo
            function anhadir(response) {
				// Vamos empezar ciclo, por lo que deactivamos botton de terminar , para que no de otra vez
				document.getElementById("BtnTerminar").disabled = true;

                b = 1;
                console.log('rs'+ rs[b]);
                set = setInterval("anhadirnuevos(rs)", 1000);

            } 
        </script>
    </body>
</html>
