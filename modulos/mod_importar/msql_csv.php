<?php 
/* Este fichero es llamo desde funcion javascript consultaDatos
/* Realizamos conexion a base Datos */
include ("./../mod_conexion/conexionBaseDatos.php");
if ($BDImportRecambios->connect_errno) {
    echo "Falló la conexión a MySQL: (" . $BDImportRecambios->connect_errno . ") " . $BDImportRecambios->connect_error;
}


/* Comprobamos si el fichero que queremos enviar tiene 3 campos */


$lineaA = $_POST['lineaI'] ;
$lineaF = $_POST['lineaF'] ;
// Ahora creamos la ruta del fichero.
// Hay que tener en cuenta que esta ruta puede cambiar según donde guarde los ficheros temporales el servidor.
$nombrecsv = $_POST['Fichero'];
$fichero = 'C:\xampp\tmp'.'\\'.$nombrecsv;
//~ $nombrestabla = substr($nombrecsv, 0,-4);
// Ante de iniciar debemos saber que cuanto campos va tener, segun el fichero que sea.

if ($nombrecsv == "ReferenciasCruzadas.csv"){
	$NumeroCamposCsv = 3;
	$CamposSinCubrir = "0','0";
	$nombretabla= "referenciascruzadas";
}
if ($nombrecsv == "ReferenciasCversionesCoches.csv"){
	$NumeroCamposCsv = 3;
	$nombretabla= "referenciasCversiones";
}
if ($nombrecsv == "ListaPrecios.csv"){
	$NumeroCamposCsv = 3;
	$nombretabla= "listaprecios";
	$CamposSinCubrir = "0";

}
// Ahora abrimos fichero para leer lineas.
$archivo = fopen($fichero,'r');
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
				$Clinea = $num_linea;
				for ($i = 0; $i < $NumeroCamposCsv; $i++) {
					$campo[$i] = trim($datos[$i]);
				}
				
				
			   // Antes de guardar cuantos campo hay
				if (count($datos) !== $NumeroCamposCsv){
				$Estado = 'Campos'.count($datos).' Linea:'.$linea;
				} else {
				$Estado = '';
				}
			   
			   //guardamos en base de datos la línea leida
			   // AQUI DEBERIA CREAR UNA CONSULTA Y LUEGO EJECUTARLA.
			   // Recuerda que $campos tiene una coma al final
			   $campos = implode("','", $campo);
			   
			   $consulta = "INSERT INTO ".$nombretabla." VALUES('$Clinea','$campos','$Estado','$CamposSinCubrir')";
			   mysqli_query($BDImportRecambios,$consulta);
			   
		 
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
	mysqli_close($BDImportRecambios);
	//$html = 'Añadi de linea '.$lineaA. ' hasta linea '.$lineaF."\n".$RefProveedor.'Marca:'.$Marca.'NombreFichero:'.$nombretabla.'<br/>'.$consulta;
	$html = 'fichero '.$fichero. ' hasta linea '.$lineaF."\n".$RefProveedor.'Marca:'.$Marca.'NombreFichero:'.$nombretabla.'<br/>'.$consulta;

        echo $html ;
	echo '<br/>Linea:<br/>'.addslashes($Textolinea).'<br/>';






//~ function NumeroCampos($fichero,$nlinea)
//~ {
	//~ // Recuerda que tiene abierto el fichero.
   //~ $linea = fgets($fichero, $nlinea);
   //~ $datos = explode(",",$linea);
	//~ return $linea;
//~ }
?>
