<?php

function SimpleAuth($module,$password){
	if (!$password){
		echo _("Este modulo esta desactivado");	
		exit();	
	}	
	
	if (!$_SESSION["Permitir_". $module]){
		$ok = false;
		if (isset($_POST["pass"])){
			if ($_POST["pass"]==$password){
				$ok = 1;
				$_SESSION["Permitir_". $module] = true;
			}
		}

	if (!$ok){	
			echo "<form method='post' name='form'>";
			echo "<input id='inputbox' type=password name='pass' onkeypress=\"if (event.which == 13) form.submit();\">" ;		
			echo "</form>" ;
			echo "<script> document.getElementById('inputbox').focus() </script>";
			exit();		
		} 	
	}
	
}

function Admite($noseque,$modulo=false){
	global $modulos;

	//Si exige modulo, pero este no esta disponible	
	if ($modulo and !$modulos[$modulo] )
		return false;	
	
	$val = getSesionDato("PerfilActivo");
	return 	$val[$noseque];
}

function xulAdmite($noseque,$modulo=false){
	if (Admite($noseque,$modulo)){
		return "";
	}else{
		return " disabled='true' ";		
	}

}

function gulAdmite($noseque,$modulo=false){		
	echo xulAdmite($noseque,$modulo);
}

function RegistrarTiendaLogueada($id){
	$id = CleanID($id);
	setSesionDato("IdTienda",$id);	
}

function RegistrarUsuarioLogueado($id){
			
	$sql = "SELECT Nombre,IdPerfil,AdministradorWeb FROM ges_usuarios WHERE IdUsuario='$id'";
	$row = queryrow($sql,"¿como se llama usuario?");
	if($row)
		$nombre = $row["Nombre"];
	
	
	setSesionDato("NombreUsuario",$nombre);
	setSesionDato("IdUsuario",$id);
	
	if ($row["AdministradorWeb"])
		setSesionDato("UsuarioAdministradorWeb",1);
	else
	  	setSesionDato("UsuarioAdministradorWeb",false);
	
	$user = getUsuario($id);
	$_SESSION["EsAdministradorFacturas"] = $user->get("AdministradorFacturas");
	
	//Autentificacion para modulos novisuales																							
	$_SESSION["AutentificacionAutomatica"] = true;

	$idPerfil = $row["IdPerfil"];
	
	$sql = "SELECT * FROM ges_perfiles_usuario WHERE IdPerfil = '$idPerfil'";
	
	$row = queryrow($sql);
	if (!$row)
		return;
	
	setSesionDato("PerfilActivo",$row);	
}


function identificacionLocalValidaMd5($identificador,$passmd5){
	global $_motivoFallo;
	
	//$randString = $_SESSION["CadenaAleatoria"];
	
	$identificador = CleanLogin($identificador);	
	$datosValidos = strlen($identificador)>1 and strlen($passmd5)>1;
	
	if (!$datosValidos) {
		//$_motivoFallo = "datos'$identificador o $passmd5 nulos'";
		return false;	
	}		
	
	$sql = "SELECT IdLocal,Password FROM ges_locales WHERE Identificacion = '$identificador' AND Eliminado=0";
	$row = queryrow($sql);
	if (!$row) {
		//$_motivoFallo = _("No encuentra local");			
		return false;
	}
	
	$valido = md5($row["Password"]);// . $randString);
	
	if ( $valido != $passmd5) {
		//$_motivoFallo = "DEBUG: datos'$valido != $passmd5', para " . $row["Password"];		
		return false;		
	}
		
	return $row["IdLocal"];	
}


function identificacionUsuarioValidoMd5($identificador,$passmd5){
	global $_motivoFallo;
	
	//$randString = $_SESSION["CadenaAleatoria"];
		
		
	$datosValidos = strlen($identificador)>1 and strlen($passmd5)>1;
	
	if (!$datosValidos)
		return false;		
	
	$sql = "SELECT IdUsuario, Password FROM ges_usuarios WHERE Identificacion = '$identificador' AND Eliminado=0";
	$row = queryrow($sql);
	if (!$row)
		return false;

	$valido = md5($row["Password"]);// . $randString);
	if ( $valido != $passmd5) {
		$_motivoFallo = "datos'$valido != $passmd5'";		
		return false;		
	}
		
		
	return $row["IdUsuario"];	
}


function identificacionLocalValida($identificador,$pass){
	
	$datosValidos = strlen($identificador)>1 and strlen($pass)>1;
	
	if (!$datosValidos)
		return false;	
		
	
	$sql = "SELECT IdLocal FROM ges_locales WHERE Identificacion = '$identificador' AND Password = '$pass'";
	$res = query($sql);
	if (!$res)
		return false;
		
	$row = Row($res);
	if (!is_array($row))
		return false;
		
	return $row["IdLocal"];	
}

function identificacionUsuarioValido($identificador,$pass){
	
	$datosValidos = strlen($identificador)>1 and strlen($pass)>1;
	
	if (!$datosValidos)
		return false;		
	
	$sql = "SELECT IdUsuario FROM ges_usuarios WHERE Identificacion = '$identificador' AND Password = '$pass'";
	$res = query($sql);
	if (!$res)
		return false;
		
	$row = Row($res);
	if (!is_array($row))
		return false;
		
	return $row["IdUsuario"];	
}

function SimpleAutentificacionAutomatica($subtipo=false,$redireccion=false){
	if(!isset($_SESSION["AutentificacionAutomatica"]) || !$_SESSION["AutentificacionAutomatica"]){
		//Si no esta autentificado, la pagina termina aqui mismo.
		// esto es valido para modulos sin parte visual,
		// y deberia solo ocurrir cuando se trata de acceder directamente		
		// por un cracker.
		
		if ($redireccion) {
			session_write_close();
			header("Location: $redireccion");
		}		
		exit;	
	}	
}
 
?>
