<?php
include ("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

$tamPagina = 10;

function ListarModistos() {
	//Creamos template
	global $action, $tamPagina;

	$ot = getTemplate("ListadoModistos");
	if (!$ot) {
		error(__FILE__.__LINE__, "Info: template no encontrado");
		return false;
	}

	$marcado = getSesionDato("CarritoProv");

	//echo "ser: " . serialize($marcado). "<br>";

	$oModisto = new Modisto;

	$indice = getSesionDato("PaginadorModistos");

	$hayModistos = $oModisto->Listado(false, $indice);
    $ot->fijar("tAviso", _("¿Esta seguro de que quiere eliminarlo?"));

	if (!$hayModistos) {
		echo gas("aviso", "No hay Modisto disponibles");
	} else {
		$ot->fijar("tTitulo", _("Lista de modisto"));
		$ot->fijar("action", $action);
		$ot->resetSeries(array ("IdModisto", "Referencia", "Nombre", "tBorrar", "tEditar", "tSeleccion", "marca"
			,"vNombreComercial"));
		$num = 0;
		while ($oModisto->SiguienteModisto()) {
			$num ++;
			$id = $oModisto->getId();
			$ot->fijarSerie("IdModisto", $id);
			$ot->fijarSerie("tBorrar", _("Eliminar"));
			$ot->fijarSerie("tEditar", _("Modificar"));
			$ot->fijarSerie("tNombreComercial",_("Nombre comercial"));
			$ot->fijarSerie("vNombreComercial",$oModisto->get("NombreComercial"));
			if (in_array($id, $marcado)) {
				$ot->fijarSerie("marca", "<abbr title='Seleccion' style='color:red'>S</abbr>");
				$ot->fijarSerie("tSeleccion", "");
				$ot->eliminaSeccion("s$num");
			} else {
				$ot->fijarSerie("marca", "");
				$ot->fijarSerie("tSeleccion", _("Selección"));
			}
		}

		$ot->paginador($indice, false, $num);

		$ot->terminaSerie(false);
		echo $ot->Output();
	}
}

function MostrarModistoParaEdicion($id, $lang) {
	global $action;

	$oModisto = new Modisto;
	if (!$oModisto->Load($id, $lang)) {
		error(__FILE__.__LINE__, "W: no pudo mostrareditar '$id'");
		return false;
	}

	$ot = getTemplate("ModificarModisto");
	if (!$ot) {
		error(__FILE__.__LINE__, "Info: template no encontrado");
		return false;
	}
	$ot->fijar("action", $action);
	$ot->fijar("vIdModisto", $id);
	
	$ot->fijar("tModPagoHabitual", _("Modo pago hab."));
	$ot->fijar("vIdModPagoHabitual", $oModisto->get("IdModPagoHabitual"));	
	$ot->fijar("comboModPagoHabitual", genComboModPagoHabitual( $oModisto->get("IdModPagoHabitual")));
	
	$ot->campo(_("Pagina web"), "PaginaWeb", $oModisto);
	$ot->fijar("comboIdPais" ,genComboPaises($oModisto->get("IdPais")));
			
	$ot->fijar("tIdPais", _("País"));			
	$ot->fijar("tTitulo", _("Modificando modisto"));
	$ot->campo(_("Nombre comercial"), "NombreComercial", $oModisto);
	$ot->campo(_("Nombre legal"), "NombreLegal", $oModisto);
	$ot->campo(_("Dirección"), "Direccion", $oModisto);
	$ot->campo(_("Localidad"), "Localidad", $oModisto);
	$ot->campo(_("Código postal"), "CP", $oModisto);
	$ot->campo(_("Telf.(1)"), "Telefono1", $oModisto);
	$ot->campo(_("Telf.(2)"), "Telefono2", $oModisto);
	$ot->campo(_("Contacto"), "Contacto", $oModisto);
	$ot->campo(_("Cargo"), "Cargo", $oModisto);
	$ot->campo(_("Email"), "Email", $oModisto);
	$ot->campo(_("Cuenta bancaria"), "CuentaBancaria", $oModisto);
	$ot->campo(_("Número fiscal"), "NumeroFiscal", $oModisto);
	$ot->campo(_("Comentarios"), "Comentarios", $oModisto);	
	

	echo $ot->Output();
}



function OperacionesConModistos() {
	if (!isUsuarioAdministradorWeb())
		return;
		
	echo gas("titulo", _("Operaciones sobre Modistos"));
	echo "<table border=1>";
	echo "<tr><td>"._("Crear un nuevo modisto")."</td><td>".gModo("alta", _("Alta"))."</td></tr>";
	echo "<tr><td style='color:red'>Debug: vaciar Modistos</td><td>".gModo("vaciarbasededatos", _("Eliminar todo"))."</td></tr>";
	echo "</table>";
}

function FormularioAlta($esPopup=false) {
	global $action;

	$oModisto = new Modisto;

	$oModisto->Crea();

	$ot = getTemplate("FormAltaModisto");
	if (!$ot) {
		error(__FILE__.__LINE__, "Info: template no encontrado");
		return false;
	}
	$ot->fijar("action", $action);
	$ot->fijar("tTitulo", _("Alta modisto"));	
	
	$ot->fijar("tModPagoHabitual", _("Modo pago hab."));
	$ot->fijar("vIdModPagoHabitual", $oModisto->get("IdModPagoHabitual"));	
	$ot->fijar("comboModPagoHabitual", genComboModPagoHabitual( $oModisto->get("IdModPagoHabitual")));
	
	$ot->campo(_("Pagina web"), "PaginaWeb", $oModisto);
	
	$ot->fijar("tIdPais", _("País"));
	$ot->fijar("comboIdPais" ,genComboPaises($oModisto->get("IdPais")));
		
	$ot->campo(_("Nombre comercial"), "NombreComercial", $oModisto);
	$ot->campo(_("Nombre legal"), "NombreLegal", $oModisto);
	$ot->campo(_("Dirección"), "Direccion", $oModisto);
	$ot->campo(_("Localidad"), "Localidad", $oModisto);
	$ot->campo(_("Código postal"), "CP", $oModisto);
	$ot->campo(_("Telf.(1)"), "Telefono1", $oModisto);
	$ot->campo(_("Telf.(2)"), "Telefono2", $oModisto);
	$ot->campo(_("Contacto"), "Contacto", $oModisto);
	$ot->campo(_("Cargo"), "Cargo", $oModisto);
	$ot->campo(_("Email"), "Email", $oModisto);
	$ot->campo(_("Cuenta bancaria"), "CuentaBancaria", $oModisto);
	$ot->campo(_("Número fiscal"), "NumeroFiscal", $oModisto);
	$ot->campo(_("Comentarios"), "Comentarios", $oModisto);
	
	if ($esPopup) {
		$ot->fijar("vesPopup", 1);
		$ot->fijar("onClose", "window.close()");
	} else {
		$ot->fijar("vesPopup", 0);
		$ot->fijar("onClose", "location.href='modmodistos.php'");	
	}

	echo $ot->Output();

}

function PaginaBasica() {
	//	AccionesSeleccion();
	ListarModistos();
	OperacionesConModistos();
}

function BorrarModisto($id) {
	$oModisto = new Modisto;

	if ($oModisto->Load($id)) {
		//$nombre = $oModisto->get("Nombre");
		echo gas("Aviso", _("Modisto  borrado"));
		$oModisto->MarcarEliminado();
	} else {
		echo gas("Aviso", _("No se ha podido borrar el modisto"));
	}
}

function AgnadirCarritoModistos($id) {
	$actual = getSesionDato("CarritoProv");
	if (!is_array($actual)) {
		$actual = array ();
	}

	if (!in_array($id, $actual))
		array_push($actual, $id);

	$_SESSION["CarritoProv"] = $actual;
}

function ListarOpcionesSeleccion() {
	echo gas("titulo", _("Operaciones sobre la selección"));
	echo "<table border=1>";
	echo "<tr><td>"._("Hacer una compra a modistos")."</td><td>".gModo("comprar", _("Comprar"))."</td></tr>";
	echo "<tr><td>"._("Buscar en el almacén")."</td><td>".gModo("transsel", _("Buscar"))."</td></tr>";
	//echo "<tr><td>"._("Cambio global de precio")."</td><td>".gModo("preciochange",_("Precios"))."</td></tr>";
	echo "</table>";
}

function ConvertirSelModistos2Articulos() {
}


function FormularioDeCambiodePrecio() {
}
function ModistoEnAlmacen($id) {
}

function VaciarDatosModistosyAlmacen() {
	query("DELETE FROM ges_Modistos");
}

function CrearModisto($comercial, $legal, $direccion, $poblacion,
	 $cp, $email, $telefono1, $telefono2, $contacto, $cargo, $cuentabancaria, $numero,
	 	 $comentario,$IdModPagoHabitual,$paginaweb,$idpais) {

	$oModisto = new Modisto;
	$oModisto->Crea();


	$oModisto->set("IdPais", $idpais, FORCE);
	$oModisto->set("PaginaWeb", $paginaweb, FORCE);

	$oModisto->set("NombreComercial", $comercial, FORCE);
	$oModisto->set("NombreLegal", $legal, FORCE);
	$oModisto->set("Direccion", $direccion, FORCE);
	$oModisto->set("Localidad", $poblacion, FORCE);
	$oModisto->set("CP", $cp, FORCE);
	$oModisto->set("Email", $email, FORCE);
	$oModisto->set("Telefono1", $telefono1, FORCE);
	$oModisto->set("Telefono2", $telefono2, FORCE);
	$oModisto->set("Contacto", $contacto, FORCE);
	$oModisto->set("Cargo", $cargo, FORCE);	
	$oModisto->set("CuentaBancaria", $cuentabancaria, FORCE);
	$oModisto->set("NumeroFiscal", $numero, FORCE);
	$oModisto->set("Comentarios", $comentario, FORCE);

	
	$oModisto->set("IdModPagoHabitual", $IdModPagoHabitual, FORCE);

	if ($oModisto->Alta()) {
		if(isVerbose())
			echo gas("aviso", _("Nuevo modisto registrado"));
		return true;
	} else {
		if (isVerbose())
			echo gas("aviso", _("No se ha podido registrar el nuevo producto"));
		return false;
	}

}

function ModificarModisto($id,$comercial, $legal, $direccion, $poblacion, $cp, $email, $telefono1, 
	$telefono2, $contacto, $cargo, $cuentabancaria, $numero, $comentario,	$IdModPagoHabitual,$paginaweb,$idpais	){
	$oModisto = new Modisto;
	if (!$oModisto->Load($id)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
	
	$oModisto->set("IdPais", $idpais, FORCE);
	$oModisto->set("PaginaWeb", $paginaweb, FORCE);
	
	$oModisto->set("NombreComercial", $comercial, FORCE);
	$oModisto->set("NombreLegal", $legal, FORCE);
	$oModisto->set("Direccion", $direccion, FORCE);
	$oModisto->set("Localidad", $poblacion, FORCE);
	$oModisto->set("CP", $cp, FORCE);
	$oModisto->set("Email", $email, FORCE);
	$oModisto->set("Telefono1", $telefono1, FORCE);
	$oModisto->set("Telefono2", $telefono2, FORCE);
	$oModisto->set("Contacto", $contacto, FORCE);
	$oModisto->set("Cargo", $cargo, FORCE);	
	$oModisto->set("CuentaBancaria", $cuentabancaria, FORCE);
	$oModisto->set("NumeroFiscal", $numero, FORCE);
	$oModisto->set("Comentarios", $comentario, FORCE);
	
	if($IdModPagoHabitual)
		$oModisto->set("IdModPagoHabitual", $IdModPagoHabitual, FORCE);

	
	if ($oModisto->Modificacion() ){
		if(isVerbose())
			echo gas("aviso",_("Modisto modificado"));	
	} else {
		echo gas("problema",_("No se puede cambiar datos de [$comercial]"));	
	}	
}


PageStart();

//echo gas("cabecera", _("Gestion de Modistos"));

switch ($modo) {
	case "borrar":

		$Id = CleanID($_GET["Id"]);
		BorrarModisto($Id);
		PaginaBasica();
		break;
	case "modmodisto":
		$id = CleanID($_POST["IdModisto"]);
		$comercial = $_POST["NombreComercial"];
		$legal = $_POST["NombreLegal"];
		$direccion = $_POST["Direccion"];
		$poblacion = $_POST["Localidad"];
		$cp = CleanCP($_POST["CP"]);
		$email = CleanEmail($_POST["Email"]);
		$telefono1 = CleanTelefono($_POST["Telefono1"]);
		$telefono2 = CleanTelefono($_POST["Telefono2"]);
		$contacto = $_POST["Contacto"];
		$cargo = $_POST["Cargo"];
		$cuentabancaria = $_POST["CuentaBancaria"];
		$numero = $_POST["NumeroFiscal"];
		$comentario = $_POST["Comentarios"];
		$IdModPagoHabitual = CleanID($_POST["IdModPagoHabitual"]);
		$paginaweb = CleanUrl($_POST["PaginaWeb"]);
		$idpais = CleanID($_POST["IdPais"]);
		
		ModificarModisto($id,$comercial, $legal, $direccion, $poblacion, $cp, $email,
			 $telefono1, $telefono2, $contacto, $cargo, $cuentabancaria, $numero, $comentario,
			 	$IdModPagoHabitual,$paginaweb,$idpais);
		//Separador();
		PaginaBasica();
		break;
	case "editar":	
		$id = CleanID($_GET["Id"]);
		MostrarModistoParaEdicion($id);		
		break;
	case "newmodisto" :
		$comercial = $_POST["NombreComercial"];
		$legal = $_POST["NombreLegal"];
		$direccion = $_POST["Direccion"];
		$poblacion = $_POST["Localidad"];
		$cp = $_POST["CP"];
		$email = $_POST["Email"];
		$telefono1 = $_POST["Telefono1"];
		$telefono2 = $_POST["Telefono2"];
		$contacto = $_POST["Contacto"];
		$cargo = $_POST["Cargo"];
		$cuentabancaria = $_POST["CuentaBancaria"];
		$numero = $_POST["NumeroFiscal"];
		$comentario = $_POST["Comentarios"];
		$IdModPagoHabitual = CleanID($_POST["IdModPagoHabitual"]);
		$paginaweb = $_POST["PaginaWeb"];
		$idpais = $_POST["IdPais"];		
		$espopup = $_POST["esPopup"];
				
		if (CrearModisto($comercial, $legal, $direccion, $poblacion, $cp, $email, $telefono1, $telefono2, $contacto, $cargo, $cuentabancaria, $numero, $comentario,$IdModPagoHabitual,$paginaweb,$idpais )) {
			if ($espopup){
				echo "<script>window.close()</script>";
				exit();				
			}
			//Separador();
			PaginaBasica();						
		} else {										
			FormularioAlta($espopup);
		}		
		break;
	case "altapopup":
	    $esPopup = true;
	case "alta" :
		FormularioAlta($esPopup);
		break;
	case "listar" :
		PaginaBasica();
		break;
	case "pagmenos":
		$indice = getSesionDato("PaginadorModistos");
		$indice = $indice - $tamPagina;
		if ($indice<0)
			$indice = 0;
		setSesionDato("PaginadorModistos",$indice);
		PaginaBasica();
		break;	
	case "pagmas":
		$indice = getSesionDato("PaginadorModistos");
		$indice = $indice + $tamPagina;
		setSesionDato("PaginadorModistos",$indice);
		PaginaBasica();
		break;			
	default :
		PaginaBasica();
		break;
}

PageEnd();
?>