<?php
	/* Mostramos menu y realizamos comprobaciones comunes...
	 * */
	$htmlError = '';
	$Controlador = new ControladorComun;
	$htmlError = $Controlador->VerConexiones($Conexiones);
	// Revisamos conexiones
	if ($htmlError == '' ){
		// Comprobamos sincronizacion: parametros ( Base datos local y Bases datos web )
		$DifVirtuemart= $Controlador->SincronizarWeb($BDRecambios,$BDWebJoomla,$prefijoJoomla);
	} else {
		// Quiere decir que hay error de conexiones
		$htmlError .= $Controlador->VerConexiones($Conexiones);
	}
	
	

?>

<header>
   <!-- Debería generar un fichero de php que se cargue automaticamente el menu -->
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
	<a class="navbar-brand" href="#">Catalogo</a>
      </div>
      <ul class="nav navbar-nav">
	<li class="active"><a href="<?php echo $HostNombre.'/index.php'?>">Home</a></li>
	<li><a href="<?php echo $HostNombre.'/modulos/mod_recambios/ListaRecambios.php';?>">Recambios</a></li>
	<li><a href="./familia2.html">Familia 2</a></li>
	<li><a href="./familia3.html">Familia 3</a></li>
	<li><a href="<?php echo $HostNombre.'/modulos/mod_buscar/buscar.php';?>">Buscar</a></li>
	<li><a href="<?php echo $HostNombre.'/modulos/mod_importar/Importar.php';?>">Importar</a></li>
      </ul>
	   
		<div class="text-right">
		<?php // Creamos icono de informacion para indicar que la BD Web y BD Recambios no estan bien sincronizadas.
			$UrlSincro = $HostNombre.'/modulos/mod_sincronizar/sincronizar.php';
			$htmlDif ='<a href="'.$UrlSincro.'" class="navbar-brand"';
			if ($DifVirtuemart['Rows']){
				// Quiere decir que esta mal... no coiciden registros entre las BD
				$titleSincro = 'title="Error en sincronizacion,&#13; puede que no tengas todos los datos de la web"><span style="color:red; " class="glyphicon glyphicon-minus-sign"></span>';
			}else {
				// Quiere decir que coinciden el numero registros , por ello es correcto sincronizacion.
				$titleSincro = 'title="Correcta la sincronizacion,&#13; haz clic si quiere volver a sincronizar"><span class="glyphicon glyphicon-ok-sign"></span>';
			}
			echo $htmlDif.' '.$titleSincro.'</a>';
		?>
		</div>
	</div>	
  </nav>
  <!-- Fin de menu -->
</header>
