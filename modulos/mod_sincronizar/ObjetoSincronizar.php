 
<?php
class ObjSincronizar 
{
 function CopiarTablasWeb ($BDRecambios,$BDWebJoomla,$BDNombre1,$BDNombre2,$prefijoJoomla){
		// Objetivo copia tabla virtuemart_products en Recambios.
		$consulta = 'INSERT INTO `'.$BDNombre1.'`.`virtuemart_products` SELECT * FROM `'.$BDNombre2.'`.`'.$prefijoJoomla.'_virtuemart_products`';
		if (!$BDRecambios->query($consulta)){
			$resultado ='Error '.$BDRecambios->errno.'<br/>Consulta:'.$consulta;
		} else {
			$Queryinfo = $BDRecambios->query($consulta);
			$resultado ='Copia tabla virtuemart_products'; // $BDRecambios->affected_rows; muestra me devuelve -1 como si hubiera un error en la consulta.. 
		}
		return $resultado;
	}
	
}	
	
?>
