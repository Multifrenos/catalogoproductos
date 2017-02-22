<?php 
/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogo productos Soluciones Vigo.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero
 * @Descripcion	
*/


// =========================   DESCRIPCION  ============================================  //
// Este fichero lo mostramos despues de enviar un fichero o saltamos al paso 1 de cualquiera
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

<?php
include ("./../../configuracion.php");
// Realizamos conexión a Base datos
include ("./../mod_conexion/conexionBaseDatos.php");

// Inicio de variables
	$ficherosposibles = array("ReferenciasCruzadas.csv","ReferenciasCversionesCoches.csv","ListaPrecios.csv");
	$errorFichero = ''; // Errores que no se puede continuar
	$advertencias = array(); // Posible errores, pero se puede continuar.
	$correcto = '';	
// Realizamos comprobaciones para saber si:
// 		- Si acaba subir el fichero
// 		- Si se salto el paso subir y indicamos fichero
if ($_GET) {
	if (isset($_GET["fichero"])) {
		// Si nos saltamos el paso, entonces ponemos extensión
		$ficheroNombre = $_GET["fichero"] . '.csv';
		$fichero_subido = $ConfDir_subida . $ficheroNombre;
		// Ahora comprobamos si existe el fichero en cuestión en /tmp
		if (is_file($fichero_subido) == true){
			$advertencias['subido'] = 'No se subió';
			$advertencias['texto'] = '<li>No se subió fichero se salto ese paso.</li>';
			$correcto = $correcto . " - El fichero encontrado <strong>".$fichero_subido."</strong>.<br/>";

		} else {
		$errorFichero= "- No SE SUBIO fichero y NO EXISTE el fichero en directorio temporal:<strong> ".$ConfDir_subida.$ficheroNombre."</strong><br/>";
		}
	} else {
		// Comprobamos que los subimos
		$ficheroNombre= $_FILES['fichero_usuario']['name'];
		$fichero_subido = $ConfDir_subida . $ficheroNombre;
		
		if ($_GET["subida"] == 0) {
			// Antes guardar el fichero recien subido, comprobamos
			//    - Que el nombre sea correcto y que se pueda guardar.
			if (in_array($ficheroNombre, $ficherosposibles) and move_uploaded_file($_FILES['fichero_usuario']['tmp_name'], $fichero_subido)) {
				 $correcto = " - El fichero se acaba subir y existe en directorio temporal.<br/>";
				
			} else {
				$errorFichero = $errorFichero . "- Fichero " . $ficheroNombre 
								. " no es un nombre de fichero correcto.<br/>Los nombre de ficheros que puede utilizar son:<br/>-"
								. implode("<br/>-", $ficherosposibles).'<br/>';
			}
		} 
		
	}
	
} else {

	$errorFichero = " Algo esta mal, ya que no se subio fichero y tampoco se envio fichero a analizar";
}


// Asignamos valor $nombretabla

if ($ficheroNombre == 'ListaPrecios.csv') {
	$nombretabla = "listaprecios";
}
if ($ficheroNombre == 'ReferenciasCruzadas.csv') {
	$nombretabla = "referenciascruzadas";
}
if ($ficheroNombre == 'ReferenciasCversionesCoches.csv') {
	$nombretabla = "referenciascversiones";
}
		
// Abrimos fichero CSV
	if (file_exists ($ConfDir_subida.$ficheroNombre)){
		//abro el archivo para lectura
		$rutafichero = $ConfDir_subida . $ficheroNombre;
		$archivo = fopen($rutafichero, 'r');
		//inicializo una variable para llevar la cuenta de las líneas y los caracteres
		$num_lineas = 0;
		//Hago un bucle para recorrer el archivo línea a línea hasta el final del archivo
		// Mostramos las primeras 10 lineas registro si las hay claro..	
	} else {
		$errorFichero = $errorFichero . "- ERROR: No se pudo abrir fichero ".$ConfDir_subida . $ficheroNombre."<br/>";
	}

		
// Ahora Creamos clase Consulta para realizar comprobaciones... 
include ("./Consultas.php");
$consultaRegistros = new ConsultaImportar;
$whereC = '';
// Consultamos si tiene registros la tabla.
$NumeroRegistros = $consultaRegistros->contarRegistro($BDImportRecambios,$nombretabla,$whereC);
if ($NumeroRegistros > 0){
	$advertencias['texto'] = $advertencias['texto'] .'<li>En la tabla '
							.$nombretabla.' tiene <strong>'.$NumeroRegistros.'</strong> registros.</li>';
}
?>

<!DOCTYPE html>
<html>
<head>
	<?php
	include './../../head.php';
	
	?>
	<script src="<?php echo $HostNombre; ?>/modulos/mod_importar/importar.js"></script>
	
	
	
	
	
</head>
<body>
	<?php include './../../header.php';?>

	<div class="container">
		<div class="col-md-12 text-center">
			<?php if ($advertencias['subido'] == 'No se subió'){?>
				<h2>Paso 1 : Añadir registros a BD temporal del fichero <?php echo $ficheroNombre;?> SIN SUBIR  </h2>

			<?php	
			} else {?>
				<h2>Paso 1 : Añadir registros a BD temporal del fichero <strong>RECIEN SUBIDO</strong> </h2>
			<?php
			}
			?>
		</div>
		<?php
		 // Este error hacemos que no continue comprobando que salga.
		if ($errorFichero != '') {
			?>
			<div class="alert alert-danger">
				<strong>ERRORES <br/></strong>
				<?php echo $errorFichero; ?>
			</div>	
		<?php
		// Solo mostramos si no se produce un error.
		} else { ?>
		<div class="col-md-6">
			<?php
			//Hago un bucle para recorrer fichero csv abierto, lo recorremos línea a línea hasta el final del archivo
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
				$advertencias['texto'] = $advertencias['texto'] . '<li>No tiene registros suficiente para procesar,' 
										. $num_lineas . '<br/>Solo permitimos más dos lineas</li>';
			} else {
				$correcto = $correcto . '- Numero de LINEAS a procesar son ' . $num_lineas . '<br/>';
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
			if ($advertencias['texto']) {
				?>
				<div class="alert alert-danger">
					<strong>Advertencias <br/></strong>
					<?php echo $advertencias['texto']; ?>
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
						
					</div>
				</div>
				<div id="resultado">
				</div>
				<div id="ErrorInsert">
					
				</div>
			</div>	
			<!-- Script para ejecutar funcion php -->
			<script>
				// [ PENDIENTE  ]
				// Una vez pulsado btn Importar a Mysql deberíamos desactivar 
				// input de lineas y btn , para evitar que usuario pulse en ellos y cambie o vuelve ejecutar.
				
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
					// En la instrucción anterior [bucleProceso(bucleProceso(lineaF, lineaActual, fichero)]
					// realizamos el primer proceso, antes de empezar el ciclo.
					// El ciclo no sabes cuando tiempo tarda en realizar insert de los 400 registros,
					// incluso, si el fichero es muy grande en las lineas finales debería tardar más.
					// Lo ideal sería hacer las peticiones AJAX de este ciclo sincrono en vez asincrono.
					// Asi no empezaría ninguna petición al servidor antes terminar las otras.
					// Ver más informacion en : http://ayuda.svigo.es/index.php/programacion-2/javascript/176-peticiones-ajax-sincrono-o-asincrono
					
					// De momento lo hago asincrono y le pongo que espere 3 segundo antes enviar otra petición.
					// Al utilizar setInterval() crea un ciclo ejecutando la funcion cada ms que le indiquemos.
					// 		- 	Empieza contar el tiempo y realiza petición:
					ciclo = setInterval("bucleProceso(lineaF,lineaActual,fichero)", 3000);

				}

				// Función que al pulsar en Importar a MySql pone 
				// valores a las variables.
				// Y empezamos a EJECUTAR cicloProceso() me modo temporal.
				function valoresProceso(valorCaja1, valorCaja2) {
					var respuestaConf = confirm('Si tiene datos la tabla temporal se va a Borrar ¿Estas seguro? ');
					if (respuestaConf == true) {
						var nombretabla = "<?php echo $nombretabla; ?>"; /* Nombre de la tabla */
						var parametros = {
							'nombretabla': nombretabla,
							'pulsado': 'borrar'
						};
						$.ajax({
							data: parametros,
							url: 'tareas.php',
							type: 'post',
							beforeSend: function () {
								$("#resultado").html('Borrando <?php echo $nombretabla; ?>, espere por favor...<span><img src="./img/ajax-loader.gif"/></span>');
							},
							success: function (response) {
								console.log('Eliminada tabla '+response);
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
		<?php 
		} // Cerramos if !erroFichero 
		?>

		</div>

</body>
</html>
