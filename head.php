<?php
	// Recuerda que este fichero se include en muchos ficheros, por lo que 
	// la ruta getcwd() no es la misma siempre.
	if (isset($DirectorioInicio)) {
		$Ruta = './';
	} else {
		$Ruta = './../../' ; // Porque estoy en modulo...
		// Esto tiene porque ser así se podría asignar antes, desde el fichero que include.
	}
	
	include ($Ruta.'configuracion.php');
	include ($RutaServidor.$HostNombre."/modulos/mod_conexion/conexionBaseDatos.php");
	include ($RutaServidor.$HostNombre."/controllers/Controladores.php");
    

?>




<meta name="language" content="es">
<meta charset="UTF-8">
<link rel="stylesheet" href="<?php echo $HostNombre;?>/css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="<?php echo $HostNombre;?>/css/template.css" type="text/css">

<script src="<?php echo $HostNombre;?>/jquery/jquery-2.2.5-pre.min.js"></script>
<script src="<?php echo $HostNombre;?>/css/bootstrap.min.js"></script>

<!--
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
-->

