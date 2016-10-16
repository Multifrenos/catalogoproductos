
<?php
/*	
 * */


class Recambio
{
    
    
    function ObtenerRecambios($BDRecambios)
    {
	$recambios= array();
        $consulta = "SELECT * FROM `recambios` LIMIT 40 ";
	$ResRecambios = $this->ConsultaRecambios($BDRecambios,$consulta);
        //~ $ResRecambios = $BDRecambios->query($consulta);
        //~ if ($ResRecambios == true){
		//~ $recambios['conexion'] = 'Correcto,consulta todas familias';
		//~ } else {
		//~ $recambios['conexion'] = 'Error '.mysqli_error($BDRecambios);	
		//~ return $resultado;
		//~ // No continuamos..
	//~ }
	//~ // Ahora tenemos que montar el array de resultado...
	$recambios['NItems'] = $ResRecambios->num_rows;
	$i = 0;
	$recambio = array();
	while ($recambio = $ResRecambios->fetch_assoc()) {
		$recambios['items'][$i]['id']= $recambio['id'];
		$recambios['items'][$i]['Descripcion']= $recambio['Descripcion'];
		$recambios['items'][$i]['coste']= $recambio['coste'];
		$recambios['items'][$i]['margen']= $recambio['margen'];
		$recambios['items'][$i]['pvp']= $recambio['pvp'];

		
		$i = $i+1;
	}
	return $recambios;
    }
    
    function ConsultaRecambios($BDRecambios,$consulta)
    {
	$recambios = array();
	$ResRecambios = $BDRecambios->query($consulta);
	 if ($ResRecambios == true){
		$recambios['conexion'] = 'Correcto,consulta todas familias';
		} else {
		$recambios['conexion'] = 'Error '.mysqli_error($BDRecambios);	
		return $resultado;
		// No continuamos..
	}
	//~ $recambios['NItems'] = $ResRecambios->num_rows;
	return $ResRecambios ;
    }
    
    
    
    
}


?>
