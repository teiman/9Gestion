<?php

/* Funciones necesarias */


function ErrorMensaje($mensaje,$fatal=false){
	echo "<div style='background-color: #eee; border:2px solid red;padding:8px'>";
	//echo "<h3>".$UltimaTarea. "</h3>";
	if ($mensaje){ 
		echo ($fatal?"Error fatal: ":"Error: ");	
		echo $mensaje;
	} else {
		if ($fatal) {
			echo "Se ha producido un error fatal.";	
		} else 
			echo "Se ha producido un error.";	
	}
	echo "</div>";	
}
	



function webAssert($condicionTrue,$mensajeOk,$mensajeError,$fatal=false){
	global $numErrores,$numFatal,$UltimaTarea;
	if ($condicionTrue) {		
		echo $mensajeOk;
		
		if($mensajeOk and $mensajeOk!="" and $mensajeOk!=".")
			echo "<br>";					
		
		return 0;
	}
	
	//Fallo:	

	if ($fatal) 
		$numFatal++;
	else
		$numErrores++;


	echo "<div style='background-color: #eee; border:2px solid red;padding:8px'>";
	//echo "<h3>".$UltimaTarea. "</h3>";
	if ($mensajeError){ 
		echo ($fatal?"Error fatal: ":"Error: ");	
		echo $mensajeError;
	} else {
		if ($fatal) {
			echo "Se ha producido un error fatal.";	
		} else 
			echo "Se ha producido un error.";	
	}
	echo "</div>";
	
	if ($fatal)
		die;
	
	return 1;//numero de errores		
} 



function IniciaTarea($mensaje){
	//global $UltimaTarea;
	//$UltimaTarea = $mensaje;
	echo "<h2>",$mensaje,"</h2>";	
}

function split_queris($bigcode){
	$lines = split("\n",$bigcode);
	$out = array();
	$buffer = "";
	
	foreach( $lines as $line ){	
		if (!preg_match('/^;;;;;;/', $line)){
			$buffer .= $line;
		}	else {
			$out[] = $buffer;
			$buffer = "";						
		}		
	}	
	return $out;	
}



function PresentarInterface($interface,$datos=false){	
	include($interface);	
}


?>