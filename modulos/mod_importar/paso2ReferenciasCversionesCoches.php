<?php
/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogo productos Soluciones Vigo.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero && Alberto lago
 * @Descripcion	Este fichero los utilizamos cmprobar el tabla ReferenciasCversiones temporal y añadir BDRecambios.
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
		include_once './Consultas.php';
		include_once ("./funcP2ReferCversionesCoches.php");

        ?>
        <script src="<?php echo $HostNombre; ?>/modulos/mod_importar/importar.js"></script>
	    <script src="<?php echo $HostNombre; ?>/modulos/mod_importar/paso2ReferenciasCversionesCoches.js"></script>
    </head>
    <body >
		<?php 
		include './../../header.php';
		// Ahora montamos opciones de select de fabricantes.
		$consultaFabricantes = mysqli_query($BDRecambios, "SELECT `id`,`Nombre` FROM `fabricantes_recambios` ORDER BY `Nombre`");
		while ($fila = $consultaFabricantes->fetch_assoc()) {
			$htmloptiones .= '<option value="' . $fila["id"] . '">' . $fila["Nombre"] . '</option>';
		}
		$consultaFabricantes->close();
		// Ahora obtenemos datos inicio para mostrar en resumen.
		$InicioConsulta = new ConsultaBD;
		// Realizamos resumen 
		$Resumen =CochesResumen($BDImportRecambios,$InicioConsulta);
		$RefDistintaSinIdRecambio = $Resumen[0]['TotalReferenciasDistintas'];
		$RefDistintaSinIdversion = $Resumen[1]['TotalReferenciasDistintas'];
		$TotalRegistros =  $Resumen['TotalRegistro'];
		$RegistrosVisto =  $Resumen['RegistroVistos'];

		echo '<pre>';
		print_r($Resumen);
		echo '</pre>';
        ?>
        <div class="container">
            <div class="col-md-12 text-center">
                <h2>Paso 2 - Añadir versiones de coches que se aplican a los recambios.</h2>
			 </div>
             <div class="col-md-6">
				 <h4>Seleccion Fabricante del cual pertenece el fichero</h4>
				 <form class="form-horizontal" role="form">
					
					<div class="form-group paso3">
						
						<label class="control-label col-md-4">Fabricante Principal<a title="El fabricante que nos dio el fichero de referencias cruzadas, con el que cruzan">(*)</a></label>
						<select name="fabricante" id="IdFabricante">
							<option value="0">Seleccione Fabricante</option>
							<?php echo $htmloptiones; ?>
						</select>
					</div>
				</form>
				 
				<p> Hay que tener en cuenta que las versiones de coches las comprobamos en la BDCoches y si son correctas entonces se añade esa relacion con el recambio.</p>
				
                <h4>Resumen de comprobación de tabla temporal ReferenciaCVersiones</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Registros</th>
                            <th>Otros Datos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Registros de <br/>Referencias Versiones</th>
                            <td>Total Registros
                            <a title="Total de registros que contiene la tabla referenciacversiones de BDImportar.">(*)</a>
                            :<strong>
							<span id="TotalRegistros">
								<?php echo $TotalRegistros;?>
							</span>
							</strong>
							
                             </td>
                            <td>
                             Registros Analizados
                            <a title="Registros analizados que tiene Estado o tienen cubierto IDrecambio y IDversion.">(*)</a>
                            :<strong>
							<span id="EstadoCubierto">
								<?php echo $RegistrosVisto;?>
							</span>
							</strong>
                            </td>
                        </tr>
                        <tr>
                            <th>Distintos <br/>IDRecambio</th>
                            <td>Distintas Referencias
                            <a title="Cantidad (distintas) referencias principales encontradas que no tenga RecambioID.">(*)</a>
                            :<strong>
							<span id="DistintasReferenPrincipales">
								<?php 
								if ($RefDistintaSinIdRecambio >0 ){
								echo $RefDistintaSinIdRecambio;
								} else {
								echo '0';
								}
								?>
							</span>
							</strong><br/>
							<span>Sin ID Recambio.</span>
                             </td>
                            <td>Con ID Recambio
                             <a title="Cantidad (distintas) referencias principales encontradas que tiene IDRecambio.">(*)</a>
                            :<strong>
								<span id="NItemIDRecambio"><?php echo $Resumen[0]['RefDistintasConID'];?></span>
								</strong><br/>
                            Error Recambio
                             <a title="Cantidad (distintas) referencias principales con ERROR:[ERROR P2-23]:Referencia Principal no existe.">(*)</a>
                            :<strong>
								<span id="NItemError"><?php echo $Resumen['RefDistintasError'];?></span>
								</strong><br/>
                            </td>
                        </tr>
                        <tr>
                            <th>Distintos <br/>IDVersiones</th>
                            <td>Distintas Referencias
                            <a title="Cantidad (distintas) referencias principales encontradas que no tenga IDVersiones.">(*)</a>
                            :<strong>
							<span id="DistintasRefPrinSIDversion">
								<?php 
								if ($RefDistintaSinIdversion >0 ){
								echo $RefDistintaSinIdversion;
								} else {
								echo '0';
								}
								?>
							</span>
							</strong><br/>
							<span>Sin ID Recambio.</span>
                             </td>
                            <td>Registros con IDVersiones:<strong>
								<span id="NItemIDVersiones"><?php echo $Resumen[1]['RefDistintasConID'];?></span>
								</strong><br/>
                            <span>Y el estado esta blanco.</span>
                            </td>
                        </tr>
                        <tr>
                            <th>PASO 3: Registros Estado Blanco</th>
                            <td>Registros a procesar:<strong><span id="RegBlanco"></strong></span></td>
                            <td> </td>
                        </tr>
                    </tbody>
                </table>
				
				<div class="col-md-6">
					<?php 
					if ($RefDistintaSinIdRecambio >0 ){
					// Quiere decir que tiene registros sin IDRecambio ...
					?>
					<form class="form" role="form" id="relaciones" action="javascript:CochesObtenerRegistros('IDrecambio');">
					
					<div class="form-group">
						<button id="btn-IDRecambio" type="submit" class="btn btn-primary btn-sm">ID Recambio</button>
					</div>
					
					</form>
					<?php
					} // Cerramos el if anterior
					?>
				</div>
				
				<div class="col-md-6">
					<?php 
					if ($RefDistintaSinIdversion >0 ){
					// Quiere decir que tiene registros sin IDversiones
					?>
					<form class="form" role="form" id="relaciones" action="javascript:CochesObtenerRegistros('IDversion');">
					
					<div class="form-group">
						<button id="btn-IDVersion" type="submit" class="btn btn-primary btn-sm">Anotar ID Version</button>
					</div>
					</form>
					<?php 
					}
					?>
					
				</div>	
				

            </div>
<!-- Tablas temporales de coches -->
            <div class="col-md-6">
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
				<hr />

				<div>
					<hr/>	
					<h4>Tablas temporales coches</h4>
					<p>En BDimportarRecambios no hay tablas temporales de coches (marcas,modelos,versiones,combustible), lo que hacemos es crearla por si queremos añadirlas a BDCoches,<strong> pero de momento no tengo el proceso hecho.</strong></p>
					<p>Las tablas temporales coches en BDimportaRecambios las puedes volver a crear siempre que quieras, teniendo en cuenta elimina los datos que puedan tener.</p>
					<div class="col-md-6">
						<table class="table table-bordered text-center">
						<thead>
							<tr>
								<th class="text-center" colspan="2">Tabla Marca</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>
									Creada:
								</th>
								<td>
									<span><img src="./img/ajax-loader.gif"/></span>
									<span class="glyphicon glyphicon-check"></span>
									<span class="glyphicon glyphicon-remove"></span>
								</td>
							</tr>
							<tr>
								<th>
								Registros:
								</th>
								<td>
								</td>
							</tr>
						</tbody>
						</table>
					</div>
					<div class="col-md-6">
						<table class="table table-bordered text-center">
						<thead>
							<tr>
								<th class="text-center" colspan="2">Tabla Combustibles</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>
									Creada:
								</th>
								<td>
									<span><img src="./img/ajax-loader.gif"/></span>
									<span class="glyphicon glyphicon-check"></span>
									<span class="glyphicon glyphicon-remove"></span>
								</td>
							</tr>
							<tr>
								<th>
								Registros:
								</th>
								<td>
								</td>
							</tr>
						</tbody>
						</table>
					</div>
					<div class="col-md-6">
						<table class="table table-bordered text-center">
						<thead>
							<tr>
									<th class="text-center" colspan="2"> Tabla Modelos</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>
									Creada:
								</th>
								<td>
									<span><img src="./img/ajax-loader.gif"/></span>
									<span class="glyphicon glyphicon-check"></span>
									<span class="glyphicon glyphicon-remove"></span>
								</td>
							</tr>
							<tr>
								<th>
								Registros:
								</th>
								<td>
								</td>
							</tr>
						</tbody>
						</table>
					</div>
					<div class="col-md-6">
						<table class="table table-bordered text-center">
						<thead>
							<tr>
								<th class="text-center" colspan="2">Tabla Versiones</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>
									Creada:
								</th>
								<td>
									<span><img src="./img/ajax-loader.gif"/></span>
									<span class="glyphicon glyphicon-check"></span>
									<span class="glyphicon glyphicon-remove"></span>
								</td>
							</tr>
							<tr>
								<th>
								Registros:
								</th>
								<td>
								</td>
							</tr>
						</tbody>
						</table>
					</div>
						
					<div class="col-md-2">
						<form class="form" role="form" id="tablas" action="javascript:crearTablas();">
							<div class="form-group">
								<button type="submit" class="btn btn-primary btn-sm">crear tablas</button>
							</div>
						</form>
					</div>	
					<div class="col-md-6">
						<form class="form" role="form" id="relaciones" action="javascript:CochesInsertTemporal();">
						<div class="form-group">
							<button type="submit" class="btn btn-primary btn-sm">Añadimos datos tablas temporales</button>
						</div>
						</form>
					</div>	
					<div class="col-md-4">
						<form class="form" role="form" id="relaciones" action="javascript:CochesUpdateTemporal();">
						<div class="form-group">
							<button type="submit" class="btn btn-primary btn-sm">Crear relaciones entre ellas</button>
						</div>
						</form>
					</div>		
						
                </div>
                
			</div>	

		
    </body>
</html>
