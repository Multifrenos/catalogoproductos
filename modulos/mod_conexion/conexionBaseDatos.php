<?php 
/* Realizamos conexion a la base de datos. */
$link =mysqli_connect("localhost","coches","coches");
mysqli_select_db($link,"coches") OR DIE ("Error: No es posible establecer la conexión");
?>
