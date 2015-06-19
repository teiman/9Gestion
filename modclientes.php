<?php
include ("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

$tamPagina = 10;

function ListarClientes() {
	//Creamos template
	global $action, $tamPagina;

	$ot = getTemplate("ListadoClientes");
	if (!$ot) {
		error(__FILE__.__LINE__, "Info: template no encontrado");
		return false;
	}

	$marcado = getSesionDato("CarritoCliente");

	//echo "ser: " . serialize($marcado). "<br>";

	$oCliente = new cliente;

	$indice = getSesionDato("PaginadorCliente");

	$hayClientes = $oCliente->Listado(false, $indice);

	if (!$hayClientes) {
		echo gas("aviso", "No hay cliente disponibles");
	} else {
		$ot->fijar("tTitulo", _("Lista de cliente"));
		$ot->fijar("action", $action);
		$ot->fijar("tBorrar", _("Eliminar"));
		$ot->fijar("tEditar", _("Modificar"));
		$ot->fijar("tCliente", _("Cliente"));
		$ot->fijar("tAviso", _("¿Quieren borrar este cliente?") );
		$ot->resetSeries(array ("IdCliente", "Referencia", "Nombre", "tSeleccion", "marca"
			,"vNombreComercial"));
		$num = 0;
		while ($oCliente->SiguienteCliente()) {
			$num ++;
			$id = $oCliente->getId();
			$ot->fijarSerie("IdCliente", $id);			
			$ot->fijarSerie("tNombreComercial",_("Nombre comercial"));
			$ot->fijarSerie("vNombreComercial",$oCliente->get("NombreComercial"));
			$ot->fijarSerie("vNombreLocal",getNombreLocalId($oCliente->get("IdLocal")));
		}

		$ot->paginador($indice, false, $num);

		$ot->terminaSerie();
		echo $ot->Output();
	}
}

function MostrarClienteParaEdicion($id, $lang) {
	global $action;

	$oCliente = new cliente;
	if (!$oCliente->Load($id, $lang)) {
		error(__FILE__.__LINE__, "W: no pudo mostrareditar '$id'");
		return false;
	}
	
	if ($oCliente->esEmpresa())
		$ot = getTemplate("ModificarCliente");
	else
		$ot = getTemplate("ModificarClienteParticular");
		
	if (!$ot) {
		error(__FILE__.__LINE__, "Info: template no encontrado");
		return false;
	}
	$ot->fijar("action", $action);
	$ot->fijar("vIdCliente", $id);
	$ot->fijar("tTitulo", _("Modificando cliente"));
	
	if ($oCliente->esEmpresa())
		$ot->campo(_("Nombre comercial"), "NombreComercial", $oCliente);
	else
		$ot->campo(_("Nombre"), "NombreComercial", $oCliente);
		
	
	$ot->fijar("tModPagoHabitual", _("Modo pago hab."));
	$ot->fijar("vIdModPagoHabitual", $oCliente->get("IdModPagoHabitual"));	
	$ot->fijar("comboModPagoHabitual", genComboModPagoHabitual( $oCliente->get("IdModPagoHabitual")));

	$ot->fijar("tIdLocal",_("Tienda"));
	$ot->fijar("comboIdLocal", genComboAlmacenes( $oCliente->get("IdLocal")));
	

	$ot->fijar("tIdPais",_("País"));
	$ot->fijar("vIdPais", $oCliente->get("IdPais"));
	$ot->fijar("comboIdPais",genComboPaises($oCliente->get("IdPais")));
								
	$ot->campo(_("Pagina web"), "PaginaWeb", $oCliente);

	
	$ot->campo(_("Nombre legal"), "NombreLegal", $oCliente);
	$ot->campo(_("Dirección"), "Direccion", $oCliente);
	$ot->campo(_("Localidad"), "Localidad", $oCliente);
	$ot->campo(_("Código postal"), "CP", $oCliente);
	$ot->campo(_("Telf.(1)"), "Telefono1", $oCliente);
	$ot->campo(_("Telf.(2)"), "Telefono2", $oCliente);
	$ot->campo(_("Contacto"), "Contacto", $oCliente);
	$ot->campo(_("Cargo"), "Cargo", $oCliente);
	$ot->campo(_("Email"), "Email", $oCliente);
	$ot->campo(_("Cuenta bancaria"), "CuentaBancaria", $oCliente);
	$ot->campo(_("Número fiscal"), "NumeroFiscal", $oCliente);
	$ot->campo(_("Comentarios"), "Comentarios", $oCliente);	
	
	//$ot->campo(_("Fecha nac."), "FechaNacim", $oCliente);
	
	echo $ot->Output();
}

function OperacionesConClientes() {
	if (!isUsuarioAdministradorWeb())
		return;
	
	echo gas("titulo", _("Operaciones sobre Clientes"));
	echo "<table border=1>";
	echo "<tr><td>"._("Crear un nuevo cliente")."</td><td>".gModo("alta", _("Alta"))."</td></tr>";
	echo "<tr><td style='color:red'>Debug: vaciar clientes</td><td>".gModo("vaciarbasededatos", _("Eliminar todo"))."</td></tr>";
	echo "</table>";
}

function FormularioAlta() {
	global $action;

	$oCliente = new cliente;

	$oCliente->Crea();

	$ot = getTemplate("FormAltaCliente");
	if (!$ot) {
		error(__FILE__.__LINE__, "Info: template no encontrado");
		return false;
	}
	$ot->fijar("tTitulo", _("Alta cliente"));
	$ot->fijar("action", $action);
		
	$ot->fijar("tModPagoHabitual", _("Modo pago hab."));
	$ot->fijar("vIdModPagoHabitual", $oCliente->get("IdModPagoHabitual"));	
	$ot->fijar("comboModPagoHabitual", genComboModPagoHabitual( $oCliente->get("IdModPagoHabitual")));

	$ot->fijar("tIdPais",_("País"));
	$ot->fijar("vIdPais", $oCliente->get("IdPais"));
	$ot->fijar("comboIdPais",genComboPaises($oCliente->get("IdPais")));
		
	$ot->campo(_("Nombre comercial"), "NombreComercial", $oCliente);
	$ot->campo(_("Nombre legal"), "NombreLegal", $oCliente);
	$ot->campo(_("Dirección"), "Direccion", $oCliente);
	$ot->campo(_("Localidad"), "Localidad", $oCliente);
	$ot->campo(_("Código postal"), "CP", $oCliente);
	$ot->campo(_("Telf.(1)"), "Telefono1", $oCliente);
	$ot->campo(_("Telf.(2)"), "Telefono2", $oCliente);
	$ot->campo(_("Contacto"), "Contacto", $oCliente);
	$ot->campo(_("Cargo"), "Cargo", $oCliente);
	$ot->campo(_("Email"), "Email", $oCliente);
	$ot->campo(_("Cuenta bancaria"), "CuentaBancaria", $oCliente);
	$ot->campo(_("Número fiscal"), "NumeroFiscal", $oCliente);
	$ot->campo(_("Comentarios"), "Comentarios", $oCliente);

	echo $ot->Output();

}

function FormularioAltaParticular() {
	global $action;

	$oCliente = new cliente;

	$oCliente->Crea();

	$ot = getTemplate("FormAltaClienteParticular");
	if (!$ot) {
		error(__FILE__.__LINE__, "Info: template no encontrado");
		return false;
	}
	$ot->fijar("tTitulo", _("Alta cliente"));
	$ot->fijar("action", $action);
	
	$ot->fijar("tModPagoHabitual", _("Modo pago hab."));
	$ot->fijar("vIdModPagoHabitual", $oCliente->get("IdModPagoHabitual"));	
	$ot->fijar("comboModPagoHabitual", genComboModPagoHabitual( $oCliente->get("IdModPagoHabitual")));

	$ot->fijar("tIdPais",_("País"));
	$ot->fijar("vIdPais", $oCliente->get("IdPais"));
	$ot->fijar("comboIdPais",genComboPaises($oCliente->get("IdPais")));	
	
	$ot->campo(_("Nombre"), "NombreComercial", $oCliente);
	$ot->campo(_("Nombre legal"), "NombreLegal", $oCliente);
	$ot->campo(_("Dirección"), "Direccion", $oCliente);
	$ot->campo(_("Localidad"), "Localidad", $oCliente);
	$ot->campo(_("Código postal"), "CP", $oCliente);
	$ot->campo(_("Telf.(1)"), "Telefono1", $oCliente);
	$ot->campo(_("Telf.(2)"), "Telefono2", $oCliente);
	$ot->campo(_("Contacto"), "Contacto", $oCliente);
	$ot->campo(_("Cargo"), "Cargo", $oCliente);
	$ot->campo(_("Email"), "Email", $oCliente);
	$ot->campo(_("Cuenta bancaria"), "CuentaBancaria", $oCliente);
	$ot->campo(_("Número fiscal"), "NumeroFiscal", $oCliente);
	$ot->campo(_("Comentarios"), "Comentarios", $oCliente);
 	//modalidad de pago habitual?
	echo $ot->Output();

}

function PaginaBasica() {
	//	AccionesSeleccion();
	ListarClientes();
	OperacionesConClientes();
}



function AgnadirCarritoClientes($id) {
	$actual = getSesionDato("CarritoCliente");
	if (!is_array($actual)) {
		$actual = array ();
	}

	if (!in_array($id, $actual))
		array_push($actual, $id);

	$_SESSION["CarritoCliente"] = $actual;
}

function ListarOpcionesSeleccion() {
	echo gas("titulo", _("Operaciones sobre la selección"));
	echo "<table border=1>";
	echo "<tr><td>"._("Hacer una compra a clientes")."</td><td>".gModo("comprar", _("Comprar"))."</td></tr>";
	echo "<tr><td>"._("Buscar en el almacén")."</td><td>".gModo("transsel", _("Buscar"))."</td></tr>";
	//echo "<tr><td>"._("Cambio global de precio")."</td><td>".gModo("preciochange",_("Precios"))."</td></tr>";
	echo "</table>";
}

function VaciarDatosClientesyAlmacen() {
	query("DELETE FROM ges_clientes");
}


function ModificarCliente($parametros,$IdLocal2=false) {
	
	list( $id,$comercial, $legal, $direccion, $poblacion, $cp, $email, 
	$telefono1, $telefono2, $contacto, $cargo, $cuentabancaria,
	 $numero, $comentario,$IdModPagoHabitual,$idpais,$paginaweb,$prueba,$nace,$IdLocal) = $parametros;

	$oCliente = new cliente;
	
	
	if (!$oCliente->Load($id)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
			
	
	$oCliente->set("NombreComercial", $comercial, FORCE);
	$oCliente->set("NombreLegal", $legal, FORCE);
	$oCliente->set("Direccion", $direccion, FORCE);
	$oCliente->set("Localidad", $poblacion, FORCE);
	$oCliente->set("CP", $cp, FORCE);
	$oCliente->set("Email", $email, FORCE);
	$oCliente->set("Telefono1", $telefono1, FORCE);
	$oCliente->set("Telefono2", $telefono2, FORCE);
	$oCliente->set("Contacto", $contacto, FORCE);
	$oCliente->set("Cargo", $cargo, FORCE);	
	$oCliente->set("CuentaBancaria", $cuentabancaria, FORCE);
	$oCliente->set("NumeroFiscal", $numero, FORCE);
	$oCliente->set("Comentarios", $comentario, FORCE);
	$oCliente->set("IdPais", $idpais, FORCE);
	$oCliente->set("PaginaWeb", $paginaweb, FORCE);
	//$oCliente->set("FechaNacim", $nace, FORCE);
	$oCliente->set("IdLocal", $IdLocal2, FORCE);

	//BUG: por alguna razon no importa bien "IdLocal"
	// asi que usamos IdLocal2 que ha sido importado como parametro
	//echo "<h1>IdLocal:$IdLocal,IdLocal2:$IdLocal2</h1>";

	if ($oCliente->Modificacion() ){
		if(isVerbose())
			echo gas("aviso",_("Cliente modificado"));	
	} else {
		echo gas("problema",_("No se puede cambiar datos de [$referencia]"));	
	}	
}

function BorrarCliente($id){
	$oCliente = new cliente;	
	
	if ($oCliente->Load($id)) {		
		if(isVerbose())
			echo gas("aviso",_("Cliente borrado"));
		
		$oCliente->MarcarEliminado();	
		//invalidarSesion("ListaTiendas");	
	}	else {
		echo gas("aviso",_("No se ha podido borrar el cliente"));	
	}
}

PageStart();

//echo gas("cabecera", _("Gestion de Clientes"));

switch ($modo) {
	case "modcliente":
		$id 		= CleanID($_POST["IdCliente"]);
		$comercial 	= $_POST["NombreComercial"];
		$legal 		= $_POST["NombreLegal"];
		$direccion	= $_POST["Direccion"];
		$poblacion 	= $_POST["Localidad"];
		$cp 		= CleanCP($_POST["CP"]);
		$email 		= CleanEmail($_POST["Email"]);
		$telefono1 	= CleanTelefono($_POST["Telefono1"]);
		$telefono2 	= CleanTelefono($_POST["Telefono2"]);
		$contacto 	= $_POST["Contacto"];
		$cargo 		= $_POST["Cargo"];
		$cuentabancaria = CleanCC($_POST["CuentaBancaria"]);
		$numero 	= $_POST["NumeroFiscal"];
		$comentario = $_POST["Comentarios"];
		$IdModPagoHabitual = CleanID($_POST["IdModPagoHabitual"]);
		$idpais 	= CleanID($_POST["IdPais"]); 
		$paginaweb  = $_POST["PaginaWeb"];
		$nace  		= $_POST["FechaNacim"];	
		$IdLocal	= CleanID($_POST["IdLocal"]);			
			
		$parametros = array($id,$comercial, $legal, $direccion, $poblacion, $cp, $email,
			 $telefono1, $telefono2, $contacto, $cargo, $cuentabancaria, $numero, 
			 $comentario,$IdModPagoHabitual,$idpais,$paginaweb,$nace,$IdLocal);
			  
		ModificarCliente($parametros,$IdLocal);
		Separador();
		PaginaBasica();
		break;
	case "editar":	
		$id = $_GET["Id"];
		MostrarClienteParaEdicion($id);		
		break;
	case "newcliente" :
		$comercial 	= $_POST["NombreComercial"];
		$legal 		= $_POST["NombreLegal"];
		$direccion	= $_POST["Direccion"];
		$poblacion 	= $_POST["Localidad"];
		$cp		   	= CleanCP($_POST["CP"]);
		$email 		= CleanEmail($_POST["Email"]);
		$telefono1 	= CleanTelefono($_POST["Telefono1"]);
		$telefono2 	= CleanTelefono($_POST["Telefono2"]);
		$contacto 	= $_POST["Contacto"];
		$cargo 		= $_POST["Cargo"];
		$cuentabancaria = CleanCC($_POST["CuentaBancaria"]);
		$numero 	= $_POST["NumeroFiscal"];
		$comentario = $_POST["Comentarios"];
		$tipocliente = $_POST["TipoCliente"];
		$IdModPagoHabitual = CleanID($_POST["IdModPagoHabitual"]);
		$idpais 	= CleanID($_POST["IdPais"]); 
		$paginaweb  = $_POST["PaginaWeb"];
		$nace  		= $_POST["FechaNacim"];
				
		CrearCliente($comercial, $legal, $direccion, $poblacion, $cp, $email, 
			$telefono1, $telefono2, $contacto, $cargo, 			
			$cuentabancaria, $numero, $comentario,$tipocliente,$IdModPagoHabitual,$idpais,$paginaweb,$nace);
		Separador();
		PaginaBasica();
		break;
	case "alta" :
		FormularioAlta();
		break;
	case "altaparticular" :
		FormularioAltaParticular();
		break;		
	case "listar" :
		PaginaBasica();
		break;
	case "pagmenos":
		$indice = getSesionDato("PaginadorCliente");
		$indice = $indice - $tamPagina;
		if ($indice<0)
			$indice = 0;
		setSesionDato("PaginadorCliente",$indice);
		PaginaBasica();
		break;	
	case "pagmas":
		$indice = getSesionDato("PaginadorCliente");
		$indice = $indice + $tamPagina;
		setSesionDato("PaginadorCliente",$indice);
		PaginaBasica();
		break;			
	case "borrar":
		$id = CleanID($_GET["Id"]);
		BorrarCliente($id);
		Separador();
		PaginaBasica();			
		break;				
	default :
		PaginaBasica();
		break;
}

PageEnd();
?>