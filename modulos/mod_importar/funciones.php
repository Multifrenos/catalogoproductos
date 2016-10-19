<?php
/* Con el switch al final y variable $pulsado
 *     	$pulsado = 'borrar'					-> Ejecuta borrar($nombretabla, $BDImportRecambios);
 * 												Se ejecuta en Paso 1  (todos los ficheros )
 *     	$pulsado = 'contar'					-> Ejecuta contador($nombretabla, $BDImportRecambios);
 * 												Se ejecuta en Paso 2 de ListaPrecios --->
 * 		$pulsado = 'comprobar'				-> Ejecuta comprobar($nombretabla, $BDImportRecambios, $BDRecambios);
 * 		$pulsado = 'contarVacios'			-> Ejecuta contarVacios($nombretabla, $BDImportRecambios);
 * 												Se ejecuta en Paso 2 de ListaPrecios --> 
 * 												Cuando pulsamos en comprobar... despues de seleccionar familia y fabricante.
 * 		$pulsado = 'verNuevos'				-> Ejecuta verNuevosRef($BDImportRecambios);
 * 												Se ejecuta en Paso 3 de ListaPrecios.
 * 		$pulsado = 'anahirRecam'			-> Ejecuta anahirRecam($BDRecambios);
 * 		$pulsado = 'BuscarError'			-> Ejecuta BuscarError($BDImportRecambios);
 * 												Se ejecuta en Paso 2 de Referencias Cruzadas al mostrar la pagina.
 * 		$pulsado = 'BuscarErrorFab'			-> Ejecuta BuscarErrorFab($BDImportRecambios);
 * 		$pulsado = 'comPro'					-> Ejecuta errorFab($BDImportRecambios, $BDRecambios);
 * 		$pulsado = 'resumen'				-> Ejecuta resumen($BDImportRecambios);
 * 		$pulsado = 'contarVacioscruzados'	-> Ejecuta contarVaciosCru($BDImportRecambios);
 * 		$pulsado = 'comprobar2cruz'			-> Ejecuta comprobarCruzadas($BDImportRecambios, $BDRecambios);
 * 
 * 
 *  */

/* ===============  REALIZAMOS CONEXIONES  ===============*/

include ("./../mod_conexion/conexionBaseDatos.php");
// creo que esta recogida de datos debe estar antes swich y solo pulsado.
// la tabla solo en la opción que la necesite.
$nombretabla = $_POST['nombretabla'];
$pulsado = $_POST['pulsado'];

/* ===============  FUNCIONES  ===========================*/
/* Function borrar -> Se ejecuta en PASO 1
 * 		Todos los ficheros 
 * */
function borrar($nombretabla, $BDImportRecambios) {
    $consulta = "Delete from " . $nombretabla;
    mysqli_query($BDImportRecambios, $consulta);
}
/* Function contarVacios-> Se ejecuta en Paso 2 de ListaPrecios 
 * Cuando pulsamos en comprobar... despues de seleccionar familia y fabricante.
 * OBJETIVO: 
 * Traer un array con os registros que el estado esté vació.
 * Traemos la RefFabPrin y linea.
 * */
function contarVacios($nombretabla, $BDImportRecambios) {
    // En arrayContarVacios traemos RefFabPrin y linea de los que tengan el estado vació.
    $arrayContarVacios = array();
    $consulta = "SELECT RefFabPrin,linea FROM " . $nombretabla . " where Estado = ''";
    $consultaContador = mysqli_query($BDImportRecambios, $consulta);
    $i = 0;
    while ($row_planets = $consultaContador->fetch_assoc()) {
        $arrayContarVacios[$i]["id"] = $row_planets['RefFabPrin'];
        $arrayContarVacios[$i]["linea"] = $row_planets['linea'];
        $i++;
    }
    header("Content-Type: application/json;charset=utf-8");
    echo json_encode($arrayContarVacios);
}
 /* Funtion contarVaciosCru
  * 	Devolvemos un arrar con 400 registros máximo en blanco
  * 	en el que tenemos el id ->RefProveedor ( que realmente RefRecambioPrincipal )
  * 						 linea-> Que indica la linea (id-primary) de $BDIMPORTAR-referenciascruzadas
  * 						 F_rec -> El nombre del fabricante cruzado...
  * 						 Ref_F-> La referencia cruzada a buscar o añadir.
  * */
function contarVaciosCru($BDImportRecambios) {
    $array = array();
    $consulta = "SELECT * FROM `referenciascruzadas` where Estado = '' limit 400";
    $consultaContador = mysqli_query($BDImportRecambios, $consulta);
    $i = 0;
    while ($row_planets = $consultaContador->fetch_assoc()) {
        $array[$i]["id"] = $row_planets['RefProveedor'];
        $array[$i]["linea"] = $row_planets['linea'];
        $array[$i]["F_rec"] = $row_planets['Fabr_Recambio'];
        $array[$i]["Ref_F"] = $row_planets['Ref_Fabricante'];
        $i++;
    }



    header("Content-Type: application/json;charset=utf-8");
    echo json_encode($array);
}
/* Funtion verNuevosRef -->  Ejecuta verNuevosRef($BDImportRecambios);
 * Se ejecuta en Paso 3 de ListaPrecios.
 * 
 * */
function verNuevosRef($BDImportRecambios) {
    $array = array();
    $consulta = "Select * From listaprecios";
    $conNuevo = mysqli_query($BDImportRecambios, $consulta);
    if ($conNuevo == true){
		$i = 0;
		while ($row_planets = $conNuevo->fetch_assoc()) {
			$array[$i]['coste'] = $row_planets['Coste'];
			$array[$i]['des'] = $row_planets['Descripcion'];
			$array[$i]['ref'] = $row_planets['RefFabPrin'];
			$array[$i]['estado'] = $row_planets['Estado'];
			$array[$i]['id'] = $row_planets['RecambioID'];
			$i++;
		}
	} else {
		$array['error'] = ' Error en consulta';
		}
    header("Content-Type: application/json;charset=utf-8");
    echo json_encode($array);
}

function contador($nombretabla, $BDImportRecambios) {
	// Inicializamos array
    $Tresumen['n'] = 0; //nuevo
    $Tresumen['t'] = 0; //total
    $Tresumen['e'] = 0; //existe
	$Tresumen['v'] = 0; //existe

	// Contamos los registros que tiene la tabla
    $consulta = "SELECT count(linea) as vacio FROM " . $nombretabla. " WHERE Estado = ''";;
    $consultaContador = mysqli_query($BDImportRecambios, $consulta);
    if ($consultaContador == true){
        $contador = $consultaContador->fetch_assoc();
    }
    $Tresumen['v'] = $contador['vacio']; // vacio
    // Contamos los registros que tiene la tabla
    $consulta = "SELECT count(linea) as total FROM " . $nombretabla;
    $consultaContador = mysqli_query($BDImportRecambios, $consulta);
    if ($consultaContador == true){
        $contador = $consultaContador->fetch_assoc();
    }
    $Tresumen['t'] = $contador['total']; // total
	// Contamos los registros que tiene la tabla nuevo
    $consulta = "SELECT count(linea) as nuevo FROM " . $nombretabla. " WHERE Estado = 'nuevo'";
    $consultaContador = mysqli_query($BDImportRecambios, $consulta);
    if ($consultaContador == true){
        $contador = $consultaContador->fetch_assoc();
    }
    $Tresumen['n'] = $contador['nuevo']; //nuevo
	// Contamos los registros que tiene la tabla existente
    $consulta = "SELECT count(linea) as existe FROM " . $nombretabla. " WHERE Estado = 'existe'";
    $consultaContador = mysqli_query($BDImportRecambios, $consulta);
    if ($consultaContador == true){
        $contador = $consultaContador->fetch_assoc();
    }
    $Tresumen['e'] = $contador['existe']; //existe
    header("Content-Type: application/json;charset=utf-8");
    echo json_encode($Tresumen);
}
/* Funtion BuscarErrorFab -->  Ejecuta PASO2REFERENCIAS CRUZADAS TERMINAR DE CARGAR 
 * Lo que hace un array con los distintos fabricantes que hay en la tabla ReferenciasCruzadas 
 * 
 * */
function BuscarErrorFab($BDImportRecambios) {
    $array = array();
    $consulta = "SELECT DISTINCT(Fabr_Recambio) FROM `referenciascruzadas` WHERE Estado = ''";
    $conNuevo = mysqli_query($BDImportRecambios, $consulta);
    $i = 0;
    while ($row_planets = $conNuevo->fetch_assoc()) {
        $array[$i]['Fabr_Recambio'] = $row_planets['Fabr_Recambio'];

        $i++;
    }
    header("Content-Type: application/json;charset=utf-8");
    echo json_encode($array);
}
/* =========================  Funcion de consulta  ========================================*/
   // Encontramos que la tabla listaprecios tiene registros con el estado VACIO, entonces 
   // comprobamos en la tabla REFERENCIASCRUZADAS de BD de RECAMBIOS, si existe la referencia
   // 		-Si existe se pone en ESTADO = "existe"
   // 		-NO existe se pone en ESTADO = "nuevo"
   // Estos cambios son el campor ESTADO de la tabla LISTAPRECIOS de BD IMPORTARRECAMBIOS.
            
function comprobar($nombretabla, $BDImportRecambios, $BDRecambios) {
    $id = $_POST['idrecambio'];
    $l = $_POST['linea'];
    $f = $_POST['fabricante'];
    // Inicializamos variables
    $consfinal = 0;
    $existente = 0;
    $nuevo = 0;
    $consul = "SELECT * FROM referenciascruzadas where RefFabricanteCru ='" . $id . "'";
    $consultaReca = mysqli_query($BDRecambios, $consul);
    if ($consultaReca == true) {
        // Controlamos que la consulta sea correcta, ya que sino lo es genera un error la funcion fetch
        $consfinal = $consultaReca->fetch_assoc();
    }
        if ($consfinal['RefFabricanteCru'] == $id && $consfinal['IdFabricanteCru'] == $f) {
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
	

function comprobarCruzadas($BDImportRecambios, $BDRecambios) {
    $ref = $_POST['idrecambio']; // Realmente es la referencia del proveedor principal, no es id recambio... 
    $l = $_POST['linea']; // La utilizamos para modificar campo ESTADO de BDIMPORTAR-REFERENCIASCRUZADAS.
    $f = $_POST['fabricante']; // Id de fabricante principal...
    $ref_f = $_POST['Ref_fa']; // Referencia de fabricante cruzado.
    $fab_ref = $_POST['Fab_ref'];// Nombre fabricante cruzado.
    // Reiniciamos 
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

			
			$creaCru = "INSERT INTO `referenciascruzadas`( `RecambioID`, `IdFabricanteCru`, `RefFabricanteCru`) VALUES (0," . $id . "," . "'".$ref_f. "'" . ")";
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
		$consul = "UPDATE `referenciascruzadas` SET `Estado`='ERROR[Referencia Principal]' WHERE RefProveedor ='" . $ref . "' and linea ='".$l."'";
		mysqli_query($BDImportRecambios, $consul);
		$ControlPaso = $ControlPaso."ERROR[Referencia Principal]";

    }
    $datos[0]['respuesta'] = $ControlPaso;
	$datos[0]['busqueda'] = $ControlBusqueda;
    header("Content-Type: application/json;charset=utf-8");
    echo json_encode($datos);
}
/* ===================  FUNCION BUSCAR ERROR ===========================================*/
	// Se ejecuta en Paso2ReferenciasCruzadas al terminar de mostrar la pagina 
	// donde lo que hace es buscar aquellos registros que el nombre Fabricante Recambio
	// o la referencia del fabricante tenga menos de dos caracteres.
	
function BuscarError($BDImportRecambios) {
    // Llamamos funcion contar registro con estado en blanco
    $datos = ContarVaciosBlanco($BDImportRecambios,'referenciascruzadas');
     
    $consul = "UPDATE `referenciascruzadas` SET `Estado`='ERR:[CampoVacio]' WHERE LENGTH(Fabr_Recambio) < 2 or LENGTH(Ref_Fabricante) < 2";
    $ConsErr = $BDImportRecambios->query($consul);
    $datos['Menos2C'] = $BDImportRecambios->affected_rows; 
    header("Content-Type: application/json;charset=utf-8");
    echo json_encode($datos);
    //~ return $datos;
    
}

function anahirRecam($BDRecambios) {

    $id = $_POST['idrecam'];
    $tab = $_POST['nombretabla'];
    $familia = $_POST['familia'];
    $fabricante = $_POST['fabricante'];
    $estado = $_POST['estado'];
    $ref = $_POST['referen'];
    $coste = $_POST['coste'];
    $descripcion = $_POST['descrip'];
    // Inicializamos variables: 
    $fecha = date('Y-m-d');
    $desdef = ''; //Descripcion
    $bfa= array() ; // Nombre familia recambio.
    $bFa= array(); // Nombre fabricante.
    $contador = array(); // Donde tomamos Margen y Iva
    $margen = 0 ;
    $iva = "1.";
    $pvp = 0; 
    if ($estado == "nuevo") {
		// Buscamos en familias_recambios la familia seleccionada.
		// Obtener el descripcion de familia que lo necesitamos para meter descripcion de 
		// producto.
        $cons = "SELECT * FROM `familias_recambios` WHERE id = " . $familia;
        $consFa = mysqli_query($BDRecambios, $cons);
        if ($consFa == true){
            $bfa = $consFa->fetch_assoc();
        }
        if (isset($bfa["Familia_es"])== true){
        $desdef = $bfa["Familia_es"];
        } else {
        echo "algo";
        }
		// Buscamos en fabricantes_recambios la familia seleccionada.
        $consulFab = "SELECT * FROM `fabricantes_recambios` where id =" . $fabricante;
        $cFa = mysqli_query($BDRecambios, $consulFab);
        if ($cFa == true){
        $bFa = $cFa->fetch_assoc();
        }
        if (isset($bFa['Nombre'])== true){
        $desdef .= " " . $bFa['Nombre'];
        }
        $desdef .= " " . $descripcion;
			
		$pvp = ($coste + (($coste * 40) / 100)) * 1.21;

        $consul = "INSERT INTO " . $tab . "( `Descripcion`, `coste`, `margen`, `iva`, `pvp`, `IDFabricante`, `FechaActualiza`) VALUES ('" . $desdef . "'," . $coste . ",40,21," . $pvp . "," . $fabricante . ",'" . $fecha ."')";
        $BDRecambios->query($consul);
        $resul = $BDRecambios->insert_id;

        $consulta = "INSERT INTO `referenciascruzadas`( `IdFabricanteCru`, `RecambioID`, `RefFabricanteCru`) VALUES ('" . $fabricante . "','" . $resul . "','" . $ref . "')";
        $BDRecambios->query($consulta);
        $resFinal = $BDRecambios->insert_id;

        $consulta = "INSERT INTO `recamb_familias`( `IdRecambio`, `IdFamilia`) VALUES (" . $resul . "," . $familia . ")";
        $BDRecambios->query($consulta);
        $resFinal2 = $BDRecambios->insert_id;
    } else {
		if ($estado == "existe") {
			$cnsulta = "select * from recambios where id =" . $id;
			$consultaReca = mysqli_query($BDRecambios, $cnsulta);
			if ($consultaReca == true){
				$contador = $consultaReca->fetch_assoc();
			}
			$margen = $margen + $contador['margen'];
			$iva .= $contador['iva'];
			if ($coste != 0 && $margen !=0 && $iva !=0 ){
			$pvp = ($coste + (($coste * $margen) / 100)) * $iva;
			}
			$modifcoste = "UPDATE `recambios` SET `coste`=" . $coste . ",`pvp`=" . $pvp . ",`FechaActualiza`='" . $fecha . "' WHERE `id` =" . $id;
			mysqli_query($BDRecambios, $modifcoste);
		}
    }
}

/* ===================  FUNCION ERROR FABRICANTE ===========================================*/
	// Se ejecuta en Paso2ReferenciasCruzadas al terminar de mostrar la pagina 
	// Estamos en ciclo, en el que enviamos el nombre fabricante y si no existe pone en 
	// ESTADO = ERR:[FABRICANTE cruzado no existe]
	// y si existe no hace nada...
	

function errorFab($BDImportRecambios, $BDRecambios) {
    $fab = $_POST['fabricante'];
    $consul = "SELECT * FROM `fabricantes_recambios` WHERE Nombre ='" . $fab . "'";
    $consFa = mysqli_query($BDRecambios, $consul);
    $ResultadoBusqFabrica = "Si"; 
    if ($consFa == true){
    $consultaFabricante = $consFa->fetch_assoc();
    }
    if ((int) $consultaFabricante['id'] == 0) {
		// Cambiamos valor variable que respondemos
		$ResultadoBusqFabrica = "No"; 
        $con = "UPDATE `referenciascruzadas` SET `Estado`= 'ERR:[FABRICANTE cruzado no existe]' WHERE Fabr_Recambio ='" . $fab . "'";
        $consFa = mysqli_query($BDImportRecambios, $con);
       
    }
    //~ $ResultadoBusqFabrica = $consultaFabricante['id'];
    // Vamos enviar si se encontro o no... 
    header("Content-Type: application/json;charset=utf-8");
    echo json_encode($ResultadoBusqFabrica);
}

function resumenCruz($BDImportRecambios) {
	// Contamos los REGISTROS que tiene error: ERR:[FABRICANTE cruzado no existe] 
    $consulta = "SELECT count(Fabr_Recambio) as total FROM `referenciascruzadas` WHERE Estado = 'ERR:[FABRICANTE cruzado no existe]'";
    $conmys = mysqli_query($BDImportRecambios, $consulta);
    if ($conmys == true) {
    $efab = $conmys->fetch_assoc();
    }
    // Contamos los REGISTROS que tiene error: ERR:[CampoVacio] 
    $consulta2 = "SELECT count(Fabr_Recambio) as total FROM `referenciascruzadas` WHERE Estado = 'ERR:[CampoVacio]'";
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

    header("Content-Type: application/json;charset=utf-8");
    echo json_encode($datos);
}


/* ===================  FUNCION CONTAR REGISTROS CON ESTADO EN BLANCO ==================================*/
	// Esta funcion la utilizamos en varios procesos, para saber cuantos registros hay en BDImportRecambios
	// que tenga el campo ESTADO en blanco.
function ContarVaciosBlanco ($BD,$tabla){
	$datos['conexion'] = 'error';
	$consulta = "SELECT * FROM $tabla WHERE Estado = ''";
    $conmys3 = mysqli_query($BD, $consulta);
    if ($conmys3 == true) {
	$datos['NItems'] = $conmys3->num_rows;
	$datos['conexion'] = 'correcto';
    }
	return($datos);
}

/* ===================  FUNCION BUSCAR REFERENCIA CRUZADAS  ==================================*/
	// Esta funcion la utilizamos en varios procesos, para obtener la ID de la referencia cruzada de BDRecambios
	// Necesito los siguientes datos:
	


/* ===============  SWICH PARA EJECUTAR FUNCIONES SEGUN OPCION (PULSADO) ===============*/

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
        contarVacios($nombretabla, $BDImportRecambios);
        break;
    case 'verNuevos':
        verNuevosRef($BDImportRecambios);
        break;
    case 'anahirRecam':
        anahirRecam($BDRecambios);
        break;
    case 'BuscarError':
        BuscarError($BDImportRecambios);
        break;
    case 'BuscarErrorFab':
        BuscarErrorFab($BDImportRecambios);
        break;
    case 'comPro':
        errorFab($BDImportRecambios, $BDRecambios);
        break;
    case 'resumen':
        resumenCruz($BDImportRecambios);
        break;
    case 'contarVacioscruzados':
        contarVaciosCru($BDImportRecambios);
        break;
    case 'comprobar2cruz':
        comprobarCruzadas($BDImportRecambios, $BDRecambios);
        break;
}

/* ===============  CERRAMOS CONEXIONES  ===============*/

mysqli_close($BDImportRecambios);
mysqli_close($BDRecambios);
