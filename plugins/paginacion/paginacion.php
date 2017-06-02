<?php 
// =========       inicio de Paginado     ===================  //
// Funcion para crear array de paginacion.
// Explicacion de los datos que necesitamos:
// $PagActual
// $CantidadRegistros
// $LimitePagina  // Ya a tengo creada;
// $LinkBase Donde estamos...Esto no lo mandamos de momento...
// $OtrosParametros  de momento no lo utilizo
// El objetivo:
// Crear un array que traiga la enumeración de las paginas y sus links correspondiente.
// Mostrando como mucho 5 pag. al inicio y 5 pagina al final.
// Y en el medio 5 paginas.
//
// Esta funcion se ejecuta despues de la carga siempre, ya que debe saber en que pagina esta.
// Estructura array de paginas:
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
function paginado ($PagActual,$CantidadRegistros,$LimitePagina,$LinkBase,$OtrosPametros) {

	if ($CantidadRegistros > $LimitePagina ) {
	// Si hay mas 50 , realizamos paginación.
			$TotalPaginas = $CantidadRegistros / $LimitePagina;
	}
	
	// Ahora creamos array paginas.
	$paginas = array();
	
	$paginas['Actual'] = $PagActual; 
	$paginas['Ultima'] = round($TotalPaginas, 0, PHP_ROUND_HALF_UP);   // Redondeo al alza...
	$paginas['inicio'] = 1;
	
	switch ($paginas['Actual']) {
		case 1:
		$paginaInicio = $paginas['Actual'];
		break;
		case $TotalPaginas:
		$paginas['Ultima'] = $paginas['Actual'];
		break;
	}
	
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
	$Linkpg = '<li><a href="'.$LinkBase.'pagina=';
	// Pagina inicio 
	if (count($paginas['previo'])== 0){
		if ($paginas['Actual'] == $paginas['inicio']){
			$htmlPG = $htmlPG.'<li class="active"><a>'.$paginas['inicio'].'</a></li>';
		} else {
		$htmlPG = $htmlPG.$Linkpg.$paginas['inicio'].$OtrosParametros.'">'.$paginas['inicio'].'</a></li>';
		}
	} else {
		if ($paginas['inicio']+6 <= $paginas['Actual']) {
		$htmlPG = $htmlPG.$Linkpg.$paginas['inicio'].$OtrosParametros.'">'."Inicio".'</a></li>';
		$htmlPG = $htmlPG.'<li class="disabled"><a>'.'<<...>>'.'</...></a></li>';

		} else {
		$htmlPG = $htmlPG.$Linkpg.$paginas['inicio'].$OtrosParametros.'">'.$paginas['inicio'].'</a></li>';
		}
		
	}


	// Paginas anteriores
	foreach ($paginas['previo'] as $pagina) {
		// Si hay valor de busqueda tenemos que meterlo en link.
		
		$htmlPG = $htmlPG.$Linkpg.$pagina.$OtrosParametros.'">'.$pagina.'</a></li>';
		
	
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
		$htmlPG = $htmlPG.$Linkpg.$paginaF.$OtrosParametros.'">'.$paginaF.'</a></li>';
	}
	//~ $controlError .= '-PaginaF:'.$paginaF;
	// Mostramos ultima pagina, si no se mostro en previo.
	if ($paginaF){
		if ($paginaF + 1 < $paginas['Ultima']){
			$htmlPG = $htmlPG.'<li class="disabled"><a>'.'<<...>>'.'</...></a></li>';
			$htmlPG = $htmlPG.$Linkpg.$paginas['Ultima'].$OtrosParametros.'">'.'Ultima</a></li>';

		} else{
		$htmlPG = $htmlPG.$Linkpg.$paginas['Ultima'].$OtrosParametros.'">'.$paginas['Ultima'].'</a></li>';
		}
	}
	$htmlPG = $htmlPG. '</ul>';
	// Mostramos errores
	//~ echo $controlError;
	
	// =========       Fin paginado      ===================  //
}
?>
