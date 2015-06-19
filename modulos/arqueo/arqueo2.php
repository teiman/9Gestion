<?php

include("../../tool.php");

header("Content-Type: application/vnd.mozilla.xul+xml");
header("Content-languaje: es");

$CabeceraXUL = '<#xml version="1.0" encoding="UTF-8"#>';
$CabeceraXUL .=	'<#xml-stylesheet href="chrome://global/skin/" type="text/css"#>';
$CabeceraXUL = str_replace("#","?",$CabeceraXUL);

echo $CabeceraXUL;

?>
<window id="ventana-principal" title="ventana principal"
        xmlns:html="http://www.w3.org/1999/xhtml"        
        xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">       	
<groupbox  flex="1">

<hbox align="start" flex="1">
<description>FECHA DE ARQUEO:</description>
    <menulist label="Elige arqueo..." id="SeleccionArqueo">
     <menupopup id="itemsArqueo">
      <menuitem label="Elige arqueo..."/>      
     </menupopup>
    </menulist>

<button label="CONSULTAR CAJA" oncommand="onConsultarCaja()" collapsed="true"/> 
<spacer flex="1"/>
<vbox style="background-color: orange;text-align:center;font-size: 120%;font-weight: bold">
<textbox id='estadoCajaTexto' style="background-color: orange;border: 0px;"  class="plain big" value="------"/>
<textbox id='estadoCajaFecha' style="background-color: orange;border: 0px;"  class="plain big" value="--/--/---- --:--"/>
<textbox id='estadoCajaFecha' style="background-color: orange;border: 0px;"  class="plain big"

collapsed="true"

 value="IdTienda:
 <?php 
 
 echo CleanID(getSesionDato("IdTienda"));
 
 ?>"/>
    
</vbox>
</hbox>
<textbox id="log" flex="1" multiline="true" wrap="off" collapsed="true"/>  
<description  style="text-decoration: underline">MOVIMIENTOS DE CAJA:</description>
<listbox collapsed="false" id="listaMovimientos" flex="1">
     <listcols>
	<listcol flex="1" />
	<splitter class="tree-splitter" />
	<listcol flex="1" />
	<splitter class="tree-splitter" />
	<listcol flex="1" />
	<splitter class="tree-splitter" />
	<listcol flex="1" />
      </listcols>
      <listhead>
	<listheader label="OPERACION" style="text-decoration: underline"/>
	<listheader label="CONCEPTO"  style="text-decoration: underline"/>
	<listheader label="IMPORTE"  style="text-decoration: underline"/>
	<listheader label="HORA"  style="text-decoration: underline"/>
      </listhead>
</listbox>

<groupbox flex="1">
<description  style="text-decoration: underline">ARQUEO DE CAJA</description>

<hbox>
<grid style="background-color: white;border: 2px #ccc solid;">
<rows>
<row>
<description  style="text-decoration: underline">SALDO INICIAL:</description>
<textbox  class="plain"  id='saldoInicialText' value="0 €"/>
</row>

<row>
<description  style="text-decoration: underline">+INGRESOS:</description>
<textbox class="plain"   id='ingresosText' value="0 €"/>
</row>

<row>
<description  style="text-decoration: underline">-GASTOS:</description>
<textbox  class="plain"  id='gastosText' value="0 €"/>
</row>

<row>
<description  style="text-decoration: underline">+APORTACIONES:</description>
<textbox  class="plain"  id='aportacionesText' value="0 €"/>
</row>

<row>
<description  style="text-decoration: underline">-SUSTRACCIONES:</description>
<textbox  class="plain"  id='sustraccionesText' value="0 €"/>
</row>

<row>
<description  style="text-decoration: underline">=TEORICO CIERRE</description>
<textbox  class="plain"  id='TeoricoCierre' value="0 €"/>
</row>

</rows>
</grid>

<spacer style="width: 16px"/>

<grid style="background-color: white;border: 2px #ccc solid;">
<rows>
<row style="background-color: #ccc">
 <description  class="plain" style="text-decoration: underline">CIERRE CAJA</description>
</row>
<row>
 <textbox  class="plain"  id='cierreCajaText' style="font-size: 120%;font-weight: bold;text-align:center;text-decoration: underline" value="0 EUR"/>
</row>

<row style="background-color: #ccc">
 <description style="text-decoration: underline">DESCUADRE CAJA </description>
</row>
<row>
 <textbox  class="plain" id='descuadreCajaText' style="font-size: 120%;font-weight: bold;text-align:center;text-decoration: underline" value="0 EUR"/>
</row>
</rows>
</grid>
<spacer style="width: 16px"/>
<vbox>
<button label="CERRAR CAJA" flex="1" oncommand="Comando_CerrarCaja()"/>
<button label="ARQUEO CAJA" flex="1" oncommand="Comando_ArqueoCaja()"/>
</vbox>
</hbox>
<spacer style="height: 16px"/>
  <tabbox  flex="1">
    <tabs >
       <tab label="APORTACION"/>
       <tab label="SUSTRACCION"/>
       <tab label="INGRESO"/>
       <tab label="GASTO"/>
    </tabs>
    <tabpanels flex="1" >
     <groupbox>
	 <hbox>
 		<description style="font-weight: bold;">CONCEPTO:</description>
		<textbox id='conceptoText' value=""/>
 		<description style="font-weight: bold;">IMPORTE:</description>
		<textbox id='importeText' value=""/>
		<button label="GUARDAR" oncommand="Comando_HacerUnAporte()"/>
	 </hbox>
     </groupbox>
     <groupbox>
	 <hbox>
 		<description style="font-weight: bold;">CONCEPTO:</description>
		<textbox  id='conceptoTextSubs' value=""/>
 		<description style="font-weight: bold;">IMPORTE:</description>
		<textbox  id='importeTextSubs' value=""/>
		<button label="GUARDAR" oncommand="Comando_HacerUnaSubstraccion()"/>
	 </hbox>
     </groupbox>
     <groupbox>
	 <hbox>
 		<description style="font-weight: bold;">CONCEPTO:</description>
		<textbox id='conceptoTextIngreso' value=""/>
 		<description style="font-weight: bold;">IMPORTE:</description>
		<textbox id='importeTextIngreso' value=""/>
		<button label="GUARDAR" oncommand="Comando_HacerUnIngreso()"/>
	 </hbox>
     </groupbox>
     <groupbox>
	 <hbox>
 		<description style="font-weight: bold;">CONCEPTO:</description>
		<textbox id='conceptoTextGasto' value=""/>
 		<description style="font-weight: bold;">IMPORTE:</description>
		<textbox id='importeTextGasto' value=""/>
		<button label="GUARDAR" oncommand="Comando_HacerUnGasto()"/>
	 </hbox>
     </groupbox>
    </tabpanels>          
  </tabbox>

</groupbox>

</groupbox>

<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/cadenas.js.php?ver=1/r<?php echo rand(0,99999999); ?>"/>
<script type="application/x-javascript" src="<?php echo $_BasePath; ?>modulos/arqueo/js/arquero.js?ver=1/r<?php echo rand(0,99999999); ?>"/>

<script>//<![CDATA[

var Local = new Object();
Local.IdLocalActivo = '<?php echo CleanID(getSesionDato("IdTienda")) ?>';

setTimeout("onLoadFormulario()",300);


//]]></script>




</window>