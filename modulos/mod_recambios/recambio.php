<!DOCTYPE html>
<html>
    <head>
        <?php
		// Reinicio variables
        include './../../head.php';
        include ("./../mod_conexion/conexionBaseDatos.php");
		include ("./../mod_familias/ObjetoFamilias.php");
		include ("./ObjetoRecambio.php");
		// Obtenemos id
		if ($_GET[id]) {
		$id = $_GET[id];
		} else {
		// NO hay parametro .
		$error = "No podemos continuar";
		}
		// Creamos objeto Recambio para realizar las consultas..
		$Crecambios = new Recambio;
		// Realizamos consulta para saber cuantos registros tiene y hacer paginación.
		$RecamID = $Crecambios->RecambioUnico($BDRecambios,$id);
		$RecamID = $Crecambios->ObtenerRecambios($RecamID);
		?>
	</head>
	<body>
		<div class="container">
			<div class="col-md-7">
				<h1> Recambio : DESCRIPCION</h1>
				<?php
				echo '<pre>';
				print_r($RecamID);
				echo '</pre> ';
				?>
				<div class="col-md-6">
				IMAGEN
				</div>
				<div class="col-md-6">
				<p>ID:</p>
				<p>Fabricante:</p>
				<p>Familia:</p>
				<p>Precio Coste:</p>
				<p>Margen Beneficio:</p>
				<p>IVA:</p>
				<p>PRECIO:</p>
				</div>
			</div>
			<div class="col-md-5">
			<h1> Referencias cruzadas</h1>
			</div>
		</div>
	</body>
</html>
