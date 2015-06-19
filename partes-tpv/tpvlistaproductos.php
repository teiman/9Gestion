    <listbox id="listaProductos" flex="1" contextmenu="accionesLista" ondblclick="agnadirPorMenu(1)" class="listado">
     <listcols flex="1">
		<listcol style="maxwidth: 8em"/>
		<splitter class="tree-splitter" />
		<listcol style="maxwidth: 40em;minwidth: 40em;"/>
		<splitter class="tree-splitter" />
		<listcol  style="maxwidth: 8em"/>
		<splitter class="tree-splitter" />
		<listcol  flex="1"/>
		<splitter class="tree-splitter" />
		<listcol style="maxwidth: 8em"/>
		<splitter class="tree-splitter" />
		<listcol/>		
		<listcol/>				
     </listcols>
     <listhead>
		<listheader label="Referencia"/>
		<listheader label="Nombre"/>
		<listheader label="Talla"/>
		<listheader label="Color"/>
		<listheader label="<?php echo $pvpUnidad ?>"/>
		<listheader label="" />
		<listheader label="" />		
     </listhead>

    </listbox>