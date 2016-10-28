<!DOCTYPE html>
<html>
    <head>
        <?php
// Reinicio variables
	include './../../head.php';
	include ("./../mod_conexion/conexionBaseDatos.php");    
	include ("./../mod_familias/ObjetoFamilias.php");
	include ("./ObjetoRecambio.php");
	// Creamos objeto familia y leemos familias para mostrar..
	$Dfamilias = new Familias;
	$Familias= $Dfamilias->LeerFamilias($BDRecambios);
	// Ahora creamos array paginas.
	$paginas = array();

	// Estructura:
	// paginas{
	//		actual:
	//		inicio:
	//		ultima:
	//		
	//		next->
	//			[id]
	//		previo->
	//			[id]
	// 			
	$paginas['Actual'] = 1; // por defecto
	$palabraBuscar = ''; // por defecto
	// Obtenemos datos url si los hay...
	if ($_GET) {
		if ($_GET['pagina']) {
			$paginas['Actual'] = $_GET['pagina'];
			$LinkpgActual =  '<a class="PaginaActual"'.$paginas['Actual'].'>'.$paginas['Actual'].'</a>';
		//~ } else {
			//~ $paginas['Actual'] = 1;
		}
	
		if ($_GET['buscar']) {
			$palabraBuscar = $_GET['buscar'];
		} else {
			$palabraBuscar = '';
		}
	}
	// ===================  CONSULTAMOS CUANTOS RECAMBIOS HAY CON LA BUSQUEDA QUE PUSIMOS  =============   //	
	// Creamos objeto Recambio para realizar las consultas..
	$Crecambios = new Recambio;
	if ($palabraBuscar !== '') {
		$filtro =  "WHERE `Descripcion` LIKE '%".$palabraBuscar."%' or RC.RefFabricanteCru LIKE '%".$palabraBuscar."%'";
		//~ echo ' Ver Entro: '.$filtro;
	} else {
	$filtro = '';
	}
	$limite = 40 ; // Esto puede ser variable ...
	// Realizamos consulta para saber cuantos registros tiene y hacer paginación.
	$ContarRecambios = $Crecambios->ConsultaRecambios($BDRecambios,"0","0",$filtro);
	// Obtenemos datos
	$TotalRecambios = $ContarRecambios->num_rows;
	
	// =========       Creamos paginado      ===================  //

	$TotalPaginas = $TotalRecambios / $limite ;
	
	$paginas['Ultima'] = round($TotalPaginas, 0, PHP_ROUND_HALF_UP);   // Redondeo al alza...
	$paginas['inicio'] = 1;
	
	// La variables controlError la utilizao como un debug, no se muestra... Solo si hubiera un error..
	//~ $controlError = 'Obtenemos o creamos Pagina Actual :'.$paginas['Actual']; 

	switch ($paginas['Actual']) {
	    case 1:
		$paginaInicio = $paginas['Actual'];
		$LinkpgInicio = $LinkpgActual;
		break;
	    case $TotalPaginas:
		$paginas['Ultima'] = $paginas['Actual'];
		$LinkpgFinal = $LinkpgActual;
		break;
	}
	//~ $controlError .= ' Redifino pagina actual...:'.$paginas['Actual'];
	
	if ($paginas['Actual'] < $paginas['Ultima']) {
		$difPg= $paginas['Ultima']- $paginas['Actual'];
		if ($difPg > 6 ){
			$difPg = 5; // Su hay mas 5, solo muestra 6
			 
		}
		// Array siguientes
		for ($i = 1; $i <= $difPg; $i++) {
			if ($paginas['Actual']+$i != $paginas['Ultima']) {
				$paginas['next'][$i] = $paginas['Actual']+ $i  ;
			} 
		}
	}
	//~ $controlError .= ' actual...:'.$paginas['Actual'];

	if ($paginas['Actual'] > $paginas['inicio']) {
		$difPg= $paginas['Actual'] - $paginas['inicio'];
		if ($difPg >6 ){
			$difPg = 6; // Recuerda que restamos una entrada, por eso es 5 paginas solo las muestra..
		
		}
		// Array anteriores
		for ($i = 1; $i < $difPg; $i++) {
			if ($difPg == 1) {
				$difp = 2;
			} else {
				$difp = $difPg;

			}
			$paginas['previo'][$i] = $paginas['Actual']-($difp-$i);
		}
	}
	//~ $controlError .= 'Pagina Actual(1):'.$paginas['Actual'];

	// Montamos HTML para mostrar...
	$htmlPG =  '<ul class="pagination">';
	$Linkpg = '<li><a href="./ListaRecambios.php?pagina=';
	// Pagina inicio 
	if (count($paginas['previo'])== 0){
		if ($paginas['Actual'] == $paginas['inicio']){
			$htmlPG = $htmlPG.'<li class="active"><a>'.$paginas['inicio'].'</a></li>';
		} else {
		$htmlPG = $htmlPG.$Linkpg.$paginas['inicio'].'&buscar='.$palabraBuscar.'">'.$paginas['inicio'].'</a></li>';
		}
	} else {
		if ($paginas['inicio']+6 <= $paginas['Actual']) {
		$htmlPG = $htmlPG.$Linkpg.$paginas['inicio'].'&buscar='.$palabraBuscar.'">'."Inicio".'</a></li>';
		$htmlPG = $htmlPG.'<li class="disabled"><a>'.'<<...>>'.'</...></a></li>';

		} else {
		$htmlPG = $htmlPG.$Linkpg.$paginas['inicio'].'">'.$paginas['inicio'].'</a></li>';
		}
		
	}
	//~ $controlError .= 'Pagina Actual(2.1):'.$paginas['Actual'];

	//~ $controlError .= 'Pagina Inicio(2):'.$paginas['inicio'];

	// Paginas anteriores
	foreach ($paginas['previo'] as $pagina) {
		// Si hay valor de busqueda tenemos que meterlo en link.
		
		$htmlPG = $htmlPG.$Linkpg.$pagina.'&buscar='.$palabraBuscar.'">'.$pagina.'</a></li>';
		
	
	}
	// El valor $pagina cuando la pagina actual es 2, es 0 ya que 
	// no tiene previo, la uno es la pagina inicio que ya la mostramos.
	// Por este motivo, el siguiente if para mostrar pagina actual.
	//~ $controlError .= 'Pagina(3):'.$pagina;
	//~ $controlError .= 'Pagina Actual (3):'.$paginas['Actual'];

	if ($pagina > 1 or $paginas['Actual'] == 2){
	// Pagina actual distinta a inicio....
	$htmlPG = $htmlPG.'<li class="active"><a>'.$paginas['Actual'].'</a></li>';
	}
	// Pagina siguientes.
	foreach ($paginas['next'] as $paginaF	) {
		$htmlPG = $htmlPG.$Linkpg.$paginaF.'&buscar='.$palabraBuscar.'">'.$paginaF.'</a></li>';
	}
	//~ $controlError .= '-PaginaF:'.$paginaF;
	// Mostramos ultima pagina, si no se mostro en previo.
	if ($paginaF){
		if ($paginaF + 1 < $paginas['Ultima']){
			$htmlPG = $htmlPG.'<li class="disabled"><a>'.'<<...>>'.'</...></a></li>';
			$htmlPG = $htmlPG.$Linkpg.$paginas['Ultima'].'&buscar='.$palabraBuscar.'">'.'Ultima</a></li>';

		} else{
		$htmlPG = $htmlPG.$Linkpg.$paginas['Ultima'].'&buscar='.$palabraBuscar.'">'.$paginas['Ultima'].'</a></li>';
		}
	}
	$htmlPG = $htmlPG. '</ul>';
	// Mostramos errores
	//~ echo $controlError;
	
	// =========       Fin paginado      ===================  //


	?>
      
	<script>
	// Declaramos variables globales
	var checkID = [];
	var BRecambios ='';
	function VerRecambiosSeleccionado (){
		$(document).ready(function()
		{
			// Array para meter lo id de los checks
			
			// Contamos check están activos.... 
			checkID = [] ; // Reiniciamos varible global.
			var i= 0;
			// Con la funcion each hace bucle todos los que encuentra..
			$(".rowRecambio").each(function(){ 
				i++;
				//todos los que sean de la clase row1
				if($('input[name=checkRec'+i+']').is(':checked')){
					// cant cuenta los que está seleccionado.
					valor = '0';
					valor = $('input[name=checkRec'+i+']').val();
					checkID.push( valor );
					// Ahora tengo hacer array con id...
				}
				
			});
			console.log('ID de Recmabios seleccionado:'+checkID);
			return;
		});
	
	
	}
	
	function BuscarRecambio (){
		$(document).ready(function()
		{
			// Lo ideal sería identificar palabras..
			// de momento solo una palabra..
			NuevoValorBuscar = $('input[name=Buscar').val();
			NuevoValorBuscar = $.trim(NuevoValorBuscar);
			if (NuevoValorBuscar !== ''){
				BRecambios= NuevoValorBuscar;
				console.log('Filtro:'+BRecambios);
			} else {
				alert (' Debes poner algun texto ');
				BRecambios = '';
			}
			return;
		});
	
	
	}
	
	
	
	function metodoClick(pulsado){
	    console.log("Inicimos switch de control pulsar");
	    switch(pulsado) {
			case 'VerRecambio':
				console.log('Entro en VerRecambio');
				// Cargamos variable global ar checkID = [];
				VerRecambiosSeleccionado ();
				if (checkID.length >1) {
				alert ('Solo puedes selecciona \n un recambio para editar');
				return	
				}
				// Ahora redireccionamos 
				// recambi.php?id=id
				window.location.href = './recambio.php?id='+checkID[0];
				
				
				
				
				
				
				break;
			case 'NuevaBusqueda':
				// Obtenemos puesto en input de Buscar
				BuscarRecambio ();
				// Ahora redireccionamos 
				// recambi.php?buscar = buquedaid=id
				if (BRecambios !== ''){
					window.location.href = './ListaRecambios.php?buscar='+BRecambios;
				} else {
					window.location.href = './ListaRecambios.php';	
				}
				console.log('Resultado Buscar:'+BRecambios);
				break;
			default:
				alert('Error no pulsado incorrecto');
			}
	} 
	    
	    
	    

	</script> 
    </head>

    <body>
        <?php
        include './../../header.php';
        ?>
        <div class="container">
            <div class="col-md-12 text-center">
                <h2> Recambios: Editar, Añadir y Borrar Recambios </h2>
                <?php 
				//~ echo 'Numero filas'.$Familias->num_rows.'<br/>';
				//~ echo '<pre class="text-left">';
				//~ print_r($Familias);
				//~ 
				//~ echo '</pre>';
				?>
            </div>
			
			<!--=================  Sidebar -- Menu y filtro =============== -->
			<div class=" col-md-2">
				<?php 
					/*  Al pulsar en cualquiera de estas opciones vamos ejecutar funcion AJAX.
					 * */
				?>
				<h4> Opciones Recambios</h4>
				<ul>
					<li> Añadir</li>
					<li> Modificar</li>
					<li> Borrar</li>
					<li><input type="submit" value="Ver" onclick="metodoClick('VerRecambio');"> </li>
				</ul>
				<h4> Mostrar Familias</h4>
				<form name="FormFamilia">
					<input type="checkbox" name="cktodos" value="all"  onclick="metodoClick()">Todos

					<?php
					foreach ($Familias['items'] as $familia){ 
					    echo '<h5>'. $familia['Nombre'].'</h5>';
					    if ($familia['NumeroHijos'] > 0){
						?>
						<fieldset name="grupoCked" style="display:none;">
						<?php
						foreach ($familia['Hijos'] as $Nieto){
						
						?>
						<input type="checkbox" name="Nieto<?php echo $Nieto['id'];?>" value="">
						<?php echo $Nieto['Nombre'];?>
						
						<br/>
						<?php
						}
						?>
						</fieldset>
						<?php
					    }
					    
					}
				?>
				</form> 
			</div>
			<!--==========  Contenido: Buscador, paginador y lista recambios ========== -->

	<div class="col-md-10">
		<h4>Recambios encontrados:<?php echo $TotalRecambios;?></h4>
                <?php 	// Mostramos paginacion 
                  echo $htmlPG;
				?>
				
			<div class="form-group ClaseBuscar">
				<label>Buscar en descripcion / Referencia Fabricante</label>
				<input type="text" name="Buscar" value="">
				<input type="submit" name="BtnBuscar" value="Buscar" onclick="metodoClick('NuevaBusqueda');">
			</div>
			
                 <!-- TABLA DE PRODUCTOS -->
		<div>
			<table class="table table-striped">
				<thead>
					<tr>
						<th></th>
						<th>ID</th>
						<th>DESCRIPCION</th>
						<th>COSTE</th>
						<th>MARGEN</th>
						<th>PVP</th>
						<th>REF_FABR</th>

					</tr>
				</thead>
	
				<?php 
				// Ahora meto los datos de la consulta.
				$paginasMulti = $paginas['Actual']-1;
				if ($paginasMulti > 0) {
				$desde = ($paginasMulti * $limite)-1; 
				} else {
				$desde = 0;
				}
				// Realizamos consulta 
				if ($palabraBuscar !== '') {
					$filtro =  "WHERE `Descripcion` LIKE '%".$palabraBuscar."%' or RC.RefFabricanteCru LIKE '%".$palabraBuscar."%'";
				} else {
					$filtro = '';
				}
				$recambios  = $Crecambios->ConsultaRecambios($BDRecambios,$limite,$desde,$filtro);
				$recambios  = $Crecambios->ObtenerRecambios($recambios);
				$checkRecam = 0;
				foreach ($recambios['items'] as $recambio){ 
					$checkRecam = $checkRecam + 1; 
				?>

				<tr>
					<td class="rowRecambio"><input type="checkbox" name="checkRec<?php echo $checkRecam;?>" value="<?php echo $recambio['id'];?>">
					</td>
					<td><?php echo $recambio['id']; ?></td>
					<td><?php echo $recambio['Descripcion']; ?></td>
					<td><?php echo $recambio['coste']; ?></td>
					<td><?php echo $recambio['margen']; ?></td>
					<td><?php echo $recambio['pvp']; ?></td>
					<td><?php echo $recambio['RefFabricanteCru'];?></td>
				</tr>

				<?php 
				}
				?>
				
			</table>
		</div>
	</div>
        </div>
    </body>
</html>
