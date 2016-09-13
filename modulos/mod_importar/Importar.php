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
	<div class="col-md-4">
		<h2>Importación de datos a Recambios.</h2>
		<p> La importación de datos al "Catalogo de Recambios" se hacer en dos fases:</p>
		<ol>
		<li> Se añaden datos de ficheros a la <strong>BD importarRecambios</strong></li>
		<li> Se añaden o se modifican en <strong>BD Recambios </strong>de catalago</li>
		</ol>
		
		<p> El <strong>fichero ListaPrecios</strong> es el encargado de añadir Recambios nuevo y sus referencias de fabricante (marca) correpondiente.</p>
		
		<p> El <strong>fichero referencias cruzadas</strong>, se va buscar la referencia del fabricante de recambio en la tabla de recambios, donde comprobaremos si existe o no , sino existe no se añade el registro.</p>
		<p> EL <strong>fichero referencias cruzadas de Versiones coches</strong>, es el encargado de añadir los coches en los que se aplica ese recambio, por eso es fundamental que exista el recambio.</p>
		<div>
		
		
		<h4>Especificaciones Tecnicas</h4>
			<ul>
			<li> Los ficheros <strong>csv</strong>, con separador (,) y divisor campos (").</li>
			<li> Los ficheros se almacenan directament en directorio temporal del servidor, salvo que se lo indiquemos con upload_tmp_dir que no sea así.</li>
			<li> Los ficheros no pueden ser superiores a 50MG</li>
			<li> El proceso se hace con AJAX a trozos, para evitar saturar el servidor.</li>
			<li> Ver más informacion en <a href="http://localhost/pruebas/phpInfo/info.php">php.ini</a></li>
			</ul>
		</div>
		
	</div>
	<div class="col-md-8">
		<?php include 'enviarcsv.php'; ?>
	</div>
	
</div>

</body>
</html>
