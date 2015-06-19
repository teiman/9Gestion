

<listbox flex="1" id="buzon-mensajes">
	<listitem  id="guardianMensajes"  value="3" onclick="ToggleMensajes()">
	<button image="img/nmensaje16.gif" label=" Escribir mensaje" oncommand="ToggleMensajes()"  /> 
	</listitem>	
</listbox>
	
	<groupbox><caption label="Contenido:"/>
	<toolbarbutton label="Ok" oncommand="mensajesModoRecepcion()"/>	
	<label crop="end" value="texto" id="tituloVisual" style="max-width: 200px;font-weight: bold;color: blue"/>
	<textbox multiline="true" readonly="true" value="texto aqui" flex="1" id="textoVisual" 
		style="color: blue; background-color: ThreeDFace !important"/>
</groupbox>	

<groupbox>
	<caption crop="end" label="<?php echo _("Mensaje") ?>" id="tituloVisual"/>
	<vbox flex="1">
	<menulist  id="localDestino">						
	<menupopup>
	<menuitem label="<?php echo _("Elije local") ?>" style="font-weight: bold"/>
	<?php echo genXulComboAlmacenes(false,"menuitem")  ?>
	</menupopup>
	</menulist>		
	<textbox id="tituloNuevoMensaje"/>
	<textbox flex="1" id="cuerpoNuevoMensaje" multiline="true"/>
	<hbox>
	<button  label="<?php echo _("Enviar") ?>"  oncommand="EnviarMensajePrivado()"/>	<button label="Cancelar"  oncommand="ToggleMensajes()"/>
	</hbox>
	</vbox>
</groupbox>	

