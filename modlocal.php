<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

function ListarLocales() {
	global $action;
	
	$res = Seleccion( "Local","","IdLocal ASC","");
	
	if (!$res){
		echo gas("aviso","No hay locales disponibles");	
	} else{
		
		//echo gas("titulo",_("Lista de locales"));
		echo "<center>";
		echo "<table border=0 class=forma>";
		echo "<tr><td class='lh'>Local</td><td class='lh'></td><td class='lh'></td></tr>";
		while ($oLocal = LocalFactory($res) ){		
		
			$id = $oLocal->getId();
			//error("Info: id es '$id'");
		
			$nombre = $oLocal->getNombre();
			$linkEdicion = gAccion("editar",_("Modificar"),$id); 
			$linkborrado = gAccionConfirmada( "borrar", _("Eliminar") ,$id ,_("¿Seguro que quiere borrar?"));  
			echo "<tr class='f'><td class=nombre>$nombre</td><td>$linkEdicion</td><td>$linkborrado</td></tr>";					
		}		
		echo "</table>";
		
		//TODO: debe ser relativo a un parametro: permitealtalocales
		echo g("center",gAccion("alta",_("Alta de local")));
	}
	
	userOperacionesConLocales();
	echo "</center>";	
}

function userOperacionesConLocales(){
	//OBSOLETO
}

		

function MostrarLocalParaEdicion($id) {
	global $action;
	
	$oLocal = new local;
	if (!$oLocal->Load($id)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
	
	echo $oLocal->formEntrada($action,true);	
}

function setAlmacenCentral($id){
	$id = CleanID($id);
	$sql = "UPDATE ges_locales SET AlmacenCentral = 0";
	query($sql);
	
	$sql = "UPDATE ges_locales SET AlmacenCentral = 1 WHERE IdLocal = '$id'";
	query($sql);
	
	$sql = "UPDATE ges_parametros SET AlmacenCentral = '$id'";
	query($sql);
}

function ModificarLocal($id,$nombre,
			$nombrelegal,$direccion,
			$poblacion,$codigopostal,
			$telefono,$fax,
			$movil, $email,
			$paginaweb,$cuentabancaria,$pass,$identificacion,$esCentral,$IdTipoNumeracionFactura,
			$ImpuestoIncluido,$idpais,$ididioma,$MensajeMes			
			){
	$oLocal = new local;
	if (!$oLocal->Load($id)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
	
	
	$oLocal->set("NombreComercial",$nombre,FORCE);
	$oLocal->set("NombreLegal",$nombrelegal,FORCE);
	$oLocal->set("DireccionFactura",$direccion,FORCE);
	$oLocal->set("Poblacion",$poblacion,FORCE);
	$oLocal->set("CodigoPostal",$codigopostal,FORCE);
	$oLocal->set("Telefono",$telefono,FORCE);
	$oLocal->set("Fax",$fax,FORCE);
	$oLocal->set("Movil",$movil,FORCE);
	$oLocal->set("Email",$email,FORCE);
	$oLocal->set("PaginaWeb",$paginaweb,FORCE);
	$oLocal->set("CuentaBancaria",$cuentabancaria,FORCE);
	$oLocal->set("Password",$pass,FORCE);
	$oLocal->set("Identificacion",$identificacion,FORCE);
	$oLocal->set("IdTipoNumeracionFactura",$IdTipoNumeracionFactura,FORCE);
	$oLocal->set("ImpuestoIncluido",$ImpuestoIncluido,FORCE);
	$oLocal->set("IdPais",$idpais,FORCE);
	$oLocal->set("IdIdioma",$ididioma,FORCE);
	$oLocal->set("MensajeMes",$MensajeMes,FORCE);
	
	if ($esCentral){		
		setAlmacenCentral($id);	
		$oLocal->set("AlmacenCentral",1,FORCE);
	}
		
			
	if ($oLocal->Modificacion()){
		//if(isVerbose())
		//	echo gas("aviso",_("Local modificado"));	
		invalidarSesion("ListaTiendas");
		unset($_SESSION["tLOCAL_$id"]);
		
		return true;
	} else {
		//echo gas("problema",_("No se puedo cambiar dato"));	
		return false;
	}	
}

function OperacionesConLocales(){
	if (!isUsuarioAdministradorWeb())
		return;
	
	
	echo gas("titulo",_("Operaciones sobre Locales"));
	echo "<table border=1>";
	echo "<tr><td>"._("Crear una nueva tienda")."</td><td>".gModo("alta",_("Alta"))."</td></tr>";
	echo "</table>";
}

function FormularioAlta() {
	global $action;
	
	$oLocal = new local;

	$oLocal->Crea();
	
	echo $oLocal->formEntrada($action,false);	
}



function CrearLocal($nombre,
			$nombrelegal,$direccion,
			$poblacion,$codigopostal,
			$telefono,$fax,
			$movil, $email,
			$paginaweb,$cuentabancaria,$pass,$identificacion,$idpais,$idioma){
	$oLocal = new local;

	$oLocal->Crea();

	$oLocal->set("NombreComercial",$nombre,FORCE);
	$oLocal->set("NombreLegal",$nombrelegal,FORCE);
	$oLocal->set("DireccionFactura",$direccion,FORCE);
	$oLocal->set("Poblacion",$poblacion,FORCE);
	$oLocal->set("CodigoPostal",$codigopostal,FORCE);
	$oLocal->set("Telefono",$telefono,FORCE);
	$oLocal->set("Fax",$fax,FORCE);
	$oLocal->set("Movil",$movil,FORCE);
	$oLocal->set("Email",$email,FORCE);
	$oLocal->set("PaginaWeb",$paginaweb,FORCE);
	$oLocal->set("CuentaBancaria",$cuentabancaria,FORCE);
	$oLocal->set("Password",$pass,FORCE);
	$oLocal->set("Identificacion",$identificacion,FORCE);
	$oLocal->set("IdPais",$idpais,FORCE);
	$oLocal->set("IdIdioma",getIdFromLang("es"),FORCE);
		
	if ($oLocal->Alta()){
		invalidarSesion("ListaTiendas");
		
		//TODO: aqui tenemos una ligadura fuerte entre un modulo y la aplicación.
		// esto se debe automatizar para que la ligadura sea debil.		
		$oLocal->IniciarArqueos();		

		return true;
	} else {
		//echo gas("aviso",_("No se ha podido registrar el nuevo local"));
		return false;
	}
}

function PaginaBasica(){
	ListarLocales();	
	OperacionesConLocales();	
}

function BorrarTienda($id){
	$oLocal = new local;	
	
	if ($oLocal->Load($id)) {		
		$nombre = $oLocal->getNombre();
		//echo gas("Aviso",_("Local eliminado"));
		
		$oLocal->MarcarEliminado();			
		invalidarSesion("ListaTiendas");	
		return true;
	}	else {
		//echo gas("Aviso",_("No se ha podido borrar el local"));	
		return false;
	}
}

PageStart();

//echo gas("cabecera",_("Gestion de Locales"));


switch($modo){
	case "newsave":		
		$nombre = $_POST["NombreComercial"];
		$nombrelegal = $_POST["NombreLegal"];
		$direccion = $_POST["DireccionFactura"];
		$poblacion = $_POST["Poblacion"];
		$codigopostal = $_POST["CodigoPostal"];
		$telefono = $_POST["Telefono"];
		$fax = $_POST["Fax"];
		$movil = $_POST["Movil"];
		$email = $_POST["Email"];
		$paginaweb = $_POST["PaginaWeb"];
		$cuentabancaria = $_POST["CuentaBancaria"];		
		$identificacion = $_POST["Identificacion"];		
		$idpais 	= CleanID($_POST["IdPais"]);	
		$ididioma 	= CleanID($_POST["IdIdioma"]);
		$pass= $_POST["Password"];
		
		CrearLocal($nombre,
			$nombrelegal,$direccion,
			$poblacion,$codigopostal,
			$telefono,$fax,
			$movil, $email,
			$paginaweb,$cuentabancaria,$pass,$identificacion,$idpais,$ididioma);
			
		PaginaBasica();	
		break;	
	case "alta":
		FormularioAlta();	
		break;
	case "modsave":
		$id 				= CleanID($_POST["id"]);
		$nombre 			= CleanText($_POST["NombreComercial"]);
		$nombrelegal 		= CleanText($_POST["NombreLegal"]);
		$direccion 			= CleanText($_POST["DireccionFactura"]);
		$poblacion 			= CleanText($_POST["Poblacion"]);
		$codigopostal 		= CleanCP($_POST["CodigoPostal"]);
		$telefono 			= CleanTelefono($_POST["Telefono"]);
		$fax 				= CleanTelefono($_POST["Fax"]);
		$movil 				= CleanTelefono($_POST["Movil"]);
		$email 				= CleanEmail($_POST["Email"]);
		$paginaweb 			= CleanUrl($_POST["PaginaWeb"]);
		$cuentabancaria 	= $_POST["CuentaBancaria"];
		$pass 				= CleanTo($_POST["Password"]," ");
		$identificacion 	= $_POST["Identificacion"];
		$esCentral 			= ($_POST["esCentral"] =='on');
		$IdTipoNumeracionFactura = CleanID($_POST["IdTipoNumeracionFactura"]);
		$ImpuestoIncluido 	=	CleanID($_POST["ImpuestoIncluido"]=='on');
		$idpais 			= CleanID($_POST["IdPais"]);
		$ididioma 			= CleanID($_POST["IdIdioma"]);
		$mensaje 			= CleanText($_POST["MensajeMes"]);
		
		ModificarLocal($id,$nombre,
			$nombrelegal,$direccion,
			$poblacion,$codigopostal,
			$telefono,$fax,
			$movil, $email,
			$paginaweb,$cuentabancaria,$pass,$identificacion,$esCentral,$IdTipoNumeracionFactura,
			$ImpuestoIncluido,$idpais,$ididioma,$mensaje			
			);
		PaginaBasica();	
		break;
	case "editar":
		$id = CleanID($_GET["id"]);
		MostrarLocalParaEdicion($id);
		break;
	case "borrar":
		$id = CleanID($_GET["id"]);
		BorrarTienda($id);
		PaginaBasica();			
		break;		
	default:
		ListarLocales();
		OperacionesConLocales();
		break;		
}

PageEnd();

?>
