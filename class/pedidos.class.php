<?php

define("PEDIDO_PETICION",1);
define("PEDIDO_PEDIDO",2);
define("PEDIDO_RECIBIDO",3);

class pedido extends Cursor {
	
	var $filas;
	var $filascoste;
	var $filasproveedor;
	
	var $IdPedido;
	
	function pedido(){
		return $this;	
	}
	
	function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("ges_pedidos", "IdPedido", $id);
		return $this->getResult();
	}
			
	function AgnadirProducto($id,$cantidad,$coste,$idproveedor) {
		$this->filas[$id] = $cantidad;		
		$this->filascoste[$id] = $coste;
		$this->filasproveedor[$id] = $idproveedor;
	}
	
	function Crea(){
		$this->IdPedido = false;
		$this->filas = array();
		$this->filascoste = array();		
		$this->filasproveedor 	= array();
		$this->set("Status",PEDIDO_PETICION,FORCE);		
	}
	
	function Alta(){
		global $UltimaInsercion;
		
		$data = $this->export();
		
		$evitar = array("FechaPeticion");
		$coma = false;		
		$listaKeys = "";
		$listaValues = "";
				
		foreach ($data as $key=>$value){
			if (!in_array($key,$evitar)){
				if ($coma) {
					$listaKeys .= ", ";
					$listaValues .= ", ";
				}
				
				$listaKeys .= " $key";
				$value_s = CleanRealMysql($value);
				$listaValues .= " '$value_s'";
				$coma = true;	
			}														
		}
		
		$listaKeys 		.= ", FechaPeticion";
		$listaValues 	.= ", NOW()";		
	
		$this->IdPedido = false;
		$sql = "INSERT INTO ges_pedidos ( $listaKeys ) VALUES ( $listaValues )";		
		$res = query($sql);
		$this->IdPedido = $UltimaInsercion;
		 
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ , "E: no pudo guardar pedido");
			return false;
		}
		
		foreach ($this->filas as $id=>$unidades) {
			$costeunidad = $this->filascoste[$id];
			$idproveedor = $this->filasproveedor[$id];
			$this->AltaFilaPedido($id,$unidades,$this->IdPedido,$costeunidad,$idproveedor);	
		}
		
		return $this->IdPedido;								 	
	}
		
	function AltaFilaPedido($id,$unidades,$IdPedido,$costeunidad,$idproveedor){
		$IdPedido 		= CleanID($IdPedido);
		$id 			= CleanID($id);
		$unidades 		= intval($unidades);
		$costeunidad 	= intval($costeunidad*100)/100;
		$sql = "INSERT INTO ges_compras (IdPedido,IdProducto,Unidades,PrecioUnidad,IdProveedor) VALUES ('$IdPedido','$id','$unidades','$costeunidad','$idproveedor')";
		$res = query($sql,"Alta fila pedido");
		 
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ , "E: no pudo guardar pedido");
			return;
		}			
		
		//Actualizamos costesiniva de producto
		$sql = "UPDATE ges_productos SET CosteSinIVA ='$costeunidad' WHERE (IdProducto = '$id')";				 
		query($sql,"Actualizando el coste");		
	}	
	
	function RecepcionarPedido($IdLocal=false) {
		
		if (!$IdLocal) {			
			$IdLocal = $this->get("IdAlmacenRecepcion");
		}		
		
		$id = $this->getId();
		
		$sql = "SELECT * FROM ges_compras WHERE (IdPedido = '$id')";
		$res = query($sql);
		if(!$res){
			$this->Error(__FILE__ . __LINE__ , "E: no puedo recepcionar");
			return false;	
		}
		$almacen = new almacenes;
		$oProducto = new producto;
		while($row= Row($res)) {			
			$oProducto->Load($row["IdProducto"]);
			$almacen->ApilaProducto($oProducto,$IdLocal,$row["Unidades"]);			
		} 
		
		//echo "Pedido recepcionado completo, actualizado estado.<br>";
		$sql = "UPDATE ges_pedidos SET Status= '" . PEDIDO_RECIBIDO."',  FechaRecepcion=NOW() WHERE (IdPedido='$id')";
		query($sql);
		
	}
	
}





?>
