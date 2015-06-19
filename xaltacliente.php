<?php
 
include("tool.php"); 
 
SimpleAutentificacionAutomatica("novisual-services"); 
 
$modo = $_REQUEST["modo"]; 
 
switch($modo) { 
	case "modificarcliente":
	

		$idcliente = CleanID($_POST["IdCliente"]);
		
		$comercial = $_POST["NombreComercial"];
		$poblacion = $_POST["Localidad"];
		$direccion = $_POST["Direccion"];
		$cp = CleanCP($_POST["CP"]);
		$email = CleanEmail($_POST["Email"]);
		$telefono1 = CleanTelefono($_POST["Telefono1"]);
		$telefono2 = CleanTelefono($_POST["Telefono2"]);
		$contacto = $_POST["Contacto"];
		$cargo = $_POST["Cargo"];
		$cuentabancaria = $_POST["CuentaBancaria"];
		$numero = $_POST["NumeroFiscal"];
		$comentario = $_POST["Comentarios"];
		$tipocliente = $_POST["TipoCliente"];
		$IdModPagoHabitual = CleanID($_POST["IdModPagoHabitual"]);
		$idpais 	= CleanID($_POST["IdPais"]); 
		$paginaweb  = $_POST["PaginaWeb"];
		$nace  = $_POST["FechaNacim"];
		
		$oCliente = new cliente;
		if(!$oCliente->Load($idcliente)) {
			echo 0;
			exit();	
		}
		
		$oCliente->setIfData("NombreComercial", $comercial, FORCE);
		$oCliente->setIfData("Direccion", $direccion, FORCE);
		$oCliente->setIfData("Localidad", $poblacion, FORCE);
		$oCliente->setIfData("CP", $cp, FORCE);
		$oCliente->setIfData("Email", $email, FORCE);
		$oCliente->setIfData("Telefono1", $telefono1, FORCE);
		$oCliente->setIfData("Telefono2", $telefono2, FORCE);
		$oCliente->setIfData("Contacto", $contacto, FORCE);
		$oCliente->setIfData("Cargo", $cargo, FORCE);	
		$oCliente->setIfData("CuentaBancaria", $cuentabancaria, FORCE);
		$oCliente->setIfData("NumeroFiscal", $numero, FORCE);
		$oCliente->setIfData("Comentarios", $comentario, FORCE);
		$oCliente->setIfData("TipoCliente", $tipocliente, FORCE);
		$oCliente->setIfData("IdPais", $idpais, FORCE);
		$oCliente->setIfData("PaginaWeb", $paginaweb, FORCE);
		//$oCliente->setIfData("IdLocal", CleanID(getSesionDato("IdTienda")), FORCE);
		
		if( $oCliente->Save()){
			echo $idcliente;
		} else {
			echo 0;
		}
		
		break;	
		
	case "altarapida":
		$comercial = $_POST["NombreComercial"];
		$poblacion = $_POST["Localidad"];
		$direccion = $_POST["Direccion"];
		$cp = CleanCP($_POST["CP"]);
		$email = $_POST["Email"];
		$telefono1 = CleanTelefono($_POST["Telefono1"]);
		$telefono2 = CleanTelefono($_POST["Telefono2"]);
		$contacto = $_POST["Contacto"];
		$cargo = $_POST["Cargo"];
		$cuentabancaria = $_POST["CuentaBancaria"];
		$numero = $_POST["NumeroFiscal"];
		$comentario = $_POST["Comentarios"];
		$tipocliente = $_POST["TipoCliente"];
		$IdModPagoHabitual = CleanID($_POST["IdModPagoHabitual"]);
		$idpais 	= CleanID($_POST["IdPais"]); 
		$paginaweb  = $_POST["PaginaWeb"];
		$nace  = $_POST["FechaNacim"];
				
		$IdLocal =  CleanID(getSesionDato("IdTienda"));
				
 		$id = CrearCliente($comercial, $legal, $direccion, $poblacion, $cp, $email, 
		$telefono1, $telefono2, $contacto, $cargo, $cuentabancaria, $numero, 
		$comentario,$tipocliente,$idpais,$paginaweb,$nace,$IdLocal);				
		
		if ($id)		
			echo "$id";
		else
			echo "0";
		exit();			
	break;
	
} 
	
?>
