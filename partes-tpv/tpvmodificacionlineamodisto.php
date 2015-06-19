<hbox id="editandoArreglo"  align="center" pack="center">
<spacer flex="1"/>
<groupbox>
	<caption class="grande" label="Arreglo"/>
	
<hbox>
<grid> 
 <columns><column flex="1"/></columns>
<rows>
<row>
	<caption class="media" label="Descripcion:"/>
	<menulist  class="media" id="arregloDescripcion" oncommand="" editable="true">						
		<menupopup class="media">
			<Description class="media" label="Elije ..." style="font-weight: bold;background-color: none">
			</Description>
			<menuitem class="media" value='1' label='Recoger dobladillo' />
			<menuitem class="media" value='2' label='Quitar mangas' />
			<menuitem class="media" value='3' label='Cambiar correa' />
			<menuitem class="media" value='14' label='Coser pernera' />
			<menuitem class="media" value='11' label='Matizar' />
			<menuitem class="media" value='12' label='Otros...'/>
		</menupopup>			
	</menulist>	
</row>
<row>
	<caption class="media" label="Modisto:"/>
	<menulist  class="media"  id="arregloModisto" oncommand="">						
		<menupopup class="media" >
			<Description class="media" label="Elije ..." style="font-weight: bold;background-color: none">
			</Description>
<?php 
			echo $genModistos;

?>
		</menupopup>			
	</menulist>	
</row>
<row>
	<caption class="media" label="Coste:"/>
	<textbox value="0.00" class="media precio"  id="precioArreglo"  onkeypress="if (event.which == 13) agnadirLineaModisto()"/>
</row>
<row>
	<box/>
	<hbox flex="1">
	 <button class="media" flex="1" label="Cancelar" oncommand="CancelarArreglo()"/>
	 <button class="media"  flex="1" label="Entrar" oncommand="agnadirLineaModisto()"/>
	</hbox>
</row>
</rows>
</grid>
</hbox>

</groupbox>
<spacer flex="1"/>
</hbox>
