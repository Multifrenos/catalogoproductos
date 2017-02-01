<?php
// Objeto para realizar consultas.
// Operaciones comunes
class ConsultaImportar 
{
	
	function borrar($nombretabla, $BDImportRecambios) 
	{
    $consulta = "Delete from " . $nombretabla;
    mysqli_query($BDImportRecambios, $consulta);
    return $nombretabla;
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
