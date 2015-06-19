<?php

	include("tool.php");
	include("include/tpv.inc.php");
	
	SimpleAutentificacionAutomatica("visual-xul");
	
	header("Content-type: application/vnd.mozilla.xul+xml");
	header("Content-languaje: es");

	header("Pragma: no-cache");
	header("Cache-control: no-cache");
	

	if ($usuarioActivoNoEsDependiente or !$NombreDependienteDefecto){
		session_write_close();
		header("Location: xulentrar.php?modo=avisoUsuarioIncorrecto");
		exit();
	}	
	
	if (!$IdLocalActivo){
		session_write_close();
		header("Location: xulentrar.php?modo=tiendaDesconocida");
		exit();
	}		
		
	$titulo = "TPV";	
	$cr = "<?";$crf = "?>";	
	
	$titulobreve = str_replace(" ","-",trim(strtolower($titulo)));
	
	//Config: el impuesto no lo vamos a mostrar
	$esOcultoImpuesto = "true";

	$pvpUnidad = _("PVP/Unidad");

	$modosDePago = array( 
				0=> _("EFECTIVO"),
				1=> _("TARJETA"),
				2=> _("TRANSFERENCIA"),
				3=> _("GIRO"),
				4=> _("ENVIO"), 
				5=> _("BONO")
				);										
						
	$statusArreglos = array(
		'Pdte Envio' 	=> _("Pdte Envio"),
		'Enviado'		=> _("Enviado"),
		'Recibido' 		=> _("Recibido"),
		'Entregado' 	=> _("Entregado")		
		);

	$NombreEmpresa = $_SESSION["GlobalNombreNegocio"];  
	$MensajePromo = getParametro("MensajePromocion");

	echo str_replace("@","?",'<@xml version="1.0" encoding="UTF-8"@>');
	echo str_replace("@","?",'<@xml-stylesheet href="chrome://global/skin/" type="text/css"@>');
	echo str_replace("@","?",'<@xml-stylesheet href="css/xultpv.css" type="text/css"@>');
	if ($modulos["datepicker"]){
		echo str_replace("@","?","<@xul-overlay href='".$_BasePath."modulos/datepicker/datepicker-overlay.php' type='application/vnd.mozilla.xul+xml'@>");
	}
?>	
<window id="window-tpv"  xml:lang="es" title="TPV"
        xmlns:html="http://www.w3.org/1999/xhtml"        
        xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">        
<!--  no-visuales -->  
<?php include("partes-tpv/tpvnovisuales.php"); ?>
<!--  no-visuales -->  

<!-- dependiente / cliente -->
<?php include("partes-tpv/tpvdependientecliente.php"); ?>
<!-- dependiente / cliente -->


<hbox flex="1">

<deck id="modoVisual" flex="1">
   
<groupbox flex="1">
    
	<!-- compra producto -->	
	<?php include("partes-tpv/tpvbuscaproducto.php") ?>
	<!-- compra producto -->	
	
	<vbox flex="1">
	<!-- listado productos -->
	 <?php include("partes-tpv/tpvlistaproductos.php") ?> 
	<!-- listado productos -->				

	<!-- listado compra tickets -->
 	<?php include("partes-tpv/tpvlistadoticket.php") ?>  
	<!-- listado compra tickets -->
	</vbox>	

	<!-- total y salir -->
	<hbox style="background-color: black">
	<caption id="TotalLabel" label="TOTAL: 0,00"  class="grande" style="color: #0f0;background-color: black"/>
	<spacer flex="1"/>
	<toolbarbutton oncommand="MostrarUsuariosForm()">	
	<caption  style="color: #0f0;background-color: black" id="tCliente"  label="<?php echo $NombreClienteContado ?>" class="media" />
	</toolbarbutton>
	</hbox>
	<!-- total y salir -->	
</groupbox>


<!-- modificacion de linea de modisto -->
<?php include("partes-tpv/tpvmodificacionlineamodisto.php"); ?>
<!-- modificacion de linea de modisto -->

<!-- alta de cliente -->
<box align="center" pack="center">
</box>
<!-- alta de cliente -->

<!-- seleccion cliente -->
<?php include("partes-tpv/tpvseleccioncliente.php"); ?>
<!-- seleccion cliente -->

<!-- ficha producto -->
<?php include("partes-tpv/tpvfichaproductos.php"); ?>
<!-- ficha producto -->

<!-- ficha Imprimir -->
<?php include("partes-tpv/tpvimprimir.php"); ?>
<!-- ficha Imprimir  -->

<!-- ficha Imprimir -->
<?php include("partes-tpv/tpvfichaimprimir.php"); ?>
<!--  ficha Imprimir  -->

<!--  ficha Detalles Ventas  -->
<?php  include("partes-tpv/tpvdetallesventa.php"); ?>
<!--  ficha Detalles Ventas  -->

<!-- ficha Listado Modistos -->
<?php include("partes-tpv/tpvfichalistadomodistos.php"); ?>
<!-- ficha Listado Modistos -->

<!-- ficha listados -->
<?php include("partes-tpv/tpvfichalistados.php"); ?>
<!-- ficha listados -->

<!-- ficha Query Abono -->
<?php include("partes-tpv/tpvqueryabono.snip.php"); ?>
<!-- ficha Query Abono -->

<!-- ficha Arqueo de caja -->
<iframe id="frameArqueo" flex="1" src="<?php echo $_BasePath; ?>modulos/arqueo/arqueo2.php"/>
<!-- ficha Arqueo de caja -->

</deck>

<!-- panel botones derecho y mesajeria -->
<?php include("partes-tpv/tpvpanelderecho.php"); ?>
<!-- panel botones derecho y mesajeria -->

</hbox>

<box collapsed="true">
<iframe id="hiddenPrinter" src="about:blank"/>
</box>

<script>//<![CDATA[

var Local = new Object();

var po_nombreclientecontado = "<?php echo addslashes($NombreClienteContado) ?>";
var po_ticketde = "<?php echo addslashes(_("Ticket de:") . " " . $global_nombretienda) ?>";
	

 Local.numeroDeSerie 	= <?php echo CleanID($numSerieTicketLocalActual) ?>;
 Local.motd 			= "<?php echo addslashes($MOTDActivo) ?>";
 Local.IdLocalActivo 	= <?php echo CleanID(getSesionDato("IdTienda")) ?>;
 Local.prefixSerie 		= "B" + Local.IdLocalActivo  ;
 Local.prefixSerieCS 	= "CS" + Local.IdLocalActivo  ;
 Local.prefixSerieIN 	= "IN" + Local.IdLocalActivo  ;
 Local.max_dep 			= <?php echo CleanID($numDependientes) ?>;
 Local.prefixSerieActiva = Local.prefixSerie;
 Local.nombretienda 		= "<?php echo addslashes($NombreLocalActivo) ?>";
 Local.nombreDependiente 	= "<?php echo addslashes($NombreDependienteDefecto)?>";
 Local.Negocio 			= "<?php echo addslashes($NombreEmpresa) ?>";
 Local.promoMensaje		= "<?php echo addslashes($MensajePromo) ?>";
 Local.diasLimiteDevolucion = 7;



var Global = new Object();

 Global.fechahoy = "<?php 
	$cad = "%A %d del %B, %Y";
	setlocale(LC_ALL,"es_ES");		
	$hoy = strftime($cad);
	if (function_exists("iconv"))
		echo iconv("ISO-8859-1","UTF-8",$hoy);
	else 
		echo $hoy;			
	
	?>";	

 Global.totalbase = 0;//Valor del ticket actual.
 
 //NOTA: activa funciones avanzadas 
 Global.AdministradorDeFacturasPresente = "<?php echo $_SESSION["EsAdministradorFacturas"] ?>";

var modospago = new Array();

<?php
$vEFECTIVO = 0;
	foreach( $modosDePago as $value=>$label ){
		echo "modospago[$value] = '$label';\n";
		if ($label=="BONO"){
			$vBONO = $value;	
		}else if ($label=="EFECTIVO"){
			$vEFECTIVO = $value;	
		}		
	}
	
?>

var vBONO		= parseInt(<?php echo intval($vBONO) ?>,10);
var vEFECTIVO 	= parseInt(<?php echo intval($vEFECTIVO) ?>,10);




//]]></script>
<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/cadenas.js.php?ver=1/r<?php echo rand(0,99999999); ?>"/>
<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/tpv.js?ver=49/r<?php echo rand(0,99999999); ?>"/>
<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/tickets.js?ver=44/r<?php echo rand(0,99999999); ?>"/>
<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/mecagrid.js?ver=44/r<?php echo rand(0,99999999); ?>"/>
<script>//<![CDATA[

function generadorCargado(){
	//Indica que funciones como aU() estan disponibles
	return typeof aU=="function";	
}

function generadorCargadoProductos(){
	//Indica que funciones como tA() estan disponibles
	return typeof tA=="function";	
}


function CargarUsuarios(){
	if (!generadorCargado()) {
		setTimeout("CargarUsuarios()",100);
		return;
	}
	<?php
	echo $generadorJsDeClientes;
	?>
}

var L = new Array();

function CargarProductos(){
	if (!generadorCargadoProductos()) {
		setTimeout("CargarProductos()",100);
		return;
	}
	<?php
	echo $generadorJSDeProductos;
	?>
}

function CargarCBFocus(){
	if (!(typeof CBFocus=="function")) {
		setTimeout("CargarProductos()",100);
		return;
	}
	
	setTimeout("CBFocus()",200);
}



//Cargara los productos cuando sea posible.
CargarUsuarios();
CargarProductos();
CargarCBFocus();

//Prepara listado de productos para limpieza rapida.
 document.gClonedListbox = id('listaProductos').cloneNode(true);




	
//]]></script>

</window>
