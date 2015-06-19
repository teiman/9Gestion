<vbox>
<groupbox>
<grid>
<rows>
<row><description>Modisto</description><description>Status</description><description>Ticket</description><description></description></row>
<row>
	
		<menulist id="ModistoListaArreglos" >						
		<menupopup>
		<menuitem label=" "/>			
<?php 
			echo $genModistos;

?>
		</menupopup>			
	</menulist>	
	
	<menulist id="StatusListaArreglos">
	<menupopup>
	<menuitem label=" "/>	
	<?php
	foreach( $statusArreglos as $value=>$label ){
		echo "<menuitem value='$value' label='$label'/>\n";
	}
	?>
	</menupopup>
	</menulist>
	
	<textbox id="TicketListaArreglos"/>
	<button image="img/find16.png" label=" " oncommand="ListadoModistos()"/>
</row>
</rows>
</grid>
</groupbox>


<listbox id="busquedaListaArreglos" flex="1" contextmenu="popupListadoArreglos">
	<listcols flex="1">
		<listcol style="maxwidth: 11em"/>
		<splitter class="tree-splitter" />
		<listcol style="maxwidth: 35em"/>
		<splitter class="tree-splitter" />
		<listcol flex="1"/>
		<splitter class="tree-splitter" />
		<listcol style="maxwidth: 5em"/>
		<splitter class="tree-splitter" />
		<listcol style="maxwidth: 11em"/>
		<splitter class="tree-splitter" />		
		<listcol style="maxwidth: 11em"/>
		<splitter class="tree-splitter" />		
		<listcol style="maxwidth: 11em"/>
		<splitter class="tree-splitter" />				
		<listcol/>
		<splitter class="tree-splitter" />
     </listcols>
      <listhead>
	<listheader label="Modisto"/>
	<listheader label="Producto"/>
	<listheader label="Arreglos"/>
	<listheader label="Nticket"/>	
	<listheader label="Status"/>
	<listheader label="Enviado"/>	
	<listheader label="Recibido" />
      </listhead>
</listbox>
<button class="media" image="img/tpv3.gif" label="Volver TPV" oncommand="VerTPV()"/>
</vbox>
