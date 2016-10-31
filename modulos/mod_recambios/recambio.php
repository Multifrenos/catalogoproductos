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
		// ===========  Busqueda datos Recambio ============= //
			$tabla= 'recambios';
			$idBusqueda ='id='.$id;
			$RecamID = $Crecambios->BusquedaIDUnico($BDRecambios,$idBusqueda,$tabla);
			$RecamID = $Crecambios->ObtenerRecambios($RecamID);
			// Solo debería haber un resultado, creamos de ese resultado unico, pero debería comprobarlo.
		$Recambio = $RecamID[items][0]; 
		// ======== Busqueda Referencia de fabricante de Cruces ============== //
			$tabla = 'referenciascruzadas';
			$idBusqueda = 'RecambioID ='.$Recambio['id'];
		$RefFabricante = $Crecambios->UnicoRegistro($BDRecambios,$idBusqueda,$tabla);
		// Ahora añado a recambio Referencia Fabricante
		$Recambio ['FabricanteRef'] = $RefFabricante['RefFabricanteCru'];
		// ======== Buscamos datos ID familia. ========== //
			$tabla = 'recamb_familias';
			$idBusqueda = 'IdRecambio ='.$Recambio['id'];
			// el mismo $idBusqueda
		$FamRecam = $Crecambios->UnicoRegistro($BDRecambios,$idBusqueda,$tabla);
		// Ahora añado a array Recambio id de familia
		$Recambio ['FamiliaID'] = $FamRecam['IdFamilia'];
		// ======== Buscamos  nombre familia;
			$tabla = 'familias_recambios';
			$idBusqueda = 'id='.$FamRecam['IdFamilia'];
		$NombreFamRecam = $Crecambios->UnicoRegistro($BDRecambios,$idBusqueda,$tabla);
		// Ahora añado a array Recambio Descricion de familia
		$Recambio ['Familia'] = $NombreFamRecam['Familia_es'];
		// ======== Buscamos datos fabricante.
			$idBusqueda = 'id='.$Recambio['IDFabricante'];
			$tabla = 'fabricantes_recambios';
		$FabRecam = $Crecambios->UnicoRegistro($BDRecambios,$idBusqueda,$tabla);
		// Ahora añado a array Recambio Descricion de Fabricante
		$Recambio ['Fabricante'] = $FabRecam['Nombre'];

		// ======== AHORA REALIZAMOS ARRAY CRUCESRECAMBIO ============== //
		$tabla= 'cruces_referencias';
			$idBusqueda ='idRecambio='.$Recambio['id'];
			$ResultadoCrucesRecamID = $Crecambios->BusquedaIDUnico($BDRecambios,$idBusqueda,$tabla);
			$CruceRecambio['TotalCruce'] = $ResultadoCrucesRecamID->num_rows;
			$i = 0;
			while ($cruce = $ResultadoCrucesRecamID->fetch_assoc()) {
				$CruceRecambio[$i]['idFabriCruz']= $cruce['idFabricanteCruz'];
				$CruceRecambio[$i]['idReferenciaCruz']= $cruce['idReferenciaCruz'];
					// Ahota tengo que buscar en fabricantes_recambios nombre fabricantes.
					$idBusqueda = 'id='.$CruceRecambio[$i]['idFabriCruz'];
					$tabla = 'fabricantes_recambios';
					$FabRecam = $Crecambios->UnicoRegistro($BDRecambios,$idBusqueda,$tabla);
					$CruceRecambio[$i]['FabricanteCru'] = $FabRecam['Nombre'];
					// Ahora tengo que buscar en referenciaCruzadas RefFabricanteCru 
					$tabla = 'referenciascruzadas';
					$idBusqueda = 'id ='.$CruceRecambio[$i]['idReferenciaCruz'];
					$RefFabricante = $Crecambios->UnicoRegistro($BDRecambios,$idBusqueda,$tabla);
					// Ahora añado a recambio Referencia Fabricante
					$CruceRecambio[$i]['FabricanteCruRef'] = $RefFabricante['RefFabricanteCru'];
			$i = $i+1;
			}
		
		
		
			//~ $tabla = 'cruces_referencias';
			//~ // el mismo $idBusqueda
		//~ $CrucesRef = $Crecambios->UnicoRegistro($BDRecambios,$idBusqueda,$tabla);
		
		
		?>
	</head>
	<body>
		<?php
        include './../../header.php';
        ?>
		<div class="container">
			<div class="col-md-7">
				<h1> Datos Recambio</h1>
				<h3><?php echo $Recambio['Descripcion'];?></h3>
				<div class="col-md-6">
					<?php 
					// UrlImagen
					$img = './../../imagenes/recambios/'.$Recambio['IDFabricante'].'/'.$Recambio['FabricanteRef'];
					?>
					<a href="<?php echo $img;?>"><img src="<?php echo $img;?>" style="width:100%;"></a>
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
			<?php // Debug
				echo '<pre>';
				echo ' Recambio ';
				print_r($Recambio);
				
				//~ echo ' Familia '.'<br/>';
				//~ print_r($FamRecam);
				//~ echo 'Cruces Recambios <br/>';
				//~ print_r($CrucesRef);
				//~ echo 'Referencias Fabricantes <br/>';
				//~ print_r($RefFabricante);
				//~ echo 'Nombre Familia <br/>';
				//~ print_r($NombreFamRecam);
				//~ echo ' Fabricante <br/>';
				//~ print_r($FabRecam);


				echo '</pre> ';
			?>
			
			</div>
			<div class="col-md-5">
				<?php 
				$html = '<h1> Referencias cruzadas</h1>'
						.'Total referencias cruzadas encontradas '
						.$CruceRecambio['TotalCruce'].'<br>';
			 for ($i = 0; $i < $CruceRecambio['TotalCruce']; $i++) {
				$html .= '<a title="Id Referencia Cruzada:'.$CruceRecambio[$i]['idReferenciaCruz']
						.'"><span class=" glyphicon glyphicon-info-sign"></span></a>'
						.$CruceRecambio[$i]['FabricanteCruRef'].' '
						.'<a title="Id Fabricante Recambio:'
						.$CruceRecambio[$i]['idFabriCruz'].'"><span class=" glyphicon glyphicon-wrench"></span></a>'
						.$CruceRecambio[$i]['FabricanteCru'].'<br/>';
				}
			echo $html;
			//~ echo '<code>'.$html.'</code>';
			?>
			
			<?php // Debug
			//~ echo '<pre>';
			//~ echo ' Cruces ';
				//~ print_r($CruceRecambio);
			//~ echo '</pre>';
			?>
			</div>
		</div>
	</body>
</html>
