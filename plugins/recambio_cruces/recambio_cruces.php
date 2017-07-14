<?php
// Funcion para mostrar html de referencias cruzadas de recambios.
// Parametros que necesitamos.
// Array CruceRecambios
class plRecambioCruces {
	function html_cruce_ref ($CruceRecambio){
		$html = "<h2> Referencias cruzadas</h2>"
				."<p>"
				.$CruceRecambio['TotalCruce']." referencias otros fabricantes.</p>";
		for ($i = 0; $i < $CruceRecambio['TotalCruce']; $i++) {
			$html .= '<a title="Id Referencia Cruzada:'.$CruceRecambio[$i]['idReferenciaCruz']
					.'"><span class=" glyphicon glyphicon-info-sign"></span></a>'
					.$CruceRecambio[$i]['FabricanteCruRef'].' '
					.'<a title="Id Fabricante Recambio:'
					.$CruceRecambio[$i]['idFabriCruz'].'"><span class=" glyphicon glyphicon-wrench"></span></a>'
					.$CruceRecambio[$i]['FabricanteCru'].'<br/>';
		}
		return $html;
	}
	function html_cruce_vehiculo( $CrucesVehiculos,$TotalCrucesVehiculos){
		$Idmarca= 0 ;
		$Idmodelo = 0;
		$html= 	'<h2>Cruce de Vehiculos</h2>'
				.'Numero de vehiculos que montan este recambio: '.$TotalCrucesVehiculos;
		if ($CrucesVehiculos){
			foreach ( $CrucesVehiculos as $vehiculo) {
			// Lo primero ver si cambia marca o no.
				if ($Idmarca <> $vehiculo['idMarca']){
				// Antes de nada cerrar table si estuviera abierto, 
					if ($Idmarca<>0) {
						// Cerramos table
						$html .= '</tbody></table>';
					}
					$Idmarca= $vehiculo['idMarca'];
					$html.=	'<h3><a title="Id de Marca:'.$vehiculo['idMarca'].'">'.$vehiculo['Nmarca']."</a></h3>";
					$html.=	'<table class="table table-striped">'
							.'<thead>'
							.'<tr>'
							.'<th>Modelo <br/>    Version</th>'
							.'<th>Fecha Inicial</th>'
							.'<th>Fecha Final</th>'
							.'<th>Combustible</th>'
							.'<th>Potencia</th>'
							.'<th>Numero<br/>cilindros</th>'
							.'<th>Cm3</th>'
							.'</tr>'
							.'</thead>'
							.'<tbody>';
				}
				$html.='<tr>';
				if ( $Idmodelo <> $vehiculo['idModelo']){
					// Validacion de string
					$validato = strpos($vehiculo['Nmodelo'],"'");
					if ($validato === false){
						$textModelo = $vehiculo['Nmodelo'];
					} else {
						// ahora validato, indica posicion donde encontro error.
						$textModelo= str_replace("'"," ",$vehiculo['Nmodelo']);
						//~ $textModelo = 'Error '.$vehiculo['Nmodelo'];
					}
					$html.=	'<th>Modelo:'.$textModelo.'</th>'
							.'</tr>';
					$Idmodelo = $vehiculo['idModelo'];
				}
				$html .='<td>'
						.'<a title="Id de Version:'.$vehiculo['id'].'">'.$vehiculo['Nversion'].'</a>'
						.'</td>'
						.'<td>';
				if ($vehiculo['fecha_inicial'] != '0000-00-00'){ 
					$html .= $vehiculo['fecha_inicial'];
				}
				$html .='</td>'
						.'<td>';
				if ($vehiculo['fecha_final'] != '0000-00-00'){ 
					$html.=$vehiculo['fecha_final'];
				}
				$html.='</td>'
						.'<td>'.$vehiculo['Ncombustible'].'</td>'
						.'<td>'.$vehiculo['cv'].'cv/'.$vehiculo['kw'].'kw</td>'
						.'<td>'.$vehiculo['ncilindros'].'</td>'
						.'<td>'.$vehiculo['cm3'].'cm3'.'</td>'
						.'</tr>';
				}
				// Cerramos tablas que esta abierta fijo...
				$html.='</tbody></table>';
		return $html;
		} else {
			// Quiere decir que no hay cruces.
			$html= 	'<h2>Cruce de Vehiculos</h2>'
					.' <p>No existen cruces de vehiculos para este recambio.( Sin actualizar)</p>';
		}
		
	}
}
