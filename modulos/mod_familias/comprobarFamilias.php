<?php 
/* En este modulo comprobamos
 *  - Si existe algún recambio( temporal )  que no tenga una familia en Base de datos recambios.
 *  - Cuantos cuantos recambios vamos a añadir en cada familia.
 * */
 
 
 
 /* Realizamos conexion a base Datos */
include ("./../mod_conexion/conexionBaseDatos.php");

?>

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
		<h2>Comprobar BD temporal con familias de BD de recambios</h2>
		<p> Los recambiós que hay por cada familia y que vamos añadir.<strong>De momento no filtramos los recambios que ya existan en la BD de recambios.</strong>.
		<?php
		 // Creamos array de con numero recambios por familias.familias.
		 $ResultadoFam = $BDRecambios->query("SELECT * FROM Familias ORDER BY id ASC");
		echo '<pre>';
		$x=0;
		while ($fila = $ResultadoFam->fetch_assoc()) {
			$FamiliaEN = $fila['Familia_en'];
			/* Sumamos resultado ... */
			$query1= "SELECT * FROM `referenciasCversiones` WHERE DescripCorta='".$FamiliaEN."' ";
			$RegisFam= $BDImportRecambios->query($query1);
			$SumaRegisFam[$x]= $RegisFam->num_rows;
			$x=$x+1;
		}
		//~ print_r($SumaRegisFam);
		/* Muevo puntero al principio sino no muestra resultados. */
		$ResultadoFam->data_seek(0);
		echo '</pre>';
		?>
		<table class="table table-striped">
				<thead>
					<tr>
						<th>ID</th>
						<th>Familia (Es)</th>
						<th>Familia (EN)</th>
						<th>NºRecambios</th>
					</tr>
				</thead>
				<?php
				$x=0;
				while ($fila = $ResultadoFam->fetch_assoc()) {
				?>
				<tr>
					<td><?php echo $fila['id'];?>		</td>
					<td><?php echo $fila['Familia_es'];?></td>
					<td><?php echo $fila['Familia_en'];?>		</td>
					<td><?php echo $SumaRegisFam[$x];
						$x=$x+1;
						?>
					</td>
				</tr>
				<?php
				}
				?>
		</table>
		
		<div class="alert alert-danger">
		<strong>PRECAUCION <br/></strong>
		Realmente añadimos todos lo recambios que tenga una familia correcta, en la base de datos de recambios
		</div>
		<div>
		<!-- Comprobar familias que no existan y se cambia el estado de a ERROR en familia -->
		
		</div>
		
	</div>
	
	
</div>

</body>
</html>
