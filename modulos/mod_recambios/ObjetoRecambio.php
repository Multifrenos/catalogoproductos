
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

			
			$i = $i+1;
		}
		return $recambios;
    }
    
    
    
    function ConsultaRecambios($BDRecambios,$limite,$desde)
    {
		if ($limite > 0){
			$rango = "LIMIT ".$limite." OFFSET ".$desde;
			} else {
			$rango ="";
		}
        $consulta = "SELECT * FROM `recambios`".$rango;
		$ResRecambios = $BDRecambios->query($consulta);
		 //~ if ($ResRecambios == true){
			//~ $recambios['conexion'] = 'Correcto,consulta todas familias';
			//~ } else {
			//~ $ResRecambios['conexion'] = 'Error '.mysqli_error($BDRecambios);	
			//~ return $resultado;
			//~ // No continuamos..
		//~ }
		return $ResRecambios ;
    }
    
    function RecambioUnico($BDRecambios,$id)
    {
		$consulta = "SELECT * FROM `recambios` WHERE id=".$id;
		$ResRecambios = $BDRecambios->query($consulta);
		 //~ if ($ResRecambios == true){
			//~ $recambios['conexion'] = 'Correcto,consulta todas familias';
			//~ } else {
			//~ $ResRecambios['conexion'] = 'Error '.mysqli_error($BDRecambios);	
			//~ return $resultado;
			//~ // No continuamos..
		//~ }
		return $ResRecambios ;
    }
    
    
    
    
    
}


?>
