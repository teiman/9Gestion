<?php

include("tool.php");


SimpleAutentificacionAutomatica("visual-xul","xulentrar.php");

$NombreUsuarioDefecto = $_SESSION["NombreUsuario"];

//TODO: hacer esto XUL seguro
$NombreUsuarioDefecto_s = str_replace(">"," ",$NombreUsuarioDefecto);
$NombreUsuarioDefecto_s = str_replace("<"," ",$NombreUsuarioDefecto_s);
$NombreUsuarioDefecto_s = str_replace("&"," ",$NombreUsuarioDefecto_s);

StartXul(_("Ventana principal"));


if (isUsuarioAdministradorWeb()){
?>
<command id="verTemplates" 
  oncommand="solapa('modtemplates.php?modo=lista')"
  label="Templates"/>  
<command id="altaTemplate" 
  oncommand="solapa('modtemplates.php?modo=alta')"
  label="Alta template"/>  
<command id="editarJS" 
  oncommand="popup('xuleditor.php?id=24')"
  label="Editar JS"/>  
<command id="editarCSS" 
  oncommand="popup('xuleditor.php?id=26')"
  label="Editar CSS"/>  
<command id="editarxulCSS" 
  oncommand="popup('xuleditor.php?id=52')"
  label="Editar xul CSS"/>      
<command id="verLog" 
  oncommand="solapa('modulos/logactivo/logactivo.php?sesion=no&amp;num='+prompt('Lineas de log:',10))"
  label="Ver Log"/> 
<command id="verSesion" 
  oncommand="solapa('logactivo.php?num=1')"
  label="Ver sesion"/>   
<?php
}
?>

<command id="verCarrito" oncommand="popup('vercarrito.php?modo=check','dependent=yes,width=600,height=320,screenX=200,screenY=300,titlebar=yes')"
  <?php gulAdmite("Compras") ?> label="<?php echo _("Ver carrito") ?>"/>  

<command id="altaProveedor" oncommand="proveedor_Alta()" <?php gulAdmite("Proveedores") ?> 
  label="<?php echo _("Alta proveedor") ?>"/>  

<command id="altaClienteMain" oncommand="solapa('modclientes.php?modo=alta','<?php echo _("Clientes: Alta") ?>')" 
  label="<?php echo _("Alta cliente") ?>"  <?php gulAdmite("Clientes") ?>/>
  
<command id="altaClienteParticularMain"  oncommand="solapa('modclientes.php?modo=altaparticular','<?php echo _("Clientes: Alta") ?>')" 
  label="<?php echo _("Alta cliente particular") ?>"  <?php gulAdmite("Clientes") ?>/>  

<command id="altaUsuario"   oncommand="solapa('modusers.php?modo=alta','<?php echo _("Usuarios: Alta") ?>')"   
  <?php gulAdmite("Administracion") ?>  label="<?php echo _("Alta usuario") ?>"/>

  
<command id="verPerfiles"   oncommand="solapa('modperfiles.php?modo=lista','<?php echo _("Perfiles") ?>','varios')"
    <?php gulAdmite("Administracion") ?>  label="<?php echo _("Ver perfiles") ?>"/>
  
<command id="altaFamilia"  oncommand="solapa('modfamilias.php?modo=alta','<?php echo _("Familia: Alta") ?>')" 
  <?php gulAdmite("Administracion") ?>  label="<?php echo _("Alta familia") ?>"/>
  
<command id="cmdLogout"  oncommand="document.location.href='logout2.php'"  label="<?php echo _("Salir") ?>"/>

<command id="procesarCompra" oncommand="solapa('modcompras.php?modo=continuarCompra','<?php echo _("Compras") ?>')"
     <?php gulAdmite("Compras") ?> label="<?php echo _("Continuar compra") ?>"/>
  
<command id="seleccionRapida"   oncommand="popup('selalmacen.php?modo=empieza','dependent=yes,width=210,height=530,screenX=100,screenY=100,titlebar=yes')" 
    <?php gulAdmite("VerStocks") ?> label="<?php echo _("Captura CB") ?>"/>


<command id="nuevaCompra"  oncommand="solapa('modcompras.php?modo=noselecion')','<?php echo _("Compras") ?>')" 
  <?php gulAdmite("Compras") ?>  label="<?php echo _("Cancelar compra") ?>"/>
  
<command id="altaProducto" oncommand="solapa('modproductos.php?modo=alta','<?php echo _("Productos: Alta") ?>')" 
  <?php gulAdmite("Productos") ?>  label="<?php echo _("Alta producto") ?>"/>

<command id="altaTienda" oncommand="solapa('modlocal.php?modo=alta','<?php echo _("Tiendas: Alta") ?>','varios')"
  <?php gulAdmite("Administracion") ?>  label="<?php echo _("Alta tienda") ?>"/>

<command id="buscaCB" oncommand="solapa('modproductos.php?modo=buscaporcb'+'&amp;'+'CodigoBarras='+prompt('CB',''),'<?php echo _("Productos") ?>')" 
   <?php gulAdmite("Productos") ?> label="Busca CB"/>

<command id="verCompras" oncommand="solapa('xulcompras.php?modo=entra','<?php echo _("Compras") ?>','compras')"
  <?php gulAdmite("Compras") ?> label="<?php echo _("Compras") ?>"/>

<command id="verTiendas" oncommand="solapa('modlocal.php?modo=lista','<?php echo _("Tiendas") ?>','varios')"
  <?php gulAdmite("Administracion") ?>  label="<?php echo _("Ver tiendas") ?>"/>

<command id="verModistos" oncommand="solapa('modmodistos.php?modo=lista','<?php echo _("Modistos") ?>','varios')"
  <?php gulAdmite("Administracion") ?>  label="<?php echo _("Ver modistos") ?>"/>


<command id="verAlmacen"   oncommand="solapa('xulalmacen.php?modo=entra','<?php echo _("Almacén") ?>','almacen')" 
  <?php gulAdmite("VerStocks") ?> label="<?php echo _("Almacén") ?>"/>
  
<command id="verProductos"  oncommand="solapa('xulproductos.php?modo=lista','<?php echo _("Productos") ?>','productos')"
  <?php gulAdmite("Productos") ?>    label="<?php echo _("Productos") ?>"/>
    
<command id="verProveedores" oncommand="solapa('modproveedores.php?modo=lista','<?php echo _("Proveedores") ?>','proveedores')" 
  <?php gulAdmite("Proveedores") ?>  label="<?php echo _("Proveedores") ?>"/>
    
<command id="verClientes"  oncommand="solapa('modclientes.php?modo=lista','<?php echo _("Clientes") ?>','clientes')" 
  <?php gulAdmite("Administracion") ?>  label="<?php echo _("Clientes") ?>"/>
  
<command id="verUsuarios"   oncommand="solapa('modusers.php?modo=lista','<?php echo _("Usuarios") ?>','varios')"
  <?php gulAdmite("Administracion") ?>  label="<?php echo _("Ver usuarios") ?>"/>  
  
<command id="verFamilias"   oncommand="solapa('modfamilias.php?modo=lista','<?php echo _("Familias") ?>','varios')"
  <?php gulAdmite("Administracion") ?>   label="<?php echo _("Ver familias") ?>"/>  

<command id="verConfiguracion"   oncommand="solapa('xulconfiguracion.php?modo=inicio','<?php echo _("Configuración") ?>','configuracion')"
  <?php gulAdmite("Administracion") ?>  label="<?php echo _("Configuración") ?>"/>  
  
<command id="verListados"   oncommand="solapa('modulos/generadorlistados/formlistados.php?area=admin','<?php echo _("Ver Listados") ?>','listados')"
  <?php gulAdmite("Administracion","generadorlistados") ?>  label="<?php echo _("Ver Listados") ?>"/>  
  

<command id="verCarritoAlmacen"   oncommand="almacen_MuestraCarrito()"
  <?php gulAdmite("Stocks") ?>  label="<?php echo _("Ver carrito") ?>"/>  


<command id="verCarritoCancelar"   oncommand="ifConfirmExec('¿Esta seguro que quiere cancelar el carrito?','almacen_CancelarCarrito()')"
  <?php gulAdmite("Stocks") ?>  label="<?php echo _("Cancelar carrito") ?>"/>     
   
<command id="verCarritoEnOferta"   oncommand="almacen_EnOfertaCarrito()"
  <?php gulAdmite("Stocks") ?>  label="<?php echo _("En oferta") ?>"/>     

<command id="verCarritoObsoleto"   oncommand="almacen_EsObsoletoCarrito()"
  <?php gulAdmite("Stocks") ?>  label="<?php echo _("Es obsoleto") ?>"/>     

<command id="verCarritoNoObsoleto"   oncommand="almacen_NoEsObsoletoCarrito()"
  <?php gulAdmite("Stocks") ?>  label="<?php echo _("No es obsoleto") ?>"/>     



<command id="verCarritoSinOferta"   oncommand="almacen_SinOfertaCarrito()"
  <?php gulAdmite("Stocks") ?>  label="<?php echo _("Sin Oferta") ?>"/>    
  
<command id="verCarritoSinVenta"  oncommand="almacen_nosondisponiblesCarrito()"
  <?php gulAdmite("Stocks") ?>  label="<?php echo _("Reservado") ?>"/>    
   
<command id="verCarritoEnVenta"  oncommand="almacen_disponiblesCarrito()"
  <?php gulAdmite("Stocks") ?>  label="<?php echo _("En venta") ?>"/>      

<command id="listaProveedores" oncommand="proveedor_Ver()"  
 <?php gulAdmite("Proveedores") ?>  label="<?php echo _("Lista proveedores") ?>"/>   

<command id="listaClientes"  oncommand="clientes_Ver()"
  <?php gulAdmite("Administracion") ?>  label="<?php echo _("Lista clientes") ?>"/>   
<command id="altaCliente"  oncommand="clientes_Alta()" 
 <?php gulAdmite("Administracion") ?>  label="<?php echo _("Alta clientes empresa") ?>"/>

<command id="altaClienteParticular"  oncommand="clientes_AltaParticular()" 
 <?php gulAdmite("Administracion") ?>  label="<?php echo _("Alta cliente particular") ?>"/>


<command id="buzonSugerencia"  oncommand="solapa('modulos/mensajeria/modbuzon.php?modo=feature','<?php echo _("Buzón") ?>','buzon')" 
 <?php gulAdmite("Administracion","mensajeria") ?>  label="<?php echo _("Hacer sugerencia") ?>"/>
<command id="buzonReportefallo" oncommand="solapa('modulos/mensajeria/modbuzon.php?modo=bug','<?php echo _("Buzón") ?>','buzon')" 
 <?php gulAdmite("Administracion","mensajeria") ?>  label="<?php echo _("Hacer aviso de fallo") ?>"/> 

<command id="buzonReporte" oncommand="solapa('modulos/mensajeria/reporte.php','<?php echo _("Buzón") ?>','buzon')" 
 <?php gulAdmite("Administracion","mensajeria") ?>  label="<?php echo _("Hacer sugerencia de mantenimiento") ?>"/> 

<command id="buzonNotaNormal" oncommand="solapa('modulos/mensajeria/modbuzon.php?modo=notanormal','<?php echo _("Nota normal") ?>','buzon')" 
 <?php gulAdmite("Administracion","mensajeria") ?>  label="<?php echo _("Enviar nota normal") ?>"/> 

<command id="buzonNotaImportante" oncommand="solapa('modulos/mensajeria/modbuzon.php?modo=notaimportante','<?php echo _("Nota importante") ?>','buzon')" 
 <?php gulAdmite("Administracion","mensajeria") ?>  label="<?php echo _("Enviar nota importante") ?>"/> 


<command id="lanzarTPV" oncommand="lanzarTPV()" <?php gulAdmite("TPV") ?>  label="<?php echo _("Lanzar TPV") ?>"/> 
     
	     

   
<groupbox flex="1" class="frameExtraXX">
<caption label="9Gestion Modasoft"/>

<toolbox>
	<toolbar class="cabeceraAreaPagina">
		<button image="img/stock16.png" command="verAlmacen" <?php gulAdmite("VerStocks") ?> accesskey="a"/>
		<button image="img/producto16.png" command="verProductos" <?php gulAdmite("Productos") ?> accesskey="p"/>
		<button image="img/addcart16.gif"  command="verCompras" <?php gulAdmite("Compras") ?> accesskey="c"/>
		<button image="img/proveedor16.png" command="verProveedores" <?php gulAdmite("Proveedores") ?> accesskey="v"/>
		<button image="img/cliente16.png" command="verClientes" <?php gulAdmite("Administracion") ?> accesskey="l"/>
		<button image="img/addcart16.gif" command="lanzarTPV" <?php gulAdmite("TPV") ?> accesskey="T"/>	
		
<?php
	if (isUsuarioAdministradorWeb()) {	
 		
 		$menuWebmaster = array(
		_("Templates") =>  "verTemplates",
		_("Alta template") =>  "altaTemplate",
		_("Editar CSS") =>  "editarCSS",
		_("Editar xul CSS") =>  "editarxulCSS",	
		_("Editar JS") =>  "editarJS",
		_("Mostrar log") =>  "verLog",
		_("Mostrar sesión") =>  "verSesion"	
		);  
 		echo xulMakeMenuCommands("+",$menuWebmaster);
	}
	
?> 		
	          
	<spacer flex="1"/>       
    <button  label="<?php echo _("Buzón") ?>"  type="menu" image="img/config16.png" <?php gulAdmite("Administracion") ?>>	       	       
    <menupopup id="idconfig">
     <?php
 		
 		$menuConfiguracion = array(
		_("Enviar nota normal")	=> "buzonNotaNormal",
		_("Enviar nota importante")	=> "buzonNotaImportante",
		_("Informar sugerencia o bug") =>  "buzonReporte",
		);  
 		echo xulMakeMenuOptionsCommands($menuConfiguracion);
	
	 ?>
    </menupopup>
    </button>
            
    <button label="<?php echo _("Configuración") ?>" image="img/config16.png" type="menu"  <?php gulAdmite("Administracion") ?> oncommand="Configurar()">	        	       
    <menupopup id="idconfig">
     <?php
 		
 		$menuConfiguracion = array(		
		_("Modistos") =>  "verModistos",	
		_("Familias") =>  "verFamilias",
		_("Usuarios") =>  "verUsuarios",
		_("Perfiles") =>  "verPerfiles",
		_("Tiendas") =>  "verTiendas",
		_("Listados") => "verListados"

		);  
 		echo xulMakeMenuOptionsCommands($menuConfiguracion);
	
	 ?>
    </menupopup>
    </button>
       
	<button image="img/exit16.png" command="cmdLogout" accesskey="s"/>
	</toolbar>

</toolbox>
<menubar id="status-area" class="AreaPagina" style="">
  <caption id="status" class="enAreaPagina" label=""  style="font-size: 14px;font-weight: bold;" flex="1"/>
  <caption id="status" class="enAreaPagina" label="<?php echo _("Operador: ") . $NombreUsuarioDefecto_s ; ?>" style="font-size: 14px;font-weight: bold;"/>
</menubar>

<hbox flex='1' class="frameExtra">
 <box flex="1" class="frameExtra">
 <html:iframe  id="web"  class="AreaListados" src="about:blank" flex="1"/>
 </box>
 <deck id="DeckArea" style="width: 300px;">
  <vbox id='DeckNormal' class="frameExtra"/>
  <!-- Ventana Almacen -->
  <vbox id='DeckAlmacen' class="frameExtra" >
		<tabbox class="frameExtra" flex="1">		
			<box id="accionesweb" class="frameNormal">
			<groupbox class="frameNormal" flex="1">
			<caption label="<?php echo _("Acciones"); ?>" class="frameNormal"/>
			 <button image="img/find16.png" label='<?php echo _("Buscar") ?>' oncommand="almacen_buscar()"/>			 
			 <hbox equalsize="always">
			 <button image="img/cart.gif" command="verCarritoAlmacen" flex="1"/>     
			 <button crop="end" image="img/nocart.gif" command="verCarritoCancelar" flex="1"/>
			 </hbox>
			</groupbox>
			</box>
		
		  <tabs class="AreaPagina">
		    <tab label="<?php echo _("Normal") ?>"/>
		    <tab label="<?php echo _("Acciones carrito") ?>"/>
		    <tab label="<?php echo _("Capturar") ?>"/>
		  </tabs>
		  <tabpanels flex="1">
		    <tabpanel id="normaltab" flex='1'>		    	
				<groupbox flex="1">
					<caption label="<?php echo _("Buscar"); ?>" collapse="true"/>
				   <grid> 
				     <columns> 
				       <column flex="1"/><column flex="1"/>
				     </columns>
				    <rows>
				    	<row>					
				    	<caption label="<?php echo _("Local"); ?>"/>    
						<menulist  id="a_idlocal" oncommand="">						
						 <menupopup>
						 <menuitem label="<?php echo _("Elije local") ?>" style="font-weight: bold"/>
						<?php echo genXulComboAlmacenes(false,"menuitem") ?>
						 </menupopup>
						</menulist>						
     					</row>
				    	<row>
					    <caption label="<?php echo _("CB"); ?>"/>
						<textbox id="a_CB" flex="1" onkeypress="if (event.which == 13) { almacen_buscar() }"/>
						</row>				
						<row>
					    <caption label="<?php echo _("Ref"); ?>"/>
						<textbox id="a_Referencia" flex="1" onkeypress="if (event.which == 13) { almacen_buscar() }"/>
						</row>						
						<row>
					    <caption label="<?php echo _("Nombre"); ?>"/>
						<textbox id="a_Nombre" flex="1" onkeypress="if (event.which == 13) { almacen_buscar() }"/>
						</row>						
						<row>
					    <box/>
					    <vbox>
					    <checkbox id="a_Stock" label="<?php echo _("Solo con stock"); ?>" checked="true"/>		    					    			
					    <checkbox id="a_Obsoleto" label="<?php echo _("Mostrar obsoletos"); ?>" checked="false"/>
					    </vbox>
						</row>						
						
					</rows>
				  
				   </grid>					
					<button image="img/find16.png" label='<?php echo _("Buscar") ?>' oncommand="almacen_buscar()"/>
				</groupbox>
		    </tabpanel>
		    <tabpanel id="avanzadatab" flex='1'>
				<groupbox flex="1">
					<caption label="<?php echo _("Carrito selección"); ?>" collapse="true"/>			
					<button image="img/stock16.png" type="menu" label="<?php echo _("Trasladar mercancía"); ?>" oncommand="almacen_Traslado()">	        	       
    	   			<menupopup>
	       			<?php
					echo genXulComboAlmacenes(false,"menuitem","almacen_setLocalTraslado");
					?>
       				</menupopup>
       				</button>
					<grid>
					<columns><column flex="1"/><column flex="1"/></columns>
					<rows>
					<row>
						<button image="img/oferta16.gif"  command='verCarritoEnOferta' flex="1"/>
						<button image="img/oferta16gray.gif" command='verCarritoSinOferta' flex="1"/>
					</row>
					<row>					
						<button image="img/ok1.gif" command='verCarritoEnVenta' flex="1"/>
						<button image="img/ok1gray.gif" command='verCarritoSinVenta' flex="1"/>
					</row>
					<row>
						<button image="img/candadoabierto16.gif" command='verCarritoNoObsoleto' flex="1"/>
						<button image="img/candadocerrado16.gif"  command='verCarritoObsoleto' flex="1"/>						
					</row>										
					</rows>
					</grid>
				</groupbox>
		    </tabpanel>
		    <tabpanel id="capturarAlmacen" flex='1'>
				<groupbox flex="1">
					<caption label="<?php echo _("Captura CB"); ?>" collapse="true"/>	
					<menulist  id="a_idlocal_captura" oncommand="almacen_Guard_BotonCapturar()">						
					 <menupopup>
					 <menuitem label="<?php echo _("Elije local") ?>" style="font-weight: bold"/>
					<?php echo genXulComboAlmacenes(false,"menuitem") ?>
					 </menupopup>
					</menulist>					
					<textbox  id="a_CapturaCB" multiline="true" flex="1"/>																
					<button id="botonCapturarAlmacen" disabled="true" image="img/addcart16.gif"  label="<?php echo _("Añadir"); ?>" oncommand="Almacen_selrapidaCompra()"/>	        	       
				</groupbox>
		    </tabpanel>
		  </tabpanels>
		</tabbox>
	</vbox>
	<!-- Ventana Almacen -->
	<!-- Ventana Compras -->
	<vbox class="frameExtra">
		<box id="accionesweb" class="frameNormal">
		<groupbox class="frameNormal" flex="1">
	    <caption label="<?php echo _("Acciones"); ?>" class="frameNormal"/>
     <button  image="img/producto16.png" flex="1" label="<?php echo _("Alta rápida"); ?>" oncommand="Compras_altaRapida()"/>
     <hbox  equalsize="always">
     <button image="img/cart.gif" label="<?php echo _("Ver carrito") ?>" oncommand="Compras_verCarrito()" flex="1"/>     
	 <button crop="end" image="img/nocart.gif" label="<?php echo _("Cancelar carrito") ?>" oncommand="ifConfirmExec('¿Esta seguro que quiere cancelar el carrito?','Compras_cancelarCarrito()')" flex="1"/>
	 </hbox>
	 <button  image="img/stock16.png" id='bcapturar' flex="1" type="menu" label="<?php echo _("Finalizar compra"); ?>" oncommand="Compras_compraEfectuar()">	        	       
       <menupopup id="idlocal3">
       <?php
	echo genXulComboAlmacenes(false,"menuitem","Compras_setLocal");
	?>
       </menupopup>
       </button>	 	      
	    </groupbox>
	    </box>
		<tabbox class="frameExtra" flex="1">
		  <tabs class="AreaPagina">
		   <tab label="<?php echo _("Buscar") ?>"/>
		    <tab label="<?php echo _("Comprar") ?>"/>		    
		    <tab label="<?php echo _("Capturar") ?>"/>
		  </tabs>
		  
		  <tabpanels flex="1">
		    <tabpanel id="normaltab" flex='1'>		    	
				<groupbox flex="1">
					<caption label="<?php echo _("Buscar"); ?>" collapse="true"/>
				   <grid> 
				     <columns> 
				       <column flex="1"/><column flex="1"/>
				     </columns>
				    <rows>
				    	<row>
					    <caption label="<?php echo _("CB"); ?>"/>
						<textbox id="c_CB" flex="1"/>
						</row>				
						<row>
					    <caption label="<?php echo _("Ref"); ?>"/>
						<textbox id="c_Referencia" flex="1"/>
						</row>						
						<row>
					    <caption label="<?php echo _("Nombre"); ?>"/>
						<textbox id="c_Nombre" flex="1"/>
						</row>			
						<row><box/><checkbox id="c_Obsoletos" label="<?php echo _("Ver obsoletos"); ?>" checked="false"/></row>
					</rows>
				  
				   </grid>
					
					<button image="img/find16.png" label='<?php echo _("Buscar") ?>' oncommand="Compras_buscar()"/>
				</groupbox>
		    </tabpanel>
		  
		    <tabpanel id="comprapanel" flex='1'>		    	
				<groupbox flex="1">
					<caption label="<?php echo _("Comprar"); ?>" collapse="true"/>
				   <grid> 
				     <columns> 
				       <column flex="1"/><column flex="1"/>
				     </columns>
				    <rows>
				    	<row>
					    <caption label="<?php echo _("CB"); ?>"/>
						<textbox id="c_CB" flex="1"/>
						</row>										
					</rows>				  
				   </grid>
					<button  image="img/addcart16.gif" label='<?php echo _("Comprar") ?>' oncommand="Compras_selrapidaCompra()"/>
					<spacer style="height: 16px"/>					
				</groupbox>
		    </tabpanel>		  
		    <tabpanel id="avanzadatab" flex='1'>
				<groupbox flex="1">
					<caption label="<?php echo _("Captura CB"); ?>" collapse="true"/>					
					<textbox  id="c_CapturaCB" multiline="true" flex="1"/>																
					<button   image="img/addcart16.gif"  label="<?php echo _("Comprar"); ?>" oncommand="Compras_selrapidaCompra()"/>	        	       
				</groupbox>
		    </tabpanel>
		  </tabpanels>
		</tabbox>
	</vbox>
	<!-- Ventana Compras -->
	<!-- Ventana Productos -->
		<vbox class="frameExtra">
		<box id="accionesweb" class="frameNormal">
		<groupbox class="frameNormal" flex="1">
	    <caption label="<?php echo _("Acciones"); ?>" class="frameNormal"/>
	    <button  image="img/producto16.png"  label="<?php echo _("Alta de producto"); ?>" oncommand="Productos_ModoAlta();"/>
	    </groupbox>
	    </box>
		<tabbox class="frameExtra" flex="1">
		  <tabs class="AreaPagina">
		    <tab label="<?php echo _("Normal") ?>"/>
		    <tab label="<?php echo _("Búsqueda avanzada") ?>" oncommand="Productos_loadAvanzado()"/>
		  </tabs>
		  <tabpanels flex="1">
		    <tabpanel id="normaltab" flex='1'>
				<groupbox  flex="1">
				 <caption label="<?php echo _("Buscar") ?>" />
<grid>
	<columns>
    	<column flex="1" />
    	<column flex="1" />
  	</columns>

	<rows>
    	<row><caption label="<?php echo _("CB"); ?>"/><textbox id="p_CB" onkeypress="if (event.which == 13) { Productos_buscar() }"/></row>
    	<row><caption label="<?php echo _("Ref"); ?>"/><textbox id="p_Referencia" onkeypress="if (event.which == 13) { Productos_buscar() }"/></row>
		<row><caption label="<?php echo _("Nombre"); ?>"/><textbox id="p_Nombre" onkeypress="if (event.which == 13) { Productos_buscar() }"/></row>
		<row><box/><checkbox id="p_Obsoletos" label="<?php echo _("Ver obsoletos"); ?>" checked="false"/></row>
				
	</rows>
</grid>
					
					<button  image="img/find16.png" label='<?php echo _("Buscar") ?>' oncommand="Productos_buscar()"/>
				</groupbox>
		    </tabpanel>
		    <tabpanel id="avanzadatab" flex='1'>
				<groupbox flex="1">
					<caption label="<?php echo _("Búsqueda avanzada"); ?>"/>
					<iframe id="subframe" src="" flex='1'/>
				</groupbox>
		    </tabpanel>
		  </tabpanels>
		</tabbox>
	</vbox>
	<!-- Ventana Productos -->
	<!-- Ventana Proveedores -->
	<vbox class="frameNormal">		
	<groupbox class="frameNormal" >
	    <caption label="<?php echo _("Acciones"); ?>" class="frameNormal"/>
	     <button  image="img/proveedor16.png"  command="altaProveedor"/>
	     <button  image="img/eye16.gif"  command="listaProveedores"/>
	 </groupbox>

		<tabbox class="frameExtra" flex="1">
		  <tabs class="AreaPagina">		    		    
		  </tabs>
		  <tabpanels flex="1">
		    <tabpanel id="atab" flex='1'>
				<groupbox flex="1"></groupbox>
			</tabpanel>
		</tabpanels>
		</tabbox>
					
	</vbox>
	<!-- Ventana Proveedores -->
	<!-- Ventana Clientes -->
	<vbox flex="1" class="frameNormal">		
	<groupbox class="frameNormal">
	    <caption label="<?php echo _("Acciones"); ?>" class="frameNormal"/>	     
	     <button  image="img/cliente16.png" command="altaCliente"/>
	     <button  image="img/cliente16.png" command="altaClienteParticular"/>
	     <button  image="img/eye16.gif" command="listaClientes"/>	  	        	              	     
	     <box flex="1"></box>	     
	 </groupbox>	
	 
	 		<tabbox class="frameExtra" flex="1">
		  <tabs class="AreaPagina">		    		    
		  </tabs>
		  <tabpanels flex="1">
		    <tabpanel id="atab" flex='1'>
				<groupbox flex="1"></groupbox>
			</tabpanel>
		</tabpanels>
		</tabbox>
	     	
	</vbox>
	<!-- Ventana Clientes -->	
	<!-- Ventana Servicio Tecnico -->
	<vbox flex="1" class="frameNormal">		
	<groupbox class="frameNormal">
	    <caption label="<?php echo _("Acciones"); ?>" class="frameNormal"/>	     
	     <button  image="img/cliente16.png" command="buzonSugerencia"/>
	     <button  image="img/cliente16.png" command="buzonReportefallo"/>
	     <button  image="img/eye16.gif" command="listaClientes"/>	  	        	              	     
	     <box flex="1"></box>	     
	 </groupbox>	
	 
	 	  <tabbox class="frameExtra" flex="1">
		  <tabs class="AreaPagina">		    		    
		  </tabs>
		  <tabpanels flex="1">
		    <tabpanel id="atab" flex='1'>
				<groupbox flex="1"></groupbox>
			</tabpanel>
		</tabpanels>
		</tabbox>	     	
	</vbox>
	<!-- Ventana Servicio Tecnico -->	
	</deck>	
	<!-- Ventanas auxiliares -->
</hbox>

</groupbox>
<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/cadenas.js.php?ver=1/r<?php echo rand(0,99999999); ?>"/>
<script><![CDATA[

var id2nombreAlmacenes = new Array();

<?php

	$alm = new almacenes;
	$arrayTodos = $alm->listaTodosConNombre();
		
	$out = "";	
	$call = "";
	foreach($arrayTodos as $key=>$value){
		echo "id2nombreAlmacenes[$key] = '".addslashes($value). "';\n";			
	}
?>	




var myBrowser = false;
var olddoc = false;

var status = document.getElementById("status");
try {
 function $(cosa){
		return document.getElementById(cosa);
	}
} catch(e) {};


function getBrowser()  {
	if (document)
	  olddoc = document;
	else
	  document = olddoc;
	
    if (!myBrowser)
        myBrowser = document.getElementById("web");
                  
    return myBrowser;
}

function ifConfirmExec( mensaje, command){
  if (confirm(mensaje)){
    eval(command);
  }	
}


function OpenDeck(index){
  var deck = document.getElementById("DeckArea");  
  var main = getBrowser();
       
  deck.setAttribute("selectedIndex",index);
  main.setAttribute("src","about:blank");  
}

function CloseDeck(){ //No cierra realmente, solo oculta
	var main = getBrowser();
	var deck = document.getElementById("DeckArea");
	//main.setAttribute("collapsed","false"); 
	deck.setAttribute("selectedIndex",0);   
}


var extraVisible = 1;

function OcultaDeck(){
	var deck = document.getElementById("DeckArea");
	deck.setAttribute("collapsed","true");
	extraVisible = 0;	 	   
}

function MostrarDeck(){
	if (extraVisible)
		return;

	var deck = document.getElementById("DeckArea");
	deck.setAttribute("collapsed","false");	 	   
}

function solapa(url,area,deck){
	var main = getBrowser();  
    
   	status.setAttribute("label","Area " + area);
	switch(deck){
	  case "almacen":
	  	OpenDeck(1);
		document.getElementById("a_CB").focus();
		MostrarDeck();
	  	break;
	  case "compras":
	  	OpenDeck(2);
	  	document.getElementById("c_CB").focus();
		MostrarDeck();	  	
	  	break;
	  case "productos":
	  	OpenDeck(3);
	  	document.getElementById("p_CB").focus();
		MostrarDeck();	  	
	  	break;
	  case "proveedores":
	  	OpenDeck(4);
		MostrarDeck();	  	
	  	break;
	  case "clientes":
	  	OpenDeck(5);
		MostrarDeck();  	
	  	break;
	  case "listados":
	  	OcultaDeck();
	  	main.setAttribute("src", url);
	  	//alert("configuracion, deck oculto");
	  	break;	    
	  case "configuracion":
	  	OcultaDeck();
	  	main.setAttribute("src", url);
	  	//alert("configuracion, deck oculto");
	  	break;
	  case "varios":
	  	OcultaDeck();
	  	main.setAttribute("src", url);
	  	break;
	  default:
	    avanzadoCargado = 0;//cambios en familias/etc se reflejan inmediatamente
		main.setAttribute("src", url);     		      	  
	    CloseDeck();
	    break;
	}     	     	    	

}

function popup(url,metodo){
	if (window)	   window.open(url,"aux",metodo);
	else if (document)   document.open(url,metodo);
   
}

/* ========== TPV ============ */

var tpvWindow;

function lanzarTPV(){
	
	<?php 
	if (Admite("TPV")){
	?>
	
	if (tpvWindow && tpvWindow.close) {
		//try{
		tpvWindow.close();
		//} catch ();
	}

	/*	
		var url = "tpv8a.php";
		var	metodo = "chrome";
		tpvWindow =	window.open(url,"TPV",metodo);	*/
	window.document.location.href="tpvmodular.php?modo=tpv&r=" + Math.random();

	
	<?php  } else { ?>
	
	alert("<?php echo _("No eres un usuario autorizado para TPV"); ?>");

	<?php } ?>

}

/* ========== TPV ============ */

/* ========== BUZON ============ */

/*
<command id="buzonSugerencia"  oncommand="buzon_HacerSugerencia()" 
 <?php gulAdmite("Administracion","mensajeria") ?>  label="<?php echo _("Hacer sugerencia") ?>"/>
<command id="buzonReportefallo"  oncommand="buzon_Reporte()" 
*/

/* ========== BUZON ============ */

/* ========== ALMACEN ============ */



var subweb = document.getElementById("web");

var local = 0;
var localtraslado=0;
var localcaptura= 0;

function almacen_Guard_BotonCapturar() {
	localcaptura = document.getElementById("a_idlocal_captura").value;
	if (localcaptura){
		document.getElementById("botonCapturarAlmacen").setAttribute("disabled","false");
	}else{
			document.getElementById("botonCapturarAlmacen").setAttribute("disabled","true");
	}	
}


function Almacen_selrapidaCompra() {

	var cc  = document.getElementById("a_CapturaCB");		
		
	var url="selalmacen.php?modo=agnademudo_almacen";


	var xrequest = new XMLHttpRequest();
    var data = "IdLocal="+localcaptura+"&listacompra=" + escape(cc.value);       

	xrequest.open("POST",url,false);
	xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	xrequest.send(data);
	alert(xrequest.responseText);
	cc.value= "";
	cc.setAttribute("value","");
}


function almacen_setLocal(valor) { local = valor;}

function almacen_setLocalTraslado(valor) { localtraslado = valor; }


function almacen_CancelarCarrito(){
	var url = "modalmacenes.php?modo=borrarseleccion";
	subweb.setAttribute("src",url);
}

function almacen_SinOfertaCarrito(){
	var url = "modalmacenes.php?modo=nosonoferta";
	subweb.setAttribute("src",url);
}

function almacen_EnOfertaCarrito(){
	var url = "modalmacenes.php?modo=sonoferta";
	subweb.setAttribute("src",url);
}

function almacen_EsObsoletoCarrito(){
	var url = "modalmacenes.php?modo=esobsoleto";
	subweb.setAttribute("src",url);
}

function almacen_NoEsObsoletoCarrito(){
	var url = "modalmacenes.php?modo=noobsoleto";
	subweb.setAttribute("src",url);
}

function almacen_nosondisponiblesCarrito(){
	var url = "modalmacenes.php?modo=nosondisponibles";
	subweb.setAttribute("src",url);
}

function almacen_disponiblesCarrito(){
	var url = "modalmacenes.php?modo=sondisponibles";
	subweb.setAttribute("src",url);
}


function almacen_Traslado() {
    if (localtraslado==0)
    	return;

	var	nombreLocalDestino = new String(id2nombreAlmacenes[localtraslado]);
	nombreLocalDestino = nombreLocalDestino.toUpperCase()
	

	if (!confirm( po_moviendoa +" " +nombreLocalDestino + ". " + po_confirmatraslado)) {
		return;
	}

	var url = "modalmacenes.php?modo=albaran&IdLocal=" +localtraslado;
	subweb.setAttribute("src",url);
	local = 0;
}

function almacen_selrapidaalmacen() {
    if (local==0)
    	return;

	var url = "selalmacen.php?modo=empieza&IdLocal=" +local;
	subweb.setAttribute("src",url);
	local = 0;
}


function almacen_MuestraCarrito() {
	var url = "modalmacenes.php?modo=seleccion";
	subweb.setAttribute("src",url);
}


function almacen_buscar()
{  

   
  var extra  = "&CodigoBarras=" + document.getElementById("a_CB").value;  
  extra = extra +  "&IdLocal=" + document.getElementById("a_idlocal").value;
  extra = extra +  "&Referencia=" + document.getElementById("a_Referencia").value;
  extra = extra +  "&Nombre=" + document.getElementById("a_Nombre").value;
  var solollenos = (document.getElementById("a_Stock").checked)?1:0;
  var obsoletos  = (document.getElementById("a_Obsoleto").checked)?1:0;   
  extra = extra +  "&soloConStock=" + solollenos ;
  extra = extra +  "&mostrarObsoletos=" + obsoletos ;
  
  var url = "modalmacenes.php?modo=buscarproductos" + extra; 
  subweb.setAttribute("src", url);
}

/* ========== ALMACEN ============ */
/* ========== COMPRAS ============ */

var c_local = 0;
var c_capturalocal = 0;

function Compras_setLocal(valor) { c_local = valor;}

function Compras_CapturasetLocal(valor) { c_capturalocal = valor;}

function Compras_cancelarCarrito() {
	var url = "vercarrito.php?modo=noseleccion";
	subweb.setAttribute("src",url);
}

function Compras_compraEfectuar() {
	var url = "modcompras.php?modo=continuarCompra&IdLocal="+c_local;
	subweb.setAttribute("src",url);
}


function Compras_verCarrito() {
	var url = "vercarrito.php?modo=check";
	subweb.setAttribute("src",url);
}



function Compras_selrapidaCompra() {

	var cc  = document.getElementById("c_CapturaCB");		
		
	var url="selalmacen.php?modo=agnademudo_compras";


	var xrequest = new XMLHttpRequest();
    var data = "listacompra=" + escape(cc.value);       

	xrequest.open("POST",url,false);
	xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	xrequest.send(data);
	alert(xrequest.responseText);
	cc.value= "";
	cc.setAttribute("value","");
}

function Compras_altaRapida() {
	//var url = "modproductos.php?modo=alta";
	var url = "xulcompra.php?modo=alta";
	subweb.setAttribute("src",url);
}


function Compras_buscar()
{  
  
   
  var extra  = "&CodigoBarras=" + document.getElementById("c_CB").value;  
  //extra = extra +  "&IdLocal=" + document.getElementById("c_idlocal").value;
  extra = extra +  "&Referencia=" + document.getElementById("c_Referencia").value;
  extra = extra +  "&Nombre=" + document.getElementById("c_Nombre").value;   

  var obsoletos  = (document.getElementById("c_Obsoletos").checked)?1:0;
	extra = extra + "&Obsoletos="+obsoletos;

  
  url = "modcompras.php?modo=buscarproductos" + extra; 
  subweb.setAttribute("src", url);
}
/* ========== COMPRAS ============ */

/* ========== PRODUCTOS ============ */

var avanzadoCargado = 0;
var visiblebusca = 0;
var numPeticiones = 0;

function Productos_ModoAlta() {   
 subweb.setAttribute("src","modproductos.php?modo=alta"); 
}



function Productos_buscarextra(idprov,idcolor,idmarca,idtalla,idfam,tc) {
        
   if (tc)  tc="on"; else tc="";
   
  var extra = "&IdProveedor=" + idprov;
  extra = extra + "&IdColor=" + idcolor;
  extra = extra + "&IdMarca=" + idmarca;
  extra = extra + "&IdTalla=" + idtalla;
  extra = extra + "&IdFamilia=" + idfam;
       
  var url = "modproductos.php?modo=mostrar" + extra + "&verCompletas=" + tc;
  subweb.setAttribute("src", url);
}

function Productos_loadAvanzado(){
 var subframe;
 
 //Fuerza un update de las avanzadas cada 10 vistas
 {
	 numPeticiones = numPeticiones + 1;
	 if (numPeticiones > 10){
 		avanzadoCargado = 0;
 		numPeticiones = 0;
	 }
 }
 
 if (avanzadoCargado)
 	return;
 
 subframe = document.getElementById("subframe");
 subframe.setAttribute("src","xulavanzado.php?modo=productos&rnd="+Math.random());
 subframe.setAttribute("opener",document.getElementById("web"));
  
 avanzadoCargado = 1;
}


function Productos_buscar()
{  
  var codigo = document.getElementById("p_CB").value;
  var nombre = document.getElementById("p_Nombre").value;
  var referencia = document.getElementById("p_Referencia").value;
  var extra ="";
	
	extra = extra + "&CodigoBarras="+codigo;
	extra = extra + "&Nombre="+nombre;
	extra = extra + "&Referencia="+referencia;
 
  var obsoletos  = (document.getElementById("p_Obsoletos").checked)?1:0;
	extra = extra + "&Obsoletos="+obsoletos;
       
  url = "modproductos.php?modo=buscarproductos" + extra;
  subweb.setAttribute("src", url);
}

/* ========== PRODUCTOS ============ */
/* ========== PROVEEDORES ============ */

function proveedor_Alta(){
	var url = "modproveedores.php?modo=alta";
	subweb.setAttribute("src",url);
}

function proveedor_Ver(){
	var url = "modproveedores.php?modo=lista";
	subweb.setAttribute("src",url);
}

/* ========== PROVEEDORES ============ */
/* ========== CLIENTES ============ */

function clientes_Alta(){
	var url = "modclientes.php?modo=alta";
	subweb.setAttribute("src",url);
}

function clientes_AltaParticular(){
	var url = "modclientes.php?modo=altaparticular";
	subweb.setAttribute("src",url);
}

function clientes_Ver(){
	var url = "modclientes.php?modo=lista";
	subweb.setAttribute("src",url);
}

/* ========== CLIENTES ============ */
]]></script>

<?php


EndXul();


?>