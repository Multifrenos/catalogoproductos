<?php 
/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogo productos Soluciones Vigo.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero
 * @Descripcion	Este fichero los utilizamos para :
 * 				Comprobar el tabla Referencias Cruzadas
 * 					1.- Si los campos menos 2 caracteres su ESTADO= ERR:[CampoVacio]
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

            <div class="col-md-8">
                <h3>Resumen de comprobación de fichero</h3>
                 <table class="table table-striped">
					<thead>
					  <tr>
						<th>COMPROBANDO</th>
						<th>Registros</th>
						<th>CAMPO ESTADO</th>
						<th>Otros Datos</th>
					  </tr>
					</thead>
					<tbody>
					  <tr>
						<th>Campos con menos 2 caracteres</th>
						<td>Registro de tabla:<strong><?php echo $totalRegistro;?></strong><br/>
						Registros con ESTADO en blanco:
						<strong><span id="total"></span></strong></td>
						<td>ERR:[CampoVacio]</td>
						<td>Registros campos mal:<strong><span id="campVa"></span></strong></td>
					  </tr>
					  <tr>
						<th>Fabricantes cruzados que no existen</th>
						<td>Analizando FAB de tabla de importacion:<strong><span id="fabcru"></span></strong><br/>Registros descartados:<strong><span id="Rfabcru"></span></strong></td>
						<td>ERR:[FABRICANTE cruzado no existe]</td>
						<td>De los Fabricantes analizados se descartan:<strong><span id="fabcruDes"></span></strong></td>

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
                <div id="resultado" class="col-md-12">
                <!-- Aquí mostramos respuestas de AJAX -->
                </div>
                <form class="form-horizontal" role="form" action="action_page.php">
                    <div class="form-group">
                        <legend>Seleccion  Fabricante que acabas subir listado</legend>
                    </div>
                    <div class="form-group">
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
                  

                    <div class="form-group align-right">
                        <div  id="compFichero">
                            <input type="button" href="javascript:;" onclick="finalizar($('#IdFabricante').val());return false;" value="Comprobar" id="cmp" style="display: none;"/>
                            <span class="alert alert-success">Analizando Errores del fichero. Esto puede tardar unos minutos .....</span>
                        </div>
                        
                        <br/><br/>
                        <div class="col-md-4">

                        </div>
                    </div>
                </form>

                <div id="fin"></div>

            </div>

        </div>
        

    </body>
</html>
