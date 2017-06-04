<?php
/* Fichero de tareas a realizar.

 *  */
/* ===============  REALIZAMOS CONEXIONES  ===============*/

// creo que esta recogida de datos debe estar antes swich y solo pulsado.

$pulsado = $_POST['pulsado'];

include_once ("./../../configuracion.php");
// Crealizamos conexion a la BD Datos
include_once ("./../mod_conexion/conexionBaseDatos.php");
// Incluimos el controlador comun
include_once ("./../../controllers/Controladores.php");
$Controlador = new ControladorComun;

// Incluimos clase objeto de consultas.
include_once ("./ObjetoSincronizar.php");
$ObjSincronizar = new ObjSincronizar;

// Incluimos funciones
include_once ("./funciones.php");


 
 switch ($pulsado) {
    case 'sincronizar':
        $respuesta = sincronizar($Controlador,$ObjSincronizar,$BDRecambios,$BDWebJoomla,$Conexiones,$prefijoJoomla);
        header("Content-Type: application/json;charset=utf-8");
        echo json_encode($respuesta) ;
        break;
   
	case 'ContarProductoVirtuemart':
		$nombretabla = "virtuemart_products";
		$whereC ="";
        $respuesta = $Controlador->contarRegistro($BDRecambios,$nombretabla,$whereC);
        header("Content-Type: application/json;charset=utf-8");
        echo json_encode($respuesta) ;
        break;
   
   case 'CrearVistas':
		$vistas = $_POST['vistas'];
		$limite = $_POST['limite'];
        $respuesta = crearVistas($BDRecambios,$vistas,$limite);
        header("Content-Type: application/json;charset=utf-8");
        echo json_encode($respuesta) ;
        break;
   
   case 'BuscarErrorRefVirtuemart':
		$respuesta = BuscarErrorRefVirtuemart();
        header("Content-Type: application/json;charset=utf-8");
        echo json_encode($respuesta) ;
        break;
   
}
 
/* ===============  CERRAMOS CONEXIONES  ===============*/

mysqli_close($BDImportRecambios);
mysqli_close($BDRecambios);
 
 
?>
