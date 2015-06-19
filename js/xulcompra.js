
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


function id(nombre) { return document.getElementById(nombre) };


var noretryTC = new Array();
var noretryCOD = new Array();

function ResetRetrys(){
	noretryTC = new Array();
	noretryCOD = new Array();
}

var itacolor = 0 ;//Indice de Talla/Color

function xNuevaTallaColor(){
	var firma;
	var xlistadoTacolor = id("listadoTacolor");				
	
	var actualCOD 	= id("CB").value;
	var listacolor 	= id("Colores");
	var	elistacolor = id("elementosColores");
	var vcolor 		= listacolor.value;		
	var autenticolor;
	
	//Buscando color seleccionado
	var idex = 0;
	var el;
	
	autenticolor 	= listacolor.label;
	idcolor 		= listacolor.value;

	//Buscando talla seleccionada		
	var autentitalla = "";
	
	var listatallas = id("Tallas");
	var vtalla 		= listatallas.value;		
	var	elistatalla = id("elementosTallas");		

	autentitalla 	= listatallas.label;
	idtalla 	 	= listatallas.value;
	
	//Filtros que evitan entre monsergas		
	if (autenticolor.length < 1){						
		return alert( po_faltadefcolor );
	}
	if (autentitalla.length < 1){			
		return alert( po_faltadeftalla );
	}
	if (actualCOD.length < 1){			
		return alert( po_faltadefcb );
	}
	
	if (noretryCOD[actualCOD]) {
		return alert( po_errorrepcod );		
	}
	
	if (noretryTC[autentitalla] == autenticolor) {
		
		return alert( po_tallacolrep );		
	}
		
	var unidadescompra = parseInt(prompt(po_cuantasunidades,1));				
	
	if (!unidadescompra) {
		alert( po_unidadescompra );
		return;
	}
	
	//Ha pasado filtros
	noretryTC[autentitalla] = autenticolor;		
	noretryCOD[actualCOD] = 1;		
	

	var xlistitem = document.createElement("listitem");	
	
	var xcod 	= document.createElement("label");
		xcod.setAttribute("value",actualCOD);			
		
	var xtalla 	= document.createElement("label");
		xtalla.setAttribute("value",autentitalla);
		xtalla.setAttribute("tooltipText",idtalla);					
		
	var xcolor 	= document.createElement("label");
		xcolor.setAttribute("value",autenticolor);		
		xcolor.setAttribute("tooltipText",idcolor);					
					
	var xunid 	= document.createElement("label");
		xunid.setAttribute("value",unidadescompra);	
	
	firma = "tacolor_"+itacolor;itacolor ++;
	xlistitem.setAttribute("id",firma); 
	xcod.setAttribute("id",firma+ "_cod");
	xtalla.setAttribute("id",firma+ "_talla");
	xcolor.setAttribute("id",firma+ "_color");						
	xunid.setAttribute("id",firma+ "_unid");		
	
	xlistitem.appendChild( xcod );
	xlistitem.appendChild( xtalla );
	xlistitem.appendChild( xcolor );								
	xlistitem.appendChild( xunid );

	xlistadoTacolor.appendChild( xlistitem );		
	id("CB").value = parseInt(actualCOD) + 1;
	
	setTimeout("RegenCB()",50);
}

function RegenCB() {
		var xrequest = new XMLHttpRequest();
		var url = "selcb.php?modo=cb";
		xrequest.open("GET",url,false);
		xrequest.send(null);
		var resultado = xrequest.responseText;
		if (resultado.length > 4){
			var oldCB = id("CB");
			var newvalue =  parseInt(resultado) + 1;
			//actualiza solo si "mejora" lo actual
			if ( newvalue > parseInt(oldCB.value))
				oldCB.value = newvalue;
		}
		//id("CB").style.color = "black"; 			
}



var ven_normal = "dependent=yes,width=300,height=220,screenX=200,screenY=300,titlebar=yes,status=0";
var ven_familiaplus = "dependent=yes,width=450,height=350,screenX=200,screenY=300,titlebar=yes,status=0";
var ven_talla = "dependent=yes,width=300,height=260,screenX=200,screenY=300,titlebar=yes,status=0";
var ven_marca = "dependent=yes,width=300,height=360,screenX=200,screenY=300,titlebar=yes,status=0";

var ven = new Array();
ven["talla"]= ven_normal;
ven["marca"]= ven_marca;
ven["talla"]= ven_talla;
ven["tallaje"]= ven_talla;
ven["familiaplus"] = ven_familiaplus;




function popup(url,tipo) {
 if (ven[tipo])
   extra = ven[tipo];
 else 
   extra =  'dependent=yes,width=210,height=230,screenX=200,screenY=300,titlebar=yes,status=0';
   
  var nueva = window.open(url,tipo,extra);
}


//---------------------ALTA PRODUCTO--------------------------

function AltaProducto(){
	var	firma;
	var xrequest = new XMLHttpRequest();
	var url = "services.php?modo=altaproducto";
	var data = "";
	
	var xlistitem = id("elementosTallas");
	var iditem;
	var t = 0;
	var el, talla, color, cb, idtalla, idcolor, probhab;
		
	if(	id("Nombre").value == po_nuevoproducto ){
		return alert( po_modnombreprod );	
	}
	
	if (id("Referencia").value.length <1){
		return alert( po_especificarref );	
	}
	
	if (id("PrevioVenta").value<0.01){
		return alert( po_especifiprecioventa );	
	}
	
	if (id("Coste").value<0.01){
		return alert( po_especificoste );	
	}
		

	firma = "tacolor_";

	while( el = id(firma + t) ) { 
	
		data = "";
		
		talla 	= id( firma + t + "_talla"	).value;
		
		idtalla = id(firma + t + "_talla" ).getAttribute("tooltipText");
		idcolor = id(firma + t + "_color" ).getAttribute("tooltipText")
	
		unidades = id(firma + t + "_unid" ).value;	
		color 	= id( firma + t + "_color"	).value;
		cb 		= id( firma + t + "_cod"	).value;	
		//probhab = id( firma + t + "_probhab").value;	
	
		data = data + "&Referencia=" + escape(id("Referencia").value);
		data = data + "&RefProv=" + escape(id("RefProv").value);
		data = data + "&Nombre=" + escape(id("Nombre").value);
		data = data + "&Descripcion="+ escape(id("Descripcion").value);
		data = data + "&CosteSinIVA="+ escape(id("Coste").value);	
		data = data + "&PrecioVenta="+ escape(id("PrevioVenta").value);	
		//data = data + "&PV="+ escape(id("PVP").value);	
		data = data + "&Marca="+ escape(id("Marca").value);	
		data = data + "&ProvHab="+ enviar["IdProvHab"];	
					
		data = data + "&IdFamilia="+ enviar["IdFamilia"];
		data = data + "&IdSubFamilia="+ enviar["IdSubFamilia"];
	
		
		data = data + "&IdTalla="+ idtalla;
		data = data + "&IdColor="+ idcolor;
		data = data + "&CodigoBarras="+ cb;
		data = data + "&Unidades="+ unidades;				
		
		xrequest.open("POST",url,false);
		xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xrequest.send(data);
		
		var resultado = xrequest.responseText;
	
		resultado = parseInt(resultado);
		t++;
	}
		
	if (t>0) {
		var aviso = new String( po_sehandadodealtacodigos );
		aviso = aviso.replace("%d",t);
	
		alert(aviso);
		VaciarTacolores();
		document.location = "vercarrito.php?modo=check";	
	} else {
		alert(po_nohayproductos);	
	}
	
}

//-----------------------------------------------

//---------------------MARCA--------------------------

function CogerMarca(){    popup('selmarca.php?modo=marca','marca'); }


function changeMarca( quien, txtmarca) {
	id("Marca").value = txtmarca;
}

//-----------------------------------------------

//---------------------PROVEEDOR--------------------------

function CogeProvHab() {     popup('selproveedor.php?modo=proveedorhab','proveedorhab');  }

function changeProvHab( quien, txtprov ) {
	id("ProvHab").value = txtprov;
	enviar["IdProvHab"] = quien.value;
}	

//-----------------------------------------------

//-------------------FAMILIAS----------------------------


function changeFamYSub(idsubfamilia,idfamilia,texsubfamilia, texfamilia ){
	if (!texsubfamilia || texsubfamilia == "undefined" )
 		texsubfamilia = "...";

	var famsub = "" + texfamilia + " - " + texsubfamilia;
	id("FamSub").value = famsub;
	enviar["IdSubFamilia"] = idsubfamilia;
	enviar["IdFamilia"] = idfamilia;
}

function CogeFamilia(){
    var vfamilia = enviar["IdFamilia"];
    popup('selfamilia2.php?modo=familia&IdFamilia='+vfamilia,'familiaplus');
}

//-----------------------------------------------

//--------------------TALLAJES---------------------------



function changeTallaje(idtallaje, txttallaje) {
	id("Tallaje").value = txttallaje;

	enviar["IdTallaje"] = idtallaje;	
	

	VaciarTacolores();
		
	setTimeout("RegenTallajes()",50);
}

function CogeTallaje(){    
   popup('selcolor.php?modo=xtallaje','tallaje');      
}

var itallas = 0;//Indice de talla llenada

function AddTallaLine(nombre, valor) {
	var xlistitem = id("elementosTallas");	
	
	var xtalla = document.createElement("menuitem");
	xtalla.setAttribute("id","talla_def_" + itallas);
			
	xtalla.setAttribute("value",valor);
	xtalla.setAttribute("label",nombre);												
	
	xlistitem.appendChild( xtalla);var xlistitem = id("elementosTallas");	
	itallas ++;
}

function VaciarTallas(){
	var xlistitem = id("elementosTallas");
	var iditem;
	var t = 0;

	while( el = id("talla_def_"+ t) ) {
		if (el) {
			//alert( el.id );
			xlistitem.removeChild( el ) ;	
		}
		t = t + 1;
	}
	
	itallas = 0;

	id("Tallas").setAttribute("label","");	
}



function VaciarTacolores(){
	var xlistitem = id("listadoTacolor");
	var iditem;
	var t = 0;

	while( el = id("tacolor_"+ t) ) {
		if (el) {
			xlistitem.removeChild( el ) ;	
		}
		t = t + 1;
	}
	
	itacolor = 0;

	ResetRetrys();
	
}


function RegenTallajes() {
		VaciarTallas();
		
		var mitallaje = enviar["IdTallaje"];
		if(!mitallaje)
			mitallaje = MITALLAJEDEFECTO;
			
		var xrequest = new XMLHttpRequest();
		var url = "selcb.php?modo=tallas&IdTallaje="+mitallaje;
		xrequest.open("GET",url,false);
		xrequest.send(null);
		var res = xrequest.responseText;
	
		var lines = res.split("\n");
		var actual;
		
		for(var t=0;t<lines.length;t++){
			actual = lines[t];
			actual = actual.split("=");
			AddTallaLine(actual[0],actual[1]);		
		}				
}


//-----------------------------------------------


//--------------------TALLAS---------------------------


function changeTalla(idtalla, txttalla) {
	enviar["IdTalla"] = idtalla;
	id("Tallas").value = idtalla;
	id("Tallas").setAttribute("label", txttalla);

}

function CogeTalla(){    
   popup('selcolor.php?modo=talla&IdTallaje='+enviar["IdTallaje"],'talla');      
}

//-----------------------------------------------


//--------------------COLORES---------------------------

function isObject(a) {
    return (a && typeof a == 'object') || isFunction(a);
}


function changeColor(idtalla, txttalla) {
	enviar["IdColor"] = idtalla;
	
	if (isObject(idtalla)){
		id("Colores").value = idtalla.value;
	} else {
		id("Colores").value = idtalla;		
	}
	
	id("Colores").setAttribute("label",txttalla);

}

function CogeColor(){    
   popup('selcolor.php?modo=color','color');      
}

//-----------------------------------------------


