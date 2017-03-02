<?php
/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogo productos Soluciones Vigo.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero
 * @Descripcion	Funciones en php para realizar las tareas.
 *  */


 /* Funtion ObtenerVaciosCru
  * 	Devolvemos un array con 400 registros máximo en blanco
  * 	en el que tenemos el id ->RefProveedor ( que realmente RefRecambioPrincipal )
  * 						 linea-> Que indica la linea (id-primary) de $BDIMPORTAR-referenciascruzadas
  * 						 F_rec -> El nombre del fabricante cruzado...
  * 						 Ref_F-> La referencia cruzada a buscar o añadir.
  * */
function obtenerVaciosCru($BDImportRecambios,$ConsultaImp) {
    $array = array();
    
    $tabla ="referenciascruzadas";
	$whereC = " WHERE Estado = ''";
	$campo[1]= 'RefProveedor';
	$campo[2]= 'linea';
	$campo[3]= 'Fabr_Recambio';
	$campo[4]= 'Ref_Fabricante';
	$array = $ConsultaImp->registroLineas($BDImportRecambios,$tabla,$campo,$whereC);
	//~ 
    //~ 
    //~ 
    //~ 
    //~ $consulta = "SELECT * FROM `referenciascruzadas` where Estado = '' limit 400";
    //~ $consultaContador = mysqli_query($BDImportRecambios, $consulta);
    //~ $i = 0;
    //~ while ($row_planets = $consultaContador->fetch_assoc()) {
        //~ $array[$i]["id"] = $row_planets['RefProveedor'];
        //~ $array[$i]["linea"] = $row_planets['linea'];
        //~ $array[$i]["F_rec"] = $row_planets['Fabr_Recambio'];
        //~ $array[$i]["Ref_F"] = $row_planets['Ref_Fabricante'];
        //~ $i++;
    //~ }

	return $array;

    
}



/* Funtion DistintoFabCruzTemporal -->  Ejecuta PASO2REFERENCIAS CRUZADAS TERMINAR DE CARGAR 
 * Obtenemos un array con los distintos fabricantes que hay en la tabla ReferenciasCruzadas temporal
 * 
 * */
function DistintoFabCruzTemporal($BDImportRecambios,$condicional) {
    $array = array();
    if (strlen($condicional) > 0 ){
		$condicional ="WHERE ".$condicional;
	} else {
		// Quiere decir que el condicional es nada...
		$condicional = '';
	}
    $consulta = "SELECT DISTINCT(Fabr_Recambio) FROM `referenciascruzadas` ".$condicional;
    $conNuevo = mysqli_query($BDImportRecambios, $consulta);
    $i = 0;
    while ($row_planets = $conNuevo->fetch_assoc()) {
        $array[$i]['Fabr_Recambio'] = $row_planets['Fabr_Recambio'];
        $i++;
    }
   //~ $array['consulta'] = $consulta;
   return $array;
}


/* ===================  FUNCION COMPROBARCRUZADAS ===========================================*/
	// Se ejecuta en Paso2ReferenciasCruzadas en funcion grabar 
	// 	1.- Comprobamos si existe la referencia principal.
	//	2.- Comprobamos que el fabricante cruzado exista. ( Este paso creo que podemos eliminarlo)
	// 	3.- Comprobamos que la referencia cruzada  si existe en BDRECAMBIOS-referenciascruzadas.
	// 			[SI]- Buscamos en cruce_referencia si existe:
	//					[SI]- Si existe referencia en cruce_referencias:
	//							Entonces solo cambiamos estado en BDIMPORTAR-referenciascruzadas por:
	//							ESTADO = COMBROBADO:[EXISTE CRUCE]
	//					[NO]- Entonces:
	//							1.- Añadimos en cruce_referencias (BDRECAMBIOS)
	//							2.- Cambiamos en BDIMPORTAR-referenciascruzadas
	//								en ESTADO= AÑADIDO:[CRUCE NUEVO - EXISTIA REFERENCIA CRUZADA]
	//			[NO]- Añadimos en referenciacruzadas y cruce_referencias y ademas ponemos en 
	//				BDIMPORTAR-referenciascruzadas en 
	//				ESTADO = AÑADIDO:[REFERENCIA CRUZADA - Y NUEVO CRUCE]
	// 
	// Todos los casos [NO] del punto 1 y 2 se marca el estado de tabla importar como:
	//		1.- ERROR:[NO EXISTE REFERENCIA PRINCIPAL]
	//		2.- ERROR:[NO EXISTE FABRICANTE_CRUZADO]
	

function GrabarCruzadas($BDImportRecambios, $BDRecambios) {
    $ref = $_POST['idrecambio']; // Realmente es la referencia del proveedor principal, no es id recambio... 
    $l = $_POST['linea']; // La utilizamos para modificar campo ESTADO de BDIMPORTAR-REFERENCIASCRUZADAS.
    $f = $_POST['fabricante']; // Id de fabricante principal...
    $ref_f = $_POST['Ref_fa']; // Referencia de fabricante cruzado.
    $fab_ref = $_POST['Fab_ref'];// Nombre fabricante cruzado.
    // Reiniciamos 
    $fecha = date('Y-m-d');
    $datos[0]['respuesta'] = "";
    $ControlPaso = "";
    $ControlBusqueda = "";
    $idRecambio = "";
	// 	1.- Comprobamos si existe la referencia principal.
	// 		Buscamos en BDRecambios en tabla referencias cruzadas si existe la referencia principal.
    $conRefFab = "SELECT * FROM `referenciascruzadas` WHERE RefFabricanteCru = '" . $ref . "' and IdFabricanteCru = '" . $f . "'";
    $ejconRefFab = mysqli_query($BDRecambios, $conRefFab);
    $resul = mysqli_fetch_assoc($ejconRefFab);
    $idRecambio = $resul['RecambioID']; // Id de recambio que vamos a cruzar
    if ($resul) {
		// Si existe referencia principal, por lo que continuamos.
		$ControlPaso=" [REFPRINCIPAL]=".$idRecambio;
		//	2.- Comprobamos que el fabricante cruzado exista. ( Este paso creo que podemos eliminarlo)
		// 		Ahora obtenemos el id del fabricante cruzado,
		// 		Este ya lo hicimos en paso2 , podríamos ahorrarlo si añadieramos ID FabricanteCRU en 
		// 		BDIMPORTAR-referenciacruzada cuando comprobamos los fabricantes.
        $busFacruz = "SELECT id FROM `fabricantes_recambios` WHERE Nombre = '" . $fab_ref . "'";
        $ejbusFacruz = mysqli_query($BDRecambios, $busFacruz);
        $resulFabCruz = mysqli_fetch_assoc($ejbusFacruz);
        $id = $resulFabCruz['id']; // Id de Fabricante cruzadas
       
		$ControlPaso = $ControlPaso." [IDFABRICANTE]=".$id;
		// 	3.- Comprobamos que la referencia cruzada  si existe en BDRECAMBIOS-referenciascruzadas.
		// 		Ahora buscamos en referencias cruzadas, la nueva referencia cruzada y el id fabricante cruzados.
		// 		Si existe entonces obtenemos el id de referenciacruzada.
		$ConCruRefFab = "SELECT * FROM `referenciascruzadas` WHERE RefFabricanteCru = '".$ref_f."' and IdFabricanteCru = '".$id."'";
		$ejConCruRefFab= mysqli_query($BDRecambios,$ConCruRefFab);
		$resulCru= mysqli_fetch_assoc($ejConCruRefFab);
		if($resulCru){
			// Si existe referencia cruzada.
			// 		3.-	[SI]- Buscamos en cruce_referencia si existe:
			$ControlPaso = $ControlPaso." [YA-EXISTE-REFCRUZADA]";

			$buscarcruces = "SELECT * FROM `cruces_referencias` where idReferenciaCruz =" . $resulCru['id'];
            $consul = mysqli_query($BDRecambios, $buscarcruces);
            if($consul){
			//		3.-	[SI]	[SI]- Si existe referencia en cruce_referencias:
			//						 ESTADO = COMBROBADO:[EXISTE CRUCE]
			//						 en la BD IMPORTAR en TABLA referencias cruzadas
				$ControlPaso = $ControlPaso." [EXISTE CRUCE]";

                $consul = "UPDATE `referenciascruzadas` SET `Estado`='COMBROBADO:[EXISTE CRUCE]' WHERE RefProveedor ='" . $ref . "' and linea ='".$l."'";
				mysqli_query($BDImportRecambios, $consul);
            }else{
			//		3.-	[SI]	 [NO] existe en cruce (cruce_referencias):
							//		 Entonces en BD IMPORTAR en TABLA referencias cruzadas 
							// 		ESTADO = AÑADIDO:[CRUCE NUEVO - EXISTIA REFERENCIA CRUZADA]
							// Y añadimos referencia en BD RECAMBIOS en cruces_referencias la
							// NUEVO CRUCE...
				$ControlPaso = $ControlPaso." [NO EXISTE CRUCE]";

                $insert = "INSERT INTO `cruces_referencias`(`idReferenciaCruz`, `idRecambio`, `idFabricanteCruz`) VALUES (" . $resulCru['id'] . "," . $idRecambio . "," . $id. ")";
                $secInser = mysqli_query($BDRecambios, $insert);
                $consul = "UPDATE `referenciascruzadas` SET `Estado`='AÑADIDO:[CRUCE NUEVO - EXISTIA REFERENCIA CRUZADA]' WHERE RefProveedor ='" . $ref . "' and linea ='".$l."'";
				mysqli_query($BDImportRecambios, $consul);
            }
         }else{
			//		3.-	[NO]- Añadimos en referenciacruzadas y cruce_referencias y ademas ponemos en 
			//				BDIMPORTAR-referenciascruzadas en 
			//				ESTADO = AÑADIDO:[REFERENCIA CRUZADA - Y NUEVO CRUCE]
			
			// No existe referencia cruzada en BDRECAMBIOS-referenciascruzadas, la añadimos en:
			// 	1.- BDRECAMBIOS-referenciascruzadas
			// 	2.- BDRECAMBIOS-cruce_referencias
			// Y por ultimos en BDImportar-referenciascruzadas anoto en campo
			// ESTADO = AÑADIDO:[REFERENCIA CRUZADA - Y NUEVO CRUCE] 
			$ControlPaso = $ControlPaso." [NO EXISTE REFCRUZADA]=".$ref_f;

			
			$creaCru = "INSERT INTO `referenciascruzadas`( `RecambioID`, `IdFabricanteCru`, `RefFabricanteCru`) VALUES (0," . $id . "," . "'".$ref_f. "'," .$fecha. ")";
			$ControlBusqueda = $creaCru."\n";
			
			mysqli_query($BDRecambios, $creaCru);
			// Ahora obtenemos ID de referencia cruzada recien creada.
			// Me imagino que hay otra forma de hacerlo... pero de momento repito código..
			$ConCruRefFab = "SELECT * FROM `referenciascruzadas` WHERE RefFabricanteCru = '".$ref_f."' and IdFabricanteCru = '".$id."'";
			$ejConCruRefFab= mysqli_query($BDRecambios,$ConCruRefFab);
			$resulCru= mysqli_fetch_assoc($ejConCruRefFab);
			
			$ControlPaso = $ControlPaso." [ID-NUEVO-REFCRUZADA]=".$resulCru['id'];
			$ControlBusqueda = $ControlBusqueda.$ConCruRefFab;
			// Ahora añadimos $BDRECAMBIOS -cruce_referencias.
			// 		Entiendo que no existe, pero nunca se sabe.. por lo que se debería comprobar de nuevo
			//		para esto debería ser funciones muchos proceso para evitar duplicar código
			$insert = "INSERT INTO `cruces_referencias`(`idReferenciaCruz`, `idRecambio`, `idFabricanteCruz`) VALUES (" . $resulCru['id'] . "," . $idRecambio . "," . $id. ")";
            $secInser = mysqli_query($BDRecambios, $insert);
            
			// Ahota cambiamos campo ESTADO de referencia cruzada de importar.
			$consul = "UPDATE `referenciascruzadas` SET `Estado`='AÑADIDO:[REFERENCIA CRUZADA - Y NUEVO CRUCE]' WHERE RefProveedor ='" . $ref . "' and linea ='".$l."'";
			mysqli_query($BDImportRecambios, $consul);
			$ControlPaso = $ControlPaso."[REFERENCIA CRUZADA - Y NUEVO CRUCE]";
         }

    } else {
		// No existe las referencia principal solo marcamos en tabla importar (referecias cruzadas)
		// en ESTADO = ERROR[Referencia Principal]
		$consul = "UPDATE `referenciascruzadas` SET `Estado`='[ERROR P2-1]:Referencia Principal' WHERE RefProveedor ='" . $ref . "' and linea ='".$l."'";
		mysqli_query($BDImportRecambios, $consul);
		$ControlPaso = $ControlPaso."ERROR[Referencia Principal]";

    }
    $datos[0]['respuesta'] = $ControlPaso;
	$datos[0]['busqueda'] = $ControlBusqueda;
    header("Content-Type: application/json;charset=utf-8");
    echo json_encode($datos);
}
/* ===================  FUNCION BUSCAR ERROR ===========================================*/
	// Se ejecuta por AJAX en Paso2ReferenciasCruzadas al terminar de mostrar la pagina 
	// donde cambia con un UPDATE todos los registros que el nombre Fabricante Recambio
	// o la referencia del fabricante tenga menos de dos caracteres.
	// Estado ='[ERROR P2-21]:CampoVacio'
	// Devuelve objeto:
	// 		({
	// 				conexion:"correcto" -> Donde indica si es correcto o no la conexion
	//				NItems:[numero] ->	Donde indica la cantidad de item que tiene.
	// 				Menos2C:[numero] -> Donde indica la cantidad ROW que se cambiaron.
	//		})
	
function BuscarError($BDImportRecambios) {
    // Llamamos funcion contar registro con estado en blanco
    $datos = ContarVaciosBlanco($BDImportRecambios,'referenciascruzadas');
     
    $consul = "UPDATE `referenciascruzadas` SET `Estado`='[ERROR P2-21]:CampoVacio' WHERE LENGTH(Fabr_Recambio) < 2 or LENGTH(Ref_Fabricante) < 2";
    $ConsErr = $BDImportRecambios->query($consul);
    $datos['RegistrosMenos2C'] = $BDImportRecambios->affected_rows; 
    // Ahora contamos fabricantes que se haya puesto estado  [ERROR P2-21]:CampoVacio a todos sus registros.
    $condicional = "Estado = '[ERROR P2-21]:CampoVacio'";
    $fabricantesCampoVacio = DistintoFabCruzTemporal($BDImportRecambios,$condicional);
    $datos['FabricanteMenos2C'] = count($fabricantesCampoVacio);
    return $datos;
    
}

/* ===================  FUNCION ERROR FABRICANTE ===========================================*/
	// Se ejecuta en Paso2ReferenciasCruzadas al terminar de mostrar la pagina 
	// Estamos en ciclofabricante, en el que enviamos el nombre fabricante y si no existe pone en 
	// ESTADO = [ERROR P2-22]:FABRICANTE cruzado no existe.
	// y si existe no hace nada...
	

function errorFab($BDImportRecambios, $BDRecambios) {
    $fab = $_POST['fabricante'];
    $consul = "SELECT * FROM `fabricantes_recambios` WHERE Nombre ='" . $fab . "'";
    $consFa = mysqli_query($BDRecambios, $consul);
    if ($consFa == true){
    $consultaFabricante = $consFa->fetch_assoc();
    }
    if ((int) $consultaFabricante['id'] == 0) {
		// Cambiamos valor variable que respondemos
		$ResultadoBusqFabrica = "No"; 
        $con = "UPDATE `referenciascruzadas` SET `Estado`= '[ERROR P2-22]:FABRICANTE cruzado no existe' WHERE Fabr_Recambio ='" . $fab . "'";
        $consFa = mysqli_query($BDImportRecambios, $con);
       
    } else {
		// Esto quiere decir que existe el fabricante, guardamos el ID del fabricante en IMPORTARRECAMBIOS.
		$ResultadoBusqFabrica = "Si"; 
		$con = "UPDATE `referenciascruzadas` SET `IdFabricanteRec`= '".$consultaFabricante['id']."' WHERE Fabr_Recambio ='" . $fab . "'";
		$consFa = mysqli_query($BDImportRecambios, $con);
	}
    //~ $ResultadoBusqFabrica = $consultaFabricante['id'];
    // Vamos enviar si se encontro o no... 
    header("Content-Type: application/json;charset=utf-8");
    echo json_encode($ResultadoBusqFabrica);
}

function resumenCruz($BDImportRecambios) {
	// Consultamos los fabricantes no encontrados.
	$condicional = "Estado = '[ERROR P2-22]:FABRICANTE cruzado no existe'";
	$FabriNoEncontrado = DistintoFabCruzTemporal($BDImportRecambios,$condicional);
	
	// Contamos los REGISTROS que tiene error: ERR:[FABRICANTE cruzado no existe] 
    $consulta = "SELECT count(Fabr_Recambio) as total FROM `referenciascruzadas` WHERE Estado = '[ERROR P2-22]:FABRICANTE cruzado no existe'";
    $conmys = mysqli_query($BDImportRecambios, $consulta);
    if ($conmys == true) {
    $efab = $conmys->fetch_assoc();
    }
    // Contamos los REGISTROS que tiene error: ERR:[CampoVacio] 
    $consulta2 = "SELECT count(Fabr_Recambio) as total FROM `referenciascruzadas` WHERE Estado = '[ERROR P2-21]:CampoVacio'";
    $conmys2 = mysqli_query($BDImportRecambios, $consulta2);
    if ($conmys2 == true) {
        $eref = $conmys2->fetch_assoc();
    }
    // Contamos los REGISTROS que tengan ESTADO en Blanco.
    
    $datos = ContarVaciosBlanco($BDImportRecambios,'referenciascruzadas');
	$conpro = $datos['NItems']; // Items de tabla que tiene Estado Vacio...
    
    $datos[0]['f'] = $efab['total'];
    $datos[0]['e'] = $eref['total'];
    $datos[0]['c'] = $conpro;
	$datos[0]['FabNo'] = count($FabriNoEncontrado) ;
    header("Content-Type: application/json;charset=utf-8");
    echo json_encode($datos);
}


/* ===================  FUNCION CONTAR REGISTROS CON ESTADO EN BLANCO ==================================*/
	// Esta funcion la utilizamos en varios funciones de este fichero,
	// para saber cuantos registros hay en BDImportRecambios que tenga el campo ESTADO en blanco.
function ContarVaciosBlanco ($BD,$tabla){
	$resultado['conexion'] = 'error';
	$consulta = "SELECT * FROM $tabla WHERE Estado = ''";
    $conmys3 = mysqli_query($BD, $consulta);
    if ($conmys3 == true) {
	$resultado['NItems'] = $conmys3->num_rows;
	$resultado['conexion'] = 'correcto';
    }
	return($resultado);
}

