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
                    </div>

                    <div class="form-group align-right">
                        <input id="BtnComprobar" type="button" href="javascript:;" onclick="ComprobarPaso2ListaPrecios($('#IdFabricante').val(), $('#IdFamilia').val());return false;" value="Comprobar"/>
                    </div>
                </form>
                <div id="CjaComprobar" style="display:none;">
                <h3>Resumen de comprobación</h3>
                <p>Total de Registros tabla:<span id="total"></span></p>
                <p>Registros con Estado Vacio: <span id="total"></span></p>
                <p>Recambios Nuevos: <span id="nuevos"></span></p>
                <p>Recambios Existentes: <span id="existentes"></span></p>
                </div>
                <div id="Paso3" style="display:none;">
					<div class='form-group align-right'>
						<h2>PASO 3</h2>
						<input type='button' href='javascript:;' onclick='paso3();return false;' value='terminar'/>
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
            // Llegamos a la funcion ComprobarPaso2ListaPrecios --> Al pulsar en comprobar .
            // Lo que hacemos es comprobar cuantos registros tiene y cuantos tienes el estado vacio.
            // Si hay vacios , entonces iniciamos ciclo, sino solo presentamos resumen.
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
                        $("#resultado").html("Buscando en table lista precios, espere por favor...");
                    },
                    success: function (response) {
                        if (response == null) {
                            // Al buscar en contar registros en tabla listaprecios ;
                            // no encuentrar ningún registro con el estado vacio.
                            alert("En BDimportarRecambio la tabla "+ nombretabla + "\n no tiene ningún registro con su estado en vacio \n por lo que no se hace comprobación.");
                            document.getElementById('Paso3').style.display = 'block';

                        } else {
                            // cargamos en la variable a el final de linea que es el total de registros del array
                            a = response.length;
                            // iniciamos el ciclo
                            ciclo(response);
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
                            $("#resultado").html('Comprobando estado de tabla temporal...<span><img src="./img/ajax-loader.gif"/></span>');
                        },
                        success: function (response) {
                           
                            n = n + (response[0].n);
                            e = e + (response[0].e);
                            
                         
                            
                            $("#total").html(response[0].t+"/"+a);
                            $("#nuevos").html(n);
                            $("#existentes").html(e);
                            b++;

                        }
                    });


                } else {
                    // cuando acaba el ciclo creamos el campo terminar y cerramos el bucle
                    document.getElementById('Paso3').style.display = 'block';

                    clearInterval(set);
                    b = 0;

                }

            }
            // Empezamos el ciclo de comprobar si es nuevo, existe o tiene un error
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
                        $("#resultado").html("Iniciamos ciclo de proceso, espere por favor...");
                    },
                    success: function (response) {
                        // cubrimos la linea final y lanzamos el ciclo
                        a = response.length;
                        console.log(a);
                        $("#resultado").html("Numero filas que devuelve verNuevasRef ="+ a);
						alert( "Inicio de ciclo enterminar "+a);
                        anhadir(response);

                    }
                });

            }
            // lanza el ciclo
            function anhadir(response) {
                rs = response;
                set = setInterval("anhadirnuevos()", 1000);

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
                            document.getElementById('resultado').innerHTML='Repuesta Id='+ rs[b].id;

                            console.log(b);
                            
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
