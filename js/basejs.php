<?php

header("Content-Type: text/javascript");

?>




totalTiempos = 0;
startTime=new Date().getTime();

function timingTerminaGeneracionPagina(tiempoProcesoPHP) {
 var endTime=new Date().getTime();
 echo('[Javascript time]: '+((endTime-startTime)/1000)+
      ' seconds.<br>');
 totalTiempos = totalTiempos + ((endTime-startTime)/1000);

 echo('[TOTAL Generacion JS]: '+totalTiempos+ ' seconds.<br>');
 echo('[TOTAL Generacion PHP]: '+tiempoProcesoPHP+ ' seconds.<br>');
 echo('[TOTAL JS+PHP]: '+(totalTiempos +tiempoProcesoPHP)+ ' seconds.<br>');

}

// 9Gestion, libreria compartida
var ancho_lista = 650;


function ckAction(me,id,max){ 
 var p = 0;
 var tipoAction= "";

 if (me.checked)
  tipoAction="trans";
 else
  tipoAction="notrans";     
  
  if (max>0 && me.checked){
   if (max>1)
   	p = prompt(po_cuantasunidades, max )
   else 
       p = 1;
   if (p>max)  p = max;
  }
  
  var url = 'modalmacenes.php?modo='+tipoAction+'&id='+id+'&u='+p;
  Mensaje (url);  
}


function formatCurrency(num) {
// num = num.toString().replace(/\\$|\,/g,'');

 if(isNaN(num)) num = "0";

 var sign = (num == (num = Math.abs(num)));
  num = Math.floor(num*100+0.50000000001);
 var cents = num%100;
  num = Math.floor(num/100).toString();

 if(cents<10) cents = "0" + cents;

 for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
   num = num.substring(0,num.length-(4*i+3))+'.'+ num.substring(num.length-(4*i+3));

 return (((sign)?'':'-') + num + ',' + cents + " &euro;");
}

/*====== PERSONALIZACION POPUPS =========*/


var ven_normal = "dependent=yes,width=300,height=220,screenX=200,screenY=300,titlebar=yes,status=0,dialog=yes,minimizable=no";
var ven_alta = "dependent=yes,width=400,height=520,screenX=200,screenY=300,titlebar=yes,status=,dialog=yes,minimizable=no";
var ven_seleccion = "dependent=yes,width=400,height=520,screenX=200,screenY=300,titlebar=yes,status=0,dialog=yes,minimizable=no";
var ven_codigobarras = "dependent=yes,width=320,height=420,screenX=200,screenY=300,titlebar=yes,status=0,dialog=yes,minimizable=no";
var ven_carrito = "dependent=yes,width=600,height=320,screenX=200,screenY=300,titlebar=yes,status=0,dialog=yes,minimizable=no";
var ven_color = "dependent=yes,width=300,height=260,screenX=200,screenY=300,titlebar=yes,status=0,dialog=yes,minimizable=no";
var ven_talla = "dependent=yes,width=300,height=260,screenX=200,screenY=300,titlebar=yes,status=0,dialog=yes,minimizable=no";
var ven_marca = "dependent=yes,width=300,height=360,screenX=200,screenY=300,titlebar=yes,status=0,dialog=yes,minimizable=no";
var ven_avanzada = "dependent=yes,width=600,height=80,screenX=200,screenY=300,titlebar=yes,status=0,dialog=yes,minimizable=no";
var ven_familiaplus = "dependent=yes,width=450,height=350,screenX=200,screenY=300,titlebar=yes,status=0,dialog=yes,minimizable=no";


var ven = new Array();
//ven["talla"]= ven_normal;
//ven["marca"]= ven_normal;
ven["alta"]= ven_alta;
ven["codigobarras"]= ven_codigobarras;
ven["selcomprar"]= ven_seleccion ;
ven["selalmacen"]= ven_seleccion ;
ven["carrito"]= ven_carrito ;
ven["color"]= ven_color ;
ven["talla"]= ven_talla ;
ven["marca"]= ven_marca ;
ven["avanzada"]= ven_avanzada ;
//ven["proveedorhab"] = ven_alta;
ven["altaproveedor"] = ven_alta;
ven["familiaplus"] = ven_familiaplus;

function popup(url,tipo) {
 if (ven[tipo])
   extra = ven[tipo];
 else 
   extra =  'dependent=yes,width=210,height=230,screenX=200,screenY=300,titlebar=yes,status=0';
   
 window.open(url,tipo,extra);
}

/*====== PERSONALIZACION POPUPS =========*/


/*=============== LISTADOS RAPIDOS =============*/



var K = 0;// Iteracion en coleccion de objetos
var p = new Array(); //Coleccion de objetos
var lastBase; //Memoria de bases activas
var iBase = 0;//Iterador dentro de una base activa (para listados colapsables)

var echo = function(param) {
 //document.write("<xmp>"+param+"</xmp>"); 
 document.write( param ); 
};

var actionUrl = document.location.href.split("?")[0];

function ifConfirmGo( mensaje , url ){
  if (confirm(mensaje)){
    window.location.href = url;
  }
}

function ifConfirmExec( mensaje, command){
  if (confirm(mensaje)){
    eval(command);
  }	
}



function genPaginador(iz,der,num){
 if (iz || der) {
   echo ("<table class='forma'><tr class='f'>");
   if (iz) echo ("<td><a href='"+actionUrl +"?modo=pagmenos'>"+po_pagmenos+"</a></td>");
   if (der) echo ("<td><a href='"+actionUrl +"?modo=pagmas'>"+po_pagmas+"</a></td>");   
   echo ("</tr></table>");  
 }
}


/* ============== CLASE PRODUCTO ============ */
function Producto(id){
 this.id = id;
}
/* ================ CLASE PRODUCTO============ */



 function genCompraLinea() {
   echo("<tr class='f'>"+
  "<td class=referencia>"+this.referencia+"</td>"+
  "<td class=nombre>"+this.nombre+"</td>"+
  "<td class=boton><input class=sbtn type=button "+
  "onclick='AgnadirProductoCompra("+this.id+")'value='"+po_comprar+"'></td></tr>");
 }


function genProductoLinea() {
  var sel = "";

 echo("<tr class='f'>"+
  "<td width='16'>"+icon_bar+"</td>"+
  "<td class='color' width='40%'>"+this.color+"</td>"+
  "<td class='talla' width='40%'>"+this.talla+"</td>"+
  "<td class='boton' width='20%'><nobr>"+
  "<a class='tb' onclick=\"selImpresion('codigobarrasProducto','"+this.id+"');return 0;\">"+po_imprimircodigos+"</a>"+" "+
  "<a class='tb' href='modproductos.php?modo=editarbar&id="+this.id+"'>"+po_modificar+"</a> "+
  "<a class='tb' onclick='ifConfirmGo(\""+po_avisoborrar+"\",\"modproductos.php?modo=borrar&id="+this.id+"\")'>"+po_borrar+"</a></nobr>"+
  "</td>"+
  "</tr>\n"
   );   
  return 2;
}



function genProductoResumen() {
  var sel = "";

 echo("<tr class='f'>"+
  "<td width='16'></td>"+
  "<td class='color' width='40%'></td>"+
  "<td class='talla' width='40%'></td>"+
  "<td class='boton' width='20%'><nobr>"+
  "<a class='tb' href='modproductos.php?modo=clonar&id="+this.id+"'>"+po_nuevatallacolor+"</a>"+" "+
//  "<a class='tb' href='#' onclick='expandCruzado("+this.id+");void(0);'>+</a></nobr>"+
  "<a class='tb' href='javascript:expandCruzado("+this.id+");void(0);'>+</a></nobr>"+
  "</td>"+
  "</tr><tr><td colspan='4' id='cruzado_"+this.id+"'></td></tr>\n"
   );   
}

var idCruzadoEnProceso = 0;
var xrequest = new XMLHttpRequest();

function idE(ncosa) {return document.getElementById(ncosa);};

var arrayExpandidos = new Array();
var arrayUnidades = new Array();

function cuentaUnidades(idbase,cantidad){
  var actual = arrayUnidades[idbase];
  if(!actual) actual = 0;
  
  arrayUnidades[idbase] = parseInt(actual,10) + parseInt(cantidad,10);  
  return arrayUnidades[idbase];
}

function expandCruzado( idCruzado ){

	if (arrayExpandidos[idCruzado]){ 
		idE("cruzado_"+idCruzado).innerHTML = "";
		arrayExpandidos[idCruzado] = 0;//Si estaba expandido ha sido comprimido, y marcado comprimido
		return;
	}

	if (idCruzadoEnProceso)
		return;

	idCruzadoEnProceso = idCruzado;
	var url = "simplecruzado.php?modo=soloficha&IdProducto="+ idCruzado;
	
	xrequest.open("GET",url,true);
	xrequest.onreadystatechange = RececepcionFicha;
	xrequest.send(null);			
}

function RececepcionFicha(){
	if (xrequest.readyState==4) {
		var rawtext = xrequest.responseText;			
		//alert(rawtext);
		var contenedor = idE("cruzado_"+idCruzadoEnProceso);
		
		if (!contenedor)
			return alert("no contenedor!");
		//else 
		//	alert(contenedor);
		arrayExpandidos[idCruzadoEnProceso] = 1;//Marcar como expandido
		
		contenedor.innerHTML = rawtext;		
		
		idCruzadoEnProceso = 0;		
	}
}


function genProductoResumenCompras() {
  var sel = "";

 echo("<tr class='f'>"+
  "<td width='16'></td>"+
  "<td class='color' width='40%'></td>"+
  "<td class='talla' width='40%'></td>"+
  "<td class='boton' width='20%'><nobr>"+
  "<a class='tb' href='modproductos.php?modo=clonar&id="+this.id+"&volver=modcompras'>"+po_nuevatallacolor+"</a>"+" "+
  "</td>"+
  "</tr>\n"
   );   

}


function genCompraLineaCompras() {
  var sel = "";

 echo("<tr class='f'>"+
  "<td width='16'>"+icon_bar+"</td>"+
  "<td class='color' width='40%'>"+this.color+"</td>"+
  "<td class='talla' width='40%'>"+this.talla+"</td>"+
  "<td class=boton><nobr>"+
  "<a class='tb' onclick=\"selImpresion('codigobarrasProducto','"+this.id+"');return 0;\">"+po_imprimircodigos+"</a>"+" "+
  "<input class=sbtn type=button "+
  "onclick='AgnadirProductoCompra("+this.id+")'value='"+po_comprar+"'></nobr></td>"+
  "</tr>\n"
   );      
}


function cL(id, L_talla,L_color){ //Aparece en compras
  var o = new Producto(id);
  o.id = id;
  o.talla = L[L_talla];
  o.color = L[L_color];
  o.genLinea = genCompraLineaCompras;
  o.genResumen = genProductoResumenCompras;
  o.tipo = TIPO_NORMAL;
  p[K++] = o;
}


function cP(id, L_talla,L_color){ //Aparece en productos
  var o = new Producto(id);
  o.id = id;
  o.talla = L[L_talla];
  o.color = L[L_color];
  o.genLinea = genProductoLinea;
  o.tipo = TIPO_NORMAL;
  o.genResumen = genProductoResumen;
  p[K++] = o;
}

function genProductoLineaHead() { //Head de Listados de Productos
 echo("</table></div></td></tr>\n\n<tr class='t f'>"+
  "<td width='16'>"+icon_productos+"</td>\n"+
  "<td class='referencia'>"+this.referencia+"</td>\n"+
  "<td class='nombre'>"+this.nombre+"</td>\n"+
 "<td class='familia'>"+this.familia+" - " +this.subfamilia+"</td>\n"+ 
  "<td class='botonplus' width='16'><nobr><input type='button' onclick='document.location=\"modproductos.php?modo=editar&id="+this.id+"\"' value='Modificar'> "+
 "<input type='button' onclick='MuestraBases("+this.id+")' value='+'></nobr></td>\n"+
   "</tr>\n\n"+
   "<tr><td colspan='8'><div id='base"+this.id+
"' style='display: none'><table class='subcaja' width='95%'>\n");   

 echo("<tr class='f lh'>"+
  "<td width='16'></td>"+
  "<td class='color'>"+po_color+"</td>"+
  "<td class='talla'>"+po_talla+"</td>"+
  "<td class='boton'></td>"+
  "</tr>\n"
   ); 
 return 1;
}

function genProductoLineaHeadCompras() {//Head de listados de productos
 echo("</table></div></td></tr>\n\n<tr class='t f'>"+
  "<td width='16'>"+icon_productos+"</td>\n"+  
  "<td class='referencia'>"+this.referencia+"</td>\n"+
  "<td class='nombre'>"+this.nombre+"</td>\n"+
 "<td class='familia'>"+this.familia+" - " +this.subfamilia+"</td>\n"+ 
  "<td class='botonplus' width='16'><input type='button' onclick='MuestraBases("+this.id+")' value='+'></td>\n"+
   "</tr>\n\n"+
   "<tr><td colspan='8'><div id='base"+this.id+"' style='display: none'><table  class='subcaja' width='95%'>\n");   

 echo("<tr class='f lh'>"+
  "<td width='16'></td>"+
  "<td class='color'>"+po_color+"</td>"+
  "<td class='talla'>"+po_talla+"</td>"+
  "<td class='boton'></td>"+
  "</tr>\n"
   ); 

}

function cPH(id, nombre,ref, L_familia, L_subfamilia){
  var o = new Producto(id);
  o.id = id;
  o.nombre = nombre;
  o.referencia = ref;
  o.familia = L[L_familia];
  o.subfamilia = L[L_subfamilia];
  o.genLinea = genProductoLineaHead;
  o.tipo = TIPO_HEAD;
  o.genResumen = genProductoResumen;
  p[K++] = o;
}

function cLH(id, nombre,ref, L_familia, L_subfamilia ){
  var o = new Producto(id);
  o.id = id;
  o.familia = L[L_familia];
  o.subfamilia = L[L_subfamilia];
  o.nombre = nombre;
  o.referencia = ref
  o.tipo = TIPO_HEAD;
  o.genLinea = genProductoLineaHeadCompras;
  p[K++] = o;
}


function genAlmacenLinea() {
  var sel = "";

  if (this.seleccionado) sel = "checked='true'";
    
  sel = "<input type='checkbox' "+sel+" onclick='ckAction(this,"+this.transid+","+this.unidades+")'>";

 echo("<tr class='f'>"+
  "<td class='iconos'>"+Iconos2Images(this.iconos)+"</td>"+
  "<td class='local'>"+this.local+"</td>"+
  "<td class='talla'>"+this.talla+"</td>"+
  "<td class='color'>"+this.color+"</td>"+
  "<td class='unidades'>"+this.unidades+" u.</td>"+
  "<td class='precio'>"+formatCurrency(this.precio)+"</td>"+
  "<td class='boton'>"+
  "<a  class='tb' href='modalmacenes.php?modo=editar&id="+this.transid+"'>"+
   po_modificar+"</a></td>"+
  "<td class=boton>"+sel+"</td></tr>\n"
   );   
 }


var autoexpand = new Array();

function cA(id,iconos,unidades,precio,seleccionado,transid,L_talla,L_color,L_local){
  var o = new Producto(id);
  
  cuentaUnidades(lastBase,unidades);   
  
  o.idbase = lastBase;//ajustado por un cAH anterior
  o.iBase = iBase;//mantenido por esta misma funcion 
  iBase ++;
  o.iconos = iconos;
  o.unidades  = unidades;
  o.precio = precio;
  o.seleccionado = seleccionado;
  if (seleccionado) {
    autoexpand[lastBase] = 1;//Ordenamos que se autoexpanda este elemento    
  }
  o.transid = transid;
  o.talla = L[L_talla];
  o.color = L[L_color];
  o.local = L[L_local];
  o.genLinea = genAlmacenLinea;
  o.iK = K;
  p[K++] = o;
}

function genAlmacenLineaHead() {
 
  var display = "none";
  var iconusar, unidadestotal, extra;
  
  if (autoexpand[this.idBase])
      display = "normal";   
      
 unidadestotal = cuentaUnidades(this.idBase,0);
     
 if(unidadestotal>0){
    iconusar = icon_stockfull;
    extra = " <sup>"+unidadestotal+"</sup>";
 } else {
   iconusar = icon_stock;	
   extra = " &nbsp; ";
 }

 echo("</table></div></td></tr>\n\n<tr class='t f'>"+
  "<td width='16' id='icon_stock_"+ this.idBase +"' style='text-align: left'><nobr>"+iconusar+extra+"</nobr></td>"+
  "<td class='referencia'>"+this.referencia+"</td>\n"+
  "<td class='nombre'>"+this.nombre+"</td>\n"+
  "<td class='familia'>"+this.familia+" - "+this.subfamilia+"</td>\n"+
  "<td class='botonplus' width='16'><input type='button' onclick='MuestraBases("+this.idBase+")' value='+'></td>\n"+
   "</tr>\n\n"+
   "<tr><td colspan='8'><div id='base"+this.idBase+
  "' style='display: "+display+"'><table  class='subcaja' width='95%'>\n");   

 echo("<tr class='f lh'>"+
  "<td class='iconos'></td>"+
  "<td class='local'>"+po_nombre+"</td>"+
  "<td class='talla'>"+po_talla+"</td>"+
  "<td class='color'>"+po_color+"</td>"+
  "<td class='unidades'>"+po_unidades+"</td>"+
  "<td class='precio'>"+po_precio+"</td>"+
  "<td class='boton' width='64'></td>"+
  "<td class='boton'></td></tr>\n"
   );   

}

var oldbases = new Array();

function MuestraBases(id){
 status = oldbases[id];
 
 if (status){
  oldbases[id] = 0;
  document.getElementById("base"+id).style.display = "none";
 } else {
  document.getElementById("base"+id).style.display = "block";
  oldbases[id] = 1;
 }

}


function cAH(idBase,nombre,referencia,descripcion, L_local, L_familia, L_subfamilia){
  var o = new Producto(idBase);
  lastBase = idBase; //Ajuste de base activa
  iBase = 0;//Empieza un listado con bases
  o.idBase = idBase;
  o.nombre  = nombre;
  o.referencia = referencia;
  o.descripcion = descripcion;
  o.local = L[L_local];
  o.familia = L[L_familia];
  o.subfamilia = L[L_subfamilia];
  o.genLinea = genAlmacenLineaHead;
  o.iK = K;
  p[K++] = o;
}

var AutoFocusIdBase = 0;//Pone en foco en este elemento del almacen

function cListAlmacen() {
 var t,o;
 echo ("<table class='forma' width='"+ancho_lista+"'><tbody><tr><td><table>");
 var oldref = "";
 for(t=0;t<K;t++) {
   o = p[t];
   o.genLinea();
 }
 echo ("</table></td></tr></tbody></table>");
 
 
 if (AutoFocusIdBase){ 
 	setTimeout("SetFocoAlmacen('"+AutoFocusIdBase+"')",200); 	
 } 
}

function idElemento(nombre) { return document.getElementById(nombre); };

function SetFocoAlmacen(idBase){
	var basediv = idElemento("base"+idBase);	
	if (!basediv) return;
	if (!basediv.focus) return;
	basediv.focus();			
}

var TIPO_HEAD = 1;
var TIPO_NORMAL = 0;

function cListProductos() {
 var t,o;
 echo ("<table class='forma' width='"+ancho_lista+"'><tbody><tr><td><table>");
 var oldtipo = 0; 
 for(t=0;t<K;t++) {
   o = p[t];
   o.genLinea();   
   tipo = p[t].tipo;
   if ((t+1)<K){
     nextipo = p[t+1].tipo;
     if ( nextipo == TIPO_HEAD && tipo == TIPO_NORMAL ) {
        o.genResumen();
     }
   } else {
      o.genResumen();
   }
 }
 echo ("</table></td></tr></tbody></table>");
}

function cListCompras() {
 var t,o;
 
 echo ("<table class='forma' width='"+ancho_lista+"'><tbody><tr><td><table>");

 for(t=0;t<K;t++) {
   o = p[t];
   o.genLinea();
 }
 echo ("</table></td></tr></tbody></table>");
}



/*=============== SISTEMAS AUXILIARES =============*/

function auxCancelarCompra(){
  var url = "selcomprarapida.php?modo=noselecion"
  Mensaje(url);
}



function AgnadirProductoCompra(idproducto){
  var url,p;

  p = prompt(po_cuantasunidadesquiere, '1')
  if (p>0){
   url = "selcomprarapida.php?modo=agnadeuna&id=" + idproducto + "&u=" + p;
   Mensaje(url);
  }
}




function getMe(layerID) {      //busca elemento y lo devuelve
            if(document.getElementById){
                  return document.getElementById(layerID);
            }else if(document.all){
                  return document.all[layerID];
            }else if(document.layers){
                  return document.layers[layerID];
            }
            return null;
}


function MuestraCapa(nombre){
  capa = getMe(nombre);
  if (!capa) 
    return;
 
  capa.style.visibility = "visible";
  capa.style.block = "auto";
}

function AgnadirCarrito(IdProducto) {
  p = prompt(po_cuantasunidadesquiere, '1')
  if (p>0){
     Mensaje('selcomprarapida.php?modo=agnadeuna&id='+IdProducto + "&u="+p+ "&close=1");
  } 
}


//Cambia el valor de un campo para nombre conocido
function AjustarCampo(nombre,valor){
 getMe(nombre).value = valor;
}


function selImpresion(tipo,id){    
 
  p = prompt( po_cuantascopias , '1')

  if (p>0){
     popup('selimpresion.php?modo='+tipo+'&id='+id+'&copias='+p,'codigobarras');
  }
}





function editatemplate(id) {		     
 window.open( "modtemplates.php?modo=editar&id=" + id,
 'Input',
 'dependent=yes,width=1100,height=750,screenX=0,screenY=0,titlebar=yes');	
}


function dotab(e,me) //Captura tab y lo mete en donde estemos
{

 if (!e) {
   e = window.event;
   return false; 
 } 

 if (e.keyCode == 9) // tab 
 {
 //   e.srcElement.value = e.srcElement.value + "\\\\t";
   // e.srcElement.focus() 
    me.value = me.value + "\\\\t";
   me.focus();

    return false;
 }
 return true;
}


//Combos de cambiar familia, subfamilia, color...

function ResetMarca() {  
  o = getMe("IdMarca");
  if (o) o.value = false;
  o = getMe("TextoMarca");
  if (o) o.innerHTML = "";
}

function ResetTalla() {  
  o = getMe("IdTalla");
  if (o) o.value = false;
  o = getMe("TextoTalla");
  if (o) o.innerHTML = "";
}

function ResetSubfamilia() {  
  o = getMe("IdSubFamilia");
  if (o) o.value = false;
  o = getMe("TextoSubFamilia");
  if (o) o.innerHTML = "";
}

function ResetProveedorHab() {  
  o = getMe("IdProvHab");
  if (o) o.value = false;
  o = getMe("TextoProvHab");
  if (o) o.innerHTML = "";
}

function ResetColor() {  
  o = getMe("IdColor");
  if (o) o.value = false;
  o = getMe("TextoColor");
  if (o) o.innerHTML = "";
}

//
// VENTANAS AUXILIARES
//

function auxCarritoCompra(){     popup('vercarrito.php?modo=check','carrito');  }
function auxCarritoMover(){     popup('vertrans.php?modo=check','carrito');  }
function auxSeleccionRapidaAlmacen(IdLocal){    popup('selalmacen.php?modo=empieza&IdLocal='+IdLocal,'selalmacen'); }
function auxAlta(){    popup('altarapida.php?modo=altaycompra','alta');  }
function auxSeleccionRapidaCompra(){   popup('selcomprarapida.php?modo=empieza','selcomprar');  }
function auxColor(){    popup('selcolor.php?modo=color','color');  }
function auxProveedorHab() {     popup('selproveedor.php?modo=proveedorhab','proveedorhab');  }
function auxAltaProv() { popup('modproveedores.php?modo=altapopup','altaproveedor'); }
function auxMarca(){    popup('selmarca.php?modo=marca','marca'); }
function auxAvanzada(vuelta){    popup('selavanzada.php?modo=avanzada&vuelta='+vuelta,'avanzada'); }

function auxTalla(tipo){    
 if (tipo=='nuevo') { //Se debe elegir primero tallaje
   popup('selcolor.php?modo=tallaje','talla');      
   return;
 }
 popup('selcolor.php?modo=talla&IdTallaje='+tipo,'talla');  
}

function auxFamilia(){
    //ResetSubfamilia();
    var vfamilia = getMe("IdFamilia").value;
//    popup('selfamilia.php?modo=familia','familia');
    popup('selfamilia2.php?modo=familia&IdFamilia='+vfamilia,'familiaplus');
}

function auxSubFamilia(){
	auxFamilia();
/*
  var vfamilia = getMe("IdFamilia").value;

  popup('selfamilia.php?modo=subfamilia&IdFamilia='+vfamilia,'subfamilia');
  */
}


function change(o,label){
  value = o.value;
  vf = getMe("TextoFamilia");

 if (!vf) {
  window.alert("no texfamilia!");
  return;
 }
 vf.innerHTML = ""+ label+ "";

 vf = getMe("IdFamilia");

 if (!vf) {
  window.alert("no idsub!");
  return;
 }
 
 vf.value = value;
}


function changeFamYSub(idsubfamilia,idfamilia,texsubfamilia, texfamilia ){

 //alert("Recibe: idsub"+idsubfamilia+",idfam"+idfamilia+",texsub["+texsubfamilia+",texfam["+texfamilia);
 getMe("TextoFamilia").innerHTML = ""+ texfamilia + "";
 getMe("TextoSubFamilia").innerHTML = ""+ texsubfamilia + "";
 getMe("IdFamilia").value = idfamilia;
 getMe("IdSubFamilia").value = idsubfamilia;

}






function changeProvHab(o,label){
  value = o.value;
  vf = getMe("TextoProvHab");

 if (!vf) {
  window.alert("no prov hab!");
  return;
 }
 vf.innerHTML = ""+ label+ "";

 vf = getMe("IdProvHab");

 if (!vf) {
  window.alert("no idprohab!");
  return;
 }
 
 vf.value = value;
}


function changeSub(o,label){
  valor = o.value;
 
  vf = getMe("TextoSubFamilia");

 if (!vf) {
  window.alert("no textofamilia!");
  return;
 }
 vf.innerHTML = ""+ label+ "";

 vf = getMe("IdSubFamilia");

 if (!vf) {
  window.alert("no idsub!");
  return;
 } 
 vf.value = valor;
}



function changeColor(o,label){
  valor = o.value;
 
  vf = getMe("TextoColor");

 if (!vf) {
  window.alert("no textocolor!");
  return;
 }
 vf.innerHTML = ""+ label+ "";

 vf = getMe("IdColor");

 if (!vf) {
  window.alert("no color!");
  return;
 } 
 vf.value = valor;
}

function changeTalla(o,label){
  valor = o.value;
 
  vf = getMe("TextoTalla");

 if (!vf) {
  window.alert("no textotalla!");
  return;
 }
 vf.innerHTML = ""+ label+ "";

 vf = getMe("IdTalla");

 if (!vf) {
  window.alert("no talla!");
  return;
 } 
 vf.value = valor;
}

function changeMarca(o,label){
  valor = o.value;
 
  vf = getMe("TextoMarca");

 if (!vf) {
  window.alert("no textomarca!");
  return;
 }
 vf.innerHTML = ""+ label+ "";

 vf = getMe("IdMarca");

 if (!vf) {
  window.alert("no marca!");
  return;
 } 
 vf.value = valor;
}



function AjustarModo(modo) {
 AjustarCampo('modoactual',modo);
}						


/*==== SISTEMAS COMUNICACION ASINCRONA ==========*/

var xmlhttp;



function remoteThis(url) {  
  xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange=xmlhttpChange;
  xmlhttp.open("GET",url,true);
  xmlhttp.send(null);
}

function Mensaje(url) {  
  xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange=DummyFunction();
  xmlhttp.open("GET",url,true);
  xmlhttp.send(null);
}

function DummyFunction() {};


function xmlhttpChange() {
 if (xmlhttp.readyState==4) {  // if "OK"
  if (xmlhttp.status==200) {         eval( xmlhttp.responseText );           } 
 }
}


function genMensajeros(){};


//Al documento en curso lo llama y le pasa el campo y su valor actual, para que compruebe su validez.
function Valida(me) {
  url = document.location.href.split("?");
  remoteThis(url[0] + "?modo=valida&campo="+me.name+"&valor="+me.value+"&idcampo="+me.id);
}

//"	" <-- tabulador

/*==== SISTEMAS COMUNICACION ASINCRONA ==========*/

/*========== GRAFOS ICONOS ============*/
var icons = new Array();

icons["$"] = "<img src='img/oferta16gray.gif'>";
icons["S"] = "<img src='img/oferta16.gif'>";
//icons["V"] = "<img src='img/enventa16.gif'>";
//icons["x"] = "<img src='img/enventa16gray.gif'>";
icons["V"] = "<img src='img/ok1.gif'>";
icons["x"] = "<img src='img/ok1gray.gif'>";



/* Convierte grafos en imagenes */
function Iconos2Images(cad){
 var out;
 if(!cad) return;
 out = icons[cad[0]]+" "+icons[cad[2]];
 return out;
}

/* Convierte grafos en imagenes y emite por pantalla */
function gI2I(cad){
 echo(Iconos2Images(cad));
}


var icon_stock 		= "<img src='img/stock16.png'>";
var icon_stockfull 	= "<img src='img/stockfull.gif'>";
var icon_productos 	= "<img src='img/producto16.png'>";
var icon_bar 		= "<img src='img/bar16.png'>";

function iconStockMark(idmark){
	return "<img id='icon_stock_"+idmark+"' src='img/stock16.png'>";
}

