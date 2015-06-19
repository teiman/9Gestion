<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");

function OpcionesBusqueda($retorno) {
	global $action;
	
	
	$ot = getTemplate("xulBusquedaAvanzada");
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template no encontrado");
		return false; }
	
	$idprov = getSesionDato("FiltraProv");
	$idmarca = getSesionDato("FiltraMarca");
	$idcolor = getSesionDato("FiltraColor");
	$idtalla = getSesionDato("FiltraTalla");

		
	$ot->fijar("action",$action);
	$ot->fijar("pagRetorno",$retorno);
	$ot->fijar("comboProveedores",genXulComboProveedores($idprov));
	$ot->fijar("comboMarcas",genXulComboMarcas($idmarca));
		
		//echo q($idcolor,"color a mostrar en template");
		//echo q(intval($idcolor),"intval color a mostrar en template");
			
	if (intval($idcolor) >=0)
			$ot->fijar("comboColores",genXulComboColores($idcolor));
	else
			$ot->fijar("comboColores",genXulComboColores("ninguno"));
			
	$ot->fijar("comboTallas",genXulComboTallas($idtalla));
		
	
	echo $ot->Output();	
}

function GeneraXul($retorno) {
?>	
				   <grid> 
				     <columns> 
				       <column flex="1"/><column flex="1"/>
				     </columns>
				    <rows>
<row>
<caption label="<?php echo _("Proveedor") ?>"/>				    
<menulist id="idprov">
 <menupopup>
  <menuitem label="Elije proveedor" style="font-weight: bold"/>
<?php echo genXulComboProveedores(false,"menuitem") ?>
 </menupopup>
</menulist>
</row>
<row>
<caption label="<?php echo _("Marca") ?>"/>
<menulist  id="idmarca">
 <menupopup>
 <menuitem label="Elije marca" style="font-weight: bold"/>
<?php echo genXulComboMarcas(false,"menuitem") ?>
 </menupopup>
</menulist>
</row>
<row>
<caption label="<?php echo _("Color") ?>"/>
<menulist  id="idcolor">
 <menupopup>
 <menuitem label="Elije color" style="font-weight: bold"/>
<?php echo genXulComboColores(false,"menuitem") ?>
 </menupopup>
</menulist>
</row><row><caption label="<?php echo _("Talla") ?>"/>
<menulist  id="idtalla">
 <menupopup>
 <menuitem label="Elije talla" style="font-weight: bold"/>
<?php echo genXulComboTallas(false,"menuitem") ?>
 </menupopup>
</menulist>
</row><row><caption label="<?php echo _("Familia") ?>"/>
<menulist  id="idfamilia">
 <menupopup>
 <menuitem label="Elije familia" style="font-weight: bold"/>
<?php echo genXulComboFamilias(false,"menuitem") ?>
 </menupopup>
</menulist>
</row>
</rows>
</grid>
<button  image="img/find16.png"  label='<?php echo _("Buscar") ?>' oncommand="buscar()"/>
	
<script><![CDATA[

function buscar()
{
  var tc;

  var idprov = document.getElementById("idprov").value;
  var idcolor=  document.getElementById("idcolor").value;
  var idmarca =  document.getElementById("idmarca").value;
  var idtalla = document.getElementById("idtalla").value;
  var idfam = + document.getElementById("idfamilia").value;
  
  window.parent.Productos_buscarextra(idprov,idcolor,idmarca,idtalla,idfam,tc);
     
}
]]></script>

<?php	
}


StartXul(_("Elije color"));


switch($modo){
	default:
	case "avanzada":
	$retorno = $_GET["vuelta"];
	//OpcionesBusqueda($retorno);
	GeneraXul($retorno);
	break;	
}

EndXul();

?>
