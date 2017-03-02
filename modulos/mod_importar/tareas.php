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
 * 		$pulsado = 'verNuevos'				-> Ejecuta verNuevosRef($BDImportRecambios);
 * 												Se ejecuta en Paso 3 de ListaPrecios.
 * 		$pulsado = 'anahirRecam'			-> Ejecuta anahirRecam($BDRecambios);
 * 		$pulsado = 'BuscarError'			-> Ejecuta BuscarError($BDImportRecambios);
 * 												Se ejecuta en Paso 2 de Referencias Cruzadas al mostrar la pagina.
 * 		$pulsado = 'DistintoFabCruzTemporal'-> Ejecuta DistintoFabCruzTemporal($BDImportRecambios);
 * 		$pulsado = 'comPro'					-> Ejecuta errorFab($BDImportRecambios, $BDRecambios);
 * 		$pulsado = 'resumen'				-> Ejecuta resumen($BDImportRecambios);
 * 		$pulsado = 'obtenerVacioscruzados'	-> Ejecuta obtenerVaciosCru($BDImportRecambios);
 * 		$pulsado = 'grabarCruzadas'			-> Ejecuta grabarCruzadas($BDImportRecambios, $BDRecambios);
 * 		$pulsado = 'msql_csv				-> Ejectua MsqlCsv($lineaA, $lineaF,$nombrecsv);

 * 
 *  */
/* ===============  REALIZAMOS CONEXIONES  ===============*/

// creo que esta recogida de datos debe estar antes swich y solo pulsado.
// la tabla solo en la opción que la necesite.
if (isset($_POST['nombretabla'])){
$nombretabla = $_POST['nombretabla'];
}
$pulsado = $_POST['pulsado'];

include_once ("./../../configuracion.php");

// Crealizamos conexion a la BD Datos
include_once ("./../mod_conexion/conexionBaseDatos.php");
// Incluimos clase objeto de consultas.
include_once ("./Consultas.php");
$ConsultaImp = new ConsultaBD;

// Incluimos funciones
include_once ("./funciones.php");
include_once ("./funcP2ListaPrecios.php");
include_once ("./funcP2ReferCruzad.php");

 
 switch ($pulsado) {
    case 'borrar':
        $respuesta = $ConsultaImp->borrar($nombretabla, $BDImportRecambios);
        echo json_encode($respuesta) ;
        break;
    case 'contar':
        $respuesta = contador($nombretabla, $BDImportRecambios,$ConsultaImp);
        echo json_encode($respuesta);
        break;
    case 'comprobar':
        $id = $_POST['idrecambio'];
		$l = $_POST['linea'];
		$f = $_POST['fabricante'];
        $respuesta = comprobar($nombretabla, $BDImportRecambios, $BDRecambios,$id,$l,$f);
        echo json_encode($respuesta) ;
        break;
    case 'verNuevos':
        verNuevosRef($BDImportRecambios);
        break;
    case 'anahirRecam':
        $respuesta= anahirRecam($BDRecambios);
        echo json_encode($respuesta);
        break;
    case 'BuscarError':
        $datos = BuscarError($BDImportRecambios);
        header("Content-Type: application/json;charset=utf-8");
		echo json_encode($datos);
        break;
    case 'DistintoFabCruzTemporal':
        //~ $condicional ="Estado = ''";
        $condicional = $_POST['condicional'];
        $array = DistintoFabCruzTemporal($BDImportRecambios, $condicional);
        header("Content-Type: application/json;charset=utf-8");
		echo json_encode($array);
        break;
    case 'comPro':
        errorFab($BDImportRecambios, $BDRecambios);
        break;
    case 'resumen':
        resumenCruz($BDImportRecambios);
        break;
    case 'ObtenerVacioscruzados':
        $array = obtenerVaciosCru($BDImportRecambios,$ConsultaImp);
        header("Content-Type: application/json;charset=utf-8");
		echo json_encode($array);
        break;
    case 'grabarCruzadas':
        GrabarCruzadas($BDImportRecambios, $BDRecambios);
        break;
    case 'msql_csv':
        $lineaA = $_POST['lineaI'] ;
		$lineaF = $_POST['lineaF'] ;
		$nombrecsv = $_POST['Fichero'];
		
        MsqlCsv($lineaA, $lineaF,$nombrecsv,$ConfDir_subida,$BDImportRecambios);
        break;
}
 
/* ===============  CERRAMOS CONEXIONES  ===============*/

mysqli_close($BDImportRecambios);
mysqli_close($BDRecambios);
 
 
?>
