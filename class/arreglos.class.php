<?php



class job  extends Cursor {	
	var $dadoDeAlta;

    function job() {
    }

	function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("ges_modistos_tbjos ", "IdTbjoModisto ", $id);
		return $this->getResult();
	}
	function Crea(){
		$this->setEstado("Pdte Envio");			
	}

	function CreaDesdeArreglo($arreglo){
		$this->Crea();		
		$this->set("IdProducto",	$arreglo->idproducto,	FORCE);
		$this->set("NTicket",		$arreglo->nticket,	FORCE);
		$this->set("IdModisto",  	$arreglo->idmodisto,	FORCE);
		//"NombreProducto" - "Talla" - "Color"
		$this->set("DescripcionProducto",  	$arreglo->descripcion,	FORCE);
		
		if ( $this->Alta()) {
			$arreglo->AltaArreglo($this);	
		}		
	}

	function setEstado($modo){
		$this->set("Status",$modo,FORCE);
	}

	function esMio($IdModisto){
		return ($this->get("IdModisto")==$IdModisto);
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
	
		$sql = "INSERT INTO ges_modistos_tbjos ( $listaKeys ) VALUES ( $listaValues )";
		
		$res = query($sql,"Alta trabajo de modisto");
		
		if ($res) {		
			$id = $UltimaInsercion;	
			$this->setId($id);
			$this->set("IdTbjoModisto ",$id,FORCE);
			return $id;			
		}
						
		return false;				 		
	}

	function qModificacionEstado($estado=false,$idestado=false){		
		if (!$estado)	
			$estado = $this->get("Status");
		
		if (!$idestado)
			$idestado = $this->getId();
		else 
			$this->setId($idestado);		
		
		$this->setEstado($estado);
		
	//enum('Pdte Envio', 'Enviado', 'Recibido', 'Entregado')
	//	 FechaEnvio   	date  	   	No   	0000-00-00   	   	  Cambiar   	  Eliminar   	  Primaria   	  Indice   	  Unico   	 Fulltext
	// FechaRecepcion  	date
	 
		switch($estado){
			case "Enviado":
				$extra = ", FechaEnvio = NOW(), FechaRecepcion = '0000-00-00' ";
				break;
			case "Recogido":
			case "Recibido":
				$extra = ", FechaRecepcion = NOW() ";
				$estado = "Recibido";
				break;
			case "Pdte Envio":
				$extra = ", FechaEnvio = '0000-00-00', FechaRecepcion = '0000-00-00' ";
				break;    				
		}
		
		
		
		$sql = "UPDATE ges_modistos_tbjos SET Status = '$estado' $extra WHERE (IdTbjoModisto='$idestado')";
		query($sql);
	}

	function AgnadeConcepto($nuevoconcepto){
		$conactual = $this->get("Arreglos");
		if (!$conactual or $conactual == ""){
			$conactual = $nuevoconcepto;				
		}  else {
			$conactual = $conactual . " - ". $nuevoconcepto; 
		}
		
		$this->set("Arreglos",$conactual,FORCE);		
	}
	
	function SaveConceptoArreglo(){
		//$this->QuickSave("Arreglos",$this->get("Arreglos"));
		//$this->set($key,$value,FORCE);
		$arreglo = CleanRealMysql($this->get("Arreglos"));
		$id = $this->getId();
		$sql = "UPDATE ges_modistos_tbjos SET Arreglos = '$arreglo' WHERE (IdTbjoModisto= $id)";
		$res = query($sql);
		
		//$sql = "UPDATE ges_modistos_tbjo SET Arreglos=";		
	}
}
?>