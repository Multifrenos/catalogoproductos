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
		<h2>Referencias Cruzadas.</h2>
		<p> Esta tabla de la base de datos de recambios es donde registramos las referencias de los fabricantes de recambios  indicando que cruces posibles puede haber. Está tabla es muy util para facilitar la busqueda de un recambio y sus posibles homologos de otras marcas.</p>
		<p>Por ejemplo: <br/>
		Filtro de aire para Mondeo I (FD) Berlina/saloon lo fabrican:<br/>
		CHAMPION con referencia "U 632"<br/>
		DELPHI con referencia "AF0203"<br/>
		...<br/>
		Y así con muchas más marcas de recambios ( fabricantes), con está tabla indicamos que referencias son las que se cruzan con nuestra recambio( id).
		</p>
		<h4>Estructura de la tabla referenciascruzadas de BD Recambios</h4>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Campo</th>
					<th>Descripción</th>
				</tr>
			</thead>
			
			<tr>
				<td><strong>RecambioID</strong></td>
				<td>[int(11)] Nuestro ID , el que vamos utilizar para identificar el producto, es único y debería coincidir con el tengamos en virtuemart.</td>
			</tr>
			<tr>
				<td><strong>IdFabricanteRec</strong></td>
				<td>[inte(11)] Es el id de la marca (fabricante) del cruce del recambio.</td>
			</tr>
			<tr>
				<td><strong>MarcaRecambio</strong></td>
				<td>[text] >El nombre marca (fabricante) del recambio.</td>
			</tr>
			<tr>
				<td><strong>RefMarcaRecambio</strong></td>
				<td>[text] Es la referencia que tiene la marca (fabricante) a ese recambio.</td>
			</tr>
			
		</table>
	</div>
	<div class="col-md-12">
		<h3>Importar datos a la tabla referenciascruzadas</h3><a name="importar"></a>
		<p> Cuando queremos realizar una importación de las referencias cruzadas desde un csv a la BD de recambios, tenemos que tener creado en la tabla de <mark>fabricantesrecambios de BD recambios</mark> el fabricante principal como resto fabricantes (marcas) que indicamos que son cruzados.</p>
		<p> Una vez subamos el fichero al servidor [enviarcsv.php], el proceso importación se realiza en 3 Pasos:</p>
		
		<div class="col-md-4">
			<h4>PASO 1: </h4>
			<p>Comprueba que:<br/>
			-<strong><mark>PENDIENTE</mark></strong> Comprobar tabla temporal si tiene registros, si los tiene se debe indicar que los va eliminar.<br/>
			- El fichero es correcto.<br/>
			- Si tiene datos y cuales.<br/>
			Nos muestra un <mark>formulario</mark> que nos permite pide:<br/>
			- Linea Inicial: Donde le indicamos donde quiere empezar.<br/>
			- Linea Final: Donde quieres que termine de realizar importacion de datos a MySql.<p>
			<p>( Esto ultimo ideal para realizar por parte un fichero )<br/>
			 seleccionar linea inicial y linea final, que son las que va añadir  añadir a la <mark>tabla temporal de BD importarRecambios.</mark></p>
			 <p>Una vez ejecutado formulario, <mark> pulsando [Paso 1: importar a mysql], mediante AJAX empieza añadir registros</mark> a la BD, a trozos para no saturar el servidor, e incluso hacerlo por parte el proceso.</p>
			Ficheros que se utilizan:<br/>
			 - php <mark>[recibircsv.php],[mysql_csv.php]</mark><br/>
			 - javascript <mark>[importar.js]</mark> </p>
			
		</div>
		<div class="col-md-4">
			<h4>PASO 2: </h4>
			<p>Llegamos al terminar PASO 1 o pulsando en el link <mark>Saltar paso 1 y directo al paso 2</mark>.</p>
			<p>El PASO 2 empieza ejecutando <mark>[paso2refcruzada.php]</mark>, donde lo dividimos en 3:<br/>
			- Logica antes de mostrar html (comprobaciones)<br/>
			- Html y formulario que muestra.<br/>
			- Funciones que llamamos al realizar cambio y opciones en formulario.<br/>
			</p>
			<p> Por lo que en <strong>logica antes de mostrar html realizamos:</strong><br/>
			- Comprueba que todos campos tengan datos, más de un caracter, sino pone <mark>ERR:CampoVacio</mark> <br/>
			- Cual es el fabricante principal y si existe la referencia, sino<mark>ERR:RefFabPrin no existe</mark><br/>
			TIENES QUE TENER EN CUENTA EL TIEMPO EJECUCION PARA NO SATURAR SERVIDOR</p>
			<p><strong>HTML y formulario que mostramos</strong><br/>
			A partir de aquí , solo trabajamos con los registros que NO tenga error, nos tiene que informar de:<br/>
			- Cuantos recambios hay para importar.<br/>
			- Cuantos fabricantes hay cruzados validos<br/>
			- Cuantos fabricantes hay no validos. (se pone ERR:FabricanteDesconocido)<br/>
			- No tiene permitir modificar el nombre de fabricantes no valido.<br/>
			Toda esta lógica se debe hacer AJAX, para evitar saturación del servidor y ademas cada vez que se cambie el nombre tendrá que comprobar nuevamente esos registros si el fabricante existe.<br/>
			Mientras no termine procesos AJAX no puede permitir continuar al PASO 3
			</p>
			
		</div>
		<div class="col-md-4">
			<h4>PASO 3: </h4>
			<p>Añadimos los datos correctos a la tabla referenciascruzadas de la base datos recambios</p>
		</div>
	</div>
	<div class="col-md-12">
		<h3>Estructura de csv y campos de tabla referenciascruzadas de BD tempporal</h3>
		<div class="col-md-4">
			<h4>Fichero referenciasCruzadas.csv</h4>
			<p> El <strong>fichero referenciasCruzadas.csv</strong> que vamos importar tiene contener los campos que indicamos, ni uno menos ni más.</p>
			<p> Se debe utilizar (,) como <mark>separador</mark> de campos y se utiliza (") como <mark>divisor o encapsulador</mark> de campos.</p>
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
					<td> Referencia del recambio del fabricante principal, el que nos proporciona el cruce de datos.
				</tr>
				<tr>
					<td><strong>Marca</strong></td>
					<td>Nombre de la marca o fabricante con el que cruzamos.</td>
				</tr>
				<tr>
					<td><strong>RefMarca</strong></td>
					<td>Referencia de la Marca, con la se cruza.</td>
				</tr>
			</table>
		</div>
		<div class="col-md-8">
			<h4>Tabla referenciaCruzadas de BD importarRecambios</h4>
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
					<td><strong>RecambioID</strong></td>
					<td>[int(11)] El ID del recambio ( producto que tenemos a la venta)<br/>
					En PASO 1<mark> queda en blanco</mark> ya que no lo tenemos.<br/>
					En PASO 2 obtenemos el dato de la busqueda con <mark>RefFabPrin</mark>
					</td>
				</tr>
				
				<tr>
					<td><strong>MarcaRecambio</strong></td>
					<td>[text] >El nombre marca (fabricante) del recambio.</td>
				</tr>
				<tr>
					<td><strong>IdFabricanteRec</strong></td>
					<td>[int(11)] Es el id de la marca (fabricante) del cruce del recambio.<br/>
					En PASO 1 <mark>se deja en blanco</mark><br/>
					En PASO 2 se busca el nombre de la marca (fabricante) BD de recambios y pone el id.<br/>
					Sino <mark>NO se encontrará</mark>, se anota en el error "Marca cruce no encontrada" en el campo de ESTADO de BD importarRecambios.
					</td>
				</tr>
				<tr>
					<td><strong>RefMarcaRecambio</strong></td>
					<td>[text] Es la referencia que tiene la marca (fabricante) a ese recambio.</td>
				</tr>
				<tr>
					<td><strong>Estado</strong></td>
					<td>[text] Lo vamos utilizar para indicar, si hay un error, si fue añadido a la BD de Recambios, si la referencia del fabricante principal no se encuentra.</td>
				</tr>
				
			</table>
		</div>
	</div>		

</div>

</body>
</html>
