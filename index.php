<!DOCTYPE html>
<html>
<head>
<?php
	include 'head.php';
?>
</head>
<body>
	<?php 
	include 'header.php';
	?>
	<section>
		<div class="container">
			<div class="col-md-8">
				<h1>Catalogo de recambios</h1>
				<p>En este catalogo vamos mostrar por familias LOS RECAMBIOS AUTOMOVILES que nos ofrecan los distintos distribuidores, fabricantes o marcas. De momento solo podemos tener un precio por marca o fabricante, aunque se entiende que si es un distribuidor, este puede tener un precio coste distinto.</p>
				<p>Este catalogo tiene definido 3 Bases de Datos (BD), donde cada una tiene sus tablas:</p>
				<ul>
					<li>Base de datos Recambios</li>
					<li>Base de datos Coches</li>
					<li>Base de datos importarRecambios</li>
				</ul>
				<p>Aunque son BD distintas , se relacionan entre si, ya muchos datos se sacan unas de las otras, pero tenemos que conseguir que <mark>cada BD sea independiente igualmente</mark>, para ello debemos guardar los datos necesarios en BD para que los campos y registros muestren la información que buscamos y no un simple ID que no se reconocido si no existe la otra de las otras BD.</p>
				<h3>Tablas de las BD que utilizamos en este Catalogo</h3>
				<div class="col-md-6">
					<h4>BD de Recambios</h4>
					<p>Esta BD será la que utilicemos para la gestión completa de recambios</p>
					<p>Ver <a href="./estatico/BDRecambios.php">más información</a></p>
				</div>
				<div class="col-md-6">
					<h4>BD de Coches</h4>
					<p>La utilizamos para registrar los distintos coches con sus marcas, modelo, versiones y acabados.</p>

					
				</div>
			</div>
			<div class="col-md-4">
				<div>
				<h2>Información funcionamiento</h2>
				<p>Las funcionalidades que vamos hacer son: </p>
				<ul>
				<li> - Buscar recambios por marca, modelos y versiones </li>
				<li> - Buscar recambios por referencias de fabricantes de recambios</li>
				<li> - Mostrar recambios por familias de productos.</li>
				</ul>
				</div>
				<div>
				<h2>Requesitos mínimos</h2>
				<ul>
				<li>Servidor Apache.</li>
				<li>PHP y MySql</li>
				<li>Framework Bootstrap</li>
				</ul>
				<p>Esta aplicación no tiene necesidad conexion a internet</p>
				</div>
				<div>
					<div class="alert alert-info">
					<p>Está aplicación es OPEN SOURCE, con ello queremos decir que puedes utilizar este código en otras aplicaciones y modificarlo sin problemas.</p>
					</div>
					<div class="alert alert-danger">
					<p>Lo que no se puede es publicar son los datos de la base de datos, ya que no tenemos autorizacio de los fabricantes de recambios.</p>
					</div>
				</div>
			</div>
			
		</div>
	</section>
</body>
</html>
