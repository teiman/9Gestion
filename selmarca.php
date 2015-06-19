<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");

StartXul(_("Elije color")); 


switch($modo){
	case "salvamarca":
		$marca = CleanText($_GET["marca"]);
		if (!$marca or $marca == "")
			break;
		
		$sql = "SELECT IdMarca FROM ges_marcas WHERE Marca='$marca'";
		$row = queryrow($sql);
		
		if ($row and $row["IdMarca"]) {
			$idold = $row["IdMarca"];
			$sql = "UPDATE ges_marcas SET Eliminado=0 WHERE IdMarca='$idold'";					
			query($sql);// devolvemos a la vida una marca existente
			break;		
		}
		

		query("INSERT INTO ges_marcas (Marca) VALUES ('$marca')");
		break;
		
	case "eliminamarca":
		$marca = CleanText($_GET["marca"]);
		$sql = "UPDATE ges_marcas SET Eliminado=1 WHERE Marca='$marca'";
		query($sql);	
		break;
	default:
		break;	
}


//SE EJECUTA SIEMPRE

		echo "<groupbox><caption label='" . _("Marcas") . "'/>";
		
		$familias = genArrayMarcas();
		$combo = "";
		echo "<script>\n";
		echo " fam =new Object();\n";
		foreach ($familias as $key=>$value){
			echo "fam[$key] = '$value';\n";
		}
		
		echo "
		function UsarNuevo() {
              
			var talla, url;
			var nuevocolor = document.getElementById('nuevamarca');			
			if (nuevocolor)
                 talla = nuevocolor.value;
            if (!talla || talla == '')
                 return;
            
			url = 'selmarca.php';
			url = url +'?';
            url = url + 'modo';
            url = url + '=salvamarca';
            url = url + '&amp;'+'marca=' + talla;
			document.location.href = url			
		}
		
		function Eliminar() {
			var marcaname, url;
			var lamarca = document.getElementById('nuevamarca');	
			if (lamarca) 
				marcaname = lamarca.value;
			if (!marcaname || marcaname== '')
				return;
				
			url = 'selmarca.php';
			url = url +'?';
            url = url + 'modo';
            url = url + '=eliminamarca';
            url = url + '&amp;'+'marca=' + marcaname;
			document.location.href = url						  			
		}
		
		";
		
		echo "\n</script>";						
				
		echo "<listbox id='Marca' rows='5' onclick='opener.changeMarca(this,fam[this.value]);window.close();return true;'>\n";
		echo  genXulComboMarcas();				
		echo "</listbox>";
		echo "</groupbox>";
		echo "<groupbox>";
		echo "<caption label='" . _("Marca") . "'/>";
		echo "<textbox id='nuevamarca'/>";
		echo "<hbox flex='1'>";
		echo "<button flex='1' label='"._("Crear")."' onkeypress='if (event.which == 13) UsarNuevo()' oncommand='UsarNuevo()'/>";
		echo "<button flex='1' label='"._("Eliminar")."' onkeypress='if (event.which == 13) Eliminar()' oncommand='Eliminar()'/>";
		echo "</hbox>";
		echo "</groupbox>";
		//echo "<p><a class=tb href='selmarca.php?modo=creamarca'>". _("Nueva marca") . "</a></p>";



EndXul();






?>
