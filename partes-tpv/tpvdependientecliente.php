<hbox align="center" style="zbackground-color: orange;">
  <hbox flex="1" align="center">    
    <description class="media">Le esta atendiendo </description><toolbarbutton style="background-color: transparent" id="depLista" type="menu" label="<?php echo $NombreDependienteDefecto; ?>" class="media" oncommand ="cambiaDependiente()">
       <menupopup>
		 <?php
		 
		 echo $generadorDeDependientes;
				
		 ?>
        </menupopup>     
	</toolbarbutton>              
  </hbox>
  <description  class="media"><?php
  
  echo $NombreLocalActivo
  
  ?>
  </description>
  <spacer style="width: 32px"/>
  <toolbarbutton image="img/exit16.png" oncommand="SalirNice()" class="salir"/>   
</hbox>