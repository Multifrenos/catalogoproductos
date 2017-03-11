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
				<h3>Seleccion  fabricante que acabas subir listado</h3>
				<form class="form-horizontal" role="form">
					
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
                <p><small><strong>(?)</strong> Quiere decir que no hizo el paso aun</small></p>
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
							Estado en blanco
							<a title="El campo estado esta por defecto en blanco hasta que no se termine el proceso,&#10; o haya encontrado un error, al termina se puede ver NUEVOS,EXISTENTES...">(*)</a>
							:<strong><span id="RegBlanco"></strong></span><br/>
							Registros sin IDs
							<a title="Registros que tiene estado blanco, tambien el ID Recambio no es 0,&#10; deber ser cero si ya terminamos el PASO 2.">(*)</a>
							:<strong><span id="RegBlancoCRecambio"></strong></span>
							</td>
						<td>
							[ERROR P2-21]:CampoVacio<br/>
							Registros Descartados:<strong><span id="campVa"></span></strong><br/>
							Fabricantes Descartados:<strong><span id="FabrError21"></span></strong><br/>

						</td>
						<td></td>
					  </tr>
					  <tr>
						<th>Comprobando <br/>Fabricantes,Marcas y distribuidores</th>
						<td>Fab_Importar
							<a title="Fabricantes encontrados contando &#10; los descartados.">(*)</a>
							:<strong><span id="Totfabcru"></span></strong><br/>
							A buscar:<strong><span id="Bfabcru"></span></strong><br/>
							YA buscados:<strong><span id="Yafabcru"></span></strong><br/>

						</td>	
						<td>
							[ERROR P2-22]:FABRICANTE cruzado no existe<br/>
							Registros Descartados:<strong><span id="Rfabcru"></span></strong><br/>
							Fabricantes Descartados:<strong><span id="FabrError22"></span></strong><br/>
						</td>
						<td></td>

					  </tr>
					  <tr>
						<th>Comprobando <br/> Referencias Principales</th>
						<td>Todas
							<a title="Referencias encontradas distintas en el fichero &#10; y con IdFabricaCruzado distinto 0 &#10;TODOS!!.">(*)</a>
							:<strong><span id="RefPrincipales"></strong></span><br/>
							A Analizar:
							<a title="Referencias encontradas distintas con estado blanco &#10; y con IdFabricaCruzado distinto 0 &#10; y ademas NO tiene ID Recambio.">(*)</a>
							:<strong><span id="RefPrinPendIDRecam"></strong></span><br/>
							Con IDRecambio:
							<a title="Referencias encontradas distintas con estado blanco &#10; y con IdFabricaCruzado distinto 0 &#10; y ademas se asocio el ID Recambio.">(*)</a>
							:<strong><span id="RefPrincipalesIDRecam"></strong></span>
						 
						</td>
						<td>[ERROR P2-23]:Referencia Principal no existe.<br/>
							Registros Descartados:<strong><span id="Error23"></span></strong><br/>
							Ref_Principales Descartada:<strong><span id="RefPrincDescartadas"></strong></span>
						</td>
						<td> </td>
					  </tr>
					  <tr>
						<th>Referencias Cruzadas</th>
						<td>Nueva Referencia Cruzada
						<a title="Nuevas Referencias cruzadas son aquellas que no existe en BDRecambios/referenciacruzadas &#10; y por lo que si añade tanto esa tabla como crucereferencias.">(*)</a>
						 :<strong><span id="NuevRefCruzada"></strong></span><br/>
						Nuevo cruce:
						<a title="Nuevo cruce es que existe la referencia cruzada en BDRecambios/refernciacruzadas &#10; y pero NO existe el cruce entre el recambio.">(*)</a>
						 :<strong><span id="NuevoCruce"></strong></span><br/>
						Existe:
						<a title="Existe tanto cruce como referencia cruzada, por lo que solo se actualiza campo FechaActualiza&#10; y asi queda registrado que se comprobo ese cruce tal fecha.">(*)</a>
						 :<strong><span id="ExisteCruce"></strong></span>
						 
						</td>
						<td>
						</td>
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
								<input type="button" href="javascript:;" onclick="comprobar($('#IdFabricante').val());return false;" value="Comprobar" id="cmp" style="display: none;"/>
								
							</div>	
							<div  id="compFichero">
								<input type="button" href="javascript:;" onclick="ObtenerReferenciasPrincipales('paso3')" value="Nuevo o Existe" id="nuevoExiste" style="display: none;"/>
								
							</div>							
					</div>
                </div>
                <div id="fin"></div>

            </div>

        </div>
         <script>
            var lineafinal; // Indica el final ciclo primero de fabricantes...
            var respuesta;
            var arrayConsulta;
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
                //~ var algo = 0;
                resumenresul();
				//~ ProcesoBarra(); // Para se defina..
                
            });
        </script>
        

    </body>
</html>
