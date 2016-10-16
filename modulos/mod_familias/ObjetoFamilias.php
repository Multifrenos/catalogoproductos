
<?php
class Familias
{
    
    
    function LeerFamilias($BDRecambios)
    {
		$familias= array();
        $consulta = "SELECT * FROM `familias_recambios` ORDER BY `Familia_es` ASC ";
        $ResFamilias = $BDRecambios->query($consulta);
        if ($ResFamilias == true){
			$familias['conexion'] = 'Correcto,consulta todas familias';
			} else {
			$familias['conexion'] = 'Error '.mysqli_error($BDRecambios);	
			return $resultado;
			// No continuamos..
		}
		// Ahora tenemos que montar el array de resultado...
		$familias['NItems'] = $ResFamilias->num_rows;
		$i=0;
		while ($familia = $ResFamilias->fetch_assoc()) {
					
					if ($familia['id_Padre'] == 0) {
						$familias['items'][$i]['id']= $familia['id'];
						$familias['items'][$i]['Nombre']= $familia['Familia_es'];
						//~ $familias[$i]['NumeroHijos'] = 0;
						// Ahora consultamos cuando hijos tiene.. si los tienes	
						
						$hijos  = $this->ConsultaHijos($familia['id'],$BDRecambios);
						if ($hijos['conexion'] = 'Correcta') {
							$familias['items'][$i]['NumeroHijos'] = $hijos['NHijos'] ;
							// Si tiene hijos creamos items de hijos...
							if ($hijos['NHijos'] > 0 ){
							$familias['items'][$i]['Hijos'] = $hijos['items'] ;
							}

						} else {
							$familias['items'][$i]['NumeroHijos'] = $hijos['conexion'];
						}
					$i = $i + 1 ;
					}
					
		}
		return $familias;
 
    }
    
    
    function ConsultaHijos($id,$BDRecambios){
	$hijos = array();
	$consulta = "SELECT * FROM `familias_recambios` WHERE `id_Padre` =$id";
	$ResHijos = $BDRecambios->query($consulta);
        if ($ResHijos == true){
			$hijos['conexion'] = 'Correcto';

		} else {
			$hijos['conexion'] = 'Incorrecto';
			return $hijos;
		}
	$hijos['NHijos'] = $ResHijos->num_rows;
	// Ahora creamos array con lo id, hijos..
	$i = 0;
	$registros = array();
	while ($hijo = $ResHijos->fetch_assoc()) {
	   $registros[$i]['id'] = $hijo['id'];
   	   $registros[$i]['Nombre'] = $hijo['Familia_es'];

	   $i = $i +1;
	}
	
	$hijos['items'] = $registros;
	return $hijos ;
	}
    
    
    
    
    
}


?>
