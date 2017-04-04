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
						<label class="control-label col-md-4">Fabricante Principal<a title="El fabricante que nos dio el fichero de referencias cruzadas, con el que cruzan">(*)</a></label>
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
							<a title="El campo estado esta por defecto en blanco hasta que no se &#10; termine el proceso, cuando no haya ningún registro con el Estado en blanco, empezará a grabar los Nuevas referenciaas, nuevos cruces o actualiza los existentes.">(*)</a>
							:<strong><span id="RegBlanco"></strong></span><br/>
							Registros sin IDs
							<a title="Los registros que tiene estado blanco y ademas tienen ID Recambio en 0,&#10; aun se busco si existe esa referencia, se hace en el PASO 2.">(*)</a>
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
							A buscar
							<a title="Los registros que tiene estado blanco y ademas tienen ID Fabricante es 0,&#10; aun se busco si el fabricante de ese cruce, se hace en el PASO 2.">(*)</a>
							:<strong><span id="Bfabcru"></span></strong><br/>
							YA buscados
							<a title="Los registros que tiene estado blanco y ademas el ID Fabricante es distinto de 0,&#10; son los fabricantes que busco y se identificaron, se hace en el PASO 2.">(*)</a>
							:<strong><span id="Yafabcru"></span></strong><br/>

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
						<td>Registros:
						<a title="Registros que aun no se comprobo si son Nuevas o existen en BDRecambios/referenciacruzadas o cruce_referencias.">(*)</a>
						 :<strong><span id="NuevoExisteDuplicado"></span></strong><br/>
						Nuevas Referencias:
						<a title="Referencias que añadimos a BDRecambios/referenciacruzadas y cruce_referencias.">(*)</a>
						 :<strong><span id="NuevasCreadas"></span></strong><br/>
						Nuevo cruce:
						<a title="Nuevo cruce cuando se añadio a cruce_referencia y existia la referencia cruzada en BDRecambios/refernciacruzadas &#10; y pero NO existe el cruce_referencias.">(*)</a>
						 :<strong><span id="NuevoCruce"></span></strong><br/>
						Existe:
						<a title="Existe tanto cruce como referencia cruzada, por lo que solo se actualiza campo FechaActualiza&#10; y asi queda registrado que se comprobo ese cruce tal fecha.">(*)</a>
						 :<strong><span id="ExisteCruce"></span></strong>
						 
						</td>
						<td> Referencias que existen:
						<a title="Referencias existen / comprobadas si existe en cruce.">(*)</a>
						<strong><span id="ExisteRefFaltaCruce"></span></strong><br/>
						Referencias Nuevas Duplicadas:
						<a title="Referencias Nuevas pero son duplicadas, ya que solo podemos dar un referencia como Nueva.">(*)</a>
						<strong><span id="NuevRefCruzDuplicada"></span></strong><br/>
						Referencias Nuevas:
						<a title="Referencias Nuevas que no existen en referenciascruzadas de BDRecambios y aun no las añadimos.">(*)</a>
						<strong><span id="NuevRefCruzadaPendi"></span></strong>
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
					<div id="resultado">
					<!-- Aquí mostramos respuestas de AJAX -->
					</div>
                </div>
                <div>
					<div class="form-group align-right">
							<div  class="compFichero">
								<input type="button" href="javascript:;" onclick="comprobar($('#IdFabricante').val());return false;" value="Comprobar Fabricante Principal" id="cmp" style="display: none;"/>
							</div>
							<div  class="compFichero">
								<input type="button" href="javascript:;" onclick="ObtenerReferenciasPrincipales('proceso2')" value="Comprobar existen Referencias Principales" id="ComprobarRefPrin" style="display: none;"/>
								
							</div>	
							<div  class="compFichero">
								<input type="button" href="javascript:;" onclick="ObtenerReferenciasPrincipales('proceso3')" value="Comprobando si es Referencia Cruzadas nueva o ya existe" id="nuevoExiste" style="display: none;"/>
								
							</div>
							<div  class="compFichero">
								<input class="btn btn-danger" type="button" href="javascript:;" onclick="ObtenerReferenciasPrincipales('proceso4')" value="Creamos Nuevas Referencias Cruzadas" id="btnReferenciasCruzadas" style="display: none;"/>
								
							</div>	
							<div  class="compFichero">
								<input class="btn btn-warning" type="button" href="javascript:;" onclick="ObtenerReferenciasPrincipales('proceso5')" value="Comprobamos si existen CRUCES" id="btnComprobarCruce" style="display: none;"/>
								
							</div>	
							<div  class="compFichero">
								<input class="btn btn-warning" type="button" href="javascript:;" onclick="ObtenerReferenciasPrincipales('proceso6')" value="Creamos Nuevos CRUCES" id="btnFaltaCruce" style="display: none;"/>
								
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
            var ciclo;
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
