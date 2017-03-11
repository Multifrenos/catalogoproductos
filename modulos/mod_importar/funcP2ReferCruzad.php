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
  * 	Esta funcion devuelve dos opciones según el paso que estemos ($condicional)
  * 			1.- PASO2-> Devuelve Referencias Principales distintas ( campo: RefProveedor )
  * 			2.- PASO3-> Devuelve RegistroLineas con los campos ( 'Ref_Fabricante','RecambioID','IdFabricaCruzado')
  * */
function obtenerReferenciasPrincipales($BDImportRecambios,$ConsultaImp,$condicional) {
    $array = array();
    $tabla ="referenciascruzadas";
	$whereC = " WHERE Estado = '' and IdFabricaCruzado <> 0";
	if ($condicional == "paso2") {
		$whereC = $whereC." and RecambioID = 0";
		$campo= 'RefProveedor';
		//Ejecutamos consulta.
		$array = $ConsultaImp->distintosCampo($BDImportRecambios,$tabla,$campo,$whereC);
	}
	if ($condicional == "paso3"){
		$campo = array('Ref_Fabricante','RecambioID','IdFabricaCruzado');
		$array = $ConsultaImp->registroLineas($BDImportRecambios,$tabla,$campo,$whereC); 	
	}
	
	// Ahora creamos un array con los distintos Recambios .
	$array['paso'] = $condicional;
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
    //~ foreach ( $arrayDistintosVacios as $referencia) {

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
		// Enviamos este dato por si hace falta.
		$datos['Iniciar'] = 'Paso2';	
	}
   	if ( $datos['NItemsEstadoBlanco'] > 0 ){
		// Enviamos este dato por si hace falta.
		$datos['Iniciar'] = 'Paso3';	
	}

    
    // Consultamos distintos fabricantes (total, no encontrados, no buscados)
	$campo = 'Fabr_Recambio';
   	$ArrayFabricantes[1] = "Estado = '[ERROR P2-22]:FABRICANTE cruzado no existe'"; //Fabricantes no encontrados.
   	$ArrayFabricantes[2] = "IdFabricaCruzado <>0"; 					// Fabricantes que ya fueron buscados.
   	$ArrayFabricantes[3] = "Estado = '' and IdFabricaCruzado =0";	// Fabricantes que NO fueron buscados.
   	$ArrayFabricantes[4] = "Estado = '[ERROR P2-21]:CampoVacio'"; 	// Fabricantes descartados por campo vacio.
   	$ArrayFabricantes[5] = ""; 										// Fabricantes que hay en la tabla.

   	$NErrores = count($ArrayFabricantes);
		for ($i = 1; $i <= $NErrores; $i++) {
			$whereC = '';
			if ($i <5){
			$whereC = " WHERE ".$ArrayFabricantes[$i];
			}
			$ArrayFabricante = $ConsultaImp->distintosCampo($BDImportRecambios,$nombretabla,$campo,$whereC);
			$ArrayFabricantes[$i]= $ArrayFabricante['NItems'];
		}   	
   
	// Contamos registros que tiene errores.
	$ArrayErrores[1]['estado'] = '[ERROR P2-22]:FABRICANTE cruzado no existe';
	$ArrayErrores[2]['estado'] = '[ERROR P2-21]:CampoVacio';
	$ArrayErrores[3]['estado'] = '[ERROR P2-23]:Referencia Principal no existe.';
		// Ahora realizamos bluce de consultas.
		$NErrores = count($ArrayErrores);
		for ($i = 1; $i <= $NErrores; $i++) {
			$whereC = ' WHERE Estado="'.$ArrayErrores[$i]['estado'].'"';
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
    
    
    // Cubrimos array que devolvemos.
    $datos['error22'] 	= $ArrayErrores[1]['Nitems'];
    $datos['error21'] 	= $ArrayErrores[2]['Nitems'];
    $datos['error23'] 	= $ArrayErrores[3]['Nitems'];

	$datos['FabNoEncontrado'] 	= $ArrayFabricantes[1];
	$datos['FabYaBuscado'] 		= $ArrayFabricantes[2];
	$datos['FabNoBuscado'] 		= $ArrayFabricantes[3];
	$datos['FabError21'] 		= $ArrayFabricantes[4];
	$datos['Totalfabcru'] 		= $ArrayFabricantes[5]; // Distintos fabricantes encontrados
  
    $datos['RefPrinYAIDRecam'] 	= $ArrayRefPrincipales[1];
    $datos['RefPrinEncontradas']= $ArrayRefPrincipales[2];
	$datos['RefPrinPendIDRecam']= $ArrayRefPrincipales[3];
	$datos['NRefPrinNOenc'] 	= $ArrayRefPrincipales[4];

	

	
    header("Content-Type: application/json;charset=utf-8");
    echo json_encode($datos);
}


function NuevoExiste($BDImportRecambios, $BDRecambios,$ConsultaImp,$arrayDistintosVacios,$Fabricante){
	$array= array();
    $inRefFabricante = array();
    $inIdFabricante = array();
    $UpDato = array();
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
		if ($resultados['NItems'] !=0) {
			foreach ( $resultados as $resultado ) {
			$idRefCruzada = 0;
				if ($Enviado['Ref_Fabricante'] == $resultado['RefFabricanteCru'] && $Enviado['IdFabricaCruzado'] == $resultado['IdFabricanteCru']){
					$idRefCruzada = $resultado['id'];
					break;
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
	//~ $consulta = array();
	foreach ( $ArrayEncontrados as $Encontrado) {
		$i++;
		if ($Encontrado['Buscado'] =='NoEncontrado') { 
			$Estado = 'Nuevo';
		} else {
			$Estado = 'Existe Referencia Cruzada';
		}
		$consulta[$i] = 'UPDATE referenciascruzadas SET Estado ="'.$Estado.'" WHERE RecambioID ='.$Encontrado['RecambioID'].' AND IdFabricaCruzado ='.$Encontrado['IdFabricaCruzado'].' AND  Ref_Fabricante ="'.$Encontrado['Ref_Fabricante'].'"';
	}
	$array['Consultas'] = $consulta;

	return $array;
}
