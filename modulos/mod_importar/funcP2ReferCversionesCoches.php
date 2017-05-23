<?php 
/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogo productos Soluciones Vigo.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero && Alberto Lago
 * @Descripcion	Funciones en php para realizar las tareas de Paso2ReferenciasCVersion.
 *  */

	function CochesCrearTablas($BDImportRecambios,$ConsultaImp) {
		$tablas= array('marcas','combustibles','modelos','versiones');
		$consultas = array();
		$array = array();
		$creartabla = array();
		// Consulta para crear la tabla de marcas, debería comprobar si existe antes.
		$consultas[0]= 'CREATE TABLE '.$tablas[0].' ( `id` INT NOT NULL AUTO_INCREMENT, `descripcion` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB';
		// Consulta para crear la tabla de combustible, debería comprobar si existe antes.
		$consultas[1]= 'CREATE TABLE '.$tablas[1].' (   `id` int(11) NOT NULL AUTO_INCREMENT,   `descripcion` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci';
		// Consulta para crear la tabla de modelos, debería comprobar si existe antes.
		$consultas[2]= 'CREATE TABLE '.$tablas[2].' (`id` int(11) NOT NULL AUTO_INCREMENT,`MarcaDescrip` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,`id_marca` int(11) NOT NULL,`descripcion` varchar(100) COLLATE utf8_spanish_ci NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci';

		// Consulta para crear la tabla de versiones, debería comprobar si existe antes.
		$consultas[3]= 'CREATE TABLE '.$tablas[3].' (`id` int(11) NOT NULL AUTO_INCREMENT,`id_modelo` int(11) NOT NULL,
  `ModeloVersion` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,`descripcion` varchar(100) COLLATE utf8_spanish_ci NOT NULL,`kw` int(2) NOT NULL,`cv` int(3) NOT NULL,`Cm3` int(4) NOT NULL,`Ncilindros` int(2) NOT NULL,
  `FechaInici` date NULL,`FechaFinal` date NULL,`id_combustible` int(11) NOT NULL,`TipoCombustible` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,`estado` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `uno` int(11) NOT NULL,`dos` int(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci';
		
		for ($i = 0; $i <= 3; $i++) {
			$creartabla[$i] = $BDImportRecambios->query($consultas[$i]);
			$array['creartabla'][$i] = $creartabla[$i];
			$array['consulta'][$i] = $consultas[$i];

		}
		return $array;
	
	
	}
	
	
	function CochesInsertTemporal ($BDImportRecambios,$ConsultaImp) {
		$tablas= array('marcas','combustibles','modelos','versiones');
		$consultas = array();
		$array = array();
		$insert = array();
		
		$consultas[0]='INSERT INTO '.$tablas[0].' (`descripcion`) SELECT DISTINCT `MarcaDescrip` FROM `referenciascversiones`;';
		
		$consultas[1]='INSERT INTO '.$tablas[1].' (`descripcion`) SELECT DISTINCT `TipoCombustible` FROM `referenciascversiones` WHERE TipoCombustible IS NOT NULL;';

		$consultas[2]='INSERT INTO '.$tablas[2].' (MarcaDescrip, id_marca, descripcion) SELECT DISTINCT MarcaDescrip, 0, ModeloVersion FROM `referenciascversiones`;';
		
		$consultas[3]='INSERT INTO '.$tablas[3].' (id_modelo, ModeloVersion, descripcion,  `kw`,  `cv`,  `Cm3`,  `Ncilindros`,  `FechaInici`,  `FechaFinal`, id_combustible,  `TipoCombustible`)  SELECT DISTINCT 0, ModeloVersion,VersionAcabado,   `kw`,  `cv`,  `Cm3`,  `Ncilindros`,  `FechaInici`,  `FechaFinal`, 0,  `TipoCombustible` FROM `referenciascversiones`;';
		
		for ($i = 0; $i <= 3; $i++) {
			$insert[$i] = $BDImportRecambios->query($consultas[$i]);
			$array['insert'][$i] = $insert[$i];
		}
		return $array;
		
		
		
		
	}
	
	function CochesUpdateTemporal ($BDImportRecambios,$ConsultaImp) {
		$tablas= array('marcas','combustibles','modelos','versiones');
		$consultas = array();
		$array = array();
		$update = array();
		
		$consultas[0]='UPDATE modelos, marcas SET modelos.id_marca=marcas.id WHERE modelos.MarcaDescrip=marcas.descripcion;';
		$consultas[1] = 'UPDATE versiones, modelos SET versiones.id_modelo=modelos.id WHERE versiones.ModeloVersion=modelos.descripcion';
		$consultas[2] = 'UPDATE versiones, combustibles SET versiones.id_combustible=combustibles.id WHERE versiones.TipoCombustible=combustibles.descripcion';
		for ($i = 0; $i <= 2; $i++) {
			$update[$i] = $BDImportRecambios->query($consultas[$i]);
			$array['update'][$i] = $update[$i];
		}
		return $array;
	
		
	
	return $array;
	}
	
	
	function CochesObtenerRegistros($BDImportRecambios,$ConsultaImp,$Buscar) {
		// Solo obtenemos el numero total de Referencias distintas que vamos a gestionar.
		// por lo que no hay riesgo de exceso de memoria.
		$array = array();
		$wheres = array();
		// obtenemos datos de referenciasCversiones las RefProveedor distintos que estado = blanco y RecambioID sea 0
	    $tabla ="referenciascversiones";
	    switch ($Buscar) {
			case 'IDrecambio':
				$andWhere = "RecambioID =0 ";
				break;
			
			case 'IDversion':
				$andWhere = "IdVersion =0 ";
				break;
			
			case 'NuevoExiste':
				$andWhere = "(`RecambioID`>0 and `IdVersion`>0)";
				break;
	    }
	    
	    
		$whereC = " WHERE Estado = '' and ".$andWhere;
		$campo = 'RefProveedor';
		$resultados =$ConsultaImp->distintosCampo($BDImportRecambios,$tabla,$campo,$whereC);
		$array['TotalReferenciasDistintas'] = $resultados['NItems'];
		$array['consulta'] = $whereC;

		return $array;
		
	
	}
	
	
	
	
	
	
	
	function CochesIDRecambioTemporal ( $BDRecambios,$BDImportRecambios,$ConsultaImp,$Fabricante) {
		$array = array();
		$wheres = array();
		// obtenemos datos de referenciasCversiones las RefProveedor distintos que estado = blanco y RecambioID sea 0
	    $tabla ="referenciascversiones";
		$whereC = " WHERE Estado = '' and RecambioID =0 limit 200";
		$campo = 'RefProveedor';
		$resultados =$ConsultaImp->distintosCampo($BDImportRecambios,$tabla,$campo,$whereC);
		if ($resultados['NItems']>0){
			// Ahora tenemos que buscar el resultado en BDRecambios para obtener ID
			// Hay que contar con que puede que no existan todas las referencias.
			$f= $Fabricante;
			$RefNoencontradas = array();
			$ReferenciaEncontrada = array();
			$ErrorRefPrincipal = 0;
			$i = 0;
			foreach ( $resultados as $resultado) {
				// Recuerda que el resultado no tiene un array directo ya tien NItems y alguna cosa mas por eso debemos hacer condicional
				if (isset($resultado['RefProveedor'])){
					// Inicializamos varibles
					$ref = '';
					$idRecambio = 0 ;
					// 	1.- Comprobamos si existe la referencia principal.
					// 		Buscamos en BDRecambios en tabla referencias cruzadas si existe la referencia principal.
					$ref= $resultado['RefProveedor'];
					$consul = "SELECT * FROM `referenciascruzadas` WHERE RefFabricanteCru = '" . $ref . "' and IdFabricanteCru = '" . $f . "'";
					$ejconRefFab = mysqli_query($BDRecambios, $consul);
					$resul = mysqli_fetch_assoc($ejconRefFab);
					$idRecambio = $resul['RecambioID']; // Id de recambio que vamos a cruzar
					if ($idRecambio == 0 ) {
					// Esto es que no encontro la rreferencia principal
						$RefNoencontradas[$i] ='"'.$ref.'"';
					} else {
					//~ $array[$i] = var_export ($ref);
					$ReferenciaEncontrada[$i]["id"] = $idRecambio; 
					$ReferenciaEncontrada[$i]['Referencia'] = $ref; 
					}	
					$i++;
				}
				
			}
			
			$array['Ref_Principal_Entregadas'] = $i;
			// Ahora cambiamos el estado de todos las referencias que no se encontraron.
			// En BDImportar/ReferenciCruzadas en campo Estado
			// Estado ='[ERROR P2-23]:Referencia Principal no existe.'
			$ReferenciasError = 'RefProveedor='.implode(' OR RefProveedor=',$RefNoencontradas);
			$consul = "UPDATE ".$tabla." SET `Estado`='[ERROR P2-23]:Referencia Principal no existe.' WHERE ".$ReferenciasError;
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
			$consul = "UPDATE ".$tabla." SET `RecambioID` = CASE `RefProveedor` ".$array['Consulta1']." END WHERE RefProveedor IN (".$array['Consulta2'].")";
			$Anhadir = $BDImportRecambios->query($consul);
			$AnhadirIDRecambio= $BDImportRecambios->affected_rows;
			$array['RegistroAnhadirIDRecambio'] = 	$AnhadirIDRecambio;
					
			
			
		}
		
		$array['TotalReferenciasDistintas'] = $resultados['NItems'];
		//~ $array['NoEncontrados'] =$RefNoencontradas;
		return $array;
	
		
	
	}
	
	
	
	function CochesResumen($BDImportRecambios,$ConsultaImp,$Paso) {
		// En esta función lo que vamos a realizar es el resumen de tabla referenciascversiones
		// y obtenemos los datos para mostralos.
		// Recibimos parametro $Paso para poder descartar algunas consultas, según el paso que estemos.
		$array = array();
		$tabla ="referenciascversiones";
	    // 1.- Contamos registros
			$whereC= ' ';
			$resultado = $ConsultaImp->contarRegistro($BDImportRecambios,$tabla,$whereC);
			$array['TotalRegistro'] = $resultado;
		// 2.- Contamos registros que tengan Estado cubiertos o tenga IDś cubiertos ( Recambio y Versiones)
		// Si el resultado de esto es 0 , quiere decir que no se busco IDś recambios y IDVersiones.
			$whereC= "  WHERE `Estado`<>'' or (`RecambioID`>0 and `IdVersion`>0)";
			$resultado = $ConsultaImp->contarRegistro($BDImportRecambios,$tabla,$whereC);
			$array['RegistroVistos'] = $resultado;
		// 3.- Contamos registros que tengan Estado Blanco y tenga IDś cubiertos ( Recambio y Versiones)
		// Si el resultado de esto es 0 , que ya existen tienen estado todos los registros.
		// con lo que si tiene IDś quiere decir que su estado es:
		// Nuevo,Existe,Duplicado.
			$whereC= "  WHERE `Estado`=''  and (`RecambioID`>0 and `IdVersion`>0)";
			$resultado = $ConsultaImp->contarRegistro($BDImportRecambios,$tabla,$whereC);
			$array['RegistroCIDs'] = $resultado;

			
	    // 4.- Contamos registros cuantas RefProveedor distintas con Estado Blanco y IDRecambio =0  o IDversiones = 0 
	    $andWheres = array('RecambioID','IdVersion');
		$i = 0;
		foreach ($andWheres as $andWhere) {
	   		$whereC = " WHERE Estado = '' and ".$andWhere.'= 0';
			$campo = 'RefProveedor';
			$resultados =$ConsultaImp->distintosCampo($BDImportRecambios,$tabla,$campo,$whereC);
			$array[$i]['TotalReferenciasDistintas'] = $resultados['NItems'];
			$i++;
		}
		// 5.- Ahora obtenemos cuantas RefProveedor distintas con Estado Blanco y IDRecambio >0  o IDversiones > 0 
		$i = 0;
		foreach ($andWheres as $andWhere) {
	   		$whereC = " WHERE Estado = '' and ".$andWhere.'> 0';
			$campo = 'RefProveedor';
			$resultados =$ConsultaImp->distintosCampo($BDImportRecambios,$tabla,$campo,$whereC);
			$array[$i]['RefDistintasConID'] = $resultados['NItems'];
			$array[$i]['consulta'] = $whereC;
			$i++;
		}
		// 6.- Obtenemos cuantos cruces duplicados hay.
		// Esto no se hace si el no están ya buscados todos los IDś de Recambios y Versiones, por lo que :
		if ( $array[0]['TotalReferenciasDistintas'] === 0 and $array[1]['TotalReferenciasDistintas'] === 0  ) {
			
			
			$QueryGroup = "SELECT concat( `RecambioID` , ':', `IdVersion` ) AS concatenado, `linea` , `RefProveedor` , `RecambioID` , `IdVersion` , `Estado` , count( `Estado` ) AS c
			FROM `referenciascversiones`
			WHERE Estado=''
			AND IdVersion !=0
			GROUP BY concatenado
			HAVING c !=1
			ORDER BY c DESC ";
			$resultado = $BDImportRecambios->query($QueryGroup);
			$array['EstadoFinal']['Duplicado'] = $resultado->num_rows;;
			
			// Ahora contamos los Duplicados que ya tenemos marcados en estado.
			$whereC= "  WHERE `Estado`='Duplicado'";
			$resultado = $ConsultaImp->contarRegistro($BDImportRecambios,$tabla,$whereC);
			$array['EstadoFinal']['RegDuplicadoDescartados'] = $resultado;

		
		
		
		
		}
		
		
		// Ahora obtenemos la NItems distintos tipo errores o advertencias.
		// Estado ='[ERROR P2-23]:Referencia Principal no existe.
		// Estado = 'Error Version';
		// Estado = 'Error Marca o Modelo';
			$arrayErrores = array();
				$arrayErrores[] = '[ERROR P2-23]:Referencia Principal no existe.';
				$arrayErrores[] = 'Error Version';
				$arrayErrores[] = 'Error Marca o Modelo';
				
			$campo = 'RefProveedor';
			$i=0;
			foreach ($arrayErrores as $arrayError) {
			$whereC = ' WHERE Estado="'.$arrayError.'"';
			$resultados =$ConsultaImp->distintosCampo($BDImportRecambios,$tabla,$campo,$whereC);
			$array['Errores'][$i] = $resultados['NItems'];
			$i++;
			}
					
		
		// Ahora contamos las distintas versiones que existe.
			$campos = array('MarcaDescrip','ModeloVersion','VersionAcabado','kw','cv','Cm3','Ncilindros','TipoCombustible');
			$nombretabla= "referenciascversiones";
			$CampoDistinct = implode(",", $campos);
			// Realizamos un select con concatenado campos para buscar las versiones distintas que hay.
			$QueryDis = 'SELECT distinct(concat('.$CampoDistinct.")) as concatenado".$CampoDistinct."  FROM `referenciascversiones` WHERE Estado = '' and IdVersion=0";
			
			$resultado = $BDImportRecambios->query($QueryDis);
			$array['NVersionesDifSIDversion'] = $resultado->num_rows;
			
		// Ahora contamos las distintas versiones que existen pero con IDVersiones.
			$QueryDis = 'SELECT distinct(concat('.$CampoDistinct.")) as concatenado".$CampoDistinct."  FROM `referenciascversiones` WHERE Estado = '' and IdVersion<>0";
			
			$resultado = $BDImportRecambios->query($QueryDis);
			$array['NVersionesDifCIDversion'] = $resultado->num_rows;
		
		
		
		
		return $array;
	}
	
	
	
	function CochesIDVersiones($BDVehiculos,$BDImportRecambios,$ConsultaImp) {
		// Reinicio de variables
		$Resumen = array(); // Metemos los datos generales que vamos a necesitar
		$array = array(); // Metemos los dastos de las distinta versiones
		$combustibles = array();
	    $tabla ="referenciascversiones";

		// Ahora contamos con estado cubierto o ID cubierto:
			$whereC= "  WHERE `Estado`<>' ' or (`RecambioID`>0 and `IdVersion`>0)";
			$resultado = $ConsultaImp->contarRegistro($BDImportRecambios,$tabla,$whereC);
			$Resumen['RegistroVistos'] = $resultado;
		// Antes de hacer nada creamos array de combustible
		$nombretabla = 'vehiculo_combustibles';
		$BuscarTipoCombustible = "Select * FROM ".$nombretabla;
		$Bcombustible = $BDVehiculos->query($BuscarTipoCombustible);
		
		while ($row = $Bcombustible->fetch_assoc()){
			$OtrasDecripciones = array();
			// Añadimos si hay mas descripciones , sino no.
			if (strlen($row['OtrasDescripciones']) > 0 ) {
				$OtrasDecripciones = explode(",",$row['OtrasDescripciones']);
			}
			// Añadimos descripcion principal si no existe en array.
			
			$Expresion = "/".$row['nombre']."/i";
			if (!preg_grep($Expresion,$OtrasDecripciones)){
				// Si no existe entonces añadimos el principal.
				$OtrasDecripciones[] =  $row['nombre'];
			}
			$combustibles[$row['id']] = $OtrasDecripciones;
		}
	
		// Ahora empezamos para buscar IDVersiones

		// Consultamos tabla recambiosVersiones y obtenemos los datos 
		
		$campos = array('VersionAcabado','kw','cv','Cm3','Ncilindros','TipoCombustible');
		$nombretabla= "referenciascversiones";
		$CampoDistinct = implode(",", $campos);
		// Realizamos un select con concatenado campos para buscar las versiones distintas que hay.
		$QueryDis = 'SELECT distinct(concat('.$CampoDistinct.")) as concatenado,MarcaDescrip,ModeloVersion,FechaInici,FechaFinal,RecambioID,".$CampoDistinct."  FROM `referenciascversiones` WHERE Estado = '' and ( RecambioID >0 and IdVersion=0) limit 350";
		//~ $Resumen['consulta'] = $QueryDis;

		$resultado = $BDImportRecambios->query($QueryDis);
		// Ahora obtenemos los datos de 100
		$Resumen['NItems'] = $resultado->num_rows;

		if ($Resumen['NItems']>0){
			$i=0;
			while ($row_planets = $resultado->fetch_assoc()) {
				$array[$i]['concatenado1'] = $row_planets['concatenado'];
				$array[$i]['marca'] = $row_planets['MarcaDescrip'];
				$array[$i]['modelo'] = $row_planets['ModeloVersion'];
				$array[$i]['TipoCombustible'] = $row_planets['TipoCombustible'];
				$array[$i]['VersionAcabado'] = $row_planets['VersionAcabado'];
				$array[$i]['kw'] = $row_planets['kw'];
				$array[$i]['cv'] = $row_planets['cv'];
				$array[$i]['Cm3'] = $row_planets['Cm3'];
				$array[$i]['Ncilindros'] = $row_planets['Ncilindros'];
				$array[$i]['FechaInici'] = $row_planets['FechaInici'];
				$array[$i]['FechaFinal'] = $row_planets['FechaFinal'];

				// Evitar que genere error.
				if ( isset ($row_planets['Estado'])){
				$array[$i]['Estado'] = $row_planets['Estado'];
				} else {
					$array[$i]['Estado'] = '';
				}
				$array[$i]['RecambioID'] = $row_planets['RecambioID'];

				// Ahora Buscamos ID Marca
				$nombretabla = 'vehiculo_marcas';
				$BuscarMarca = "Select id FROM ".$nombretabla." where nombre='".$array[$i]['marca']."'";
				$idMarca = $BDVehiculos->query($BuscarMarca);
				if ($idMarca->num_rows ==1) {
					while ($row = $idMarca->fetch_assoc()){
					$array[$i]['IDmarca'] = $row['id'];
					
					}
				} else {
					// Error en marca, se encontro mas de una...
					// Deberiamos marcar error:
					$array[$i]['IDmarca'] ='Error' ;
				}
				// Ahora Buscamos ID Modelo
				$nombretabla = 'vehiculo_modelos';
				$BuscarModelo = 'Select id FROM '.$nombretabla.' where nombre="'.$array[$i]['modelo'].'"';
				$idModelo = $BDVehiculos->query($BuscarModelo);
				if ($idModelo->num_rows ==1) {
					while ($row = $idModelo->fetch_assoc()){
					$array[$i]['IDmodelo'] =$row['id'] ;
					}
				} else {
					// Error en modelo, se encontro mas de una...
					// Deberiamos marcar error:
					$array[$i]['IDmodelo'] ='Error' ;

				}
				// Ahora Buscamos el ID Tipo combustible 
				$IDCombustibles = array_keys($combustibles);
				foreach ($IDCombustibles as $ID){
					foreach ($combustibles[$ID] as $combustible){
						
						if ($array[$i]['TipoCombustible'] == $combustible){
							$array[$i]['IDCombustible'] = $ID;
						}
					}
				}
				
				
				$array[$i]['concatenado2'] = $array[$i]['IDmarca'].$array[$i]['IDmodelo'].$row_planets['concatenado'];
				// Este es where que vamos utilizar para buscar en versiones de BD vehiculos
				$array[$i]['BusquedaIDVersion'] = "where `idMarca`=".
												$array[$i]['IDmarca']." and idModelo=".
												$array[$i]['IDmodelo']." and nombre ='".
												$array[$i]['VersionAcabado']. "' and kw=".
												$array[$i]['kw']. " and cv=".
												$array[$i]['cv']. " and Cm3=".
												$array[$i]['Cm3']. " and Ncilindros=".
												$array[$i]['Ncilindros'];
				// Este es where que vamos utilizar para buscar nuevamente en referenciasCversiones en BDImport
				$array[$i]['BusquedaRefCversion'] = 'where `MarcaDescrip`="'.
												$array[$i]['marca'].'" and ModeloVersion="'.
												$array[$i]['modelo'].'" and VersionAcabado ="'.
												$array[$i]['VersionAcabado']. '" and kw='.
												$array[$i]['kw']. ' and cv='.
												$array[$i]['cv']. ' and Cm3='.
												$array[$i]['Cm3']. ' and Ncilindros='.
												$array[$i]['Ncilindros']. ' and FechaInici="'.
												$array[$i]['FechaInici']. '" and FechaFinal="'.
												$array[$i]['FechaFinal']. '" and TipoCombustible="'.
												$array[$i]['TipoCombustible'].'"';
																	
				// El siguiente montaje arrays es para utilizar un udpate unico, pero de momento no lo utilizo.
				$array[$i]['UPDateunico'] = '(`MarcaDescrip`="'.
												$array[$i]['marca'].'" and ModeloVersion="'.
												$array[$i]['modelo'].'" and VersionAcabado ="'.
												$array[$i]['VersionAcabado']. '" and kw='.
												$array[$i]['kw']. ' and cv='.
												$array[$i]['cv']. ' and Cm3='.
												$array[$i]['Cm3']. ' and Ncilindros='.
												$array[$i]['Ncilindros']. ' and FechaInici="'.
												$array[$i]['FechaInici']. '" and FechaFinal="'.
												$array[$i]['FechaFinal']. '" and TipoCombustible="'.
												$array[$i]['TipoCombustible'].'")';

				$array[$i]['concatenado1'] = $array[$i]['marca'].$array[$i]['modelo'].$row_planets['concatenado'];

				$i++;
			}
		}
		// Ahora tenemos una array con datos IDMarca y IDModelo para buscar la version.
		// Realizamo consulta en BDvehiculos-> Tabla versiones para saber si existe esa version de coche.
		$nombretabla= "vehiculo_versiones";
		for ( $i=0; $i< $Resumen['NItems']; $i++) {
			// Ahora los que tiene tiene Marca y Modelo buscamos IDVersion
			if ($array[$i]['IDmodelo'] <> 'Error' and $array[$i]['IDmarca']<> 'Error') {
				$BuscarIDversion = "Select id FROM ".$nombretabla." ".$array[$i]['BusquedaIDVersion'];
				$idVersiones = $BDVehiculos->query($BuscarIDversion);
					$array[$i]['NumeroRowversiones']=$idVersiones->num_rows;
					if ($idVersiones->num_rows ==1) {
						while ($row = $idVersiones->fetch_assoc()){
						$array[$i]['IDversion'] =$row['id'] ;
						}
					} else {
						// Error en versiones, se encontro mas de una...
						// Deberiamos marcar error:
						$array[$i]['IDversion'] ='Error Version' ;
					}
					
			} else {
				// Quiere decir que tiene un error en marca o modelo
				$array[$i]['IDversion'] ='Error Marca o Modelo' ;

			}
		}
		// Ahora ya tenemos el id de los distintos vehiculos obtenido, ahora tenemos grabar dato id version en BDimport
		$nombretabla= "referenciascversiones";
		$Resumen['TotalRegistrosIDRecambios'] = 0 ;
		$Resumen['TotalRegistrosConError']= 0;
		// Este array de momento no lo utilizo es para montar un update unico.
		$arrayWhere = array() ; 
		for ( $i=0; $i< $Resumen['NItems']; $i++) {
			$whereC = $array[$i]['BusquedaRefCversion'];
			
			if ($array[$i]['IDversion']=== 'Error Version' or $array[$i]['IDversion'] ==='Error Marca o Modelo'){
				// Quiere decir que tiene un error en version, marca o modelo.
				// Si el IDversiones tiene como valor "Error" en vez de numero , lo que hacemos es cambiar el estado a los registros
				//~ $QueryDis = 'UPDATE `referenciascversiones` SET `Estado`=concat(Estado," -'.$array[$i]['IDversion'].'") '.$whereC;
				$arrayWhere['error'][] ='when '.$array[$i]['UPDateunico'].' then "'.$array[$i]['IDversion'].'"';
			} else {
				// Quiere decir que tiene IDVersion
				//~ $QueryDis = 'UPDATE `referenciascversiones` SET `IdVersion`='.$array[$i]['IDversion'].' '.$whereC;
				$arrayWhere['insert'][] = 'when '.$array[$i]['UPDateunico'].' then '.$array[$i]['IDversion'];

			}
		}
		// Creamos variable con la que vamos hacer UPDATE unico, donde añadirmo IDVersion o Error a la tabla referenciaCversiones
		if (isset($arrayWhere['error'])){
			$QueryDis='UPDATE '.$nombretabla.' SET Estado=  case '.implode(' ', $arrayWhere['error']).' else Estado end where Estado=""';
			$resultado = $BDImportRecambios->query($QueryDis);
			$Resumen['TotalRegistrosConError'] = $BDImportRecambios->affected_rows;
			//~ $Resumen['UPDATEUnicoError'] =$QueryDis;
		}
		if (isset($arrayWhere['insert'])){
			$QueryDis='UPDATE '.$nombretabla.' SET IdVersion=  case '.implode(' ', $arrayWhere['insert']).' else IdVersion end';
			$resultado = $BDImportRecambios->query($QueryDis);
			$Resumen['TotalRegistrosIDRecambios'] = $BDImportRecambios->affected_rows;
			//~ $Resumen['UPDATEUnicoInsert'] =$QueryDis;
		}
		//~ $Resumen['Array'] = $array; // Lo añado para ver que sucede, solo es un control debug
		
		return $Resumen;
	
	}
	
	
	function CochesNuevaExiste($BDVehiculos,$BDImportRecambios,$ConsultaImp) {
		$resumen = array();
		// Obtenemos los registros que el Estado está blanco y tiene IDs( IDRecmabio y IDversiones)
		$tabla = "referenciascversiones";
		$whereC= "  WHERE `Estado`=''  and (`RecambioID`>0 and `IdVersion`>0)";
		$resultado = $ConsultaImp->contarRegistro($BDImportRecambios,$tabla,$whereC);
		$resumen['RegistroCIDs'] = $resultado;
		if ($resumen['RegistroCIDs']>0) {
			// Quiere decir que hay registros que el Estado está en blanco y tiene IDRecambios y IDVersiones.
			// Ahora comprobamos si hay duplicados sin procesar.
			$QueryGroup = "SELECT concat( `RecambioID` , ':', `IdVersion` ) AS concatenado, `linea` , `RefProveedor` , `RecambioID` , `IdVersion` , `Estado` , count( `Estado` ) AS c
			FROM `referenciascversiones`
			WHERE Estado=''
			AND IdVersion !=0
			GROUP BY concatenado
			HAVING c !=1
			ORDER BY c DESC ";
			$resultado = $BDImportRecambios->query($QueryGroup);
			$resumen['EstadoFinal']['Duplicado'] = $resultado->num_rows;;
		}
		// Si tenemos pendientes entonces ejecutamos UPDAte de cambiar estado de los duplicados menos en uno.
		if ($resumen['EstadoFinal']['Duplicado']>0){
			// Ahora cubrimos los duplicados de 100 primeros Distintos duplicados encontrados.
			$QueryGroup = "UPDATE (
			SELECT linea, `RecambioID` , `IdVersion` , `Estado` , concat( `RecambioID` , ':', `IdVersion` ) AS con, count( `IdVersion` ) AS c
			FROM referenciascversiones
			WHERE Estado = ''
			AND IdVersion !=0
			AND Estado != 'Duplicado'
			GROUP BY con
			HAVING c >1
			LIMIT 100
			) AS nueva, referenciascversiones AS ref
			SET ref.Estado = if(ref.linea = nueva.linea,'','Duplicado') WHERE nueva.RecambioID = ref.RecambioID AND nueva.IdVersion = ref.IdVersion";
			$resultado = $BDImportRecambios->query($QueryGroup);

			$resumen['EstadoFinal']['RegDuplicadoCambiados']= $BDImportRecambios->affected_rows;
			
		
		
		return $resumen ; // Ya que no podemos continuar hasta terminar los duplicados.
		}
		
		
		
		
		
		return $resumen ;
		
	}
	
?>
