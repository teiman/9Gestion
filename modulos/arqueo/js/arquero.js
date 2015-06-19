

function id(nombreCosa){
	return document.getElementById(nombreCosa);
}


var xrequestArqueo = false;
var arqueoCargandose = 0;

function CambioArqueo(IdArqueo){
	
	log("Solicitando CambioArqueo() para arqueo:"+IdArqueo);
	
	arqueoCargandose = IdArqueo; 
	
	var	url = "arqueoservices.php?modo=getArqueo&IdArqueo=" + IdArqueo + "&" + Math.random();
	xrequestArqueo = new XMLHttpRequest();
	
	
	xrequestArqueo.open("POST",url,true);
	xrequestArqueo.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	xrequestArqueo.onreadystatechange = RecibeArqueo;
	xrequestArqueo.send(null);		
	
}


function RecibeArqueo() {
	if (xrequestArqueo.readyState!=4) 
			return;			
	//TODO: procesar aqui un arqueo que se solicitu su carga
	
    log("Recibiendo arqueo :"+arqueoCargandose);
	
	Local.IdArqueoActual = arqueoCargandose;	
}

/*------------------------------------------*/

var xrequestListaArqueos = false;

function CambioListaArqueos(){
	//...
		
	log("<-- Solicitando la lista de arqueos de local:"+Local.IdLocalActivo);
	
	var	url = "arqueoservices.php?modo=getListaUltimosDiez&IdLocal="+Local.IdLocalActivo+"&r=" + Math.random();
	xrequestListaArqueos = new XMLHttpRequest();
	
	xrequestListaArqueos.open("POST",url,true);
	xrequestListaArqueos.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	xrequestListaArqueos.onreadystatechange = RecibeListaArqueos;
	xrequestListaArqueos.send(null);			
}

var UltimosArqueos;
var IdArqueo2RefUltimos;

var esRecibidaListaArqueos = false;


function RecibeListaArqueos() {
	var data;

   //log("Recibe lista arqueos?");

	if (xrequestListaArqueos.readyState!=4) 
			return;			
			
	log("--> RecibeListaArqueos():"+xrequestListaArqueos.responseText);
	
	//TODO: procesar aqui un arqueo que se solicitu su carga	
	var obj = eval( "(" + xrequestListaArqueos.responseText+ ")" );
	var t;		
	var FechaApertura, IdArqueo;
							
	//Reinicializamos los arqueos 							
	UltimosArqueos = new Array(); 
	IdArqueo2RefUltimos = new Array();
	
	for( t=0; t<10; t++){
		log("--> parseando vuelta.. "+t);
		UltimosArqueos["arqueo_"+t] = obj["arqueo_"+t];
		try {
			data = obj["arqueo_"+t];
			IdArqueo2RefUltimos[data.IdArqueo] = t; 
			log("carga arqueo.."+data.IdArqueo);						
		} catch(e){ };		
	}
	
	ActualizarComboArqueos();
								
	esRecibidaListaArqueos = true;								
}

function VaciarDeHijos(padreNombre){	
	var padre = id(padreNombre);
	while( padre.childNodes.length ){
		padre.removeChild( padre.lastChild );
	}		
}


function ActualizarComboArqueos(){
	var idarqueo, fecha, arqueo,t, IdArqueo,xmenu;
	VaciarDeHijos("itemsArqueo");
	var padre = id("itemsArqueo");
	
	if(!padre){
		return alert("Fallo de formato. Recargue la pagina");
	}
	 
	for(t=0;t<10;t++){
		//..
		arqueo =  UltimosArqueos["arqueo_"+t];
		
		if (arqueo){		
			//log("load arqueo:"+arqueo.toSource());
//			fecha = arqueo["FechaApertura"];
			fecha = arqueo["FechaCierre"];
			idarqueo = arqueo["IdArqueo"];
			
			xmenu = document.createElement("menuitem");
			xmenu.setAttribute( "label",idarqueo + "-" + datetimeToFechaCastellano(fecha) );
			xmenu.setAttribute( "id-referencia",idarqueo );
			xmenu.setAttribute( "id","arqueo_"+idarqueo );			
			xmenu.setAttribute( "oncommand","CargarArqueoSeleccionado("+t+");" );
			
			id("itemsArqueo").appendChild( xmenu );
			log("xul<-- mostrando.."+idarqueo);
		}
	}					
}

//recibe YYYY-MM-DD HH:MM, genera DD-MM-YYYY HH:MM
function datetimeToFechaCastellano(fecha){
	if (fecha== "0000-00-00 00:00:00" || !fecha){
		//return "00-00-0000 ";			
		//return "--/--/--- --:--";
		return " 00/00/0000 --:--";
	}
	if (fecha=="hoy"){
		var hoy = new Date();	
		return (hoy.getDate())+"-"+(hoy.getMonth()+1)+"-"+(hoy.getYear()+1900);
	}
	
	var partesdatetime = fecha.split(" ");
	
	var partefecha 	= partesdatetime[0];
	var partehora 	= partesdatetime[1];
	
	var parteshoras = partehora.split(":");
	
	var hora =  parteshoras[0]+":"+parteshoras[1];
	
	var datosfecha 	= partefecha.split("-");
	
	return datosfecha[2] + "/" + datosfecha[1] + "/" + datosfecha[0] + " " + hora;  		
}


function CargarArqueoSeleccionado(IdRef){

	var arqueo;
	log("Peticion de cargar datos de en index:"+IdRef);
	
	try {		
		arqueo = UltimosArqueos["arqueo_"+IdRef];
	} catch(e){
		log("W: no encontrados datos en index:"+IdRef);
		return false;
	}
	
	if(!arqueo){
		log("W: arqueo no contiene datos. / index:"+IdRef);
		return;// alert("No hay arqueos para esta");			
	}	
				 	 	
	var	url = "arqueoservices.php?modo=getDatosActualizadosArqueo&IdArqueo="+arqueo.IdArqueo+			
			"&IdLocal="+ Local.IdLocalActivo+	"&r=" + Math.random();
			
	xrequest = new XMLHttpRequest();
	
	xrequest.open("POST",url,false);
	xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');	
	xrequest.send(null);
	
	
	if (!xrequest.responseText)
		alert("No hay conexion con el servidor");	
	
	try {
		var arqueoactualizado = eval( "(" + xrequest.responseText + ")" );
	} catch(e){
		//Degradacion amistosa: si no puede coger datos actualizados, mantiene los actuales conocidos.
		arqueoactualizado = arqueo;
	}
	
	
	
 	UltimosArqueos["arqueo_"+IdRef] = arqueoactualizado;		

    log("Nos traemos del server:"+xrequest.responseText);		 	 
    log("Habia en casa:"+arqueo.toSource());
    
	//ActualizarVisualizacion(arqueo);
	ActualizarVisualizacion(arqueoactualizado);
	CambioListaMovimientos( arqueo.IdArqueo );	
}


function ActualizarVisualizacion(arqueo){
	log("I: actualizando datos de arqueo:"+arqueo.toSource());
	var sign = " EUR";

	//Balances
	id("saldoInicialText").setAttribute("value", formatDinero(arqueo.ImporteApertura) + sign);
	id("ingresosText").setAttribute("value", formatDinero(arqueo.ImporteIngresos) + sign);
	id("gastosText").setAttribute("value", formatDinero(arqueo.ImporteGastos) + sign);
	id("aportacionesText").setAttribute("value", formatDinero(arqueo.ImporteAportaciones) + sign);
	id("sustraccionesText").setAttribute("value", formatDinero(arqueo.ImporteSustracciones) + sign);
	
	//Teorico de cierre
	id("TeoricoCierre").setAttribute("value", formatDinero(arqueo.ImporteTeoricoCierre) + sign);		

	//Importe de cierre
	id("cierreCajaText").setAttribute("value",formatDinero(arqueo.ImporteCierre) + sign);


	//Importe descuadre
	id('descuadreCajaText').setAttribute("value",formatDinero(0-arqueo.ImporteDescuadre) + sign);


	//Fecha
	id("estadoCajaFecha").setAttribute("value",datetimeToFechaCastellano(arqueo.FechaCierre));
	
	//Estado abierto/cerrado
    if (arqueo.esCerrada>0){
    	id("estadoCajaTexto").setAttribute("value","CERRADA");
    } else 
		id("estadoCajaTexto").setAttribute("value","ABIERTA");
}



function test(cosaParaVer){
	prompt( "D:",cosaParaVer.toSource());
}

/*---------------------------------------------------*/

var xrequestListaMovimientos = false;
var MovimientosDineros = false;


function CambioListaMovimientos(IdArqueo){
	
	if (!IdArqueo) {
		//return alert('Arqueo inexistente');
		return;//Si no hay arqueo definido, no hay datos que cargar
	}


	//var	url = "arqueoservices.php?modo=getListaUltimosDiez&IdLocal="+Local.IdLocalActivo+"&r=" + Math.random();
	var	url = "arqueoservices.php?modo=getMovimientos&IdArqueo="+IdArqueo+"&r=" + Math.random();
	xrequestListaMovimientos = new XMLHttpRequest();
	
	xrequestListaMovimientos.open("POST",url,true);
	xrequestListaMovimientos.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	xrequestListaMovimientos.onreadystatechange = RecibeListaMovimientos;
	xrequestListaMovimientos.send(null);			
}

function RecibeListaMovimientos() {
	
	if (xrequestListaMovimientos.readyState!=4) 
			return;			
	//TODO: procesar aqui un arqueo que se solicitu su carga
	
	var obj = eval( "(" + xrequestListaMovimientos.responseText+ ")" );
	var t;		
	var FechaApertura, IdArqueo;
	
	if (!obj){
		return alert("No se obtiene respuesta del servidor");
	}
	
							
	//Reinicializamos los arqueos 							
	MovimientosDineros = new Array(); 
	
	t=0;
	while( obj["mov_"+t] ){
		MovimientosDineros["mov_"+t] = obj["mov_"+t];
		t++;		
	}
	if(!t){
		//alert('No hubo movimientos');
		//Si no hay movimientos, no hay necesidad de generar nada, hemos terminado
		//return;
	}
	
	log("Se recibieron '"+t+"' movimientos");

	RegenerarCuadroDeMovimientos();
}



function VaciarDeHijosTag(padreNombre,Tag){	
	var padre = id(padreNombre);
	while( padre.childNodes.length && padre.lastChild && padre.lastChild.getAttribute(Tag) ){
		padre.removeChild( padre.lastChild );
	}		
}


function RegenerarCuadroDeMovimientos(){
	//VaciarDeHijos("listaMovimientos");
	VaciarDeHijosTag("listaMovimientos","esMov");
	//Borrando a mano

	var listaMov = id("listaMovimientos");
	var t = 0;
	while( mov = MovimientosDineros["mov_"+t] ) {
	
		var xrow = document.createElement("listitem");
		xrow.setAttribute("esMov",true);
		
		xcell = document.createElement("listcell");	xcell.setAttribute("esMov",true);	
		xcell.setAttribute("label",mov.TipoOperacion );

		xrow.appendChild(xcell);

		xcell = document.createElement("listcell");	xcell.setAttribute("esMov",true);	
		xcell.setAttribute("label",mov.Concepto );

		xrow.appendChild(xcell);

		xcell = document.createElement("listcell");	xcell.setAttribute("esMov",true);	
		xcell.setAttribute("label",formatDinero(mov.Importe) );

		xrow.appendChild(xcell);
		
		xcell = document.createElement("listcell");	xcell.setAttribute("esMov",true);	
		xcell.setAttribute("label",datetimeToFechaCastellano(mov.FechaInsercion) );

		xrow.appendChild(xcell);

		
		listaMov.appendChild(xrow);			
		
		t++;
	}

}


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


/*---------------------------------------------------*/


function CambioAuxliaresDeInterface(){

}



function Comando_CerrarCaja(){

	var p = confirm(po_quierecerrar);
	if (!p) return;//Abortado por decision del usuario 

	Comando_ArqueoCaja();
	
	//Guardamos datos actuales arqueo como actual
	// ..y creamos el siguiente.	 
	Local.IdArqueoActual = ActualizarDatosDeCierre();	
		
	//Recargamos 		
	//ActualizarComboArqueos();
	document.location = "arqueo2.php?r="+Math.random();
	
}


function OLD_Comando_CerrarCaja(){

}

/*---------------------------------------------------*/



function Comando_ArqueoCaja(){
	
	var p = prompt(po_importereal,"0");	
	p = parseMoney(p); 		 	
	
	 	
	log("Comando arqueocaja: actualizando arreglo.. "); 	
	sv_actualizarArreglo(p); 	
				

	if (!UltimosArqueos){
		UltimosArquos = new Array();
	}		
	
	if (!UltimosArqueos["arqueo_0"]){
		UltimosArqueos["arqueo_0"] = new Object();
	}				
				
	UltimosArqueos["arqueo_0"].ImporteCierre = p;
							
	//Recargamos 		
	log("Comando arqueocaja: cambiolistaarqueos.. ");
	CambioListaArqueos();	
		
	//Cargamos primero
	log("Comando arqueocaja: cargamos el arqueo ultimo.. ");
	CargarArqueoSeleccionado(0);
}

function ActualizarDatosDeCierre(){

   log("arquearYAbrirNuevaCaja para local:"+Local.IdLocalActivo);
   
	var	url = "arqueoservices.php?modo=arquearYAbrirNuevaCaja&IdLocal="+Local.IdLocalActivo+"&r=" + Math.random();
	var xrequest = new XMLHttpRequest();
	
	xrequest.open("POST",url,false);
	xrequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
	xrequest.send(null);	

	var res = xrequest.responseText;
		
	return 0;

}

//
// Actualiza la cantidad en caja en el servidor del ultimo arqueo 
//

function sv_actualizarArreglo(CantidadCierre){


   log("Marcando nueva cantidad en caja.."+CantidadCierre);
    

	var	url = "arqueoservices.php?modo=actualizarCantidadCaja&IdLocal="+Local.IdLocalActivo+
		"&cantidad="+escape(CantidadCierre)+"&r=" + Math.random();
	var xrequest = new XMLHttpRequest();
	
	xrequest.open("POST",url,false);
	xrequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
	xrequest.send(null);	

	var res = xrequest.responseText;	
}


function onConsultarCaja(){

	var	sel = id("SeleccionArqueo");
	//alert( sel.toSource() );

}


/*---------------------------------------------------*/

function Comando_HacerUnAporte(){
	var cantidad = id("importeText").value;
	var concepto = id("conceptoText").value;

	var	url = "arqueoservices.php?modo=hacerAporteDinero&IdLocal="+Local.IdLocalActivo+
		"&cantidad="+escape(cantidad)+"&concepto="+escape(concepto)+"&r=" + Math.random();
		
	var xrequest = new XMLHttpRequest();
	
	xrequest.open("POST",url,false);
	xrequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
	xrequest.send(null);		 
	alert("Se hizo un aporte de "+cantidad+" EUR");
	
	id("importeText").setAttribute("value","");
	id("conceptoText").setAttribute("value","");
	id("importeText").value = "";
	id("conceptoText").value = "";
	CargarArqueoSeleccionado(0);
	
}


function Comando_HacerUnaSubstraccion(){
	var cantidad = id("importeTextSubs").value;
	var concepto = id("conceptoTextSubs").value;

	var	url = "arqueoservices.php?modo=hacerSubstraccionDinero&IdLocal="+Local.IdLocalActivo+
		"&cantidad="+escape(cantidad)+"&concepto="+escape(concepto)+"&r=" + Math.random();
		
	var xrequest = new XMLHttpRequest();
	
	xrequest.open("POST",url,false);
	xrequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
	xrequest.send(null);		 
	alert("Se hizo una substraci&otilde;n de "+cantidad+" EUR");
	
	id("importeTextSubs").setAttribute("value","");
	id("conceptoTextSubs").setAttribute("value","");
	id("importeTextSubs").value = "";
	id("conceptoTextSubs").value = "";
	CargarArqueoSeleccionado(0);
}

function Comando_HacerUnIngreso(){
	var cantidad = id("importeTextIngreso").value;
	var concepto = id("conceptoTextIngreso").value;

	var	url = "arqueoservices.php?modo=hacerIngresoDinero&IdLocal="+Local.IdLocalActivo+
		"&cantidad="+escape(cantidad)+"&concepto="+escape(concepto)+"&r=" + Math.random();
		
	var xrequest = new XMLHttpRequest();
	
	xrequest.open("POST",url,false);
	xrequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
	xrequest.send(null);		 
	alert("Se hizo un ingreso de "+cantidad+" EUR");
	
	id("importeTextIngreso").setAttribute("value","");
	id("conceptoTextIngreso").setAttribute("value","");
	id("importeTextIngreso").value = "";
	id("conceptoTextIngreso").value = "";
	CargarArqueoSeleccionado(0);
}


function Comando_HacerUnGasto(){
	var cantidad = id("importeTextGasto").value;
	var concepto = id("conceptoTextGasto").value;

	var	url = "arqueoservices.php?modo=hacerGastoDinero&IdLocal="+Local.IdLocalActivo+
		"&cantidad="+escape(cantidad)+"&concepto="+escape(concepto)+"&r=" + Math.random();
		
	var xrequest = new XMLHttpRequest();
	
	xrequest.open("POST",url,false);
	xrequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
	xrequest.send(null);		 
	alert("Se hizo un gasto de "+cantidad+" EUR");
	
	id("importeTextGasto").setAttribute("value","");
	id("conceptoTextGasto").setAttribute("value","");
	id("importeTextGasto").value = "";
	id("conceptoTextGasto").value = "";
	CargarArqueoSeleccionado(0);
}


/*---------------------------------------------------*/

function CerrarCajaActual(){


}

function TestFuncionamiento(){	
	alert(xrequestListaArqueos.toSource());		
}

/* --------------------------------------------------*/

function onLoadFormulario(){
	CambioListaArqueos();
	
	setTimeout("DemonioPrimeraApertura()",200); 
}



function DemonioPrimeraApertura(){
	
	if (esRecibidaListaArqueos) {
	 	//cargamos el ultimo arqueo que tenemos.
		CargarArqueoSeleccionado(0); 		 	
	}else {
		//aun no se ha cargado la lista de arqueos..
		setTimeout("DemonioPrimeraApertura()",200);//reintentamos		
	}// reintentamos..hasta cargar la lista de arqueos 	
}


/* --------------------------------------------------*/


//Desde "1.331,33" hacia "1331.33"
function CleanMoney(cadena) {
	return parseMoney(new String(cadena) );
}

function parseMoney (cadena) {
	//var cadoriginal = cadena;
	
	if (!cadena) 
		return 0.0	
	
	cadena = new String( cadena );
	if( !cadena.replace ){
		 return cadena;		 	
	}	
	
	cadena = cadena.replace(/\./g,"");
	cadena = cadena.replace(/\,/g,".");
	cadena = parseFloat( cadena );	
	
	if (isNaN( cadena ))
		return 0.0;
		
	return cadena;
}

function log(text){
  //id("log").setAttribute("value",  id("log").getAttribute("value") + "\n" + text );
}





