

/*=========== CREAR TICKET USO  ==============*/

/* Crea un ticket desde el formulario de pantalla */
function t_CrearTicket(esCopia) {
	var ticket = new Ticket();


	/* Iniciamos un nuevo ticket, trae num del servidor, etc */
	ticket.IniciaNumeroDeSerie(ModoDeTicket);//trae del servidor etc
	
	/* Dinero entregado, y quien es el dependiente */
	
	if( modoMultipago ){
		ticket.multipago = true;		
		ticket.entregaCambio 	= parseFloat(id("peticionCambioEntregado").value);		
		//NOTA: suponemos que lo efectivo entregado es el dinero que queda en caja pasadas operaciones
		// de cambio.
		ticket.entregaEfectivo = parseFloat(CleanMoney(id("peticionEfectivo").value));
		ticket.entregaTarjeta = parseFloat(CleanMoney(id("peticionTarjeta").value));
		ticket.entregaBono = parseFloat(CleanMoney(id("peticionBono").value));
		ticket.setEntregado( ticket.entregaEfectivo + ticket.entregaTarjeta + ticket.entregaBono  );
	} else {
		ticket.multipago = false;
		var modo =  id("modoDePagoTicket").value;
		ticket.entregaEfectivo = 0;
		ticket.entregaTarjeta = 0;
		ticket.entregaBono = 0;
		ticket.entregaCambio 	= parseFloat(id("peticionCambioEntregado").value); 
								
		switch(parseInt(modo)){
			case 0://EFECTIVO
				ticket.entregaEfectivo = parseFloat(CleanMoney(id("peticionEntrega").value));
				break;
			case 1://TARJERA
				ticket.entregaTarjeta = parseFloat(CleanMoney(id("peticionEntrega").value));
				//alert('entregado con tarjeta:'+ticket.entregaTarjeta);
				break;
			case 5:///BONO
				ticket.entregaBono = parseFloat(CleanMoney(id("peticionEntrega").value));			
				break;
			default:
				ticket.entregaEfectivo = parseFloat(CleanMoney(id("peticionEntrega").value));
				//alert('pago generico:'+modo);
				break;			
		}
				
		ticket.setEntregado( ticket.entregaEfectivo + ticket.entregaTarjeta + ticket.entregaBono  );	
	}
	
	ticket.setDependiente(Local.nombreDependiente);//id("tDependiente").value
	ticket.SetModoPago( parseInt(id("modoDePagoTicket").value) );
	
	
	if (!esCopia) {
		//ATENCION: avanzamos numero de ticket		
		Local.numeroDeSerie = Local.numeroDeSerie + 1;
	}	
			
	var res = new Object();	
	res.text_data = ticket.generaTextTicket();
	res.post_data = ticket.generaPostData();
	return res;
}


/* Recupera un ticket, desde datos XML. */

function t_RecuperaTicket(IdFactura){
	var res = Raw_t_RecuperaTicket(IdFactura);
	
	top.TicketFinal = window.open(EncapsrTextoParaImprimir(res.text_data),"Consola Ticket","width=400,height=600,scrollbars=1,resizable=1,dependent=yes","text/plain");		
}


function Raw_t_RecuperaTicket(IdFactura) {
	var ticket = new Ticket();
	
	var newDetalles 	= t_BuscarDetallesVentaAntiguos(IdFactura);
	var newGlobal 		= t_BuscaGlobalesFactura(IdFactura);
	
	ticket.SetModoRemoto( IdFactura, newGlobal, newDetalles );	
	ticket.setDependiente( newGlobal.Dependiente );
	ticket.setEntregado( newGlobal.DineroEntregado );	
	ticket.SetModoPago( newGlobal.ModoDePago );
	ticket.SetAlfaNumFactura( newGlobal.serie + "-" + newGlobal.num );
	
	var res = new Object();	
	res.text_data = ticket.generaTextTicket();
	
	//alert( res.text_data );
	
	return res;
}


/* Carga de datos del ticket */

var GlobalNewTicket = new Object();

function CargaVenta(vendedor,serie,num,fecha,total,pendiente,estado,IdFactura,nombreCliente){
	GlobalNewTicket.Dependiente = vendedor;
	GlobalNewTicket.serie 		= serie;
	GlobalNewTicket.num 		= num;
	GlobalNewTicket.fecha 		= fecha;
	GlobalNewTicket.total 		= total;
	GlobalNewTicket.pendiente 	= pendiente;
	GlobalNewTicket.estado 		= estado;
	GlobalNewTicket.IdFactura 		= IdFactura;
	GlobalNewTicket.nombreCliente 	= nombreCliente;	
	GlobalNewTicket.DineroEntregado = 0;//TODO
	GlobalNewTicket.ModoDePago 	= 0;	//TODO
	
}

function t_BuscaGlobalesFactura(IdFactura) {
	GlobalNewTicket = new Object();

		
	RawBuscarVentas("","","","","",IdFactura,CargaVenta);
	return GlobalNewTicket;
}


/* Cargamos los datos de lineas del ticket */

var lineasTicketSombra = new Array();
function ColeccionarDetallesTicket(Referencia, Nombre,Talla, Color, Unidades, Descuento, PV, Codigo){	
	var prod = new Object();
	prod.referencia = Referencia;
	prod.nombre 	= Nombre;
	prod.talla 		= Talla;
	prod.color 		= Color;
	prod.unidades 	= Unidades;
	prod.descuento 	= Descuento;
	prod.precio 	= PV;
	prod.concepto 	= "";
	prod.idmodisto 	= 0;
	prod.codigo 	= Codigo;

	lineasTicketSombra[ lineasTicketSombra.length ] =  prod;	
}

function t_BuscarDetallesVentaAntiguos(IdFactura){
	lineasTicketSombra = new Array();			
	RawBuscarDetallesVenta(IdFactura, ColeccionarDetallesTicket);
	return lineasTicketSombra;
}



/*=========== CREAR TICKET USO  ==============*/


/*=========== IMPLEMENTACION TICKET ==============*/


/* Clase que representa un ticket*/

function Ticket(){
	/* si debe cargar los datos desde detallesSombra o desde tic */
	this.esTicketRemoto = false;
	
	
	this.numeroserie = 0;
	this.aportacionimpuestos = 0;
	this.DineroEntregado = 0;
	this.dependiente = "";
	
	this.TotalBase = 0;
	
	/* Al registrar un producto, añadimos aqui los datos a enviar al server
	 relativos a una fila de ticket*/
	this.datos_post_productos = "";
	this.datos_text_productos = "";
	this.cr = "\n";
	this.tab = "\t";
	
	/* LocalSombra es una indireccion de los datos del local, de modo que pueda ser
	  ..o bien el local real en el que estamos, y en el que trabajamos en vivo.
	  O bien un local "fantasma", el del ticket que estamos imprimiendo que 
	  podria ser en el futuro un local distinto del actual, pero que en cualquier 
	  caso tendra algunos datos distintos.
	  
	  Esto facilita el imprimir tickets antiguos.	
	 */
	this.LocalSombra = Local;
	
	/* como en localsombra.. */	

	this.LocalGlobal = Global;	
}

Ticket.prototype.SetModoRemoto = function ( IdFactura, newGlobal, newDetalles ){
	for (prop in newGlobal){
		this.LocalGlobal[prop] = newGlobal[prop];
	}
	this.detallesSombra = newDetalles;
	this.esTicketRemoto = true;
}
	
	
	
Ticket.prototype.SetModoPago = function (newmodopago){
	this.modopago = newmodopago;	
}

Ticket.prototype.getEntregado = function(){
	return this.DineroEntregado;
}

//NOTA: para forzar numero correcto de factura 
Ticket.prototype.SetAlfaNumFactura = function(alfanum){
	this.alfanumFactura = alfanum;
}

function getSpaces(num){
	var salida="";
	for(t=0;t<num;t++){
		salida += " ";
	}
	return salida;
}



/* Preparamos la cadena de texto plano que representa el ticket */
Ticket.prototype.generaTextTicket = function(){
	var cambio;
	var salida = "";
	var cr = this.cr;

	var len = new String(Local.Negocio).length;
	pad = len - 19;
			
	salida += "*** " + Local.Negocio + getSpaces(pad) + "***" + cr;
	
	len = new String(Local.promoMensaje).length;
	pad = len - 19;
	
	salida += "*** " + Local.promoMensaje + getSpaces(pad) + "***"+cr+cr;
	
	salida += this.Colum( new Array( po_ticketde , this.LocalSombra.nombretienda)) + cr;	
	salida += this.Colum( new Array( po_numtic , this.alfanumFactura ));	
	salida += this.TexModoTicket(ModoDeTicket);		
	
	salida += this.Colum( new Array(po_unid,po_precio,po_descuento,po_Total));	
	salida += this.Linea();
	salida += this.GenerarTextoProductos();
	salida += this.Linea();
	
	cambio = this.genCambio();
	
	salida += this.Colum( new Array(po_TOTAL,"", formatDinero(this.TotalBase)) );
	salida += this.Colum( new Array(po_Entregado,"", formatDinero(this.getEntregado())) );
	salida += this.Colum( new Array(po_Cambio,"", formatDinero(cambio)) );
	salida += this.Linea();
	
	salida += this.Colum( new Array(po_desgloseiva ,formatDinero(this.aportacionimpuestos)) );		
	
	var modopago = this.modopago;
	if (modopago>0)
		salida = salida + po_mododepago + " " + modospago[modopago] + cr;
		
	var po_15diaslimite_resultante = new String( po_15diaslimite );
	po_15diaslimite_resultante = po_15diaslimite_resultante.replace(/\\n/,cr);			
	po_15diaslimite_resultante = po_15diaslimite_resultante.replace("%d",this.LocalSombra.diasLimiteDevolucion);	
		
	salida += this.Linea();
	salida += this.Colum( new Array( po_leatendio + " ",  this.dependiente) );
	salida += this.Fecha() 		+ cr;
	salida += po_15diaslimite_resultante 	+ cr;
	salida += cr + this.LocalSombra.motd 		+ cr;
	
	return salida;
}

/* Construimos el numero de ticket, consultando la serie con el servidor */
Ticket.prototype.IniciaNumeroDeSerie = function (Modo){
	/* Actualiza el numero de serie de ticket desde el servidor, solo por si acaso */
	//NOTA: se usa para construir alfanumFactura
	//NOTA: se actualiza al siguiente numero con cada impresion
	this.LocalSombra.numeroDeSerie = this.TraerDelServidorNumeroDeSerie();
	
	//Local.numeroDeSerie: numero actual calculado por la tpv
	//this.numeroserie: dato que se enviara al servidor			
	
	this.numeroserie  = Local.numeroDeSerie;

	/* NOTA:*/
	/* Construye "numero" de factura completa, como mezcla num y letras es 'alfanumfac' */
	/* Se tiene en cuenta el tipo de ticket, normal, cesion, etc.. */
	//NOTA: es el dato que aparecera impreso.
	this.alfanumFactura = this.AlfaNumFac( this.LocalSombra.numeroDeSerie ,Modo);
	
	if (modoPersonalizado){
		/*NOTA:
		En el modo personalizado se permite especificar el proximo numero de serie y 
		de factura. */
		var serieForzada = id("ajusteSerieTicket").value;
		var numeroForzado = id("ajusteNumeroTicket").value;
		
		//No se tiene en cuenta cesion, etc.. todo forzado.
		this.alfanumFactura = serieForzada + "-"+numeroForzado;
		//this.LocalSombra.numeroDeSerie = numeroForzado;	
		Global.fechahoy = id("ajusteFechaTicket").value;
	}
	
	
}

Ticket.prototype.setDependiente = function(nombre){
	this.dependiente = nombre;
}

Ticket.prototype.setEntregado = function (cantidadString){
	this.DineroEntregado = parseFloat(cantidadString);
}

/* Construimos la cadena post que servira para avisar al servidor de los datos del envio */
Ticket.prototype.generaPostData = function(){
	var data = "";
	var crd = "&";
	data += "entrega=" + escape(this.getEntregado()) + crd;
	data += "cambio="  + escape( this.entregaCambio ) + crd;

	data += "dependiente=" + escape(this.dependiente) + crd;	
	data += "serieticket=" + escape(Local.prefixSerieActiva) + crd;
	data += "numticket=" + escape(this.numeroserie) + crd;		

	data += "entrega_efectivo=" + escape(this.entregaEfectivo) + crd;		
	data += "entrega_bono=" + escape(this.entregaBono) + crd;			
	data += "entrega_tarjeta=" + escape(this.entregaTarjeta) + crd;			
	data += "entrega_cambio="   + escape( this.entragaCambio ) + crd;	
	
	data += this.datos_post_productos + crd;		
	
	data += "numlines=" + escape(iticket) + crd;		
	data += "UsuarioSeleccionado=" + UsuarioSeleccionado + crd;
	return data;
}

/* NOTA: ademas de recoger numero de serie, ajusta this.numeroserie 
*/
Ticket.prototype.TraerDelServidorNumeroDeSerie = function(){
	//numeroSiguienteDeFacturaParaNuestroLocal
	var moticket = ModoDeTicket;
	
	var	url = "services.php?modo=numeroSiguienteDeFacturaParaNuestroLocal&" + "&moticket=" + moticket + "&" + ApendRand();
	var xrequest = new XMLHttpRequest();
	
	xrequest.open("GET",url,false);
	xrequest.send(null);
	
	var resultado = parseInt(xrequest.responseText);	
	this.numeroserie = resultado;
	return resultado;	
}

Ticket.prototype.sizeOfTab = function(){
	return 8;
}	

Ticket.prototype.Linea = function() {
	return "---------------------------"  + this.cr;
}


Ticket.prototype.Colum = function(col) {
	var salida = "";
	
	if (!col)	return this.cr;
			
	for(var t=0;t<col.length;t++) {
		c = col[t];
		
		if (!c) c = this.tab;
		
		salida = salida + c;
		
		if ( c.length < this.sizeOfTab() ) {
			salida = salida + this.tab;
		} else {
			salida = salida + " ";
		}
	}	
	return salida + this.cr;		
}

Ticket.prototype.AlfaNumFac = function (Serie,Modo){
	switch( Modo ){
		case "interno":
			this.LocalSombra.prefixSerieActiva = this.LocalSombra.prefixSerieIN;break;
		case "cesion":
			this.LocalSombra.prefixSerieActiva = this.LocalSombra.prefixSerieCS;break;
		default:
		case "devolucion":
		case "venta":		
			this.LocalSombra.prefixSerieActiva =  this.LocalSombra.prefixSerie;break;			
	}
	
	return this.LocalSombra.prefixSerieActiva + "-" + Serie;			
}


Ticket.prototype.TexModoTicket = function(Modo){
	
	switch( Modo ){
		case "interno":
			return po_ticketarreglointerno + this.cr;
		case "cesion":
			return po_ticketcesionprenda + this.cr;
		case "devolucion":
			return po_ticketdevolucionprenda + this.cr;
		case "venta":		
			return "";
	}
}

Ticket.prototype.Fecha = function (){
	return "Fecha:"+ this.cr + this.LocalGlobal.fechahoy + this.cr;
}

Ticket.prototype.pgetIdModisto = function (){
	return this.productoSombra["idmodisto"];
}

/* Carga datos desde el ticket presente en el formulario */

Ticket.prototype.ProductoDato = function (key){
	var xdato 	= id(key + this.CodigoProductoSeleccionado);
	if(xdato)
		return xdato.value;
	return false;
}



Ticket.prototype.pgetUnidades = function (){
	return this.productoSombra["unid"];
}

Ticket.prototype.pgetPrecio = function (){
	return this.productoSombra["precio"];
}

Ticket.prototype.pgetDescuento = function (){
	return this.productoSombra["descuento"];
}

Ticket.prototype.pgetImpuesto = function (){
	return this.productoSombra["impuesto"];
}

Ticket.prototype.pgetReferencia = function (){
	return this.productoSombra["referencia"];
}

Ticket.prototype.pgetTalla = function (){
	return this.productoSombra["talla"];
}

Ticket.prototype.pgetColor = function (){
	return this.productoSombra["color"];
}

Ticket.prototype.pgetNombre = function (){
	return this.productoSombra["nombre"];
}
Ticket.prototype.pgetConcepto = function (){
	return this.productoSombra["concepto"];
}


Ticket.prototype.genSombraDesdeTic = function(){
 	var datos = new Array(); 	
    datos["unid"] 		= parseInt(this.ProductoDato("tic_unid_"));
	datos["precio"] 	= normalFloat(CleanMoney(this.ProductoDato("tic_precio_")));
	datos["descuento"] 	= normalFloat(this.ProductoDato("tic_descuento_"));
	datos["impuesto"] 	= normalFloat(CleanInpuesto( this.ProductoDato("tic_impuesto_") )/100.0);
	datos["referencia"] = this.ProductoDato("tic_referencia_");
	datos["talla"] 		= this.ProductoDato("tic_talla_");
	datos["color"] 		= this.ProductoDato("tic_color_");
	datos["nombre"] 	= this.ProductoDato("tic_nombre_");
	datos["concepto"] 	= this.ProductoDato("tic_nombre2_");
	datos["idmodisto"]	= this.ProductoDato("tic_modisto_");
	datos["codigo"]		= this.CodigoProductoSeleccionado;
	this.productoSombra = datos;
}

Ticket.prototype.genSombraDesdeRemota = function(){
 	var datos = new Array(); 	
  	var prod = this.detallesSombra[this.indiceProductoSombra];
  	
    datos["unid"] 		= prod.unidades;
	datos["precio"] 	= prod.precio;
	datos["descuento"] 	= prod.descuento;
	datos["impuesto"] 	= prod.impuesto;
	datos["referencia"] = prod.referencia;
	datos["talla"] 		= prod.talla;
	datos["color"] 		= prod.color;
	datos["nombre"] 	= prod.nombre;
	datos["concepto"] 	= prod.concepto;
	datos["idmodisto"]	= prod.idmodisto;
	datos["codigo"] 	= prod.codigo;
	this.productoSombra = datos;
}


Ticket.prototype.GenerarTextoProductos = function(){
	var maxproductos;//maximo de productos que podrian encontrarse
	var codigo, prod;

	if(this.esTicketRemoto) {
		maxproductos = this.detallesSombra.length;
	} else {
		maxproductos = iticket;
	}
	
	this.indiceProductoSombra = 0;
	var agnadidos = new Array();
	for (var t=0;t<maxproductos;t++) {
		if(!this.esTicketRemoto)
			codigo = ticketlist[t];	
		else {
			this.genSombraDesdeRemota();
			codigo = this.productoSombra["codigo"];
		}
		if ( !agnadidos[codigo]   ) {	
			this.GeneraProducto(codigo,t);
			this.indiceProductoSombra = this.indiceProductoSombra + 1;
			agnadidos[codigo] = 1;		
		}
	}

	
	return this.datos_text_productos;
}

Ticket.prototype.genCambio = function (){
	var cambio =  parseFloat(this.getEntregado()) -  parseFloat(this.TotalBase) ;	
	if (cambio<0)	cambio = 0;
	return cambio;
}
	
Ticket.prototype.GeneraProducto = function(codigo, indiceDeEntrada){
	this.CodigoProductoSeleccionado = codigo;
	
	//Numero de orden en productos ya enviados (0,1,2,3...)
	this.indiceProductoMetido = indiceDeEntrada;
	
	if( !this.esTicketRemoto ){
		var tic =  id( "tic_" + codigo );		
		if (!tic) return; //No esta en cesta de compra...
		
		//Prepopula la sombra con los datos del tic		
		this.genSombraDesdeTic();
	} else {
		this.genSombraDesdeRemota();
	}
	
	//Lee los datos desde la sombra
	var prod = new Object();
	
	prod.idmodisto 	= this.pgetIdModisto()
	prod.unidades  	= this.pgetUnidades();					
	prod.precio 	= this.pgetPrecio();		
	prod.descuento 	= this.pgetDescuento();
	prod.impuesto  	= this.pgetImpuesto();
	prod.referencia = this.pgetReferencia();
	prod.talla 		= this.pgetTalla();
	prod.color 		= this.pgetColor();
	prod.nombre 	= this.pgetNombre();						
	prod.concepto	= this.pgetConcepto();											
	prod.codigo		= codigo;
	
//	alert("datos lee:\n con:"+concepto+",nom:"+nombre);
	this.RawGeneraProducto(prod);
}	

Ticket.prototype.RawGeneraProducto = function(prod){

	var cr 		= this.cr;
	var nombreenticket = "";

	var pvp = 0;
	var total = 0;
	
	if (prod.idmodisto>0){
		nombreenticket = prod.concepto;
	} else {
		prod.concepto 	= "";
		nombreenticket = prod.nombre;
	}									
				
	pvp  	= parseFloat(prod.precio);//Impuesto incluido
	total 	= parseFloat(pvp) * parseFloat(prod.unidades);									
	
	if (prod.descuento>0) {
		total = parseFloat(parseFloat(total) - ( parseFloat(total) * (parseFloat(prod.descuento) /100.0) ));			
	}
	
	//Cuanto dinero del que paga el cliente es en concepto de impuestos
	this.aportacionimpuestos  += parseFloat(total) * prod.impuesto;
	
	//Total del ticket
	this.TotalBase = parseFloat(this.TotalBase) + parseFloat(total);
	
	
	/* Añadimos estos datos a la informacion del texto del ticket */	
	var salida = "";
	salida += cr + this.Colum( new Array(prod.unidades+" u",formatDinero(pvp),formatDescuento(prod.descuento),formatDinero(total)));					
	salida += prod.codigo + " " + nombreenticket +" " + cr;			
	
	this.datos_text_productos += salida;
		
	/* Añadimos estos datos a la informacion que habria que enviar al servidor */
						
	var	data_tickets2  = "";	
	var t = this.indiceProductoMetido;	
	var	firma = "line_" + t + "_"; 
	var crd = "&";	
		
	data_tickets2 += firma + "cod=" 		+ escape(prod.codigo) + crd;
	data_tickets2 += firma + "unid=" 		+ 		prod.unidades + crd;
	data_tickets2 += firma + "precio=" 		+ 		prod.precio + crd;//PVP
	data_tickets2 += firma + "impuesto=" 	+ escape(prod.impuesto) + crd;
	data_tickets2 += firma + "descuento="	+ escape(prod.descuento) + crd;				
	data_tickets2 += firma + "referencia="	+ escape(prod.referencia) + crd;
	data_tickets2 += firma + "cb="			+ escape(prod.codigo) + crd;				
	data_tickets2 += firma + "nombre=" 		+ escape(prod.nombre) + crd;							
	data_tickets2 += firma + "concepto=" 	+ escape(prod.concepto) + crd;				
	data_tickets2 += firma + "talla=" 		+ escape(prod.talla) + crd;
	data_tickets2 += firma + "color=" 		+ escape(prod.color) + crd;	
	data_tickets2 += firma + "idmodisto=" 	+ escape(prod.idmodisto) + crd;
	
	this.datos_post_productos = this.datos_post_productos +  data_tickets2;
	this.indiceProductoMetido = this.indiceProductoMetido + 1;

}

/*=========== IMPLEMENTACION TICKET ==============*/

/*=========== HELPERS ==============*/


function normalFloat(cadena){
	var f = parseFloat(cadena);
	if (isNaN(f)) return 0.0;
	if (f<0.000000000001) return 0.0;
	return f;
}

/*=========== HELPERS ==============*/
