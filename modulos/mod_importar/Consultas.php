<?php
// Objeto para realizar consultas.
// Operaciones comunes
class ConsultaImportar 
{
	
	function borrar($nombretabla, $BDImportRecambios) 
	{
    // Eliminamos registros
    $consulta = "Delete from " . $nombretabla;
		if ($BDImportRecambios->query($consulta)) {
			$respuesta ="Eliminado Registros de tabla ".$nombretabla;
		} else {
			$respuesta= "Error deleting record: " . $BDImportRecambios->error;
		}
		return $respuesta;
  	}

	function contarRegistro($BDImportRecambios,$nombretabla,$whereC) {
    $array = array();
    $consulta = "SELECT * FROM ". $nombretabla.$whereC;
    $consultaContador = $BDImportRecambios->query($consulta);
	$array['NItems'] = $consultaContador->num_rows;
	//~ $array['Consulta'] = $consulta;

   	return $array['NItems'];
	}


}


?>
