<?php
/* Fichero de tareas a realizar.
 * 
 * 
 * Con el switch al final y variable $pulsado
 *     	$pulsado = 'borrar'					-> Ejecuta borrar($nombretabla, $BDImportRecambios);
 * 												Se ejecuta en Paso 1  (todos los ficheros )
 *     	$pulsado = 'contar'					-> Ejecuta contador($nombretabla, $BDImportRecambios);
 * 												Se ejecuta en Paso 2 de ListaPrecios --->
 * 		$pulsado = 'comprobar'				-> Ejecuta comprobar($nombretabla, $BDImportRecambios, $BDRecambios);
 * 		$pulsado = 'contarVacios'			-> Ejecuta contarVacios($nombretabla, $BDImportRecambios);
 * 												Se ejecuta en Paso 2 de ListaPrecios --> 
 * 												Cuando pulsamos en comprobar... despues de seleccionar familia y fabricante.
 * 		$pulsado = 'verNuevos'				-> Ejecuta verNuevosRef($BDImportRecambios);
 * 												Se ejecuta en Paso 3 de ListaPrecios.
 * 		$pulsado = 'anahirRecam'			-> Ejecuta anahirRecam($BDRecambios);
 * 		$pulsado = 'BuscarError'			-> Ejecuta BuscarError($BDImportRecambios);
 * 												Se ejecuta en Paso 2 de Referencias Cruzadas al mostrar la pagina.
 * 		$pulsado = 'BuscarErrorFab'			-> Ejecuta BuscarErrorFab($BDImportRecambios);
 * 		$pulsado = 'comPro'					-> Ejecuta errorFab($BDImportRecambios, $BDRecambios);
 * 		$pulsado = 'resumen'				-> Ejecuta resumen($BDImportRecambios);
 * 		$pulsado = 'contarVacioscruzados'	-> Ejecuta contarVaciosCru($BDImportRecambios);
 * 		$pulsado = 'comprobar2cruz'			-> Ejecuta comprobarCruzadas($BDImportRecambios, $BDRecambios);
 * 
 * 
 *  */
/* ===============  REALIZAMOS CONEXIONES  ===============*/

// creo que esta recogida de datos debe estar antes swich y solo pulsado.
// la tabla solo en la opción que la necesite.
$nombretabla = $_POST['nombretabla'];
$pulsado = $_POST['pulsado'];

// Crealizamos conexion a la BD Datos
include ("./../mod_conexion/conexionBaseDatos.php");
// Incluimos clase objeto de consultas.
include ("./Consultas.php");
$ConsultaImp = new ConsultaImportar;

// Incluimos funciones
include ("./funciones.php");


 
 switch ($pulsado) {
    case 'borrar':
        $ConsultaImp->borrar($nombretabla, $BDImportRecambios);
        break;
    case 'contar':
        contador($nombretabla, $BDImportRecambios,$ConsultaImp);
        break;
    case 'comprobar':
        comprobar($nombretabla, $BDImportRecambios, $BDRecambios);
        break;
    case 'contarVacios':
        contarVacios($nombretabla, $BDImportRecambios);
        break;
    case 'verNuevos':
        verNuevosRef($BDImportRecambios);
        break;
    case 'anahirRecam':
        anahirRecam($BDRecambios);
        break;
    case 'BuscarError':
        BuscarError($BDImportRecambios);
        break;
    case 'BuscarErrorFab':
        BuscarErrorFab($BDImportRecambios);
        break;
    case 'comPro':
        errorFab($BDImportRecambios, $BDRecambios);
        break;
    case 'resumen':
        resumenCruz($BDImportRecambios);
        break;
    case 'contarVacioscruzados':
        contarVaciosCru($BDImportRecambios);
        break;
    case 'comprobar2cruz':
        comprobarCruzadas($BDImportRecambios, $BDRecambios);
        break;
}
 
/* ===============  CERRAMOS CONEXIONES  ===============*/

mysqli_close($BDImportRecambios);
mysqli_close($BDRecambios);
 
 
?>
