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
                    <div class="form-group">
				<?php // Realizamos consulta de Fabricantes
				$consultaFamilias = mysqli_query($BDRecambios,"SELECT `id`,`id_Padre`,`Familia_es` FROM `FamiliasRecambios` ORDER BY `Familia_es`");
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
				<input type="button" href="javascript:;" onclick="ComprobarPaso2ListaPrecios($('#LineaInicial').val(), $('#LineaFinal').val());return false;" value="Comprobar"/>
                    </div>
                </form>
                <h3>Resumen de comprobación</h3>
                <p>Numero de Registros analizados: <span id="total"></span></p>
                <p>Numero de Recambios Nuevos: <span id="nuevos"></span></p>
                <p>Numero de Recambios Existentes: <span id="existentes"></span></p>
                <div id="fin"></div>
                <div id="bar" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                    0 % completado
                    <!--
                                                                    <span id="spanProceso" class="sr-only">0% Complete</span>
                    -->
                </div>
            </div>

        </div>

        <script>
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
            // En este proceso buscamos en la tabla lista precios los que su estado sea vacio 
            // para el bucle no hacer busquedas ineccesarias
            function ComprobarPaso2ListaPrecios(fabricante, familia) {
                fa = familia;
                f = fabricante;
                var nombretabla = "listaprecios";
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
                        $("#resultado").html("Procesando, espere por favor...");
                    },
                    success: function (response) {
                        if (response == null) {
                            alert("no hay ficheros que modificar");
                            var campo = "<div class='form-group align-right'><h2>PASO 3</h2><input type='button' href='javascript:;' onclick='paso3();return false;' value='terminar'/></div>"
                            $("#fin").append(campo);
                        } else {
                            // cargamos en la variable a el final de linea qeu es el total de registros del array
                            a = response.length;
                            // iniciamos el ciclo
                            ciclo(response, fabricante);
                        }

                    }
                });

            }
            // función que va a comprobar si existe en la tabla referenciaz Cruzadas si existe El estado
            // en lista de precios ponemos existe y añadimos el id del recambio
            // si es nuevo añadimos nuevo en el estado de lista de precios
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
                            $("#resultado").html("Procesando, espere por favor...");
                        },
                        success: function (response) {

                            n = n + (response[0].n);
                            e = e + (response[0].e);
                            console.log("nuevo "+n );
                            console.log("existe "+e);
                            console.log("temporal "+b);
                            console.log("id "+rs[b].id);
                            console.log("linea "+rs[b].linea);
                            $("#total").html(response[0].t+"/"+a);
                            $("#nuevos").html(n);
                            $("#existentes").html(e);
                            b++;

                        }
                    });


                } else {
                    // cuando acaba el ciclo creamos el campo terminar y cerramos el bucle
                    var campo = "<div class='form-group align-right'><h2>PASO 3</h2><input type='button' href='javascript:;' onclick='paso3();return false;' value='terminar'/></div>"
                    $("#fin").append(campo);
                    clearInterval(set);
                    b = 0;

                }

            }
            // va a lanzar la el bucle de buscar en cruzadas
            function ciclo(response) {
                rs = response;
                set = setInterval("consulta()", 500);
            }
            
            
            
            // buscamos el total de referencias que vamos a lanzar el bucle
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
                        $("#resultado").html("Procesando, espere por favor...");
                    },
                    success: function (response) {
                        // cubrimos la linea final y lanzamos el ciclo
                        a = response.length;
                        anhadir(response);

                    }
                });

            }
            // lanza el ciclo
            function anhadir(response) {
                rs = response;
                set = setInterval("anhadirnuevos()", 500);

            }
            // esta función va a hacer el paso definitivo que es el siguientes:
            // si es nuevo crea el articulo en recambios  a continuación cubre la relación recambio - familia
            // para terminar el proceso de nuevo la relaccion referenciascruzadas
            // existe actualiza el coste
            function anhadirnuevos() {

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
                    $.ajax({
                        data: parametros,
                        url: 'funciones.php',
                        type: 'post',
                        beforeSend: function () {
                            $("#resultado").html("Procesando, espere por favor...");
                        },
                        success: function (response) {
                           
                            b++;
                            BarraProceso(b, a);
                        }

                    });

                }else{
          clearInterval(set);
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
        </script>
    </body>
</html>
