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
	<h2>Sincronizacion y comprobacion de bases de datos ( Recambios con la WEB ).</h2>
	<div class="col-md-6">
		<h4>En consiste Sincronizacion</h4>
			<p> La sincronizacion de bases de datos ( Recambios con la WEB ) consiste en:</p>
		<ul>
			<li>Copiar la tabla de BD de la web virtuemart_products en BD Recambios	</li>
		</ul>
		
		<h4>Especificaciones Tecnicas</h4>
		<ul>
			<li> Tener conexion con la Web</li>
		</ul>
	</div>
	<div class="col-md-6">

		<?php 
		$copiaTabla = $Controlador->CopiarTablasWeb ($Conexiones[2]['NombreBD'],$Conexiones[3]['NombreBD']); ?>
		Copiadas tabla.... 
		<?php
		echo '<pre>';
		print_r($copiaTabla);
		echo '</pre>';
		?>
	</div>
</div>
</body>
</html>
