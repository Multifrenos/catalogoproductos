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
		<p> Los ficheros que vamos importar son <strong>csv</strong>, con separador (,) y divisor campos (").</p>
		<p> Lo que realizamos es añadir datos a la base de datos temporal <strong>(importarRecambios)</strong> y luego indicaremos si queremos o no añadir a la base de datos <strong>RECAMBIOS.</strong></p>
		<div class="alert alert-danger">
		<strong>PRECAUCION <br/></strong>
		Tenga mucho cuidado ya que puede estropear la base datos, recomendable realizar un copia de la base de datos antes de realizar esta operacion.
		</div>
		<div>
		<p> Recueda que los ficheros no pueden ser superiores a <strong>50MG</strong>, el proceso se realiza por trozos para evitar que se bloqué el servidor.</p>
		<p>Por defecto, los ficheros se almacenan en el <strong>directorio temporal</strong> predeterminado del servidor, a menos que se haya indicado otra ubicación con la directiva upload_tmp_dir en <a href="http://localhost/pruebas/phpInfo/info.php">php.ini</a></p>
		</div>
		
	</div>
	<div class="col-md-8">
		<?php include 'enviarcsv.php'; ?>
	</div>
	
</div>

</body>
</html>
