<?php 
/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogo productos Soluciones Vigo.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero , Alberto Lago , Marcos Araujo
 * @Descripcion	Funciones para el Paso 1 de todas los ficheros.
 * */

//Funcion donde se consulta fichero.csv y realizamos INSERT DATOS en tablas *
function MsqlCsv($lineaA, $lineaF,$nombrecsv,$ConfDir_subida,$BDImportRecambios) {
	$fichero = $ConfDir_subida.$nombrecsv;
	// Ante de iniciar debemos saber que cuanto campos va tener, segun el fichero que sea, no se cuenta el campo linea.
	
	$FDatos = FicheroDatos($nombrecsv);
	$NumeroCamposCsv 	= $FDatos['NumeroCampos'];
	$CamposSinCubrir 	= $FDatos['CamposSinCubrir'];
	$nombretabla 		= $FDatos['nombretabla'];
	
	
	// Ahora abrimos fichero para leer lineas.
	$archivo = fopen($fichero,'r');
	// Ahora colocamos el punteroe en la linea Inicial.
	$num_linea = 0;
	//Recuerda que la linea empieza en 0
	$consulta = [];//inicializamos el array de consultas

		while (!feof ($archivo)) {
			$Estado = '';// inicilializamos estado
			$linea = $num_linea;
			$Textolinea = fgets($archivo);
			if  ($linea >= $lineaA)
			{ 
			   
			   if($linea < $lineaF) 
			   { 
					
					// Comprobamos que los campos contienen datos
					// - Eliminamos de las linesa los espacios,los puntos,comas,0 para comprobarlo..
					// sino añadimos error en estado.
					 $limpiamosLinea = str_replace([",", ".", "0", " "], "", $Textolinea); // Quitamos (,)
					if (strlen($limpiamosLinea) < 2) {
						$Estado = 'Campos vacios con menos 2 caracteres';
					}
					
					// Obtenemos los campos de la linea en array datos.
					$datos = str_getcsv($Textolinea,"," ,'"');
					
					//Almacenamos los datos que vamos leyendo en una variable
					$Clinea = $num_linea;
					for ($i = 0; $i < $NumeroCamposCsv; $i++) {
						// Limpiamos espacios y ademas si aparece ' dentro de un campo tambien lo inserta y no genera un error(\') 
						// inserta el caracter especial y no la \
						$campo[$i] = trim(str_replace(["'"], "\\'", $datos[$i]));

					}
					
					
				   // Comprobamos cuantos campo hay en el csv y tiene haber los mismo que indicamos en cada fichero.
					if (count($datos) !== $NumeroCamposCsv){
					$Estado = 'Campos'.count($datos).' Linea:'.$linea;
					} 
				   
				   //Guardamos en tabla los campos obtenidos en la línea leida
				   //Si hubiera mas campos , estos no los mostraría.
				   //Si hubiera mas campos en la linea estos los creara en blanco.
				   $campos = implode("','", $campo);
				   $consulta[] = "('$Clinea','$campos','$Estado','$CamposSinCubrir')";
				   //cerramos condición
			   }
			}
			 
			if ( $linea >= $lineaF ) {
			break;
			}
			//cerramos bucle
			$num_linea++;
		}
		$myconsulta = "INSERT INTO " . $nombretabla . " VALUES " . implode(',', $consulta);
		//si el array de consultas NO ESTA VACIO se hace realmente la inserción en la base de datos    
		$ErrorConsulta = "";
		if (count($consulta) > 0) {
			//~ mysqli_query($BDImportRecambios, $myconsulta);
			$BDImportRecambios->query($myconsulta);
		   // Tengo que comprobar si no hubo un error en el insert, si lo hubo tengo que comunicarlo.
			$ErrorConsulta =  $BDImportRecambios->error;
		} 
		 
		 
		fclose($archivo);
		
		// Devolvemos un array con:
		// 	- Linea Inicial
		// 	- Linea Final
		// 	- Correcto o incorrecto el INSERT.
		$resultado['Inicio'] = $lineaA;
		$resultado['Final'] = $lineaF;
		if ( $ErrorConsulta == "" ) {
		$resultado['Resultado'] = 'Correcto el insert';
		} else {
		$resultado['Resultado'] = $ErrorConsulta;
		}
		//~ $resultado['nombrecsv'] = $nombrecsv;
	    //~ header("Content-Type: application/json;charset=utf-8");
		echo json_encode($resultado) ;
}

function FicheroDatos($nombrecsv){
	// funcion que comprobamos que fichero es y creamos array con sus datos
	if ($nombrecsv == "ReferenciasCruzadas.csv"){
		$FicheroDatos['NumeroCampos'] 		= 3;
		$FicheroDatos['CamposSinCubrir'] 	= "0','0";
		$FicheroDatos['nombretabla']		= "referenciascruzadas";
	}elseif ($nombrecsv == "ReferenciasCversionesCoches.csv"){
		$FicheroDatos['NumeroCampos']		= 11;
		$FicheroDatos['CamposSinCubrir']	= "0','0";
		$FicheroDatos['nombretabla']		= "referenciascversiones";
	}elseif ($nombrecsv == "ListaPrecios.csv"){
		$FicheroDatos['NumeroCampos']	 	= 3;
		$FicheroDatos['CamposSinCubrir']	= "0";
		$FicheroDatos['nombretabla']		= "listaprecios";
	}
	return $FicheroDatos;
	
}
?>
