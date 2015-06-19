<hbox align="center">	
	<image src="img/bar16.png" height="16" width="16"/>
    <caption  label="CB" class="compacta"/>
    <textbox   id="CB"  size="12" class="compacta" flex="1" onkeypress="if (event.which == 13) agnadirPorCodigoBarras()"/>
	<spacer style="width: 32px"/>
	<caption label="REF"  class="compacta" />
	<image class="menulist-icon" src="img/busca1.gif"/>
	<textbox  id="REF"  size="12" class="compacta" flex="1" onkeypress="if (event.which == 13) agnadirPorReferencia()"/>
	<spacer style="width: 32px"/>
	<caption label="NOM"  class="compacta" />
	<image class="menulist-icon" src="img/busca1.gif"/>
	<textbox  id="NOM"  size="12" class="compacta" flex="1" onkeypress="if (event.which == 13) agnadirPorNombre()"/>
	<spacer style="width: 32px"/>
      <image src="img/network.png" style="width:16px;height:16px" id="bolaMundo"/>      
      <checkbox  id="buscar-servidor" checked="true" name="buscar-en-internet"/>

</hbox>