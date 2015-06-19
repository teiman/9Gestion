<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");

StartXul(_("Elije color"));


switch($modo){
	case "nuevocolor":
	   $sql = "SELECT Max(IdColor) as MaxCol FROM ges_colores";
	   $row = queryrow($sql);
	   
	   if ($row){
	   		$IdIdioma = getSesionDato("IdLenguajeDefecto");
			$max = intval($row["MaxCol"])+1; 	
			$color = CleanRealMysql(CleanText($_GET["color"]));
			$sql = "INSERT INTO ges_colores 
	           (IdColor, IdIdioma, Color) 
	       VALUES ( '$max', '$IdIdioma', '$color')";
	       
	       	query($sql,"Creando nuevo color");
	   }
	
	case "color":
		//echo gas("titulo",_("Color"));
		echo "<groupbox> <caption label='" . _("Colores") . "'/>";
  
		$familias = genArrayColores();
		$combo = "";
		echo "<script>\n";
		echo " fam =new Object();\n";
		foreach ($familias as $key=>$value){
			echo "fam[$key] = '$value';\n";
			//$combo = "<option 			
		}
		
		echo "
		function UsarNuevo() {
              
			var color, url;
			var nuevocolor = document.getElementById('nuevocolor');			
			if (nuevocolor)
                 color = nuevocolor.value;
            if (!color || color == '')
                 return;
            
			url = 'selcolor.php';
			url = url +'?';
            url = url + 'modo';
            url = url + '=nuevocolor';
            url = url + '&amp;'+'color=' + color;
			document.location.href = url			
		}
		";
		
		
		echo "\n</script>\n";						
				
		echo "<listbox rows='5' flex='1' id='Color'  onclick='opener.changeColor(this,fam[this.value]);window.close();return true;'>\n";		
		echo  genXulComboColores();				
		echo "</listbox>";		
		echo "</groupbox>";
		
		echo "<groupbox>".
		  "<caption label='" . _("Nuevo color") . "'/>";
		echo "<textbox id='nuevocolor'/>";
		echo "<button label='"._("Nuevo")."' onkeypress='if (event.which == 13) UsarNuevo()' oncommand='UsarNuevo()'/>";
		echo "</groupbox>";
		
		break;		

	case "xtallaje":
		echo "<groupbox flex='1'> <caption label='" . _("Tallajes") . "'/>";
		
		$sql = "SELECT IdTallaje,Tallaje FROM ges_tallajes ORDER BY Tallaje ASC";
		$res = query($sql);
		while( $row= Row($res) ) {
			$txtalla = 	$row["Tallaje"];
			$idtalla =  $row["IdTallaje"];
			
			if (getParametro("TallajeLatin1")){				
				$txtalla = iso2utf($txtalla);	
			}
			
			echo "<button label='". $txtalla."' oncommand='changeNuestroTallaje(\"".$idtalla."\",\"".$txtalla."\",opener);'/>";	
		}				
		echo "<spacer flex='1'/>";
		echo "<button label='". _("Cerrar")."' oncommand='window.close()'/>";	

		echo "<script>
		function changeNuestroTallaje(idtallaje,txt,padre) {
 			padre.changeTallaje(idtallaje,txt);
 			window.close(); 			
		}
		</script>";
		
		echo "</groupbox>";
	   break;
	   
	   
	case "tallaje":
		echo "<groupbox flex='1'> <caption label='" . _("Tallajes") . "'/>";
		
		$sql = "SELECT IdTallaje,Tallaje FROM ges_tallajes ORDER BY Tallaje ASC";
		$res = query($sql);
		while( $row= Row($res) ) {
			$txtalla = $row["Tallaje"];
			
			if (getParametro("TallajeLatin1")){				
				$txtalla = iso2utf($txtalla);	
			} 
			
			echo "<button label='". $txtalla."' oncommand='UsaTallaje(".$row["IdTallaje"].")'/>";	
		}				
		echo "<spacer flex='1'/>";
		echo "<button label='". _("Cerrar")."' oncommand='window.close()'/>";	

		echo "<script>
		function UsaTallaje(id){		
			document.location.href = 'selcolor.php?modo=talla&amp;IdTallaje='+id;
		}

		function changeTalla(me,val) {
 			document.opener.changeTalla(me,val);
		}
		</script>";
		
		echo "</groupbox>";
	   break;
	case "nuevatalla":
	   $sql = "SELECT Max(IdTalla) as MaxTal FROM ges_tallas";
	   $row = queryrow($sql);
	   
	   if ($row){
	   		$IdIdioma = getSesionDato("IdLenguajeDefecto");
			$max = intval($row["MaxTal"])+1; 	
			$talla = CleanText($_GET["talla"]);
			$tallaje = CleanID($_GET["IdTallaje"]);
			
			$sql = "INSERT INTO ges_tallas 
	           (IdTalla, IdIdioma, Talla, IdTallaje) 
	       VALUES ( '$max', '$IdIdioma', '$talla', '$tallaje')";
	       
	       	query($sql,"Creando nueva talla");
	   }			   
	case "talla":		
		
		$IdTallaje = CleanID($_GET["IdTallaje"]);
	
		echo "<groupbox flex='1'>
		  <caption label='" . _("Tallas") . "'/>";
		  
		$familias = genArrayTallas($IdTallaje);
		$combo = "";
		echo "<script>\n";
		echo " fam =new Object();\n";
		foreach ($familias as $key=>$value){
			echo "fam[$key] = '$value';\n";
		}
		echo "
		function UsarNuevo(IdTallaje) {
              
			var talla, url;
			var nuevocolor = document.getElementById('nuevatalla');			
			if (nuevocolor)
                 talla = nuevocolor.value;
            if (!talla || talla == '')
                 return;
            
			url = 'selcolor.php';
			url = url +'?';
            url = url + 'modo';
            url = url + '=nuevatalla';
            url = url + '&amp;'+'talla=' + talla;
            url = url + '&amp;'+'IdTallaje=' + IdTallaje;

			document.location.href = url			
		}
		";

		echo "\n</script>";						
		
				
		echo "<listbox id='Talla' flex='1' onclick='opener.changeTalla(this,fam[this.value]);window.close();return true;'>\n";
		echo  genXulComboTallas(false,"listitem",$IdTallaje);				
		echo "</listbox>";
		echo "</groupbox>";
		
		echo "<groupbox>".
		  "<caption label='" . _("Nueva talla") . "'/>";
		echo "<textbox id='nuevatalla'/>";
		echo "<button label='"._("Nuevo")."' onkeypress='if (event.which == 13) UsarNuevo()' oncommand='UsarNuevo(".$IdTallaje.")'/>";
		echo "</groupbox>";

		break;		
			
	default:
		break;	
}


//PageEnd();
EndXul();

?>
