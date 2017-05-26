// JavaSCRIPT para modulo de recambios.


function copiarAlPortapapeles(id_elemento) {
  //~ document.execCommand("delete")
  document.execCommand("delete");
  console.log('Ver si entra:'+id_elemento);
  //~ var aux;
  var aux = document.getElementById(id_elemento);
  aux.select();
  console.log(aux.value);
  document.execCommand("copy");
};
function copiasIDVirtuemart() {
	// Funcion que utilizamos para copiar en descripcion del producto en virtuemart.
	var id = parseInt($("#IDWeb").val());
	// Ahora montamos el html queremos copiar.
	var ReferenciasCruzadas =document.getElementById("RefCruzadas");
	var DatosRefCruzVersiones = '<div class="col-md-3">' +  ReferenciasCruzadas.innerHTML + '</div>';
	var ReferenciasVersiones = document.getElementById("RefCruVersiones") ;
	var LimpioRefCruVersiones = ReferenciasVersiones.innerHTML;
	var TLetras = LimpioRefCruVersiones.trim().length;
	DatosRefCruzVersiones = DatosRefCruzVersiones +  '<div class="col-md-9">'+ LimpioRefCruVersiones.trim()+ '</div>';
	//~ alert( 'ReferenciasCruzadas:'+ReferenciasCruzadas);
	var parametros = {
		'id': id,
		'DatosRefCruzadas': DatosRefCruzVersiones,
		'TotalLetras': TLetras
	};
	$.ajax({
		data: parametros,
		url: 'funciones.php',
		type: 'post',
		beforeSend: function () {
			$("#resultado").html('Copiando datos referencias cruzadas en id');

		},
	success: function (response) {
			var html= "";
			if (response['RowsAfectados'] === 1 ){
				html = "Ok, copiado correctamente descripcion larga de id:";
			} else {
				html = "Error. NO fue correcta la consulta.";
			} 
			$("#resultado").html(html+id);
			

		}
	});
	
};
