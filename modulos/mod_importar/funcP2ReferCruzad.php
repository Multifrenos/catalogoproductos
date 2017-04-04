<?php
/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogo productos Soluciones Vigo.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero
 * @Descripcion	Funciones en php para realizar las tareas.
 *  */


 /* Funtion obtenerReferenciasPrincipales
  * 	Devolvemos un array con loss RefProveedor principal, que estado esté blanco y tenga ID_Fabricantes
  * 	obtenemos RefProveedor ( que realmente RefRecambioPrincipal )
  * 	Esta funcion devuelve dos opciones según el proceso que estemos ($condicional)
  * 			1.- PROCESO2-> Devuelve Referencias Principales distintas ( campo: RefProveedor )
  * 			2.- PROCESO3-> Devuelve RegistroLineas con los campos ( 'Ref_Fabricante','RecambioID','IdFabricaCruzado')
  * */
function obtenerReferenciasPrincipales($BDImportRecambios,$ConsultaImp,$condicional) {
    $array = array();
    $tabla ="referenciascruzadas";
	$whereC = " WHERE Estado = '' and IdFabricaCruzado <> 0";
	if ($condicional == "proceso2") {
		$whereC = $whereC." and RecambioID = 0";
		$campo= 'RefProveedor';
		//Ejecutamos consulta.
		$array = $ConsultaImp->distintosCampo($BDImportRecambios,$tabla,$campo,$whereC);
	}
	if ($condicional == "proceso3"){
		// Obtenemos registros para saber NUEVO o EXISTE
		$campo = array('Ref_Fabricante','RecambioID','IdFabricaCruzado');
		$array = $ConsultaImp->registroLineas($BDImportRecambios,$tabla,$campo,$whereC); 	
	}
	
	if ($condicional == "proceso4"){
		// Obtenemos registros que son NUEVOs y hay crearlo
		// Para que no pese, solo vamos obtener lineas.
		// Aquí el where cambia
		$whereC = " WHERE Estado = 'Nuevo' and IdFabricaCruzado <> 0";
		$campo = array('linea');
		$array = $ConsultaImp->registroLineas($BDImportRecambios,$tabla,$campo,$whereC); 	
	}
	if ($condicional == "proceso5"){
		// Obtenemos registros Nuevos creados, duplicados y que son Existen 
		// Solo obtenemos lineas para que no pese mucho.
		$whereC = " WHERE Estado = 'Nuevo Duplicado' or Estado = '[Añadido] Referencia Cruzada' or Estado = 'Existe referencia cruzada'";
		$campo = array('linea');
		$array = $ConsultaImp->registroLineas($BDImportRecambios,$tabla,$campo,$whereC); 	
	
	}
	
	if ($condicional == "proceso6"){
		// Obtenemos registros Existe 
		// Solo obtenemos lineas para que no pese mucho.
		$whereC = " WHERE Estado = 'Nuevo Duplicado' or Estado = '[Añadido] Referencia Cruzada' or Estado = 'Existe referencia cruzada'";
		$campo = array('linea', 'RecambioID','IdFabricaCruzado','IDRefCruzada');
		
		$array = $ConsultaImp->registroLineas($BDImportRecambios,$tabla,$campo,$whereC); 	
	
	}
	
	
	
	
	$array['proceso'] = $condicional;
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
	//~ if ($conNuevo == true) {
		while ($row_planets = $conNuevo->fetch_assoc()) {
			$array[$i]['Fabr_Recambio'] = $row_planets['Fabr_Recambio'];
			$i++;
		}
	//~ }
   //~ $array['consulta'] = 'Prueba';//$consulta;
   return $array;
}

/* ===================  FUNCION BuscarRecamPrincipal ===========================================*/
// El objetivo es buscar si existe el recambio y si no existe entonces se cambia el estado como 
// Estado ='[ERROR P2-23]:Referencia Principal no existe.'

function BuscarRecamPrincipal($BDImportRecambios, $BDRecambios,$ConsultaImp,$arrayDistintosVacios,$Fabricante) {
    // poniendo  a= var_export($arrayVacios) no crea un string para poder verlo.
    // Recibimos en $arrayVacios  {"RefProveedor":"A110001"..}
    // Los distintos refProveedor principal que su estado esta blanco y ID_Fabricante es distinto 0 .
    // Ten en cuenta que se tiene que recibir solo 200 , ya que puede haber un limite en variable en el servidor.
    // INICIALIZAMOS VARIABLES
    $array= array();
    $RefNoencontradas = array();
    $ReferenciaEncontrada = array();
    $array['Ref_Principal_Entregadas'] = count($arrayDistintosVacios);
    $f= $Fabricante;
	$ErrorRefPrincipal = 0;
    $i = 0;

    foreach ( $arrayDistintosVacios as $referencia) {
		// Inicializamos varibles
		$ref = '';
		$idRecambio = 0 ;
		// 	1.- Comprobamos si existe la referencia principal.
		// 		Buscamos en BDRecambios en tabla referencias cruzadas si existe la referencia principal.
		$ref= $referencia['RefProveedor'];
		$consul = "SELECT * FROM `referenciascruzadas` WHERE RefFabricanteCru = '" . $ref . "' and IdFabricanteCru = '" . $f . "'";
		$ejconRefFab = mysqli_query($BDRecambios, $consul);
		$resul = mysqli_fetch_assoc($ejconRefFab);
		$idRecambio = $resul['RecambioID']; // Id de recambio que vamos a cruzar
		if ($idRecambio == 0 ) {
		// Esto es que no encontro la referencia principal
			$RefNoencontradas[$i] ='"'.$ref.'"';
		} else {
		//~ $array[$i] = var_export ($ref);
		$ReferenciaEncontrada[$i]["id"] = $idRecambio; 
		$ReferenciaEncontrada[$i]['Referencia'] = $ref; 
		}	
		$i = $i +1;

	}
	$array['Ref_Principal_Entregadas'] = $i;
	// Ahora cambiamos el estado de todos las referencias que no se encontraron.
	// En BDImportar/ReferenciCruzadas en campo Estado
	// Estado ='[ERROR P2-23]:Referencia Principal no existe.'
	$ReferenciasError = 'RefProveedor='.implode(' OR RefProveedor=',$RefNoencontradas);
	$consul = "UPDATE `referenciascruzadas` SET `Estado`='[ERROR P2-23]:Referencia Principal no existe.' WHERE ".$ReferenciasError;
	$ConsErr = $BDImportRecambios->query($consul);
	$ErrorRefPrincipal= $BDImportRecambios->affected_rows;
	$array['RegistrosErrorRefPrincipal'] = $ErrorRefPrincipal;
    
    // Ahora creo implode para las encontradas.
    $array['Consulta1'] = '';
    $array['Consulta2'] = '';
    foreach ( $ReferenciaEncontrada as $referencia){
		$array['Consulta1'] = $array['Consulta1'].' WHEN "'.$referencia['Referencia'].'" THEN '.$referencia["id"];
		if (strlen($array['Consulta2']) >0 ){
			$array['Consulta2'] = $array['Consulta2'].',"'.$referencia['Referencia'].'"';
		} else {
			$array['Consulta2'] = '"'.$referencia['Referencia'].'"'	;
		}
	}
    // Montamos update para añadir ID de Recambio
    $consul = "UPDATE  `referenciascruzadas` SET `RecambioID` = CASE `RefProveedor` ".$array['Consulta1']." END WHERE RefProveedor IN (".$array['Consulta2'].")";
    $Anhadir = $BDImportRecambios->query($consul);
	$AnhadirIDRecambio= $BDImportRecambios->affected_rows;
    $array['RegistroAnhadirIDRecambio'] = 	$AnhadirIDRecambio;
    return $array;
   
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
		$con = "UPDATE `referenciascruzadas` SET `IdFabricaCruzado`= '".$consultaFabricante['id']."' WHERE Fabr_Recambio ='" . $fab . "'";
		$consFa = mysqli_query($BDImportRecambios, $con);
	}
    //~ $ResultadoBusqFabrica = $consultaFabricante['id'];
    // Vamos enviar si se encontro o no... 
    header("Content-Type: application/json;charset=utf-8");
    echo json_encode($ResultadoBusqFabrica);
}

function resumenCruz($BDImportRecambios,$ConsultaImp) {
	// Inicializamos variables
	$datos = array();
	$ArrayErrores = array();
	$ArrayFabricantes = array();
	$ArrayFabricante = array();
	$ConsultaRefPrincipales = array();
	$ArrayRefPrincipales = array();
	$nombretabla = 'referenciascruzadas';
	// Contamos TODOS LOS REGISTROS DE TABLA
	$whereC= '';
	$totalRegistro = $ConsultaImp->contarRegistro($BDImportRecambios,$nombretabla,$whereC);
	// Contamos los REGISTROS que tengan ESTADO en Blanco.Obtenemos NItems y conexion
    $whereC =" WHERE Estado = '' ";
    $datos['NItemsEstadoBlanco'] = $ConsultaImp->contarRegistro($BDImportRecambios,$nombretabla,$whereC);
    // Contamos los REGISTROS que tengan ESTADO en Blanco con ID Recambio. 
    $whereC =" WHERE Estado = '' AND RecambioID = 0";
    $datos['NItemsCRecambio'] = $ConsultaImp->contarRegistro($BDImportRecambios,$nombretabla,$whereC);
    // Antes de continuar tenemos comprobar si ya hizo alguna comprobación o no.
   	// Si Registros totales es igual Registros a procesar y ademas estos tiene IDs Fabricantes y Recambio en 0
   	if ( $datos['NItemsCRecambio'] == $totalRegistro ){
		// Quiere decir que no hizo ninguna comprobación y hacemos la primera comprobación
		$consul = "UPDATE `referenciascruzadas` SET `Estado`='[ERROR P2-21]:CampoVacio' WHERE LENGTH(Fabr_Recambio) < 2 or LENGTH(Ref_Fabricante) < 2";
		$ConsErr = $BDImportRecambios->query($consul);
	}
   	// Consultamos distintos fabricantes (total, no encontrados, no buscados)
	$campo = 'Fabr_Recambio';
   	$ArrayFabricantes[1] = "Estado = '[ERROR P2-22]:FABRICANTE cruzado no existe'"; //Fabricantes no encontrados.
   	$ArrayFabricantes[2] = "IdFabricaCruzado <>0"; 					// Fabricantes que ya fueron buscados.
   	$ArrayFabricantes[3] = "Estado = '' and IdFabricaCruzado =0";	// Fabricantes que NO fueron buscados.
   	$ArrayFabricantes[4] = "Estado = '[ERROR P2-21]:CampoVacio'"; 	// Fabricantes descartados por campo vacio.
   	$ArrayFabricantes[5] = ""; 	// Referencias Cruzadas creadas.

   	$NErrores = count($ArrayFabricantes);
		for ($i = 1; $i <= $NErrores; $i++) {
			$whereC = '';
			if ($i <5){
			$whereC = " WHERE ".$ArrayFabricantes[$i];
			}
			$ArrayFabricante = $ConsultaImp->distintosCampo($BDImportRecambios,$nombretabla,$campo,$whereC);
			$ArrayFabricantes[$i]= $ArrayFabricante['NItems'];
		}   	
   
	// Contamos registros que tiene errores o ya tienen estado.
	$ArrayErrores[1]['estado'] = '[ERROR P2-22]:FABRICANTE cruzado no existe';
	$ArrayErrores[2]['estado'] = '[ERROR P2-21]:CampoVacio';
	$ArrayErrores[3]['estado'] = '[ERROR P2-23]:Referencia Principal no existe.';
	$ArrayErrores[4]['estado'] = 'Nuevo'; //Que ya esta listo para grabar, pero no se hizo.
	$ArrayErrores[5]['estado'] = '%Existe referencia cruzada%'; //Existe pero no se comprobo si existe cruce.
	$ArrayErrores[6]['estado'] = '%Nuevo Duplicado%'; //Contiene -Ya que se pudo o no hacer. .
	$ArrayErrores[7]['estado'] = '[Añadido] Referencia Cruzada'; //Que ya esta listo para grabar, pero no se hizo.
	$ArrayErrores[8]['estado'] = '[COMPROBADO]Existe referencia cruzada y cruce'; //Existe y se comprobo que existe cruce.

	
		// Ahora realizamos bluce de consultas.
		$NErrores = count($ArrayErrores);
		for ($i = 1; $i <= $NErrores; $i++) {
			$whereC = ' WHERE Estado LIKE "'.$ArrayErrores[$i]['estado'].'"';
			$ArrayErrores[$i]['Nitems'] = $ConsultaImp->contarRegistro($BDImportRecambios,$nombretabla,$whereC);
		}
	// Contamos DISTINTAS REFERENCIAS ( NO REGISTROS)
	//			  	1.- ReferenciasPrincipales distinta ya tienes asociado ID Recambio.
    // 				2.- ReferenciasPrincipales hay distintas.
	// 				3.- ReferenciasPrincipales faltan por meter ID Recambio.//
    //				4.- ReferenciasPrincipales que NO existen.
    $tabla ="referenciascruzadas";
    $campo= 'RefProveedor';
    $ConsultaRefPrincipales[1] = " Estado = '' and IdFabricaCruzado <> 0 and `RecambioID`<> 0";
	$ConsultaRefPrincipales[2] = " IdFabricaCruzado <> 0";
	$ConsultaRefPrincipales[3] = " Estado = '' and IdFabricaCruzado <> 0 and `RecambioID`= 0";
	$ConsultaRefPrincipales[4] = " Estado = '[ERROR P2-23]:Referencia Principal no existe.'";

		// Ahora realizamos bucle de consultas
		$NErrores = count($ConsultaRefPrincipales);
		for ($i = 1; $i <= $NErrores; $i++) {
			$whereC = ' WHERE '.$ConsultaRefPrincipales[$i];
			$ArrayRefPrincipal = $ConsultaImp->distintosCampo($BDImportRecambios,$tabla,$campo,$whereC);
			$ArrayRefPrincipales[$i] = $ArrayRefPrincipal['NItems'];
		}
    // Para terminar de hacer proceso control, volvemos a 
    // Contamos los REGISTROS que tengan ESTADO en Blanco.Obtenemos NItems y conexion
    $whereC =" WHERE Estado = '' ";
    $datos['NItemsEstadoBlanco'] = $ConsultaImp->contarRegistro($BDImportRecambios,$nombretabla,$whereC);
    
    // Cubrimos array que devolvemos.
    $datos['error22'] 	= $ArrayErrores[1]['Nitems'];
    $datos['error21'] 	= $ArrayErrores[2]['Nitems'];
    $datos['error23'] 	= $ArrayErrores[3]['Nitems'];
	$datos['NuevRefCruzadaPendi'] 	= $ArrayErrores[4]['Nitems']; // No existe Referencia cruzada en BDRecambios
	$datos['ExisteRefFaltaCruce'] 	= $ArrayErrores[5]['Nitems']; // Existe Referencia Cruzada pero no sabemos si existe cruce.
	$datos['NuevRefCruzDuplicada'] 	= $ArrayErrores[6]['Nitems']; // Estas son las duplicadas de la Referencia Cruzadas Nuevas
	$datos['NuevasCreadas'] 	= $ArrayErrores[7]['Nitems']; // Estas son las duplicadas de la Referencia Cruzadas Nuevas
	$datos['ExisteRefCruce'] 	= $ArrayErrores[8]['Nitems']; // Existe referencia cruzadas y cruce

	$datos['FabNoEncontrado'] 	= $ArrayFabricantes[1];
	$datos['FabYaBuscado'] 		= $ArrayFabricantes[2];
	$datos['FabNoBuscado'] 		= $ArrayFabricantes[3];
	$datos['FabError21'] 		= $ArrayFabricantes[4];
	$datos['Totalfabcru'] 		= $ArrayFabricantes[5]; // Distintos fabricantes encontrados
  
    $datos['RefPrinYAIDRecam'] 	= $ArrayRefPrincipales[1];
    $datos['RefPrinEncontradas']= $ArrayRefPrincipales[2];
	$datos['RefPrinPendIDRecam']= $ArrayRefPrincipales[3];
	$datos['NRefPrinNOenc'] 	= $ArrayRefPrincipales[4];

	
	
	
	return $datos;
}


function NuevoExisteDuplicado($BDImportRecambios, $BDRecambios,$ConsultaImp,$arrayDistintosVacios,$Fabricante){
	// Objetivo: 
	// 		1.- Buscar Referencias Cruzadas NUEVAS
	//		2.- Referencias Cruzadas que EXISTEN
	//		3.- Buscar las referencias NUEVAS que son DUPLICADAS.
	// Poner como estado: 'Nuevo','Existe referencia cruzada','Nueva Duplicada'
	
	$array= array();
    $resultado = array();
    $inRefFabricante = array();
    $inIdFabricante = array();
	$consultas = array();
	$fecha = date('Y-m-d');
    $array['Ref_Principal_Entregadas'] = count($arrayDistintosVacios);
	// Campos que encontramos $arrayDistintosVacios
	// Ref_Fabricante , RecambioID, IdFabricaCruzado
	$i = 0;
	
	foreach ( $arrayDistintosVacios as $referencia) {
		// Creamos array con datos recibidos.
		$inRefFabricante[$i] = '"'.$referencia['Ref_Fabricante'].'"';
		$inIdFabricante[$i] = $referencia['IdFabricaCruzado'];
		$i++;
	}
	$ConsulInRefFabricante = implode(',',$inRefFabricante);
	$ConsulInIdFabricante = implode(',',$inIdFabricante);
	
	// Ahora consultamos en tabla referencias cruzadas esos datos para obtener los registros de esos solamente.
	$campos = array ('id','RecambioID','IdFabricanteCru','RefFabricanteCru','FechaActualiza' ); 
	$nombretabla = 'referenciascruzadas';
	$whereC = " WHERE `RefFabricanteCru` IN (".$ConsulInRefFabricante.") AND `IdFabricanteCru` IN (". $ConsulInIdFabricante.")";
	$resultados = $ConsultaImp->registroLineas($BDRecambios,$nombretabla,$campos,$whereC); 

	// Primero vamos localizar los que NO EXISTEN referenciascruzadas.
	// Quiere decir que hubo resultados, por lo que algunos existen o todos.
	$i = 0 ;
	$x= 0;
	$ArrayEncontrados = array();
	foreach ($arrayDistintosVacios as $Enviado){
		$idRefCruzada = 0;
		$i++;
		$ArrayEncontrados[$i]['IdFabricaCruzado'] 	= $Enviado['IdFabricaCruzado'];
		$ArrayEncontrados[$i]['Ref_Fabricante'] 	= $Enviado['Ref_Fabricante'];
		$ArrayEncontrados[$i]['RecambioID'] 		= $Enviado['RecambioID'];
		// Ahora creamos foreach del resultado
		if (($resultados['NItems'] !=0 )||( isset($resultados['NItems']) == false)) {
			foreach ( $resultados as $resultado ) {
			$idRefCruzada = 0;
				if (isset($resultado["RefFabricanteCru"])){
					if ($Enviado['Ref_Fabricante'] == $resultado["RefFabricanteCru"] && $Enviado['IdFabricaCruzado'] == $resultado['IdFabricanteCru']){
						$idRefCruzada = $resultado['id'];
						break;
					}
				}
			}
			// Como salimos foreach anterior, entonces ahora comprobamos si.
			if ($idRefCruzada != 0) {
				// Quiere decir que existe, añadimos a array
				// Hago array separado para hacer implode ya que no se como hacer... 
				//~ $inRefFabricante[$i] = '"'.$resultado['RefFabricanteCru'].'"';
				$ArrayEncontrados[$i]['IDReferenciaCruzado'] = $idRefCruzada;
				$ArrayEncontrados[$i]['Buscado'] = 'Encontrado';
			}
		} 
		if ($idRefCruzada == 0) {
		// Quiere decir que NO existe
		$ArrayEncontrados[$i]['Buscado'] = 'NoEncontrado';
		}
		
	}
	// Ahora falta añadir a estado, NUEVO o EXISTE en referenciascruzadas de BDImportar
	$i = 0;
	$case= '';
	$WhereEstado = array();
	foreach ( $ArrayEncontrados as $Encontrado) {
		$i++;
		if ($Encontrado['Buscado'] =='NoEncontrado') { 
			// Nueva referencia Cruzada
			$WhereEstado['Nuevo'][$i] = '(RecambioID ='.$Encontrado['RecambioID'].' AND IdFabricaCruzado ='.$Encontrado['IdFabricaCruzado'].' AND  Ref_Fabricante ="'.$Encontrado['Ref_Fabricante'].'")';
		} else {
			// Existe Referencia Cruzada
			// Montamos case para guardar IDReferencia Cruzada en DBImport
			$WhereEstado['Existe'][$i] = '(RecambioID ='.$Encontrado['RecambioID'].' AND IdFabricaCruzado ='.$Encontrado['IdFabricaCruzado'].' AND  Ref_Fabricante ="'.$Encontrado['Ref_Fabricante'].'")';
			$case = $case.' WHEN '.$WhereEstado['Existe'][$i].' THEN '.$Encontrado['IDReferenciaCruzado'];
		
		}
		
	}
	if (isset($WhereEstado['Nuevo'])) {
		$whereNuevo = implode(' OR ',$WhereEstado['Nuevo']);
		$consultas[1] = 'UPDATE referenciascruzadas SET Estado ="Nuevo" WHERE '.$whereNuevo;
	}
	if (isset($WhereEstado['Existe'])) {
		$whereExiste = implode(' OR ',$WhereEstado['Existe']);
		$consultas[2] = 'UPDATE referenciascruzadas SET Estado ="Existe referencia cruzada",`IDRefCruzada` = CASE '.$case.' end WHERE '.$whereExiste;
	}
	//~ $array['ArrayEncontrados'] =$ArrayEncontrados;
	//~ $array['WhereEstado'] =$WhereEstado;
	// Inicializamos porque nos hace falta enviar datos de los tres array para evitar error js
	$array['resultado'][1] =0; // Nuevo
	$array['resultado'][2]= 0; // Existe 
	$array['resultado'][3]= 0; // Nuevo duplicado
	// Ahora ejecutamos el update
	$i= 1;
	foreach  ($consultas as $consulta) { 
		if ($BDImportRecambios->query($consulta)){;
			$RegistrosCambiados= $BDImportRecambios->affected_rows;
			$array['resultado'][$i] = 	$RegistrosCambiados;
		} else {
			// Quiere decir que hubo error en la consulta.
				$array['consulta'][$i] = $consulta;
				$array['error'][$i] = $BDImportRecambios->error_list;
		}
	$i++;
	}
	// Ahora tenemos que comprobar si hay duplicados en Nuevos.
	// Esto realmente solo deberíamos hacerlo al final ciclo, no en todo el ciclo...
	// Es adsurdo repetir...
	$consultaDuplicado = "SELECT `linea` , `Ref_Fabricante` , COUNT( * ) Total FROM referenciascruzadas WHERE `Estado` = 'Nuevo' GROUP BY `Ref_Fabricante` , `IdFabricaCruzado` HAVING COUNT( * ) >1";
	$ReferenciasDuplicadas =$BDImportRecambios->query($consultaDuplicado);
	if (isset($ReferenciasDuplicadas)){;
		// Registros que están duplicados.
		//~ $array['ReferenciasDuplicadas'] = $BDImportRecambios->affected_rows;
		$array['ReferenciasDuplicadas'] = $ReferenciasDuplicadas->num_rows; 
	}else {
	// Quiere decir que hubo error en la consulta.
		$array['consulta'][3] =$consultaDuplicado;
		$array['error'][3] = $BDImportRecambios->error_list;
	}
	// Ahora creamos array con resultado
	$arrayDuplicados= array();
	if ($array['ReferenciasDuplicadas'] >0){
		// quiere decir hay registros duplicados.
		$i= 0;
		while ($row_planets = $ReferenciasDuplicadas->fetch_assoc()) {
			$arrayDuplicados[$i]='"'.$row_planets['Ref_Fabricante'].'"';
			$i++;
		}
	}
	// Montamos consulta para obtener todos los registros de las referencias duplicadas.
	$consultaDuplicado = 'SELECT * FROM `referenciascruzadas` WHERE `Ref_Fabricante` in ('.implode(',',$arrayDuplicados).') and Estado= "Nuevo" ORDER BY `Ref_Fabricante` ASC ';
	$ResultadoDuplicadas =$BDImportRecambios->query($consultaDuplicado);
	if (isset($ResultadoDuplicadas)){;
		// Registros que están duplicados.
		$array['RegistrosDuplicadas'] = $ResultadoDuplicadas->num_rows; 
	}else {
	// Quiere decir que hubo error en la consulta.
		$array['consulta'][3] =$consultaDuplicado;
		$array['error'][3] = $BDImportRecambios->error_list;
	}
	if ($array['RegistrosDuplicadas'] >0){
		// quiere decir hay registros duplicados.
		// Ahora recordamos que tenemos registros por orden Ref_Fabricantes y hay duplicados (Ref_Fabricante y IdFabricaCruzado).
		// por tenemos que obtener es el listado lineas que son duplicado para cambiar el estado.
		$i= 0;
		$linea= 0;
		$refFabricante ='';
		$idFabricanteCru = '';
		$arrayLineasDuplicado = array();
		while ($row_planets = $ResultadoDuplicadas->fetch_assoc()) {
			if ( $refFabricante !== $row_planets['Ref_Fabricante']){
			$refFabricante = $row_planets['Ref_Fabricante'];
			$idFabricanteCru = $row_planets['IdFabricaCruzado'];
			$linea = $row_planets['linea'];
			}
			if ($linea !== $row_planets['linea']){
				// Quiere decir es puede ser duplicado
				// Comprobamos que el mismo idFabricante
				if ($idFabricanteCru == $row_planets['IdFabricaCruzado']){
					// Es un duplicado
					$arrayLineasDuplicado[$i]= $row_planets['linea'];
					$i++; 
				}

			}
		}
		// Ahora solo falta ejecutar consulta para cambiar Estado : "Nuevo Duplicado"
		$consulta= ' WHERE linea in ('.implode(',',$arrayLineasDuplicado).')'; 
		$consulta2= "UPDATE  `referenciascruzadas` SET `Estado`= 'Nuevo Duplicado' ".	$consulta;
		$Anhadir = $BDImportRecambios->query($consulta2);
		$array['RegistrosDuplicados'] = $BDImportRecambios->affected_rows;
	}
	
	return $array;
}

function  ComprobarCruce($BDImportRecambios, $BDRecambios,$ConsultaImp,$arrayDistintosVacios,$Fabricante){
	// Objetivo:
	//  El objetivo es comprobar si existe el cruce de los NUEVOS CREADOS,DUPLICADO o EXISTENTES 
	//				1.- Si existe , entonces se cambia Estado = '[COMPROBADO] EXISTE CRUCE Y REFERENCIA PRINCIPAL'
	$array= array();
    $lineas = array();
	$valores = array();
	$wheres = array();
	$Nlinea = array();
	$fecha = date('Y-m-d');
	$consulta2 ='';
	// Contamos array entrega 
	$array['Ref_Principal_Entregadas'] = count($arrayDistintosVacios);
	// Campos que encontramos $arrayDistintosVacios
	// 'Linea'
	$i=0 ;
	foreach ($arrayDistintosVacios as $referencia) {
	$lineas[$i] = $referencia['linea'];
	$i++;
	}
	
	// El motivo de traer solo esos datos es evitar sobrecargar el ajax.
	$ObtenerDatos= ' WHERE linea in ('. implode(',',$lineas).')';
    $tabla ="referenciascruzadas";
	$campo = array('linea','Ref_Fabricante','IdFabricaCruzado','IDRefCruzada','RecambioID');
	$arrayNuevo = $ConsultaImp->registroLineas($BDImportRecambios,$tabla,$campo,$ObtenerDatos); 	
	$i = 0;
	// Ahora creamos la consulta 
	// Creamos arrays para hacer consultas.
	foreach ( $arrayNuevo as $referencia) {
		// El resultado aparte de los datos trae dos registros mas con dato NItem y ... por eso el if
		if (isset($referencia['IdFabricaCruzado'])){
			if ($referencia['IdFabricaCruzado'] >0 && $referencia['IDRefCruzada'] > 0 && $referencia['IDRefCruzada'] > 0){
				// Los campo que tenemos que busca son : `idReferenciaCruz` `idRecambio` `idFabricanteCruz`
				$wheres[$i] = '(idReferenciaCruz ='.$referencia['IDRefCruzada'].' and idRecambio ='.$referencia['RecambioID'].' and idFabricanteCruz ='.$referencia['IdFabricaCruzado'].')';
				$i++;
				
			}
		}
	}
	// Obtenemos registro si existe en cruce_referencias.
	$ConsultInsert = ' WHERE '.implode(' or ',$wheres);
	$tabla ="cruces_referencias";
	$campo = array('id','idReferenciaCruz','idRecambio','idFabricanteCruz');
	$ExisteCruces = $ConsultaImp->registroLineas($BDRecambios,$tabla,$campo,$ConsultInsert); 
	//~ $array['Encontrados'] = $ExisteCruces;
	$array['NumerEncontrados'] = $ExisteCruces['NItems'];

	if ($ExisteCruces['NItems'] >0){
		// Si obtenemos resultados entonces debemos cambiar el Estado en referenciascruzadas de BDImport
		$wheres = array(); // Reinicio crear una nueva
		$i= 0 ;
		foreach ($ExisteCruces  as $ExisteCruce){
			if (isset($ExisteCruce['idReferenciaCruz'])){
				// Lo hago para evirta adevertencia ya que el array contiene mas datos aparte de los campos
				// Montamos consulta para cambiar el estado.
				$wheres[$i] = '(IDRefCruzada ='.$ExisteCruce['idReferenciaCruz'].' and RecambioID ='.$ExisteCruce['idRecambio'].' and IdFabricaCruzado ='.$ExisteCruce['idFabricanteCruz'].')';
				$i++;
			}	
			
		} 
		$ConsultInsert = ' WHERE '.implode(' or ',$wheres);
		$ConsultInsert1 = ' WHERE '.implode(',',$wheres);

		$consulta2 = "UPDATE  `referenciascruzadas` SET `Estado`= concat('[COMPROBADO]',referenciascruzadas.Estado,' y cruce') ".$ConsultInsert;
		$array['NExisteCruce'] = 0; // Inicializamos para evitar error;
		$Anhadir = $BDImportRecambios->query($consulta2);
		$array['NExisteCruce']= $BDImportRecambios->affected_rows;
		
	}
	//~ $array['Consulta2'] = $consulta2;


	return $array;
}







function  AnhadirReferenciaCruce($BDImportRecambios, $BDRecambios,$ConsultaImp,$arrayDistintosVacios,$Fabricante){
	// Objetivo: 
	//		1.- Añadir Referencia cruzada.
	//		2.- Se se cambia Estado = '[ACTUALIZADO] NUEVA REFERENCIA' y añadir IDRefCruzada que acabamos crear.
	// 		
	$array= array();
    $lineas = array();
	$valores = array();
	$wheres = array();
	$Nlinea = array();

	$fecha = date('Y-m-d');
	// Contamos array entrega 
	$array['Ref_Principal_Entregadas'] = count($arrayDistintosVacios);
	// Campos que encontramos $arrayDistintosVacios
	// 'Linea'
	$i=0 ;
	foreach ($arrayDistintosVacios as $referencia) {
	$lineas[$i] = $referencia['linea'];
	$i++;
	}
	
	// El motivo de traer solo esos datos es evitar sobrecargar el ajax.
	$ObtenerDatos= ' WHERE linea in ('. implode(',',$lineas).')';
    $tabla ="referenciascruzadas";
	$campo = array('linea','Ref_Fabricante','IdFabricaCruzado');
	$arrayNuevo = $ConsultaImp->registroLineas($BDImportRecambios,$tabla,$campo,$ObtenerDatos); 	
	$i = 0;
	$x = 0;
	// Ahora creamos la consulta 
	// Creamos arrays para hacer consultas.
	foreach ( $arrayNuevo as $referencia) {
		// El resultado aparte de los datos trae dos registros mas con dato NItem y ... por eso el if
		if (isset($referencia['IdFabricaCruzado']) && isset($referencia['Ref_Fabricante'])){
			// Recuerda que RecambioID es 0 ya que son referencias cruzadas , no recambios que ponemos a la venta.
			$valores[$i] =  '(0,'.$referencia['IdFabricaCruzado'].',"'.$referencia['Ref_Fabricante'].'","'.$fecha.'")';
			$Nlinea[$i] = $referencia['linea'];
			$wheres[$i] = '(RefFabricanteCru ="'.$referencia['Ref_Fabricante'].'" and IdFabricanteCru ='.$referencia['IdFabricaCruzado'].')';
			$i++;
			
		}
	}
	$ConsultInsert = implode(',',$valores);
	
	// Insert Nuevas referencias cruzadas.
	$consulta1 = "INSERT INTO `referenciascruzadas`(`RecambioID`, `IDFabricanteCru`,`RefFabricanteCru`, `FechaActualiza`) VALUES ".$ConsultInsert;
	$Anhadir =$BDRecambios->query($consulta1);
	$array['NuevasReferenciaAnhadidos'] = $BDRecambios->affected_rows;

	// Cambiamos estado ... aquellos correctos
	$ObtenerDatos= ' WHERE linea in ('. implode(',',$Nlinea).')';
	$consulta2= "UPDATE  `referenciascruzadas` SET `Estado`= '[Añadido] Referencia Cruzada' ".	$ObtenerDatos;
    $Anhadir = $BDImportRecambios->query($consulta2);
    $array['CambioEstado'] = $BDRecambios->affected_rows;
	// Obtenemos IDRefCruzada para los que acabamos de crear y los duplicados de BDRECAMBIOS 
	$ObtenerDatos= ' WHERE '.(implode(' or ',$wheres));
	$tabla ="referenciascruzadas";
	$campo = array('id','RefFabricanteCru','IdFabricanteCru');
	$arrayIdNuevo = $ConsultaImp->registroLineas($BDRecambios,$tabla,$campo,$ObtenerDatos); 
	// Creamos arrays para hacer consultas para meter los ID.
	$i=0 ;
	$case= '';
	$wheres= array();
	foreach ( $arrayIdNuevo as $referencia) {
		// El resultado aparte de los datos trae dos registros mas con dato NItem y ... por eso el if
		if (isset($referencia['IdFabricanteCru']) && isset($referencia['RefFabricanteCru'])){
			$wheres[$i] = '( IdFabricaCruzado ='.$referencia['IdFabricanteCru'].' AND  Ref_Fabricante ="'.$referencia['RefFabricanteCru'].'")';
			$case = $case.' WHEN '.$wheres[$i].' THEN '.$referencia['id'];
			$i++;
			
		}
	}
	$whereEstado = implode(' OR ',$wheres);
	$consulta3 = 'UPDATE referenciascruzadas SET `IDRefCruzada` = CASE '.$case.' end WHERE '.$whereEstado;
	$Anhadir = $BDImportRecambios->query($consulta3);
    $array['AnhadidoID'] = $BDImportRecambios->affected_rows;
    
	//~ $array['Insert'] = $consulta1;
	//~ $array['UpDATE1'] = $consulta2;
	$array['Anhadidos']= count($Nlinea);
	//~ $array['IDNuevos']= $consulta3;
	//~ 
	return $array;
}

function  AnhadirCruce($BDImportRecambios, $BDRecambios,$ConsultaImp,$arrayDistintosVacios,$Fabricante){
	// Objetivo: 
	// 		1.Añadimos registros a cruce_referencias los Existian,Nuevo y duplicados
	// 		2.Cambiamos estado 
	// Creo que faltaría comprobar antes, a lo mejor en el proceso ComprobarCruce si esta duplicado al añadir...
	// porque quien nos dice que no nos metieron una referencias duplicadas.
	$array= array();
    $resultado = array();
	$consultas = array();
	$fecha = date('Y-m-d');
	$insert = array();
	$wheres = array();
	// Contamos array entrega 
	$array['Ref_Principal_Entregadas'] = count($arrayDistintosVacios);
	// Campos que encontramos $arrayDistintosVacios
	// 'linea', 'RecambioID','IdFabricaCruzado','IDRefCruzada'
	$i = 0;
	// Creamos arrays para hacer consultas.
	foreach ( $arrayDistintosVacios as $referencia) {
		// Creamos array con datos recibidos.
		$insert[$i] = '('.$referencia['IDRefCruzada'].','. $referencia['RecambioID'].','.$referencia['IdFabricaCruzado'].',"'.$fecha.'")';
		$wheres[$i] = $referencia['linea'];
		$i++;
	}
	// Ahora insertamos los registros nuevos.
	// Campos que necesitamos para cruces_referencias `idReferenciaCruz``idRecambio` `idFabricanteCruz` `FechaActualiza`
	$ConsultInsert = implode(',',$insert);
	$consulta1 = "INSERT INTO `cruces_referencias`(`idReferenciaCruz`, `idRecambio`, `idFabricanteCruz`, `FechaActualiza`) VALUES ".$ConsultInsert;
	$Anhadir =$BDRecambios->query($consulta1);
	$array['CrucesAnhadidos'] = $BDRecambios->affected_rows;
	
	
	
	// Ahora añadimos solo idReferenciaCurza en BDImportar y cambiamos estado.
	$consulWhere = 'linea in ('.implode(',',$wheres).')';
	
	$consulta2 = "UPDATE  `referenciascruzadas` SET `Estado`= concat('[Añadido Cruce]',referenciascruzadas.Estado)  WHERE ".$consulWhere;
    $Anhadir = $BDImportRecambios->query($consulta2);
	$array['EstadisCrucesAnhadidos']= $BDImportRecambios->affected_rows;
	
	$array['consulta1'] = $consulta1;
	$array['consulta2'] = $consulta2;
	
	return $array;
}
