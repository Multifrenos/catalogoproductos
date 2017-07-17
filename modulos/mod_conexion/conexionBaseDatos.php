<?php 
// Creamos Array $Conexiones para obtener datos de conexiones
// teniendo en cuenta que le llamo a conexiones  a cada conexion a la Bases de Datos..
$Conexiones = array(); 
$Conexiones [1]['NombreBD'] = "importarrecambios";
$Conexiones [2]['NombreBD'] = "recambios";
$Conexiones [3]['NombreBD'] = $nombreBDJoomla;
$Conexiones [4]['NombreBD'] = "vehiculos";

// Array de conexion de obtenemos [Numero conexion]
//		[NombreBD] = Nombre de la base datos..
// 		[conexion] = Correcto o Error
//		[respuesta] = " Respuesta de conexion de error o de Correcta"
// 		[tablas] = " Tablas que existen en la conexion"


// El prefijo joomla es una variable configuracion.
//~ $Conexiones [3]['prefijoJoomla'] = $prefijoJoomla;



/************************************************************************************************/
/*************   Realizamos conexion de base de datos de ImportarRecambios.          ************/
/************************************************************************************************/
$nameBD = $Conexiones [1]['NombreBD'];
$BDImportRecambios = new mysqli("localhost", "coches", "coches", $nameBD);
// Como connect_errno , solo muestra el error de la ultima instrucción mysqli, tenemos que crear una propiedad, en la que 
// está vacía, si no se produce error.
if ($BDImportRecambios->connect_errno) {
	$Conexiones [1]['conexion'] = 'Error';
	$Conexiones [1]['respuesta']=$BDImportRecambios->connect_errno.' '.$BDImportRecambios->connect_error;
	$BDImportRecambios->controlError = $BDImportRecambios->connect_errno.':'.$BDImportRecambios->connect_error;
} else {
	$Conexiones [1]['conexion'] ='Correcto';
	$Conexiones [1]['respuesta']= $BDImportRecambios->host_info;
	/** cambio del juego de caracteres a utf8 */
	mysqli_query ($BDImportRecambios,"SET NAMES 'utf8'");
	$sql = "SHOW TABLES FROM ".$nameBD;
	$resultado = $BDImportRecambios->query($sql);
	$tablas = array();
	$i = 0;
	while ($fila = $resultado->fetch_row()) {
		$i++;
		$tablas[$i] = $fila[0];
	}
	$Conexiones[1]['tablas'] =$tablas;

}

/************************************************************************************************/
/*****************   Realizamos conexion de base de datos de Recambios.          ****************/
/************************************************************************************************/
$nameBD = $Conexiones [2]['NombreBD'];
$BDRecambios = @new mysqli("localhost", "coches", "coches", $nameBD);
// Como connect_errno , solo muestra el error de la ultima instrucción mysqli, tenemos que crear una propiedad, en la que 
// está vacía, si no se produce error.

if ($BDRecambios->connect_errno) {
		$Conexiones [2]['conexion'] = 'Error';
		$Conexiones [2]['respuesta'] = $BDRecambios->connect_errno.' '.$BDRecambios->connect_error;
		$BDRecambios->controlError = $BDRecambios->connect_errno.':'.$BDRecambios->connect_error;
} else {
	/** cambio del juego de caracteres a utf8 */
	$Conexiones [2]['conexion'] ='Correcto';
	$Conexiones [2]['respuesta'] = $BDRecambios->host_info;
	mysqli_query ($BDRecambios,"SET NAMES 'utf8'");	
	$sql = "SHOW TABLES FROM ".$nameBD;
	$resultado = $BDRecambios->query($sql);
	$tablas = array();
	$i = 0;
	while ($fila = $resultado->fetch_row()) {
		$i++;
		$tablas[$i] = $fila[0];
	}
	$Conexiones[2]['tablas'] =$tablas;

}



/************************************************************************************************/
/*****************   Realizamos conexion de base de datos Web Multipiezas        ****************/
/************************************************************************************************/
// Hay que tener en cuenta que los prefijos de instalación cambian
// que los usuarios y contraseña cambian según instalacion, esto debería haber un 
// proceso configuracion y instalacion.
$nameBD = $Conexiones [1]['NombreBD'];
$BDWebJoomla = @new mysqli("localhost", "coches", "coches", $nombreBDJoomla);
// Como connect_errno , solo muestra el error de la ultima instrucción mysqli, tenemos que crear una propiedad, en la que 
// está vacía, si no se produce error.

if ($BDWebJoomla->connect_errno) {
		$Conexiones [3]['conexion'] = 'Error';
		$Conexiones [3]['respuesta'] = $BDWebJoomla->connect_errno.' '.$BDWebJoomla->connect_error;
		$BDWebJoomla->controlError = $BDWebJoomla->connect_errno.':'.$BDWebJoomla->connect_error;
} else {
/** cambio del juego de caracteres a utf8 */
	$Conexiones [3]['conexion'] = 'Correcto';
	$Conexiones [3]['respuesta']= $BDWebJoomla->host_info;
	mysqli_query ($BDWebJoomla,"SET NAMES 'utf8'");
	$sql = "SHOW TABLES FROM ".$nameBD;
	$resultado = $BDWebJoomla->query($sql);
	$tablas = array();
	$i = 0;
	while ($fila = $resultado->fetch_row()) {
		$i++;
		$tablas[$i] = $fila[0];
	}
	$Conexiones[3]['tablas'] =$tablas;
}



/************************************************************************************************/
/*****************   Realizamos conexion de base de datos de Recambios.          ****************/
/************************************************************************************************/
$BDVehiculos = @new mysqli("localhost", "coches", "coches", "vehiculos");
// Como connect_errno , solo muestra el error de la ultima instrucción mysqli, tenemos que crear una propiedad, en la que 
// está vacía, si no se produce error.

if ($BDVehiculos->connect_errno) {
		$Conexiones [4]['conexion'] = 'Error';
		$Conexiones [4]['respuesta'] = $BDVehiculos->connect_errno.' '.$BDVehiculos->connect_error;
		$BDVehiculos->controlError = $BDVehiculos->connect_errno.':'.$BDVehiculos->connect_error;
} else {
	/** cambio del juego de caracteres a utf8 */
	$Conexiones [4]['conexion'] ='Correcto';
	$Conexiones [4]['respuesta'] = $BDVehiculos->host_info;
	mysqli_query ($BDVehiculos,"SET NAMES 'utf8'");	
	$sql = "SHOW TABLES FROM ".$nameBD;
	$resultado = $BDWebJoomla->query($sql);
	$tablas = array();
	$i = 0;
	while ($fila = $resultado->fetch_row()) {
		$i++;
		$tablas[$i] = $fila[0];
	}
	$Conexiones[4]['tablas'] =$tablas;
}






?>
