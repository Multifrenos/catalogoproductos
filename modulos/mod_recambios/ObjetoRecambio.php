
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
		$resultado = array();
		$LidVersiones = implode(',',$idVersiones);
		$ConsultaMultitabla= "SELECT v.`id`,v.`idMarca`,Marca.nombre as Nmarca, v.`idModelo`,m.nombre as Nmodelo, v.`nombre` as Nversion, v.`idTipo`, v.`idCombustible`,c.nombre as Ncombustible, v.`fecha_inicial`, v.`fecha_final`, v.`kw`, v.`cv`, v.`cm3`, v.`ncilindros` 
FROM (((`vehiculo_versiones` as v LEFT JOIN `vehiculo_modelos` as m ON v.idModelo = m.`id`) LEFT JOIN `vehiculo_marcas` as Marca ON v.idMarca = Marca.id) LEFT JOIN `vehiculo_combustibles` as c ON c.id = v.idCombustible) WHERE v.id in (".$LidVersiones.") order by Marca.nombre,m.nombre,v.nombre,v.fecha_inicial ASC";
		$resultados = $BDVehiculos->query($ConsultaMultitabla);
		if ($resultados){
			while ($vehiculo = $resultados->fetch_assoc()) {
				$resultado[] = $vehiculo;
			}
		} else {
			$resultado['consulta'] = $ConsultaMultitabla;
			$resultado['Error'] = 'Error en consulta o no existe cruce';
		}
		
		
		
		
		return $resultado ;
	}
	
	
}


?>
