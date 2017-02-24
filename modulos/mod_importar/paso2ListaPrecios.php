<?php 
/*
 * @version     0.1
 * @copyright   Copyright (C) 2017 Catalogo productos Soluciones Vigo.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ricardo Carpintero
 * @Descripcion	Paso 2 importar Lista Precios.
 *  */
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
// Reinicio variables
        $htmloptiones = '';
        $htmlfamilias = '';
        include './../../head.php';
        ?>
        <script src="<?php echo $HostNombre; ?>/modulos/mod_importar/importar.js"></script>
		<script src="<?php echo $HostNombre; ?>/modulos/mod_importar/paso2ListaPrecios.js"></script>
		<script>
			/* =====================  DEFINIMOS VARIABLES GLOBALES DE JAVASCRIPT ===================== */
            // Referencias existentes
            var e = 0;    // Referencias nuevas
            var n = 0;    // linea intermedia
            var b = 0;    // ciclos
            var set;      
            var a;        // linea final
            var f;        // id fabricance
            var rs;       // array de ajax
            var fa; // id familia 
            var nombretabla = "listaprecios"; // Nombre de la tabla 
         </script>
    </head>

    <body>
        <?php
        include './../../header.php';
        ?>
        <div class="container">
            <div class="col-md-12 text-center">
                <h2>Paso 2 - Lista Precios: Seleccionar Familia ,Fabricante y buscar referencia </h2>
            </div>

            <div class="col-md-6">
                <form class="form-horizontal" role="form" action="action_page.php">
                    <div class="form-group">
                        <legend>Seleccion Familia y Fabricante que acabas subir listado</legend>
                    </div>
                    <div class="form-group">
                        <?php
                        // Realizamos consulta de Fabricantes
                        $consultaFabricantes = mysqli_query($BDRecambios, "SELECT `id`,`Nombre` FROM `fabricantes_recambios` ORDER BY `Nombre`");
                        // Ahora montamos htmlopciones
                        while ($fila = $consultaFabricantes->fetch_assoc()) {
                            $htmloptiones.='<option value="' . $fila["id"] . '">' . $fila["Nombre"] . '</option>\n';
                        }
                        $consultaFabricantes->close();
                        ?>
                        <label class="control-label col-md-4">Fabricante</label>
                        <select name="fabricante" id="IdFabricante">
                            <option value="0">Seleccione Fabricante</option>
                            <?php echo $htmloptiones; ?>
                        </select>
                    </div>
                    <div class="form-group">
						<?php // Realizamos consulta de Fabricantes
						// Lo que pretendo es que muestre los padres y dentro de estos los hijos
						$consultaFamilias = mysqli_query($BDRecambios,"SELECT `id`,`id_Padre`,`Familia_es` FROM `familias_recambios` ORDER BY `Familia_es`");
                        // Ahora montamos htmlopciones
                        while ($fila = $consultaFamilias->fetch_assoc()) {
							if ($fila["id_Padre"]== 0){
								$familia = $fila["Familia_es"];
								} else {
								$familia = '--> '.$fila["Familia_es"];
                            }
                            $htmlfamilias.='<option value="'.$fila["id"].'">'.$familia.'</option>';
						}
                        $consultaFamilias->close();
                        ?>
                        <label class="control-label col-md-4">Familia a la que quieres añadir</label>
                        <select name="familia" id="IdFamilia">
                            <option value="0">Seleccione Familia</option>
                            <?php echo $htmlfamilias; ?>
                        </select>
                    </div>

                    <div class="form-group align-right">
                        <input id="BtnComprobar" type="button" href="javascript:;" onclick="ComprobarSeleccionFamFab ($('#IdFabricante').val(), $('#IdFamilia').val());return false;" value="Comprobar"/>
                    </div>
                </form>
                <div id="CjaComprobar" style="display:none;">
                <h3>Resumen de comprobación</h3>
                <p>Total de Registros tabla:<span id="total"></span></p>
                <p>Registros Vacios (ID/Vacios): <span id="vacio"></span></p>
                <p>Recambios Nuevos/Creados: <span id="nuevos"></span>/<span id="creados"></span></p>
                <p>Recambios Existentes/Modificados: <span id="existentes"></span>/<span id="modificados"></span></p>
                <p>Otros Estados ( Mal ) :<span id="otrosEstados"></span></p>
                </div>
                <div id="Paso3" style="display:none;">
					<div class='form-group align-right'>
						<h2>PASO 3</h2>
						<input id="BtnTerminar" type='button' href='javascript:;' onclick='paso3();return false;' value='terminar'/>
					</div>
                <div class="col-md-12">
					<div class="progress" style="margin:0 100px">
						<div id="bar" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
						0 % completado
						</div>
					</div>
                </div>
                
                
                </div>
                
                <div id="resultado"></div>

            </div>

        </div>

       
    </body>
</html>
