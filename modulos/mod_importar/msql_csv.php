<?php 
/* Este fichero es llamo desde funcion javascript consultaDatos
/* Realizamos conexion a base Datos */
include ("./../../configuracion.php");

include ("./../mod_conexion/conexionBaseDatos.php");
if ($BDImportRecambios->connect_errno) {
    echo "Falló la conexión a MySQL: (" . $BDImportRecambios->connect_errno . ") " . $BDImportRecambios->connect_error;
}

/* Comprobamos si el fichero que queremos enviar */

$lineaA = $_POST['lineaI'] ;
$lineaF = $_POST['lineaF'] ;
// Ahora creamos la ruta del fichero.
// Hay que tener en cuenta que esta ruta puede cambiar según donde guarde los ficheros temporales el servidor.
$nombrecsv = $_POST['Fichero'];
$fichero = $ConfDir_subida.$nombrecsv;
// Ante de iniciar debemos saber que cuanto campos va tener, segun el fichero que sea, no se cuenta el campo linea.

if ($nombrecsv == "ReferenciasCruzadas.csv"){
	$NumeroCamposCsv = 3;
	$CamposSinCubrir = "0','0";
	$nombretabla= "referenciascruzadas";
}
if ($nombrecsv == "ReferenciasCversionesCoches.csv"){
	$NumeroCamposCsv = 11;
	$CamposSinCubrir = "0','0";
	$nombretabla= "referenciascversiones";
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
		$Estado = '';// inicilializamos estado
		
		$linea = $num_linea;
		$Textolinea = fgets($archivo);
		if  ($linea >= $lineaA)
		{ 
		   
		   if($linea < $lineaF) 
		   { 
				
				// Ahora comprobamos que la linea contiene datos sino añadimo error en estado.
				$limpiamosLinea = str_replace(",","",$Textolinea); // Quitamos (,)
				$limpiamosLinea = str_replace(".","",$limpiamosLinea); // Quitamos (.)
				$limpiamosLinea = str_replace("0","",$limpiamosLinea); // Quitamos (0)
				$limpiamosLinea = str_replace(" ","",$limpiamosLinea); // Quitamos ( ) espacio..
				if (strlen($limpiamosLinea) < 2) {
					$Estado = 'Campos vacios con menos 2 caracteres';
				}


				 
				
				$datos = str_getcsv($Textolinea,"," ,'"');
				//~ $datos = explode(",",$Textolinea);
				
				//Almacenamos los datos que vamos leyendo en una variable
				$Clinea = $num_linea;
				for ($i = 0; $i < $NumeroCamposCsv; $i++) {
					$campo[$i] = trim($datos[$i]);
				}
				
				
			   // Comprobamos cuantos campo hay en el csv y tiene haber los mismo que indicamos en cada fichero.
				if (count($datos) !== $NumeroCamposCsv){
				$Estado = 'Campos'.count($datos).' Linea:'.$linea;
				} else {
				$Estado .= '';
				}
			   
			   // Comprobamos si los campos son correctos, ya que necesitamos que:
			   // Nuestra tabla puede tener campos int y fechas.
			    
			    
			   
			   
			   
			   //guardamos en base de datos la línea leida
			   // AQUI DEBERIA CREAMOS UNA CONSULTA Y LUEGO EJECUTARLA.
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
	$html = 'fichero '.$fichero. ' hasta linea '.$lineaF."\n".'Marca:'.'NombreFichero:'.$nombretabla.'<br/>'.$consulta;

    echo $html ;
?>
