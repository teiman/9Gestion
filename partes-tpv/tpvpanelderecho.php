<groupbox id="panelDerecho">
<groupbox align="center" pack="center" flex="1" style="height: 100px;max-height: 100px" collapsed="true">
	<spacer flex="1"/>
	<image src="imagenproducto.php?cp=" id="muestraProducto" style="width:90px;height:90px"/>
	<spacer flex="1"/>
</groupbox>
<groupbox align="center" pack="center" flex="1">
	<vbox>
	<hbox><image src="img/bar16.png" height="16" width="16"/><label id='muestraProductoCB'/></hbox>
	<box><label id='nombreProducto'/></box>
	</vbox>
</groupbox>
<button crop="end" image="img/colorprint30.png" label="<?php echo _("Impr. ticket") ?>" class="compacta" oncommand="AbrirPeticion()"/>
<button crop="end" image="img/nocart.gif" label="<?php echo _("Borrar venta") ?>"  class="compacta" oncommand="CancelarVenta()"/>	
<deck id="modoMensajes" flex="1" style="width: 200 !important">
<?php include("tpvmensajeria.php"); ?>
</deck>

 <hbox>
	<button flex="1" id="VerVentasButton" crop="end" image="img/cart.gif" label="<?php echo _("Ventas") ?>"  class="compacta" oncommand="VerVentas()"/>		
	<?php if($modulos["arreglodecaja"]): ?>
	<button flex="1" id="VerCajaButton" crop="end" image="img/cart.gif" label="<?php echo _("Caja") ?>"  class="compacta" oncommand="VerCaja()"/>
	<?php endif; ?>
 </hbox>
 	
    <button id="VerArreglosButton" crop="end" image="img/attach.png" label="<?php echo _("Arreglos") ?>"  class="compacta" oncommand="VerArreglos()"/>
    <?php if($modulos["generadorlistados"]): ?>	    
    <button id="VerArreglosButton" crop="end" image="img/listado.png" label="<?php echo _("Listados") ?>"  class="compacta" oncommand="VerListados()"/>
    <?php endif; ?>
    <button id="VolverTPV"  image="img/tpv3.gif"  crop="end" label="<?php echo _("Volver TPV") ?>"  class="compacta" oncommand="VerTPV()"/>

</groupbox>