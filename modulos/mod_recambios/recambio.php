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
		if ($_GET['id']) {
		$id = $_GET['id'];
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
		$Recambio = $RecamID['items'][0];
		
		// ======== Buscamos id de la Web.
			$tabla = 'virtuemart_products';
			$idBusqueda = 'product_sku ='.$Recambio['id'];
		$WebRecam = $Crecambios->UnicoRegistro($BDRecambios,$idBusqueda,$tabla);
		// Ahora añado a array Recambio Descricion de Fabricante
		if (isset($WebRecam['virtuemart_product_id']) ){
			$Recambio ['IDWeb'] = $WebRecam['virtuemart_product_id'];
		} else {
			$Recambio ['IDWeb'] = 0;
		}
		
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
		
		
		// ======== AHORA REALIZAMOS ARRAY CRUCESVEHICULOS ============== //
		$CruceVehiculo = array();
		$tabla= 'cruces_vehiculos';
		$idBusqueda ='RecambioID='.$Recambio['id'];
			$ResultadoCrucesVehiculos = $Crecambios->BusquedaIDUnico($BDRecambios,$idBusqueda,$tabla);
			$CruceVehiculo['TotalCruce'] = $ResultadoCrucesVehiculos->num_rows;
			$i = 0;
			while ($cruce = $ResultadoCrucesVehiculos->fetch_assoc()) {
				$CruceVehiculo[$i]['IDcruce']= $cruce['id'];
				$CruceVehiculo[$i]['IDVersion']= $cruce['VersionVehiculoID'];
				$CruceVehiculo[$i]['Fecha_Actua']= $cruce['FechaActualiza'];
			$i++;
			}
		?>
		<script src="<?php echo $HostNombre; ?>/modulos/mod_recambios/funciones.js"></script>


	</head>
	<body>
		<?php
        include './../../header.php';
       
       
        ?>
		<div class="container">
			<h1 class="text-center"> Datos Recambio</h1>
			<a class="text-ritght" href="javascript:history.back(1)">Volver Atrás</a>
			<div class="col-md-12">
				
				<h3><?php echo $Recambio['Descripcion'];?></h3>
				<div class="col-md-3">
					<?php 
					// UrlImagen
					$img = './../../imagenes/recambios/'.$Recambio['IDFabricante'].'/'.$Recambio['FabricanteRef'].'.jpg';
					?>
					<a href="<?php echo $img;?>"><img src="<?php echo $img;?>" style="width:100%;"></a>
				</div>
				<div class="col-md-9">
					<div class="DatosWeb">
						<div class="form-group">
							<label>Nombre Recambio:</label>
							<input type="text" id="Descripcion" name="NombreRecambio" value="<?php echo $Recambio['Descripcion'];?>" size="60" readonly>
							<button onclick="copiarAlPortapapeles('Descripcion')">Copiar</button>
							
						</div>
						<div class="col-md-6 form-group">
							<label>Ref. del producto:</label>
							<input type="text" id="RefProducto" name="ReferenciaProducto" value="<?php echo $Recambio['id'];?>"   readonly>
							<button onclick="copiarAlPortapapeles('RefProducto')">Copiar</button>
						</div>
						<div class="col-md-6 form-group">
							<label>Ref. del producto del Fabricante - GTIN (EAN,ISBN):</label>
							<input type="text" id="RefProdFabricante" name="ReferenciaProdFabricante" value="<?php echo $Recambio['FabricanteRef'];?>"   readonly>
							<button onclick="copiarAlPortapapeles('RefProdFabricante')">Copiar</button>
						</div>
						<div class="form-group">
							<label>PVP (Precio Final):</label>
=======
							<input type="text" id="PVP" name="PrecioPVP" value="<?php echo $Recambio['pvp'];?>"   readonly>
							<button onclick="copiarAlPortapapeles('PVP')">Copiar</button>
						</div>
					</div>
					<div class="Otros datos">
						<div class="col-md-6 form-group">
							<label>ID Fabricante:</label>
							<input type="text" id="IDFabricante" name="IDdeFabricante" value="<?php echo $Recambio['IDFabricante'];?>"   readonly>
						</div>
						<div class="col-md-6 form-group">
							<label>Nombre Fabricante:</label>
							<input type="text" id="Fabricante" name="DescripFabricante" value="<?php echo $Recambio['Fabricante'];?>"   readonly>
						</div>
						<div class="col-md-6 form-group">
							<label>ID de Familia:</label>
							<input type="text" id="IDFamilia" name="IDFamilia:" value="<?php echo $Recambio['FamiliaID'];?>"   readonly>
						</div>
						<div class="col-md-6 form-group">
							<label>Nombre Familia:</label>
							<input type="text" id="Familia" name="Familia:" value="<?php echo $Recambio['Familia'];?>"   readonly>
						</div>
						<div class="col-md-6 form-group">
							<label>Coste:</label>
							<input type="text" id="coste" name="coste" value="<?php echo $Recambio['coste'];?>"   readonly>
						</div>
						<div class="col-md-6 form-group">
							<label>Margen Beneficio:</label>
							<input type="text" id="Beneficio" name="Beneficio" value="<?php echo $Recambio['margen'];?>"   readonly>
						</div>
						
					</div>		
				

				
				
				</div>
			
			
			</div>
			<div class="col-md-6">
			<?php 
				// El problema que encuentro para realizar copia de esto con botton al portapapeles
				// Es que el contenido html general un cierre de la etiqueta antes de tiempo
				// pienso que se puede resolver limpiando... 
				$html = "<h3> Referencias cruzadas</h3>"
						."<p>Total referencias cruzadas encontradas "
						.$CruceRecambio['TotalCruce']."</p>";
				$htmlCopia = "Referencias cruzadas"
						."Total referencias cruzadas encontradas "
						;
			 for ($i = 0; $i < $CruceRecambio['TotalCruce']; $i++) {
				$html .= '<a title="Id Referencia Cruzada:'.$CruceRecambio[$i]['idReferenciaCruz']
						.'"><span class=" glyphicon glyphicon-info-sign"></span></a>'
						.$CruceRecambio[$i]['FabricanteCruRef'].' '
						.'<a title="Id Fabricante Recambio:'
						.$CruceRecambio[$i]['idFabriCruz'].'"><span class=" glyphicon glyphicon-wrench"></span></a>'
						.$CruceRecambio[$i]['FabricanteCru'].'<br/>';
				}
			?>
			
			<label>Copiar Relaciones Cruzadas:</label>
			<button onclick="copiarAlPortapapeles('RefCruzadas')">Copiar</button>
			<textarea id="RefCruzadas" name="RefCruzadas" readonly>
			<?php echo $html;?>
			</textarea>
			<?php echo $html;?>

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
				
				echo '<pre>';
				echo '<h1>Cruce de Vehiculos</h1>';
				print_r($CruceVehiculo);
				echo '</pre>';
				
				
			?>
		</div>
	</body>
</html>
