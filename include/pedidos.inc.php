<?php

function getIndexNocero($tarray){		
	foreach($tarray as $key=>$value){
			
		if (intval($key)>0)
			return $key;
	}		
}	

function CrearTraslado($idLocalDestino,$datosCompra) {
	$idOrden = "";
	//TODO: ¿Esta esto bien comentado?		
	//foreach ($compras as $id=>$unidades) {		//TODO: bug?
	foreach ($datosCompra as $id=>$unidades) {
		//$coste = getCosteDefectoProducto($id);				
		//$oPedido->AgnadirProducto($id,$unidades,$coste);
	}
		
	//$idOrden = $oPedido->Alta();
	
	return $idOrden;	
}

class traslado {
	var $_IdAlbaranTranspaso;
	var $_stockMover;
	var $_origen;
	var $_destino;
	var $userLog;	
	var $userLogCabecera;
	var $FechaPedido;
	var $FechaSalida;

	function OpenLog($titulo){		
		$comercio = $_SESSION["GlobalNombreNegocio"];	
		$this->userLogCabecera = "<center><u><b><font style='size: 14px'>".CleanParaWeb($comercio) . "</font></b></u></center><p>";
		$this->userLogCabecera .= "<table width='100%' style='border: 1px solid #999'><tr><td><b style='font-size: 110%;text-decoration:underline'>".
			 CleanParaWeb($titulo). "</B></td></tr>";
		$this->userLog = "";
	}
	function CloseLog(){
		$this->userLog .= "</table>";	
		$this->userLog = $this->userLogCabecera . $this->userLog;
	}
	
	function Log(){
		return $this->userLog;	
	}
	
	function LogAdd($html){
		$this->userLog .= $html;
	}
	
	function LogProducto($unid,$nombre,$referencia){
		$this->userLog .= "<tr><td>". $referencia . " - " . $nombre . "</td><td>". $unid . "</td></tr>"; 
	}
	
	function LogAddCabecera($textoCabecera){
		$this->userLogCabecera .= $textoCabecera;
	}
	
	function OperacionTraslado($destino) {
		
		$head = "";
		
		$this->OpenLog(_("ALBARÁN DE TRASPASO"));
		//Extraer el local origen de forma muy indirecta
		//TODO: mejorar este codigo
		$trans = getSesionDato("CarritoMover");		
		$id = getIndexNocero($trans);
		
		$id = CleanID($id);
		$sql = "SELECT IdLocal  FROM ges_almacenes WHERE Id = '$id'";
		$row = queryrow($sql);
		if (!$row)	return false;
		
		$origen = $row["IdLocal"];
		
		$local = new local;
		$local->Load($origen);
		$nombreorigen = CleanParaWeb($local->getNombre());
		
		$localdestino = new local;
		$localdestino->Load($destino);
		$nombredestino = CleanParaWeb($localdestino->getNombre());
				
		
		if (!$this->Crear($origen,$destino)) return false;
		$this->patronLog = "<td>";		
				
		foreach($trans as $id=>$unid){
			$this->AgnadirDetalles($id,$unid);			
		}		
		$fecha = CleanFechaFromDB($this->FechaPedido);


		$IdAlbaran = $this->_IdAlbaranTranspaso;
		
		$this->TrasladoBrutal();
		$this->LogAddCabecera("<tr><td>".g("b",CleanParaWeb(_("Nº Albarán:"))). " " . $IdAlbaran . "</td></tr>" );		
		$this->LogAddCabecera("<tr><td>".g("b",_("Fecha:")). " " . $fecha . "</td></tr>" );
		$this->LogAddCabecera("<tr><td>".g("b",_("Origen:")). " " . $nombreorigen .
			"</td><td>". g("b",_("Destino:")). " ". $nombredestino ."</td></tr>" );
			
								
		$this->LogAddCabecera("</table><p></p><table width='100%' style='border: 1px solid #999'><tr><td width='50%'>". 
				"<div style='border-bottom: 1px solid #999'>".g("b",CleanParaWeb(_("Producto")))."</div>". 
				 "</td><td>".
				"<div style='border-bottom: 1px solid #999'>".g("b",CleanParaWeb(_("Uds")))."</div>". 
				"</td></tr>");						
		$this->CloseLog();

		return true;
	}
	
	function Crear($IdAlmacenSalida,$IdAlmacenRecepcion) {
		global $UltimaInsercion;
		$IdAlmacenSalida = CleanID($IdAlmacenSalida);
		$IdAlmacenRecepcion = CleanID($IdAlmacenRecepcion);
		
		$this->_stockMover = array();
		
		$this->_origen = $IdAlmacenSalida;
		$this->_destino = $IdAlmacenRecepcion;		
		
		if (!$IdAlmacenSalida or !$IdAlmacenRecepcion)
			return false;		  	
		
		$sql = "INSERT INTO ges_albaranes_traspaso ( IdAlmacenSalida,IdAlmacenRecepcion, 
	  			FechaPedido,FechaSalida) VALUES ( '$IdAlmacenSalida','$IdAlmacenRecepcion',CURDATE(),CURDATE() )";
		$res = query($sql);
		if (!$res) return false;
		
		$id = $UltimaInsercion;
		$this->_IdAlbaranTranspaso =$id; 
		
		$row = queryrow("SELECT * FROM ges_albaranes_traspaso WHERE IdAlbaranTraspaso = '$id'");
		
		if ($row){
			$this->FechaPedido = $row["FechaPedido"];
			$this->FechaSalida = $row["FechaSalida"];
		}

		return 	$this->_IdAlbaranTranspaso;				
	}
	
	function AgnadirDetalles($Id,$unid){
		$this->_stockMover[$Id] = $this->_stockMover[$Id] + $unid;
	}
	
	function RegistrarDetalle($IdProducto,$Unidades){
		$IdProducto = CleanID($IdProducto);
		$Unidades = CleanInt($Unidades);
		$IdAlbaran = $this->_IdAlbaranTranspaso;
		
		$sql = "INSERT INTO ges_albtraspaso_det (IdAlbaranTraspaso,IdProducto,Unidades) 
				VALUES ('$IdAlbaran','$IdProducto','$Unidades')";
		return query($sql);			
	}	
	
	function TrasladoBrutal() {		
		$trans = $this->_stockMover; 
		
		foreach($trans as $id=>$unid){
			$unid = intval($unid);
			
			if($unid<1)
				continue;			
			
			$IdProducto = getIdProductoFromIdArticulo($id);
			
			error(__FILE__,"Info: '$IdProducto' desde '$id'");
			
			$origen = $this->_origen;//No es necesario, esta implicito al "id"
			$destino = $this->_destino;		
			
			$prod = new producto;
			
			$prod->Load($IdProducto);
			$nombre = CleanParaWeb($prod->getNombre());		
				
			$this->LogProducto($unid,$nombre,$prod->get("Referencia"));
				
			$this->RegistrarDetalle($IdProducto,$unid);
			
			$sql = "UPDATE ges_almacenes SET Unidades = Unidades - '$unid' WHERE Id = '$id'";
			if (query($sql)){
				$sql = "UPDATE ges_almacenes SET Unidades = Unidades + '$unid' WHERE IdProducto = '$IdProducto' AND IdLocal='$destino'";
				$res = query($sql);
				
			}
		}			
	}
}


function ResetearCarritoCompras(){
		setSesionDato("CompraProveedor",false);
		setSesionDato("PaginadorCompras",0);//Puede haber ahora muchos menos
		
		
		//Reseteamos carrito (no queremos mezclar productos de diferentes proveedores
		setSesionDato("CarritoCompras",false);
		setSesionDato("CarroCostesCompra",false);
		setSesionDato("PaginadorSeleccionCompras",0);	
		setSesionDato("PaginadorSeleccionCompras2",0);
}


function CrearOrdenTraslado($idLocalDestino,$datosCompra) {
		
	$oPedido = new pedido;
	
	$oPedido->Crea();
	
	$oPedido->set("IdAlmacenRecepcion",$idLocalDestino,FORCE);
	
	
	//foreach ($compras as $id=>$unidades) {		//TODO: bug?
	foreach ($datosCompra as $id=>$unidades) {
		$coste = getCosteDefectoProducto($id);				
		$oPedido->AgnadirProducto($id,$unidades,$coste);
	}
		
	$idOrden = $oPedido->Alta();
	
	return $idOrden;	
}


function CrearOrdenDeCompra($idLocal){

	$id = getSesionDato("DestinoAlmacen");
	
	//echo gas("Nota","Se ha enviado una orden de compra");
	//echo "Localid $id<br>";	
	
	$oPedido = new pedido;
	
	$oPedido->Crea();
	
	$oPedido->set("IdAlmacenRecepcion",$idLocal,FORCE);
	
	$compras = getSesionDato("CarritoCompras");
	$costes =  getSesionDato("CarroCostesCompra");
	
	foreach ($compras as $id=>$unidades) {		
		//TODO: el proveedor podria ser distinto del proveedor habitual
		// ..aqui asumimos que son iguales.				
		$idproveedor = getIdProveedorFromIdProducto($id);
		
		//Añade una fila de orden de compra				
		$oPedido->AgnadirProducto($id,$unidades,$costes[$id],$idproveedor);
	}
		
	$idOrden = $oPedido->Alta();
	
	return $idOrden;	
}

function EjecutaRecepcionarPedido($idPedido){
	$oPedido = new pedido;
	if (!$oPedido->Load($idPedido)){
		error(__FILE__ . __LINE__ , "E: no pudo recepcionar el pedido");
		return false;
	}
	 
	//echo "Recepcionando pedido '$idPedido'<br>";	
	$oPedido->RecepcionarPedido();
	//echo _("Se han agnadido los productos al almacen");	
}


function VaciarTrasladados($trans){
	foreach($trans as $id){		
		//echo "Anulando ..$id<br>";
		$sql = "UPDATE ges_almacenes SET Unidades = 0 WHERE Id = '$id'";
		query($sql);	
	}	
}





?>
