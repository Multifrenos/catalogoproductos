<?php 
// En este modulo lo que hacemos es la comprobación de que todo esta ok y actualizamos 

?>
<!DOCTYPE html>
<html>
<head>
<?php
	include_once './../../head.php';
?>
	<script src="<?php echo $HostNombre; ?>/modulos/mod_sincronizar/funciones.js"></script>
</head>
<body>
<?php 
	include_once './../../header.php';
	// Ahora creamos array con los iconos que vamos mostrar en la pagina.
	$iconos = array();
	$iconos[1] = '<span class="glyphicon glyphicon-ok-sign"></span>';// Correcto
	$iconos[2] = '<span class="glyphicon glyphicon-remove"></span>'; // Incorrecto
	$iconos[3] = '<span class="glyphicon glyphicon-info-sign"></span>'; // Informacion
	$iconos[4] = '<span><img src="../../css/img/ajax-loader.gif"/></span>';
	// Creamos array para mostrar lo que tenemos comprobar a la carga pagina.
	// Ya que hay comprobaciones que debemos tener claras antes de poder permitir hacer otras, realizaremos con Ajax y Javascript.
	$comprobaciones = array();
	// Cada item del array tendra:
	// Estado = De los que puede ser:
	// 			-Correcto
	// 			-Incorrecto
	// 			-Pendiente.
	// Icono = Será html para mostrar el estado.
	// 			-Estado correcto: 	$iconos[1]
	//			-Estado incorrecto:	$iconos[2]
	// 			-Estado pendiente:   .. No pongo Icon , para evitar malos entendimiento... 
	
	
	
	// COMPROBAMOS SI NO SE MODIFICO LA TABLA VIRTUEMART_PRODUCT.
	if (isset($DifVirtuemart['Rows'])){
		$comprobaciones['virtuemart']['Estado'] ="Incorrecto"; // Id = EstadoSincro, ObservaSincro
		$comprobaciones['virtuemart']['Icono'] = $iconos[2];
		$mostrabtn = "display:none";
	} else {
		$comprobaciones['virtuemart']['Estado'] ="Correcto"; // Id = EstadoSincro, ObservaSincro
		$comprobaciones['virtuemart']['Icono'] = $iconos[1];
		$mostrabtn = "display:block";

	}
	
?>
<div class="container">
	<h2>Sincronizacion y comprobacion de bases de datos ( Recambios con la WEB ).</h2>
	<div class="col-md-5">
		<h4>Sincronizacion</h4>
			<p> La sincronizacion de bases de datos ( Recambios con la WEB ) consiste en:</p>
		<ul>
			<li>Copiar la tabla de BD de la web virtuemart_products en BD Recambios	</li>
			<li>Comprobar que todos los productos que hay en la Web tiene referencia de Recambios</li>
		</ul>
		
		<h4>Especificaciones Tecnicas</h4>
		<ul>
			<li> Tener conexion con la Web</li>
		</ul>
	</div>
	<div class="col-md-7">
		<div>
			
			<h4>Barra de Proceso</h4>
			<div class="progress" style="margin:0 100px">
				<div id="bar" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
				0 % completado
				</div>
			</div>
			<div id="resultado" class="col-md-12">
			<!-- Aquí mostramos respuestas de AJAX -->
			</div>
			
		</div>
		
		<h4> Resultado de sincronización</h4>
		
		<table class="table table-bordered">
			<thead>
			  <tr>
				<th></th>
				<th>
					<input type="checkbox" name="checkTodos" value="0" onchange="metodoClick('TodaSeleccion');">
					<a title ="Seleccionamos todos">
					<?php echo $iconos[3];?>
					</a>
				</th>
				<th>Estado</th>
				<th>Observacion</th>
			  </tr>
			</thead>
			<tbody>
			  <tr>
				<th>Sincronizacion Virtuemart</th>
				<td></td>
				<td id="EstadoSincro" ><?php echo $comprobaciones['virtuemart']['Icono'] ;?>
	</td>
				<td id="ObservaSincro" ></td>
			  </tr>
			  <tr>
				<th>Referencias
				<a title=" Comprobamos que todos los productos de virtuemart tenga las referencias fabricante cruzado y el id del recambio sea correcta">
					<?php echo $iconos[3];?>
				</a>
				</th>
				<td></td>
				<td id="EstadoReferencias"></td>
				<td id="ObservacionesReferencias"></td>
			  </tr>
			  <tr>
				<th>Reescribir <br/> Descripcion Larga
				<a title=" Re-escribimos la descripcion larga de todos los productos.">
					<?php echo $iconos[3];?>
				</a>
				</th>
				<td></td>

				<td id="EstadoDescripcion"></td>
				<td id="ObservacionesDescrion"></td>
			  </tr>
			</tbody>
		</table>
		<!-- Recuerda que entrada, si no es correcta varaible virtuemart-estado no se muestra -->
		<div id="capa-botones" style="<?php echo $mostrabtn;?>">
			<p> Presentacion de botones acciones.</p>
			<div class="col-md-4" id="f-revisarRef"  >
				<form class="form" role="form" id="f-referencias" action="javascript:ComprobarRefVirtuemart(1);">
				
				<div class="form-group">
					<button id="btn-Referencias" type="submit" class="btn btn-primary btn-sm">Comprobar Referencias</button>
				</div>
				
				</form>
			</div>
			<div class="col-md-4" id="f-erroresCsv" style="display:none;" >
				<form name="EnvioErrores" class="form" method="post"  action="./MostrarErrores.php">
				<div class="form-group">
					<button id="btn-Reescribir" type="submit" class="btn btn-warning btn-sm">Ver errores</button>
					<input type="hidden" id="DatosErrores" name="errores">
				</div>
				
				</form>
			</div>
			<div class="col-md-4" id="f-copiarDescrip" style="display:none;" >
				<form name="CopiarDescripcion" class="form" method="post"  action="javascript:InicioCopiarDescripcion();">
				<div class="form-group">
					<button id="btn-CopiarDescripcion" type="submit" class="btn btn-danger">Copiar Descripciones</button>
					<input type="hidden" id="DatosErrores" name="errores">
				</div>
				
				</form>
			</div>
			
		</div>
		
		
		

	</div>
</div>



	<script type="text/javascript">
		<?php 
		// Aunque sea correcto siempre se va sincronizar, ya que
		// puede que las comprobaciones iniciales sean correctas, pero se  vaya
		// pulsar para hacer mas comprobaciones, por lo que se debe estar seguro que 
		// esos son los datos que tenemos en virtuemart.
		echo 'Sincronizar();';
		?>
	</script>

</body>
</html>
