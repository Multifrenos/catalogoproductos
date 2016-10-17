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
        ?>
        <script>
            var lineafinal; // Indica el final ciclo
            var respuesta;
            var lineaIntermedia = 0;
            var ciclodefunciones;
            var fabricanteserror = 0;
            // Se ejecuta cuando termina de carga toda la pagina.
            $(document).ready(function () {

                // Buscamos los campos que tengan menos 2 caracteres para no analizar
                function modifestadofab() {
                    var parametros = {
                        'pulsado': 'BuscarError'

                    };
                    $.ajax({
                        data: parametros,
                        url: 'funciones.php',
                        type: 'post',
                        beforeSend: function () {
                            $("#resultado").html("Buscando campos con solo 2 caracterres, espere por favor...");
                        },
                        success: function (response) {
							$("#resultado").html("Termino de buscar campos con solo 2 caracterres....");
							console.log("Success de modifestado");
                            alert ("Success de modifestado");
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
                        url: 'funciones.php',
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
                            url: 'funciones.php',
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
								$("#fabcru").html(fabricanteserror);

								}
								if (respuesta[lineaIntermedia].Fabr_Recambio =="TOYOT"){
									alert ("Ojo Toyot");
								}
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
                        url: 'funciones.php',
                        type: 'post',
                        datatype: 'json',
                        beforeSend: function () {
                            $("#resultado").html("Realizando resumen fichero importar ReferenciasCruzadas, espere por favor...");
                        },
                        success: function (response) {
                            var vacio = response[0].e;
                            var fab = response[0].f;
                            var validos = response[0].c;
                            console.log("Total lineas" + lineafinal);
                            console.log("Total vacios" + vacio);
                            console.log("Total Fabricante Cruzado No" + fab);
                            console.log("Total Validos" + validos);

                            
                            $("#compFichero span").remove();
                            //var campo="<input type='button' href='javascript:;' onclick='ComprobarPaso2RefCruzadas($('#IdFabricante').val());return false;' value='Comprobar'/>";
                            //$("#compFichero").append(campo);
                            $("#cmp").css("display", "block");
                            $("#total").html(lineafinal);
                            $("#campVa").html(vacio);
                            $("#fabcru").html(fab);
                            $("#validos").html(validos);

                        }

                    });

                }
                ;
                modifestadofab();
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


            });
        </script>

    </head>

    <body>
        <?php
        include './../../header.php';
        ?>
        <div class="container">
            <div class="col-md-12 text-center">
                <h2>Paso 2 - Añadir ReferenciasCruzadas al proveedor seleccionado </h2>
            </div>

            <div class="col-md-6">
                <h3>Resumen de comprobación</h3>
<!--                <p>Numero de Registros analizados: <span id="total"></span></p>-->
                <p>Numero de Registros Error Campo Vacio: <span id="campVa"></span></p>
                <p>Numero de Recambios Error Fabricante Cruzado: <span id="fabcru"></span></p>
                <p>Numero de Recambios A Comprobar Referencia: <span id="validos"></span></p>
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
                    <!--                    <div class="form-group">
                    <?php
                    // Realizamos consulta de Fabricantes
                    $consultaFamilias = mysqli_query($BDRecambios, "SELECT `id`,`Familia_es` FROM `familias_recambios` ORDER BY `Familia_es`");
                    // Ahora montamos htmlopciones
                    while ($fila = $consultaFamilias->fetch_assoc()) {
                        $htmlfamilias .= '<option value="' . $fila["id"] . '">' . $fila["Familia_es"] . '</option>';
                    }
                    $consultaFamilias->close();
                    ?>
                                            <label class="control-label col-md-4">Familia a la que quieres añadir</label>
                                            <select name="familia" id="IdFamilia">
                                                <option value="0">Seleccione Familia</option>
                    <?php echo $htmlfamilias; ?>
                                            </select>
                                        </div>-->

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
            var lineabarra=0;
            var fabricante;
            var finallinea;
            var arrayConsulta;
            var arranqueCiclo;
            var intermedia = 0;
            var lineaIntermedia = 0;

            function finalizar(fabri) {
                fabricante = fabri;
                if (fabricante == 0) {
                    alert("Selecciona un Fabricante");
                } else {
                    ComprobarPaso2RefCruzadas();
                }

            }
            ;

            function ComprobarPaso2RefCruzadas() {
               
                var finallinea = $("#validos").html();
                
                if (fabricante == 0) {
                    alert("Selecciona un Fabricante");
                } else {
                    var parametros = {
                        'pulsado': 'contarVacioscruzados'
                    };
                    $.ajax({
                        data: parametros,
                        url: 'funciones.php',
                        type: 'post',
                        datatype: 'json',
                        beforeSend: function () {

                            $("#fin").html("Procesando, espere por favor...");

                        },
                        success: function (response) {
//                        if (response == null) {
//                            alert("no hay ficheros que modificar");
////                            var campo = "<div class='form-group align-right'><h2>PASO 3</h2><input type='button' href='javascript:;' onclick='paso3();return false;' value='terminar'/></div>"
////                            $("#fin").append(campo);
//                        } else {
//                            // cargamos en la variable a el final de linea qeu es el total de registros del array
//                           
////                            console.log(response.length);
//                            // iniciamos el ciclo
//                           v
//                            grabar();

//                        }
                            if (response.length != 0) {
                                arrayConsulta = response;
                                console.log("respuesta del ajax length "+response.length);
                                grabar();
                                
                            } else {

                            }

                        }
                    });

                }
            }
            ;
//          function ciclo(response){
//           arrayConsulta=response;
//           
//           arranqueCiclo=setInterval('grabar()',2500);
//           };
            function grabar() {

//               if(intermedia < finallinea){
                var parametros = {

                    'pulsado': 'comprobar2cruz',
                    'idrecambio': arrayConsulta[intermedia].id,
                    'linea': arrayConsulta[intermedia].linea,
                    'fabricante': fabricante,
                    'Ref_fa': arrayConsulta[intermedia].Ref_F,
                    'Fab_ref': arrayConsulta[intermedia].F_rec
                };
//                        console.log("********************************");
//                        console.log("que id es "+arrayConsulta[intermedia].id);
//                        console.log("que linea es "+arrayConsulta[intermedia].linea);
//                        console.log("que fabricante es "+fabricante);
//                        console.log("que ref_fa es "+arrayConsulta[intermedia].Ref_F);
//                        console.log("que fab_ref es "+arrayConsulta[intermedia].F_rec);
                $.ajax({
                    data: parametros,
                    url: 'funciones.php',
                    type: 'post',
                    datatype: 'json',
                    beforeSend: function () {
                        $("#resultado").html("<p alert .alert-info>Procesando, espere por favor...</p>");

                    },
                    success: function (response) {

//                        console.log( arrayConsulta[intermedia].id);
//                        console.log( arrayConsulta[intermedia].linea);
//                        console.log( fabricante);
//                        console.log("ref cruzada "+ arrayConsulta[intermedia].Ref_F);
//                        console.log("prof cruzado"+ arrayConsulta[intermedia].F_rec);
                         console.log("****************");
                         console.log(response[0].respuesta);
                        
                        if (intermedia == (arrayConsulta.length)-1) {
                            intermedia=0;
                            ComprobarPaso2RefCruzadas();
                        } else {
                            console.log("esto es la linea intermedia: "+intermedia);
                            
                            intermedia++;
                            lineabarra+=intermedia;
                            BarraProceso2(lineabarra,finallinea);
                            grabar();
                        }
                    }
                });


//                } else {
//                    // cuando acaba el ciclo creamos el campo terminar y cerramos el bucle
//                    var campo = "<div class='form-group align-right'><h2>PASO 3</h2><input type='button' href='javascript:;' onclick='paso3();return false;' value='terminar'/></div>"
//                    $("#fin").append(campo);
//                    clearInterval(arranqueCiclo);
//                    intermedia = 0;
//
//                }

            }
            ;

            function BarraProceso2(lineaA, lineaF) {
                // Script para generar la barra de proceso.
                // Esta barra proceso se crea con el total de lineas y empieza mostrando la lineas
                // que ya estan añadidas.
                // NOTA:
                // lineaActual no puede ser 0 ya genera in error, por lo que debemos sustituirlo por uno
                if (lineaA == 0) {
                    lineaA = 1;
                }
                if (lineaF == 0) {
                    //alert('Linea Final es 0 ');
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

    </body>
</html>
