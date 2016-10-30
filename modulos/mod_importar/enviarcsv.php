<?php 
 // Este fichero se incluye en Importar-php
 // Objetivo de este fichero es:
 // - Poder subir los tres ficheros necesarios
 // - Poder enviar variable que fichero subido.
 // - Poder mostrar los link acceso a los PASO 1 y 2 de cada fichero..
 // para ello tengo que comprobar, antes:
		// 1.-Si existe el fichero csv en temporal.
		// 2.-Si la BD importar y la tabla en cuestión tiene registros.
				

// Incluimos configuración para hacer comprobación.
include ("./../../configuracion.php");
$ficherosposibles = array("ReferenciasCruzadas.csv","ReferenciasCversionesCoches.csv","ListaPrecios.csv");
$i=0;
foreach ($ficherosposibles as $fichero){
	$i++;
	echo 'Fichero:'.$ConfDir_subida.$fichero;
	if (is_file($ConfDir_subida.$fichero) == true){
		$Accesofichero[$i]['csv'] =$ConfDir_subida.$fichero;
	} else {
		$Accesofichero[$i]['csv'] ='[ERROR: FICHERO]';
	}
}
// Ahora array $RutaFichero ya tiene datos , si existe tiene el fichero , sino tiene un error...

// Ahora comprobamos si tiene datos las tablas DBImportar
include ("./../mod_conexion/conexionBaseDatos.php");
if ($BDImportRecambios->connect_errno) {
    echo "Falló la conexión a MySQL: (" . $BDImportRecambios->connect_errno . ") " . $BDImportRecambios->connect_error;
}

// Ahora vamos comprobar con el clase Consulta si tiene datos ... 
include ("./Consultas.php");
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
				<h4>Ref. Cruzadas</h4>
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
				<h4>Ref.Versiones Coches</h4>
				<div class="alert alert-info">
					<small><strong>Nombre:</strong></small>
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
				<div class="col-md-4">
				<h4>Lista Precios</h4>
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
						<p>La tabla Listaprecios tiene <strong><?php echo $Accesofichero[3]['registros']; ?></strong> quieres ir al <a href="./paso2ListaPrecios.php">PASO 2 de ListaPrecios</a></p>
					<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>


