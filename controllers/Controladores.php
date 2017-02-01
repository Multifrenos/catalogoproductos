
<?php
/*	 Es crear un controlador de consultas comunes para varios modulos.
 * */


class ControladorComun 
{
     function InfoTabla ($Bd,$tabla){
		// Funcion que nos proporciona informacion de la tabla que le indicamos
		/* Nos proporciona informacion como nombre tabla, filas, cuando fue creada, ultima actualizacion .. y mas campos interesantes:
		 * Ejemplo de print_r de virtuemart_products 
		 * 		[Name] => virtuemart_products  // Normal ya que el prefijo ....
		 *    	[Rows] => Numero registros  // ESTE ES IMPORTANTE, el que analizamos inicialmente.
		 *    	[Create_time] => 2016-10-31 18:23:52 // Normal ya que nunca coincidira... se crearía fechas distintas.
		 *    	[Update_time] => 2016-10-31 20:46:35 // Lo recomendable que la hora Update ser superior en nuestra BD , pero no siempre será
		*/
		$fila = array();
		if ($tabla != ''){
			// Quiere decir que queremos consultar informa todas las tablas.
				$tablas = 'WHERE `name`="'.$tabla.'"';
		} else {
			// Quiere decir que queremos consultar informa un tabla
				$tablas = '';
		}
		$consulta = 'SHOW TABLE STATUS '. $tablas;
		$Queryinfo = $Bd->query($consulta);
		// Hay que tener en cuenta que no produce ningún error... 
		$Ntablas = $Bd->affected_rows   ;
		if ($Ntablas == 0) {
			$fila ['error'] = 'Error tabla no encontrada - '.$tablas;
		} else {
			$fila = $Queryinfo->fetch_assoc();
		}
		$fila['consulta'] = $consulta;
		
		return $fila ;
		
	}
	
	function SincronizarWeb ($BDRecambios,$BDWebJoomla,$prefijoJoomla) {
		// Objetivo es que llamando a esta funcion compruebe si esta correcta la sincronizacion con la web.
		
		// Consultamos datos de BD web de tabla virtuemart_products y comparamos con nuestra tabla virtuemart_products en BDRecambios.
		// y obtenemos diferencia, siempre va haber diferencias, lo que se trata es de ver si hay los mismo registros principalmente.
		
		// Consulta en BD WEB
		$tablaVirt= $prefijoJoomla."_virtuemart_products";
		$InfoProdVirt	=	$this->InfoTabla($BDWebJoomla,$tablaVirt);
				
		// Consulta en BD Recambios
		$NueVirt="virtuemart_products";
		$InfoNueVirt	=	$this->InfoTabla($BDRecambios,$NueVirt);
		/* Cremos array diferencias con los datos de tabla (BDJoomla virtuemart) y la nuestra:
		 * 		[Name] => virtuemart_products  // Normal ya que el prefijo ....
		 *    	[Rows] => Numero registros  // ESTE ES IMPORTANTE, el que analizamos inicialmente.
		 *    	[Create_time] => 2016-10-31 18:23:52 // Normal ya que nunca coincidira... se crearía fechas distintas.
		 *    	[Update_time] => 2016-10-31 20:46:35 // En BD Recambio deberías ser superior, para estar seguros que actulizada
		*/
		$DifVirtuemart	=	 array_diff($InfoProdVirt,$InfoNueVirt);
		
		
		
		// Debug 
		// Para corregir posibles errores
		//~ $DifVirtuemart[0] = $InfoProdVirt;
		//~ $DifVirtuemart[1] = $InfoNueVirt;

		return $DifVirtuemart;	
	}
    
    
   
	function VerConexiones ($Conexiones){
		// Objetivo comprobar si las conexiones son correctas.
		$htmlError = '';
		foreach ($Conexiones as $conexion) {
				if ($conexion['conexion'] == 'Error'){
					$htmlError .= 	'Error de conexion en la BD '.$conexion['NombreBD'].'<br/>'
									.'¡¡Revisa configuracion! <br/>';
				}
		}
		return $htmlError ;
	}
}


?>
