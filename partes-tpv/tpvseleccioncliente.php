<vbox flex="1" zalign="center" pack="center">
<tabbox flex="1">
<tabs>
<tab id="tab-selcliente" accesskey="S" label="Selección de cliente"/>
<tab id="tab-newcliente" label="Crear nuevo cliente"/>
<tab id="tab-vistacliente" label="" collapsed="true"/>
</tabs>
<tabpanels flex="1" >
<tabpanel flex="1">
<groupbox flex="1">
<caption label="Listado de cliente activos"/>
<vbox flex="1" zalign="top" pack="center" style="overflow: auto">
	<hbox>   
		<button  image="img/cliente16.png" id="clieLista" label="Usar cliente contado"   oncommand ="pickClienteContado()"/>        
		<button collapsed="true" image="img/cliente16.png" id="clieLista" label="Usar seleccionado"   oncommand ="pickClienteContado()"/>
		<button collapsed="true" id="clieLista" image="img/button_cancel.png" label="Cancelar"  oncommand ="ToggleListadoUsuariosForm()"/>    
	</hbox>

<listbox id="clientPickArea" flex="1" style="height: 100%;max-width: 320px" value="0">
  <listcols>
    <listcol/>
    <listcol/>
  </listcols>
</listbox>

</vbox>
</groupbox>
</tabpanel>

<tabpanel>
<groupbox flex="1">
<caption label="Datos administrativos del cliente"/>
<box flex="1">
<grid flex="1">
<rows flex="1"> 
 <columns><column/></columns>
<row><caption label="Nombre"/><textbox id="NombreComercial"/></row>
<row><caption label="Direccion"/><textbox id="Direccion"/></row>
<row><caption label="Localidad"/><textbox id="Localidad"/></row>
<row><caption label="Codigo postal"/><textbox id="CP"/></row>
<row><caption label="Telf"/><textbox id="Telefono1"/></row>
<row><caption label="CC"/><textbox id="CuentaBancaria"/></row>
<row><caption label="Numero fiscal"/><textbox id="NumeroFiscal"/></row>
<row><caption label="Comentarios"/><textbox multiline="true" id="Comentarios"/></row>
<row><caption label="Pais"/><textbox value="España" id="Pais"/></row>
<row><caption label="Pagina web"/><textbox id="PaginaWeb"/></row>
<row><caption label="Fecha naci."/><textbox value="DD/MM/AA"  id="FechaNacim"/></row>
<row><caption label="Modo pago hab."/>
<menulist >
	<menupopup>
<?php
	foreach( $modosDePago as $value=>$label ){
		echo "<menuitem value='$value' label='$label'/>\n";
	}
?>
	</menupopup>
</menulist>
</row>
<row>
	<box/>
	<button image="img/addcliente.png" oncommand="AltaCliente()" label="Alta"/>				
</row>
</rows>
</grid>
</box>
</groupbox>
</tabpanel>
<tabpanel>
<groupbox>
<caption label="Datos administrativos del cliente"/>
<box>
<grid>
<rows> 
 <columns><column/></columns>
<row><caption  label="Nombre"/><textbox id="visNombreComercial"/></row>
<row><caption  label="Direccion"/><textbox  id="visDireccion"/></row>
<row><caption  label="Localidad"/><textbox  id="visLocalidad"/></row>
<row><caption  label="Codigo postal"/><textbox  id="visCP"/></row>
<row><caption  label="Telf"/><textbox  id="visTelefono1"/></row>
<row><caption  label="CC"/><textbox  id="visCuentaBancaria"/></row>
<row><caption  label="Numero fiscal"/><textbox  id="visNumeroFiscal"/></row>
<row><caption  label="Comentarios"/><textbox  multiline="true" id="visComentarios"/></row>
<row><caption  label="Pais"/><textbox  value="España" id="visPais"/></row>
<row><caption  label="Pagina web"/><textbox   id="visPaginaWeb"/></row>
<row><caption  label="Fecha naci."/><textbox  value="dd/mm/aa"  id="visFechaNacim"/></row>
<row><caption   label="Modo pago hab."/>
<menulist  id="visModoPago"  >
	<menupopup>
<?php
	foreach( $modosDePago as $value=>$label ){
		echo "<menuitem value='$value' label='$label'/>\n";
	}
?>
	</menupopup>
</menulist>
</row>
<row>	
	<box/>
	<box>
			<button image="img/modcliente.png" oncommand="ModificarCliente()" label="Modificar"/>
			<button image="img/del.gif" label="Eliminar" oncommand="EliminarClienteActual()"/>
	</box>
</row>
</rows>
</grid>
</box>
</groupbox>
</tabpanel>
</tabpanels>
</tabbox>
<button class="media"  image="img/tpv3.gif" label="Volver TPV" oncommand="VerTPV()" collapsed="false"/>

</vbox>

