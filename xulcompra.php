<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");

StartXul(_("Compras")); 

define("TALLAJE_DEFECTO",1);


$op = new Producto();

$op->Crea(); 

$Referencia = $op->get("Referencia");
$Nombre 	= $op->get("Nombre");
$Marca 		= _("Varias");
$FamDefecto = _("Varias");

$primerCB 	= $op->get("CodigoBarras");

$IdFamiliaDefecto 		= getFirstNotNull("ges_familias","IdFamilia");
$IdSubFamiliaDefecto 	= getSubFamiliaAleatoria($IdFamiliaDefecto );

$FamDefecto = getIdFamilia2Texto($IdFamiliaDefecto ) . " - " .
	getIdSubFamilia2Texto( $IdFamiliaDefecto,$IdSubFamiliaDefecto );

		//$fam = getFirstNotNull("ges_familias","IdFamilia");
		//$this->set("IdFamilia",$fam,FORCE);
		//$this->set("IdSubFamilia",getSubFamiliaAleatoria($fam), FORCE);
		

?>
<hbox>

<groupbox>
<caption label="Caracteristicas"/>
<!-- alta de prod -->
<grid>
<rows> 
<row><caption class="media" label="Referencia"/><textbox class="media" id="Referencia" value="<?php echo $Referencia ?>"/></row>
<row><caption class="media" label="Ref Proveedor"/><textbox class="media" id="RefProv"/></row>
<row><caption class="media" label="Nombre"/><textbox class="media" id="Nombre" value="<?php echo $Nombre ?>"/></row>
<row><caption class="media" label="Descripcion"/><textbox class="media" multiline="true" id="Descripcion"/></row>
<row><caption class="media" label="Coste"/><textbox class="media" id="Coste" value="0"/></row>
<row><caption class="media" label="PVP"/><textbox class="media" id="PrevioVenta" value="0"/></row>
<row><caption class="media" label="Marca"/><box><toolbarbutton style="width: 32px !important" oncommand="CogerMarca()" label="+"/><textbox class="media" id="Marca" value="<?php echo $Marca ?>"  flex="1"/></box></row>
<row><caption class="media" label="Prov. hab"/><box><toolbarbutton style="width: 32px !important" oncommand="CogeProvHab()" label="+"/><textbox class="media" id="ProvHab" readonly="true" flex="1"/></box></row>
<row><caption class="media" label="Fam/Subfam"/><box><toolbarbutton style="width: 32px !important" oncommand="CogeFamilia()" label="+"/><textbox value="<?php echo $FamDefecto; ?>" flex="1" id="FamSub"/></box></row>
<row><caption class="media" label="Tallaje"/><box><toolbarbutton style="width: 32px !important" oncommand="CogeTallaje()" label="+"/><textbox readonly="true" class="media" id="Tallaje" flex="1"/></box></row>
<row><box><spacer flex="1" style="height: 8px"/></box><box/></row>

</rows>
</grid>

<!-- alta de prod -->
</groupbox>


<!-- listado compra tickets -->
    <vbox flex="1" class="listado">
	<spacer style="height: 16px"/>
    <caption label="<?php echo _("Tallas y colores") ?>" class="media" style="border-bottom: 1px black solid"/>	    
	<spacer style="height: 16px"/>
	
	<grid>
	<rows> 
	<row><caption class="media" label="Talla"/>
	<hbox>
	<toolbarbutton style="width: 32px !important" oncommand="CogeTalla()" label="+"/>
  	<menulist flex="2" class="media" id="Tallas" style="min-width: 7em">			
  	<menupopup class="media" id="elementosTallas">
<?php  	
	//function genXulComboTallas($selected=false,$xul="listitem",$IdTallaje=5){  	
  	echo  genXulComboTallas("32", "menuitem",TALLAJE_DEFECTO,"def");			
?>  	
	</menupopup>
	</menulist>
	</hbox>
</row>
<row><caption class="media" label="Color"/>
	<hbox>
	<toolbarbutton style="width: 32px !important" oncommand="CogeColor()" label="+"/>
<?php
	//function genXulComboColores($selected=false,$xul="listitem"){

  	echo '<menulist flex="2" style="min-width: 4em" class="media" id="Colores">';
  	echo '<menupopup class="media" id="elementosColores">';
	echo  genXulComboColores(false, "menuitem");			
	echo '</menupopup>';	
	echo '</menulist>';

?></hbox>
</row>
<row><caption class="media" label="CB"/>
	<textbox style="min-width: 7em" class="media" id="CB" value="<?php echo $primerCB ?>"/>
</row>
</rows>
	</grid>
	
	<hbox>
		<button image="img/bar16.png" class="media" flex="1" label="Nueva Talla/Color" oncommand="xNuevaTallaColor()"/> 		
	</hbox>		
    <listbox id="listadoTacolor" rows="4" flex="1" contextmenu="accionesTicket">
	<listcols flex="1">
		<listcol  />
		<splitter class="tree-splitter" />
		<listcol flex="5" />
		<splitter class="tree-splitter" />
		<listcol flex="2" />
		<splitter class="tree-splitter" />
		<listcol flex="2" />
		<splitter class="tree-splitter" />
		<listcol/>
     </listcols>
      <listhead>
	<listheader label="Codigo" style="width: 8em"/>
	<listheader label="Talla" style="width: 50px" />
	<listheader label="Color" style="width: 50px"/>	
	<listheader label="Unidades" style="width: 9em"/>
      </listhead>
	  
    </listbox>
	<hbox>
	<button image="img/cart.gif" class="media" flex="1" oncommand="AltaProducto()" label="Comprar..."/>
	<button image="img/nocart.gif" class="media" flex="1" label="Cancelar" oncommand="CancelarTallasYColores()"/>  
	</hbox>
    </vbox>	
<!-- listado compra tickets -->	

<script>//<![CDATA[

var enviar = new Array();

enviar["IdSubFamilia"] = <?php echo CleanID($IdSubFamiliaDefecto) ?>;
enviar["IdFamilia"] = <?php echo CleanID($IdFamiliaDefecto) ?>;
enviar["IdTallaje"] = '<?php echo TALLAJE_DEFECTO ?>';//Precargado con un tallaje por defecto para autogeneracion

var MITALLAJEDEFECTO = '<?php echo TALLAJE_DEFECTO ?>';//Si no se especifica tallaje, prepopula para un tallaje concreto x defecto

function CancelarTallasYColores(){
	if(confirm(po_segurocancelar))
		VaciarTacolores();
}


//]]></script>
<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/cadenas.js.php?a=4"/>
<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/xulcompra.js?a=4"/>
</hbox>
<?php


EndXul();


?>
