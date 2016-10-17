<!DOCTYPE html>
<html>
    <head>
        <?php
// Reinicio variables
        include './../../head.php';
        include ("./../mod_conexion/conexionBaseDatos.php");
        
	include ("./../mod_familias/ObjetoFamilias.php");
	include ("./ObjetoRecambio.php");
        $Dfamilias = new Familias;
	$Familias= $Dfamilias->LeerFamilias($BDRecambios);
	
	// Consultamos cuantos registros hay en Recambios:
	$Crecambios = new Recambio;
	$consulta = "SELECT * FROM `recambios`";
	$ResRecambios = $Crecambios->ConsultaRecambios($BDRecambios,$consulta);
		$TotalRecambios = $ResRecambios->num_rows;
		$TotalPaginas = $TotalRecambios / 40 ;
		$TotalPaginas = round($TotalPaginas, 0, PHP_ROUND_HALF_UP);   // Redondeo al alza...
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
				//~ echo '<pre class="text-left">';
				//~ print_r($Familias);
				//~ 
				//~ echo '</pre>';
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
					<?php
					foreach ($Familias['items'] as $familia){ 
					    echo '<h5>'. $familia['Nombre'].'</h5>';
					    if ($familia['NumeroHijos'] > 0){
						foreach ($familia['Hijos'] as $Nieto){
						
						?>
						<input type="checkbox" name="Nieto<?php echo $Nieto['id'];?>" value="">
						<?php echo $Nieto['Nombre'];?>
						
						<br/>
						<?php
						}
					    }
					    
					}
				?>
				</form> 
			</div>
            <div class="col-md-10">
				<h4>Recambios encontrados</h4>
                <?php 	echo 'Recambios encontrados:'.$TotalRecambios.'<br/>';
                		echo 'Pagina: 1 - '.$TotalPaginas;
				?>
		<form class="form-horizontal" role="form">
                    <div class="form-group">
					<label>Buscar</label>
					<input class="control-label col-md-6" type="text" name="Buscar" value="">
                    </div>
                    <div class="form-group">
                        <legend>Listado de Recambios</legend>
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
			
			$recambios  = $Crecambios->ObtenerRecambios($BDRecambios);
			    //~ echo '<pre>';
			    //~ var_dump($recambios);
			    //~ echo '</pre>';
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
