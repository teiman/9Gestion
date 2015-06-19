<groupbox flex="1">
<vbox flex="1" align="center" pack="center" style="background-color: #eee">
<caption class="media"  label="Peticion Ticket" id="etiquetaTicket" style="background-color: #eee"/>
<groupbox style="background-color: -moz-dialog">
<grid>
	<columns><column/><column/></columns>
	<rows>
	    <row>
	     <caption class="grande" label=" "/>
	    </row>
		<row>		
		<caption class="grande" label="<?php echo _("TOTAL") ?>"/>
		<caption id="peticionTotal" class="grande"  label="0,00 EUR"/>
		</row>
		<row id="Fila-peticionEntrega">
		<caption class="grande" label="<?php echo _("ENTREGA") ?>"/>
		<textbox id="peticionEntrega" class="grande"  value="0,00" onkeyup="ActualizaPeticion()" onkeypress="ActualizaPeticion()"/>
		</row>
		<row>
		<caption class="grande" label="<?php echo _("CAMBIO") ?>"/>
		<caption id="peticionPendiente" class="grande" label="0,00 EUR"/>
		<textbox id="peticionCambioEntregado" collapsed="true" value="0"/>
		</row>	
		<spacer style="height: 8px"/>
		<row id="Pagos_1" collapsed="true">		
		<caption class="media" label="<?php echo _("EFECTIVO") ?>"/>
		<textbox id="peticionEfectivo" class="media"  value="0,00" onkeyup="ActualizaPeticion()" onkeypress="ActualizaPeticion()"/>
		</row>			
		<row id="Pagos_2" collapsed="true">
		<caption class="media" label="<?php echo _("BONO") ?>"/>
		<textbox id="peticionBono" class="media"  value="0,00" onkeyup="ActualizaPeticion()" onkeypress="ActualizaPeticion()"/>
		</row>			
		<row id="Pagos_3" collapsed="true">
		<caption class="media" label="<?php echo _("TARJETA") ?>"/>
		<textbox id="peticionTarjeta" class="media"  value="0,00" onkeyup="ActualizaPeticion()" onkeypress="ActualizaPeticion()"/>
		</row>			
		<row id="Pagos_4" collapsed="true">
		<caption class="media" label="<?php echo _("TRANSFERENCIA") ?>"/>
		<textbox id="peticionTransferencia" class="media"  value="0,00" onkeyup="ActualizaPeticion()" onkeypress="ActualizaPeticion()"/>
		</row>			
		
		<row id="Pago_Modo">
		<caption class="grande" label="<?php echo _("PAGO") ?>"/>
		<menulist class="grande" id="modoDePagoTicket">
		<menupopup>
		<?php
	foreach( $modosDePago as $value=>$label ){
		echo "<menuitem value='$value' label='$label'/>\n";
	}
		?>
		</menupopup>
		</menulist>
		</row>
		<spacer style="height: 8px"/>

		<row id="Pagos_0">		
		<box/>
		<button image="img/presupuestos.png" flex="1" class="media" label="Multipagos" oncommand="ModoMultipago()"/>
		</row>			
		
		<?php 
		
		//NOTA: condicionado a ser un administrador de facturas se muestran los controles extra 		
		if ($_SESSION["EsAdministradorFacturas"]){										
		
		?>		
		<row id="Admintic_0">		
		<box/>
		<button image="img/presupuestos.png" flex="1" class="media" label="Personalizar" oncommand="ModoPersonalizado()"/>
		</row>	
		<row id="Admintic_1"  collapsed="true">
		<caption class="media" label="<?php echo _("Serie ticket") ?>"/>
		<textbox id="ajusteSerieTicket" class="media"  
			value="<?php
				echo "B" . CleanID(getSesionDato("IdTienda"));
				?>"/>
		</row>		
		<row  id="Admintic_2"  collapsed="true">
		<caption class="media" label="<?php echo _("Nº Ticket") ?>"/>
		<textbox id="ajusteNumeroTicket" class="media"  
			value="<?php
				echo CleanID($numSerieTicketLocalActual);
				?>"/>				
		</row>		
		<row  id="Admintic_3"  collapsed="true">
		<caption class="media" label="<?php echo _("Fecha Ticket") ?>"/>
		<textbox id="ajusteFechaTicket" class="media"  
			value="<?php
				$cad = "%A %d del %B, %Y";
				setlocale(LC_ALL,"es_ES");			
				echo strftime($cad);
				?>"/>				
		</row>	
		<?php
		
		}
		
		?>		
		
		<row>
		<box/>
		<hbox>		
		<button flex="1" id="BotonAceptarImpresion" image="img/colorprint30.png" class="media" label="¿ACEPTAR?" oncommand="ImprimirTicket()"/>
		<button flex="1" image="img/button_cancel.png" class="media" label="Cancelar" oncommand="CerrarPeticion()"/>
		</hbox>
		</row>	
		<row>
		<toolbarbutton collapsed="true" flex="1" image="img/colorprint30.png" class="media" label="Copia" oncommand="ImprimirTicket('copia')"/>		
		</row>	
	</rows>
</grid>
</groupbox>
</vbox>
</groupbox>