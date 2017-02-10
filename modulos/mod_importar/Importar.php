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
		<?php include 'enviarcsv.php'; ?>
	</div>
	
</div>

</body>
</html>
