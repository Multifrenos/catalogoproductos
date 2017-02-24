<?php
/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogo productos Soluciones Vigo.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero
 * @Descripcion	Funciones en php para Paso2ListaPrecios.
 *  */


 
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


// Funcion que llamos desde Paso2Listaprecios -> Tareas -> Funcion contador
// En donde realizamos RESUMEN, es decir comprueba cuantos registros hay y cuantos son nuevo o existentes.
function contador($nombretabla, $BDImportRecambios,$ConsultaImp) {
	// Inicializamos array
    $Tresumen['n'] = 0; //nuevo
    $Tresumen['t'] = 0; //total
    $Tresumen['e'] = 0; //existe
	$Tresumen['v'] = 0; //existe
	
	// Contamos los registros que tiene la tabla
   	$total = 0;
    $whereC = '';
    $total = $ConsultaImp->contarRegistro($BDImportRecambios,$nombretabla,$whereC);
    $Tresumen['t'] = $total; // total registros
		
    // Obtenemos lineas de registro en blanco y contamos cuantas
	$whereC = " WHERE trim(Estado) = ''";
	$campo[1]= 'RefFabPrin';
	$campo[2]= 'linea';
	$RegistrosBlanco = $ConsultaImp->registroLineas($BDImportRecambios,$nombretabla,$campo,$whereC);
	// Como queremos devolver java los creamos
	$Tresumen['v'] = $RegistrosBlanco['NItems'];
	$Tresumen['LineasRegistro'] = $RegistrosBlanco; //Registros en blanco
    
    
	// Contamos los registros que tiene la tabla nuevo
	$total = 0;
	$whereC = " WHERE Estado = 'nuevo'";
    $total = $ConsultaImp->contarRegistro($BDImportRecambios,$nombretabla,$whereC);
    $Tresumen['n'] = $total; //nuevo
	
	
	
	
	
	
	// Contamos los registros que tiene la tabla existente
	$total = 0;
	$whereC = " WHERE Estado = 'existe'";
    $total = $ConsultaImp->contarRegistro($BDImportRecambios,$nombretabla,$whereC);
	$Tresumen['e'] = $total; //existe
    return $Tresumen;
}

/* =========================  Funcion de consulta  ========================================*/
   // Encontramos que la tabla listaprecios tiene registros con el estado VACIO, entonces 
   // comprobamos en la tabla REFERENCIASCRUZADAS de BD de RECAMBIOS, si existe la referencia
   // 		-Si existe se pone en ESTADO = "existe"
   // 		-NO existe se pone en ESTADO = "nuevo"
   // Estos cambios son el campor ESTADO de la tabla LISTAPRECIOS de BD IMPORTARRECAMBIOS.
            
function comprobar($nombretabla, $BDImportRecambios, $BDRecambios,$id,$l,$f) {
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
    return $datos;
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
    $respuesta = array();
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
        // No debería suceder nunca
        echo "algo";
        }
		// Buscamos en fabricantes_recambios la fabricante seleccionada.
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
        $respuesta['Consulta1']=$consul;
        $resul = $BDRecambios->insert_id;

        $consulta = "INSERT INTO `referenciascruzadas`( `IdFabricanteCru`, `RecambioID`, `RefFabricanteCru`,`FechaActualiza`) VALUES ('" . $fabricante . "','" . $resul . "','" . $ref . "','" . $fecha. "')";
        $BDRecambios->query($consulta);
        $respuesta['Consulta2']=$consulta;
        $resFinal = $BDRecambios->insert_id;

        $consulta = "INSERT INTO `recamb_familias`( `IdRecambio`, `IdFamilia`,`FechaActualiza`) VALUES (" . $resul . "," . $familia . ",'"  .$fecha . "')";
		$BDRecambios->query($consulta);
        $respuesta['Consulta3']=$consulta;

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
    return $respuesta;
}

