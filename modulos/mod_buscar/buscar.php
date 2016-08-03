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
	<div class="row">
	<div class="col-md-12">
			<h2 class="text-center">Busqueda de Recambios</h2>
		<p class="text-center">Podras buscar el recambio por modelo de coche, por referencia de DKM o por referencia de pieza</p>
		<div class="col-md-4">
		<h3>Vehículo / version de automovil</h3>
		<p>Selecione el vehículo del que quieres conocer los recambios que tenemos para ese modelo.</p>
		<p>Lista todos los recambios que tenemos en base datos para ese vehículo</p>
			<div id="SeleccionarVersion">
				<form>
					<!-- Presentacion de marca -->
					<div class="marca">
						<label class="marca">Marca</label>
						<!-- Cargamos select con marcas -->
						<select name="myMarca" id="myMarca" onchange="DespuesAjax()">
							<option value="0">Seleccione una marca</option>
							<option value="1">Renault</option>
							<option value="2">Citroen</option>
						</select>
						<input id="CheckMarca" type="checkbox" name="vehicle" value="Sin Seleccionar" onchange="CambioMarcas()"> Selecciono Marca<br>
					</div>
					<!-- Presentacion de modelo -->
					<div class="nodelo">
						<label class="nodelo">Modelo</label>
									<!-- Cargamos select con marcas -->
						<select disabled name="Minodelo" id="nodelo" onchange="CambioModelos()">
							<option value="0">Seleccione una modelo</option>
						</select>
					</div>
					<!-- Presentacion de version -->
					<div class="versiones">
						<label class="versiones">Versiones</label>
									<!-- Cargamos select con marcas -->
						<select disabled name="MiVersiones" id="versiones">
							<option value="0">Seleccione una modelo</option>
						</select>
					</div>
					<div class="enviar">
						<input type="submit">
					</div>
				</form>
			</div>
		</div>

			<!-- Formulario de busqueda por Referencia DKM -->
			<div class="col-md-4">
				<h3> Por Referencia DKM </h3>
				<p> Busca todos los modelos de coches que usan ese recambio DKM y ademas busca las referencias de otros fabricantes de recambios.</p>
				<form>
				<label class="refDKM">Referencia DKM</label>
				<input class="control-label col-md-6" type="text" id="RefDKM" name="Ref_DKM" value="">

				</form>
			
			</div>
			<div class="col-md-4">
				<h3> Referencia de otros fabricantes </h3>
				<p> Busca la referencia de otros fabricantes y nos indica la referencia DKM.</p>
				<form>
				<label class="refOtros">Referencia Otros</label>
				<input class="control-label col-md-6" type="text" id="RefDKM" name="Ref_DKM" value="">

				</form>
			
			</div>
			
			
	</div>
</div>		
	<script type="text/javascript">
		modelo =['Clio','C3'];
		modeloId = [1,2];
		
	</script>
	<script type="text/javascript">
	function DespuesAjax() {
	if (document.getElementById("CheckMarca").value == 'Sin Seleccionar' ){
			if (document.getElementById("myMarca").value != 0){ 
				document.getElementById("CheckMarca").checked = "checked";
				document.getElementById("CheckMarca").value = 'Seleccionado';
				alert(document.getElementById("CheckMarca").value);
				CambioMarcas();
			}
	 } else {
			if (document.getElementById("myMarca").value == 0){ 
				document.getElementById("CheckMarca").checked = "";
				document.getElementById("CheckMarca").value = 'Sin Seleccionar';
				alert(document.getElementById("CheckMarca").value);
			}

	}
}

	</script>
		
</body>
</html>
