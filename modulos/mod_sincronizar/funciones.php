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
	// Como vamos utilizar esta funcion en proceso, donde vamos hacer una vista de tabla por trozos, por eso 
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
	if ($vistas[0] = 'virtuemart'){
		$CrearViewVirtuemart = "CREATE or REPLACE VIEW ".$vistas[0]." AS SELECT * FROM `virtuemart_products` LIMIT ".$limite[0].",".$limite[1]; 
	}
	if ($vistas[1] = 'vista_recambio'){
		$CrearViewVistaRecambio = "CREATE or REPLACE VIEW ".$vistas[1]." AS SELECT r.id, r.IDFabricante, rc.RefFabricanteCru FROM `recambios` AS r, referenciascruzadas AS rc WHERE r.id = rc.RecambioID AND r.IDFabricante = rc.IdFabricanteCru";
	}
	if ($vistas[0] = 'virtuemart') {
		// Views virtuemart
		//~ $respuesta['ViewVirtuemart']['TextoConsulta'] =$CrearViewVirtuemart;// para debug
		$respuesta['ViewVirtuemart']['consulta'] = $BDRecambios->query($CrearViewVirtuemart);
		// No indica cantidad de item  $BDRecambios->affected_rows;
		// Si embargo indica true o false 'consulta'

	}
	if ($vistas[1] = 'vista_recambio') {
		// Views recambio con referencia cruzada.
		//~ $respuesta['ViewRecambio']['TextoConsulta'] = $CrearViewVistaRecambio;// para debug
		$respuesta['ViewRecambio']['consulta'] = $BDRecambios->query($CrearViewVistaRecambio);
		// No indica cantidad de item  $BDRecambios->affected_rows;
		// Si embargo indica true o false 'consulta'
	}
	//~ $respuesta['limite'] = $limite; // para debug
	//~ $respuesta['vistas'] = $vistas; // para debug
	return $respuesta;
	
	
}
	
function BuscarErrorRefVirtuemart() {
	// Realizamos una consulta donde nos muestra aquellos productos que NO existe idRecambio y ReferenciaCruzada.
	$consulta2= "SELECT concat( v.product_gtin, ':', v.`product_sku` ) AS referencias, v.`virtuemart_product_id` , r.id FROM `virtuemart_products` AS v LEFT JOIN vista_recambio AS r ON CONCAT( r.RefFabricanteCru, ':', r.id ) = concat( v.product_gtin, ':', v.`product_sku` ) WHERE r.id IS NULL";
	$respuesta['consulta'] = $consulta2; // para debug
	
	return $respuesta;
	}
	
	
?>
