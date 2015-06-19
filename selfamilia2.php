<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");

switch($modo){
	case "salvafamilia":
		$nombre = CleanText($_GET["familia"]);		
		if (strlen($nombre)>1)	CrearFamilia($nombre);							
		break;	
		
	case "salvasubfamilia":			
		$padre = CleanID($_GET["IdFamilia"]);
		$nombre = CleanText($_GET["Nombre"]);					
		CrearSubFamilia($nombre,$padre);	
		break;				
	case "getsubfamilia":
		$idfamilia = CleanID($_GET["IdFamilia"]);

		$subfamilias = genArraySubFamilias($idfamilia);

		foreach ($subfamilias as $key=>$value){
			echo "$value=$key\n";			
		}		
		
		exit();
		break;
}



StartXul(_("Elige familia"));

?>

<groupbox flex='1'> 
	<caption label='<?php echo _("Familia"); ?>'/>
<script>//<![CDATA[

		function id(valor){
			return document.getElementById(valor);
		}

		var sub = new Object();
		var fam = new Object();

<?php
		$familias = genArrayFamilias();				
		foreach ($familias as $key=>$value){
			echo "fam[$key] = '$value';\n";			
		}
?>
		function UsarNuevoFam() {              
			var url;
			var familia = document.getElementById('nueva').value;			

            if (!familia || familia == '') return;
            
			url = 'selfamilia2.php';
			url = url +'?modo=salvafamilia';
            url = url + '&familia=' + familia;
			document.location.href = url			
		}

		function UsarNuevoSub() {              
			var url;
			var famvalue = id("Familia").value;	
			var subfamilia = document.getElementById('nueva').value;			

            if (!subfamilia || subfamilia == '') return;
            if (!famvalue ) return;
            
			url = 'selfamilia2.php';
			url = url +'?modo=salvasubfamilia';
            url = url + '&Nombre=' + subfamilia;
            url = url + '&IdFamilia=' + famvalue;
			document.location.href = url			
		}
				
		function VaciarListaSubFamilias(){
			var lista = document.getElementById("Subfamilia");
	
			
			while( lista.hasChildNodes()) {
			    lista.removeChild(lista.childNodes[0]);
		    }
		    
		    /*
		    var xsub= 	document.createElement("listitem");
			xsub.setAttribute("label","Cargando...");
			xsub.setAttribute("id","cargando");
			lista.appendChild(xsub);*/
					
		}
								
		
		function ProcesarSubfamilias(ordenes){		
			if (!ordenes)
				return;

			var xroot = document.getElementById("Subfamilia");
			var datos = ordenes.split("\n");
			var valores;
							


			for(var t=0;t<datos.length;t++){				
				valores = datos[t].split("=");		
				
				if (valores && valores[0] && valores[1] ) {
					var xsub= 	document.createElement("listitem");
					xsub.setAttribute("label",valores[0]);
					xsub.setAttribute("value",valores[1]);												
					xroot.appendChild( xsub );
					sub[valores[1]] = valores[0];
				}
			}
			
		}
		
		
		function RecalculaSubfamilia(valor){								
			var xrequest = new XMLHttpRequest();	
			var url;										
					

			id("familiatxt").setAttribute("label",fam[valor]);		
			id("subfamiliatxt").setAttribute("label","???");		
					
			VaciarListaSubFamilias();	
			
			
			
	
			url = "selfamilia2.php?modo=getsubfamilia&IdFamilia="+valor;
			xrequest.open("GET",url,false);
			xrequest.send(null);
  
			ProcesarSubfamilias(xrequest.responseText);					
		}
		
		function DevolverFamySubFam( subfamilia ){
			var subtxt = sub[subfamilia];
			id("subfamiliatxt").setAttribute("label",subtxt);
			var famvalue = id("Familia").value;		
			
			//alert("Envia sub:"+subfamilia+",fam:"+famvalue+",sub["+sub[subfamilia]+",fam["+fam[famvalue]);
					
			opener.changeFamYSub(subfamilia,famvalue,sub[subfamilia],fam[famvalue]);
			window.close();
			return true;
		}

//]]></script>

<hbox flex='1'>
	<listbox id='Familia'   onclick='RecalculaSubfamilia(this.value)' flex='1'>
		<?php  echo genXulComboFamilias();	?>
	</listbox>
	<listbox id='Subfamilia'  onclick='DevolverFamySubFam(this.value)' flex='1'>
	</listbox>
</hbox>		
</groupbox>

<hbox>
<caption label="Seleccion:"/><spacer style="width: 8px"/><caption id="familiatxt" label="???"/><caption label="-"/><caption id="subfamiliatxt"  label="???"/>
<?php 

//<spacer flex="1"/><button label='<?php echo _("Ok") ?>' oncommand="escoge()"/>

?>
</hbox>

<hbox>
<groupbox flex='1'>
	<caption label='<?php echo _("Crear nueva") ?>'/>		
	<textbox id='nueva'/>
	<hbox flex="1">
	<button flex='1' label='<?php echo _("Familia") ?>' onkeypress='if (event.which == 13) UsarNuevoFam()' oncommand='UsarNuevoFam()'/>
	<button flex='1' label='<?php echo _("Subfamilia") ?>' onkeypress='if (event.which == 13) UsarNuevoSub()' oncommand='UsarNuevoSub()'/>
	</hbox>
</groupbox>
</hbox>

<?php

EndXul();

?>
