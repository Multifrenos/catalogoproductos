<?php
// Objeto para realizar consultas.
// Operaciones comunes
class ConsultaBD 
{
	function crearTabla ($nombretabla,$BD,$campos) 
	{
		// Creamos una tabla si no existe, con los campos que le indiquemos en el array
		// Ejemplo:
		// de $nombreBD.'.'.$nombretabla --> importRecambio.
		// Ejemplo de array campos:
		// 		$campos[1] = { 	"nombre": "id",
		//						"tipo": "INT NOT NULL AUTO_INCREMENT",
		//						"Primary:" "si"}
		//		$campos[2] = {"nombre": "descripcion",
		//						"tipo": "VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL",
		//						"Primary:" "si"}
		$consulta = "CREATE TABLE IF NOT EXISTS ".$nombreBD.'.'.$nombretabla. ' ('.$campoAuto.','.   $restocampos.','.'PRIMARY KEY ('.$campoKey.')) ENGINE = InnoDB;';
		
	}
	function borrarTabla($nombretabla, $BD)
	{
		//Eliminamos la tabla
		$consulta ="DROP DATABASE IF EXISTS ".$nombretabla;
		if ($BD->query($consulta)) {
				$respuesta ="Eliminado la tabla completamente ".$nombretabla;
			} else {
				$respuesta= "Error al eliminar la tabla: " . $BD->error;
		}
		return $respuesta;
	}
	
	function borrarContenido($nombretabla, $BD) 
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
		if ($BD->query($consulta)) {
			$array['NItems'] = $consultaContador->num_rows;
		} else {
			// Quiere decir que hubo error en la consulta.
			$array['consulta'] = $consulta;
			$array['error'] = $BD->error;
		}
		return $array['NItems'];

	}
	
	function registroLineas($BD,$nombretabla,$campo,$whereC) {
		// Inicializamos variables
		$i= 0;
		$campos =implode(",",$campo);
		$array = array();
		$array['NItems']  = 0 ; //Evitamos error
		$consulta = "SELECT ".$campos." FROM ". $nombretabla.$whereC;
		$QueryConsulta = $BD->query($consulta);
		$array['consulta'] = $consulta;

		if ($BD->query($consulta)) {
			$array['NItems'] =  $QueryConsulta->num_rows;
			while ($row_planets = $QueryConsulta->fetch_assoc()) {
			foreach ($campo as $NombreCampo){ 
				$array[$i][$NombreCampo] = $row_planets["$NombreCampo"];
			}
			$i++;
			
			}
		
		} else {
			// Quiere decir que hubo error en la consulta.
			$array['consulta'] = $consulta;
			$array['error'] = $BD->error;
		}
		// Ahora creamos array con los datos de los campos.
		//~ echo '<pre>';
		//~ print_r($campo);
		//~ echo '</pre>';
		//~ 
		return $array;
	}
	
	function distintosCampo($BD,$nombretabla,$campo,$whereC) {
		// Inicializamos variables
		$i= 0;
		$array = array();
		$array['NItems']  = 0 ; //Evitamos error
		$consulta = "SELECT DISTINCT(`".$campo."`) FROM ". $nombretabla.$whereC;
		$QueryConsulta = $BD->query($consulta);
		$array['consulta'] = $consulta;

		if ($BD->query($consulta)) {
			$array['NItems'] =  $QueryConsulta->num_rows;
			while ($row_planets = $QueryConsulta->fetch_assoc()) {
				$array[$i][$campo] = $row_planets["$campo"];
			$i++;
			
			}
		
		} else {
			// Quiere decir que hubo error en la consulta.
			$array['consulta'] = $consulta;
			$array['error'] = $BD->error;
		}
		return $array;
	}


}


?>
