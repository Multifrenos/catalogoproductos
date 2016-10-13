<?php 
/************************************************************************************************/
/*************   Realizamos conexion de base de datos de ImportarRecambios.          ************/
/************************************************************************************************/
$BDImportRecambios = new mysqli("localhost", "coches", "coches", "importarrecambios");
// Como connect_errno , solo muestra el error de la ultima instrucción mysqli, tenemos que crear una propiedad, en la que 
// está vacía, si no se produce error.
if ($BDImportRecambios->connect_errno) {
		$BDImportRecambios->controlError = $BDImportRecambios->connect_errno.':'.$BDImportRecambios->connect_error;
} else {
/** cambio del juego de caracteres a utf8 */
 mysqli_query ($BDImportRecambios,"SET NAMES 'utf8'");
}
/************************************************************************************************/
/*****************   Realizamos conexion de base de datos de Recambios.          ****************/
/************************************************************************************************/
$BDRecambios = @new mysqli("localhost", "coches", "coches", "recambios");
// Como connect_errno , solo muestra el error de la ultima instrucción mysqli, tenemos que crear una propiedad, en la que 
// está vacía, si no se produce error.

if ($BDRecambios->connect_errno) {
		$BDRecambios->controlError = $BDRecambios->connect_errno.':'.$BDRecambios->connect_error;
} else {
/** cambio del juego de caracteres a utf8 */
 mysqli_query ($BDRecambios,"SET NAMES 'utf8'");
}
?>
