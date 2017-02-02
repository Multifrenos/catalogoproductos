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
	<div class="col-md-12">
		<h2>Base de datos de Recambio.</h2>
		<p> Es la base de datos principal, la tenemos las tablas de recambios, familias de recambios, referencias cruzadas, fabricantes, referencias cruzadas de versiones de coches.</p>
		
		<h4>Tablas de BD Recambios.</h4>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Tabla</th>
					<th>Función</th>
				</tr>
			</thead>
			<tr>
				<td><small>Recambio</small></td>
				<td>Contiene los datos de los productos ( recambios ) que vamos a vender o comprar<br/>
				Si quiere ver <a href="./recambio.php">más información</a></td>
			</tr>
			<tr>
				<td><small>ReferenciasCruzadas</small></td>
				<td>Se utiliza para relacionar nuestro ID con las referencias de otros fabricantes (marcas).<br/>
				Con esto conseguimos mostrar que recambios (productos) son homologos, semejantes.<br/>
				Si quiere ver <a href="./referenciascruzadas.php">más información</a></td>
			</tr>
			<tr>
				<td><small>FabricantesRecambios</small></td>
				<td>Tabla de lista de fabricantes (marcas) de recambios, se utiliza para relacionar ID con las referencias cruzadas de otros fabricantes (marcas).
				</td>
			</tr>
			<tr>
				<td><small>Familias Recambios</small></td>
				<td>Tabla en la que tenemos creadas las familias en las que vamos a clasificar los recambios, esta tabla utiliza el sistema de enraizado de hijo padre..
				</td>
			</tr>
		</table>
	</div>
	

</div>

</body>
</html>
