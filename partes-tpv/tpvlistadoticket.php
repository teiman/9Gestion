  <spacer style="height: 8px"/>
  <splitter collapse="none" resizebefore="farthest" resizeafter="farthest"/>
  <vbox flex="3" class="listado">
	
	<hbox align="center">
    	<caption label="Ticket actual:" style="text-decoration: underline"/>
    	<spacer flex="1"/>
  		<radiogroup orient="horizontal" id="rgModosTicket" oncommand="NuevoModo()">
  			<radio id="rVenta" label="Venta" selected="true" value="venta"/>
  			<radio id="rCesion" label="Cesion" value="cesion"/>
  			<radio id="rDevolucion" label="Devolucion" value="devolucion"/>  			
  			<radio id="rInterno" label="Arreglo interno" value="interno"/>
  		</radiogroup>    	
    </hbox>
    <listbox id="listadoTicket" rows="4" flex="1" contextmenu="accionesTicket">
	<listcols flex="1">
		<listcol  />
		<splitter class="tree-splitter" />
		<listcol flex="5" />
		<splitter class="tree-splitter" />
		<listcol flex="2" />
		<splitter class="tree-splitter" />
		<listcol flex="2" />
		<splitter class="tree-splitter" />
		<listcol flex="2" />
		<splitter class="tree-splitter" />		
		<listcol flex="2" />
		<splitter class="tree-splitter" />		
		<listcol flex="1" collapsed="<?php echo $esOcultoImpuesto ?>"/>
		<splitter class="tree-splitter" />				
		<listcol/>
		<listcol/>
		<splitter class="tree-splitter" />
     </listcols>
      <listhead>
	<listheader label="Referencia" />
	<listheader label="Nombre" style="width: 100px"/>
	<listheader label="Talla"  />
	<listheader label="Color"  />	
	<listheader label="Unidades" style="width: 50px"/>
	<listheader label="Descuento" style="width: 50px"/>	
	<listheader label="Impuesto" style="width: 50px" collapsed="<?php echo $esOcultoImpuesto ?>"/>	
	<listheader label="<?php echo $pvpUnidad ?>" />
	<listheader label="" />
      </listhead>
	  
    </listbox>
    </vbox>	