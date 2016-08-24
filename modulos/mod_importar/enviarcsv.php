<?php 
 // Este fichero se incluye en Importar-php
 // Es el formulario de carga de fichero csv
 // de momento pasamos de el, es decir no lo realizamos , ya 
 // asi no puede cargar el fichero y no puede importar.
 // lo que hacemos es simplmente crear el formulario de carga de fichero.
 
?>

	<div class="row">
		<div class="col-md-12 text-center">
			<h2>Subida de fichero a BD temporal</h2>
		</div>
		<div class="col-md-12">
			<div id="SubidaFicheros">
			<?php 
				/* En el form debemos controlar recibircsv.php, es decir aquí cambia según 
				 * el fichero que subamos, ya que no se procesa de la misma manera, uno u otro.
				 * */
			?>
			<form role="form" enctype="multipart/form-data" action="recibircsv.php" method="POST">
				<input type="hidden" name="MAX_FILE_SIZE" value="5023834" />
				<!-- MAX_FILE_SIZE debe preceder al campo de entrada del fichero -->
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
				<p> Los nombres de los ficheros que se pueden subir son:</p>
				<div class="col-md-4">
				<h4>Ref. Cruzadas</h4>
				<div class="alert alert-info">
					<small><strong>Nombre:</strong></small>
					<small>ReferenciasCruzadas.csv</small>
				</div>
				<p>Este fichero es el encargado de indicar las referencias de otros fabricantes.</p>
				<p>Estos son los los campos que debe contener el fichero.</p>
				<ul>
				<li>RefDKM 	(text)</li>
				<li>Fabr_Recambio (text	)</li>
				<li>Ref_Fabricante (text)</li>
				</ul>
				<p>La tabla temporal tiene un campo más, que le llamamos <strong>Estado</strong>, donde vamos indicar si hay errores en la carga, o si fue importado a la Base de datos de Recambios.</p>
				
				</div>
				<div class="col-md-4">
				<h4>Ref.Versiones Coches</h4>
				<div class="alert alert-info">
					<small><strong>Nombre:</strong></small>
					<small>ReferenciasCversionesCoches.csv</small>
				</div>
				<p>Este fichero es el encargado de indicar las recambios monta cada version de coches.</p>
				</div>
				<div class="col-md-4">
				<h4>Lista Precios</h4>
				<div class="alert alert-info">
					<small><strong>Nombre:</strong></small>
					<small>ListaPreciosProveedor.csv</small>
				</div>
				<p>Este fichero es encargado de indicar el precio coste de cada proveedor.</p>
				</div>
			</div>
		</div>
	</div>


