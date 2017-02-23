<?php 
/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogo productos Soluciones Vigo.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero
 * @Descripcion	Es el encargado leer el fichero subido y importar los registros que le indiquemos.
*/


// =========================   DESCRIPCION  ============================================  //
// Este fichero lo mostramos despues de enviar un fichero o saltamos al paso 1 de cualquiera
// de los tres ficheros que podemos subir.
// OBJETIVO PRINCIPAL:
// Poder seleccionar el intervalo de registros vamos a subir.
// Primero realizamos las siguientes comprobaciones:
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


// Inicio de variables
	$ficherosposibles = array("ReferenciasCruzadas.csv","ReferenciasCversionesCoches.csv","ListaPrecios.csv");
	$errorFichero = ''; // Errores que no se puede continuar
	$advertencias = array(); // Posible errores, pero se puede continuar.
	$advertencias['texto'] = '';
	$correcto = '';	
	// $ficheroNombre, que puede ser el recien subido o el indica GEt si nos saltamos al PASO 1
	if ($_GET) {
		if (isset($_GET["fichero"])) {
			$ficheroNombre = $_GET["fichero"] . '.csv';
			$advertencias['subido'] = 'SIN SUBIR';
		} else {
			$ficheroNombre= $_FILES['fichero_usuario']['name'];
			$advertencias['subido'] = 'RECIEN SUBIDO';	
		}
	}
?>


<!DOCTYPE html>
<html>
<head>
	<?php
	include './../../head.php';
	?>
	<script src="<?php echo $HostNombre; ?>/modulos/mod_importar/importar.js"></script>
	<script src="<?php echo $HostNombre; ?>/modulos/mod_importar/recibircsv.js"></script>
	<script>
		// [ PENDIENTE  ]
		// Una vez pulsado btn Importar a Mysql deberíamos desactivar 
		// input de lineas y btn , para evitar que usuario pulse en ellos y cambie o vuelve ejecutar.
		
		// **************  Variables Globales ********************
		var fichero = "<?php echo $ficheroNombre; ?>";
		var lineaActual = 0;
		var lineaF = 0;
		var ciclo;
		
	</script>
</head>
<body>
	<?php 
	include './../../header.php';
	include_once ("funciones.php");
	include_once ("./Consultas.php");
	?>

	<?php
	// Realizamos comprobaciones para saber si:
	// 		- Si acaba subir el fichero con nombre correcto 
	// 		- Si existe en temporal
	// Creando advertencias o error que no permite seguir.
	$fichero_subido = $ConfDir_subida . $ficheroNombre;
	if ($_GET["subida"] == 0) {
		if (in_array($ficheroNombre, $ficherosposibles) and move_uploaded_file($_FILES['fichero_usuario']['tmp_name'], $fichero_subido)) {
			$correcto = " - Correcto el nombre del fichero recien subido.<br/>";
			} else {
			$errorFichero = "- Fichero " . $ficheroNombre 
							. " no es correcto su nombre.<br/>Los nombre de ficheros que puede utilizar son:<br/>-"
							. implode("<br/>-", $ficherosposibles).'<br/>';
		}
	}
	if (is_file($fichero_subido) == true){
			$correcto = $correcto." - El fichero existe en directorio temporal.<br/>";
		} else {
		$errorFichero= $errorFichero."- Fichero NO EXISTE en directorio temporal:<strong> ".$ConfDir_subida.$ficheroNombre."</strong><br/>";
	}

	// Asignamos valor $nombretabla
	$FDatos = FicheroDatos($ficheroNombre);
	$nombretabla 		= $FDatos['nombretabla'];
	
	// Abrimos fichero CSV
	if (file_exists ($fichero_subido)){
		//abro el archivo para lectura
		$archivo = fopen($fichero_subido, 'r');
		//inicializo una variable para llevar la cuenta de las líneas y los caracteres
		$num_lineas = 0;
		//Hago un bucle para recorrer el archivo línea a línea hasta el final del archivo
		// Mostramos las primeras 10 lineas registro si las hay claro..	
	} else {
		$errorFichero = $errorFichero . "- ERROR: No se pudo abrir fichero ".$ConfDir_subida . $ficheroNombre."<br/>";
	}

		
	// Ahora Creamos clase Consulta para realizar comprobaciones... 
	$consultaRegistros = new ConsultaImportar;
	$whereC = '';
	// Consultamos si tiene registros la tabla.
	$NumeroRegistros = $consultaRegistros->contarRegistro($BDImportRecambios,$nombretabla,$whereC);
	if ($NumeroRegistros > 0){
		$advertencias['texto'] = $advertencias['texto'] .'<li>En la tabla '
								.$nombretabla.' tiene <strong>'.$NumeroRegistros.'</strong> registros.</li>';
	}
?>


	<div class="container">
		<div class="col-md-12 text-center">
			<?php if ($advertencias['subido'] == 'SIN SUBIR'){?>
				<h2>Paso 1 : Añadir registros a BD temporal del fichero <?php echo $ficheroNombre.' '.$advertencias['subido'];?>   </h2>

			<?php	
			} else {?>
				<h2>Paso 1 : Añadir registros a BD temporal del fichero <strong><?php echo $advertencias['subido'];?></strong> </h2>
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
						<legend>¿Que líneas quieres importar a mysql?</legend>
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
						Una vez seleccionado intervalo de lineas se importa los datos csv a BD temporal de MYSQL<br/>

						<?php 
						if ($NumeroRegistros > 0) {?>
							Teniendo en cuenta que se eliminan los <strong><?php echo $NumeroRegistros; ?></strong> registros que tiene TABLA TEMPORAL <?php echo $nombretabla; ?> o prefieres <a href="paso2<?php echo substr($ficheroNombre, 0, -4) . '.php'; ?>">IR A PASO 2</a></strong> donde comprobamos los todos esos registros. </strong> 
							
							<?php
							}
						?>
					</div>
					<div class="form-group align-right">
						<input type="button" href="javascript:;" onclick="valoresProceso($('#LineaInicial').val(), $('#LineaFinal').val(),'<?php echo $nombretabla; ?>');return false;" value="Importar a MySql"/>
						
					</div>
				</form>
				<div>
					<?php if ($NumeroRegistros > 0) {?>
					
					<?php
					}?>
					
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
			
		</div>
		<?php 
		} // Cerramos if !erroFichero 
		?>

		</div>

</body>
</html>
