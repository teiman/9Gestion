<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

$op = new Producto;

$op->Crea(); 

$Referencia = $op->get("Referencia");
$Nombre 	= $op->get("Nombre");
$Marca 		= _("Varias");
$primerCB 	= $op->get("CodigoBarras");

switch($modo) {
	case "cb":
		echo $primerCB;
		break;		
	case "tallas":
		$IdTallaje = CleanID($_GET["IdTallaje"]);
		$talla = genArrayTallas($IdTallaje);
		
		foreach ($talla as $key=>$value) {
			echo "$value=$key\n";
		}		
		break;	
		
}



?>
