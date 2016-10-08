<!DOCTYPE html>
<html>
    <head>
        <?php
        include './../../head.php';
        ?>
        <script src="<?php echo $HostNombre; ?>/modulos/mod_importar/importar.js"></script>
    </head>
    <body>
        <?php
        include './../../header.php';
        // Inicio de variables
			$ficherosposibles = array("ReferenciasCruzadas.csv","ReferenciasCversionesCoches.csv","ListaPrecios.csv");
			//~ $dir_subida = '/tmp/'; // Lugar donde el servidor indica que guarda los tmp
			$ficheroNombre= $_FILES['fichero_usuario']['name'];

        //~ $fichero_subido = $dir_subida . basename($_FILES['fichero_usuario']['name']);
			$fichero_subido = $ConfDir_subida . $ficheroNombre;

        $errorFichero = '';
        $correcto = '';
        ?>
        <div class="container">
            <div class="col-md-12 text-center">
                <h2>Paso 1 : Añadir datos del fichero csv a BD temporal </h2>
            </div>
            <?php
            // Si ya enviamos nombre fichero por URL quiere decir:
            //   - Que ya habíamos subido con anterioridad el fichero
            //   - Queremos continuar con el proceso pero en otros registros.

            if (isset($_GET["fichero"])) {
                $ficheroNombre = $_GET["fichero"] . '.csv';
                // Ahora comprobamos si existe el fichero en cuestión en /tmp
			if (file_exists ($ConfDir_subida.$ficheroNombre)){
                    echo '<div class="alert alert-info">';
                    echo 'No se subió fichero se salto ese paso , el fichero que vamos analizar es:' . $ficheroNombre;
                    echo '</div>';
                } else {
				$errorFichero= "- No se subio fichero y tampoco hemos encontrado el fichero en directori ".$ConfDir_subida.$ficheroNombre."<br/>";
                }
            } else {
                // Caso contrario es que no se paso nombre fichero por url
                // Movemos fichero recien subido a tmp
                if (move_uploaded_file($_FILES['fichero_usuario']['tmp_name'], $fichero_subido)) {
                    $correcto = " - El fichero acaba subir y existe en directorio temporal.<br/>";
                } else {
                    // No debería haber llegado nunca aquí, hay error.
                    $errorFichero = $errorFichero . "- Hubo un error en la carga del fichero.<br/>Linea 45 de fichero recibircsv.php mod_importar<br/>AVISAR SERVICIO TECNICO<br/>";
                }
            }
            if (!in_array($ficheroNombre, $ficherosposibles)) {
                // Comprobamos que el nombre del fichero es correcto, para evitar errores, aunque esto 
                // implica que el usuario tiene que poner los nombres correcto al fichero que suba.
                // es una forma de evitar problemas.
                if ($_GET["subida"] == 0) {
                    $errorFichero = $errorFichero . "- Fichero " . $ficheroNombre . " no es un nombre de fichero correcto.<br/>Los nombre de ficheros que puede utilizar son:<br/>-" . implode("<br/>-", $ficherosposibles);
                }
            }

            // Este error hacemos que no continue comprobando que salga.
            if ($errorFichero != '') {
                ?>
                <div class="alert alert-danger">
                    <strong>ERRORES <br/></strong>
                    <?php echo $errorFichero; ?>
                </div>	
            </div> <!-- Cerramos div container ya que no continuamos -->
        </body>
    </html>
    <?php
    return;
}
include ("./../mod_conexion/conexionBaseDatos.php");
if ($ficheroNombre == "ReferenciasCruzadas.csv") {
    $NumeroCamposCsv = 3;
    $CamposSinCubrir = "0','0";
    $nombretabla = "referenciascruzadas";
}
if ($ficheroNombre == "ReferenciasCversionesCoches.csv") {
    $NumeroCamposCsv = 3;
    $nombretabla = "referenciasCversiones";
}
if ($ficheroNombre == "ListaPrecios.csv") {
    $NumeroCamposCsv = 3;
    $nombretabla = "listaprecios";
    $CamposSinCubrir = "0";
}
$consulta = "SELECT count(linea) as cuenta FROM " . $nombretabla;
$consultaContador = mysqli_query($BDImportRecambios, $consulta);
if($consultaContador == true){
$contador = $consultaContador->fetch_assoc();
}
mysqli_close($BDImportRecambios);
if ($contador['cuenta'] == '0') {
    $correcto.= "- Tabla temporal sin reguistros <br/>";
} else {
    $errorFichero.= "- La tabla temporal contiene " . $contador['cuenta'] . " registros <br/>"
            . "- Al pulsar importar se borraran los reguistros de la tabla";
}
?>



<div class="col-md-6">
    <?php
// Comprobamos si existe el fichero
			if (file_exists ($ConfDir_subida.$ficheroNombre)){
        $correcto = $correcto . " - El fichero encontrado.<br/>";
        //abro el archivo para lectura
        $rutafichero = $ConfDir_subida . $ficheroNombre;
        $archivo = fopen($rutafichero, 'r');

        //inicializo una variable para llevar la cuenta de las líneas y los caracteres
        $num_lineas = 0;
        //Hago un bucle para recorrer el archivo línea a línea hasta el final del archivo
        // Mostramos las primeras 10 lineas registro si las hay claro..	
    } else {
        $errorFichero = $errorFichero . "- No encuentro el fichero.<br/>";
    }


//Hago un bucle para recorrer el archivo línea a línea hasta el final del archivo
// Mostramos las primeras 10 lineas registro si las hay claro..
    ?>
    <h4>Las primeras lineas de <?php echo $ficheroNombre; ?></h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Linea</th>
                <th>Contenido</th>
            </tr>
        </thead>
        <?php
				if (file_exists ($ConfDir_subida.$ficheroNombre)){
            // Solo se ejecuta si existe el fichero.
            while (!feof($archivo)) {
                //si extraigo una línea del archivo y no es false
                if ($lineactual = fgets($archivo)) {
                    // El contador empieza en 0
                    if ($num_lineas < 10) {
                        ?>
                        <tr>
                            <td> <?php echo $num_lineas; ?>
                            </td>
                            <td>
                                <?php echo $lineactual; ?>
                            </td>
                        </tr>
                        <?php
                    }
                    //acumulo una en la variable número de líneas
                    $num_lineas++;
                }
            } // Fin de bucle.
        }
        ?>
    </table>
    <?php
    fclose($archivo);
    // Añadimos numero de lineas a variables de control.
    // Si solo hay un registro, o ninguno lo ponemos como error
    if ($num_lineas < 2) {
        $errorFichero = $errorFichero . '- No tiene registros suficiente para procesar,' . $num_lineas . '<br/>';
    } else {
        $correcto = $correcto . '- Numero de registros a procesar son ' . $num_lineas . '<br/>';
    }


    // Ahora imprimimos resultado control de fichero
    ?>
</div>
<div class="col-md-6">
    <h4>Comprobamos si el fichero es correcto</h4>
    <div class="alert alert-info">
        <strong>COMPROBACIONES BÁSICAS CORRECTAS <br/></strong>
        <?php echo $correcto; ?>

    </div>

    <?php
    if ($errorFichero != '') {
        ?>
        <div class="alert alert-danger">
            <strong>ERRORES <br/></strong>
            <?php echo $errorFichero; ?>
        </div>	
        <?php
    }
    ?>
    <div>
        <form class="form-horizontal" role="form" >
            <div class="form-group">
                <legend>¿Desde que línea quiere importar?</legend>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Línea Inicial</label>
                <input class="control-label col-md-6" type="number" id="LineaInicial" name="linea_inicial" value="0">
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Línea Final</label>
                <input class="control-label col-md-6" type="number" id="LineaFinal" name="linea_final" value="<?php echo $num_lineas; ?>">
            </div>
            <div class="form-group">
                <p>Ahora vamos importar los datos csv a base datos de MYSQL</p>
            </div>
            <div class="form-group align-right">
                <input type="button" href="javascript:;" onclick="valoresProceso($('#LineaInicial').val(), $('#LineaFinal').val());return false;" value="Importar a MySql"/>
            </div>
        </form>
        <div>
            <a href="paso2<?php echo substr($ficheroNombre, 0, -4) . '.php'; ?>">Saltar esté paso 1 y al paso 2</a>
        </div>
        <!-- Script para ejecutar funcion php -->
        <script>
            // La variables lineaActual y lineaF son globales .
            // Estás variables la lee al cargar la pagina.

            var fichero = "<?php echo $ficheroNombre; ?>";

            var lineaActual = 0;
            var lineaF = 0;
            var ciclo;
            // Función que inicia el ciclo de proceso, para 
            // añadir datos mysql, el intervalo de tiempo
            // puede modificarse en función servidor y hardware que se tenga.
            // yo de momento le puse 20000, son 20 segundos. 
            function cicloProcesso() {
                alert('Recuerda que los registros van a ser sustituidos por los nuevos \n' +
                        ' ya campo Linea es primario, por eso nunca creara uno nuevo.');

                       
//                bucleProceso(lineaF, lineaActual, fichero);
                ciclo = setInterval("bucleProceso(lineaF,lineaActual,fichero)", 20000);

            }

            // Función que al pulsar en Importar a MySql pone 
            // valores a las variables.
            // Y empezamos a EJECUTAR cicloProceso() me modo temporal.
            function valoresProceso(valorCaja1, valorCaja2) {
                var respuestaConf = confirm('Vamos a Borrar los registros de la tabla temporal\n\
                Estas seguro');
                if (respuestaConf == true) {
                    var nombretabla = "<?php echo $nombretabla; ?>";
                    var parametros = {
                        'nombretabla': nombretabla,
                        'pulsado': 'borrar'
                    };
                    $.ajax({
                        data: parametros,
                        url: 'funciones.php',
                        type: 'post',
                        beforeSend: function () {
                            $("#resultado").html("Procesando, espere por favor...");
                        },
                        success: function (response) {
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
            // FIN DE FUNCIONES
        </script>





        <div id="resultado">
        </div>
        <div class="progress" style="margin:100px">
            <div id="bar" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                0 % completado
                <!--
                                                                <span id="spanProceso" class="sr-only">0% Complete</span>
                -->
            </div>
        </div>
    </div>	
    <?php ?>



</div>
</div>

</body>
</html>
