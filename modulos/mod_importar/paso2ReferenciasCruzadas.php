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
<!--        <script src="<?php echo $HostNombre; ?>/modulos/mod_importar/comprobar.js"></script>-->
        <script>
            var lineafinal;
            var respuesta;
            var lineaIntermedia = 0;
            var ciclodefunciones;
            $(document).ready(function () {

                function modifestadofab() {
                    var parametros = {
                        'pulsado': 'BuscarError'

                    };
                    $.ajax({
                        data: parametros,
                        url: 'funciones.php',
                        type: 'post',
                        beforeSend: function () {
                            $("#resultado").html("Procesando, espere por favor...");
                        },
                        success: function (response) {
                            buscProvee();


                        }

                    });
                };
                
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
                            $("#resultado").html("Procesando, espere por favor...");
                        },
                        success: function (response) {
                            // cubrimos la linea final y lanzamos el ciclo
                            lineafinal = response.length;
                            ciclofabricante(response);
                        }
                    });
                };
                

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
                                $("#resultado").html("Procesando, espere por favor...");
                            },
                            success: function (response) {
                               BarraProceso(lineaIntermedia, lineafinal);
                                    lineaIntermedia++;

                            }

                        });

                    } else {
                        clearInterval(ciclodefunciones);
                        lineaIntermedia = 0;
                        resumenresul();
                        
                    }

                };
                
                function ciclofabricante(response) {
                    respuesta = response;
              
                 ciclodefunciones = setInterval(fabricexist, 500);
                   
                };
                 function resumenresul(){
                    var parametros = {
                            'pulsado': 'resumen'
                        };
                        $.ajax({
                            data: parametros,
                            url: 'funciones.php',
                            type: 'post',
                            datatype: 'json',
                            beforeSend: function () {
                                $("#resultado").html("Procesando, espere por favor...");
                            },
                            success: function (response) {
                                var vacio=response[0].e;
                                var fab=response[0].f;
                                var validos=response[0].c;
                                $("#compFichero span").remove();
                                //var campo="<input type='button' href='javascript:;' onclick='ComprobarPaso2RefCruzadas($('#IdFabricante').val());return false;' value='Comprobar'/>";
                                //$("#compFichero").append(campo);
                                $("#cmp").css("display","block");
                                $("#total").html(lineafinal);
                                $("#campVa").html(vacio);
                                $("#fabcru").html(fab);
                                $("#validos").html(validos);
                                   
                            }

                        });

                };
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
//                if (lineaF == 0) {
//                    //alert('Linea Final es 0 ');
//                    return;
//                }
                var progreso = Math.round((lineaA * 100) / lineaF);

                $('#bar').css('width', progreso + '%');
                // Añadimos numero linea en resultado.
                document.getElementById("bar").innerHTML = progreso + '%';  // Agrego nueva linea antes 
                return;

            };
            
           
            });
        </script>

    </head>

    <body>
        <?php
        include './../../header.php';
        ?>
        <div class="container">
            <div class="col-md-12 text-center">
                <h2>Paso 2 - ReferenciasCruzadas: Seleccionar Familia ,Fabricante y buscar referencia </h2>
            </div>

            <div class="col-md-6">
                <form class="form-horizontal" role="form" action="action_page.php">
                    <div class="form-group">
                        <legend>Seleccion  Fabricante que acabas subir listado</legend>
                    </div>
                    <div class="form-group">
                        <?php
                        // Realizamos consulta de Fabricantes
                        $consultaFabricantes = mysqli_query($BDRecambios, "SELECT `id`,`Nombre` FROM `FabricantesRecambios` ORDER BY `Nombre`");
                        // Ahora montamos htmlopciones
                        while ($fila = $consultaFabricantes->fetch_assoc()) {
                            $htmloptiones.='<option value="' . $fila["id"] . '">' . $fila["Nombre"] . '</option>';
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
                    $consultaFamilias = mysqli_query($BDRecambios, "SELECT `id`,`Familia_es` FROM `FamiliasRecambios` ORDER BY `Familia_es`");
                    // Ahora montamos htmlopciones
                    while ($fila = $consultaFamilias->fetch_assoc()) {
                        $htmlfamilias.='<option value="' . $fila["id"] . '">' . $fila["Familia_es"] . '</option>';
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
                            <input type="button" href="javascript:;" onclick="ComprobarPaso2RefCruzadas($('#IdFabricante').val());return false;" value="Comprobar" id="cmp" style="display: none;"/>
                        <span class="alert alert-success">Analizando Errores del fichero. Esto puede tardar unos minutos .....</span>
                        </div>
                        <br/><br/>
                        <div class="col-md-4">
                        <div id="bar" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                    0 % completado
                    <!--
                                                                    <span id="spanProceso" class="sr-only">0% Complete</span>
                    -->
                </div>
                        </div>
                    </div>
                </form>
                <h3>Resumen de comprobación</h3>
<!--                <p>Numero de Registros analizados: <span id="total"></span></p>-->
                <p>Numero de Registros Error Campo Vacio: <span id="campVa"></span></p>
                <p>Numero de Recambios Error Fabricante Cruzado: <span id="fabcru"></span></p>
                <p>Numero de Recambios A Comprobar Referencia: <span id="validos"></span></p>
                <div id="fin"></div>
                <div id="bar2" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                    0 % completado
                    <!--
                                                                    <span id="spanProceso" class="sr-only">0% Complete</span>
                    -->
                </div>
            </div>

        </div>
        <script>
            var fabricante;
            var finallinea;
            var arrayConsulta;
            var arranqueCiclo;
            var intermedia = 0;
            
            function ComprobarPaso2RefCruzadas(fabri){
               fabricante=fabri;
               
              if(fabricante == 0){
                  alert("Selecciona un Proveedor")
              }else{
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
                        if (response == null) {
                            alert("no hay ficheros que modificar");
                            var campo = "<div class='form-group align-right'><h2>PASO 3</h2><input type='button' href='javascript:;' onclick='paso3();return false;' value='terminar'/></div>"
                            $("#fin").append(campo);
                        } else {
                            // cargamos en la variable a el final de linea qeu es el total de registros del array
                            finallinea = response.length;
                            console.log(response.length);
                            // iniciamos el ciclo
                            ciclo(response, fabricante);
                        }

                    }
                });
               
               }
           };
          function ciclo(response){
           arrayConsulta=response;
           
           arranqueCiclo=setInterval('grabar()',2500);
           };
           function grabar(){
               if(intermedia < finallinea){
                   var parametros = {
                        
                        'pulsado': 'comprobar2cruz',
                        'idrecambio': arrayConsulta[intermedia].id,
                        'linea': arrayConsulta[intermedia].linea,
                        'fabricante': fabricante
                    };
                    console.log("REferencia "+arrayConsulta[intermedia].id);
                    console.log("Linea "+arrayConsulta[intermedia].linea);
                    console.log("fabricante "+fabricante);
                    $.ajax({
                        data: parametros,
                        url: 'funciones.php',
                        type: 'post',
                        datatype: 'json',
                        beforeSend: function () {
                            $("#resultado").html("<p alert .alert-info>Procesando, espere por favor...</p>");
                           
                        },
                        success: function (response) {

                           
                           
                           console.log("linea intermedia "+intermedia);
                           console.log("linea final"+finallinea);
//                           console.log(response[0].t);
                            intermedia++;
                            BarraProceso2(intermedia, finallinea);
                              
                        }
                    });


                } else {
                    // cuando acaba el ciclo creamos el campo terminar y cerramos el bucle
                    var campo = "<div class='form-group align-right'><h2>PASO 3</h2><input type='button' href='javascript:;' onclick='paso3();return false;' value='terminar'/></div>"
                    $("#fin").append(campo);
                    clearInterval(arranqueCiclo);
                    intermedia = 0;

                }
                    
        };
        
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
                document.getElementById("bar2").innerHTML = progreso + '%';  // Agrego nueva linea antes 
                return;

            };
            </script>
        
    </body>
</html>
