<!DOCTYPE html>
<html>
<head>
<?php
	include './../head.php';
?>
</head>
<body>
<?php 
	include './../header.php';
?>

<div class="container">
	<div class="col-md-12">
		<h2>Tabla de Recambio de BD Recambios.</h2>
		<p> Contiene los datos de los productos ( recambios ) que vamos a vender o comprar, es la tabla con la que más vamos a trabajar.</p>
		
		<h4>Estructura de la tabla Recambio de BD Recambios</h4>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Campo</th>
					<th>Descripción</th>
				</tr>
			</thead>
			<tr>
				<td><strong>id</strong></td>
				<td>[int(11)] Nuestro ID , el que vamos utilizar para identificar el producto, es único y debería coincidir con el tengamos en virtuemart.</td>
			</tr>
			<tr>
				<td><strong>descripcion</strong></td>
				<td>[text] Sería el nombre del producto, normalmente es el Nombre de la familia , más la marca y si tiene alguna característica también, como puede ser dimensiones o posicion.</td>
			</tr>
			<tr>
				<td><strong>coste</strong></td>
				<td>[decimal(11,2)] es el precio del distribuidor, sin impuesto.</td>
			</tr>
			<tr>
				<td><strong>margen</strong></td>
				<td>[int(2)] es 40 fijo cada vez que añada un articulo, pero es un campo editable y recalcula.</td>
			</tr>
			<tr>
				<td><strong>iva</strong></td>
				<td>[int(2)] es 21 , ya que este tipo de negocio no tiene otro iva...</td>
			</tr>
			<tr>
				<td><strong>pvp</strong></td>
				<td>[decimal(11,2)] es el precio de venta con iva, este campo es calculado ( coste + margen + iva ), redondeo a 2 </td>
			</tr>
			<tr>
				<td><strong>FechaActualiza</strong></td>
				<td>[date] Fecha de ultima actualización del producto, pero solo de está tabla, no de la tablas relacionadas.</td>
			</tr>
		</table>
	</div>
	<div class="col-md-12">
		<a name="importar"></a>
		<h3>Importar datos a la tabla Recambio de DB Recambios</h3>
		<p> El poder añadir de forma masiva productos ( recambios) es algo necesario por la cantidad de recambios que se van tener.</p>
		<p> Está importación se hace con el <strong>fichero ListaPrecios.csv</strong>, que nos facilitará el proveedor, teniendo en cuenta que:</p>
		<ul>
		<li> Solo puede contener <mark>recambios de una familia de productos.</mark></li>
		<li> Que está familia tiene que existir en la <mark>tabla de FamiliasRecambios.</mark></li>
		<li> Solo puede contener <mark>recambios de una sola marca ( fabricante) </mark></li>
		<li> Que ese fabricante (marca) tiene que <mark>existir en tabla FabricantesRecambios</mark></li>

		</ul>
		<p>Lo que realiza en la importación es:<br/>
		- Añadir a tabla de BD temporal lista de precios.<br/>
		- Luego busca en la tabla de referencias cruzadas el ID del recambio si existe (RecambioID = ID) y si no existe se considera nuevo ( ESTADO= NUEVO)<br/>
		- Luego en lo existentes solo cambia coste y PVP según beneficio, en los nuevo crea en la tabla de referencias cruzadas BD Recambios y lo crea Recambios con el mismo ID</p>
		<h4>Es muy importante para que funcione preparar bien el fichero csv antes subirlo.</h4>
		<p> <strong>DESCRIPCION:</strong><br/>
			La descripcion del recambio no debe contener el nombre de la Familia , ni la Marca ya que estos datos se añaden en el PASO 3 antes de esta descripcion.<br/>
			Por ejemplo:<br/>
			Familia [ FILTRO AIRE ] + Marca [MANN] + Descripción de csv [ Micro fibra ] <br/>
			El campo descripción del recambio quedaría:	FILTRO AIRE MANN Micro fibra</p>
			<p>Recuerda que la descripción es corta (max 50 caracteres ) y ademas en la web tendremos una descripción larga.</p>
			<p>A veces los recambios pueden tener campos a mayores indicando posicion, medidas, etc.., aunque en la web podremos tener esos campos, a lo mejor es interesante alguno de esos campos en la descripcion antes de subir, como por ejemplo:<br/>
			<small><strong>Campos personalizado (POSICION):</strong></small><br/>
			<mark>Delantero,Trasero,Derecha,Izquierda,Lado Rueda,Lado Engranaje</mark></p>
		<p> Pasos del proceso importacion del fichero ListaPreciosProveedores:</p>
		<p> <strong>PRECIO COSTE:</strong><br/>
		Recuerda que la importación calcula el precio PVP automaticamente con un beneficio 40% + 21 % de IVa, por ello los precio coste tienen que ser sin IVA.<br/>
		Además el formato decimal tiene que ser con (.) no como coma, así no funciona.
		</p>
		<h4>PASOS Y ESTRUCTURA DE CSV Y TABLA DE IMPORTACION</h4>
		<p> Antes de estos pasos en IMPORTAR , debemos indicar el<mark>fichero Listaprecios.csv</mark> que vamos a subir, teniendo en cuenta que debe ser con los campos que indicamos más abajo.</p>
		<div class="col-md-4">
			<h5>PASO 1: </h5>
			<p>Seleccionamos lineas de fichero csv que queremos añadir a la <mark>tabla base de Datos importaRecambios</mark>, que la utilizamos como <strong>BD de temporal.</strong></p>
		</div>
		<div class="col-md-4">
			<h5>PASO 2:</h5>
			<p> Ahora ya tenemos tabla Listaprecios ( temporal ).<br/>
			Nos pregunta a que fabricante pertenece y que de familia es los recambios que acabamos de subir.( tiene existir primero)<br/>
			Al <mark>comprobar</mark> lo que hace buscar esa referencia en tabla referenciascruzadas de BD de Recambios, solo de aquellos que no tenga nada en ESTADO:<br/>
			- Si lo encuentra añadie RECAMBIOID de importar el ID que encuentra y en ESTADO pone EXISTENTE.<br/>
			- Si <strong>no</strong> la encuentra entonces solo cubre campo ESTADO como NUEVO.<br/>
			Este proceso se debe hacer en AJAX para no saturar el servidor. <br/>
			Además al terminar debe informar:<br/>
			- Resumen de articulos ( nuevos, existentes y los que no trata por error.<br/>
			</p>
		</div>
		<div class="col-md-4">
			<h5>PASO 3: </h5>
			<p>Una vez terminada la comprobación y aceptado los cambios. Lo que hacemos es :<br/> -NUEVOS:Añadir los nuevos recambios a BD Recambios.<br/>
			-EXISTENTES:Modificar los precios de los Recambios.<br/>
			-Reguarda fichero csv en carpeta importacionesRealizadas con el siguiente formato:
			[AÑO-MES-DIA]-[FABRICANTE]-[FAMILIA]<br/>
			-Elimina de la tabla Listaprecios de BD Importar, los que ya hemos añadido.</p>
		</div>

		<div class="col-md-12">
		
			<div class="col-md-5">
				<h4>Fichero ListaPreciosProveedor.csv</h4>
				<p> El <strong>fichero ListaPreciosProveedor.csv</strong> que vamos importar tiene contener los campos que indicamos, ni uno menos ni más.</p>
				<p> Se debe utilizar (,) como <mark>separador</mark> de campos y se utiliza (") como <mark>divisor o encapsulador</mark> de campos.</p>
				<p>RECUERDA:<br/>
				Que debe ser listado de la misma familia y de la misma marca (fabricante de recambio)</p>
				<p>Los datos que debe contener el csv son:</p>
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Dato</th>
							<th>Explicacion de dato</th>
						</tr>
					</thead>
					<tr>
						<td><strong>RefFabPrin</strong></td>
						<td> Referencia del producto del fabricante (marca) que vamos añadir listado precios para esa familia de recambios.
					</tr>
					<tr>
						<td><strong>Descripcion</strong></td>
						<td>Alguna característica, pero sin el Nombre de la familia ni la marca, para no repetir, puede ir en blanco </td>
					</tr>
					<tr>
						<td><strong>Coste</strong></td>
						<td>El precio de coste del producto (recambio) sin impuestos.<br/>Formato americano con punto (.) para decimales.</td>
					</tr>
				</table>
			</div>
			<div class="col-md-7">
				<h4>Tabla ListaPrecios de BD importarRecambios</h4>
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Campo</th>
							<th>Descripción</th>
						</tr>
					</thead>
					<tr>
						<td><strong>Linea</strong></td>
						<td>[int(11)] No indica el numero de linea del fichero csv que importamos</td>
					</tr>
					<tr>
						<td><strong>RefFabPrin</strong></td>
						<td>[text] Referencia del recambio del fabricante principal, el <mark>fabricante o marca</mark> que nos proporciona el cruce de datos.<br/>
						<mark>Este dato lo vamos utilizar en PASO 2 para buscar el dato de RecambioID.</mark>
					</tr>
					<tr>
						<td><strong>Descripcion</strong></td>
						<td>[text] >El nombre recambio, normalmente es una característica, pero sin el Nombre de la familia ni la marca, para no repetir.</td>
					</tr>
					<tr>
						<td><strong>Coste</strong></td>
						<td>[decimal 11,2] Es el coste del recambio sin iva.</td>
					</tr>
					<tr>
						<td><strong>Estado</strong></td>
						<td>[text] Lo vamos utilizar para indicar, si hay un error, si fue añadido a la BD de Recambios, si la referencia del fabricante principal no se encuentra.</td>
					</tr>
					<tr>
						<td><strong>RecambioID</strong></td>
						<td>[int(11)] El ID del recambio ( producto que tenemos a la venta)<br/>
						En PASO 1<mark> queda en blanco</mark> ya que no lo tenemos.<br/>
						En PASO 2 obtenemos el dato de la busqueda con <mark>RefFabPrin</mark>
						</td>
					</tr>
					
				</table>
			</div>
		</div>
	</div>	

</div>

</body>
</html>
