<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");

StartXul(_("Elige proveedor"));

switch($modo){
	case "proveedorhab":
			
		echo "<groupbox flex='1'><caption label='" . _("Proveedor") . "'/>";		
		$familias = genArrayProveedores();		
		echo "<script>\n";
		echo " provhab =new Object();\n";
		foreach ($familias as $key=>$value){
			echo "provhab[$key] = '$value';\n";			
		}
		echo "\n</script>";						
				
		echo "<listbox flex='1' id='Proveedor' rows='5' onclick='opener.changeProvHab(this,provhab[this.value]);window.close();return true;'>";
		echo  genXulComboProveedores();				
		echo "</listbox>";
		echo "</groupbox>";
		
		break;				
	default:
		break;	
}

EndXul();


?>
