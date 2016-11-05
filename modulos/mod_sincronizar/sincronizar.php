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
	include './ObjetoSincronizar.php';
	$ObjSincronizar = new ObjSincronizar
?>
<div class="container">
	<h2>Sincronizacion y comprobacion de bases de datos ( Recambios con la WEB ).</h2>
	<div class="col-md-6">
		<h4>En consiste Sincronizacion</h4>
			<p> La sincronizacion de bases de datos ( Recambios con la WEB ) consiste en:</p>
		<ul>
			<li>Copiar la tabla de BD de la web virtuemart_products en BD Recambios	</li>
			<li>Comprobar que todos los productos que hay en la Web tiene referencia de Recambios</li>
		</ul>
		
		<h4>Especificaciones Tecnicas</h4>
		<ul>
			<li> Tener conexion con la Web</li>
		</ul>
	</div>
	<div class="col-md-6">
		<h4> Resultado de sincronización</h4>
		<?php 
		if ($DifVirtuemart['Rows']){
			// Quiere decir que hay diferencias entre las filas que hay.
			$ResulCopiaTabla = $ObjSincronizar->CopiarTablasWeb ($BDRecambios,$BDWebJoomla,$Conexiones[2]['NombreBD'],$Conexiones[3]['NombreBD'],$prefijoJoomla); 
		} else {
			$ResulCopiaTabla = '<span class="glyphicon glyphicon-ok-sign"></span>'.' NO SE COPIO: Ya que hay misma cantidad registros entre las dos BD de la tabla '.$Conexiones[2]['NombreBD'];
		}
		 echo $ResulCopiaTabla
		?>

	</div>
</div>
</body>
</html>
