<?php 
/*  Este fichero los utilizamos para :
 *    Comprobar el tabla Referencias Cruzadas
 * 		1.- Si los campos Ref_Fabricantes y Fabricante tiene mas 2 caracteres sino ESTADO= ERR:[CampoVacio]
 * 
 * */ 
?>

<!DOCTYPE html>
<html>
    <head>
        <?php
// Reinicio variables
        $htmloptiones = '';
        $htmlfamilias = '';
        include './../../head.php';
        include ("./../mod_conexion/conexionBaseDatos.php");
        include ("./Consultas.php");
        $consultaRegistros = new ConsultaImportar;
		$tabla ="referenciascruzadas";
		$whereC = ""; 
		$totalRegistro = $consultaRegistros->contarRegistro($BDImportRecambios,$tabla,$whereC);
        ?>
        <script>
            var lineafinal; // Indica el final ciclo primero de fabricantes...
            var respuesta;
            var lineaIntermedia = 0;
            var ciclodefunciones;
            var fabricanteserror = 0;
            // Se ejecuta cuando termina de carga toda la pagina.
            $(document).ready(function () {
			    BarraProceso(); // Para se defina..
                // Buscamos los campos que tengan menos 2 caracteres para no analizar
                function modifestadofab() {
					
                    var parametros = {
                        'pulsado': 'BuscarError'

                    };
                    $.ajax({
                        data: parametros,
                        url: 'tareas.php',
                        type: 'post',
                        beforeSend: function () {
                            $("#resultado").html("Buscando campos con solo 2 caracterres, espere por favor...");
                        },
                        success: function (response) {
							console.log(response.toSource()); // Cuando recibimos un objeto, lo vemos asi..
							if ( response.conexion == 'correcto'){
							console.log('Numero Items:'+ response.NItems);
							console.log('Numero Items Error Campo 2:' + response.Menos2C);
							$("#total").html(response.NItems);
                            $("#campVa").html(response.Menos2C);
							
							} 
							
							$("#resultado").html("Termino de buscar campos con solo 2 caracterres....");
							console.log("Success de modifestado");
                            buscProvee();


                        }

                    });
                }
                ;

                function buscProvee() {
                    var nombretabla = "referenciascruzadas";
                    var parametros = {
                        'nombretabla': nombretabla,
                        'pulsado': 'BuscarErrorFab'

                    };

                    $.ajax({
                        data: parametros,
                        url: 'tareas.php',
                        type: 'post',
                        datatype: 'json',
                        beforeSend: function () {
                            $("#resultado").html("Creamos array con los distintos fabricantes que hay importacion, espere por favor...");
                        },
                        success: function (response) {
                            // cubrimos la linea final y lanzamos el ciclo
                            $("#resultado").html("Terminado de crear Array con los distintos fabricantes y lanzamos ciclo ...");
                            lineafinal = response.length;
							console.log("Fabricantes encontrados en importacion:" + lineafinal);
                          
                            ciclofabricante(response);
                        }
                    });
                }
                ;


                function fabricexist() {
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
                                console.log ( "LineaFinal:" + lineafinal);
                                console.log ( "LineaIntermedia:" + lineaIntermedia);
                                console.log ("Repuesta:"+ response);
                                if (response == 'No'){
								fabricanteserror = fabricanteserror + 1;
								console.log ( "Fabricantes con error:" + fabricanteserror);	
								$("#fabcruDes").html(fabricanteserror);
								}
								$("#fabcru").html(lineaIntermedia); // Indicamos fabricantes analizados.

								lineaIntermedia++;
                                $("#resultado").html("Resultado de "+ respuesta[lineaIntermedia].Fabr_Recambio );
                                BarraProceso(lineaIntermedia, lineafinal);
                            }

                        });

                    } else {
                        clearInterval(ciclodefunciones);
                        lineaIntermedia = 0;
                        resumenresul();

                    }

                }
                ;

                function ciclofabricante(response) {
                    respuesta = response;

                    ciclodefunciones = setInterval(fabricexist, 500);

                }
                ;
                function resumenresul() {
                    var parametros = {
                        'pulsado': 'resumen'
                    };
                    $.ajax({
                        data: parametros,
                        url: 'tareas.php',
                        type: 'post',
                        datatype: 'json',
                        beforeSend: function () {
                            $("#resultado").html("Realizando resumen fichero importar ReferenciasCruzadas, espere por favor...");
                        },
                        success: function (response) {
                            var vacio = response[0].e;// Registros que tiene error campo ( 2 caracteres)
                            var fab = response[0].f; // Registros de fabricantes cruzados no correctos
                            var validos = response[0].c; // Registros que vamos a comprobar.
                            $("#compFichero span").remove();
                            $("#cmp").css("display", "block");
                            $("#fabcru").html(lineafinal); // Total fabricantes 
                            $("#campVa").html(vacio);
                            $("#Rfabcru").html(fab);
                            $("#RegBlanco").html(validos);
							$("#resultado").html("COMPLETADO PASO2 (REFERENCIAS CRUZADAS). Selecciona fabricante principal y seguir comprobando...");
                        }

                    });

                };
                modifestadofab();
                


            });
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
                ;
        </script>

    </head>

    <body>
        <?php
        include './../../header.php';
        ?>
        <div class="container">
            <div class="col-md-12 text-center">
                <h2>Paso 2 - Añadir ReferenciasCruzadas al proveedor seleccionado </h2>
                <p> Ahora tenemos una tabla temporal con las referencias cruzadas que acabas de subir, donde solo se controlo que las lineas del fichero .csv contenga los campos.</p>
            </div>

            <div class="col-md-8">
                <h3>Resumen de comprobación de fichero</h3>
<!--                <p>Numero de Registros analizados: <span id="total"></span></p>-->
                 <table class="table table-striped">
					<thead>
					  <tr>
						<th>COMPROBANDO</th>
						<th>Registros</th>
						<th>CAMPO ESTADO</th>
						<th>Otros Datos</th>
					  </tr>
					</thead>
					<tbody>
					  <tr>
						<th>Campos con menos 2 caracteres</th>
						<td>Registro de tabla:<strong><?php echo $totalRegistro;?></strong><br/>
						Registros con ESTADO en blanco:
						<strong><span id="total"></span></strong></td>
						<td>ERR:[CampoVacio]</td>
						<td>Registros campos mal:<strong><span id="campVa"></span></strong></td>
					  </tr>
					  <tr>
						<th>Fabricantes cruzados que no existen</th>
						<td>Analizando FAB de tabla de importacion:<strong><span id="fabcru"></span></strong><br/>Registros descartados:<strong><span id="Rfabcru"></span></strong></td>
						<td>ERR:[FABRICANTE cruzado no existe]</td>
						<td>De los Fabricantes analizados se descartan:<strong><span id="fabcruDes"></span></strong></td>

					  </tr>
					  <tr>
						<th>PASO 3: Registros Estado Blanco</th>
						<td>Registros a procesar:<strong><span id="RegBlanco"></strong></span></td>
						<td></td>
						<td> </td>
					  </tr>
					</tbody>
				  </table>
                
               
                <div id="bar" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                    0 % completado
                    <!--
					<span id="spanProceso" class="sr-only">0% Complete</span>
                    -->
                </div>
                <hr />
                <div id="resultado" class="col-md-12">
                <!-- Aquí mostramos respuestas de AJAX -->
                </div>
                <form class="form-horizontal" role="form" action="action_page.php">
                    <div class="form-group">
                        <legend>Seleccion  Fabricante que acabas subir listado</legend>
                    </div>
                    <div class="form-group">
                        <?php
                        // Realizamos consulta de Fabricantes
                        $consultaFabricantes = mysqli_query($BDRecambios, "SELECT `id`,`Nombre` FROM `fabricantes_recambios` ORDER BY `Nombre`");
                        // Ahora montamos htmlopciones
                        while ($fila = $consultaFabricantes->fetch_assoc()) {
                            $htmloptiones .= '<option value="' . $fila["id"] . '">' . $fila["Nombre"] . '</option>';
                        }
                        $consultaFabricantes->close();
                        ?>
                        <label class="control-label col-md-4">Fabricante</label>
                        <select name="fabricante" id="IdFabricante">
                            <option value="0">Seleccione Fabricante</option>
                            <?php echo $htmloptiones; ?>
                        </select>
                    </div>
                  

                    <div class="form-group align-right">
                        <div  id="compFichero">
                            <input type="button" href="javascript:;" onclick="finalizar($('#IdFabricante').val());return false;" value="Comprobar" id="cmp" style="display: none;"/>
                            <span class="alert alert-success">Analizando Errores del fichero. Esto puede tardar unos minutos .....</span>
                        </div>
                        
                        <br/><br/>
                        <div class="col-md-4">

                        </div>
                    </div>
                </form>

                <div id="fin"></div>

            </div>

        </div>
        <script>
			// Funciones que se ejecutan despues de seleccionar comprobar
			// Se obtiene registros que su estado="" de tabla referenciascruzadas de importar
			// para:
			// 		1.- Buscar si existe la referencia_principal
			// 			[NO EXISTE] Entonces añadimos a  campo estado como ERROR[Referencia Principal]
			//			[SI EXISTE] Entonces buscamos la referencia cruzada si existe o no , ojo siempre
			//		comparandolo con el fabricante.
            var lineabarra=0;
            var fabricante;
            var finallinea; // La utilizamos en barra
            var arrayConsulta;
            //~ var arranqueCiclo;
            var intermedia = 0;
            var lineaIntermedia = 0;

            function finalizar(fabri) {
                fabricante = fabri;
                if (fabricante == 0) {
                    alert("Selecciona un Fabricante");
                } else {
					// Informamos que ahora si va añadir y que no hay vuelta atrás.
					var respuestaConf = confirm('Vamos empezar añadir registros a BDRecambios,\n\ ahora si que no hay vuelta atrás, por lo que es conveniente tener una copia de seguridad de BDRecambios\n\ Estas seguro');
					if (respuestaConf == true) {
						ComprobarPaso2RefCruzadas();
					}
				}

            };

            function ComprobarPaso2RefCruzadas() {
				// En esta funcion obtenemos los 400 registros primeros que tengan ESTADO=""
                // Tambien ten encuenta que a esta funcion se llama desde:
                // 		funcion finalizar-> Cuando pulsamos btn comprobar...
                //		function grabar() --> Ajax response si ya leyo los 400 registros....
				
				// Obtenemos de tabla BDImportar-Registroscruzados los que tenga el estado ""
                finallinea = $("#RegBlanco").html();
                
                if (fabricante == 0) {
                    alert("Selecciona un Fabricante");
                } else {
                    var parametros = {
                        'pulsado': 'contarVacioscruzados'
                    };
                    $.ajax({
                        data: parametros,
                        url: 'tareas.php',
                        type: 'post',
                        datatype: 'json',
                        beforeSend: function () {
							console.log ('Obtenemos array con resgistros que tiene estado en blanco');
                            $("#fin").html("Obteniendo registros con ESTADO en BLANCO, espere por favor...");

                        },
                        success: function (response) {

                            if (response.length != 0) {
                                // Creamos array con los datos $BDImportar-referenciascruzadas
                                // Por ejemplo:
                                // [{	id:"A110049",
                                //	 	linea:"1413", 
								//		F_rec:"QUINTON HA",
								// 		Ref_F:"WF8232"}
								// y así con 399 registros mas... 
                                arrayConsulta = response;
                                console.log("************ RESPUESTA AJAX FUNCION ComprobarPaso2RefCruzadas *********")
                                console.log("Numero registros obtenidos "+response.length);
                                $("#fin").html("Hemos obtenido "+ response.length + " con ESTADO en BLANCO,Llevamos " + lineabarra +" de " + finallinea +" procesando...");
                                
                                grabar();
                                
                            } else {
								 $("#fin").html("Hemos terminado ya que hay" +  response.length + " con estado en blanco.");
								 alert ( "Terminamos");
                            }

                        }
                    });

                } // Fin else fabricante no es 0
            };

            function grabar() {

               var parametros = {

                    'pulsado': 'comprobar2cruz',
                    'idrecambio': arrayConsulta[intermedia].id,
                    'linea': arrayConsulta[intermedia].linea,
                    'fabricante': fabricante,
                    'Ref_fa': arrayConsulta[intermedia].Ref_F,
                    'Fab_ref': arrayConsulta[intermedia].F_rec
                };
                        console.log("******* FUNCION GRABAR ENVIAMOS AJAX *********************");
                        console.log("que id es "+arrayConsulta[intermedia].id);
                        console.log("que linea es "+arrayConsulta[intermedia].linea);
                        console.log("que fabricante es "+fabricante);
                        console.log("que ref_fa es "+arrayConsulta[intermedia].Ref_F);
                        console.log("que fab_ref es "+arrayConsulta[intermedia].F_rec);
                $.ajax({
                    data: parametros,
                    url: 'tareas.php',
                    type: 'post',
                    datatype: 'json',
                    beforeSend: function () {
                        textoMostrar = "Grabar()- Comprobando Referencia:"+arrayConsulta[intermedia].Ref_F;
                        textoMostrar = textoMostrar + "\n Fabricantes cruzado es:" + arrayConsulta[intermedia].F_rec;
                        $("#resultado").html(textoMostrar);

                    },
                    success: function (response) {
						console.log("******* RESPUESTA AJAX DE GRABAR *************************");
                        //~ console.log( arrayConsulta[intermedia].id);
                        //~ console.log( arrayConsulta[intermedia].linea);
                        //~ console.log( fabricante);
                        //~ console.log("ref cruzada "+ arrayConsulta[intermedia].Ref_F);
                        //~ console.log("prof cruzado"+ arrayConsulta[intermedia].F_rec);
                        //~ console.log("****************");
                        
                        console.log("Respuesta:" + response[0].respuesta);
                        console.log("Busqueda:" + response[0].busqueda);

                        // Este if es el que hace ciclo.. es decir
                        // Vuelve ejecutar la misma funcion mientras la 
                        // variable global var intermedia no llege al final de arrayConsulta.length
                        if (intermedia == (arrayConsulta.length)-1) {
                            intermedia=0;
							console.log("Obtener nuevamente array en ComprobarPaso2RefCruzadas");
                            ComprobarPaso2RefCruzadas();
                        } else {
							console.log("Continuamos con ciclo de grabar..");
							intermedia++;
                            lineabarra++;
                            BarraProceso(lineabarra,finallinea);
                            
                            console.log("Numero que vamos en este ciclo: "+intermedia);
                            console.log("Maximo ciclo: "+arrayConsulta.length);
							console.log("linea final de barra proceso: "+finallinea);
							console.log("linea actual de barra proceso: "+lineabarra);

                            grabar();
                        }
                    }
                });
            };

           
        </script>

    </body>
</html>
