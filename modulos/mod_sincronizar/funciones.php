<?php
// funciones para ejecutar.
function sincronizar($Controlador,$ObjSincronizar,$BDRecambios,$BDWebJoomla,$Conexiones,$prefijoJoomla){
	
	// Quiere decir que hay diferencias entre las dos BDDatos la recambios y la de la web.
		// tenemos que vaciar la tabla viruemart_product de recambios y luego copiarla ( añadir los registros...
		// ya que sino produce un error .
		// Error :ERROR 1062: Duplicate entry 
        $respuesta['Eliminados'] = $Controlador->EliminarTabla('virtuemart_products',$BDRecambios);
        // La respuesta será los numeros de registros eliminado.
		// Ahora copia la tabla en BD
		$respuesta['Copiado'] = $ObjSincronizar->CopiarTablasWeb ($BDRecambios,$BDWebJoomla,$Conexiones[2]['NombreBD'],$Conexiones[3]['NombreBD'],$prefijoJoomla);	

	return $respuesta;
}
 

function crearVistas($BDRecambios,$vistas,$limite) {
	// En esta funcion lo que hacermos es crear Vistas de BDRecambios para realizar consultas mas rápidas.
	// Esta funcion la utilizamos en varios procesos (funciones),incluso vamos hacer vistas por trozos, por eso 
	// creamos los parametros vistas y limite.
	// Los parametros de fucion:
	// 		$BDRecambios ( BD que creamos vista)
	// 		$vistas -> Esté puede ser
	//				$vista[0] = virtuemart -> Crea vista tabla virtuemart_productos pero con una cantida registros ( limite)
	// 				$vista[1] = vista_recambio -> Que tiene id Recambio, IdFabricante y RefFabricanteCru de las 
	// 				$vista[2] = vista_recambio -> Que tiene id Recambio, IdFabricante y RefFabricanteCru de las 
	//				dos tablas ( recambios y referenciascruzadas).
	// 		$limite-> Array que indicar el limite de la vista[1]
	//				limite[0] -> inicial
	// 				limite[1]-> final
	 
	$respuesta =array();

	
	// CREAR VISTA NECESARIAS.
	if (isset($vistas[0])){
		if ($vistas[0] == 'virtuemart'){
			$CrearViewVirtuemart = "CREATE or REPLACE VIEW ".$vistas[0]." AS SELECT * FROM `virtuemart_products` LIMIT ".$limite[0].",".$limite[1]; 
			// Views virtuemart
			//~ $respuesta['ViewVirtuemart']['TextoConsulta'] =$CrearViewVirtuemart;// para debug
			$respuesta['ViewVirtuemart']['consulta'] = $BDRecambios->query($CrearViewVirtuemart);
			$respuesta['ViewVirtuemart']['Queryconsulta'] = $CrearViewVirtuemart;
			// No indica cantidad de item  $BDRecambios->affected_rows;
			// Sin embargo indica true o false 'consulta'
		}
	} else {
		$respuesta['ViewVirtuemart']['consulta'] = false;
	}
	
	if (isset($vistas[1])){
		if ($vistas[1] == 'vista_recambio') {
			$CrearViewVistaRecambio = "CREATE or REPLACE VIEW ".$vistas[1]." AS SELECT r.id, r.IDFabricante, rc.RefFabricanteCru FROM `recambios` AS r, referenciascruzadas AS rc WHERE r.id = rc.RecambioID AND r.IDFabricante = rc.IdFabricanteCru";
			// Views recambio con referencia cruzada.
			//~ $respuesta['ViewRecambio']['TextoConsulta'] = $CrearViewVistaRecambio;// para debug
			$respuesta['ViewRecambio']['consulta'] = $BDRecambios->query($CrearViewVistaRecambio);
			// No indica cantidad de item  $BDRecambios->affected_rows;
			// Si embargo indica true o false 'consulta'
		}
	} else {
		$respuesta['ViewRecambio']['consulta'] =false;
	}
	
	return $respuesta;
	
}
	
	
function BuscarErrorRefNuevo($BDRecambios) {
	// Con esta funcion comprobamos que los datos que tenemos en tabla virtuemart ( referencias recambio y referencia de fabricante) son correctos.
	$resultado = array();
	$consulta1 = "Select product_gtin,product_sku,virtuemart_product_id from virtuemart";
	$busqueda = $BDRecambios->query($consulta1);
	if ($busqueda) {
		//~ $x= 0; // Solo debug
		$i =0;
		while ($producto =$busqueda->fetch_assoc()) {
			// ahora tenemos que buscar ese resultado en vista Recambios y ver si es igual
			$Error = 'NO'; // Variable de control guardar datos o no 
			$Nresultados = 0;
			if (strlen($producto['product_gtin']) >0) {
				$consulta2 = 'Select * from vista_recambio where RefFabricanteCru ="'.trim($producto['product_gtin']).'" and id='.trim($producto['product_sku']);
				$busqueda2 = $BDRecambios->query($consulta2);
				$Nresultados = $busqueda2->num_rows;
				if ($Nresultados == 0 or $Nresultados>1  ){
					// Quiere decir que no es encontro o que hay mas de un resultado.
					$Error = 'SI';
				}
				if (isset($busqueda2)){
				// Liberamos memoria de 
				mysqli_free_result($busqueda2); // Liberamos memoria 
				}
			}
			
			
			if ($Error ==='SI'){
				$resultado[$i]['idRecambio'] = $producto['product_gtin'];
				$resultado[$i]['GTIN-Virtuemart'] = $producto['product_sku'];
				$resultado[$i]['idVirtuemart'] = $producto['virtuemart_product_id'];
				//~ $resultado[$i]['consulta'] = $consulta2;
			$i++;
			}
			// debug
			//~ $resultado[$i]['consulta2'] = $consulta2;
			//~ $x++;
		}
	mysqli_free_result($busqueda); // Liberamos memoria 
	}
	return $resultado;
}

function CopiarDescripcion($ObjRecambio,$BDRecambios,$Reg_Inicial,$TotalRegistro,$intervalo,$ObjRecambio_Cruces,$prefijoJoomla,$BDWebJoomla,$BDVehiculos){
	// En esta funcion estamos en ciclo javascript ObtenerDatosVirtuemart()
	// Parametros:
	//		$ObjRecambio-> Es objeto que utilizamos en Recambios
	// 		$BDRecambios -> Conexion a BD
	//		$BDWebJoomla-> Conexion a BD de Joomla
	//		$BDVehiculos-> Conexion a BD de Vehiculos
	// 		$Reg_Inicial -> Registro desde donde iniciamos la consulta.
	// 		$TotalRegistro-> Total de registros que hay en tabla virtuemart_product.
	// 		$intervalo-> La cantidad de registros que vamos obtener.
	// LO QUE VAMOS HACER ES:
	// Vamos obtener de tabla virtuemart_products :
	// 		virtuemart_product_id -> Id producto de wev
	// 		product_sku: -> id producto de recambio
	// 		product_gtin:-> Referencia de fabricante
	// para luego general el html de la vista plugin de cruces.
	// para guardar en tabla virtuemart_product_es en campo descripcion larga.
	$respuesta = array();
	$Recambio = array();
	$CruceRecambio = array();
	$consulta = 'SELECT `virtuemart_product_id` , `product_sku` , `product_gtin` FROM `virtuemart_products` LIMIT '. $Reg_Inicial.','.$intervalo;
	$resultados = $BDRecambios->query($consulta);
	//~ $Nresultados = $resultado->num_rows;
	if ($resultados) {
		$x=0;
		while ($fila = $resultados->fetch_assoc()) {
			$Recambio[$x]['id'] = $fila['virtuemart_product_id'];
			$Recambio[$x]['idRecambio'] = $fila['product_sku'];
			$Recambio[$x]['RefFabriCru'] = $fila['product_gtin'];

			// ======== AHORA REALIZAMOS ARRAY CRUCESRECAMBIO ============== //
			$htmlRecamCru = '';
			$idBusqueda = $fila['product_sku'];
			$consulta = "SELECT c.`id` , c.`idReferenciaCruz` , c.`idRecambio` , c.`idFabricanteCruz` , f.`Nombre` AS Nfabricante, r.`RefFabricanteCru` AS NRefFabricante FROM `cruces_referencias` AS c, `fabricantes_recambios` AS f, `referenciascruzadas` AS r WHERE f.`id` = c.`idFabricanteCruz`AND r.`id` = c.`idReferenciaCruz` and c.`idRecambio` =". $idBusqueda;
			$ResultadoCrucesRecam = $BDRecambios->query($consulta);
			if ($ResultadoCrucesRecam){
				// Ahora tenemos que montar el html cruces de referencias.
				$CruceRecambio['TotalCruce'] = $ResultadoCrucesRecam->num_rows;
				$i = 0;
				while ($cruce = $ResultadoCrucesRecam->fetch_assoc()) {
					$CruceRecambio[$i]['idFabriCruz']= $cruce['idFabricanteCruz'];
					$CruceRecambio[$i]['idReferenciaCruz']= $cruce['idReferenciaCruz'];
					$CruceRecambio[$i]['FabricanteCru'] = $cruce['Nfabricante'];
					$CruceRecambio[$i]['FabricanteCruRef'] = $cruce['NRefFabricante'];
					$i = $i+1;
				}
			}
			// Ya tengo html de recambios cruzados.
			$htmlRecamCru = $ObjRecambio_Cruces->html_cruce_ref($CruceRecambio);
			$Recambio[$x]['HtmlCruRecambio'] = $htmlRecamCru;
			$x++;
			// ======== AHORA REALIZAMOS ARRAY CRUCESVEHICULOS ============== //
			$CrucesVehiculos = array();
			$idVersiones = array();
			$htmlCruceVehiculo='';
			$tabla= 'cruces_vehiculos';
			$idBusqueda ='RecambioID='.$fila['product_sku'];
			$ResultadoCrucesVehiculos = $ObjRecambio->BusquedaIDUnico($BDRecambios,$idBusqueda,$tabla);
			$consulta = "SELECT * FROM ".$tabla." WHERE ".$idBusqueda;
			if (isset($ResultadoCrucesVehiculos)){
				$TotalCrucesVehiculos = $ResultadoCrucesVehiculos->num_rows;
					if ($TotalCrucesVehiculos > 0) {
						// Si existe cruce entonces realizamos busquedas de cruces
						$i = 0;
						while ($cruce = $ResultadoCrucesVehiculos->fetch_assoc()) {
							$idVersiones[$i]= $cruce['VersionVehiculoID'];
							$i++;
						};
						 $CrucesVehiculos= $ObjRecambio->CrucesVehiculos($BDVehiculos,$idVersiones);
						 $htmlCruceVehiculo = $ObjRecambio_Cruces->html_cruce_vehiculo($CrucesVehiculos,$TotalCrucesVehiculos);
					};
			};
			// ======== AHORA PREPARAMOS PARA COPIAR LUEGO LAS DESCRIPCIONES ============== //
			//Montamos los queremos copiar:
			$DatosRefCruzadas = '<div class="col-md-3">'.$htmlRecamCru.'</div>'.'<div class="col-md-9">'.$htmlCruceVehiculo.'</div>';
			$id = $fila['virtuemart_product_id'];
			$respCopiar = $ObjRecambio->CopiarDescripcion($id,$DatosRefCruzadas,$prefijoJoomla,$BDWebJoomla);
			$Recambio[$x]['descripcion'] = $respCopiar;
			$x++;
		
		}
		
	}	
	// debug
	// Ahora tenemos los recambios que vamos tratar
		// recambios 
		// 		[$x]
		//			['id'] = $fila['virtuemart_product_id']-> Id de producto virtuemart;
		//			['idRecambio'] = $fila['product_sku']-> Id recambio
		//			['RefFabriCru'] = $fila['product_gtin']-> Referencia cruzada de farbicante.
		// 			['CruReamcbio']=> html de los recmabios con los que cruza.
		//			['descripcion'] = descripcion que copiamos.
	$respuesta['consulta'] = $consulta;
	$respuesta['Recambio'] = $Recambio;
	return $respuesta;


}



?>
