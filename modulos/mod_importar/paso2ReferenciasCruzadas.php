<?php 
/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogo productos Soluciones Vigo.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero
 * @Descripcion	Este fichero los utilizamos cmprobar el tabla Referencias Cruzadas temporal y añadir BDRecambios.
 * 
 * */ 
?>

<!DOCTYPE html>
<html>
    <head>
        <?php
// Reinicio variables
        $htmloptiones = '';
        $htmlfamilias = '';
        include './../../head.php';
        include_once ("./Consultas.php");
        $consultaRegistros = new ConsultaBD;
		$tabla ="referenciascruzadas";
		$whereC = ""; 
		$totalRegistro = $consultaRegistros->contarRegistro($BDImportRecambios,$tabla,$whereC);
        ?>
        <script src="<?php echo $HostNombre; ?>/modulos/mod_importar/importar.js"></script>
	    <script src="<?php echo $HostNombre; ?>/modulos/mod_importar/paso2ReferenciasCruzadas.js"></script>

        <script>
            var lineafinal; // Indica el final ciclo primero de fabricantes...
            var respuesta;
            var lineaIntermedia = 0;
            var ciclodefunciones;
            var fabricanteserror = 0;
            var lineabarra=0;
            var fabricante;
            var finallinea; // La utilizamos en barra
            var arrayConsulta;
            var intermedia = 0;
            var lineaIntermedia = 0;
            
            // Se ejecuta cuando termina de carga toda la pagina.
            $(document).ready(function () {
				ProcesoBarra(); // Para se defina..
                modifestadofab();
                
            });
        </script>
        
        

    </head>

    <body>
        <?php
        include './../../header.php';
        ?>
        <div class="container">
			<div class="col-md-12 text-center">
                <h2>Paso 2 - Añadir ReferenciasCruzadas al proveedor seleccionado </h2>
                <p> Ahora tenemos una tabla temporal con las referencias cruzadas que acabas de subir, donde solo se controlo que las lineas del fichero .csv contenga los campos.</p>
            </div>
			<div>
				<form class="form-horizontal" role="form">
					<div class="form-group">
						<legend>Seleccion  fabricante que acabas subir listado</legend>
					</div>
					<div class="form-group paso3">
						<?php
						// Realizamos consulta de Fabricantes
						$consultaFabricantes = mysqli_query($BDRecambios, "SELECT `id`,`Nombre` FROM `fabricantes_recambios` ORDER BY `Nombre`");
						// Ahora montamos htmlopciones
						while ($fila = $consultaFabricantes->fetch_assoc()) {
							$htmloptiones .= '<option value="' . $fila["id"] . '">' . $fila["Nombre"] . '</option>';
						}
						$consultaFabricantes->close();
						?>
						<label class="control-label col-md-4">Fabricante</label>
						<select name="fabricante" id="IdFabricante">
							<option value="0">Seleccione Fabricante</option>
							<?php echo $htmloptiones; ?>
						</select>
					</div>
				</form>
						
						
			</div>
            
            
            <div class="col-md-9">
                <h3>Resumen de comprobación de fichero</h3>
                 <table class="table table-striped">
					<thead>
					  <tr>
						<th></th>
						<th>Registros</th>
						<th>POSIBLES ESTADOS</th>
						<th></th>
					  </tr>
					</thead>
					<tbody>
					  <tr>
						<th>Tabla temporal <br/> ReferenciasCruzadas</th>
						<td>
							Total:<strong><?php echo $totalRegistro;?></strong><br/>
							</td>
						<td>
							[ERROR P2-21]:CampoVacio<br/>
							Registros:<strong><span id="campVa"></span></strong><br/>
							Fabricantes:<strong><span id="FabrError21"></span></strong><br/>

						</td>
						<td></td>
					  </tr>
					  <tr>
						<th>Comprobando <br/>Fabricantes,Marcas y distribuidores</th>
						<td>Fab_Importar:<strong><span id="Totfabcru"></span></strong><br/>
							Buscados:<strong><span id="fabcru"></span></strong><br/>
						</td>	
						<td>
							[ERROR P2-22]:FABRICANTE cruzado no existe<br/>
							Registros:<strong><span id="Rfabcru"></span></strong><br/>
							Fabricantes:<strong><span id="FabrError22"></span></strong><br/>
						</td>
						<td></td>

					  </tr>
					  <tr>
						<th>PASO 3: Registros Estado Blanco</th>
						<td>Registros a procesar:<strong><span id="RegBlanco"></strong></span></td>
						<td></td>
						<td> </td>
					  </tr>
					</tbody>
				  </table>
                
               
               	<div class="progress" style="margin:0 100px">
					<div id="bar" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                    0 % completado
                    </div>
				</div>
                <hr />
                
                <div class="alert alert-success">
				<div id="resultado" class="col-md-12">
                <!-- Aquí mostramos respuestas de AJAX -->
                </div>
                <span>Esto puede tardar unos minutos .....</span>
                </div>
                <div>
					<div class="form-group align-right">
							<div  id="compFichero">
								<input type="button" href="javascript:;" onclick="finalizar($('#IdFabricante').val());return false;" value="Comprobar" id="cmp" style="display: none;"/>
								
							</div>							
					</div>
                </div>
                <div id="fin"></div>

            </div>

        </div>
        

    </body>
</html>
