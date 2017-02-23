<?php 
/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogo productos Soluciones Vigo.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero
 * @Descripcion	Importar ficheros para catalogo. ( Listaprecios, ReferenciasCruzadas, ReferenciasCversiones).
 *  */
		// Objetivo de este fichero es:
		// - Poder subir los tres ficheros necesarios
		// - Poder enviar variable que fichero subido.
		// - Poder mostrar los link acceso a los PASO 1 y 2 de cada fichero..
		// para ello tengo que comprobar, antes:
			// 1.-Si existe el fichero csv en temporal.
			// 2.-Si la BD importar y la tabla en cuestión tiene registros.

?>

<!DOCTYPE html>
<html>
<head>
<?php
	include './../../head.php';
?>
</head>
<body>
<?php 
	include './../../header.php';
	include_once ("./Consultas.php");

?>

<div class="container">
	<div class="col-md-3">
		<h2>Importación de datos a Recambios.</h2>
		<p> La importación de datos al "Catalogo de Recambios" se hacer en dos fases:</p>
		<ol>
		<li> Se añaden datos de ficheros a la <strong>BD importarRecambios</strong></li>
		<li> Se añaden o se modifican en <strong>BD Recambios </strong>de catalago</li>
		</ol>
		
		<p> Se pueden añadir solo los ficheros:</p>
		<ol>
			<li>ListaPrecios.csv</li>
			<li>ReferenciasCruzadas.csv</li>
			<li>ReferenciasCversiones.csv</li>
		</ol>
		<p> Recuerda que el primero es el que crea el Recambio, por lo que debe ser el primero en añadir y es uno por familia y fabricante principal.</p>
		<div>
		<h4>Especificaciones Tecnicas</h4>
			<ul>
			<li> Los ficheros <strong>csv</strong> tiene para cada fichero unos campos, son generados con separador (,) y divisor campos (").<a href="./../../estatico/general/comoHacerCsv.php">Ver como hacer preparar csv</a></li>
			<li> Los ficheros se almacenan directament en directorio temporal del servidor, salvo que se lo indiquemos con upload_tmp_dir que no sea así.</li>
			<li> Los ficheros no pueden ser superiores a 50MG</li>
			<li> El proceso se hace con AJAX a trozos, para evitar saturar el servidor.</li>
			<li> Ver más informacion en <a href="http://localhost/pruebas/phpInfo/info.php">php.ini</a></li>
			</ul>
		</div>
		
	</div>
	<div class="col-md-9">
		
		
		
			<?php 
	// Incluimos configuración para hacer comprobación.
	$ficherosposibles = array("ReferenciasCruzadas.csv","ReferenciasCversionesCoches.csv","ListaPrecios.csv");
	$i=0;
	foreach ($ficherosposibles as $fichero){
		$i++;
		//~ echo 'Fichero:'.$ConfDir_subida.$fichero;
		if (is_file($ConfDir_subida.$fichero) == true){
			$Accesofichero[$i]['csv'] =$ConfDir_subida.$fichero;
		} else {
			$Accesofichero[$i]['csv'] ='[ERROR: FICHERO]';
		}
	}
	// Ahora array $RutaFichero ya tiene datos , si existe tiene el fichero , sino tiene un error...

	// Ahora comprobamos si tiene datos las tablas DBImportar
	if ($BDImportRecambios->connect_errno) {
		echo "Falló la conexión a MySQL: (" . $BDImportRecambios->connect_errno . ") " . $BDImportRecambios->connect_error;
	}

	// Ahora vamos comprobar con el clase Consulta si tiene datos ... 
	$consultaRegistros = new ConsultaImportar;
	$tablasposibles =  array("referenciascruzadas","referenciascversiones","listaprecios");
	$i=0;
	$whereC = "";
	foreach ($tablasposibles as $tabla){
		$i++;
		$Accesofichero[$i]['registros'] =$consultaRegistros->contarRegistro($BDImportRecambios,$tabla,$whereC);
	}

	?>

		<div class="row">
			<div class="col-md-12 text-center">
				<h2>Enviar fichero de csv para importar datos</h2>
			</div>
			<div class="col-md-12">
				<p> El formulario de subida de ficheros es el mismo para los tipos de ficheros con los que trabajamos.</p>
				<div id="SubidaFicheros">
				<?php 
					/* En el form debemos controlar recibircsv.php, es decir aquí cambia según 
					 * el fichero que subamos, ya que no se procesa de la misma manera, uno u otro.
					 * */
				?>
				<form role="form" enctype="multipart/form-data" action="recibircsv.php?subida=0" method="POST">
					<div class="form-group">
					<label class="control-label">Seleciona el fichero a subir al servidor:</label>
					<input name="fichero_usuario" type="file">
					<!-- El nombre del elemento de entrada determina el nombre en el array $_FILES -->
					</div>
					<div class="form-group">
					<label>Enviar este fichero:</label>
					<input type="submit" value="Enviar fichero" />
					</div>
				</form>
				</div>	
				<div>
					<p> - No hay ningún tipo problema en subir varias veces el mismo fichero o el orden en que suban los ficheros.<br/>
					- Los nombres de los ficheros deben ser los que se indican, sino produce un error que no encuentra fichero.<br/>
					- Mira en más información que campos necesita cada fichero.</p>
					<p> En el PASO 1 es cuando comprueba esos ficheros y le indicamos que lineas de registro queremos añadir a la BD temporal.</p>
					<p> Puede saltarte el subir los ficheros, si lo acabas de realizar. Recuerda que ese fichero .csv solo estará en directorio temporal mientras no cierres sesión.</p>
					<div class="col-md-4">
					<h4><span class="circulo">1</span> Lista Precios</h4>
					<div class="alert alert-info">
						<small><strong>Nombre:</strong></small>
						<small>ListaPrecios.csv</small>
					</div>
					<p>Este fichero es encargado de indicar el precio coste de cada fabricante (marca).</p>
					<p>Ver campos y <a href="./../../estatico/recambio.php#importar">más información </a> de como importar el fichero ListaPreciosProveedores.csv</p>
						<?php
						if ($Accesofichero[3]['csv'] !='[ERROR: FICHERO]'){
							?>
						 <p><strong><a href="./recibircsv.php?subida=1&fichero=ListaPrecios">Ir a PASO 1 </a></strong>donde selecciona las lineas a subir de ListaPrecios</p>
						<?php
						}
						if ($Accesofichero[3]['registros'] > 0 ){
							?>
							<p><strong><a href="./paso2ListaPrecios.php">Ir a PASO 2</a></strong> donde comprobamos los <strong><?php echo $Accesofichero[3]['registros']; ?></strong> registros que tiene TABLA TEMPORAL Listaprecios.csv</p>
						<?php
						}
						?>
					</div>
					<div class="col-md-4">
					<h4><span class="circulo">2</span> Ref. Cruzadas</h4>
					<div class="alert alert-info">
						<small><strong>Nombre:</strong></small>
						<small>ReferenciasCruzadas.csv</small>
					</div>
					<p>Este fichero es el encargado de indicar las referencias de otros fabricantes.</p>
					<p>Ver campos y <a href="./../../estatico/referenciascruzadas.php#importar">más información </a> de como importar el fichero ReferenciasCruzadas que <strong>existe en temporal</strong></p>
						<?php
						if ($Accesofichero[1]['csv'] !='[ERROR: FICHERO]'){
							?>
						 <p><strong><a href="./recibircsv.php?subida=1&fichero=ReferenciasCruzadas">Ir a PASO 1 </a></strong>donde selecciona las lineas a subir de ReferenciasCruzadas</p>
						<?php
						}
						if ($Accesofichero[1]['registros'] > 0 ){
							?>
							<p>La tabla ReferenciasCruzadas tiene <strong><?php echo $Accesofichero[1]['registros']; ?></strong> quieres ir al <a href="./paso2ReferenciasCruzadas.php">PASO 2 de ReferenciasCruzadas</a></p>
						<?php
						}
						
						?>
					</div>
					<div class="col-md-4">
					<h4><span class="circulo">3</span> Ref.Versiones Coches</h4>
					<div style="padding: 15px 0 ">
						<strong>Nombre:</strong>
						<small>ReferenciasCversionesCoches.csv</small>
					</div>
					<p>Este fichero es el encargado de indicar las recambios monta cada version de coches.</p>
						<?php
						if ($Accesofichero[2]['csv'] !='[ERROR: FICHERO]'){
							?>
						 <p><strong><a href="./recibircsv.php?subida=1&fichero=ReferenciasCversionesCoches">Ir a PASO 1  de ReferenciasCversiones</a></strong></p>
						<?php
						}
						?>
									
					</div>
					
				</div>
			</div>
		</div>
	</div>
	
</div>

</body>
</html>
