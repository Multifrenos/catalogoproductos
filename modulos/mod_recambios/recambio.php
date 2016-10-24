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
		$Recambio = $RecamID[items][0];
		// Buscamos datos ID familia.
		$tabla = 'recamb_familias';
		$FamRecam = $Crecambios->UnicoRegistro($BDRecambios,$id,$tabla);
		// Buscamos  nombre familia;
		$tabla = 'familias_recambios';
		$NombreFamRecam = $Crecambios->UnicoRegistro($BDRecambios,$FamRecam['id'],$tabla);
		// Buscamos datos fabricante.
		$IdFabri =$Recambio['IDFabricante'];
		$tabla = 'fabricantes_recambios';
		$FabRecam = $Crecambios->UnicoRegistro($BDRecambios,$IdFabri,$tabla);
		// Buscamos datos familia.
		
		
		?>
	</head>
	<body>
		<?php
        include './../../header.php';
        ?>
		<div class="container">
			<div class="col-md-7">
				<h1> Recambio : DESCRIPCION</h1>
				<?php
				echo '<pre>';
				print_r($RecamID);
				echo ' Familia '.'<br/>';
				print_r($FamRecam);
				echo 'Nombre Familia <br/>';
				print_r($NombreFamRecam);
				echo 'Error '.mysqli_error($BDRecambios).'<br/>';

				echo ' Fabricante <br/>';
				print_r($FabRecam);

				echo 'Error '.mysqli_error($BDRecambios);

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
