<?php
// Objeto para realizar consultas.
// Operaciones comunes
class ConsultaImportar 
{
	
	function borrar($nombretabla, $BDImportRecambios) 
	{
    // Eliminamos registros
    $consulta = "Delete from " . $nombretabla;
		if (mysqli_query($BDImportRecambios, $consulta)) {
			$respuesta ="Registros de tabla ".$nombretabla;
		} else {
			$repuesta= "Error deleting record: " . mysqli_error($conn);
		}
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
