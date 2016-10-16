<!DOCTYPE html>
<html>
    <head>
        <?php
// Reinicio variables
        include './../../head.php';
        include ("./../mod_conexion/conexionBaseDatos.php");
        include ("./../mod_familias/ObjetoFamilias.php");
        $Dfamilias = new Familias;
		$Familias= $Dfamilias->LeerFamilias($BDRecambios);
        ?>
      
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
				echo '<pre class="text-left">';
				print_r($Familias);
				echo '</pre>';
				?>
            </div>
			<div class=" col-md-2">
				<h4> Opciones Recambios</h4>
				<ul>
					<li> Añadir</li>
					<li> Modificar</li>
					<li> Borrar</li>
					<li> Ver </li>
				</ul>
				<h4> Mostrar Familias</h4>
				
				<form>
					<input type="checkbox" name="Familia1" value="IDF1">Filtros <br/>
					|__-><input type="checkbox" name="Familia2" value="IDF16">Filtros de Aceite <br/>
				<br><br>
				</form> 
			</div>
            <div class="col-md-10">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
					<label>Buscar</label>
					<input class="control-label col-md-6" type="text" name="Buscar" value="">
                    </div>
                    <div class="form-group">
                        <legend>Listado de Recambios</legend>
                    </div>
                 </form>
                 <!-- Resultado de busqueda -->
                 
             </div>
        </div>
    </body>
</html>
