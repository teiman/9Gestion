var po_numtic='Num tic:';
var po_unid='Unid.';
var po_precio='Precio';
var po_descuento='Desc.';
var po_Total='Total';
var po_TOTAL='TOTAL:';
var po_Entregado='Entregado:';
var po_Cambio='Cambio:';
var po_desgloseiva='Desglose de IVA:';
var po_leatendio='Le atendió:';
var po_ticketarreglointerno='Ticket arreglo interno';
var po_ticketcesionprenda='Ticket cesión de prenda';
var po_ticketdevolucionprenda='Ticket devolución de prenda';
var po_ticketnoserver='El servidor no ha podido autorizar la impresión de este ticket. Inténtelo mas tarde';
var po_txtTicketVenta='Ticket venta';
var po_txtTicketCesion='Ticket cesión';
var po_txtTicketDevolucion='Ticket devolución';
var po_txtTicketArregloInterno='Ticket arreglo interno';
var po_imprimircopia='Impr. copia';
var po_cerrar='Cerrar';
var po_servidorocupado='Servidor ocupado, inténtelo más tarde';
var po_nopuedeseliminarcontado='¡No puedes eliminar el cliente contado!';
var po_seguroborrarcliente='¿Quieren borrar este cliente?';
var po_clienteeliminado='Cliente eliminado del sistema';
var po_noseborra='No se puede borrar ese cliente';
var po_nuevocreado ='Nuevo cliente creado';
var po_clientemodificado='Cliente modificado';
var po_operacionincompleta='Operacion con cliente incompleta, inténtelo mas tarde';
var po_mensajeenviado='Mensaje enviado';
var po_modopago='Modo de pago:';
var po_nombreclientecontado='Cliente contado';
var po_Elige='Elije...';
var po_15diaslimite='No se admiten devoluciones\\npasados %d días';
var po_cuentascopias='¿Cuántas copias del código de barras necesita imprimir?';
var po_cuantasunidadesquiere='¿Cuántas unidades del producto requiere?';
var po_cuantasunidades='¿Cuántas unidades?';
var po_faltadefcolor='Falta definir color';
var po_faltadeftalla='Falta definir talla';
var po_faltadefcb='Falta definir el CB';
var po_errorrepcod='Código de barras repetido';
var po_tallacolrep='Talla o color repetidos';
var po_unidadescompra='Debe especificar unidades de compra';
var po_modnombreprod='Debe modificar el nombre del producto';
var po_especificarref='Debe especificar una referencia';
var po_especifiprecioventa='Debe especificar un precio de venta';
var po_especificoste='Debe especificar un coste';
var po_nuevoproducto='Nuevo producto';
var po_nohayproductos='No hay productos';
var po_sehandadodealtacodigos='Se han dado de alta %d códigos';
var po_segurocancelar='¿Esta seguro que quiere cancelar?';
var po_imprimircodigos='Imprimir CB';
var po_borrar='Eliminar';
var po_avisoborrar='¿Desea eliminar?';
var po_nombre='Nombre';
var po_talla='Talla';
var po_color='Color';
var po_unidades='Unid.';
var po_nombrecorto='Nombre de cliente demasiado corto';
var po_quierecerrar='Seguro que quiere proceder al \'CIERRE DE CAJA\'?';
var po_sugerenciarecibida='Sugerencia recibida';
var po_incidenciaanotada='Incidencia anotada';
var po_notaenviada='Nota enviada';
var po_confirmatraslado='¿Esta seguro?';
var po_destino='Destino:';
var po_moviendoa='Moviendo mercancía a: ';
var po_importereal='Importe real de la caja:';

var po_error = po_servidorocupado;

var po_pagmas = ">>";
var po_pagmenos = "<<";



	

var id = function(name) { return document.getElementById(name); }

var Vistas = new Object();
Vistas.ventas = 7;
Vistas.abonar = 10;
Vistas.tpv = 0;
Vistas.caja = 11;

//Ultimo articulo añadido al carrito.
var xlastArticulo;
	
/* Devuelve una coletilla aleatoria */

function ApendRand(){ 
	return "r="+ Math.random();
}	
	
/*=========== REVISION VENTAS   ==============*/	

function VaciarDetallesVentas(){
	var lista = id("busquedaDetallesVenta");
	
	for (var i = 0; i < idetallesVenta; i++) { 
		kid = id("detalleventa_"+i);					
		if (kid)	lista.removeChild( kid ); 
	}
	idetallesVenta = 0;
}

function VaciarBusquedaVentas(){
	var lista = id("busquedaVentas");
	
	for (var i = 0; i < ilineabuscaventas; i++) { 
		kid = id("lineabuscaventa_"+i);					
		if (kid)	lista.removeChild( kid ); 
	}
	ilineabuscaventas = 0;
}

var idetallesVenta = 0;
	
function RevisarVentaSeleccionada(){
	VaciarDetallesVentas();
	var idex = id("busquedaVentas").selectedItem;
	BuscarDetallesVenta(idex.value);
}

function AbrirVentaSeleccionada(){
	VaciarDetallesVentas();
	var idex = id("busquedaVentas").selectedItem;
	var facturanum = idex.value;

//	var p = prompt("¿Cuanto dinero quiere abonar?",valpen);
	
	t_RecuperaTicket( facturanum );	
}


var Abonar = new Object();

function VentanaAbonos(){
	//VaciarDetallesVentas();
	LimpiarFormaAbonos();
	
	var idex = id("busquedaVentas").selectedItem;
	
	if(!idex)	return;//no se selecciono nada
	
	var IdFactura = idex.value;
	
	if (!IdFactura) return;//seleccion invalidad	
	
	var xpen = id("venta_pendiente_"+IdFactura);
	var dineropendiente = xpen.getAttribute("label");
	var serie = id("venta_serie_" + IdFactura).getAttribute("label");
	var num = id("venta_num_" + IdFactura).getAttribute("label");
	var serienumfactura = serie+num;
	
	//resetea nuevo abono
	Abonar = new Object();	
	
	//fijamos la id actual
	Abonar.IdFactura = IdFactura;	
	Abonar.Maximo = parseFloat(dineropendiente).toFixed(2);
		
	id("abono_Debe").setAttribute("label",formatDinero(Abonar.Maximo));
	id("abono_Efectivo").setAttribute("value",formatDinero(Abonar.Maximo));
	id("abono_numTicket").setAttribute("label",serienumfactura);
	
	document.getElementById("modoVisual").setAttribute("selectedIndex",Vistas.abonar);	
}


function ActualizaPeticionAbono() {
	var cr = "\n";
	var color ="black";
	
	var entrega = 0;
	entrega += parseFloat(CleanMoney(document.getElementById("abono_Efectivo").value));		
	entrega += parseFloat(CleanMoney(document.getElementById("abono_Bono").value));				
	entrega += parseFloat(CleanMoney(document.getElementById("abono_Tarjeta").value));		
	
	var pendiente = Abonar.Maximo - entrega;
	id("abono_Pendiente").setAttribute("label", formatDinero(pendiente));
	id("abono_nuevo").setAttribute("label", formatDinero(entrega));	
}

function LimpiarFormaAbonos(){
	id("abono_Efectivo").value = "0";
	id("abono_Bono").value = "0";
	id("abono_Tarjeta").value = "0";	
	Abonar.Maximo = 0;
	ActualizaPeticionAbono();	
}


function RealizarAbono(){
	var IdFactura = Abonar.IdFactura;
	var abono_efectivo = CleanMoney(id("abono_Efectivo").value);
	var abono_tarjeta = CleanMoney(id("abono_Tarjeta").value);
	var abono_bono = CleanMoney(id("abono_Bono").value);	

	var obj = new XMLHttpRequest();
	var url = "services.php?modo=realizarAbono&idfactura=" + escape(IdFactura)
		+ "&pago_efectivo=" + parseFloat(abono_efectivo)
		+ "&pago_bono=" + parseFloat(abono_bono)
		+ "&pago_tarjeta=" + parseFloat(abono_tarjeta)	
		+ "&r=" + Math.random();		
	
	obj.open("POST",url,false);
	obj.send("");	

	var text = obj.responseText;
	
	if (!text) return alert(po_servidorocupado);
		
		
	var xpen = id("venta_pendiente_"+IdFactura);
	var xstatus = id("venta_status_"+IdFactura);
		
	text = parseFloat(text);		
	xpen.setAttribute("label",parseFloat(text).toFixed(2));//Nuevo valor pendiente
	
	if (text<0.01){
		if (xstatus)
			xstatus.setAttribute("label","PAGADO");//Correspondiente nuevo estado		
	}
	
	LimpiarFormaAbonos();
	VolverVentas();
}



function AddLineaDetallesVenta(Referencia, Nombre,Talla, Color, Unidades, Descuento, PV){
	var lista = id("busquedaDetallesVenta");
	var xitem, xReferencia,xNombre,xTalla,xColor,xUnidades,xDescuento,xPV;

	xitem = document.createElement("listitem");
	xitem.setAttribute("id","detalleventa_" + idetallesVenta);
	idetallesVenta++;
	
	xReferencia = document.createElement("listcell");
	xReferencia.setAttribute("label", Referencia);
	
	xNombre = document.createElement("listcell");
	xNombre.setAttribute("label", Nombre);

	xTalla = document.createElement("listcell");
	xTalla.setAttribute("label", Talla);

	xColor = document.createElement("listcell");
	xColor.setAttribute("label", Color);

	xUnidades = document.createElement("listcell");
	xUnidades.setAttribute("label", Unidades);
//	xUnidades.setAttribute("style","text-align:right");

	xDescuento = document.createElement("listcell");
	xDescuento.setAttribute("label", Descuento);
	xDescuento.setAttribute("style","text-align:right");

	xPV = document.createElement("listcell");
	xPV.setAttribute("label", parseFloat(PV).toFixed(2));
	xPV.setAttribute("style","text-align:right");
	
	xitem.appendChild( xReferencia );
	xitem.appendChild( xNombre );
	xitem.appendChild( xTalla );
	xitem.appendChild( xColor );
	xitem.appendChild( xUnidades );
	xitem.appendChild( xDescuento );	
	xitem.appendChild( xPV );
						
	lista.appendChild( xitem );
}


var serialNum = (Math.random()*9000).toFixed();

function BuscarDetallesVenta(IdFactura ){
	RawBuscarDetallesVenta(IdFactura, AddLineaDetallesVenta);
}

function RawBuscarDetallesVenta(IdFactura,FuncionRecogerDetalles){
	var obj = new XMLHttpRequest();
	var url = "services.php?modo=mostrarDetallesVenta&idfactura=" + escape(IdFactura)
		+ "&r=" + serialNum;		
	serialNum++;		
	
	obj.open("GET",url,false);
	obj.send(null);	

	var tex = "";
	var cr = "\n";
	var Referencia, Nombre,Talla, Color, Unidades, Descuento, PV;
	var node,t,i;
	
	if (!obj.responseXML) return alert(po_servidorocupado);		
	
	var xml = obj.responseXML.documentElement;
	for (i=0; i<xml.childNodes.length; i++) {
		node = xml.childNodes[i];
		if (node && node.childNodes && node.childNodes.length >0){
			t = 0;
			if (node.childNodes[t].firstChild){
				Referencia = node.childNodes[t++].firstChild.nodeValue;
				Nombre = node.childNodes[t++].firstChild.nodeValue;
				Talla = node.childNodes[t++].firstChild.nodeValue;
				Color = node.childNodes[t++].firstChild.nodeValue;
				Unidades = node.childNodes[t++].firstChild.nodeValue;
				Descuento = node.childNodes[t++].firstChild.nodeValue;
				PV = node.childNodes[t++].firstChild.nodeValue;
				Codigo = node.childNodes[t++].firstChild.nodeValue;
				
				if (Unidades>1){
					PV = PV/Unidades;				
				}				
								
				FuncionRecogerDetalles(Referencia, Nombre,Talla, Color, Unidades, Descuento, PV,Codigo);
			}
		}
	}
}


function VolverVentas(){	
	id("modoVisual").setAttribute("selectedIndex",Vistas.ventas);	
}

	
function VerVentas(){
	id("FechaBuscaVentas").value ="DD-MM-AAAA";
	id("FechaBuscaVentasHasta").value ="DD-MM-AAAA";	
	VaciarBusquedaVentas();
	id("panelDerecho").setAttribute("collapsed","true");
	id("modoVisual").setAttribute("selectedIndex",Vistas.ventas);	
}

function VerCaja(){
	//id("FechaBuscaVentas").value ="DD-MM-AAAA";
	//id("FechaBuscaVentasHasta").value ="DD-MM-AAAA";	
	//VaciarBusquedaVentas();
	//id("panelDerecho").setAttribute("collapsed","true");
	id("modoVisual").setAttribute("selectedIndex",Vistas.caja);	
}


function VerTPV(){
	id("panelDerecho").setAttribute("collapsed","false");
	id("modoVisual").setAttribute("selectedIndex",Vistas.tpv);	
}


var ilineabuscaventas = 0;

function AddLineaVentas(vendedor,serie,num,fecha,total,pendiente,estado,IdFactura,nombreCliente){
	var lista = id("busquedaVentas");
	var xitem, xvendedor,xserie,xnum,xfecha,xtotal,xpendiente,xestado;

	xitem = document.createElement("listitem");
	xitem.value = IdFactura;
	xitem.setAttribute("id","lineabuscaventa_"+ilineabuscaventas);
	ilineabuscaventas++;
	
	xvendedor = document.createElement("listcell");
	xvendedor.setAttribute("label", vendedor);
	xvendedor.setAttribute("crop", "end");	
	
	xserie = document.createElement("listcell");
	xserie.setAttribute("label", serie + " - ");
	xserie.setAttribute("style","text-align:right");
	xserie.setAttribute("id","venta_serie_"+IdFactura);
		
	xnum = document.createElement("listcell");
	xnum.setAttribute("label", num);
	xnum.setAttribute("id","venta_num_"+IdFactura);
	
	xfecha = document.createElement("listcell");
	xfecha.setAttribute("label", fecha);	

	xtotal = document.createElement("listcell");
	xtotal.setAttribute("label", parseFloat(total).toFixed(2));
	xtotal.setAttribute("style","text-align:right");

	xpendiente = document.createElement("listcell");
	xpendiente.setAttribute("label", parseFloat(pendiente).toFixed(2));
	xpendiente.setAttribute("style","text-align:right");
	xpendiente.setAttribute("id","venta_pendiente_"+IdFactura);
	
	xestado = document.createElement("listcell");
	xestado.setAttribute("label", estado);
	xestado.setAttribute("style","width: 8em");
	xestado.setAttribute("crop", "end");
	xestado.setAttribute("id","venta_status_"+IdFactura);
		
	
	xnombre = document.createElement("listcell");
	xnombre.setAttribute("label", nombreCliente);
	xnombre.setAttribute("crop", "end");
		
	xitem.appendChild( xvendedor );
	xitem.appendChild( xserie );
	xitem.appendChild( xnum );
	xitem.appendChild( xfecha );
	xitem.appendChild( xtotal );
	xitem.appendChild( xpendiente );	
	xitem.appendChild( xestado );
	xitem.appendChild( xnombre );	
						
	lista.appendChild( xitem );		
}


function BuscarVentas(){
	VaciarBusquedaVentas();
	var desde = id("FechaBuscaVentas").value;
	var hasta = id("FechaBuscaVentasHasta").value;
	var nombre = id("NombreClienteBusqueda").value;	
		
	if ((!hasta || hasta == "DD-MM-AAAA") &&  (!desde || desde == "DD-MM-AAAA") && (!nombre))return;

	var modo = 	(id("modoConsultaVentas").checked)?"pendientes":"todos";
	var modoserie = (id("modoConsultaVentasSerie").checked)?"cedidos":"todos";
	
	RawBuscarVentas(desde,hasta,nombre,modo,modoserie,false,AddLineaVentas);
}


function RawBuscarVentas(desde,hasta,nombre,modo,modoserie,IdFactura,FuncionProcesaLinea){

	var url = "services.php?modo=mostrarVentas&desde=" + escape(desde) 
		+ "&modoconsulta=" + escape(modo) 
		+ "&hasta=" + escape(hasta) 
		+ "&nombre=" + escape(nombre)
		+ "&modoserie=" + escape(modoserie)
		+ "&forzarfactura=" + IdFactura;
	
	var obj = new XMLHttpRequest();

	obj.open("GET",url,false);
	obj.send(null);
	
	var tex = "";
	var cr = "\n";
	
	var vendedor,serie,num,fecha,total,pendiente,estado,IdFactura;
	var node,t,i;
	
	if (!obj.responseXML)
		return alert(po_servidorocupado);
	
	var xml = obj.responseXML.documentElement;
	for (i=0; i<xml.childNodes.length; i++) {
		node = xml.childNodes[i];
		if (node){
			t = 0;
			vendedor 	= node.childNodes[t++].firstChild.nodeValue;
			serie 		= node.childNodes[t++].firstChild.nodeValue;
			num 		= node.childNodes[t++].firstChild.nodeValue;
			fecha 		= toFormatoFecha(node.childNodes[t++].firstChild.nodeValue);
			total 		= node.childNodes[t++].firstChild.nodeValue;
			pendiente 	= node.childNodes[t++].firstChild.nodeValue;
			estado 		= node.childNodes[t++].firstChild.nodeValue;												
			IdFactura 	= node.childNodes[t++].firstChild.nodeValue;
			
			if (node.childNodes[t].firstChild){						
				nombreCliente = node.childNodes[t++].firstChild.nodeValue;			
			} else {
				nombreCliente = "";
			}
																																							
			FuncionProcesaLinea(vendedor,serie,num,fecha,total,pendiente,estado,IdFactura,nombreCliente);			
		}					
	}
}


/*=========== REVISION VENTAS   ==============*/

	
/*=========== SALIR   ==============*/


function SalirNice(){
	window.document.location.href="xulentrar.php?modo=login-user";	
}

/*=========== SALIR   ==============*/



/*=========== IMPRIMIR TICKET  ==============*/



function ActualizaPeticion() {
	var cr = "\n";
	var color ="black";
	
	if(!modoMultipago){
		var entrega = 	parseFloat(CleanMoney(document.getElementById("peticionEntrega").value));		
	} else {
		var entrega = 0;
		entrega += parseFloat(CleanMoney(document.getElementById("peticionEfectivo").value));		
		entrega += parseFloat(CleanMoney(document.getElementById("peticionBono").value));				
		entrega += parseFloat(CleanMoney(document.getElementById("peticionTarjeta").value));		
	}
	
	var pendiente = parseFloat(entrega) - parseFloat( Global.totalbase );
	id("peticionPendiente").setAttribute("label", formatDinero(pendiente));	
	if (id("peticionCambioEntregado"))
		id("peticionCambioEntregado").setAttribute("value",pendiente);
	
	if ( parseInt(pendiente*100) >=0.01)
		color = "green";
	else  
		if ( parseInt(pendiente*100) <= -0.01)  color = "red";
	
	id("peticionPendiente").style.color = color;
}

var ModoDeTicket = "venta";

function AjustarEtiquetaModo(){
	var extatus = id("rgModosTicket").value;	
	var MODOP = "EFECTIVO";
	var VMODOP = vEFECTIVO;
			
	switch (extatus) {
		default:// 
		case 'venta':		
			id("etiquetaTicket").setAttribute("label",  po_txtTicketVenta  );		
			ModoDeTicket = "venta";
			break;
 		case 'cesion': 		
			id("etiquetaTicket").setAttribute("label", po_txtTicketCesion );		
			ModoDeTicket = "cesion";
			break;
 		case 'devolucion': 		
			id("etiquetaTicket").setAttribute("label", po_txtTicketDevolucion );		
			ModoDeTicket = "devolucion";
			MODOP = "BONO";
			VMODOP = vBONO;
			break;
 		case 'interno': 		
			id("etiquetaTicket").setAttribute("label", po_txtTicketArregloInterno);		
			ModoDeTicket = "interno";			
			break;
	}


	id("modoDePagoTicket").setAttribute("label", MODOP);//no le gusta a F15

	id("modoDePagoTicket").value = VMODOP;

}

function NuevoModo(){
	var MANTENER_MODO = true;
	
	AjustarEtiquetaModo();
	CancelarVenta(MANTENER_MODO);
	TicketAjusta();
}
	


function AbrirPeticion(){
	RecalculoTotal();
	AjustarEtiquetaModo();
	
	var base = formatDinero( Global.totalbase );	
	var entregado = base;
	var pendiente = "0.00";
	
	if ( ModoDeTicket == "devolucion" || ModoDeTicket == "cesion" ){
		entregado = "0.00";
		pendiente = formatDinero( Math.abs(Global.totalbase) );
	}		
	
	id("peticionTotal").setAttribute("label", base);
	id("peticionEntrega").value = entregado;
	id("peticionPendiente").setAttribute("label", formatDinero(0));	
	
	ActualizaPeticion();
	
	id("modoVisual").setAttribute("selectedIndex",6);
}

function CerrarPeticion(){
	HabilitarImpresion();//si el boton imprimir fue desabilitado, se rehabilita
	id("modoVisual").setAttribute("selectedIndex",0);
	CBFocus();
}

function CerrarImprimir(){
	HabilitarImpresion();//TODO: aqui tambien?
	id("modoVisual").setAttribute("selectedIndex",0);
	id("fichaProducto").setAttribute("src","about:blank");
	CBFocus();
}

function CBFocus(){
	id("CB").focus();
}


var data_tickets;

function EncapsrTextoParaImprimir(texto) {
	var salida;
	var header = 'data:text/html,';
	
	salida = "<html><head><title></title>"+
	    "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />"+
		"<style type='text/css' media='screen'>"+
		".ticket{"+
		"	border: 1px gray solid;"+
		"	padding: 8px;	"+
		"}"+
		".botonera{"+
		"	border: 1px gray solid;"+
		"	padding: 8px;	"+
		"	margin: 16px;"+
		"	text-align: center;"+
		"	align: center;"+
		"}"+
		"</style>"+	
		"<style type='text/css' media='print'>"+
		"input {	visibility: hidden;}"+
		".botonera { visibility: hidden; }"+
		".ticket {"+
		"	border. none;"+
		"	border: 0pt;"+
		"	padding: 0px;"+
		"}"+
		"</style>"+
		"</head><body><div class='ticket'><xmp>\n";
	salida = salida + texto;
	salida = salida + "</xmp></div>\n<script>\nsetTimeout('window.print()',100);\n</script>"+
		"<div class='botonera'>"+
		"<input onclick='window.print()' value='"+po_imprimircopia+"' type='button'>"+
		" <input onclick='window.close()' value='"+po_cerrar+"' type='button'></div>"+
		"</body></html>";

	
	salida = header + encodeURIComponent( salida );
	return salida;
}

function ImprimirTicket(modo) {    

	var data,firma,resultado, esCopia;
	var xrequest = new XMLHttpRequest();
	var unidades, precio, descuento,codigo;
	var url = "";
	
	//Numero de serie random, para evitar peticiones multiples.
	// si dos peticiones seguidas llevan el mismo serial, la segunda se ignora
	var sr = parseInt(Math.random()*999999999999999);

	if (modo == 'copia')
		esCopia = 1;
	else
		esCopia = 0;		
		
	var ticket = t_CrearTicket(esCopia);
	var	text_ticket = ticket.text_data;
	var post_ticket = ticket.post_data;
	
	//alert( post_ticket);	
	if (!esCopia) {
		DesactivarImpresion();
		
		url = "xcreaticket.php?modo=creaticket&moticket="+escape(ModoDeTicket)+"&tpv_serialrand="+sr;
		xrequest.open("POST",url,false);
		xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xrequest.send(post_ticket);
	
		resultado = xrequest.responseText;
		resultado = parseInt(resultado);
	} else {
		resultado = 1; 
	}		
	
	if (!resultado ) {		
		alert(po_ticketnoserver);	
	} else {		
		//Un popup es mas engorroso pero mas solido
		//alert(data_tickets);
		top.TicketFinal = window.open(EncapsrTextoParaImprimir(text_ticket),"Consola Ticket","width=400,height=600,scrollbars=1,resizable=1","text/plain");		
		CerrarPeticion();
		CancelarVenta();
	}

}

function DesactivarImpresion(){
	id("BotonAceptarImpresion").setAttribute("disabled","true");
}

function HabilitarImpresion(){
	id("BotonAceptarImpresion").setAttribute("disabled","false");
}



/*=========== IMPRIMIR TICKET  ==============*/



/*=========== FICHA PRODUCTO  ==============*/

function agnadirPorNombre() {
	//MostrarAjax();
	setTimeout("raw_agnadirPorNombre()",100);
}

//NOTA: agnadir por nombre se ejecuta en dos fases, para permitir que la visualizacion de iconos
// ajax sea visible para el usuario.
function raw_agnadirPorNombre() {	
	var cod,text = "",k;
	var nombre = new String(id("NOM").value);
	id("NOM").value = "";

	if (nombre.length <3){
	   OcultarAjax();
	   return;
	}
	VaciarListadoProductos();		
	
	nombre = nombre.toUpperCase();
	
	for(var t=0;t<iprodCod;t++) {
		cod = prodCod[t];
		nom = productos[cod].nombre.toUpperCase();
		//if (nom.indexOf( nombre ) != -1) {
		if (nom.indexOf( nombre ) != -1) {
			k = productos[cod];
			if (k) {					
				CrearEntradaEnProductos(k.codigobarras,k.referencia,k.nombre, k.precio,k.impuesto,k.talla,k.color);
			}			
		}
	}
	
	if (  esOnlineBusquedas()  )
		ExtraBuscarEnServidor(nombre);  	  		
	else
		OcultarAjax();  	
}

function esOnlineBusquedas(){
	if ( id("buscar-servidor").getAttribute("checked") == "true"){
		return true;
	} else {
		return false;
	}
}


function CEEP(codigo){
	var	k = productos[codigo];		
	if (k) {					
		if ( prodlist_cb[codigo]) return;
				
		CrearEntradaEnProductos(k.codigobarras,k.referencia,k.nombre, k.precio,k.impuesto,k.talla,k.color);
	}	
}


var AjaxBuscaEnServidor = false;

function ExtraBuscarEnServidor(nombreProducto){
		var url = "services.php?modo=buscaproducto&nombre="+escape(nombreProducto);
		AjaxBuscaEnServidor = new XMLHttpRequest();	
		AjaxBuscaEnServidor.open("POST",url,true);
		AjaxBuscaEnServidor.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		AjaxBuscaEnServidor.onreadystatechange = Rececepcion_BuscaEnServidor;
		AjaxBuscaEnServidor.send(null);

}

function Rececepcion_BuscaEnServidor(){
	if (!AjaxBuscaEnServidor) {
		AjaxBuscaEnServidor = new XMLHttpRequest();
		return;
	}

	
	if (AjaxBuscaEnServidor.readyState==4) {		
		var rawtext = AjaxBuscaEnServidor.responseText;
		try {	
			eval(rawtext);
			//alert(rawtext);			
		} catch(e){
			//alert("ERROR en la evaluacion de la respuesta");	
			alert("error: "+e.toSource());	
		}
	}	
}


/*=========== FICHA PRODUCTO  ==============*/


/*=========== FICHA PRODUCTO  ==============*/

var esFichaVisible = 0;

function ToggleFichaFormOUT() {
	CBFocus();
    esFichaVisible = 0;
    
    id("panelDerecho").setAttribute("collapsed","false");
	id("modoVisual").setAttribute("selectedIndex",0);
	id("fichaProducto").setAttribute("src","about:blank");
	
}


function ToggleFichaForm() {
    var code;
    
    if (esFichaVisible) {
    	id("panelDerecho").setAttribute("collapsed","false");
       code = 0;//ocultar
    } else {
    	id("panelDerecho").setAttribute("collapsed","true");
       code = 4;
    }
       
	var cod = getCodigoSelectedProd();
	var fichaProducto = id("fichaProducto");
	
	id("modoVisual").setAttribute("selectedIndex",code);
	
	var url = "simplecruzado.json.php?CodigoBarras=" + cod;
	var obj  = Meca.cargarJSON( false,url,true);
	
	if(obj){
		Dom.MatarTodosHijos("fichaProducto");		
		Meca.generaCruzadoProductos( "fichaProducto", obj );	
	}
	
	
	esFichaVisible = code;
	

				
}


/*=========== FICHA PRODUCTO  ==============*/



/*=========== LISTADO USUARIOS  ==============*/

var usuarios = new Array();
var iusers = 0;

var ixusuarios = 0;
var UltimoRowID = "";
var indiceDeRow = 0;

var UsuarioSeleccionado = 0;//por defecto el cliente contado

aU( "Cliente contado",0,0);

// Agnadir usuarios 
function aU( nombre, idcliente, debe) {		
	usuarios[idcliente] = new Object();
	usuarios[idcliente].nombre = nombre;
	usuarios[idcliente].id = idcliente;	
	usuarios[idcliente].debe = debe;	
	addXUser( nombre, idcliente, debe );
}

var esListadoUsuariosVisible = false;


function MostrarUsuariosForm() {

	id("modoVisual").setAttribute("selectedIndex",3);
		
	esListadoUsuariosVisible = true;
}

function ToggleListadoUsuariosForm() {
    var code;
    
    if (esListadoUsuariosVisible) {
       code = 0;//ocultar
       CBFocus();
    }  else
       code = 3;      

	id("modoVisual").setAttribute("selectedIndex",code);
		
	esListadoUsuariosVisible = (code==3);
}

var preSeleccionadoCliente = false;

function SeleccionaCliente(idcliente){
	if(!idcliente){
		//pickClienteContado();
		id("tab-vistacliente").setAttribute("collapsed","true");
		return;
	}
	
	if(preSeleccionadoCliente != idcliente)	
		VerClienteId(idcliente);
		
	preSeleccionadoCliente = idcliente;
	id("tab-vistacliente").setAttribute("collapsed","false");
	id("tab-vistacliente").setAttribute("label", "Cliente: "+ id("visNombreComercial").value);
	//pickClient(idcliente);
}


function EliminarClienteActual(){

	if(!preSeleccionadoCliente)
		return alert(po_nopuedeseliminarcontado);
		
	if(!confirm(po_seguroborrarcliente))
		return;

	var obj = new XMLHttpRequest();
	var url = "services.php?modo=eliminarcliente&idcliente=" + escape(preSeleccionadoCliente)
		+ "&r=" + Math.random();		
	serialNum++;		
	
	obj.open("GET",url,false);
	obj.send(null);	
	
	var resultado =	parseInt(obj.responseText);

	if(resultado){
		delXUser( preSeleccionadoCliente);
	//	id("tab-vistacliente").setAttribute("collapsed","true");
		alert(po_clienteeliminado);
	} else {
		alert(po_noseborra);
	}
}

function pickClient(idusuario,myself) {
	var labelCliente = id("tCliente");
	var nuevoNombreUsuario;
	
	UsuarioSeleccionado = idusuario;
	nuevoNombreUsuario = usuarios[idusuario].nombre;	
	//alert("Cambio de usuario: id:"+idusuario+",nombre:"+nuevoNombreUsuario+",oldnombre:"+tc.getAttribute("value"));
	
	labelCliente.setAttribute("label", nuevoNombreUsuario );
	 
	ToggleListadoUsuariosForm();
}

function LimpiaToClienteContado() {
	var labelCliente = id("tCliente");
	var nuevoNombreUsuario;
	
	UsuarioSeleccionado = 0;
	nuevoNombreUsuario = usuarios[0].nombre;	
	
	labelCliente.setAttribute("label", nuevoNombreUsuario );	 
}



function pickClienteContado(){
	pickClient(0);
}

function PasaTab(desde,hasta,pad){
	var xPanelMod = id(hasta);
	//TODO: probablemente hay una forma mejor de hacer esto	
	id(desde).setAttribute("fistTab","true");
	id(desde).setAttribute("selectedIndex",pad);
	id(desde).setAttribute("selected","false");
	id(desde).setAttribute("selectedItem",xPanelMod);
	id(hasta).setAttribute("beforeselected","true");
//	id(desde).setAttribute("afterselected","false");

	id(hasta).setAttribute("selectedIndex",pad);
	id(hasta).setAttribute("selected","true");
	id(hasta).setAttribute("selectedItem",xPanelMod);
	//id(hasta).setAttribute("beforeselected","false");
	id(desde).setAttribute("last-tab","true");
}
	

function delXUser(iduser){
	var root = id("clientPickArea");
	var xrow = id("user_picker_"+iduser);
	
	if(xrow)
		root.removeChild(xrow);
}

//INFO: agnade un usuario al listado de usuarios
function addXUser(nombreUser, iduser, debe) {
	var root = id("clientPickArea");

	var xtext = 	document.createElement("listitem");
	xtext.setAttribute("id","user_picker_"+iduser);
	
	var xcell1 = 	document.createElement("listcell");
	var xcell0 = 	document.createElement("listcell");

	if (debe)
		xcell0.setAttribute("label",debe );	
		
	xcell0.setAttribute("value",iduser );
	xcell0.setAttribute("readonly","true");
	xcell0.setAttribute("image","img/cliente16.png");
	xcell0.setAttribute("class","listitem-iconic");
	//xcell0.setAttribute("onclick","pickClient("+iduser+")");	
	
	xcell1.setAttribute("id","user_picker_nombre_"+iduser);
	xcell1.setAttribute("label",nombreUser );	
	xcell1.setAttribute("value",iduser );
	xcell1.setAttribute("readonly","true");
	//xcell1.setAttribute("onclick","pickClient("+iduser+")");	
	xtext.setAttribute("ondblclick","pickClient("+iduser+",this)");	
	xtext.setAttribute("onclick","SeleccionaCliente("+iduser+",this)");
	xtext.setAttribute("value",iduser );	

//	SeleccionaCliente
	
	xtext.appendChild( xcell0 );
	xtext.appendChild( xcell1 );
	root.appendChild( xtext);	
}


function UpdateCliente(idCliente,nombrecliente){
	var idNombreCelda = "user_picker_nombre_"+idCliente
	var xcellnombre = id(idNombreCelda);
	
	if(xcellnombre){
		xcellnombre.setAttribute("label",nombrecliente);		
	} else {
		alert("no encontro xcellnombre:"+idNombreCelda);
	}
}


/*=========== LISTADO USUARIOS  ==============*/

/*=========== RETOCAR DESCUENTOS  ==============*/

function CleanDescuento( valor ) {
	if (!valor) 	return 0.0;
 
	valor = valor.replace(/ /g,"");
	valor = valor.replace(/%/g,"");
	valor = parseFloat(valor);
	if (isNaN( valor ))
		return 0.0;
	return valor;	
}

function formatDescuento(valor) {
	return FormateComoDescuento(valor);
}

function ModificarDescuento() {
	var p;
	var ticketcodigo = getCodigoSelectedTicket();
	if (!ticketcodigo)	return;
	var ticprecio = id("tic_descuento_"+ ticketcodigo);
	if (!ticprecio) return;
	
	p = prompt("¿Nuevo descuento?", CleanDescuento(ticprecio.value)  )
	if (p<0) p = 0.0;
	else if (p>100) p = 100.0;
		
	p = parseMoney(p);
	ticprecio.setAttribute("value",FormateComoDescuento(p));	
	Blink("tic_descuento_" + ticketcodigo, "label-descuento" );
	RecalculoTotal();
}

/*=========== RETOCAR DESCUENTOS  ==============*/


/*=========== RETOCAR PRECIOS  ==============*/

function ModificarPrecio() {
	var ticketcodigo = getCodigoSelectedTicket();
	if (!ticketcodigo)	return;
	var ticprecio = id("tic_precio_"+ ticketcodigo);
	if (!ticprecio) return;
	
	p = parseMoney(prompt("¿Nuevo precio?", ticprecio.value ));
	ticprecio.setAttribute("value",formatDinero(p));	
	Blink("tic_precio_" + ticketcodigo, "label-precio" );
	RecalculoTotal();
}

/*=========== RETOCAR PRECIOS  ==============*/

/*=========== CALCULO TOTALES  ==============*/

//Desde "1.331,33" hacia "1331.33"
function CleanMoney(cadena) {
	return parseMoney(new String(cadena) );
}

function parseMoney (cadena) {
	//var cadoriginal = cadena;
	if (!cadena) {
		 cadena = new String( cadena );
		 if( !cadena.replace ){
			 return 0.0;		 	
		 }
	}
	
	if (cadena.replace){	
		cadena = cadena.replace(/\./g,"");
		cadena = cadena.replace(/\,/g,".");
	}
	
	cadena = parseFloat( cadena );	
	
	if (isNaN( cadena ))
		return 0.0;
		
	return cadena;
}

function CleanInpuesto( iva ) {
	if (!iva)	return 0;

	iva = iva.replace(/\%/g,"");
	iva = iva.replace(/ /g,"");
	
	if (isNaN(iva))
		return 0;	
	return iva;
}

function RecalculoTotal() {
	var codigo,subtotal,subtotalconimpuesto,conimpuestoydescuento;	
	var fila, dato, impuesto;
	var totalbase = 0;
	var filaprecio,filacantidad;
	
	for (var t=0;t<ticketlist.length;t++) {
		if (ticketlist[t]) {
			codigo = ticketlist[t];	
			if (codigo) {
					fila = id("tic_" + codigo);
					if (fila) {
					
						dato 		= id("tic_unid_" + codigo).value;
						impuesto 	= id("tic_impuesto_" + codigo).value;
						 		
						impuesto 	= CleanInpuesto(impuesto);						
						 
						filacantidad =parseMoney( dato );
						filaprecio = parseMoney( id("tic_precio_" + codigo).value );
					    //alert( "Suma: " + filacantidad + " * " +filaprecio+ " = " + ( filacantidad* filaprecio ) );
						
						descuento = id("tic_descuento_" + codigo).value;
						descuento = CleanDescuento( descuento );
		
				
						subtotal = (parseFloat(filacantidad) * parseFloat(filaprecio));						
						//Ya incluye el impuesto, es PVP
						//subtotalconimpuesto = parseFloat(subtotal) + parseFloat(subtotal) * (parseFloat(impuesto)/100);
						//conimpuestoydescuento = parseFloat(subtotalconimpuesto) - parseFloat(subtotalconimpuesto)*(parseFloat(descuento)/100);
						condescuento = parseFloat(subtotal) - parseFloat(subtotal)*(parseFloat(descuento)/100);
						
						totalbase = parseFloat(totalbase) + parseFloat(condescuento);
					}
			}		 
		}
	}	
	
	var totalLabel = id("TotalLabel");

	totalLabel.setAttribute("label", "TOTAL: " + formatDinero( totalbase ) + " EUR");
	
	Global.totalbase = totalbase;

}


/*=========== CALCULO TOTALES  ==============*/

/*=========== CANCELAR  ==============*/

/*
  		<radiogroup orient="horizontal" id="rgModosTicket" oncommand="TicketAjusta()">
  			<radio id="rVenta" label="Venta" selected="true" value="venta"/>
  			<radio id="rCesion" label="Cesion" value="cesion"/>
  			<radio id="rDevolucion" label="Devolucion" value="devolucion"/>  			
  		</radiogroup>  */
  		
function LimpiarModosTicket(){
	id("rInterno").setAttribute(	"selected",	"false"	);
	id("rDevolucion").setAttribute(	"selected",	"false"	);
	id("rCesion").setAttribute(		"selected",	"false"	);
	id("rVenta").setAttribute(		"selected",	"true"	);
	
	setTimeout("TicketAjusta()",90);	
	
	var xmodos = id("rgModosTicket");
	var rventa = id("rVenta");
	
	xmodos.value = "venta";
	xmodos.setAttribute("value","venta");
	
}


function LimpiarCliente(){
	//restaurar cliente contado
	pickClienteContado();
}


 function CancelarVenta(mantenermodo) {	
	LimpiaToClienteContado();
	VaciarListadoProductos();
	VaciarListadoTickets();
	CerrarPeticion();//Evita que se quede el interface de ticket abierto
	
	
	id("NOM").value = "";
	id("REF").value = "";
	id("CB").value 	= "";
	
	
	LimpiarMultipagos();	
	LimpiarSubVentanaArreglo();

	if(!mantenermodo)
		LimpiarModosTicket();	
	
	arreglosprenda 	= new Array(); //arreglos
	prodlist 		= new Array(); //ayuda al listado de "productos" (minivista de productos)
	prodlist_cb		= new Array(); // ... igual que el anterior, pero guarda cbs almacenados
	prodlist_tag	= new Array(); // ... igual que el anterior, pero guarda cbs almacenados
	iprod 			= 0;
	carrito 		= new Array(); //Unidades de cesta (obsoleto)
	ticket 			= new Array(); //Productos en la cesta
	ticketlist 		= new Array(); //ayuda al listado de cesta	
	iticket 		= 0;
 }

/*=========== CANCELAR  ==============*/

/*=========== CLIENTES  ==============*/

function AltaCliente(){
	EnviarCliente(false,0);
}

function ModificarCliente(){
	EnviarCliente(true,preSeleccionadoCliente);
}

function getDatoCliente(vistamodificada,nombre) {
	var nombrefinal = nombre;
	if(vistamodificada)
		nombrefinal = "vis"+nombrefinal;
	
	var obj = id(nombrefinal);
	if(obj)
		return encodeURIComponent(obj.value);
		
	return false;
}


function EnviarCliente(modificar,idcliente){
   var data;
   var nombrecliente = getDatoCliente(modificar,"NombreComercial");
   var cr = "&";
   
	if ( !nombrecliente || nombrecliente.length < 2) {
		return  alert(po_nombrecorto);
	}

	if(!modificar){
		var url = "xaltacliente.php?modo=altarapida"
		data = "modo=altarapida" + cr;
	} else {
		var url = "xaltacliente.php?modo=modificarcliente"
		data = "modo=modificarcliente" + cr;	
		data = "IdCliente=" +parseInt(idcliente)+ cr;				
	}
	
	data =  data + "NombreComercial=" + nombrecliente + cr;       
	data =  data + "Direccion=" + getDatoCliente(modificar,"Direccion") + cr;    
	data =  data + "Localidad=" + getDatoCliente(modificar,"Localidad") + cr;    	
//	data =  data + "Poblacion=" + escape(id("Poblacion").value) + cr;    		
	data =  data + "CP=" + getDatoCliente(modificar,"CP") + cr;    
	data =  data + "Telefono1=" + getDatoCliente(modificar,"Telefono1") + cr;    
		//data =  data + "Telefono2=" + escape(id("Telefono2").value) + cr;    
		//data =  data + "Cargo=" + escape(id("Cargo").value) + cr;    
	data =  data + "CuentaBancaria=" + getDatoCliente(modificar,"CuentaBancaria") + cr;    
	data =  data + "NumeroFiscal=" + getDatoCliente(modificar,"NumeroFiscal") + cr;    
	data =  data + "Comentarios=" + getDatoCliente(modificar,"Comentarios") + cr;    
		//data =  data + "TipoCliente=" + escape(id("TipoCliente").value) + cr;    
//	data =  data + "IdModPagoHabitual=" + escape(id("IdModPagoHabitual").value) + cr;    
	data =  data + "Pais=" + getDatoCliente(modificar,"Pais") + cr;    
	data =  data + "PaginaWeb=" + getDatoCliente(modificar,"PaginaWeb") + cr;    
	data =  data + "FechaNacim=" + getDatoCliente(modificar,"FechaNacim") + cr;    

   var xrequest = new XMLHttpRequest();
	xrequest.open("POST",url,false);
	xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	xrequest.send(data);
	var respuesta = xrequest.responseText;//.split("=")[1];

	var idCliente = parseInt(respuesta);

	if (idCliente) {

		//OcultarClienteForm();	
		if(!modificar){
			aU(decodeURIComponent(nombrecliente),parseInt(idCliente), 0);
			LimpiarClienteForm();			
			alert(po_nuevocreado);
		} else{
			UpdateCliente(idCliente,decodeURIComponent(nombrecliente));			
			alert(po_clientemodificado);
		}
	} else
		alert(po_operacionincompleta);
		
}

function LimpiarClienteForm(){
	id("NombreComercial").value = "";
	id("Direccion").value = "";
	id("CP").value = "";   
	id("Localidad").value = "";   
	id("Telefono1").value = ""; 
	id("CuentaBancaria").value = "";
	id("NumeroFiscal").value = "";    
	id("Comentarios").value = "";    
	id("Pais").value = ""; 
	id("PaginaWeb").value = "";
	id("FechaNacim").value = "";   	
}


function VerCliente(){
	VerClienteId(1);
}

function VerClienteId(idcliente){
	//VaciarBusquedaVentas();

	

	var url = "services.php?modo=mostrarCliente"
		+ "&idcliente=" + escape(idcliente);
	
	var obj = new XMLHttpRequest();

	obj.open("GET",url,false);
	obj.send(null);
	
	var tex = "";
	var cr = "\n";
	
//	var vendedor,serie,num,fecha,total,pendiente,estado,IdFactura;
	var node,t,i;
	
	var po_error = po_servidorocupado;
	
	if (!obj.responseXML)
		return alert(po_error);		
	if (!obj.responseXML.documentElement)
		return alert(po_error);
	
	
	
	var xml = obj.responseXML.documentElement;
	

	
	for (i=0; i<xml.childNodes.length; i++) {
		node = xml.childNodes[i];
		if (node){
			t = 0;
			id("visNombreComercial").value = node.getAttribute("NombreComercial");
			id("visLocalidad").value = node.getAttribute("Localidad");
			id("visCP").value = node.getAttribute("CP");
			id("visNumeroFiscal").value = node.getAttribute("NumeroFiscal");
			id("visComentarios").value = node.getAttribute("Comentarios");
			id("visPaginaWeb").value = node.getAttribute("PaginaWeb");
			id("visDireccion").value = node.getAttribute("Direccion");
			id("visCuentaBancaria").value = node.getAttribute("CuentaBancaria");
			id("visTelefono1").value = node.getAttribute("Telefono1");			
			break;//sale del bucle, pues ya tenemos los datos
		}					
	}
}


var max_cli = 200;

//Cambia de Dependiente
function cambiaCliente() {
  var dep;
  var tDep = id("tCliente");
  
  for (var t=0;t<max_cli;t++) {
		dep = id ("clie_"+t);
		if (dep && dep.getAttribute("checked")){
			tDep.setAttribute( "value",dep.getAttribute("label"));
			return;
		}  
  }
}

function MostrarClienteForm() {
	document.getElementById("modoVisual").setAttribute("selectedIndex",2);
}

function OcultarClienteForm() {
	document.getElementById("modoVisual").setAttribute("selectedIndex",0);
}

function AltaClienteForm() {
	MostrarClienteForm();	
}

function CancelarAlta() {  
	OcultarClienteForm();
}

/*=========== CLIENTES  ==============*/

/*=========== DEPENDIENTES  ==============*/



function cambiaDependiente() {
  var dep;
  //var tDep = id("tDependiente"); //OBSOLETO, ahora se usa un campo en Local.
  var nombreDependiente;
  
  for (var t=0;t<Local.max_dep;t++) {
		dep = id ("dep_"+t);
		if (dep && dep.getAttribute("checked")){
			nombreDependiente = dep.getAttribute("label");
			
			//Etiqueta visual dependiente (dependiento del theme puede estar visible)
			//tDep.setAttribute( "value",nombreDependiente);
			
			//Boton visible
			id("depLista").setAttribute("label",nombreDependiente);
			
			return;
		}  
  }
}
/*=========== DEPENDIENTES  ==============*/

/*=========== FORMAT  ==============*/

// desde "4,43.33 $"  hacia  "443,33"
function formatDinero(numero) {
 var num = new Number(numero);
 num = num.toString().replace(/\$|\,/g,'');

 if(isNaN(num)) num = "0";

 var sign = (num == (num = Math.abs(num)));
  num = Math.floor(num*100+0.50000000001);
 var cents = num%100;
  num = Math.floor(num/100).toString();

 if(cents<10) cents = "0" + cents;

 for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
   num = num.substring(0,num.length-(4*i+3))+'.'+ num.substring(num.length-(4*i+3));

 return (((sign)?'':'-') + num + ',' + cents);
}





/*=========== FORMAT ==============*/

/*=========== ARREGLOS ==============*/

function MostrarDialogoArreglos() {
	document.getElementById("modoVisual").setAttribute("selectedIndex",1);
}

function OcultarDialogoArreglos() {
	document.getElementById("modoVisual").setAttribute("selectedIndex",0);
}


function ArregloParaFila() {
	LimpiarSubVentanaArreglo();
	MostrarDialogoArreglos();
}

var impuesto_normal = 16;//TODO: impuesto de modistos

var arreglosprenda = new Array();

function agnadirLineaModisto() {
	var ades = id("arregloDescripcion");
	var aquien = id("arregloModisto");
	var acuanto = id("precioArreglo");
	var ticketcodigo = getCodigoSelectedTicket();
	
	if (!aquien.value || !ticketcodigo) {
		return;
	}
	
	var aquiename = aquien.label;		
	var IdModisto = aquien.value;
	
	if (!aquiename || !IdModisto) {
		 return;
	}
	
	if (!arreglosprenda[ticketcodigo]) 	arreglosprenda[ticketcodigo] = 0;
	arreglosprenda[ticketcodigo]++;
			
	var numeroArreglosEstaPrenda = arreglosprenda[ticketcodigo];	
	var arregloid = "MOD."+ticketcodigo+"."+numeroArreglosEstaPrenda+"."+IdModisto; 
	var arregloref = "MOD."+ticketcodigo; 

	
	VaciarListadoProductos();
	
	tpv.AddArreglo( ticketcodigo,arregloid, ades.value, arregloref, acuanto.value, impuesto_normal, IdModisto);
	
	OcultarDialogoArreglos();
	
	LimpiarSubVentanaArreglo();		
	RecalculoTotal();
}

function LimpiarSubVentanaArreglo(){
	id("arregloDescripcion").value = po_Elige;
	id("arregloModisto").setAttribute("label",po_Elige);
	id("precioArreglo").value = "0.00";
}

function CancelarArreglo() {  
	LimpiarSubVentanaArreglo();
	OcultarDialogoArreglos();
}


/*=========== ARREGLOS ==============*/

/*=========== MENU CONTEXTO ==============*/

function getCodigoSelectedProd() {
	var t,codigo;	
	var fila;
	
	for (t=0;t<prodlist.length;t++) {
		if (prodlist[t]) {
			codigo = prodlist[t];	
			if (codigo) {
					fila = id("prod_" + codigo);
					if (fila && fila.selected) {
						return codigo;
					}
			}		 
		}
	}	
	return null;
}

function toNegativo(unidades){
	return (unidades<0)?unidades:0 - unidades;
}

function ConvertirSignoApropiado(unidades){
	var modo = id("rgModosTicket").value;
	var salida = 0;
	switch( modo ){
		case "interno":
			salida = 0;//Unidades en interno siempre es cero.	
			break;
		case "devolucion":
			salida = toNegativo(unidades);
			break;
		default:
		case "cesion":					
		case "venta":					
			salida =  Math.abs(unidades);
			break;			
	}
//	alert("modo:"+ modo + ",entra: "+unidades+",sale: "+salida);
	return salida;
}

function agnadirPorMenu( unidades )	{
	var cod = getCodigoSelectedProd();
		
	if (!cod) return;

	if (unidades=="preguntar"){
		unidades = prompt(po_cuantasunidades,0);
		if (!unidades)	return;
	}
		
	if (!unidades) {		
		unidades = 1;
	}
	
	unidades = parseInt(unidades);
	unidades = ConvertirSignoApropiado( unidades );
	
	setImagenProducto( cod );
	
//	alert(unidades);
	
	tpv.AddCarrito( cod.toUpperCase() , unidades);
	RecalculoTotal();

	var extatus = id("rgModosTicket").value;	
	if (extatus == 'interno' ){		
		if(xlastArticulo) xlastArticulo.focus();		
		//else alert("last: no last :( ");
		
		ArregloParaFila();
	}
}





//ticketlist
function VaciarListadoTickets(){
	var lista = id("listadoTicket");
	
	for (var i = 0; i < ticketlist.length; i++) { 
		if (ticketlist[i]) {
			kid = id("tic_"+ticketlist[i]);		
			
			if (kid)	lista.removeChild( kid ); 
		}
	}
	ticketlist = new Array();
}

function VaciarListadoProductos(){
    var oldListbox = id('listaProductos');
          
    var newListbox = document.gClonedListbox.cloneNode(true);
    oldListbox.parentNode.replaceChild( newListbox,oldListbox);     

	prodlist = new Array();
	prodlist_cb = new Array();
	prodlist_tag = new Array();
}

function ModificaTicketUnidades(cuantas) {		

	if (cuantas<0){
		cuantas = prompt(po_cuantasunidades,0);
	}

	if (!cuantas)
		return;

	var cod = getCodigoSelectedTicket();
	
	if (cod) {
		var unidcod = id("tic_unid_" + cod );
		if (unidcod) {
			unidcod.setAttribute("value",cuantas);	
			Blink("tic_unid_" + cod );			
			RecalculoTotal();
		}
		
	}
	
}

function getCodigoSelectedTicket() {
	var t,codigo;	
	var fila;
	
	for (t=0;t<ticketlist.length;t++) {
		if (ticketlist[t]) {
			codigo = ticketlist[t];	
			if (codigo) {
					fila = id("tic_" + codigo);
					if (fila && fila.selected) {
						return codigo;
					}
					
					if (fila == xlastArticulo){
						return codigo;
					}	
			}		 
		}
	}	
	alert("codigo no encontrado!");
	//xlastArticulo.value
	return null;
}


function QuitarArticulo() {
	var t,codigo;	
	var fila;

	for (t=0;t<ticketlist.length;t++) {
		if (ticketlist[t]) {
			codigo = ticketlist[t];

			if (codigo) {
					fila = id("tic_" + codigo);
					if (fila && fila.selected) {
						//alert("Eliminando " +codigo);
						fila.parentNode.removeChild(fila)
						ticket[codigo] = null;
					}
			}		 
		}
	}	
	RecalculoTotal();
}

/*=========== MENU CONTEXTO ==============*/

/*=========== VISUAL HINTS ==============*/

function unIluminate(name,tipo) {
	var me = document.getElementById(name);
	document.getElementById(name).style.backgroundColor='white';
	document.getElementById(name).style.color='black';
	if (tipo=="listbox")
		id(name).style.cssText = " -moz-binding: url(\"chrome://global/content/bindings/listbox.xml#listitem\");";	
	else
	if (tipo=="menulist") 
		id(name).style.cssText = "-moz-binding: url(\"chrome://global/content/bindings/menulist.xml#menulist\");";			  
	else 
	if (tipo =="label-precio") {
		id(name).style.cssText = " -moz-binding: url(\"chrome://global/content/bindings/listbox.xml#listitem\");";	
		me.style.align ="right";
		me.style.textAlign ="right";							
	}
	else
	if (tipo == "groupbox"){
		id(name).style.cssText = "-moz-binding: url(\"chrome://global/content/bindings/groupbox.xml#groupbox\");";			  			
	}else 
	if (tipo =="label-descuento") {
		id(name).style.cssText = " -moz-binding: url(\"chrome://global/content/bindings/listbox.xml#listitem\");";	
		me.style.align ="right";
		me.style.textAlign ="right";							
	}		
	
}

function Iluminate(name) {
	id(name).style.backgroundColor='yellow';
	id(name).style.color='black';
}

function Blink(name,tipo) {
	Iluminate(name);
	
	if (!tipo) tipo ="listbox";
	
	setTimeout("unIluminate('"+name+"','"+tipo+"') ",500);
}

/*=========== VISUAL HINTS ==============*/

/*===========COMPRAR ==============*/



var Imageview = new Object();
Imageview.lastchange = 0;
Imageview.cb = false;
Imageview.oldcb = false;
Imageview.nombre = false;

function UpdateImageview(){
	var difftime = (new Date()).getTime() - parseFloat(Imageview.lastchange);

	if(difftime>100){	
		if (Imageview.oldcb != Imageview.cb){	
		  	id("muestraProductoCB").setAttribute("value",Imageview.cb);
		  	id("nombreProducto").setAttribute("value",Imageview.nombre);
		  			  	
			id("muestraProducto").setAttribute("src", "imagenproducto.php?cb="+ escape( Imageview.cb ) );
			Imageview.oldcb = Imageview.cb;
		}
	} else {
		//wait more!
		setTimeout("UpdateImageview()",25);
	}
}

function setImagenProducto( CodigoBarras ){
    if(!CodigoBarras){
     return;
    }
    
    if(Imageview.cb == CodigoBarras) return;
    
    Imageview.cb = CodigoBarras;
    
    if (pool.Existe(CodigoBarras)){ 
    	pool.select(CodigoBarras);  
    	Imageview.nombre = pool.get().nombre ;
    }
  	setTimeout("UpdateImageview()",50);
    
    //Resetea el ultimo cambio, de modo que de forma efectiva retrasa el siguiente
    Imageview.lastchange = (new Date()).getTime(); 
}


function agnadirPorCodigoBarras()	{
	var cb = id("CB");
	var vcb = CleanCB(cb.value);
	
	cb.value = "";
	cb.setAttribute("value","");
	
	//Encuentra, y añade.
	var encontrado = raw_agnadirPorCodigoBarras(vcb,true);					
} 

function aPCB(vcb){
 	raw_agnadirPorCodigoBarras(new String(vcb),true);
}


function raw_agnadirPorCodigoBarras(vcb, reEntrar) {	
	vcb = CleanCB(vcb);

	//Intenta añadirlo...
	var encontrado = tpv.AddCarrito( vcb ,ConvertirSignoApropiado(1) );
		
	if (!encontrado){			
		ExtraBuscarEnServidorXCB(vcb);
		if(pool.Existe(vcb)) {
			var encontrado = tpv.AddCarrito( vcb ,ConvertirSignoApropiado(1) );
		}
	}	else {
		//NOTA: debe añadirse al listado para que lo puedan consultar.
		CEEP(vcb);
	}	
	
	RecalculoTotal();
	
	//Si lo ha encontrado, sera una buena idea mostrar el cb y su foto,si la hay..
	if (encontrado){
		setImagenProducto( vcb );
	}			
	//NOTA: no se añade entrada en productos
}

function agnadirPorReferencia()	{
	var referencia = id("REF").value.toUpperCase();
	if (!referencia) return;
	referencia  = new String(referencia);
		
	if (referencia.length <3){
		//usar referencias de 1 de longitud puede causar problemas
		// si aparecen referencias que coincidan con este codigo	   
	   return;
	}
	
	raw_agnadirPorReferencia( CleanRef(referencia) );
}

function raw_agnadirPorReferencia(referencia)	{
	var k,yaexiste;	
	
	VaciarListadoProductos();
	
	if (ref2code[referencia]) {
		var productosRef = ref2code[referencia].split(",");
		var p;
				
		for(var t=0;t<productosRef.length;t++) {
			p = productosRef[t];
			if (p){
				k = productos[p];								
				if (k) {		
					yaexiste = prodlist_cb[k.codigobarras];
					if (!yaexiste)		
						CrearEntradaEnProductos(k.codigobarras,k.referencia,k.nombre, k.precio,k.impuesto,k.talla,k.color);
				}
			}
		}
	}	
	
	if (  esOnlineBusquedas()  ) {
		//alert("buscando en itnernet");
		ExtraBuscarEnServidorXRef(referencia);
	}
	  	  				
}


var AjaxBuscaEnServidorXRef = false;

function ExtraBuscarEnServidorXRef(refProducto){
		var url = "services.php?modo=buscaproductoref&ref="+escape(refProducto);
		AjaxBuscaEnServidorXRef = new XMLHttpRequest();	
		AjaxBuscaEnServidorXRef.open("POST",url,true);
		AjaxBuscaEnServidorXRef.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		AjaxBuscaEnServidorXRef.onreadystatechange = Rececepcion_BuscaEnServidorXRef;
		AjaxBuscaEnServidorXRef.send(null);

}

function Rececepcion_BuscaEnServidorXRef(){
	if (!AjaxBuscaEnServidorXRef) {
		AjaxBuscaEnServidorXRef = new XMLHttpRequest();
		return;
	}
	
	if (AjaxBuscaEnServidorXRef.readyState==4) {		
		var rawtext = AjaxBuscaEnServidorXRef.responseText;
		try {	
			eval(rawtext);			
		} catch(e){	
			//alert("error: "+e.toSource());	
		}
	}	
}


var AjaxBuscaEnServidorXCB = false;
var AjaxFuncionExito = false;//Para peticion asincrona


function ExtraBuscarEnServidorXCB(cbProducto){		
		//Peticion sincrona
		//NOTA: es burcaRproductocb
		var url = "services.php?modo=buscarproductocb&cb="+escape(cbProducto);
		AjaxBuscaEnServidorXCB = new XMLHttpRequest();	
		AjaxBuscaEnServidorXCB.open("POST",url,false);
		AjaxBuscaEnServidorXCB.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		AjaxBuscaEnServidorXCB.send(null);
		
		var rawtext = AjaxBuscaEnServidorXCB.responseText;
		try {	
			//alert(rawtext);
			if (rawtext) {
				eval(rawtext);
			}	
		} catch(e){	
			//alert("error: "+e.toSource());	
		}							
}


function Old_ExtraBuscarEnServidorXCB(cbProducto, functionSiExito){		
		//INFO: Peticion asincrona, ahora no se usa
		var url = "services.php?modo=buscaproductocb&cb="+escape(cbProducto);
		AjaxBuscaEnServidorXCB = new XMLHttpRequest();	
		AjaxBuscaEnServidorXCB.open("POST",url,true);
		AjaxBuscaEnServidorXCB.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		AjaxBuscaEnServidorXCB.onreadystatechange = Rececepcion_BuscaEnServidorXCB;
		AjaxBuscaEnServidorXCB.send(null);
				
		AjaxFuncionExito = functionSiExito;			
}

function Rececepcion_BuscaEnServidorXCB(){
	//INFO: version asincrona, ahora no se usa
	if (!AjaxBuscaEnServidorXCB) {
		AjaxBuscaEnServidorXCB = new XMLHttpRequest();
		return;
	}
	
	if (AjaxBuscaEnServidorXCB.readyState==4) {		
		var rawtext = AjaxBuscaEnServidorXCB.responseText;
		try {				
			eval(rawtext);	
			if (AjaxFuncionExito){
				eval(AjaxFuncionExito);				
				AjaxFuncionExito = false;//por si acaso hay rellamadas
			}		
		} catch(e){	
			//alert("error: "+e.toSource());	
		}
	}	
}


/*===========COMPRAR ==============*/

/*===========TOOLS===========*/

var id = function (param) { return document.getElementById(param); }

var log = function (param) {

	return;
      var datalog =  id("logarea");

      var actual = datalog.getAttribute("value");
      datalog.setAttribute("value",param+"\n"+actual);
}

/*===========TOOLS===========*/

///////////////////////////////
//
// POOL
//
//

var productos = new Array(); //Unidades en tienda
var prodlist = new Array(); //ayuda al listado de "productos" (minivista de productos)
var prodlist_cb = new Array(); //ayuda al listado de "productos" (minivista de productos)
var prodlist_tag = new Array(); //ayuda al listado de "productos" (minivista de productos)
var iprod = 0;
var prodCod  = new Array(); //listado de codigos de barras
var iprodCod = 0; //indexado de codigos de barras

var carrito = new Array(); //Unidades de cesta (obsoleto)
var ticket = new Array(); //Productos en la cesta
var ticketlist = new Array(); //ayuda al listado de cesta
var iticket = 0;



function Pool(nombre){
	this._nombre = nombre;
	this._selected = 0;
	
	this.get = function () {
		return productos[this._selected];
	}
	
	this.select = function (idex) {
		this._selected = idex;
	}
	
	this.add = function (idex, self) {
		productos[idex] = self;
		carrito[idex] = new Object();
		carrito[idex].unidades = 0;
	}

	this.addCarrito = function (idex,uc) {
		var ouc = carrito[idex].unidades;
		carrito[idex].unidades = parseInt(ouc) + parseInt(uc);
	}

	this.Existe = function (idbusca) {
		if (productos[idbusca]) return 1;
		return null;
	}
	
	this.ExisteTicket = function ( idt ) {
		if (ticket[idt]) return 1;
		return null;		
	}
	
	this.CreaTicket = function ( idt ) {
		ticketlist[iticket++] = idt;		
		ticket[idt] = new Object();	
		ticket[idt].nombre = productos[idt].nombre;
		ticket[idt].codigobarras = productos[idt].codigobarras;
		ticket[idt].referencia = productos[idt].referencia;
		ticket[idt].precio = productos[idt].precio;
		ticket[idt].impuesto = productos[idt].impuesto;
		ticket[idt].color = productos[idt].color;
		ticket[idt].talla = productos[idt].talla;
		ticket[idt].unidades = 0;
	}
}

var pool = new Pool();
var tpv = new Object();

var ref2code = new Array();

///////////////////////////////
//
// PRODUCTO
//

function CrearEntradaEnProductos(codigo,referencia,nombre,precio,impuesto,talla,color) {

    prodlist_cb[codigo] = 1;
    
	setImagenProducto( codigo );
				
	var xlistadoProductos = id("listaProductos");	
	var xcod = document.createElement("label");xcod.setAttribute("value",referencia);
	var xnombre = document.createElement("label");xnombre.setAttribute("value",nombre);	
	
	var xtalla = document.createElement("label");xtalla.setAttribute("value",talla);	
	var xcolor = document.createElement("label");xcolor.setAttribute("value",color);					
	

	
	var xprecio = document.createElement("label");xprecio.setAttribute("value",formatDinero(precio));	
	var xeur = document.createElement("label");xeur.setAttribute("value","EUR");	
		
	var xlistitem = document.createElement("listitem");
	
	xlistitem.setAttribute("id","prod_"+codigo);
	
	xprecio.style.align ="right";
	xprecio.style.textAlign ="right";
	
	xnombre.setAttribute("style","width: 260px");
	xnombre.setAttribute("crop","end");
			
	
	xlistitem.appendChild( xcod);
	xlistitem.appendChild( xnombre);
	xlistitem.appendChild( xtalla);
	xlistitem.appendChild( xcolor);
	xlistitem.appendChild( xprecio );
	xlistitem.appendChild( xeur );		     	
	
	xlistadoProductos.appendChild( xlistitem );
	
	prodlist_tag[iprod] = xlistitem;
	prodlist[iprod++] = codigo;	 	
}


function unescapeHTML(codigoHtml) {
	var s = new String(codigoHtml);
	s = s.replace (/&amp;/g, "&");
	s = s.replace (/&Ntilde;/g, "\xf1");
    s = s.replace (/&ntilde;/g, "\xf1");
	return s;

    /*var div = document.createElement('div');
    div.innerHTML = codigoHtml;
    
    if (div.innerText)
      return div.innerText;
    
    return div.childNodes[0] ? div.childNodes[0].nodeValue : codigoHtml;*/
}
  
  
//Funcion compacta para crear articulo
function tA(codigo,Lnombre,referencia,centimoprecio,impuesto,LTalla, LColor, descuento,idmodisto,nombre2){	
	//Traduce desde lex a normal.
	
	var talla = (LTalla)?L[LTalla]:"";		
	var color = (LColor)?L[LColor]:"";
	var nombre = (Lnombre)?L[Lnombre]:"";
	
	//Funcion "larga" que no acepta el uso de lexers
	tAL(codigo,nombre,referencia,centimoprecio,impuesto,talla, color, descuento,idmodisto,nombre2);		
}


function tAL(codigo,Nombre,referencia,centimoprecio,impuesto,Talla, Color, descuento,idmodisto,nombre2){
	//No acepta lexers
	if (!codigo)return;
	
	codigo = new String(codigo);	
	
	if (pool.Existe( codigo.toUpperCase() )){
		//Ya tenemos este producto listado
		//alert("ya existe este prod en mi lista");
		return;
	}	
	
	var a = new Object();
	codigo = new String(codigo);
	codigo = codigo.toUpperCase();
	a.codigobarras 	= codigo;
	//a.nombre 		= nombre; ahora es un lex
	a.nombre2 		= nombre2;	
	a.referencia 	= referencia;
	a.descuento 	= descuento;
	a.precio 		= (centimoprecio/100).toFixed(2);
	a.impuesto 		= impuesto;
	a.idmodisto 	= idmodisto;
	a.esArreglo 	= (idmodisto)?1:0;
	
	a.talla = Talla;		
	a.color = Color;
	a.nombre = Nombre
	
	a.nombre = unescapeHTML(a.nombre);
	a.color = unescapeHTML(a.color);
	a.talla = unescapeHTML(a.talla);	
		
	//log("Creando articulo "+a.nombre+" - "+codigo);	 	 
	 
	pool.add(codigo,a);
	 
	 /* Mantiene tablas cruzadas que ayudan en las busquedas */
	 
	var refstr = ref2code[referencia];
	if (refstr)	 ref2code[referencia] = refstr + "," + codigo;
	else 	 ref2code[referencia] = codigo;
	 	
	if ( prodCod[iprodCod] != codigo ) {
		prodCod[iprodCod] = codigo;
		iprodCod ++;
	}		
}

function FormateComoDescuento(valor) {
	if (!valor || valor ==0 || valor =="0")
		return "  ";//Especial para no hacer tan presente el descuento, dado que la mayor parte del 
			// tiempo no es bonito ni relevante.

	return valor + " %";
}

tpv.Compra = function (codigo,nombre,referencia,precio,impuesto,unidades,talla,color,descuento,idmodisto,nombre2) {
	var nuevo = null;
	if (!pool.ExisteTicket(codigo)) {
		pool.CreaTicket(codigo);
		nuevo = 1;
	}	
	
	ticket[codigo].unidades += unidades;	
	
	if (nuevo) { //agnadimos	
		var xlistadoTicket = id("listadoTicket");				
		
		var xcod = document.createElement("label");
		xcod.setAttribute("value",ticket[codigo].referencia);		
		xcod.setAttribute("id","tic_referencia_"+codigo);				
		
		var xnombre = document.createElement("label");
		xnombre.setAttribute("value",ticket[codigo].nombre);	
		
		var xunid = document.createElement("label");
		xunid.setAttribute("value",parseInt(ticket[codigo].unidades));			

		var xprecio = document.createElement("label");
		xprecio.setAttribute("value",formatDinero(ticket[codigo].precio));	
		var xeur = document.createElement("label");xeur.setAttribute("value","EUR");			
		
		var xtalla = document.createElement("label");
		xtalla.setAttribute("value",talla);	
		xtalla.setAttribute("id","tic_talla_"+codigo);
		
		var xcolor = document.createElement("label");
		xcolor.setAttribute("value",color);		
		xcolor.setAttribute("id","tic_color_"+codigo);		
				
		xnombre.setAttribute("id","tic_nombre_"+codigo);
		xnombre.setAttribute("crop","end");
		xnombre.setAttribute("style","width: 100px");
									
		var ximpuesto = document.createElement("label");
		ximpuesto.setAttribute("value",impuesto+ " %");		

		var xdescuento = document.createElement("label");
		xdescuento.setAttribute("value",FormateComoDescuento(descuento));		

		if(idmodisto){
			var xmodisto = document.createElement("label");
			xmodisto.setAttribute("value",idmodisto);
			xmodisto.setAttribute("id","tic_modisto_"+codigo);							
		}

		var xnombre2 = document.createElement("label");
		xnombre2.setAttribute("value",nombre2);	
		xnombre2.setAttribute("id","tic_nombre2_"+codigo);
		xnombre2.setAttribute("collapsed","true");		

		var xlistitem = document.createElement("listitem");	
		xlistitem.setAttribute("id","tic_"+codigo);
		xunid.setAttribute("id","tic_unid_"+codigo);
		
		xprecio.style.align ="right";
		xprecio.style.textAlign ="right";
		xprecio.setAttribute("id","tic_precio_"+codigo);

		ximpuesto.style.align ="right";
		ximpuesto.style.textAlign ="right";
		ximpuesto.setAttribute("id","tic_impuesto_"+codigo);
		
		xdescuento.style.align ="right";
		xdescuento.style.textAlign ="right";
		xdescuento.setAttribute("id","tic_descuento_"+codigo);



		xlistitem.appendChild( xcod);
		xlistitem.appendChild( xnombre);
		xlistitem.appendChild( xtalla);
		xlistitem.appendChild( xcolor);				
		xlistitem.appendChild( xunid );
		xlistitem.appendChild( xdescuento );
		xlistitem.appendChild( ximpuesto );
		xlistitem.appendChild( xprecio );
		xlistitem.appendChild( xeur );		
		xlistitem.appendChild( xnombre2 );				
		
		if(xmodisto) xlistitem.appendChild( xmodisto );	
		
		xlastArticulo = xlistitem;//recordamos el ultimo articulo añadido
		     		
		xlistadoTicket.appendChild( xlistitem );	

		Blink("tic_"+codigo);
	} else {
		var name = "tic_unid_"+codigo
		var xunid  = id(name);
		if (xunid) {
			xunid.setAttribute("value", ticket[codigo].unidades);
			Blink(name);
		}
	}		
	
	RecalculoTotal();//Redibuja el nuevo TOTAL

	//log("Comprando "+ticket[codigo].unidades +" de "+ticket[codigo].nombre);
}

tpv.AddCarrito = function (codigobarras,unidades) {
 
	if (!pool.Existe(codigobarras)) {	
		//	alert("fallo de compra, no encontrado " + codigobarras);
    	return false;
	}
 
 	setImagenProducto(codigobarras);
 
	pool.select(codigobarras); 
	this.Compra( codigobarras, pool.get().nombre, pool.get().referencia, pool.get().precio, pool.get().impuesto,unidades,pool.get().talla, pool.get().color, pool.get().descuento,0);
	RecalculoTotal();
	return true;
}


//NOTA: Esto esta bien aqui?
var L = new Array();
L[1] = "unica";
L[2] = "unico";
L[3] = "L";
L[4] = "negro";

function Nombre2Lex(nombre){
	var len = L.length;
	for(var t=0;t<len;t++){
		if (L[t]==nombre) return nombre;
	}
	L[t] = nombre;
	return t;
}

tpv.AddArreglo = function ( codigobarras, arregloid, nombre, referencia, precio,impuesto, idmodisto ) {
	var talla="",color="";

	//Los datos de talla y color los cogemos del producto original
	if (pool.Existe(codigobarras)){
		pool.select(codigobarras); 
		talla		= pool.get().talla;
		color		= pool.get().color;
		nombre2 	= pool.get().nombre;
	}
	
	if (!pool.Existe(arregloid)) {	
		//Creamos producto imaginario 
		tA(arregloid,Nombre2Lex(nombre),referencia,precio*100,impuesto,0,0,0,idmodisto,nombre2)
	//	tA(codigo,nombre,referencia,precio,impuesto,LTalla, LColor, descuento,idmodisto){
	}
 
	//Los datos de talla y color los cogemos del producto original
	pool.select(arregloid); 	
	arreglotex	= pool.get().nombre;
	referencia 	= pool.get().referencia;
	precio		= pool.get().precio;
	impuesto	= pool.get().impuesto;
	impuesto	= pool.get().impuesto;			
	descuento	= pool.get().descuento;		
	nombre2		= pool.get().nombre2;	
	
	this.Compra( arregloid, arreglotex, referencia, precio, impuesto, 1 , talla, color, descuento, idmodisto,nombre2);	
	
	RecalculoTotal();
}


tpv.getDatosFromCB	= function( cb ){
	var res = new Array();	
	if (!pool.Existe( cb ) ){
		return	res;
	}
	
	res["nombre"]	= pool.get().nombre;
	res["talla"]	= pool.get().talla;
	res["color"]	= pool.get().color;
	return res;
}



/*+++++++++++++++++++++++++ Mensajes Demonio +++++++++++++++*/

var buzonMensajes = id("buzon-mensajes");

var AjaxMensajes = new XMLHttpRequest();
var ocupado = 0;
var ultimoLeido = 0;

function insertAfter(parent, node, referenceNode) {
    parent.insertBefore(node, referenceNode.nextSibling);
}

function AgnadirMensaje(IdMensaje, Titulo, Status ){
	IdMensaje = parseInt( IdMensaje );
	if (!IdMensaje)
		return;	
	if (IdMensaje> ultimoLeido)
		ultimoLeido = IdMensaje; 
	
	var imagen;
	var xmen = document.createElement("listitem");
	xmen.setAttribute("class","listitem-iconic");
	if ( Status == "Normal" ){
		imagen = "img/mensaje16.gif";
	} else {
		imagen = "img/mensajealerta16.gif";
	}
	xmen.setAttribute("image",imagen);
	xmen.setAttribute("label", Titulo);
	xmen.setAttribute("ondblclick", "EncargarLecturaMensaje('"+IdMensaje+"')" );
	//buzonMensajes.appendChild( xmen );
	//id("guardianMensajes").insertBefore(xmen)	
	//buzonMensajes.insertAfter(xmen, id("guardianMensajes") );
	insertAfter( buzonMensajes, xmen, id("guardianMensajes") );	
}

function ProcesarNuevosMensajes( rawtext){
	var dato, row;	
	var filadatos = rawtext.split("\n");

	for(var t=0;t<filadatos.length;t++){
		dato	= filadatos[t];
		row = dato.split("'");	

		AgnadirMensaje( row[0], row[1], row[2]);
	}	
}

function RececepcionMensajes(){
	if (!AjaxMensajes) {
		AjaxMensajes = new XMLHttpRequest();
		return;
	}

	ocupado = 0;
	if (AjaxMensajes.readyState==4) {
	
		if (AjaxMensajes)
			if (AjaxMensajes.status=="200")
				peticionesSinRespuesta = 0;
				//Si responden, es que estamos online, por tanto "hay respuesta"
				// y borramos el acumulativo de peticiones sin respuesta. 

		var rawtext = AjaxMensajes.responseText;			
		//alert(rawtext);
		//alert(AjaxMensajes.status);
		ProcesarNuevosMensajes(rawtext);		
	}
}

//NOTA:
//  El demonio se ejecutara cada X segundos y enviara una peticion
// el servidor, para leer si hay mensajes nuevos.
// Ademas mantiene una variable numFallosConexion, que se incrementa con 
// cada peticion, y se anula con cada respuesta. Si muchas peticiones no reciben 
// respuesta, es que hemos perdido la conexion con el servidor. Avisaremos al usuario
// y lo protegeremos de problemas.


setTimeout("Demonio_Mensajes()",5000);

var  peticionesSinRespuesta = 0;
var test1 = 0;

function Demon_CargarNuevosMensajes(){
	//alert("cargando!1...");
	if (!AjaxMensajes)
		AjaxMensajes = new XMLHttpRequest();	
	
	//Peticiones realizadas
	peticionesSinRespuesta = peticionesSinRespuesta +1;			
		
	var url = "modulos/mensajeria/modbuzon.php?modo=leernuevos&IdUltimo=" + ultimoLeido;
	url = url + "&desdelocal="+escape(  Local.nombretienda );	

	AjaxMensajes.open("POST",url,true);
	AjaxMensajes.onreadystatechange = RececepcionMensajes;
	AjaxMensajes.send(null)
	
	setTimeout("Demon_CargarNuevosMensajes()",8000);//8000
	
	ActualizacionEstadoOnline();
}

function Demonio_Mensajes(){
	try {
	if (!AjaxMensajes)
		AjaxMensajes = new XMLHttpRequest();	
	} catch(e) {
		return;
	}

	var url = "modulos/mensajeria/modbuzon.php?modo=hoy";
	
	AjaxMensajes.open("POST",url,true);
	AjaxMensajes.onreadystatechange = RececepcionMensajes;
	AjaxMensajes.send(null)	
	
	setTimeout("Demon_CargarNuevosMensajes()",5000);
}

function EncargarLecturaMensaje(IdMensaje){
	IdMensaje = parseInt(IdMensaje);
	var url = "modulos/mensajeria/modbuzon.php?modo=CargarMensaje&IdMensaje="+IdMensaje;

	AjaxMensajes.open("POST",url,true);
	AjaxMensajes.onreadystatechange = CargarMensaje;
	AjaxMensajes.send(null)
}

function CargarMensaje(){

	ocupado = 0;
	if (AjaxMensajes.readyState==4) {
		var rawtext = AjaxMensajes.responseText;	
		if (rawtext=="error"){
			return;		
		}
		RecibirMensajeCompleto(rawtext);				
	}
}

function RecibirMensajeCompleto(rawtext){
	if (rawtext=="error")
		return;		
	var row = rawtext.split("'");	
		
	var IdMensaje = parseInt(row[0]);
	if (!IdMensaje) return;
	
	var Titulo	= row[1];
	var Status	= row[2];
	var Texto	= row[3];
	VisualizarMensaje( IdMensaje, Titulo,Status, Texto);
}

function VisualizarMensaje(IdMensaje, Titulo, Status,Texto ){
	IdMensaje = parseInt( IdMensaje );
	if (!IdMensaje)
		return;	
	
	id("tituloVisual").value = Titulo;		
	id("textoVisual").value = Texto;
	
	mensajesModoLeer();
}

function mensajesModoLeer(){
	var mensajeArea =  id("modoMensajes");
	mensajeArea.setAttribute("selectedIndex",1);
}

function mensajesModoRecepcion(){
	var mensajeArea =  id("modoMensajes");
	
	id("tituloVisual").value = "";
	id("textoVisual").value = "";
	mensajeArea.setAttribute("selectedIndex",0);	
}

function ToggleMensajes(){
	var mensajeArea =  id("modoMensajes");
	
	var modo = mensajeArea.getAttribute("selectedIndex");
	
	if (modo==2){
		mensajeArea.setAttribute("selectedIndex",0);
	} else {
		mensajeArea.setAttribute("selectedIndex",2);
	}
}

function EnviarMensajePrivado(){
	var xrequest = new XMLHttpRequest();
	var resultado;
	var url = "";
	
	var local = parseInt(id("localDestino").value);
	if (!local) return;
	var titulo = id("tituloNuevoMensaje").value;
	var texto = id("cuerpoNuevoMensaje").value;
		
	url = "modulos/mensajeria/modbuzon.php?modo=avisonotaprivada";
	
	var data = "&titulo="+escape( titulo );
	data = data + "&cuerpo="+escape( texto + "\n( "+ Local.nombreDependiente + " )" );
	data = data + "&idestino="+ escape( local );
	
	url = "modulos/mensajeria/modbuzon.php?modo=avisonotaprivada";
	xrequest.open("POST",url,false);
	xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	
	try {
		xrequest.send(data);
		resultado = xrequest.responseText;
	} catch(e) {
		//NOTA: posiblemente no tenemos conexion.
		resultado = false;	
	}

	if ( resultado == "OK" ) 
		alert(po_mensajeenviado);
	else 
		alert(po_servidorocupado);

	ToggleMensajes();	
}

var esGraficoConectado = true;

//INFO: imagen de prohibido, para utilizar en señalizar conexion perdida
var urlprohibido = "data:image/gif;base64,R0lGODlhDAAMAMQAAPpbW/8AAPPz8/8zM/XFxfednf9aWveUlP4PD/iJifTl5flycv0iIvTV1f4KCv9mZvenp/4XF////wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAUUABIALAAAAAAMAAwAAAVHoCAeQxAMhygqpekOisiYAAQB5iAcppOoEBMp4MgRRLhW4eF6KIIuiIDQYixyAYAqgQhETryAVNRopVq1W27Vcp1iqiFYFQIAOw==";

function ActualizacionEstadoOnline(){
	
	var srcconectado = "img/network.png";
	
	if ( peticionesSinRespuesta >= 3 ){	
		//Con 3 fallos o mas, asumimos problema de conexion.	
		id("bolaMundo").src = urlprohibido;	
	} else {
	
		if ( id("bolaMundo").src != srcconectado) {
			id("bolaMundo").src = srcconectado;
		}
	
	}
} 




/*=========== ARREGLOS LISTA   ==============*/	

function VerArreglos(){
	//return;	
	id("panelDerecho").setAttribute("collapsed","true");

	var estado = 	document.getElementById("modoVisual").selectedIndex;	
	estado = (estado == 8)?0:8;
	
	id("modoVisual").setAttribute("selectedIndex",estado);	
}



var indiceListadoModisto = 0;

//recibe YYYY-MM-DD, genera DD-MM-YYYY
function toFormatoFecha(fecha){
	if (fecha== "0000-00-00" || !fecha){
		return "00-00-0000";			
	}
	if (fecha=="hoy"){
		var hoy = new Date();	
		return (hoy.getDate())+"-"+(hoy.getMonth()+1)+"-"+(hoy.getYear()+1900);
	}
	
	datosfecha = fecha.split("-");
	
	return datosfecha[2] + "-" + datosfecha[1] + "-"+datosfecha[0];  		
}

function ListadoModistos(){
	//VaciarBusquedaVentas();
	var idmodisto = id("ModistoListaArreglos").value;
	var statusArreglo = id("StatusListaArreglos").value;
	var ticket = id("TicketListaArreglos").value;	

	var url = "services.php?modo=mostrarArreglos"
		+ "&idmodisto=" + escape(idmodisto) 
		+ "&status=" + escape(statusArreglo) 
		+ "&ticket=" + escape(ticket);
	
	var obj = new XMLHttpRequest();

	obj.open("POST",url,false);//POST en lugar de GET porque puede haber cambio de estado
	obj.send(null);
	
	var tex = "";
	var cr = "\n";
	
	var vendedor,serie,num,fecha,total,pendiente,estado,IdFactura;
	var node,t,i;
	
	
	if (!obj.responseXML)
		return alert(po_error);		
	if (!obj.responseXML.documentElement)
		return alert(po_error);
	
	var xml = obj.responseXML.documentElement;
		
	VaciarListadoArreglos();
	
	for (i=0; i<xml.childNodes.length; i++) {
		node = xml.childNodes[i];
		if (node){
			t = 0;
			modisto 		= node.getAttribute("NombreModisto");
			producto 		= node.getAttribute("DescripcionProducto");
			arreglos 		= node.getAttribute("Arreglos");
			nticket			= node.getAttribute("NTicket");
			statuscosica	= node.getAttribute("Status");
			enviado			= toFormatoFecha(node.getAttribute("FechaEnvio"));
			recibido		= toFormatoFecha(node.getAttribute("FechaRecepcion"));	
			ident			= node.getAttribute("IdTbjoModisto");	
			CrearFilaDeModistosListado( modisto, producto, arreglos, nticket, statuscosica, enviado, recibido, ident);					
		}					
	}

}

function CrearFilaDeModistosListado(modisto, producto, arreglos, nticket, statuscosica, enviado, recibido, ident ) {
	var xlistadoProductos = id("busquedaListaArreglos");
		
	if (!xlistadoProductos)
		return alert("Error de proceso, recargue la TPV");
		
	var xmodisto = document.createElement("label");
	xmodisto.setAttribute("value",modisto);
	
	var xproducto = document.createElement("label");
	xproducto.setAttribute("value",producto);	
	
	var xarreglos = document.createElement("label");
	xarreglos.setAttribute("value",arreglos);	
	xarreglos.setAttribute("crop","end");	
	
	var xnticket = document.createElement("label");
	xnticket.setAttribute("value",nticket);					
	
	var xstatuscosica = document.createElement("label");
	xstatuscosica.setAttribute("value",statuscosica);	
	xstatuscosica.setAttribute("id","arreglo_status_" + ident );			

	var xenviado = document.createElement("label");
	xenviado.setAttribute("value",enviado);	
	xenviado.setAttribute("id","arreglo_enviado_" + ident );				

	var xrecibido = document.createElement("label");
	xrecibido.setAttribute("value",recibido);
	xrecibido.setAttribute("id","arreglo_recibido_" + ident );				
		
	var xlistitem = document.createElement("listitem");		
	xlistitem.setAttribute("id","listadomodarreglos_" + indiceListadoModisto );		
	indiceListadoModisto++;	
	xlistitem.setAttribute("value",ident);		
	
	xlistitem.appendChild( xmodisto );
	xlistitem.appendChild( xproducto );
	xlistitem.appendChild( xarreglos );
	xlistitem.appendChild( xnticket );	
	xlistitem.appendChild( xstatuscosica );
	xlistitem.appendChild( xenviado );
	xlistitem.appendChild( xrecibido );		     	
	
	xlistadoProductos.appendChild( xlistitem );
}

function VaciarListadoArreglos(){
	//alert("vaciar: " + indiceListadoModisto);

	var lista = id("busquedaListaArreglos");
	
	for (var i = 0; i < indiceListadoModisto; i++) { 
		kid = id("listadomodarreglos_"+i);					
		if (kid)	lista.removeChild( kid ); 
	}
	indiceListadoModisto = 0;
}

//pregunta por el dia de hoy
function hoyPrompt(mensaje){
	return prompt("Fecha?",toFormatoFecha("hoy"));
}


function ListadoArreglosSeleccionadoStatus(nuevoestado){
	//
	var xlista = id("busquedaListaArreglos");

	if (!xlista) return alert("e:0");
	if (!xlista.selectedItem) return;
		
	var ident = xlista.selectedItem.value;
	
	var xstatus = id("arreglo_status_"+ident);
	if (!xstatus) return alert("error interno, reintente");
	
	xstatus.setAttribute("value",nuevoestado);
	
	var xenvia = id("arreglo_enviado_"+ident);
	var xrecibe = id("arreglo_recibido_"+ident);	
		
	var diacero = toFormatoFecha(0);
	var hoy		= toFormatoFecha("hoy");
	var newfecha ;
	
	switch(nuevoestado){
		case "Enviado"://fechaenvio hoy,. recepcion=0				
			hoy = hoyPrompt();
			xenvia.setAttribute("value",hoy);		
			xrecibe.setAttribute("value",diacero);			
			break;
		case "Recibido":
		case "Recogido"://Recepcion hoy		
			hoy = hoyPrompt();
			xrecibe.setAttribute("value",hoy);
			break;
		case "Pdte Envio"://fechas a cero 
			xrecibe.setAttribute("value",diacero);
			xenvia.setAttribute("value",diacero)
			break;	
		default:
		//	prompt(nuevoestado);
			break;
	}
	
	var url = "services.php?modo=setStatusTrabajoModisto"
		+ "&idtrabajo=" + escape(ident) 
		+ "&status=" + escape(nuevoestado) 

	var obj = new XMLHttpRequest();

	obj.open("GET",url,true); obj.send(null);					
}


/*=========== ARREGLOS LISTA   ==============*/	



/*=========== LISTADOs   ==============*/	
function VerListados(){
	id("panelDerecho").setAttribute("collapsed","true");
	id("modoVisual").setAttribute("selectedIndex",9);	
}
/*=========== LISTADOs   ==============*/	


/* Ajusta signos en la vista del ticket */

function TicketAjusta(){
	var codigo,unidades;
	var agnadidos = new Array();	
	
	for (var t=0;t<iticket;t++) {
		codigo = ticketlist[t];
		if ( !agnadidos[codigo] && id( "tic_" + codigo )  ) {	
			//txt += " " + id("tic_"+codigo)
			unidades = id("tic_unid_" + codigo).value;	
			unidades = ConvertirSignoApropiado( unidades );

			id("tic_unid_" + codigo).value = unidades;
			
			agnadidos[codigo] = true;
		}		
	}		
}

/*=========== MULTIPAGOS ==============*/	

var modoMultipago = false;


function LimpiarMultipagos(){
	id("peticionEfectivo").value = 0;
	id("peticionBono").value = 0;
	id("peticionTarjeta").value = 0;
	id("peticionTransferencia").value = 0;		

	modoMultipago = true;//forzamos modo activado
	ModoMultipago();//Cambiamos a apagado		
}



function ModoMultipago(){
	if(modoMultipago) {				
		id("Pagos_1").setAttribute("collapsed","true");
		id("Pagos_2").setAttribute("collapsed","true");
		id("Pagos_3").setAttribute("collapsed","true");
		//id("Pago_4").setAttribute("collapsed",false);	
		id("Pago_Modo").setAttribute("collapsed","false");	
		//id("peticionEntrega").setAttribute("readonly","true");
		//id("peticionEntrega").setAttribute("style","background-color: white!important");				
		id("Fila-peticionEntrega").setAttribute("collapsed","false");		
		modoMultipago  = false;
	} else {
		id("Pagos_1").setAttribute("collapsed","false");
		id("Pagos_2").setAttribute("collapsed","false");
		id("Pagos_3").setAttribute("collapsed","false");
		//id("Pago_4").setAttribute("collapsed",false);	
		id("Pago_Modo").setAttribute("collapsed","true");	
		//id("peticionEntrega").setAttribute("readonly","false");
		//id("peticionEntrega").setAttribute("style","background-color: -moz-dialog!important");		
		id("Fila-peticionEntrega").setAttribute("collapsed","true");				
		modoMultipago  = true;
	}
	ActualizaPeticion();
}

var modoPersonalizado=0;

function ModoPersonalizado(){
	if(modoPersonalizado) {				
		//id("Admintic_0").setAttribute("collapsed","false");
		id("Admintic_1").setAttribute("collapsed","true");
		id("Admintic_2").setAttribute("collapsed","true");	
		id("Admintic_3").setAttribute("collapsed","true");
		modoPersonalizado  = false;
	} else {
		//id("Admintic_0").setAttribute("collapsed","true");
		id("Admintic_1").setAttribute("collapsed","false");
		id("Admintic_2").setAttribute("collapsed","false");			
		id("Admintic_3").setAttribute("collapsed","false");
		modoPersonalizado  = true;
	}
	ActualizaPeticion();
}

/*=========== MULTIPAGOS ==============*/	

/*=========== FX   ==============*/	

function MostrarAjax(){
	//id("ajax-icon").setAttribute("src","img/ajax-loader.gif");
	//id("download-icon").setAttribute("src","img/mundo1.gif");
}

function OcultarAjax(){
	//id("ajax-icon").setAttribute("src","");
	//id("download-icon").setAttribute("src","");
}

/*=========== FX   ==============*/	

/*=========== UTIL   ==============*/


function CleanCB(cadena){
    var cad = new String(cadena);
	cad = cad.toUpperCase();
	cad = trim(cadena);
	return cad;
}

function CleanRef(cadena){
    var cad = new String(cadena);
	cad = cad.toUpperCase();
	cad = trim(cadena);
	return cad;
}




function trim(cadena) { 
	cadena = new String(cadena);
	for(i=0; i<cadena.length; ) { 
		if(cadena.charAt(i)==" " || cadena.charAt(i)=="\t" ) 
			cadena=cadena.substring(i+1, cadena.length); 
		else 
			break; 
	} 
	for(i=cadena.length-1; i>=0; i=cadena.length-1) { 
		if(cadena.charAt(i)==" " || cadena.charAt(i)=="\t" ) 
			cadena=cadena.substring(0,i); 
		else 
			break; 
	} 
	return cadena; 
}


/*=========== UTIL   ==============*/
