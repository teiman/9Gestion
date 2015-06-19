

/* DOM */


//Define id si no ha sido definida ya.
try {
	if (id);
} catch(e) {
	function id(nombrevictima){
		return document.getElementById(nombrevictima);
	}
}

var Dom = new Object();

Dom.create = function( tipoDeNodo, datosAjustar){
//	var fake = document.createElement("box");	
	var xnodo = document.createElement(tipoDeNodo);	
	var dato,paramdatos;
	var t=0;
	
	//xnodo.setAttribute("statustext",tipoDeNodo+":"+Math.random());
	
	if(datosAjustar){	
		paramdatos = datosAjustar.split(",");
		for (t=0;t<paramdatos.length;t++){
			dato = paramdatos[t].split("=");
			xnodo.setAttribute(dato[0],dato[1]);
		}	
	}
	return xnodo;
}

Dom.MatarTodosHijos = function (padreNodos){	
	var padre = id(padreNodos);
	while( padre.childNodes.length ){
		padre.removeChild( padre.childNodes[0] );
	}		
}

/* GRID */

var Meca = new Object();

Meca.genera = function (victima){

	var xtree = Dom.create("tree","flex=1");
	var xtreecols = Dom.create("treecols");
	var xtreecol;
	xtreecol = Dom.create("treecol","label=Filename,flex=1");
	xtreecols.appendChild(xtreecol);
	
	xtreecol = Dom.create("treecol","label=Location,flex=1");
	xtreecols.appendChild(xtreecol);
	
	xtreecol = Dom.create("treecol","label=Size,flex=1");
	xtreecols.appendChild(xtreecol);
	
	xtree.appendChild( xtreecols );
	
	var xtreechildren = Meca.create("treechildren");
	var xtreeitem = Meca.create("treeitem");
	var xtreerow = Meca.create("treerom");
	
	var xtreecell = Dom.create("treecell","Label=mozilla");
	xtreerow.appendChild(xtreecell);
	var xtreecell = Dom.create("treecell","Label=/data");
	xtreerow.appendChild(xtreecell);
	var xtreecell = Dom.create("treecell","Label=200 KB");
	xtreerow.appendChild(xtreecell);

	xtreeitem.appendChild( xtreerow);		
	xtreechildren.appendChild( xtreeitem );	
	xtree.appendChild( xtreechildren );
		
	id(victima).appendChild(xtree);
}


Meca.generaCruzadoProductos = function (victima, base){
	var xtree = Dom.create("tree","flex=1,enableColumnDrag=true,hidecolumnpicker=true,seltype=single");
	var xtreecols = Dom.create("treecols");
	var xtreecol;
	
	var heads = base.heads;
	var numheads = base.numheads;
	var nombre;
	
	var xtarget = id(victima);
	
	
	for(var t=0;t<numheads;t++){	
		nombre = heads["talla_"+t];
		xtreecol = Dom.create("treecol","flex=0,label="+nombre+",id=treecol_"+nombre+"_"+t);
		
		xtreecol.setAttribute("style","font-weight: bold");
		if (t==1){
			xtreecol.setAttribute("label","Color");
			//xtreecol.setAttribute("flex","1");
			xtreecol.setAttribute("style","min-width: 100px;font-weight: bold");			
		}
		
		if (t==0){
			xtreecol.setAttribute("style","min-width: 160px;font-weight: bold");		
		}
		
		
		
		xtreecols.appendChild(xtreecol);							
	}
		
	xtree.appendChild( xtreecols );	
	var xtreechildren = Dom.create("treechildren");
	
	var rows = base.rows;	
	var row,xtreecell,j,k;
	var xtreeitem;
	var xtreerow,rowheadtext;

	var rowhead = 0;
	
	var oldcero ="";
		
	
	for(var k=0;k<rows.length;k++){		
		var row = rows[k];								
		
		if (row[0]!=oldcero){				
			var firstLine = "firstLine";			
			oldcero = row[0];
		} else {
			var firstLine = "";
		}
		
		
		var	xtreeitem = Dom.create("treeitem","");
		var xtreerow = Dom.create("treerow");	
		
		for(j=0;j<row.length;j++){		
			
			if (j%2) {
				classpar = "esPar";
			} else 
				classpar = "noespar";													 
							
			xcell = Dom.create("treecell","label="+row[j]+",align=center,class="+classpar);			
			xcell.setAttribute("properties","colum_"+j+" celda " + firstLine );							
			
			xtreerow.appendChild(xcell);
		}	
		xtreeitem.appendChild( xtreerow);				
		xtreechildren.appendChild( xtreeitem );
		

	}
				
	xtree.appendChild( xtreechildren );			
	
	xtarget.setAttribute("flex",1);
	xtarget.appendChild(xtree);
}




Meca.generaTable = function (victima, base){

	var xtree = Dom.create("tree","flex=1,enableColumnDrag=true,hidecolumnpicker=true,seltype=single");
	var xtreecols = Dom.create("treecols");
	var xtreecol;
	
	var heads = base.heads;
	var numheads = base.numheads;
	var nombre;
	
	var xtarget = id(victima);
	
	
	for(var t=0;t<numheads;t++){	
		nombre = heads["talla_"+t];
		xtreecol = Dom.create("treecol","flex=0,label="+nombre+",id=treecol_"+nombre+"_"+t);
		xtreecols.appendChild(xtreecol);	
	}
		
	xtree.appendChild( xtreecols );	
	var xtreechildren = Dom.create("treechildren");
	
	var rows = base.rows;	
	var row,xtreecell,j,k;
	var xtreeitem;
	var xtreerow,rowheadtext;

	var rowhead = 0;
	for(var k=0;k<rows.length;k++){
		var	xtreeitem = Dom.create("treeitem");
		var xtreerow = Dom.create("treerow");		
		var row = rows[k];						
		for(j=0;j<row.length;j++){		
			
			if (j%2) {
				classpar = "esPar";
			} else 
				classpar = "noespar";
							
			/*if (row[j]<1){
				addFix = ",collapse=true";
			} else addFix = "";*/							 
							
			xcell = Dom.create("treecell","label="+row[j]+",class="+classpar);
			xtreerow.appendChild(xcell);
		}	
		xtreeitem.appendChild( xtreerow);				
		xtreechildren.appendChild( xtreeitem );
	}
				
	xtree.appendChild( xtreechildren );			
	
	xtarget.setAttribute("flex",1);
	xtarget.appendChild(xtree);	
}


Meca.generaArbol = function (victima, base){

	var xtree = Dom.create("tree","flex=1,enableColumnDrag=true,hidecolumnpicker=true");
	var xtreecols = Dom.create("treecols");
	var xtreecol;
	
	var heads = base.heads;
	var numheads = base.numheads;
	var nombre;
	
	var xtarget = id(victima);
	
	
	for(var t=0;t<numheads;t++){	
		nombre = heads["talla_"+t];
		xtreecol = Dom.create("treecol","flex=1,label="+nombre+",id=treecol_"+nombre+"_"+t);
		xtreecols.appendChild(xtreecol);	
	}
		
	xtree.appendChild( xtreecols );	
	var xtreechildren = Dom.create("treechildren");
	
	var rows = base.rows;	
	var row,xtreecell,j,k;
	var xtreeitem;
	var xtreerow,rowheadtext;

	var rowhead = 0;
	for(var k=0;k<rows.length;k++){
		var	xtreeitem = Dom.create("treeitem");
		var xtreerow = Dom.create("treerow");		
		var row = rows[k];				
		for(j=0;j<row.length;j++){							
			xcell = Dom.create("treecell","label="+row[j]);
			xtreerow.appendChild(xcell);

		}	
		xtreeitem.appendChild( xtreerow);				
		xtreechildren.appendChild( xtreeitem );
	}
				
	xtree.appendChild( xtreechildren );			
	
	xtarget.setAttribute("flex",1);
	xtarget.appendChild(xtree);
	
}


Meca.cargarJSON = function (revisor,url,returned) {
//	var url = "testjson.php?CodigoBarras=90007006"
	
	var obj = new XMLHttpRequest();

	obj.open("GET",url,false);
	obj.send(null);
	
	var tex = "";
	var cr = "\n";
	
	var vendedor,serie,num,fecha,total,pendiente,estado,IdFactura;
	var node,t,i;	
	
	if (!obj.responseText)
		return alert(po_error);	
		
	var objres = eval( "(" + obj.responseText+ ")" );	
	
	if(!returned)
		revisor(objres);
	
	return objres;
}



Meca.cargarXML = function (revisor) {
	var url = "service.php"
	
	var obj = new XMLHttpRequest();

	obj.open("GET",url,false);
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
	
//	alert( obj.responseText);
	
	for (i=0; i<xml.childNodes.length; i++) {
		node = xml.childNodes[i];
		if (node && node.getAttribute){
			nombre = node.getAttribute("nombre")
			//alert(nombre);
			data = new Array();			
			data["nombre"] = nombre;						
			revisor(data);
		}					
	}
}