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
	    if ($Buscar == 'IDrecambio'){
			$andWhere = "RecambioID =0 ";
		} else {
			$andWhere = "IdVersion =0 ";
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
	
	
	
	function CochesResumen($BDImportRecambios,$ConsultaImp) {
		// En esta función lo que vamos a realizar es el resumen de tabla referenciascversiones
		// y obtenemos los datos para mostralos.
		$array = array();
		$andWheres = array('RecambioID','IdVersion');
		// obtenemos datos de referenciasCversiones las RefProveedor distintos que estado = blanco y RecambioID sea 0
	    $tabla ="referenciascversiones";
		$i = 0;
		foreach ($andWheres as $andWhere) {
	   		$whereC = " WHERE Estado = '' and ".$andWhere.'= 0';
			$campo = 'RefProveedor';
			$resultados =$ConsultaImp->distintosCampo($BDImportRecambios,$tabla,$campo,$whereC);
			$array[$i]['TotalReferenciasDistintas'] = $resultados['NItems'];
			//~ $array[$i]['consulta'] = $whereC;
			$i++;
		}
		// Ahora volvemos hacer lo mismo pero comprobando aquellos que tiene registros.. esto solo debe suceder cuando ya ejecutamos AJAX
		$i = 0;
		foreach ($andWheres as $andWhere) {
	   		$whereC = " WHERE Estado = '' and ".$andWhere.'> 0';
			$campo = 'RefProveedor';
			$resultados =$ConsultaImp->distintosCampo($BDImportRecambios,$tabla,$campo,$whereC);
			$array[$i]['RefDistintasConID'] = $resultados['NItems'];
			$array[$i]['consulta'] = $whereC;
			$i++;
		}
		// Ahora obtenemos la NItems distintos que tiene error en campo.
		// El error de:
		// Estado ='[ERROR P2-23]:Referencia Principal no existe.
			$whereC = " WHERE Estado = '[ERROR P2-23]:Referencia Principal no existe.'";
			$campo = 'RefProveedor';
			$resultados =$ConsultaImp->distintosCampo($BDImportRecambios,$tabla,$campo,$whereC);
			$array['RefDistintasError'] = $resultados['NItems'];
		
		return $array;
	}
	
	
	
	function CochesIDVersiones($BDVehiculos,$BDImportRecambios,$ConsultaImp) {
		// Buscamos los IDVersiones de la tabla 
		
		
		// Consultamos tabla recambiosVersiones y obtenemos los datos 
		$array = array();
		$campos = array('VersionAcabado','kw','cv','Cm3','Ncilindros','TipoCombustible');
		$nombretabla= "referenciascversiones";
		$whereC = " WHERE Estado = '' and ( RecambioID >0 and IdVersion=0) limit 100";
		//~ $resultado = $ConsultaImp->registroLineas($BDImportRecambios,$nombretabla,$campo,$whereC);
		
		$CampoDistinct = implode(",", $campos);
		$QueryDis = 'SELECT distinct(concat('.$CampoDistinct.")) as concatenado,MarcaDescrip,ModeloVersion  FROM `referenciascversiones` WHERE Estado = '' and ( RecambioID >0 and IdVersion=0) limit 10";
		//~ $QueryDis = "SELECT * FROM ".$nombretabla." WHERE Estado = '' and RecambioID >0  limit 10";

		$resultado = $BDImportRecambios->query($QueryDis);
		// Ahora obtenemos los datos de 100
		$array['NItems'] = $resultado->num_rows;

		if ($array['NItems']>0){
			$i=0;
			while ($row_planets = $resultado->fetch_assoc()) {
				$array[$i]['concatenado'] = $row_planets['concatenado'];
				$array[$i]['marca'] = $row_planets['MarcaDescrip'];
				$array[$i]['modelo'] = $row_planets['ModeloVersion'];
				$BuscarMarca = "Select id FROM vehiculo_marcas where nombre='".$array[$i]['marca']."'";
				$idMarca = $BDVehiculos->query($BuscarMarca);
				if ($idMarca->num_rows ==1) {
					while ($row = $idMarca->fetch_assoc()){
					$array[$i]['IDmarca'] = $row['id'];
					}
				} else {
					// Error en marca, se encontro mas de una...
				}
				$BuscarModelo = "Select id FROM vehiculo_modelos where nombre='".$array[$i]['modelo']."'";
				$idModelo = $BDVehiculos->query($BuscarModelo);
				if ($idModelo->num_rows ==1) {
					while ($row = $idModelo->fetch_assoc()){
					$array[$i]['IDmodelo'] =$row['id'] ;
					}
				} else {
					// Error en marca, se encontro mas de una...
				}
				$i++;
			}
		}
		// Ahora tenmos que buscar modelo ...
		//~ $nombretabla1= "vehiculo_modelos";
		//~ $nombretabla2= "vehiculo_marcas";
//~ 
		//~ $i= 0;
		//~ 
		//~ 
		//~ 
		//~ $QueryDis = "SELECT ".$nombretabla1.".id,".$nombretabla1.".nombre,".$nombretabla2.".nombre FROM ".$nombretabla1.",".$nombretabla2." WHERE ".$nombretabla1.".nombre = '".$array[$i]['modelo']."'";
		//~ $resultado = $BDVehiculos->query($QueryDis);
		//~ $array['NItems'] = $resultado->num_rows;
		//~ if ($array['NItems']>0){
			//~ $i=0;
			//~ while ($row_planets = $resultado->fetch_assoc()) {
				//~ 
				//~ $i++;
			//~ }
		//~ }
		//~ $array['consulta'] =$QueryDis;

		return $array;
	
	}
?>
