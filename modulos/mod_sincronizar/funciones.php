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
	// En esta funcion lo que hacermos es crear Vistas para realizar consultas mas rápidas.
	// Como vamos utilizar esta funcion varias funciones, en proceso, donde vamos hacer una vista de tabla por trozos, por eso 
	// creamos los parametros vistas y limite.
	// Los parametros de fucion:
	// 		$BDRecambios ( BD que creamos vista)
	// 		$vistas -> Esté puede ser
	//				$vista[0] = virtuemart -> Crea vista tabla virtuemart_productos pero con una cantida registros ( limite)
	// 				$vista[1] = vista_recambio -> Que tiene id Recambio, IdFabricante y RefFabricanteCru de las 
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
	//~ $respuesta['limite'] = $limite; // para debug
	//~ $respuesta['vistas'] = $vistas; // para debug
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
?>
