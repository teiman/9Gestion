<?php


class filaTicket {
	var $unidades;
	var $precio;
	var $descuento;
	var $codigo;
	var $impuesto;
	var $idproducto;
	var $importe;
	var $concepto;
	var $talla;
	var $color;
	var $referencia;
	var $codigobarras;
	var $_esArreglo;
	var $idfactura;
	var $idmodisto;
	var $nticket;
	var $CB;
	
	function esArreglo(){
		return $this->_esArreglo;	
	}
	
	function DeduceIdProducto($codigo){ //deduca el idproducto desde 
		list($mod,$CB,$serial,$idmodisto) =split("\.",$codigo);
		error(__FILE__,"DeduceIdProducto: $mod,$CB,$serial,$idmodisto");
		$this->CB = $CB;
		$this->codigojob	= "$CB.$idmodisto";
		$this->idproducto = getIdFromCodigoBarras($CB);	
	}
	
	/*
	 * Setter para los datos de la fila.
	 */	
	function Set($codigo, $unidades, $precio, $descuento, $impuesto,$importe,
		$concepto,$talla,$color,$referencia,$cb,$idmodisto,$nombre){
			
		$this->unidades 	= $unidades;
		$this->precio 		= $precio;
		$this->descuento 	= $descuento;
		$this->codigo 		= $codigo;//puede contener el codigo de arreglo
		$this->CB			= $codigo;//Codigo real		
		$this->impuesto 	= $impuesto * 100;//guardado en %
		$this->importe		= $importe;
		$this->concepto 	= $concepto;
		$this->talla 		= $talla;
		$this->color		= $color;
		$this->referencia	= $referencia;
		$this->codigobarras = $cb;
		$this->idmodisto	= $idmodisto;
		$this->nombre		= $nombre;
		$this->codigojob	= "$codigo.$idmodisto";
		
		$_nombre = str_replace("-"," ",$nombre); 
		$_talla = str_replace("-"," ",$talla);
		$_color = str_replace("-"," ",$color);
		 
		$this->descripcion = "$_nombre - $_talla - $_color";
						
		if ($idmodisto>0){
			$this->_esArreglo = true;
			$this->DeduceIdProducto($codigo);
			//$this->talla = "";
			//$this->color = "";			
		}	else {
			$this->_esArreglo = false;
			$this->idproducto = getIdFromCodigoBarras($codigo);			
		}			
	}
	
	function AltaArreglo($job){
		/*IdArreglo   	int(11)  	   	No   	0   	   	  Cambiar   	  Eliminar   	  Primaria   	  Indice   	  Unico   	 Fulltext
	 	IdTbjoModisto  	int(11) 	  	No  	0  	  	Cambiar 	Eliminar 	Primaria 	Indice 	Unico 	Fulltext
	 	Arreglo  	tinytext 	  	No  	  	  	Cambiar 	Eliminar 	Primaria 	Indice 	Unico 	Fulltext
	 	Coste*/ 
	 	//ges_modistos_arreglos
	 	$id 		= $job->getId();
	 	$arreglo 	= CleanRealMysql($this->concepto);
	 	$coste 		= $this->importe;
	 	
	 	$sql = "INSERT INTO ges_modistos_arreglos (IdTbjoModisto,Arreglo,Coste) VALUES ('$id','$arreglo','$coste')";
	 	query($sql);		 
	}	
	
	function Alta($IdFactura, $SerialNum){ 
		$this->idfactura = $IdFactura;
		
		$sql = "INSERT INTO ges_facturas_det (IdFactura,IdProducto,Cantidad,Precio,Descuento,Importe, Iva,Concepto,Talla,Color,Referencia,CodigoBarras) VALUES ".
			"( '".$IdFactura."','".$this->idproducto."','".$this->unidades."','".$this->precio."','".$this->descuento."','".
			$this->importe."','".$this->impuesto."','".$this->concepto. "','".$this->talla.
			"','".$this->color."','".$this->referencia."','".$this->codigobarras."')";
		
		$this->nticket = $SerialNum;			
					
		$res = query($sql,"Detalle ticket");				
	}

	function RetiradaDeAlmacen($local){			
		global $alm;
			
		$alm->ModificaCantidad($this->idproducto,0 - $this->unidades,$local);		
	}

}
?>
