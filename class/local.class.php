<?php


function getNombreLocalId($id){
	$id = CleanID($id);
	
	$keyname = "tLOCAL_".$id;
	
	if (isset($_SESSION[$keyname]) and $_SESSION[$keyname]) return $_SESSION[$keyname];	
	
	$sql = "SELECT Identificacion FROM ges_locales WHERE IdLocal = '$id'";
	
	$row = queryrow($sql);
	
	if (!$row) return false;
	
	$nombre = $row["Identificacion"];
	$_SESSION[$keyname] = $nombre;
	return $nombre; 	
}


function LocalFactory($res) {
	if(!$res){
		error(__FILE__ . __LINE__ ,"ERROR en factory");
		return false;	
	}
	
	$row = Row($res);
	if (!is_array($row))
		return false;	
	$id = $row["IdLocal"];
	
	$oLocal = new local;
		
	if ($oLocal->Load($id))
		return $oLocal;
		
	error(__FILE__ . __LINE__ ,"ERROR no pudo cargar id '$id'");
		
	return false;
}


class local extends Cursor {
    function local() {
    	return $this;
    }
    
    function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("ges_locales", "IdLocal", $id);
		return $this->getResult();
	}
    

	function LoadCentral() {		
		$sql = "SELECT IdLocal FROM ges_locales WHERE AlmacenCentral = 1";

		$row = queryrow($sql);
		
		if (!is_array($row)){
			return false;	
		}
		
		return $this->Load($row["IdLocal"]);			
	}

    
    // SET especializados    
    function setNombreComercial($nombre){    	
    	$this->set("NombreComercial",$nombre,FORCE);	
    }
    
    
    
    // GET especializados
    function getNombre(){
    	return $this->get("NombreComercial");	
    }
    
    function getPerfil(){
    	return $this->get("Perfil");
    }
	
	//Formulario de modificaciones y altas
	function old_formEntrada($action,$esModificar){
		if($esModificar)
			$out = gas("titulo",_("Modificando local"));
		else
			$out = gas("titulo",_("Nuevo local"));
		
		$out .= "<table><tr>
		<td>Nombre comercial</td><td>" . Input("NombreComercial",$this->getNombre()) . "</td><tr>".
		"<tr><td></td><td>". Enviar(_("Guardar")) . "</td></tr>".
		"</table>";							
		
		$modo = "newsave";
		if ($esModificar) {
			$modo ="modsave";
			$extra = Hidden("id",$this->getId());
		}
		
		return "<form action='$action?modo=$modo' method=post>$out $extra</form>";					
	}
	
	
		//Formulario de modificaciones y altas
	function formEntrada($action,$esModificar){
				
		if ($esModificar)
			$ot = getTemplate("ModLocal");
		else
			$ot = getTemplate("AltaLocal");
			
		if (!$ot){
			return false;
		}
		
		if ($esModificar){
			$modo = "modsave";
			$titulo = _("Modificando local");	
		} else {
			$modo = "newsave";
			$titulo = _("Nuevo local");			
		}
					
		$combonumeracion = getComboNumeracionFacturas($this->get("IdTipoNumeracionFactura"));
			
		if ($this->is("ImpuestoIncluido"))
			$incluido = "checked";
			
		$comboidiomas = genComboIdiomas($this->get("IdIdioma"));
			
		$cambios = array(
			"tMensajeMes" => _("Mensaje ticket"),
			"vMensajeMes" => $this->get("MensajeMes"),
			"tIdIdioma" => _("Idioma"),
			"comboIdiomas" =>$comboidiomas,				
			"tIdPais" => _("País"),
			"vIdPais" => $this->get("IdPais"),
			"comboIdPais" => genComboPaises($this->get("IdPais")),
			"TITULO" => $titulo,
			"tImpuestoIncluido"=> _("Impuesto incluido"),
			"cImpuestoIncluido"=> $incluido,
			"tTipoNumeracionFactura" => _("Tipo numeración fact."),
			"comboTipoNumeracionFactura" => $combonumeracion,
			"vNombreComercial" => $this->get("NombreComercial"),
			"vNombreLegal" => $this->get("NombreLegal"),
			"vPoblacion" => $this->get("Poblacion"),
			"vCodigoPostal" => $this->get("CodigoPostal"),
			"vDireccionFactura" => $this->get("DireccionFactura"),
			"vFax" => $this->get("Fax"),
			"vEmail" => $this->get("Email"),
			"vMovil" => $this->get("Movil"),
			"vTelefono" => $this->get("Telefono"),
			"vPaginaWeb" => $this->get("PaginaWeb"),
			"vCuentaBancaria" => $this->get("CuentaBancaria"),
			"Password" =>_("Contraseña"),			
			"vPassword" => $this->get("Password"),
			"Ver" => _("Ver"),
			"Identificacion" => _("Identificación"),
			"vIdentificacion" => $this->get("Identificacion"),
			"NombreComercial" => _("Nombre comercial"),
			"NombreLegal" => _("Nombre legal"),
			"Poblacion" => _("Población"),
			"CodigoPostal" => _("CP"),
			"DireccionFactura" => _("Dirección factura"),
			"Fax" => _("Fax"),
			"Email" => _("Email"),
			"Movil" => _("Móvil"),
			"Telefono" => _("Teléfono"),
			"PaginaWeb" => _("Pagina web"),
			"CuentaBancaria" => _("Cuenta bancaria"),
			"tAlmacenCentral" => _("El almacén central"),
			"HIDDENDATA" => Hidden("id",$this->getId()),
			"ACTION" => "$action?modo=$modo",

		);

		return $ot->makear($cambios);									
	}
	
	
	function Crea(){
		$this->set("NombreComercial",_("Nuevo local"),FORCE);		
		$this->set("Identificacion",genMakePass(),FORCE);
		$this->set("Password",genMakePass(),FORCE);					
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
	
		$sql = "INSERT INTO ges_locales ( $listaKeys ) VALUES ( $listaValues )";
		
		$res = query($sql,'Alta de local en locales');				
		$IdLocalCreado = $UltimaInsercion; 
		
		$this->set("IdLocal",$IdLocalCreado,FORCE);
		
		
		$sql = "SELECT IdLocal FROM ges_locales WHERE Eliminado=0 ORDER BY IdLocal ASC";
		$row = queryrow($sql);		
		$IdLocalUsable = $row["IdLocal"]; //Vamos a clonar los productos desde este almacen		
		//TODO: salir con error si no hay ningun local. Siempre deberia haber al menos un local,
		// el añadido durante el proceso de instalación.		
		
		
		if ($IdLocalCreado){			
			
			$sql = "SELECT * FROM ges_almacenes WHERE (IdLocal='$IdLocalUsable')";			
			$res = query($sql);			
			while( $row = Row($res) ){			
				$IdProducto = $row["IdProducto"];
				$PrecioVenta = $row["PrecioVenta"];
				$Descuento = $row["Descuento"];
				$TipoImpuesto = $row["TipoImpuesto"];
				$Impuesto = $row["Impuesto"];
				$Disponible = 1;//??
				$Oferta = $row["Oferta"];
				$Eliminado = $row["Eliminado"];
				$Unidades = 0;//Empieza el almacen vacio
				$StockMin = 0;//$row["StockMin"];
				$StockMinOnline = 0;//$row["StockMinOnline"];
				$StockIlimitado = 0;//$row["StockIlimitado"];
				$DisponibleOnline = 0;//$row["DisponibleOnline"];
				$Oferta = 0;//$row["Oferta"];
				$OfertaOnline = 0;//$row["OfertaOnline"];
				
			  $newsql = "INSERT INTO `ges_almacenes` ( 
				`IdLocal` , `IdProducto` , `Unidades` , `StockMin` , `StockMinOnline` , 
			`PrecioVenta` , `Descuento` , `PrecioVentaOnline` , `DescuentoOnline` ,
			 `TipoImpuesto` , `Impuesto` , `StockIlimitado` , `Disponible` , 
			`DisponibleOnline` , `Eliminado` , `Oferta` , `OfertaOnline` 
				)VALUES (  	
				$IdLocalCreado , '$IdProducto' , '$Unidades' , '$StockMin' , '$StockMinOnline'
			 , '$PrecioVenta' , '$Descuento' , '$PrecioVentaOnline' , '$DescuentoOnline' ,
			  '$TipoImpuesto' , '$Impuesto' , '$StockIlimitado' , '$Disponible' , '$DisponibleOnline' ,
			  '$Eliminado' , '$Oferta' , '$OfertaOnline'
			    )";
				
			  query($newsql);				
			}
		}
		
		return true;		
						 	
	}

	function IniciarArqueos(){
		
		$IdLocal = $this->get("IdLocal");
		
		$sql = "INSERT INTO `ges_arqueo_caja` (IdLocal,esCerrada ) VALUES ('$IdLocal',1);";
		query($sql,'Iniciando arqueos');	
	}

		
	function Modificacion(){
		return $this->Save();		
	}

}


?>