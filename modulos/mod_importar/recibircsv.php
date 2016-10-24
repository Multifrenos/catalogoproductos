<?php 
// Este fichero lo mostramos despues de enviar fichero o saltamos al paso 2 de cualquiera
// de los tres ficheros que podemos subir.
// OBJETIVO PRINCIPAL:
// Poder seleccionar que registros vamos a subir, un intervalo.
// Antes de nada , es decir antes poder mostrar el formulario debemos hacer una serie de 
// comprobaciones.
// 	1.- Identificar si acabamos de subir fichero o no.
//	2.- Si acabamos de subir el fichero, los guardamos en tmp con el nombre que tiene el fichero recien subido.
//	3.- Se comprueba que sea correcto nombre del fichero. ( Esto a lo mejor debería cambiarse.... )
// 		Si produce un error , aquí no se permite continuar.... 
// 	4.- Creamos variables para cada tipo fichero con el numero campos posibles  y los campos a cubrir vacios o por defecto.
//	5.- Contamos si la tabla tiene registros o no.
// 	6.- Contamos las lineas que tiene el fichero...
//	7.- Mostramos errores y información...y formulario.
// NOTA: No se muestra formulario de intervalos de lineas y se produce errores graves como:
// 		1.- No existe fichero en directorio tmp
// 		2.- No hay conexion a la base de datos.


?>

<!DOCTYPE html>
<html>
    <head>
        <?php
        include './../../head.php';
		// Realizamos conexión a Base datos
        include ("./../mod_conexion/conexionBaseDatos.php");
        ?>
        <script src="<?php echo $HostNombre; ?>/modulos/mod_importar/importar.js"></script>
    </head>
    <body>
        <?php
        include './../../header.php';
        // Inicio de variables
			$ficherosposibles = array("ReferenciasCruzadas.csv","ReferenciasCversionesCoches.csv","ListaPrecios.csv");
			$errorFichero = '';
			$correcto = '';	
		// Ahora comprobamos si acabamos subir fichero o nos saltamos ese paso.
        if (isset($_GET["fichero"])) {
			// Si nos saltamos el paso, entonces ponemos extensión
            $ficheroNombre = $_GET["fichero"] . '.csv';

		} else {
			// Quiere decir que se acaba subir... Aunque tiene porque... :-)
			$ficheroNombre= $_FILES['fichero_usuario']['name'];
			$fichero_subido = $ConfDir_subida . $ficheroNombre;
		}
		
		// Creamos variable numero campos, campos a cubrir, y nombre tabla, segun para que fichero estemos tratando.
		switch ($ficheroNombre) {

			case "ReferenciasCruzadas.csv" :
				$NumeroCamposCsv = 3;
				$CamposSinCubrir = "0','0";
				$nombretabla = "referenciascruzadas";
		
			case "ReferenciasCversionesCoches.csv":
				$NumeroCamposCsv = 3;
				$nombretabla = "referenciasCversiones";
			
			case "ListaPrecios.csv":
				$NumeroCamposCsv = 3;
				$nombretabla = "listaprecios";
				$CamposSinCubrir = "0";
			
		}
			
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
                // Ahora comprobamos si existe el fichero en cuestión en /tmp
				if (file_exists ($ConfDir_subida.$ficheroNombre)){
                    echo '<div class="col-md-12"><div class="alert alert-info">';
                    echo 'No se subió fichero se salto ese paso , el fichero que vamos analizar es:<strong>' . $ficheroNombre;
                    echo '</strong></div></div>';
                } else {
				$errorFichero= "- No SE SUBIO fichero y NO EXISTE el fichero en directorio temporal:<strong> ".$ConfDir_subida.$ficheroNombre."</strong><br/>";
                }
            } else {
                // Caso contrario es que no se paso nombre fichero por url
                // Movemos fichero recien subido a tmp
                if (move_uploaded_file($_FILES['fichero_usuario']['tmp_name'], $fichero_subido)) {
                    $correcto = " - El fichero acaba subir y existe en directorio temporal.<br/>";
                } else {
                    // No pudo guardar el fichero por algún motivo.
                    // Errores posibles $_FILES
                    //    1 → El fichero seleccionado excede el tamaño máximo permitido en php.ini (podemos saber el tamaño máximo permitido usando la función ini_get(‘upload_max_filesize’)).
                    //    2 → El archivo subido excede la directiva MAX_FILE_SIZE, si se especificó en el formulario.
                    //    3 → El archivo subido fue sólo parcialmente cargado.
                    //    4 → No se ha subido ningún archivo.
                    //    6 → Falta el directorio de almacenamiento temporal.
                    //    7 → No se puede escribir el archivo (posible problema relacionado con los permisos de escritura).
					// Aun fui capaza de llegar utilizarlo... 
					//
                    $errorFichero = $errorFichero .$debugprueba. "- Hubo un error en la carga del fichero.<br/>
											Revisa configuracion de servidor, ya el error es de carga.<br/><br/>
											Error se produce en:<br/>
											<strong>move_upload_file</strong> en fichero  mod_importar/recibircsv.php ( linea 99)<br/>
											AVISAR SERVICIO TECNICO!!<br/>";
					
					
					
                }
            }
            if (!in_array($ficheroNombre, $ficherosposibles)) {
                // Comprobamos que el nombre del fichero es correcto, para evitar errores, aunque esto 
                // implica que el usuario tiene que poner los nombres correcto al fichero que suba.
                // es una forma de evitar problemas.
                if ($_GET["subida"] == 0) {
                    $errorFichero = $errorFichero . "- Fichero " . $ficheroNombre . " no es un nombre de fichero correcto.<br/>Los nombre de ficheros que puede utilizar son:<br/>-" . implode("<br/>-", $ficherosposibles);
                    $errorFichero .="<br/>";
                } else {
					// Solo comprobamos conexión si obtenemos un nombre correcto... 
              		// Comprobamos si la conexion fue correcta ( include conexion )
			
					if ($BDImportRecambios->controlError) {
						// Comprobamos si fallos la conexión con la base de datos.
						$errorFichero.= "<strong>Error en conexión:</strong>".$BDImportRecambios->controlError.'<br/>';
					}
					// Realizamos conexión para contar si tiene registros. $cuenta
					$consulta = "SELECT count(linea) as cuenta FROM " . $nombretabla;
					$consultaContador = mysqli_query($BDImportRecambios, $consulta);
					if($consultaContador == true){
						// Recogemos en variable $contador la variable de la consulta [cuenta]
						$contador = $consultaContador->fetch_assoc();
						} else {
						// Quiere decir que no es correcta la consulta, por lo que se produce un error.
						$errorFichero.= "<br/><strong>Error en consulta:</strong><br/>".mysqli_error($BDImportRecambios)."<br/>";
						$errorFichero.= "<strong>Instrucción SQL enviada:</strong><br/>".$consulta."<br/>";
					
					}
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




// Creamos advertencia , que tiene o no registros.
if ($contador['cuenta'] == '0') {
    $correcto.= "- Tabla temporal sin reguistros <br/>";
} else {
    $errorFichero.= "- La tabla temporal contiene " . $contador['cuenta'] . " registros <br/>"
            . "- Al pulsar importar se borraran los reguistros de la tabla";
}
// Cierro conexión... 
mysqli_close($BDImportRecambios);
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
         <div class="progress" style="margin:0 100px">
            <div id="bar" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                0 % completado
                <!--
                                                                <span id="spanProceso" class="sr-only">0% Complete</span>
                -->
            </div>
        </div>
        <div id="resultado">
        </div>
        <!-- Script para ejecutar funcion php -->
        <script>
			// [ PENDIENTE  ]
			// Una vez pulsado btn Importar a Mysql deberíamos desactivar 
			// input de lineas y btn , para evitar que usuario pulse en ellos y cambie o vuelve ejecutar.
			// AUNQUE AL ESTAR LA PETICIONES COMO SINCRONO, YA NO ES TAN FACIL... :-)
			
			// **************  Variables Globales ********************
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
                alert('Iniciamo ciclo, recuerda que añade 400 registros y tarda 20 segundo \n' +
                        ' cada vez que actualiza la barra de proceso.');

                
				bucleProceso(lineaF, lineaActual, fichero);
				// 15000ms segundo es el tiempo que ponemos por defecto para realizar la ciclo de peticiones a servidor.
				// Recuerda que las peticiones AJAX está como sincrono en el hilo principal están desaprobadas ( algo que no recomiendan en :
				// http://xhr.spec.whatwg.org/)
				// Al ser peticiones sincrono, afecta realmente al cliente ( usuario ) ya que no le permite hacer nada en navegador
				// mientras realiza ciclo.
				// En la instrucción anterior [bucleProceso(bucleProceso(lineaF, lineaActual, fichero)]
				// realizamos el primer proceso, antes de empezar el ciclo.
				// Si hacemos un control tiempo al iniciar petición y al terminar podemos saber
				// el tiempo que tarda en hacer el proceso 400 registros y sustituir 15000ms 
				// Al utilizar setInterval() crea un ciclo ejecutando la funcion cada ms que le indiquemos.
				// 		- 	Empieza contar el tiempo y realiza petición:
				//			Esto hace que durante los 15 primeros segundos desde pulsar btn, el usuario puede utilizar btn derecho raton
				// 		y puede inspecciona consola de eventos...  , luego el tiempo es tan justo que no puedrá. :-)
				//		- Sigue contando el tiempo aunque tengamos sincrono, pero si no recibe respuesta,
				// 		no manda la siguiente peticion antes de termine...
				// Esto es una mezcla ( para mi ) sincrono y asincrono... :-)

                ciclo = setInterval("bucleProceso(lineaF,lineaActual,fichero)", 15000);

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
                            $("#resultado").html('Borrando tabla ListaPrecios, espere por favor...<span><img src="./img/ajax-loader.gif"/></span>');
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





    </div>	
    <?php ?>



</div>
</div>

</body>
</html>
