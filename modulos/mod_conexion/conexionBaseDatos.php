<?php 
/* Realizamos conexion a la base de datos de importarRecambios. */
$BDImportRecambios =new mysqli("localhost", "coches", "coches", "importarRecambios");
if ($BDImportRecambios->connect_errno) {
    echo "Falló la conexión a MySQL: (" . $BDImportRecambios->connect_errno . ") " . $BDImportRecambios->connect_error;
}
/** cambio del juego de caracteres a utf8 */
 mysqli_query ($BDImportRecambios,"SET NAMES 'utf8'");

// Conexion de base de datos de Recambios.
$BDRecambios = new mysqli("localhost", "coches", "coches", "Recambios");

mysqli_query ($BDRecambios,"SET NAMES 'utf8'");

if ($BDRecambios->connect_errno) {
    echo "Falló la conexión a MySQL: (" . $BDRecambios->connect_errno . ") " . $BDRecambios->connect_error;
}
/** cambio del juego de caracteres a utf8 */
 mysqli_query ($BDRecambios,"SET NAMES 'utf8'");
?>
