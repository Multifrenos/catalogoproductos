
<?php
/*	
 * */


class Recambio
{
	function ObtenerRecambios($ResRecambios)
    {
		$recambios['NItems'] = $ResRecambios->num_rows;
		$i = 0;
		$recambio = array();
		while ($recambio = $ResRecambios->fetch_assoc()) {
			$recambios['items'][$i]['id']= $recambio['id'];
			$recambios['items'][$i]['Descripcion']= $recambio['Descripcion'];
			$recambios['items'][$i]['coste']= $recambio['coste'];
			$recambios['items'][$i]['margen']= $recambio['margen'];
			$recambios['items'][$i]['pvp']= $recambio['pvp'];
			$recambios['items'][$i]['IDFabricante']= $recambio['IDFabricante'];
			// No siempre existe ... 
			if ( isset($recambio['RefFabricanteCru']) ) {
			$recambios['items'][$i]['RefFabricanteCru']= $recambio['RefFabricanteCru'];
			}
			if ( isset($recambio['virtuemart_product_id']) ) {
			$recambios['items'][$i]['IDWeb']= $recambio['virtuemart_product_id'];
			}
			
			$i = $i+1;
		}
		return $recambios;
    }
    
    
    
    function ConsultaRecambios($BDRecambios,$limite,$desde,$filtro)
    {
		$rango = $filtro ;
		if ($limite > 0 ){
			$rango .= " LIMIT ".$limite." OFFSET ".$desde;
		} 
		
		$consulta = "SELECT R.id, Descripcion, coste, margen, pvp, IDFabricante, RC.RefFabricanteCru, VP.virtuemart_product_id FROM recambios R JOIN referenciascruzadas RC ON RC.RecambioID = R.id LEFT JOIN virtuemart_products VP ON VP.product_sku = R.id ".$rango;
        //~ $consulta = "SELECT * FROM `recambios`".$rango;
        
		$ResRecambios = $BDRecambios->query($consulta);
		 if ($ResRecambios == true){
			$recambios['conexion'] = 'Correcto,consulta todas familias';
			} else {
			$ResRecambios['conexion'] = 'Error '.mysqli_error($BDRecambios);
			$ResRecambios['consulta'] = $consulta;
				
			return $ResRecambios;
			// No continuamos..
		}
		//~ echo $consulta;
		return $ResRecambios ;
    }
    
    function BusquedaIDUnico($BDRecambios,$id,$tabla)
    {
		$consulta = 'SELECT * FROM `'.$tabla.'` WHERE '.$id;
		$Resultado = $BDRecambios->query($consulta);
		 if ($Resultado == true){
			$resultado['conexion'] = 'Correcto,consulta todas familias';
			} else {
			$resultado['conexion'] = 'Error '.mysqli_error($BDRecambios);	
			return $resultado;
			// No continuamos..
		}
		return $Resultado ;
    }
    
    function UnicoRegistro ($BDRecambios,$id,$tabla) {
		$consulta = 'SELECT * FROM `'. $tabla.'` WHERE '.$id;
		$QueryUnico = $BDRecambios->query($consulta);
		if (mysqli_error($BDRecambios)) {
			$fila = $QueryUnico;
		} else {
			$fila = $QueryUnico->fetch_assoc();
		}
		$fila['consulta'] = $consulta;
		return $fila ;
	}
    
    function CrucesVehiculos($BDVehiculos,$idVersiones) {
		$tabla = 'vehiculo_versiones';
		$consulta = 'SELECT * FROM `'. $tabla.'` WHERE id='.$idVersiones;
		$QueryUnico = $BDVehiculos->query($consulta);
		if (mysqli_error($BDVehiculos)) {
			$fila = $QueryUnico;
		} else {
			$fila = $QueryUnico->fetch_assoc();
		}
		// Ahora buscamos modelo y marca.
		$consultaMarca = 'SELECT combustible.nombre as Ncombustible,marca.nombre as Nmarca,modelo.nombre as Nmodelo FROM `vehiculo_marcas` as marca, `vehiculo_modelos` as modelo, `vehiculo_combustibles` as combustible WHERE marca.id='.$fila['idMarca'].' and modelo.id='.$fila['idModelo'].' and combustible.id='.$fila['idCombustible'];
		$QueryUnico = $BDVehiculos->query($consultaMarca);
		if (mysqli_error($BDVehiculos)) {
			$filaNombre = $QueryUnico;
		} else {
			$filaNombre = $QueryUnico->fetch_assoc();
		}
		//~ $fila['consulta'] = $consultaMarca;

		$fila['Marca'] = $filaNombre['Nmarca'];
		$fila['Modelo'] = $filaNombre['Nmodelo'];
		$fila['Combustible'] = $filaNombre['Ncombustible'];

		return $fila ;
	}
    
}


?>
