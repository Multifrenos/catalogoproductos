<?php

include ("./../mod_conexion/conexionBaseDatos.php");

$nombretabla =$_POST['nombretabla'];
print_r($nombretabla);
    $consulta= "Delete from ".$nombretabla;
    $BorrarTabla= mysqli_query($BDImportRecambios,$consulta);
    
    mysqli_close($BDImportRecambios);
