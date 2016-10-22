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
	// Creamos objeto Recambio para realizar las consultas..
	$Crecambios = new Recambio;
	// Realizamos consulta para saber cuantos registros tiene y hacer paginación.
	// Ahora creamos array paginas.
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
    $paginas = array();
	
	
	$limite = 40 ; // Esto puede ser variable ...
	$ContarRecambios = $Crecambios->ConsultaRecambios($BDRecambios,"0","0");
	$TotalRecambios = $ContarRecambios->num_rows;
	$TotalPaginas = $TotalRecambios / $limite ;
	
	$paginas['Ultima'] = round($TotalPaginas, 0, PHP_ROUND_HALF_UP);   // Redondeo al alza...
	$paginas['inicio'] = 1;
	// =========       Creamos paginado      ===================  //
	if ($_GET[pagina]) {
	$paginas['Actual'] = $_GET[pagina];
	echo 'Definimos pagina actual como :'.$paginas['Actual'];
	$LinkpgActual =  '<a class="PaginaActual"'.$paginas['Actual'].'>'.$paginas['Actual'].'</a>';
	} else {
	// Si NO paremetro es la primera.
	$paginas['Actual'] = 1;
	$LinkpgActual = '<a href="./ListaRecambios.php?pagina=1">1</a>';
	}
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
    echo ' Redifino pagina actual...:'.$paginas['Actual'];
    
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
	// Montamos HTML para mostrar...
	$htmlPG =  '<ul class="pagination">';
    $Linkpg = '<li><a href="./ListaRecambios.php?pagina=';
	// Pagina inicio 
	if (count($paginas['previo'])==0){
		if ($paginas['Actual'] = $paginas['inicio']){
			$htmlPG = $htmlPG.'<li class="active"><a>'.$paginas['inicio'].'</a></li>';
		} else {
		$htmlPG = $htmlPG.$Linkpg.$paginas['inicio'].'">'.$paginas['inicio'].'</a></li>';
		}
	} else {
		if ($paginas['inicio']+6 <= $paginas['Actual']) {
		$htmlPG = $htmlPG.$Linkpg.$paginas['inicio'].'">'."Inicio".'</a></li>';
		$htmlPG = $htmlPG.'<li class="disabled"><a>'.'<<...>>'.'</...></a></li>';

		} else {
		$htmlPG = $htmlPG.$Linkpg.$paginas['inicio'].'">'.$paginas['inicio'].'</a></li>';
		}
		
	}
	// Paginas anteriores
	foreach ($paginas['previo'] as $pagina	) {
		$htmlPG = $htmlPG.$Linkpg.$pagina.'">'.$pagina.'</a></li>';
		
	
	}
	// Hay que tener en cuenta que cuando la pagina actual es 2, no tiene previo
	// ya que la pagina inicio la anterior.
	// Por este motivo los controlamos aqui.
	echo 'Pagina Actual:'.$paginas['Actual'];
	if ($pagina > 1 or $paginas['Actual'] == 2){
	// Pagina actual distinta a inicio....
	echo 'entro'; 
	$htmlPG = $htmlPG.'<li class="active"><a>'.$paginas['Actual'].'</a></li>';
	}
	// Pagina siguientes.
	foreach ($paginas['next'] as $pagina	) {
		$htmlPG = $htmlPG.$Linkpg.$pagina.'">'.$pagina.'</a></li>';
	}
	if ($paginas['Actual']+5 < $pagina['Ultima']){
		$htmlPG = $htmlPG.'<li class="disabled"><a>'.'<<...>>'.'</...></a></li>';
	}
	if ($paginas['Actual'] != $paginas['Ultima']){
	$htmlPG = $htmlPG.$Linkpg.$paginas['Ultima'].'">'.$paginas['Ultima'].'</a></li>';
	}
	
	
	
	

	$htmlPG = $htmlPG. '</ul>';
	?>
      
         ?>
	<script>
	//~ function alertaChecked(){
	    //~ alert(document.miFormulario.cktodos.checked)
	//~ }
	//~ function alertaValue(){
	    //~ alert(document.miFormulario.cktodos.value)
	//~ }
	function metodoClick(){
	    console.log("Inicimos click cktodos");
	    if (document.miFormulario.cktodos.checked = true){
		alert(" ahora debería activar");
		//~ document.miFormulario.cktodos.checked = false;
		} else {
		alert(" Deberia desactivar");
		//~ document.miFormulario.cktodos.checked = true;
		document.grupoCked.style.display='block';
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
				<h4> Opciones Recambios</h4>
				<ul>
					<li> Añadir</li>
					<li> Modificar</li>
					<li> Borrar</li>
					<li> Ver </li>
				</ul>
				<h4> Mostrar Familias</h4>
				<form name="miFormulario">
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
				<h4>Recambios encontrados</h4>
                <?php 	
						echo 'Recambios encontrados:'.$TotalRecambios.'<br/>';
                		echo $htmlPG;
						
				?>
						
				<form class="form-horizontal" role="form">
                    <div class="form-group">
					<label>Buscar</label>
					<input class="control-label col-md-6" type="text" name="Buscar" value="">
                    </div>
                   
                 </form>
                 <!-- TABLA DE PRODUCTOS -->
				<div>
					<table class="table table-striped">
						<thead>
							<tr>
								<th>ID</th>
								<th>DESCRIPCION</th>
								<th>COSTE</th>
								<th>MARGEN</th>
								<th>PVP</th>
								<th>IDFABR</th>

							</tr>
						</thead>
			
						<?php 
						// Ahora meto los datos de la consulta.
						$desde = ($paginaActual * 40)-1; 
						$recambios  = $Crecambios->ConsultaRecambios($BDRecambios,$limite,$desde);
						$recambios  = $Crecambios->ObtenerRecambios($recambios);
						foreach ($recambios['items'] as $recambio){ 
						?>

						<tr>
							<td><?php echo $recambio['id']; ?></td>
							<td><?php echo $recambio['Descripcion']; ?></td>
							<td><?php echo $recambio['coste']; ?></td>
							<td><?php echo $recambio['margen']; ?></td>
							<td><?php echo $recambio['pvp']; ?></td>
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
