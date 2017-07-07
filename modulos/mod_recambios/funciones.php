<?php 


// Funciones para vista Recambio unico.
function CopiarDescripcion($id,$DatosRefCruzadas,$prefijoJoomla,$BDWebJoomla) {
	// Function para añadir contenido a descripcion de tabla virtuemart_products_es_es.

	
	$resumen = array();
	$tabla = $prefijoJoomla."_virtuemart_products_es_es";
	// Primero BORRAMOS datos en descripcion del ID, antes añadir.
	$whereC= "  WHERE `virtuemart_product_id`=".$id;
	$consulta = "UPDATE ".$tabla." SET `product_desc`=''".$whereC;
	$resultado = $BDWebJoomla->query($consulta);
	if ($resultado){
		// Tuvo respuesta la consulta
		$Afectado1 = $BDWebJoomla->affected_rows;
	} else {
		// No tuvo respuesta la consulta, esto sucede cuando no tiene descripción.
		// De momento no hago nada...
		$Afectado1 = 0;
	}
	// Ahora dividimos los datos recibidos ya que si es muy grande el update falla.
	$DatosArray = str_split($DatosRefCruzadas,5000);
	foreach ( $DatosArray as $Datos) {
		$consulta = "UPDATE ".$tabla." SET `product_desc`= concat(product_desc,'".$Datos."')".$whereC;
		$resultado = $BDWebJoomla->query($consulta);
	}
	$resumen['RowsAfectados']= $Afectado1;
	
	// para debug 
	//~ $resumen['consulta']=$consulta;
	//~ $resumen ['SeDividioString'] =count($DatosArray); // Ojo si entento devolver string puede generar un error Json.parse , pienso que por muy pesado.

	return $resumen; 
}

?>
