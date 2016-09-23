<!DOCTYPE html>
<html>
<head>
<?php
// Reinicio variables
	$htmloptiones ='';
	$htmlfamilias = '';
	include './../../head.php';
	include ("./../mod_conexion/conexionBaseDatos.php");

?>
    <script src="<?php echo $HostNombre;?>/modulos/mod_importar/comprobar.js"></script>
</head>

<body>
	<?php 
	include './../../header.php';
	?>
	<div class="container">
		<div class="col-md-12 text-center">
			<h2>Paso 2 - Lista Precios: Seleccionar Familia ,Fabricante y buscar referencia </h2>
		</div>
		
		<div class="col-md-6">
			<form class="form-horizontal" role="form" action="action_page.php">
				<div class="form-group">
				<legend>Seleccion Familia y Fabricante que acabas subir listado</legend>
				</div>
				<div class="form-group">
				<?php // Realizamos consulta de Fabricantes
				$consultaFabricantes = mysqli_query($BDRecambios,"SELECT `id`,`Nombre` FROM `FabricantesRecambios` ORDER BY `Nombre`");
				// Ahora montamos htmlopciones
				while ($fila = $consultaFabricantes->fetch_assoc()) {
					$htmloptiones.='<option value="'.$fila["id"].'">'.$fila["Nombre"].'</option>';
				}
				$consultaFabricantes->close();
				?>
				<label class="control-label col-md-4">Fabricante</label>
				<select name="fabricante" id="IdFabricante">
					<option value="0">Seleccione Fabricante</option>
					<?php echo $htmloptiones;?>
                </select>
				</div>
				<div class="form-group">
				<?php // Realizamos consulta de Fabricantes
				$consultaFamilias = mysqli_query($BDRecambios,"SELECT `id`,`Familia_es` FROM `FamiliasRecambios` ORDER BY `Familia_es`");
				// Ahora montamos htmlopciones
				while ($fila = $consultaFamilias->fetch_assoc()) {
					$htmlfamilias.='<option value="'.$fila["id"].'">'.$fila["Familia_es"].'</option>';
				}
				$consultaFamilias->close();
				?>
				<label class="control-label col-md-4">Familia a la que quieres añadir</label>
				<select name="familia" id="IdFamilia">
					<option value="0">Seleccione Familia</option>
					<?php echo $htmlfamilias;?>
                </select>
				</div>
				
				<div class="form-group align-right">
				<input type="button" href="javascript:;" onclick="ComprobarPaso2ListaPrecios($('#IdFabricante').val(), $('#IdFamilia').val());return false;" value="Comprobar"/>
				</div>
			</form>
		<h3>Resumen de comprobación</h3>
                <p>Numero de Registros analizados: <span id="total"></span></p>
		<p>Numero de Recambios Nuevos: <span id="nuevos"></span></p>
		<p>Numero de Recambios Existentes: <span id="existentes"></span></p>
                <div id="fin"></div>
		</div>
		
	</div>
    <script>
        var e=0;
        var n=0;
    var b = 0;    
    var set;
    var a;
    var f;
    var rs;
    var fa;
    function ComprobarPaso2ListaPrecios(fabricante,familia){
        fa=familia;
        var nombretabla = "listaprecios";
                var parametros = {
                    'nombretabla' : nombretabla,
                    'pulsado' : 'contarVacios'
                };
                $.ajax({
                    data: parametros,
                    url: 'funciones.php',
                    type: 'post',
                    beforeSend: function () {
                        $("#resultado").html("Procesando, espere por favor...");
                    },
                    success: function (response) {
                        if(response == null){
    alert("no hay ficheros que modificar");
    var campo="<div class='form-group align-right'><input type='button' href='javascript:;' onclick='paso3();return false;' value='terminar'/></div>"
           $("#fin").append(campo);
}else{
    a=response.length;
                       ciclo(response,fabricante);
                   }

                    }
                });
                 
    }
  
   function consulta(){
       
       if(b< a){
           var nombretabla = "listaprecios";
                var parametros = {
                    'nombretabla' : nombretabla,
                    'pulsado' : 'comprobar',
                    'idrecambio': rs[b].id,
                    'linea': rs[b].linea,
                    'fabricante': f
                };
                $.ajax({
                    data: parametros,
                    url: 'funciones.php',
                    type: 'post',
                    datatype: 'json',
                    beforeSend: function () {
                        $("#resultado").html("Procesando, espere por favor...");
                    },
                    success: function (response) {
                       
                        n=n+response[0].n;
                        e=e+response[0].e;
                       
                       $("#total").html(response[0].t);
                       $("#nuevos").html(n);
                       $("#existentes").html(e);
                        b++;
                      
                    }
                });
           
           
       }else{
        
           clearInterval(set);
       }
        var campo="<div class='form-group align-right'><input type='button' value='terminar'/></div>";
           $("#fin").append(campo);
    }
    function ciclo(response,fabricante){
        rs=response;
        f=fabricante;
        
       
    set = setInterval("consulta()",2000);
      

    
    }
    function paso3(){
        console.log("Fabricante ="+f);
        console.log("Familia ="+fa);
    }
    
    </script>
</body>
</html>
