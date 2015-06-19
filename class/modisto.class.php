<?php

 
function getNombreModisto($idProv){
	$oProv = new Modisto;
	
	if (!$oProv->Load($idProv)){
		return "???";	
	}
	return $oProv->get("NombreComercial");	
}
 
// ListadoModistos
 
class Modisto extends Cursor {

	function Modisto() {
		return $this;
	}
	
	function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("ges_modistos", "IdModisto", $id);
		return $this->getResult();
	}
  	
  	function setNombre($nombre) {
  		$this->set("NombreComercial",$nombre,FORCE);	
  	}
  	
  	function Crea(){
		$this->setNombre(_("Nuevo modisto"));
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
			
			$listaKeys .= " $key";
			$listaValues .= " '$value'";
			$coma = true;															
		}
	
		$sql = "INSERT INTO ges_modistos ( $listaKeys ) VALUES ( $listaValues )";
		
		return query($sql);
						 	
	}	
	
	function Listado($lang,$min=0){
			
    	if (!$lang)
    		$lang = getSesionDato("IdLenguajeDefecto");
    
		$sql = "SELECT		
		ges_modistos.*		
		FROM
		ges_modistos 		
		WHERE
		ges_modistos.Eliminado = 0
		";
		
		$res = $this->queryPagina($sql, $min, 10);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
		}		
				
		return $res;
	}
	
	function SiguienteModisto() {
		$res = $this->LoadNext();
		if (!$res) {
			return false;
		}
		$this->setId($this->get("IdModisto"));		
		return true;			
	}
	
	function Modificacion () {
		
		$data = $this->export();				
		
		$sql = CreaUpdateSimple($data,"ges_modistos","IdModisto",$this->get("IdModisto"));
		
		$res = query($sql);
		if (!$res) {			
			$this->Error(__FILE__ . __LINE__ , "W: no actualizo Modisto");
			return false;
		}		
		return true;
	}
	
	
}




?>
