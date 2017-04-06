<!DOCTYPE html>
<html>
<head>
<?php
	include './../../head.php';
?>
</head>
<body>
<?php 
	include './../../header.php';
?>

<div class="container">
	<div class="col-md-12">
		<h2>Como hacer preparar los csv.</h2>
		<p> Lo primero que debemos saber es:</p>
		<ol>
			<li>El separador campos es (,)</li>
			<li>El divisor campos (").</li>
			<li>Lo numeros decimales se utiliza el sistema Ingles(americano)-> 111.99</li>
		</ol>
		<div>
			<h2>Preguntas frecuentes</h2>
			<div class="pregunta1">
				<h5>
					<?php 
					/* Para que este expandido, lo hace con date aria-expanded ="false" o "true"
					 * */
					?>
					<a data-toggle="collapse" data-parent="#accordion" href="#pregunta1" aria-expanded="false">¿Hace falta que la linea cabecera nombre de columnas?
					<span style="float:left;" class="icono-collapse">+</span>
					</a>
					
				</h5>
				<div id="pregunta1" class="collapse pregunta1">
					<p>De momento no hace falta, sería ideal que creara la tabla temporal con esos nombres, pero de momento NO. </p>
					
					
				</div>
			</div>
			<div class="pregunta2">
				<h5>
					<?php 
					/* Para que este expandido, lo hace con date aria-expanded ="false" o "true"
					 * */
					?>
					<a data-toggle="collapse" data-parent="#accordion" href="#pregunta2" aria-expanded="false">¿Columnas necesarios para ReferenciasCversionesCoches.csv?
					<span style="float:left;" class="icono-collapse">+</span>
					</a>
					
				</h5>
				<div id="pregunta2" class="collapse pregunta2">
					<div>
					<p>Las columnas necesarias para <strong>ReferenciasCVersiones</strong> son las siguientes:</p>
					<ul>
					<li><strong>RefProveedor:</strong> Es la referencia del producto que utiliza el proveedor que nos facilita el fichero</li>
					</ul>
					</div>
					
				</div>
			</div>
		</div>
	</div>	

</div>

</body>
</html>
