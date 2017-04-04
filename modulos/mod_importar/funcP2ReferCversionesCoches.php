<?php 

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
	
?>
