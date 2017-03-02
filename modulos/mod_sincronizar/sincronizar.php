<!DOCTYPE html>
<html>
<head>
<?php
	include_once './../../head.php';
?>
</head>
<body>
<?php 
	include_once './../../header.php';
	include_once './ObjetoSincronizar.php';
	$ObjSincronizar = new ObjSincronizar
?>
<div class="container">
	<h2>Sincronizacion y comprobacion de bases de datos ( Recambios con la WEB ).</h2>
	<div class="col-md-6">
		<h4>Sincronizacion</h4>
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
			// Quiere decir que hay diferencias entre las dos BDDatos la recambios y la de la web.
			// tenemos que vaciar la tabla viruemart_product de recambios y luego copiarla ( añadir los registros...
			// ya que sino produce un error .
			// Error :ERROR 1062: Duplicate entry 
			$RegistrosEliminado = $Controlador->EliminarTabla('virtuemart_products',$BDRecambios);
			
			$ResulCopiaTabla = $ObjSincronizar->CopiarTablasWeb ($BDRecambios,$BDWebJoomla,$Conexiones[2]['NombreBD'],$Conexiones[3]['NombreBD'],$prefijoJoomla); 
			$Respuesta1 = '<span class="glyphicon glyphicon-ok-sign"></span>'.$RegistrosEliminado.' registros eliminados de tabla virtuemart_product de BD recambios<br/>';
			$Respuesta1 .= '<span class="glyphicon glyphicon-ok-sign"></span>'.$ResulCopiaTabla.' registros añadidos a tabla virtuemart_product de BD recambios<br/>';
		
		} else {
			$Respuesta1 = '<span class="glyphicon glyphicon-ok-sign"></span>'.' NO SE COPIO: Ya que hay misma cantidad registros entre las dos BD de la tabla '.$Conexiones[2]['NombreBD'];
		}
		 echo $Respuesta1
		?>

	</div>
</div>
</body>
</html>
