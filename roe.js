function CambioMarcas() {
	if (document.getElementById("myMarca").value != 0){
		alert('Estoy en funcion Marcas y activamos modelo');
		document.getElementById("nodelo").disabled = false;
		// Ahora ejecutamos funcion de modelo.
		AddModelos()
	} else {
		// Eliminamos opciones de y Volvemos a bloquear select de modelo
		EliminarModelos();
		document.getElementById("nodelo").disabled = true;
		
	}
}
function AddModelos() {
	// Antes de nada debemos eliminar registros si tiene.
	EliminarModelos();
	// alert( 'Entramos en funcion de Modelos');
	// Bucle para crear los modelos para marca seleccionada.
	
	for (i=0;i<modelo.length;i++){
	var x = document.getElementById("nodelo");
    var option = document.createElement("option");
    option.text = modelo[i];
    option.value = modeloId[i];
    x.add(option);
   	} 
}
function EliminarModelos() {
	// alert( 'Entramos en funcion de Modelos');
	// Bucle para crear los modelos para marca seleccionada.
	var x = document.getElementById("nodelo");
	// alert( ' Numero elementos' + x.length);
	for (i=0;i<x.length;i++){
	var x = document.getElementById("nodelo");
    x.remove(1);
   	} 
}
function CambioModelos() {
	if (document.getElementById("nodelo").value != 0){
		alert('Acabo de cambiar el modelo y activamos version');
		document.getElementById("versiones").disabled = false;
		// Ahora ejecutamos funcion de añadir version.
		AddVersiones()
	} else {
		// Eliminamos opciones de y Volvemos a bloquear select de modelo
		EliminarVersiones();
		document.getElementById("versiones").disabled = true;
		
	}
}

function AddVersiones() {
	// Antes de nada debemos eliminar registros si tiene.
	EliminarModelos();
	// alert( 'Entramos en funcion de Modelos');
	// Bucle para crear los modelos para marca seleccionada.
	
	for (i=0;i<modelo.length;i++){
	var x = document.getElementById("versiones");
    var option = document.createElement("option");
    option.text = modelo[i];
    option.value = modeloId[i];
    x.add(option);
   	} 
}
function EliminarVersiones() {
	// alert( 'Entramos en funcion de Modelos');
	// Bucle para crear los modelos para marca seleccionada.
	var x = document.getElementById("versiones");
	// alert( ' Numero elementos' + x.length);
	for (i=0;i<x.length;i++){
	var x = document.getElementById("versiones");
    x.remove(1);
   	} 
}

function BarraProceso(lineaA,lineaF) {
	// Si la lineaActual es 0 , genera in error por lo que debemos sustituirlo por uno
	//~ alert(' Linea final es '+lineaF);
	if (lineaA == 0 ) {
		lineaA = 1;
	}
	if (lineaF == 0) {
	 alert( 'Linea Final es 0 ');
	 return;
	}
	var progreso =  Math.round(( lineaA *100 )/lineaF);
	//~ alert('Dentro BarraProceso \n Valor Progreso: '+ progreso + 'Linea Actual: '+ lineaA);

	//~ var progreso = (lineaA*100)/lineaF;
	  // Aumento en 10 el progeso
	  $('#bar').css('width', progreso + '%');

	 //  Añadimos numero linea en resultado.
	 //~ document.getElementById("resultado").innerHTML = lineaA;  // Agrego nueva linea antes 
	// Ahora debería en que porcentaje va , sustituyendo id = "spanproceso"
	// pero no funciona
	 document.getElementById("bar").innerHTML = progreso + '%';  // Agrego nueva linea antes 


	  return;
		
}
