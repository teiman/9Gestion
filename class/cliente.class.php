<?php

function ClienteFactory($res) {
	if(!$res){
		error(__FILE__ . __LINE__ ,"ERROR en factory");
		return false;	
	}
	
	$row = Row($res);
	if (!is_array($row))
		return false;	
	$id = $row["IdCliente"];
	
	$oCliente = new cliente;
		
	if ($oCliente->Load($id))
		return $oCliente;
		
	error(__FILE__ . __LINE__ ,"ERROR no pudo cargar id '$id'");
		
	return false;
}



function CrearCliente($comercial, $legal, $direccion, $poblacion, $cp, $email, 
	$telefono1, $telefono2, $contacto, $cargo, $cuentabancaria, $numero, 
	$comentario,$tipocliente,$idpais,$paginaweb,$nace,$IdLocal=false) {

	$oCliente = new cliente;
	$oCliente->Crea();

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
	$oCliente->set("TipoCliente", $tipocliente, FORCE);
	$oCliente->set("IdPais", $idpais, FORCE);
	$oCliente->set("PaginaWeb", $paginaweb, FORCE);
	if ($IdLocal)
		$oCliente->set("IdLocal", $IdLocal, FORCE);
	
	if ($oCliente->Alta()) {
		//if(isVerbose())		
		//	echo gas("aviso", _("Nuevo cliente registrado"));
		return $oCliente->get("IdCliente");
	} else {
		//echo gas("aviso", _("No se ha podido registrar el nuevo producto"));
		return false;
	}

}


class cliente extends Cursor {
    function cliente() {
    	return $this;
    }
    
    function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("ges_clientes", "IdCliente", $id);
		return $this->getResult();
	}
    
    
    // SET especializados    
    function setNombre($nombre){    	
    	$this->set("NombreComercial",$nombre,FORCE);	
    }
    
    function esEmpresa() {    
    	return $this->get("TipoCliente")=="Empresa";	    	
    }
    
    
    // GET especializados
    function getNombre(){
    	return $this->get("NombreComercial");	
    }
    
    function getCliente(){
    	return $this->get("NombreComercial");
    }
	
	//Formulario de modificaciones y altas
	function formEntrada($action,$esModificar){
		$ot = getTemplate("ModificarCliente");
		if (!$ot){		return false;		}
								
		$comboidiomas = genComboIdiomas($this->get("IdIdioma"));
		$comboperfiles = genComboPerfiles($this->get("IdPerfil"));
									
		$cambios = array(	
			"tIdPais" => _("País"),
			"vIdPais" => $this->get("IdPais"),
			"comboIdPais" => genComboPaises($this->get("IdPais")),
			"TITULO" => _("Modificando cliente"),	
			"Direccion" => _("Dirección"),
			"Comision" => _("Comisión"),
			"Ver" => _("Ver"),
			"Telefono" => _("Teléfono"),
			"Nombre" => _("Nombre"),
			"Idioma" => _("Idioma"),
			"comboIdiomas" => $comboidiomas,
			"Perfil" => _("Perfil"),
			"comboPerfiles" => $comboperfiles,	
			"vNombre" => $this->getNombre(),
			"vDireccion"=>$this->get("Direccion"),
			"vComision"=>$this->get("Comision"),
			"vTelefono"=>$this->get("Telefono"),		
			"ACTION" => "$action?modo=modsave",
			"HIDDENDATA" => Hidden("id",$this->getId())
		);

		return $ot->makear($cambios);		
				
	}
	
	function formAlta($action){
		$ot = getTemplate("AltaCliente");
		if (!$ot){		return false;		}
		
		$comboidiomas = genComboIdiomas();
		$comboperfiles = genComboPerfiles();
		
		$cambios = array(
			"tIdPais" => _("País"),
			"vIdPais" => $this->get("IdPais"),
			"comboIdPais" => genComboPaises($this->get("IdPais")),	
			"TITULO" => _("Alta cliente"),
			"Ver" => _("Ver"),	
			"Direccion" => _("Dirección"),
			"Poblacion" => _("Población"),
			"Comision" => _("Comisión"),
			"Telefono" => _("Teléfono"),
			"Nombre" => _("Nombre"),
			"Idioma" => _("Idioma"),
			"Ver" => _("Ver"),
			"comboIdiomas" => $comboidiomas,
			"Perfil" => _("Perfil"),
			"comboPerfiles" => $comboperfiles,			
			"vNombre" => $this->getNombre(),
			"TEXTNOMBRE" => _("Nombre perfil"),
			"ACTION" => "$action?modo=newsave",
		);

		return $ot->makear($cambios);
	}
	
	function Crea(){
		$this->setNombre(_("Nuevo cliente"));
		//$this->set("FechaNacim","1974-09-01",FORCE);
	}
	
	function Alta(){
		global $UltimaInsercion;
		$data = $this->export();
		
		$coma = false;
		$listaKeys = "";
		$listaValues = "";
				
		foreach ($data as $key=>$value){
			if ($coma) {
				$listaKeys .= ", ";
				$listaValues .= ", ";
			}
			
			$listaKeys .= " $key";
			$listaValues .= " '$value'";
			$coma = true;															
		}
	
		$sql = "INSERT INTO ges_clientes ( $listaKeys ) VALUES ( $listaValues )";
		
		$res = query($sql,"Alta cliente");
		
		if ($res) {		
			$id = $UltimaInsercion;	
			$this->set("IdCliente",$id,FORCE);
			return $id;			
		}
						
		return false;				 		
	}

	function Listado($lang,$min=0){
			
    	if (!$lang)
    		$lang = getSesionDato("IdLenguajeDefecto");
    
		$sql = "SELECT		
		ges_clientes.*		
		FROM
		ges_clientes 		
		WHERE
		ges_clientes.Eliminado = 0
		";
		
		$res = $this->queryPagina($sql, $min, 10);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
		}		
				
		return $res;
	}
	
	function SiguienteCliente() {
		$res = $this->LoadNext();
		if (!$res) {
			return false;
		}
		$this->setId($this->get("IdCliente"));		
		return true;			
	}	
		
	function Modificacion () {
		
		$data = $this->export();				
		
		$sql = CreaUpdateSimple($data,"ges_clientes","IdCliente",$this->get("IdCliente"));
		
		$res = query($sql,'Modificamos un cliente');
		if (!$res) {			
			$this->Error(__FILE__ . __LINE__ , "W: no actualizo proveedor");
			return false;
		}		
		return true;
	}
}
