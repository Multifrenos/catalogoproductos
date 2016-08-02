<!DOCTYPE html>
<html>
<head>
<?php
	include './../../head.php';
?>
</head>
<body>
<?php
	include './../../header.php';
?>
	<div class="container">
		<div class="col-md-12 text-center">
			<h2>Revisando fichero subido</h2>
		</div>
		<?php
			// En versiones de PHP anteriores a la 4.1.0, debería utilizarse $HTTP_POST_FILES en lugar
			// de $_FILES.
			// Lugar donde el servidor indica que guarda los tmp
			$dir_subida = '/tmp/';
			$fichero_subido = $dir_subida . basename($_FILES['fichero_usuario']['name']);
			$correcto = '';
			$errorFichero = '';
			// Comprobamos si creo el fichero copio el fichero en el destino
			if (move_uploaded_file($_FILES['fichero_usuario']['tmp_name'], $fichero_subido)) {
				$correcto = " - El fichero es válido y se subió con éxito.<br/>";
			} else {
				$errorFichero= "- Fichero no subido correctamente.<br/>";

			}
		// Este error hacemos que no continue comprobando que salga.
		if ($errorFichero != ''){
			?>
			<div class="alert alert-danger">
			<strong>ERRORES <br/></strong>
				<?php echo $errorFichero;?>
			</div>	
	</div> <!-- Cerramos div container ya que no continuamos -->
</body>
</html>
		<?php
		return;
		}
		?>
		
		
		
		<div class="col-md-6">
			<?php
			// Comprobamos si es text/csv
			if ($_FILES['fichero_usuario']['type'] == 'text/csv') {
				$correcto = $correcto . " - El fichero es text/csv.<br/>";
			} else {
				$errorFichero = $errorFichero . "- No es un text/csv.<br/>";

			}
			
			// Ahora deberíamos comprobar cuanto registros tiene y si los campos son correcto...
			// Creamos url de fichero csv ( De momento bloqueamos que no sea otro fichero...
			// es decir que se tiene que llamar ReferenciasCruzadas.csv
			// Ya que lo normal es que el nombre se pongamos con variable
			// $_FILES['fichero_usuario']['name']
						
			//abro el archivo para lectura
			$archivo = fopen('/tmp/ReferenciasCruzadas.csv','r');

			//inicializo una variable para llevar la cuenta de las líneas y los caracteres
			$num_lineas = 0;
			//Hago un bucle para recorrer el archivo línea a línea hasta el final del archivo
			// Mostramos las primeras 10 lineas registro si las hay claro..
			?>
			<h4>Mostramos las primeras lineas del fichero subido</h4>
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Linea</th>
						<th>Contenido</th>
					</tr>
				</thead>
				<?php
				while (!feof ($archivo)) {
					//si extraigo una línea del archivo y no es false
					if ($lineactual = fgets($archivo)){
					   // El contador empieza en 0
					   if ($num_lineas < 10 ) {
						?>
					<tr>
						<td> <?php echo $num_lineas;?>
						</td>
						<td>
						<?php echo $lineactual; ?>
						</td>
					</tr>
						<?php
						}
					 //acumulo una en la variable número de líneas
				  $num_lineas++;
				  
					}
					
				} // Fin de bucle.
				?>
				</table>
				<?php
				fclose ($archivo);
			// Añadimos numero de lineas a variables de control.
			// Si solo hay un registro, o ninguno lo ponemos como error
			if ($num_lineas < 2 ) {
				$errorFichero = $errorFichero . '- No tiene registros suficiente para procesar,'.$num_lineas.'<br/>';

			} else {
				$correcto = $correcto . '- Numero de registros a procesar son '.$num_lineas.'<br/>';

			}
			
			
			// Ahora imprimimos resultado control de fichero
			?>
		</div>
		<div class="col-md-6">
			<h4>Comprobamos si el fichero es correcto</h4>
			<div class="alert alert-info">
			<strong>COMPROBACIONES BÁSICAS CORRECTAS <br/></strong>
				<?php echo $correcto;?>
				
			</div>	
			<?php
			if ($errorFichero != ''){
			?>
			<div class="alert alert-danger">
			<strong>ERRORES <br/></strong>
				<?php echo $errorFichero;?>
			</div>	
			<?php
			}	else {?>
			<div>
				<h4> Ahora necesitamos de tu intervención:</h4>
				<p>Si la primera lines (0) contiene la cabeceras del csv, es decir los nombres de los campos debes indicarlo: </p>
				<form class="form-horizontal" role="form" action="action_page.php">
					<div class="form-group">
					<legend>¿Desde que línea quiere importar?</legend>
					</div>
					<div class="form-group">
					<label class="control-label col-md-4">Línea Inicial</label>
					<input class="control-label col-md-6" type="number" id="LineaInicial" name="linea_inicial" value="0">
					</div>
					<div class="form-group">
					<label class="control-label col-md-4">Línea Final</label>
					<input class="control-label col-md-6" type="number" id="LineaFinal" name="linea_final" value="<?php echo $num_lineas;?>">
					</div>
					<div class="form-group align-right">
					<input type="button" href="javascript:;" onclick="valoresProceso($('#LineaInicial').val(), $('#LineaFinal').val());return false;" value="Calcula"/>
					</div>
				</form>

				<p>Ahora vamos a comprobar si hay tres campos en las lineas seleccionadas</p>
				<!-- Script para ejecutar funcion php -->
				<script>
				var lineaActual = 0;
				var lineaF = 0;
				var ciclo;

				// Ahora comprobamos si ya inicio proceso entonces los ejecutamos 
				//~ setInterval(bucleProceso(lineaActual,lineaF),2000);
				alert('Al iniciar \n Linea Actual: '+ lineaActual + 'Linea Final: '+ lineaF);
				function bucleProceso (lineaF,linea) {
					// Si la diferencia es mayor a mi lineas, tomamos esas mil lineas.
					
					//~ alert('Entro Bucle Proceso: \n '+ 'Linea Final'+ lineaF + ' \nLinea Actual: '+ linea);

					if (parseInt(linea) < parseInt(lineaF)) {
						diferencia = parseInt(lineaF) - parseInt(linea)
						if (parseInt(diferencia) >1000 ) {
							lineaActual = parseInt(linea) + 1000;
							diferencia= 1000; // Para utilizar en bucle
						} else {
							lineaActual = parseInt(linea) + parseInt(diferencia);
							// El valor de diferencia es correcto, ya que el final , menos 1000 
						}
					//~ alert('Entro If: \n '+ 'Linea Final'+ lineaF + ' \nLinea Actual: '+ lineaActual);

					//~ alert('Entro IF: \n '+ 'Linea Actual'+ lineaActual);
					// Iniciamos proceso Barra;
					consultaDatos(linea,lineaActual);

					BarraProceso(lineaActual,lineaF);
					
					// Ahora si ya son iguales los linea y lineaF entonces terminamos ciclo
						if ( parseInt(lineaActual) == parseInt(lineaF) ){
							alert ( 'terminamos' );
							clearInterval(ciclo);
						}
					}
				}
				
				
				function cicloProcesso () {
				alert('Iniciamos ciclo');
				bucleProceso(lineaF,lineaActual)
				ciclo = setInterval("bucleProceso(lineaF,lineaActual)",30000);

				
				}
				
				function valoresProceso(valorCaja1, valorCaja2){
				lineaF= valorCaja2;
				var lineaI= valorCaja1;
				// Si lineaI es mayor a Actual entonces debemos igualarla.
				//~ if (parseInt(lineaI) > parseInt(lineaActual)) {
					lineaActual = lineaI;
				//~ }
				
				alert('Entro valoresProceso: \n '+ 'Linea Actual'+ lineaActual + ' \nLinea Final: '+ lineaF);

				// Iniciar ciclo proceso. ;
					cicloProcesso ();
				}
				
				
				function consultaDatos(linea,lineaF) {
					var parametros = {
					"lineaI" : linea,
					"lineaF" : lineaF
							};
					$.ajax({
							data:  parametros,
							url:   'comprobarCampos.php',
							type:  'post',
							beforeSend: function () {
									$("#resultado").html("Procesando, espere por favor...");
							},
							success:  function (response) {
									$("#resultado").html(response);
									
							}
						});
				}
				
				

				
				</script>
				
				
				
				
				
				<div id="resultado">
				</div>
				<div class="progress" style="margin:100px">
					<div id="bar" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
						0 % completado
<!--
						<span id="spanProceso" class="sr-only">0% Complete</span>
-->
					</div>
				</div>
			</div>	
			<?php
			}
			?>
			
			

		</div>
	</div>
		
</body>
</html>
