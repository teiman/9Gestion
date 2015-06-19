<?php

require("../../tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");

header("Content-Type: application/vnd.mozilla.xul+xml");
header("Content-languaje: es");
	

$esTPV = ($_GET["area"]=="tpv");
	
echo $CabeceraXUL;

$area = CleanRealMysql($_GET["area"]);

if($area){
	 $sqlarea = " AND (Area ='$area') ";	
}


$outItems = "";

$sql = "SELECT IdListado,NombrePantalla,CodigoSQL,Peso FROM ges_listados WHERE (Eliminado=0) $sqlarea ORDER BY Peso DESC, NombrePantalla ASC";

$res = query($sql);



// function to change german umlauts into ue, oe, etc.
function cv_input($str){
     $out = "";
     for ($i = 0; $i<strlen($str);$i++){
           $ch= ord($str{$i});
           switch($ch){
               case 241: $out .= "&241;"; break;           
               case 195: $out .= "";break;   
               case 164: $out .= "ae"; break;
               case 188: $out .= "ue"; break;
               case 182: $out .= "oe"; break;
               case 132: $out .= "Ae"; break;
               case 156: $out .= "Ue"; break;
               case 150: $out .= "Oe"; break;

               default : $out .= chr($ch) ;
           }
     }
     return $out;
}

function strictify ( $string ) {
       $fixed = htmlspecialchars( $string, ENT_QUOTES );

       $trans_array = array();
       for ($i=127; $i<255; $i++) {
           $trans_array[chr($i)] = "&#" . $i . ";";
       }

       $really_fixed = strtr($fixed, $trans_array);

       return $really_fixed;
}
   
if ($res) {
	while ($row = Row($res)) {
		$NombrePantalla = $row["NombrePantalla"];		
		$id = $row["IdListado"];		
		
		$activos = DetectaActivos( $row["CodigoSQL"]);
		
		$code .= $row["CodigoSQL"] . "\n----------------------------------\n";
		
		$NombrePantalla = cv_input($NombrePantalla);					
		$NombrePantalla = strictify($NombrePantalla);
		
		$peso = $row["Peso"];
		
		if ($peso){
			$style="font-weight: bold";	
		} else {
			$style="";
		}
			
		$outItems = $outItems . "<menuitem style='$style' label='$NombrePantalla' value='$id' oncommand='SetActive(\"$activos\")'/>\n";			
	}
}
		
		

function DetectaActivos($cod){
	global $esTPV;
	$a = "";
	
	if( strpos($cod,'%IDIDIOMA%') >0 ){
		$a .= "IdIdioma,";	
	}
	if( strpos($cod,'%DESDE%')  >0){
		$a .= "Desde,";	
	}
	if( strpos($cod,'%HASTA%') >0){
		$a .= "Hasta,";	
	}
	if( strpos($cod,'%IDTIENDA%')  >0 and !$esTPV){
		$a .= "IdTienda,";	
	}
	if( strpos($cod,'%IDFAMILIA%')  >0){
		$a .= "IdFamilia,";	
	}	
	if( strpos($cod,'%IDSUBFAMILIA%')  >0){
		$a .= "IdSubFamilia,";	
	}	
	if( strpos($cod,'%IDARTICULO%')  >0){
		$a .= "IdArticulo,";	
	}		
	if( strpos($cod,'%FAMILIA%')  >0){
		$a .= "IdFamilia,";	
	}	
	if( strpos($cod,'%IDMODISTO%')  >0){
		$a .= "IdModisto,";	
	}
	if( strpos($cod,'%STATUSTBJOMODISTO%')  >0){
		$a .= "StatusTrabajo,";	
	}
	if( strpos($cod,'%IDPROVEEDOR%')  >0){
		$a .= "IdProveedor,";	
	}
	if( strpos($cod,'%IDCENTRO%')  >0){
		$a .= "IdCentro,";	
	}
	if( strpos($cod,'%IDUSUARIO%')  >0){
		$a .= "IdUsuario,";	
	}						
	if( strpos($cod,'%REFERENCIA%')  >0){
		$a .= "Referencia,";	
	}						
	if( (strpos($cod,'%IDPRODBASEDESDECB%')>0) or (strpos($cod,'%CODIGOBARRAS%')>0) ){
		$a .= "CB,";	
	}	

	return $a;
}

echo str_replace("@","?","<@xul-overlay href='".$_BasePath . "modulos/datepicker/datepicker-overlay.php' type='application/vnd.mozilla.xul+xml'@>");

?>
<window id="login-9gestion" title="Usuarios 9Menorca"
        xmlns:html="http://www.w3.org/1999/xhtml"        
        xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">       		
<script type="application/x-javascript" src="<?php echo $_BasePath; ?>modulos/datepicker/calendario.js"/>		
<popup  id="oe-date-picker-popup" position="after_start" oncommand="RecibeCalendario( this )" value=""/>	

<groupbox>	
	<hbox>	
	<menulist    id="esListas" label="Listados" class="media">						
         <menupopup>
		<?php echo $outItems; ?>
	</menupopup>
	</menulist>
	<button label="Listar" oncommand="CambiaListado()"/>
	</hbox>	
	
	<hbox>
	
	<hbox id="getDesde" collapsed="true">
	<toolbarbutton image="img/calendar-up.gif" label="" onmousedown="EnviaCalendario('oe-date-picker-popup','Desde')" popup="oe-date-picker-popup" position="after_start" />		
	<label value="Desde"/>
	<textbox class="media" id="Desde" value=""/>
	</hbox>
	
	<hbox id="getHasta" collapsed="true">
	<toolbarbutton image="img/calendar-up.gif" label="" onmousedown="EnviaCalendario('oe-date-picker-popup','Hasta')" popup="oe-date-picker-popup" position="after_start" />		
	<label value="Hasta"/>
	<textbox class="media" id="Hasta" value=""/>
	</hbox>
	
	<hbox id="getIdTienda" collapsed="true">
	<menulist  id="Local">						
	<menupopup>
	 <menuitem label="Elije local"/>
	<?php echo genXulComboAlmacenes(false,"menuitem") ?>
	 </menupopup>
	</menulist>	
	</hbox>	
	
	</hbox>
	<hbox id="getIdFamilia" collapsed="true">
	<menulist  id="Familia">						
	<menupopup>
	 <menuitem label="Elije familia"/>
	<?php echo genXulComboFamilias(false,"menuitem") ?>
	 </menupopup>
	</menulist>	
	</hbox>	
	<hbox>

	<hbox id="getIdModisto" collapsed="true">
	<menulist  id="Modisto">						
	<menupopup>
	 <menuitem label="Elije modisto"/>
	<?php echo genXulComboModistos(false,"menuitem") ?>
	 </menupopup>
	</menulist>	
	</hbox>		

	<hbox id="getStatusTrabajo" collapsed="true">
	<menulist  id="StatusTrabajo">						
	<menupopup>
	 <menuitem label="Elije estado"/>
	<?php echo genXulComboStatusTrabajo(false,"menuitem") ?>
	 </menupopup>
	</menulist>	
	</hbox>		
	
	<hbox id="getIdUsuario" collapsed="true">
	<menulist  id="IdUsuario">						
	<menupopup>
	 <menuitem label="Elije usuario"/>
	<?php echo genXulComboUsuarios(false,"menuitem") ?>
	 </menupopup>
	</menulist>	
	</hbox>			
	
	<hbox id="getReferencia" collapsed="true">
	<label value="Referencia"/>
	<textbox class="media" id="Referencia" value=""/>
	</hbox>			
	
	<hbox id="getCB" collapsed="true">
	<label value="Codigo barras"/>
	<textbox class="media" id="CB" value=""/>
	</hbox>				
</hbox>
	
</groupbox>
<iframe id="webarea" src="about:blank" flex='1'/>
<script><![CDATA[

var esTPV = <?php echo intval($esTPV); ?>;
var IdLocalActual = <?php echo intval(getSesionDato("IdTienda")); ?>

function id(nombre) { return document.getElementById(nombre); };


function CambiaListado() {
	var idlista 	= id("esListas").value;
	var web 	= id("webarea");
	var url = "listado.php?id="+idlista+
		"&Desde="+id("Desde").value +
		"&Hasta="+id("Hasta").value +		
		"&IdFamilia="+id("Familia").value+
		"&IdModisto="+id("Modisto").value+
		"&StatusTrabajoModisto="+id("StatusTrabajo").value+		
		"&IdUsuario="+id("IdUsuario").value+
		"&Referencia="+ escape(id("Referencia").value)+
		"&cb="+escape(id("CB").value)+
		"&r=" + Math.random();

	if(!esTPV){
		url += "&IdLocal="+id("Local").value;
	} else {
		url += "&IdLocal="+IdLocalActual;
	}

	web.setAttribute("src", url) ;


}

function Mostrar( idmostrar){
	var xthingie = id("get"+ idmostrar );
	
	if ( xthingie ){
		xthingie.setAttribute("collapsed","false");	
	}
}

function SetActive( val ){
	var dinterface = val.split(",");
	
	id("getDesde").setAttribute("collapsed","true");
	id("getHasta").setAttribute("collapsed","true");
	id("getIdTienda").setAttribute("collapsed","true");
	id("getIdFamilia").setAttribute("collapsed","true");
	id("getIdModisto").setAttribute("collapsed","true");	
	id("getStatusTrabajo").setAttribute("collapsed","true");
	id("getIdUsuario").setAttribute("collapsed","true");
	id("getReferencia").setAttribute("collapsed","true");
	id("getCB").setAttribute("collapsed","true");
	
	for( t=0;t<dinterface.length;t++){
		Mostrar(dinterface[t]);
	}
	
}

/*
<?php

//echo $code;

?>

*/

//]]></script>
</window>
