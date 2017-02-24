<?php
// Objeto para realizar consultas.
// Operaciones comunes
class ConsultaBD 
{
	
	function borrar($nombretabla, $BD) 
	{
		// Eliminamos registros
		$consulta = "Delete from " . $nombretabla;
			if ($BD->query($consulta)) {
				$respuesta ="Eliminado Registros de tabla ".$nombretabla;
			} else {
				$respuesta= "Error deleting record: " . $BD->error;
			}
		return $respuesta;
  	}

	function contarRegistro($BD,$nombretabla,$whereC) {
    $array = array();
    $consulta = "SELECT * FROM ". $nombretabla.$whereC;
    $consultaContador = $BD->query($consulta);
	$array['NItems'] = $consultaContador->num_rows;
	//~ $array['Consulta'] = $consulta;

   	return $array['NItems'];
	}
	
	function registroLineas($BD,$nombretabla,$campo,$whereC) {
    $campos =implode(",",$campo);
    $array = array();
    $consulta = "SELECT ".$campos." FROM ". $nombretabla.$whereC;
	$QueryConsulta = $BD->query($consulta);
	$array['NItems'] =  $QueryConsulta->num_rows;
	 $i=0 ;
	while ($row_planets = $QueryConsulta->fetch_assoc()) {
        $array[$i]["id"] = $row_planets['RefFabPrin'];
        $array[$i]["linea"] = $row_planets['linea'];
        $i++;
    }
   	return $array;
	}
	
	


}


?>
