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
	// Los parametros de fucion:
	// 		$BDRecambios ( BD que creamos vista)
	// 		$vistas -> Esté puede ser
	//				$vista[1] = virtuemart -> Crea vista tabla virtuemart_productos pero con una cantida registros ( limite)
	// 				$vista[2] = vista_recambio -> Que tiene id Recambio, IdFabricante y RefFabricanteCru de las 
	//				dos tablas ( recambios y referenciascruzadas).
	// 		$limite-> Array que indicar el limite de la vista[1]
	//				limite['inicial']
	// 				limite['final']
	 
	$respuesta =array();
	// CREAR VISTA NECESARIAS.
	if ($vista[1] = 'virtuemart'){
		$CrearViewVirtuemart = "CREATE VIEW ".$vista[1]." AS SELECT * FROM `virtuemart_products` LIMIT ".$limite['inicial'].",".$limite['final']; 
	}
	if ($vista[2] = 'vista_recambio'){
		$CrearViewVistaRecambio = "CREATE or REPLACE VIEW ".$vista[2]." AS SELECT r.id, r.IDFabricante, rc.RefFabricanteCru FROM `recambios` AS r, referenciascruzadas AS rc WHERE r.id = rc.RecambioID AND r.IDFabricante = rc.IdFabricanteCru";
	}
	if ($vista[1] = 'virtuemart') {
		// Views virtuemart
		$respuesta['ViewVirtuemart']['items'] = $BDRecambios->query($CrearViewVirtuemart);
		$respuesta['ViewVirtuemart']['Nitems'] = $BDRecambios->affected_rows;
	}
	if ($vista[2] = 'vista_recambio') {
		// Views recambio con referencia cruzada.
		$respuesta['ViewRecambio']['items'] = $BDRecambios->query($CrearViewVistaRecambio);
		$respuesta['ViewRecambio']['Nitems'] = $BDRecambios->affected_rows;
	}
	
	return $respuesta;
	
	
}
	
function BuscarErrorRefVirtuemart() {
	$consulta2= "SELECT concat( v.product_gtin, ':', v.`product_sku` ) AS referencias, v.`virtuemart_product_id` , r.id
FROM `virtuemart_products` AS v
LEFT JOIN vista_recambio AS r ON CONCAT( r.RefFabricanteCru, ':', r.id ) = concat( v.product_gtin, ':', v.`product_sku` )
WHERE r.id IS NULL";// Para mostrar aquellos registros que no coincide virtuemart con recambios, ya que concat( v.product_gtin, ':', v.`product_sku` ) es distinto a CONCAT( r.RefFabricanteCru, ':', r.id )
	}
	
	
?>
