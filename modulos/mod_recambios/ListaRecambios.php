<!DOCTYPE html>
<html>
    <head>
        <?php
// Reinicio variables
	include './../../head.php';
	
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
		}
	
		if ($_GET['buscar']) {
			$palabraBuscar = $_GET['buscar'];
		} else {
			$palabraBuscar = '';
		}
	}
	
	
	// ===================  CONSULTAMOS CUANTOS RECAMBIOS HAY CON LA BUSQUEDA QUE PUSIMOS  =============   //	
	// Creamos objeto Recambio para realizar las consultas especificas..
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
	//~ $paginas['Ultima'] = round($TotalPaginas,0,PHP_ROUND_HALF_UP);   // Redondeo al alza...
	$paginas['Ultima'] = (int) $TotalPaginas;
	if ($paginas['Ultima'] < $TotalPaginas) {
		$paginas['Ultima'] = $paginas['Ultima'] +1;
	}
	$paginas['inicio'] = 1;

	// La variables controlError la utilizao como un debug, no se muestra... Solo si hubiera un error..
	//~ $controlError = 'Obtenemos o creamos Pagina Actual :'.$paginas['Actual']; 

	switch ($paginas['Actual']) {
	    case 1:
		$paginaInicio = $paginas['Actual'];
		break;
	    case $TotalPaginas:
		$paginas['Ultima'] = $paginas['Actual'];
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
	</script> 
    <!-- Cargamos fuciones de modulo. -->
   	<script src="<?php echo $HostNombre; ?>/modulos/mod_recambios/funciones.js"></script>

    
    <!-- Cargamos libreria control de teclado -->
	<script src="<?php echo $HostNombre; ?>/lib/shortcut.js"></script>
  
	
	<script>
	// Funciones para atajo de teclado.
	shortcut.add("Shift+V",function() {
		// Atajo de teclado para ver
		metodoClick('VerRecambio');
	});    
	    
	</script> 
    </head>

<body>
        <?php
        include './../../header.php';
        ?>
       
	<div class="container">
		<div class="row">
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
	        <!--=================  Sidebar -- Menu y filtro =============== 
				Efecto de que permanezca fixo con Scroll , el problema es en
				movil
	        -->
			<nav class="col-sm-2" id="myScrollspy">
				<div data-spy="affix" data-offset-top="505">
				<h4> Recambios</h4>
				<h5> Opciones para una selección</h5>
				<ul class="nav nav-pills nav-stacked">
					<li><a href="#section1">Añadir</a></li>
					<li><a href="#section2">Modificar</a></li>
					<li><a href="#section3">Borrar</a></li>
					<li><a href="#section3" onclick="metodoClick('VerRecambio');";>Ver</a></li>

				</ul>
				</div>
			
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
			</nav>
			
				<!--==========  Contenido: Buscador, paginador y lista recambios ========== -->
			<div class="col-md-10">
					<p>
					 -Recambios encontrados BD local filtrados:
					 <?php echo $TotalRecambios;?>
					 </p>
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
						<th>idWEB</th>

					</tr>
				</thead>
	
				<?php 
				// Ahora meto los datos de la consulta.
				$paginasMulti = $paginas['Actual']-1;
				if ($paginasMulti > 0) {
				$desde = ($paginasMulti * $limite); 
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
					<td>
						<?php
						if (isset ( $recambio['IDWeb']) ) {
						 echo $recambio['IDWeb'];
						}
						 ?>
					</td>

				</tr>

				<?php 
				}
				?>
				
			</table>
			</div>
		</div>
	</div>
    </div>
		
</body>
</html>
