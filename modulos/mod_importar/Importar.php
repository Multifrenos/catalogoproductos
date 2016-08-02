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
	<h3>Importación de datos de ficheros csv.</h3>
	<p> La importación de datos de ficheros csv, lo que hace es añadir datos a la base de datos que tenemos creada, en nuestro servidor local</p>
	<div class="alert alert-danger">
	<strong>PRECAUCION <br/></strong>
	Tenga mucho cuidado ya que puede estropear la base datos, recomendable realizar un copia de la base de datos antes de realizar esta operacion.
	</div>
	<div class="container">
	<?php include 'enviarcsv.php'; ?>
	</div>
	
</div>

</body>
</html>
