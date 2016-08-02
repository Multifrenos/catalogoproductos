<?php 
 // Este fichero se incluye en Importar-php
 // Es el formulario de carga de fichero csv
 // de momento pasamos de el, es decir no lo realizamos , ya 
 // asi no puede cargar el fichero y no puede importar.
 // lo que hacemos es simplmente crear el formulario de carga de fichero.
 
?>

	<div class="row">
		<div class="col-md-12 text-center">
			<h2>Subida de fichero</h2>
		</div>
		<div class="col-md-6">
			<div id="SubidaFicheros">
				<!-- Ejemplo de http://php.net/manual/es/features.file-upload.post-method.php -->
				<!-- El tipo de codificación de datos, enctype, DEBE especificarse como sigue -->
			<form enctype="multipart/form-data" action="recibircsv.php" method="POST">
				<!-- MAX_FILE_SIZE debe preceder al campo de entrada del fichero -->

				<input type="hidden" name="MAX_FILE_SIZE" value="5023834" />
				<!-- El nombre del elemento de entrada determina el nombre en el array $_FILES -->
				<p>El fichero no puede ser superior a 30MG</p>
				<label>Seleciona el fichero Referencias Cruzadas:</label>
				<input name="fichero_usuario" type="file" />
				<p>Por defecto, los ficheros se almacenan en el directorio temporal predeterminado del servidor, a menos que se haya indicado otra ubicaicón con la directiva upload_tmp_dir en <a href="http://localhost/pruebas/phpInfo/info.php">php.ini</a></p>
				<h2>EMPEZAR A IMPORTAR FICHERO</h2>
				<p> El fichero de arriba tiene que ser un csv, sin cabecera de campos, y estos son:</p>
				<ul>
				<li> RefDKM</li>
				</ul>
				<label>Enviar este fichero:</label>
				<input type="submit" value="Enviar fichero" />
			</form>
			</div>	
		</div>
	</div>


