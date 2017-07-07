<?php 
include_once ("./../../configuracion.php");

// Crealizamos conexion a la BD Datos
include_once ("./../mod_conexion/conexionBaseDatos.php");
// Incluimos funciones
include_once ("./funciones.php");

// Obtenemos funcion que nos envia... 

$pulsado = $_POST['pulsado'];

 switch ($pulsado) {

	case 'CopiarDescripcion':
	
	if (isset($_POST['id'])){
		$id = $_POST['id'];
		$DatosRefCruzadas= $_POST['DatosRefCruzadas'];
		$respuesta = CopiarDescripcion($id,$DatosRefCruzadas,$prefijoJoomla,$BDWebJoomla);
		
	} else {
		//~ // Si no recoge ID , entonces no podemos ejecutar funcion.. es adsurdo
		$respuesta = array();
		$respuesta['RowsAfectados']= '0'; // Quiere decir que hay error.
		$respuesta= 'No entro en POST_ID'; // Quiere decir que hay error.


	}
	
	header("Content-Type: application/json;charset=utf-8");
	echo json_encode($respuesta);

}

?>
