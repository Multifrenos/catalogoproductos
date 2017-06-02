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
 

function crearVistaRecambioCruce() {
	$consulta = "SELECT r.id, r.IDFabricante, rc.RefFabricanteCru FROM `recambios` AS r, referenciascruzadas AS rc WHERE r.id = rc.RecambioID AND r.IDFabricante = rc.IdFabricanteCru";
	
	$consulta2= "SELECT concat( v.product_gtin, ':', v.`product_sku` ) AS referencias, v.`virtuemart_product_id` , r.id
FROM `virtuemart_products` AS v
LEFT JOIN vista_recambio AS r ON CONCAT( r.RefFabricanteCru, ':', r.id ) = concat( v.product_gtin, ':', v.`product_sku` )
WHERE r.id IS NULL
ORDER BY `r`.`id` ASC  ";// Para mostrar aquellos registros que no coincide virtuemart con recambios, ya que concat( v.product_gtin, ':', v.`product_sku` ) es distinto a CONCAT( r.RefFabricanteCru, ':', r.id )

	
	}
	
?>
