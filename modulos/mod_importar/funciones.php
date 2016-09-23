<?php

include ("./../mod_conexion/conexionBaseDatos.php");
$nombretabla = $_POST['nombretabla'];
$pulsado = $_POST['pulsado'];


function borrar($nombretabla, $BDImportRecambios) {
    $consulta = "Delete from " . $nombretabla;
    mysqli_query($BDImportRecambios, $consulta);
}
function contarVacios($nombretabla,$BDImportRecambios){
   $consulta ="SELECT RefFabPrin,linea FROM ".$nombretabla." where Estado = ''";
    $consultaContador=mysqli_query($BDImportRecambios, $consulta);
   $i=0;
  while($row_planets = $consultaContador->fetch_assoc()){
      $array[$i]["id"]= $row_planets['RefFabPrin'];
      $array[$i]["linea"]= $row_planets['linea'];
      $i++;
  }
 

 
 header("Content-Type: application/json;charset=utf-8");
echo json_encode($array);
}

function contador($nombretabla, $BDImportRecambios) {
    $consulta = "SELECT count(linea) as cuenta FROM " . $nombretabla;
    $consultaContador = mysqli_query($BDImportRecambios, $consulta);
    $contador = $consultaContador->fetch_assoc();
    echo $contador['cuenta'];
}

function comprobar($nombretabla, $BDImportRecambios, $BDRecambios) {
    $id = $_POST['idrecambio'];
    $l=$_POST['linea'];
    $f = $_POST['fabricante'];
   
    
        $consul = "SELECT * FROM referenciascruzadas where RefFabricanteRec ='" . $id . "'";
        $consultaReca = mysqli_query($BDRecambios, $consul);
        $consfinal = $consultaReca->fetch_assoc();

        if ($consfinal['RefFabricanteRec'] == $id && $consfinal['IdFabricanteRec'] == $f) {
            $actu = "UPDATE `listaprecios` SET `Estado`='existe',`RecambioID`=" . $consfinal['RecambioID'] . " WHERE `linea` ='" . $l . "'";
            mysqli_query($BDImportRecambios, $actu);
            $existente = 1;
        } else {
            $actu = "UPDATE `listaprecios` SET `Estado`='nuevo' WHERE `linea` ='" . $l . "'";
            mysqli_query($BDImportRecambios, $actu);
            $nuevo = 1;
        }
    
    $datos[0]['n'] = $nuevo;
    $datos[0]['e'] = $existente;
    $datos[0]['t'] = $l;
    header("Content-Type: application/json;charset=utf-8");
    echo json_encode($datos);
}

switch ($pulsado) {
    case 'borrar':
        borrar($nombretabla, $BDImportRecambios);

        break;
    case 'contar':
        contador($nombretabla, $BDImportRecambios);

        break;
    case 'comprobar':
        comprobar($nombretabla, $BDImportRecambios, $BDRecambios);
        break;
     case 'contarVacios':
        contarVacios($nombretabla, $BDImportRecambios, $BDRecambios);
        break;
}


mysqli_close($BDImportRecambios);
mysqli_close($BDRecambios);
