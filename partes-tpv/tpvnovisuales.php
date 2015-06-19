

<?php if ($modulos["datepicker"]): ?>
	<script type="application/x-javascript" src="<?php echo $_BasePath; ?>modulos/datepicker/calendario.js"/>		
	<popup  id="oe-date-picker-popup" position="after_start" oncommand="RecibeCalendario( this )" value=""/>	
<?php endif; ?>

<command id="quitaArticulo" oncommand="QuitarArticulo()"   disabled='false'  label="<?php echo _("Quitar articulo") ?>"/>  

<popupset>
  <popup id="accionesLista" class="media">
		<menu label="Comprar" >
                 <menupopup>
				 <menuitem label="<?php echo _("¿Cuántas?"); ?>" oncommand="setTimeout('agnadirPorMenu(\'preguntar\')',50)"/>
                   <menuitem label="1" oncommand="agnadirPorMenu(1)"/>
                   <menuitem label="2" oncommand="agnadirPorMenu(2)"/>
                   <menuitem label="3" oncommand="agnadirPorMenu(3)"/>
				   <menuitem label="4" oncommand="agnadirPorMenu(4)"/>
				   <menuitem label="5" oncommand="agnadirPorMenu(5)"/>
				   <menuitem label="6" oncommand="agnadirPorMenu(6)"/>
				   <menuitem label="7" oncommand="agnadirPorMenu(7)"/>
				   <menuitem label="8" oncommand="agnadirPorMenu(8)"/>
				   <menuitem label="9" oncommand="agnadirPorMenu(9)"/>
				   <menuitem label="10" oncommand="agnadirPorMenu(10)"/>				   
                 </menupopup>
           </menu>
	   <menuseparator />
	   <menuitem class="menuitem-iconic" image="img/addcart16.gif" label="Comprar"  oncommand="agnadirPorMenu()"/>
	   <menuitem label="<?php echo _("Devolución articulo") ?>" oncommand="agnadirPorMenu(-1)" collapsed="true"/>	   
	   <menuitem label="<?php echo _("Ficha") ?>"  oncommand="ToggleFichaForm()"/>	   	   	   
	   <menuitem label="<?php echo _("Cancelar venta") ?>"  />
		<menuseparator />
	   <menuitem class="menuitem-iconic" image="img/remove16.gif" label="<?php echo _("Borrar") ?>"  oncommand="VaciarListadoProductos()"/>	   	   
  </popup>
  
  <popup id="AccionesBusquedaVentas" class="media">
     <menuitem label="<?php echo _("Revisar") ?>" oncommand="RevisarVentaSeleccionada()"/>
     <menuitem label="<?php echo _("Abonar") ?>" oncommand="VentanaAbonos()"/>
     <menuitem label="<?php echo _("Reimprimir") ?>" oncommand="AbrirVentaSeleccionada()"/>
  </popup>  

  <popup id="popupListadoArreglos" class="media">
     <menuitem label="<?php echo _("Pdte Envio")?>" oncommand="ListadoArreglosSeleccionadoStatus('Pdte Envio')"/>
     <menuitem label="<?php echo _("Enviado") ?>" oncommand="ListadoArreglosSeleccionadoStatus('Enviado')"/>
     <menuitem label="<?php echo _("Recibido") ?>" oncommand="ListadoArreglosSeleccionadoStatus('Recibido')"/>
     <menuitem label="<?php echo _("Entregado") ?> " oncommand="ListadoArreglosSeleccionadoStatus('Entregado')"/>
  </popup>  
  
  <popup id="accionesTicket" class="media">
		<menu id='ticketUnidades' label="Unidades">
                <menupopup>
				<menuitem label="<?php echo _("¿Cuántas?") ?>" oncommand='ModificaTicketUnidades(-1)'/>
                <menuitem label="1" oncommand='ModificaTicketUnidades(1)'/>
                <menuitem label="2" oncommand='ModificaTicketUnidades(2)'/>
                <menuitem label="3" oncommand='ModificaTicketUnidades(3)'/>
				<menuitem label="4" oncommand='ModificaTicketUnidades(4)'/>
				<menuitem label="5" oncommand='ModificaTicketUnidades(5)'/>
				<menuitem label="6" oncommand='ModificaTicketUnidades(6)'/>
				<menuitem label="7" oncommand='ModificaTicketUnidades(7)'/>
				<menuitem label="8" oncommand='ModificaTicketUnidades(8)'/>
				<menuitem label="9" oncommand='ModificaTicketUnidades(9)'/>
				<menuitem label="10" oncommand='ModificaTicketUnidades(10)'/>
               	</menupopup>
		</menu>
	   <menuitem label="<?php echo _("Modificar precio") ?>" oncommand="ModificarPrecio()"/>
	   <menuitem label="<?php echo _("Modificar descuento") ?>" oncommand="ModificarDescuento()"/>
	   <menuitem label="<?php echo _("Arreglos") ?>" oncommand="ArregloParaFila()"/>	   
	   <menuseparator />
	   <menuitem class="menuitem-iconic" image="img/remove16.gif" command="quitaArticulo" />
        <menuseparator />
	   <menuitem class="menuitem-iconic" image="img/remove16.gif"  label="<?php echo _("Cancelar venta") ?>"  />
  </popup>
  
  
</popupset>