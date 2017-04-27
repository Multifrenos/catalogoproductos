<?php
	// Directorio actual de index.php ( del proyecto) debe coincidir con $HostNombre.$RutaServidor
	$DirectorioInicio = getcwd();
	
?>

<!DOCTYPE html>
<html>
<head>
<?php
	include 'head.php';
?>
<script>
            
            // Se ejecuta cuando termina de carga toda la pagina.
            //~ $(document).ready(function () {
				//~ texto = 'Hay un error en importar, en el PASO2 de Referencias curzadas, ya que se bloquea script \n';
				//~ texto = texto + ' tengo revisar que sucede y como lo arreglo';
				//~ alert( texto);
              //~ 
                //~ 
            //~ });
        </script>
</head>
<body>
	<?php 
	include 'header.php';
	
	
	
	?>
	<section>
		<div class="container">
			<div class="col-md-8">
				<h1>Catalogo de recambios</h1>
				<p>En este catalogo se muestra LOS RECAMBIOS AUTOMOVILES que nos ofrecan los distintos distribuidores, fabricantes o marcas. Solo tenemos un precio por marca o fabricante, aunque se entiende que si es un distribuidor, el mismo recambio puede tener varios precios <strong>(de momento no es posible)</strong>.</p>
				<p>Este catalogo tiene definido 3 Bases de Datos (BD), donde cada una tiene sus tablas:</p>
				<ul>
					<li>Base de datos Recambios</li>
					<li>Base de datos Coches</li>
					<li>Base de datos importarRecambios</li>
				</ul>
				<p>La primera BD ( Recambios ) es a importante para la gestion, la otras son complementarias, por ello el tener <strong>copias de seguridad</strong> de esta BD es fundamental.</p>
				<p>Ademas hay que tener en cuenta que los <strong>ID de BD Recambios</strong> es la que relacionamos en web, por lo que <strong>no deben cambiar</strong>, por ello es fundamental tener claro esto.</p>
				<p>La tres BD son distintas , se relacionan entre si, ya muchos datos se sacan unas de las otras, pero tenemos que conseguir que <mark>cada BD sea independiente igualmente</mark>.</p>
				<p>El que sea independientes implica que en la 3 BD debemos guardar los datos necesarios para sea legible por humanos y no puede estar solo numeros (ID). Así podremos operar con datos legibles de forma IDEPENDIENTE</p>
				<h3>Tablas de las BD que utilizamos en este Catalogo</h3>
				<div class="col-md-6">
					<h4>BD de Recambios</h4>
					<p>Esta BD será la que utilicemos para la gestión completa de recambios</p>
					<p>Ver <a href="./estatico/general/BDRecambios.php">más información</a></p>
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
