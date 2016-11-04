
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
		if ($tabla == ''){
			// Quiere decir que queremos consultar informa todas las tablas.
				$tablas = 'WHERE `name`="'.$tabla.'"';
		} else {
			// Quiere decir que queremos consultar informa un tabla
				$tablas = '';
		}
		$consulta = 'SHOW TABLE STATUS '. $tablas;
		$Queryinfo = $Bd->query($consulta);
		if (mysqli_error($Bd)) {
			$fila = $Queryinfo;
		} else {
			$fila = $Queryinfo->fetch_assoc();
		}
		$fila['consulta'] = $consulta;
		return $fila ;
		
	}
	
	function SincronizarWeb ($BDRecambios,$BDWebJoomla) {
		// Objetivo es que llamando a esta funcion compruebe si esta correcta la sincronizacion con la web.
		
		// Consultamos datos de BD web de tabla virtuemart_products y comparamos con nuestra tabla virtuemart_products en BDRecambios.
		// y obtenemos diferencia, siempre va haber diferencias, lo que se trata es de ver si hay los mismo registros principalmente.
		
		// Consulta en BD WEB
		$tablaVirt="xcv7n_virtuemart_products";
		$InfoProdVirt	=	$this->InfoTabla($BDWebJoomla,$tablaVirt);
		// Consulta en BD Recambios
		$NueVirt="virtuemart_products";
		$InfoNueVirt	=	$this->InfoTabla($BDRecambios,$NueVirt);
		// Array de diferencias.
		$DifVirtuemart	=	 array_diff($InfoNueVirt, $InfoProdVirt);
		/* Recuerda con los datos de Nuestra tabla (BDRecambios virtuemart) que sean diferentes:
		 * 		[Name] => virtuemart_products  // Normal ya que el prefijo ....
		 *    	[Rows] => Numero registros  // ESTE ES IMPORTANTE, el que analizamos inicialmente.
		 *    	[Create_time] => 2016-10-31 18:23:52 // Normal ya que nunca coincidira... se crearía fechas distintas.
		 *    	[Update_time] => 2016-10-31 20:46:35 // Lo recomendable que la hora Update ser superior en nuestra BD , pero no siempre será
		*/
			
		return $DifVirtuemart;	
	}
    
    
    function CopiarTablasWeb ($BDRecambios,$BDWebJoomla,$BDNombre1,$BDNombre2){
		// Objetivo copia tabla virtuemart_products en Recambios.
		$consulta = 'INSERT INTO `'.$BDNombre1.'`.`virtuemart_products` SELECT * FROM `'.$BDNombre2.'`.`xcv7n_virtuemart_products`';
		$Textoerr= 'No';
		if (!$BDRecambios->query($consulta)){
					$Textoerr = $BDRecambios->errno;
		} else {
		$Queryinfo = $BDRecambios->query($consulta);
		}
		return $Textoerr;
	}
}


?>
