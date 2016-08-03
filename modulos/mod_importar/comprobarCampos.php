<?php 

/* Realizamos conexion a base Datos */
include ("./../mod_conexion/conexionBaseDatos.php");



/* Comprobamos si el fichero que queremos enviar tiene 3 campos */


$lineaA = $_POST['lineaI'] ;
$lineaF = $_POST['lineaF'] ;

// Ahora creamos el for para leer lineas.
	$archivo = fopen('/tmp/ReferenciasCruzadas.csv','r');
// Ahora colocamos el punteroe en la linea Inicial.

	$num_linea = 0;

	//Recuerda que la linea empieza en 0
	while (!feof ($archivo)) {
		$linea = $num_linea;
		$Textolinea = fgets($archivo);
		if  ($linea >= $lineaA)
		{ 
		   
		   if($linea < $lineaF) 
		   { 
				//abrimos condición, solo entrará en la condición a partir de la segunda pasada del bucle.
				/* La funcion explode nos ayuda a delimitar los campos, por lo tanto irá 
				leyendo hasta que encuentre un ; */
				//~ echo $linea.' '.$Textolinea.'<br/>';
				
				
				$datos = str_getcsv($Textolinea,"," ,'"');
				//~ $datos = explode(",",$Textolinea);
				
				//Almacenamos los datos que vamos leyendo en una variable
				$RefDKM = trim($datos[0]);
				$Marca = trim($datos[1]);
				$RefCruzada = trim($datos[2]);
			   
			   // Antes de guardar cuantos campo hay
				if (count($datos) !== 3){
				$Estado = 'Campos'.count($datos).' Linea:'.$linea;
				} else {
				$Estado = '';
				}
			   
			   //guardamos en base de datos la línea leida
			   mysqli_query($link,"INSERT INTO referenciascruzadas VALUES('$RefDKM','$Marca','$RefCruzada','$Estado')");
			   
		 
			   //cerramos condición
		   }
		}
		 
		if ( $linea >= $lineaF ) {
		break;
		}
		//cerramos bucle
		$num_linea++;
	}
	
	fclose($archivo);
	mysqli_close($link);
	$html = 'Añadi de linea '.$lineaA. ' hasta linea '.$lineaF."\n".$RefDKM.'Marca:'.$Marca.'RefCruzada'.$RefCruzada;
	echo $html ;
	echo '<br/>'.addslashes($Textolinea).'<br/>';






//~ function NumeroCampos($fichero,$nlinea)
//~ {
	//~ // Recuerda que tiene abierto el fichero.
   //~ $linea = fgets($fichero, $nlinea);
   //~ $datos = explode(",",$linea);
	//~ return $linea;
//~ }
?>
