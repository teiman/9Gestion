<?php



function GenEtiqueta($id,$precio=0) {
	global $action;
	$ot = getTemplate("Etiqueta");
	
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template busqueda no encontrado");
		echo "1!";
		return false; }

	$oProducto = new producto;
	
	if (!$oProducto->Load($id)){
		error(__FILE__ . __LINE__ ,"Info: producto no encontrado");
		echo "2!";		
		return false; 		
	}
	
	$bar = $oProducto->getCB();
	$nombre = $oProducto->getNombre();

	$cr = "&";
		
	$cad = "barcode=" . $bar . $cr;
	$cad .= "format=gif" . $cr;
	//$cad .= "text=$bar" . $cr;
	//$cad .= "text=".urlencode($nombre ." - " .$oProducto->get("Referencia")) . $cr;
	$cad .= "width=".getParametro("AnchoBarras")  .$cr; 
	$cad .= "height=".getParametro("AltoBarras") . $cr;		

	$urlbarcode = "modulos/barcode/barcode.php?" . $cad;
	
	$ot->fijar("urlbarcode", $urlbarcode);
	$ot->fijar("precio",FormatMoney($precio));	
	$ot->fijar("talla",$oProducto->getTextTalla());
	$ot->fijar("color",$oProducto->getTextColor());
	$ot->fijar("referencia",$oProducto->get("Referencia"));
	$ot->fijar("nombre",$nombre);

	echo $ot->Output();					
}


function getCosteDefectoArticulo($id) {
	$id = CleanID($id);
	$sql = "SELECT IdProducto FROM ges_almacenes WHERE Id = '$id'  ";
	$row = queryrow($sql);	
	if (!$row) return false;
	$IdProducto = $row["IdProducto"];
	$sql = "SELECT CosteSinIVA FROM ges_productos WHERE IdProducto = '$IdProducto'"; 
	$row = queryrow($sql);
	if (!$row) return false;
	
	return $row["CosteSinIVA"]; 	
}



function AgnadirCarritoTraspaso($id,$u=1){
	
	if (!$id) {
		error(__FILE__.__LINE__,"no acepta nulo aqui");
		return;
	}
	
	$actual = getSesionDato("CarritoTrans");
	$mover = getSesionDato("CarritoMover");
	
	if (!in_array($id,$actual))
		array_push($actual,$id);
	
	$mover[$id] = $mover[$id] + $u;
		
	setSesionDato("CarritoMover",$mover);	
	setSesionDato("CarritoTrans",$actual);	
}

function QuitarDeCarritoTraspaso($id){
	
	if (!$id) {
		error(__FILE__.__LINE__,"no acepta nulo aqui");
		return;
	}
	
	$actual = getSesionDato("CarritoTrans");
	$mover = getSesionDato("CarritoMover");
	
	if (in_array($id,$actual)) {
	  $actual = my_array_delete_by_value($actual,$id);
	}
					
	unset($mover[$id]);	
		
	setSesionDato("CarritoMover",$mover);	
	setSesionDato("CarritoTrans",$actual);	
}

function GeneraNumDeTicket($IdLocal,$modoTicket){					
		switch($modoTicket){		
			case "interno":
				$serie = "IN";
				break;			
			case "cesion":		
				$serie = "CS";
				break;
			default: 			
				$serie = "B";
				break;
		}
										
		$miserie = $serie . $IdLocal;//Nos aseguramos de coger el valor correcto preguntando tambien por 		
			// ..la serie. Esto ayudara cuando un mismo local tenga mas de una serie, como va a ser el 
			// ..caso luego. 
		
		$sql = "SELECT Max(NFactura) as NFacturaMax FROM ges_facturas WHERE (IdLocal = '$IdLocal') AND (SerieFactura='$miserie')";
		$row = queryrow($sql,"Numero actual de factura");
	
		if ($row){
			$numSerieTicketLocalActual =  intval($row["NFacturaMax"]) + 1; 
		}	else {
			$numSerieTicketLocalActual = 0;
		}
		
		return $numSerieTicketLocalActual;	
}



class articulo extends Cursor {
	function SiguienteArticulo() {
		return $this->LoadNext();	
	}


	//Filtra repeticiones 
	function ListadoBase($IdLocal=false, $IdProducto=false,$indice=0,$tamPagina=10){
		
		$IdProducto = CleanID($IdProducto);
		$indice = intval($indice);
		
		$Idioma = getSesionDato("IdLenguajeDefecto");
		
		if ($IdLocal)				
			$restriccion_local = "ges_almacenes.IdLocal = '$IdLocal' AND ";
		
		if ($IdProducto)
			$and_producto = "AND ges_almacenes.IdProducto = '$IdProducto'";
		
	
		
		$sql ="SELECT 
			DISTINCT(ges_productos.IdProdBase),
			ges_almacenes.IdProducto, 
			ges_almacenes.Id,
			ges_almacenes.Unidades,
			ges_almacenes.PrecioVenta,
			ges_almacenes.TipoImpuesto,
			ges_almacenes.Impuesto,
			ges_almacenes.Oferta,
			ges_almacenes.StockMin,
			ges_productos.Referencia,
			ges_productos.CodigoBarras,			
			ges_productos_idioma.Nombre,
			ges_almacenes.Disponible,
			ges_locales.NombreComercial,
			ges_locales.Identificacion
			
			FROM ((ges_almacenes  INNER JOIN ges_locales ON ges_almacenes.IdLocal
			= ges_locales.IdLocal ) INNER JOIN ges_productos ON
			ges_almacenes.IdProducto = ges_productos.IdProducto $and_producto) INNER JOIN
			ges_productos_idioma ON ges_productos.IdProdBase = 
			ges_productos_idioma.IdProdBase
			
			WHERE
			$restriccion_local 
			ges_productos_idioma.IdIdioma = '$Idioma'
			AND ges_productos.Eliminado = 0
			GROUP BY ges_productos.IdProdBase";
		
		$res = $this->queryPagina($sql, $indice, $tamPagina+1);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
			return;		
		}								
	}	


	function Listado($IdLocal=false, $IdProducto=false,$indice=0,$tamPagina=10,$idbase=false){
		
		$IdProducto = CleanID($IdProducto);
		$indice = intval($indice);
		
		$Idioma = getSesionDato("IdLenguajeDefecto");
		
		$restriccion_local = "";
		if ($IdLocal)				
			$restriccion_local = "ges_almacenes.IdLocal = '$IdLocal' AND ";
		
		$and_producto = "";
		if ($IdProducto)
			$and_producto = "AND ges_almacenes.IdProducto = '$IdProducto'";
		
		$restriccion_base = "";
		if ($idbase)				
			$restriccion_base = "ges_productos.IdProdBase = '$idbase' AND";
		
		$sql ="SELECT ges_almacenes.IdProducto, 
			ges_almacenes.Id,
			ges_almacenes.Unidades,
			ges_almacenes.PrecioVenta,
			ges_almacenes.TipoImpuesto,
			ges_almacenes.Impuesto,
			ges_almacenes.Oferta,
			ges_almacenes.StockMin,
			ges_productos.Referencia,
			ges_productos.CodigoBarras,
			ges_productos_idioma.Nombre,
			ges_almacenes.Disponible,
			ges_locales.NombreComercial,
			ges_locales.Identificacion
			
			FROM ((ges_almacenes  INNER JOIN ges_locales ON ges_almacenes.IdLocal
			= ges_locales.IdLocal ) INNER JOIN ges_productos ON
			ges_almacenes.IdProducto = ges_productos.IdProducto $and_producto) INNER JOIN
			ges_productos_idioma ON ges_productos.IdProdBase = 
			ges_productos_idioma.IdProdBase
			
			WHERE
			$restriccion_local 
			$restriccion_base
			ges_productos_idioma.IdIdioma = '$Idioma'
			AND ges_productos.Eliminado = 0";
		
		$res = $this->queryPagina($sql, $indice, $tamPagina+1);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
			return;		
		}								
	}	
	

	function ListadoModular($IdLocal=false, $IdProducto=false,$indice=0,$tamPagina=10,$idbase=false,$nombre="",$soloLlenos =false,$obsoletos=false){
		
		$IdProducto = CleanID($IdProducto);
		$indice = intval($indice);
		
		$Idioma = getSesionDato("IdLenguajeDefecto");
		
		$restriccion_local = "";
		if ($IdLocal)				
			$restriccion_local = "ges_almacenes.IdLocal = '$IdLocal' AND ";
		
		$and_producto = "";
		if ($IdProducto)
			$and_producto = "AND ges_almacenes.IdProducto = '$IdProducto'";

		if ($nombre)
			$and_producto .= "AND ges_productos_idioma.Nombre LIKE '%$nombre%'";
		
		$restriccion_base = "";
		if ($idbase)				
			$restriccion_base = "ges_productos.IdProdBase = '$idbase' AND";
		
		if($soloLlenos)
			$restriccion_stock = "ges_almacenes.Unidades != 0 AND ";
	
		if (!$obsoletos) 
			$restriccion_obsoletos = " ges_productos.Obsoleto =0 AND ";
		
		
		$sql ="SELECT ges_almacenes.IdProducto,
			ges_productos.IdProdBase,  
			ges_almacenes.Id,
			ges_almacenes.Unidades,
			ges_almacenes.PrecioVenta,
			ges_almacenes.TipoImpuesto,
			ges_almacenes.Impuesto,
			ges_almacenes.Oferta,
			ges_almacenes.StockMin,
			ges_productos.Referencia,
			ges_productos.CodigoBarras,			
			ges_productos_idioma.Nombre,
			ges_almacenes.Disponible,
			ges_locales.NombreComercial,
			ges_locales.Identificacion,
			ges_productos.IdTalla,
			ges_productos.IdColor,
			ges_productos.IdFamilia,
			ges_productos.IdSubFamilia,		
			ges_almacenes.IdLocal
			FROM ((ges_almacenes  INNER JOIN ges_locales ON ges_almacenes.IdLocal
			= ges_locales.IdLocal ) INNER JOIN ges_productos ON
			ges_almacenes.IdProducto = ges_productos.IdProducto ) INNER JOIN
			ges_productos_idioma ON ges_productos.IdProdBase = 	ges_productos_idioma.IdProdBase			
			WHERE
			$restriccion_local 
			$restriccion_base
			$restriccion_stock
			$restriccion_obsoletos 			
			ges_productos_idioma.IdIdioma = '$Idioma'
			AND ges_productos.Eliminado = 0 $and_producto" .
			"ORDER BY ".
			" ges_productos_idioma.Nombre ASC, " .
			" ges_productos.IdProdBase ASC, " .			
			" ges_locales.NombreComercial ASC \n\n";
		
		$res = $this->queryPagina($sql, $indice, $tamPagina+1);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
			return;		
		}								
	}	
	
	
	function Load($idArticulo){
		$Idioma = getSesionDato("IdLenguajeDefecto");
				
		$sql ="SELECT ges_almacenes.IdProducto, ges_almacenes.Id,
			ges_almacenes.IdLocal,
			ges_almacenes.Unidades,
			ges_almacenes.PrecioVenta,
			ges_almacenes.TipoImpuesto,
			ges_almacenes.Impuesto,
			ges_almacenes.Oferta,
			ges_almacenes.StockMin,
			ges_almacenes.StockIlimitado,
			ges_almacenes.Disponible,
			ges_productos.Referencia,
			ges_productos.CodigoBarras,
			ges_productos_idioma.Nombre,
			ges_locales.NombreComercial
		
			
			FROM ((ges_almacenes  INNER JOIN ges_locales ON ges_almacenes.IdLocal
			= ges_locales.IdLocal ) INNER JOIN ges_productos ON
			ges_almacenes.IdProducto = ges_productos.IdProducto ) INNER JOIN
			ges_productos_idioma ON ges_productos.IdProdBase = 
			ges_productos_idioma.IdProdBase
			
			WHERE
			ges_almacenes.Id = '$idArticulo'
			AND ges_productos_idioma.IdIdioma = '$Idioma'
			AND ges_productos.Eliminado = 0";
		$row = queryrow($sql);
		if (!$row){
			$this->Error(__FILE__ . __LINE__,"E: no puedo cargar '$idArticulo'");
			return false;			
		}
		$this->setId($idArticulo);
		$this->import($row);
		return true;
	}

	function Iconos(){ //Genera los iconos relativos al estado del articulo
		$out = "";
		if ($this->is("Oferta")) {
			$out .= "S";	
		} else
			$out .= "$";
			
		if ($this->get("Unidades")<=$this->get("StockMin")){
			$out .= "#";
		} else
			$out .= "+";
		
		if (!$this->is("Disponible")){
			$out .= "x";
		}else {
			$out .= "V";
		}
		
		return $out;		
	}

}



class almacenes {
	var $seleccionAlmacenes;

	

	function getSeleccion(){
		return 	$this->seleccionAlmacenes;
	}

	function fijarSeleccion($array){
		$this->seleccionAlmacenes = $array;
	}
	
	function crearSeleccionProductosDesdeRes($res){	
		if (!$res)
			return false;
			
		$out = array();
		while($row = Row($res)){
			array_push($out, $row["Id"]);
		}
		return $out;
	}
	
	function crearSeleccionAlmacenesDesdeRes($res){	
		if (!$res)
			return false;
			
		$out = array();
		while($row = Row($res)){
			array_push($out, $row["IdLocal"]);
		}
		return $out;
	}

	
	function crearSeleccionAlmacenes($IdProducto, $condicionWHERE="1") {
		$sql = "SELECT DISTINCT IdLocal FROM ges_almacenes WHERE $condicionWHERE ORDER BY IdLocal ASC";
		
		$res = query($sql);
		if (!$res) {
			error (__FILE__ . __LINE__,"W: no se ha podido crear seleccion");
			return false;			
		}
		
		$out = $this->crearSeleccionAlmacenesDesdeRes($res);
		$this->seleccionAlmacenes = $out;	
		return $out;
	}

	function crearSeleccionProductos( $condicionWHERE = "1") {
		$sql = "SELECT Id FROM ges_almacenes WHERE $condicionWHERE ORDER BY IdLocal ASC";
		
		$res = query($sql);
		if (!$res) {
			error (__FILE__ . __LINE__,"W: no se ha podido crear seleccion");
			return false;			
		}
		
		$out = $this->crearSeleccionProductosDesdeRes($res);
		$this->seleccionAlmacenes = $out;	
		return $out;
	}

	function crearListaProductos( $condicionWHERE = "1") {
		$sql = "SELECT * FROM ges_almacenes WHERE $condicionWHERE ORDER BY IdLocal ASC";
		
		$res = query($sql);
		if (!$res) {
			error (__FILE__ . __LINE__,"W: no se ha podido crear seleccion");
			return false;			
		}
		
		return $res;
	}
	
	function MeterProducto($oProducto,$extradatos){
		
	}
	
	
	function listaTodos(){						
		$out = $this->crearSeleccionAlmacenes("1");						
		return $out;			
	}

	function listaTodosConNombre(){						
		$sql = "SELECT IdLocal,Identificacion FROM ges_locales  WHERE Eliminado=0 ORDER BY Identificacion ASC";
		
		$res = query($sql);
		if (!$res) {
			error (__FILE__ . __LINE__,"W: no se ha podido crear seleccion");
			return false;			
		}
		$out = array();
		
		while($row = Row($res)){
			$key = $row["IdLocal"];
			$value = $row["Identificacion"];
			$out[$key] = $value;			
		}				
								
		return $out;			
	}

	function AgnadeCantidad($id,$agnadir,$idlocal) {
		$idlocal 	= CleanID($idlocal);
		$id 		= CleanID($id);
		$agnadir 	= intval($agnadir);
		if (!$agnadir){
			return true;			
		}
		
		
		$sql 		= "UPDATE ges_almacenes SET Unidades = Unidades + $agnadir " .
				"WHERE (IdLocal = '$idlocal') AND (IdProducto = '$id') ";		
		$res 		= query($sql,"Aumentar unidades de articulo");
		if (!$res){			
			error(__FILE__ .  __LINE__ ,"E: no pudo agnadir cantidad");
			return false;
		}		
		return true;
	}
	
	function RebajaCantidad($IdProducto,$unidadesQuitadas,$IdLocal) {
		$IdLocal 	= CleanID($IdLocal);
		$IdProducto 		= CleanID($IdProducto);
		$unidadesQuitadas 	= intval($unidadesQuitadas);
		$sql 		= "UPDATE ges_almacenes SET Unidades = Unidades - $unidadesQuitadas " .
				"WHERE (IdLocal = '$IdLocal') AND (IdProducto = '$IdProducto') ";		
		$res 		= query($sql,"Disminuir unidades de articulo");
		if (!$res){			
			error(__FILE__ .  __LINE__ ,"E: no pudo disminuir cantidad");
			return false;
		}		
		return true;
	}
		
	function ModificaCantidad($IdProducto,$unidadesModificar,$IdLocal) {
		$IdLocal 			= CleanID($IdLocal);
		$IdProducto 		= CleanID($IdProducto);
		$unidadesModificar 	= intval($unidadesModificar);
				
		$sql 		= "UPDATE ges_almacenes SET Unidades = (Unidades + ($unidadesModificar)) " .
				"WHERE (IdLocal = '$IdLocal') AND (IdProducto = '$IdProducto') ";		
		$res 		= query($sql,"Modifica unidades de articulos");
		if (!$res){			
			error(__FILE__ .  __LINE__ ,"E: no pudo modificar cantidad");
			return false;
		}		
		return true;
	}



	function ApilaProducto($oProducto,$local,$unidades){
		//Comprobar que no estaba
		$id = $oProducto->getId();
		
		//echo "Existe con anterioridad?<br>";
		$num = ContarFilas ("Almacen","(IdProducto='$id') AND (IdLocal ='$local')");
		if ($num) {
			//error(__FILE__ . __LINE__ ,"E: ya fue apilado");
			return $this->AgnadeCantidad($id,$unidades,$local);					
		} 		
		
		//TODO: no hay que negar esto?
		$esInventario = intval(getParametro("Inventario"));   
 		$tipoimpuesto = getTipoImpuesto($oProducto,$local);
 		
 		//error(__FILE__ . __LINE__ ,"Infor: Precio aqui es ". $oProducto->getPrecioVenta());
		
 		$datos = array(
				"IdLocal"=>$local,
				"IdProducto"=>$oProducto->getId(),
				"PrecioVenta"=>$oProducto->getPrecioVenta(),
				"PrecioVentaOnline"=>$oProducto->getPrecioOnline(),
				"Unidades"=>$unidades,
				"StockMin"=>0,
				"StockMinOnline"=>0,	
				"TipoImpuesto"=>$tipoimpuesto,
				"Impuesto" => $oProducto->get("Impuesto"),
				"StockIlimitado"=>$esInventario,
				"Disponible"=>1,
				"Oferta"=>0,
				"OfertaOnline"=>0					
				);
				 				 		
 		$sql = CreaInsercion(false,$datos,"ges_almacenes"); 
		//"INSERT INTO dat_almacenes ($key) VALUES ($values)";
		
		query($sql,"Apilando producto en almacén");
			
	}
	
	function ApilaProductoTodos($oProducto,$unidades=0){
		
		$id = $oProducto->getId();
		
		//error(0,"Infodebug: id $id,".serialize($oProducto));		
		error(__FILE__ . __LINE__ ,"Infor: Precio aqui es ". $oProducto->getPrecioVenta());
		
		
		$listaTiendas = getSesionDato("ArrayTiendas");
		foreach ($listaTiendas as $tienda){
			$this->ApilaProducto($oProducto,$tienda,$unidades);
		}
	}

}



?>
