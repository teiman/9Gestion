<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

function ListarPerfiles() {
	global $action;
	
	$res = Seleccion( "Perfil","","IdPerfil ASC","");
	
	if (!$res){
		echo gas("aviso","No hay perfiles disponibles");	
	} else{
		
		//echo gas("titulo",_("Lista de perfiles"));
		echo "<center>";
		echo "<table border=0 class=forma>";
		echo "<tr><td class='lh'>Perfil</td><td class='lh'></td><td class='lh'></td></tr>";
				
		while ($oPerfil = PerfilFactory($res) ){		
		
			$id = $oPerfil->getId();
			//error("Info: id es '$id'");
		
			$nombre = $oPerfil->getNombre();
			//$linkEdicion = gModoButton("editar",_("Modificar"),"id=".$id); 
			//$linkborrado = gModoButton("borrar",_("Eliminar"),"id=".$id);
			$linkEdicion = gAccion("editar",_("Modificar"),$id); 
			$linkborrado = gAccionConfirmada( "borrar", _("Eliminar") ,$id ,_("¿Seguro que quiere borrar?"));
			echo "<tr class='f'><td class='nombre'>$nombre</td><td>$linkEdicion</td><td>$linkborrado</td></tr>";
					
		}		
		echo "</table>";


	}
	userOperacionesConPerfiles();
	echo "</center>";
}

function userOperacionesConPerfiles(){
	//OBSOLETO
}



function MostrarPerfilParaEdicion($id) {
	global $action;
	
	$oPerfil = new perfil;
	if (!$oPerfil->Load($id)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
	
	echo $oPerfil->formEntrada($action,true);	
}

function ModificarPerfil($id,$nombre,
			$Admin,$Informe,
			$Productos,$Compras,
			$Stocks,$Clientes,
			$TPV,$Proveedores,$VerStocks	){
	$oPerfil = new perfil;
	if (!$oPerfil->Load($id)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
	
	$oPerfil->setNombre($nombre);
	$oPerfil->set("Administracion",$Admin,FORCE);
	$oPerfil->set("InformeLocal",$Informe,FORCE);
	$oPerfil->set("Productos",$Productos,FORCE);
	$oPerfil->set("Compras",$Compras,FORCE);
	$oPerfil->set("Stocks",$Stocks,FORCE);
	$oPerfil->set("Clientes",$Clientes,FORCE);
	$oPerfil->set("TPV",$TPV,FORCE);	
	$oPerfil->set("Proveedores",$Proveedores,FORCE);
		
	$oPerfil->set("VerStocks",$VerStocks,FORCE);		
	
	if ($oPerfil->Save() ){
		//if(isVerbose())
		//	echo gas("aviso",_("Perfil modificado"));	
		return true;
	} else {
		//echo gas("problema",_("No se puedo cambiar dato"));	
		return false;
	}	
}

function OperacionesConPerfiles(){
	if (!isUsuarioAdministradorWeb())
		return;
	
	echo gas("titulo",_("Operaciones sobre Perfiles"));
	echo "<table border=1>";
	echo "<tr><td>"._("Crear un nuevo perfil")."</td><td>".gModo("alta",_("Alta"))."</td></tr>";
	echo "</table>";
}

function FormularioAlta() {
	global $action;

	$oPerfil = new perfil;

	$oPerfil->Crea();
	
	echo $oPerfil->formEntrada($action,false);	
}

function CrearPerfil($nombre){
	$oPerfil = new perfil;

	$oPerfil->Crea();
	$oPerfil->setNombrePerfil($nombre);
	if ($oPerfil->Alta()){
		//if(isVerbose())
		//	echo gas("aviso",_("Nuevo perfil registrado"));	
		return true;
	} else {
		//echo gas("aviso",_("No se ha podido registrar el nuevo perfil"));
		return false;
	}
}

function PaginaBasica(){
	ListarPerfiles();	
	OperacionesConPerfiles();	
}

function BorrarPerfil($id){
	$oPerfil = new perfil;	
	
	if ($oPerfil->Load($id)) {		
		$nombre = $oPerfil->getNombre();
		//echo gas("Aviso",_("Perfil $nombre borrado"));
		
		$oPerfil->MarcarEliminado();		
		return true;
	}	else {
		//echo gas("Aviso",_("No se ha podido borrar el perfil"));	
		return false;
	}
}


PageStart();

//echo gas("cabecera",_("Gestion de Perfiles"));


switch($modo){
	case "borrar":
		$id = CleanID($_GET["id"]);
		BorrarPerfil($id);
		Separador();
		PaginaBasica();	
		break;	

	case "newsave":		
		$nombre = $_POST["NombrePerfil"];			
		CrearPerfil($nombre);
		Separador();
		PaginaBasica();	
		break;	
	case "alta":
		FormularioAlta();	
		break;
	case "modsave":
		$id 			= CleanID($_POST["id"]);
		$nombre 		= CleanTo($_POST["NombrePerfil"]," ");
		$Admin 			= checkPOST("Administracion");
		$Informe 		= checkPOST("InformeLocal");
		$Productos 		= checkPOST("Productos");
		$Compras 		= checkPOST("Compras");
		$Stocks 		= checkPOST("Stocks");
		$Clientes 		= checkPOST("Clientes");
		$TPV 			= checkPOST("TPV");
		$Proveedores 	= checkPOST("Proveedores");
		$VerStocks 		= checkPOST("VerStocks");
		ModificarPerfil(
			$id,$nombre,
			$Admin,$Informe,
			$Productos,$Compras,
			$Stocks,$Clientes,
			$TPV,$Proveedores,$VerStocks		
			);
		Separador();
		PaginaBasica();	
		break;
	case "editar":
		$id = CleanID($_GET["id"]);
		MostrarPerfilParaEdicion($id);
		break;
	default:
		PaginaBasica();
		break;		
}

PageEnd();

?>
