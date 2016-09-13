<?php 
 // Este fichero se incluye en Importar-php
 // Objetivo de este fichero es:
 // - Poder subir los tres ficheros necesarios
 // - Poder enviar variable que fichero subido.
 
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
				<p>Ver campos y <a href="./../../estatico/referenciascruzadas.php#importar">más información </a> de como importar el fichero ReferenciasCruzadas</p>
				 <p><a href="./recibircsv.php?subida=1&fichero=ReferenciasCruzadas">PASAR A PASO 1</a></p>
				</div>
				<div class="col-md-4">
				<h4>Ref.Versiones Coches</h4>
				<div class="alert alert-info">
					<small><strong>Nombre:</strong></small>
					<small>ReferenciasCversionesCoches.csv</small>
				</div>
				<p>Este fichero es el encargado de indicar las recambios monta cada version de coches.</p>
				<p><a href="./recibircsv.php?subida=1&fichero=ReferenciasCversionesCoches">PASAR A PASO 1</a></p>
				</div>
				<div class="col-md-4">
				<h4>Lista Precios</h4>
				<div class="alert alert-info">
					<small><strong>Nombre:</strong></small>
					<small>ListaPrecios.csv</small>
				</div>
				<p>Este fichero es encargado de indicar el precio coste de cada fabricante (marca).</p>
				<p>Ver campos y <a href="./../../estatico/recambio.php#importar">más información </a> de como importar el fichero ListaPreciosProveedores.csv</p>
				 <p><a href="./recibircsv.php?subida=1&fichero=ListaPrecios">PASAR A PASO 1</a></p>

				</div>
			</div>
		</div>
	</div>


