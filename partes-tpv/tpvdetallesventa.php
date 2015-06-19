<vbox>
<groupbox>
<caption label="Busqueda" />
<hbox align="start">
	<vbox>
		<description>Desde</description>
		<hbox>
			<?php if($modulos["datepicker"]): ?>
			<toolbarbutton image="img/calendar-up.gif" label="" onmousedown="EnviaCalendario('oe-date-picker-popup','FechaBuscaVentas')" popup="oe-date-picker-popup" position="after_start" />
			<?php endif; ?>	
			<textbox id="FechaBuscaVentas" value="DD-MM-AAAA" style="width: 11em"/>
		</hbox>
	</vbox>
	<vbox>
		<description>Hasta</description>
		<hbox>
			<?php if($modulos["datepicker"]): ?>
			<toolbarbutton image="img/calendar-up.gif" label="" onmousedown="EnviaCalendario('oe-date-picker-popup','FechaBuscaVentasHasta')" popup="oe-date-picker-popup" position="after_start" />
			<?php endif; ?>
			<textbox id="FechaBuscaVentasHasta" value="DD-MM-AAAA" style="width: 11em"/>
		</hbox>
	</vbox>
	<vbox>
		<description>Nombre</description>
		<textbox id="NombreClienteBusqueda" style="width: 11em"/>
	</vbox>
	<vbox>
		<checkbox id="modoConsultaVentasSerie" label="Cedidos"/>
		<checkbox id="modoConsultaVentas" label="Pendientes"/>
	</vbox>
	<spacer flex="1"/>
	<vbox>
		<description style="-moz-opacity: 0">.  </description>	
		<button image="img/find16.png" oncommand="BuscarVentas()"/>
	</vbox>
</hbox>
</groupbox>
<vbox flex="1">
<description style="font-size: 100%;font-weight:bold">Ventas</description>
<listbox id="busquedaVentas" contextmenu="AccionesBusquedaVentas">
     <listcols flex="1">
		<listcol  flex="1"/>		
		<splitter class="tree-splitter" />
		<listcol  flex="1"/>
		<splitter class="tree-splitter" />
		<listcol  flex="1"/>
		<splitter class="tree-splitter" />
		<listcol  flex="1"/>
		<splitter class="tree-splitter" />
		<listcol  flex="1"/>
		<splitter class="tree-splitter" />
		<listcol  flex="1"/>		
		<splitter class="tree-splitter" />
		<listcol  flex="1"/>				
		<splitter class="tree-splitter" />
		<listcol  flex="1"/>				
		<splitter class="tree-splitter" />
		<listcol/>	
     </listcols>
     <listhead>
		<listheader label="Vendedor"/>
		<listheader label="Serie"/>
		<listheader label="Factura"/>
		<listheader label="Fecha factura"/>
		<listheader label="Total Importe"/>
		<listheader label="Importe Pendiente"/>
		<listheader label="Status"/>
		<listheader label="Cliente"/>		
     </listhead>

</listbox>
<spacer style="height:8px"/>
<description style="font-size: 100%;font-weight:bold">Detalle venta</description>
<listbox id="busquedaDetallesVenta" flex="1">
	<listcols flex="1">
		<listcol flex="1"/>
		<splitter class="tree-splitter" />
		<listcol flex="1"/>
		<splitter class="tree-splitter" />
		<listcol flex="1"/>
		<splitter class="tree-splitter" />
		<listcol flex="1"/>
		<splitter class="tree-splitter" />
		<listcol flex="1"/>
		<splitter class="tree-splitter" />		
		<listcol flex="1"/>
		<splitter class="tree-splitter" />		
		<listcol flex="1"/>
		<splitter class="tree-splitter" />				
		<listcol/>
		<splitter class="tree-splitter" />
		<listcol/>
     </listcols>
      <listhead>
	<listheader label="Referencia" />
	<listheader label="Nombre" style="width: 100px"/>
	<listheader label="Talla"  />
	<listheader label="Color"  />	
	<listheader label="Unidades" style="width: 50px"/>
	<listheader label="Descuento" style="width: 50px"/>	
	<listheader label="PV" />
	<listheader label="" />
      </listhead>
</listbox>
</vbox>
<box flex="1"/>
<button class="media"  image="img/tpv3.gif" label="Volver TPV" oncommand="VerTPV()" collapsed="false"/>
</vbox>
