<header>
   <!-- Debería generar un fichero de php que se cargue automaticamente el menu -->
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
	<a class="navbar-brand" href="#">Catalogo</a>
      </div>
      <ul class="nav navbar-nav">
	<li class="active"><a href="<?php echo $HostNombre.'/index.php'?>">Home</a></li>
	<li><a href="./familia1.php">Familia 1</a></li>
	<li><a href="./familia2.html">Familia 2</a></li>
	<li><a href="./familia3.html">Familia 3</a></li>
	<li><a href="<?php echo $HostNombre.'/modulos/mod_buscar/buscar.php';?>">Buscar</a></li>
	<li><a href="<?php echo $HostNombre.'/modulos/mod_importar/Importar.php';?>">Importar</a></li>
      </ul>
    </div>	
  </nav>
  <!-- Fin de menu -->
</header>
