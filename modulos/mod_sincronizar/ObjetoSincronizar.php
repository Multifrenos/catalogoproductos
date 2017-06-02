 
<?php
class ObjSincronizar 
{
 function CopiarTablasWeb ($BDRecambios,$BDWebJoomla,$BDNombre1,$BDNombre2,$prefijoJoomla){
		// Objetivo copia tabla virtuemart_products en Recambios.
		$consulta = 'INSERT INTO `'.$BDNombre1.'`.`virtuemart_products` SELECT * FROM `'.$BDNombre2.'`.`'.$prefijoJoomla.'_virtuemart_products`';
		// La respuesta va ser un array
		// $respuesta[resultado] = "Correcto/Error"
		// $respuesta[descripcion] = "Texto explicativo.... Correcto o que error fue"

		
		if (!$BDRecambios->query($consulta)){
			$respuesta['resultado'] = "Error";
			$respuesta['descripcion'] ='Error '.$BDRecambios->errno.'<br/>Consulta:'.$consulta;
		} else {
			//~ La consulta ya se ejecuta... 
			$respuesta['resultado'] = "Correcto";
			$respuesta['descripcion'] ='Copia tabla virtuemart_products, numero registros copiado '.$BDRecambios->affected_rows; 
			// muestra me devuelve -1 como si hubiera un error en la consulta.. 
		}
		return $respuesta;
	}
	
}	
	
?>
