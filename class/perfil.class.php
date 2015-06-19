<?php

function PerfilFactory($res) {
	if(!$res){
		error(__FILE__ . __LINE__ ,"ERROR en factory");
		return false;	
	}
	
	$row = Row($res);
	if (!is_array($row))
		return false;	
	$id = $row["IdPerfil"];
	
	$oPerfil = new perfil;
		
	if ($oPerfil->Load($id))
		return $oPerfil;
		
	error(__FILE__ . __LINE__ ,"ERROR no pudo cargar id '$id'");
		
	return false;
}


class perfil extends Cursor {
    function perfil() {
    	return $this;
    }
    
    function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("ges_perfiles_usuario", "IdPerfil", $id);
		return $this->getResult();
	}
    
    
    // SET especializados    
    function setNombrePerfil($nombre){    	
    	$this->set("NombrePerfil",$nombre,FORCE);	
    }
    
    
    
    // GET especializados
    function getNombre(){
    	return $this->get("NombrePerfil");	
    }
    
    function getPerfil(){
    	return $this->get("Perfil");
    }
	
	//Formulario de modificaciones y altas
	function formEntrada($action,$esModificar){
				
		$ot = getTemplate("AltaPerfil");
		if (!$ot){
			return false;
		}
		
		if ($esModificar){
			$modo = "modsave";
			$titulo = _("Modificando perfil");	
		} else {
			$modo = "newsave";
			$titulo = _("Nuevo perfil");			
		}
						
		$cambios = array(	
			"TITULO" => $titulo,	
			"VALUENOMBRE" => $this->getNombre(),
			"TEXTNOMBRE" => _("Nombre perfil"),
			"HIDDENDATA" => Hidden("id",$this->getId()),
			"ACTION" => "$action?modo=$modo",
			"CADMINISTRACION" => gCheck($this->is("Administracion")),
			"CINFORMELOCAL" => gCheck($this->is("InformeLocal")),			
			"CINFORMES" => gCheck($this->is("Informes")),
			"CPRODUCTOS" => gCheck($this->is("Productos")),
			"CPROVEEDORES" => gCheck($this->is("Proveedores")),
			"CSTOCKS" => gCheck($this->is("Stocks")),
			"CCOMPRAS" => gCheck($this->is("Compras")),
			"CCLIENTES" => gCheck($this->is("Clientes")),
			"CTPV" => gCheck($this->is("TPV")),
			"CVERSTOCKS" => gCheck($this->is("VerStocks")),
			"TADMINISTRACION" => _("Administración"),
			"TINFORMELOCAL" => _("Informe local"),			
			"TINFORMES" =>  _("Informes"),
			"TPRODUCTOS" => _("Productos"),
			"TPROVEEDORES" => _("Proveedores"),
			"TSTOCKS" => _("Stocks"),
			"TCOMPRAS" => _("Compras"),
			"TCLIENTES" => _("Clientes"),
			"TTPV" => _("TPV"),	
			"TVERSTOCKS" => _("Ver stocks")
		);

		return $ot->makear($cambios);									
	}
	
	
	function Crea(){
		$this->setNombrePerfil(_("Nuevo perfil"));
	}
	
	function Alta(){
	
		$data = $this->export();
		
		$coma = false;
		
		$listaKeys = "";
		$listaValues = "";
		
		foreach ($data as $key=>$value){
			if ($coma) {
				$listaKeys .= ", ";
				$listaValues .= ", ";
			}
			
			$listaKeys .= " " . $key;
			$listaValues .= " '".$value."'";
			$coma = true;															
		}
	
		$sql = "INSERT INTO ges_perfiles_usuario ( $listaKeys ) VALUES ( $listaValues )";
		
		return query($sql);
						 	
	}
		
	function setNombre($nombre){
		$this->set("NombrePerfil",$nombre,FORCE);		
	}	
		
}


?>
