<?php 

include_once ("./../../configuracion.php");

// Crealizamos conexion a la BD Datos
include_once ("./../mod_conexion/conexionBaseDatos.php");
// Incluimos clase objeto de consultas.
if (isset($_POST['id'])){
$id = $_POST['id'];
$DatosRefCruzadas= $_POST['DatosRefCruzadas'];

}


$resumen = array();
$tabla = $prefijoJoomla."_virtuemart_products_es_es";
// Borramos datos antes añadir.
$whereC= "  WHERE `virtuemart_product_id`=".$id;
$consulta = "UPDATE ".$tabla." SET `product_desc`=''".$whereC;
$resultado = $BDWebJoomla->query($consulta);
$Afectados = $BDWebJoomla->affected_rows;

// Ahora dividimos los datos recibidos ya que si es muy grande el update falla.
$DatosArray = str_split($DatosRefCruzadas,10000);
$resumen ['PartirString'] =$DatosArray;
foreach ( $DatosArray as $Datos) {
	$consulta = "UPDATE ".$tabla." SET `product_desc`= concat(product_desc,'".$Datos."')".$whereC;
	$resultado = $BDWebJoomla->query($consulta);
}

//~ $resumen['consulta']=$consulta;
$resumen['RowsAfectados']= $Afectados;
header("Content-Type: application/json;charset=utf-8");
echo json_encode($resumen);
?>
