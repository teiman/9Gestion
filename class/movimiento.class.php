<?php

	
function EntregarCantidades($concepto, $IdLocal,$entregaEfectivo, $entregaBono, $entregaTarjeta,$IdFactura,$TipoOperacion="Ingreso"){
	if($entregaEfectivo)
		EntregarMetalico($IdLocal,$entregaEfectivo,$concepto,$IdFactura,$TipoOperacion);
	if($entregaBono)
		EntregarBono($IdLocal,$entregaBono,$concepto,$IdFactura);
	if($entregaTarjeta)
		EntregarTarjeta($IdLocal,$entregaTarjeta,$concepto,$IdFactura);		
}	

function EntregarMetalico($IdLocal,$entregado,$concepto,$IdFactura=false,$TipoOperacion="SinEspecificar"){
	$mov = new Movimiento();
	
	$mov->SetFactura($IdFactura);
	$mov->SetConcepto("Metalico: $concepto");
	$mov->EntregaEnTienda($IdLocal,$entregado,"EFECTIVO");
	
	if ($TipoOperacion!="SinEspecificar")
		$mov->SetTipoOperacion($TipoOperacion);
	$mov->GuardaOperacion();			
}


function EntregarBono($IdLocal,$entregado,$concepto,$IdFactura=false){
	$mov = new Movimiento();
	
	$mov->SetFactura($IdFactura);
	$mov->SetConcepto("Bono: $concepto");
	$mov->EntregaEnTienda($IdLocal,$entregado,"BONO DE COMPRA");
	$mov->GuardaOperacion();			
}

function EntregarTarjeta($IdLocal,$entregado,$concepto,$IdFactura=false){
	$mov = new Movimiento();
	
	$mov->SetFactura($IdFactura);
	$mov->SetConcepto("Tarjeta: $concepto");
	$mov->EntregaEnTienda($IdLocal,$entregado,"TARJETA");
	$mov->GuardaOperacion();			
}


function getIdFromMedio($medio){
	$medio = strtoupper(trim($medio));
	$sql = "SELECT IdModPago FROM ges_modalidadespago WHERE ModalidadPago='$medio' ";
	$row =queryrow($sql);
	return $row["IdModPago"];
}

function MovimientoFactory($res) {
	if(!$res){
		error(__FILE__ . __LINE__ ,"ERROR en factory");
		return false;	
	}
	
	$row = Row($res);
	if (!is_array($row))
		return false;	
	$id = $row["IdOperacionCaja "];
	
	$oMovimiento = new movimiento();
		
	if ($oMovimiento->Load($id))
		return $oMovimiento;
		
	error(__FILE__ . __LINE__ ,"ERROR no pudo cargar id '$id'");
		
	return false;
}


class movimiento extends Cursor {
	var $ingresos;
	var $gastos;
	var $localOperacion;
	var $IdFactura;
	var $totalmovimiento;
	var $TipoOperacion;
	var $Concepto;
	var $Modalidad;
	
	function SetFactura($IdFactura){
		$this->IdFactura = CleanID($IdFactura);	
	}
	
	function SetConcepto($concepto){
		$this->Concepto = $concepto;
	}
	
	function EntregaEnTienda($IdLocal,$entregado,$mediodepago){
		if(!isset($this->ingresos[$mediodepago]))
			$this->ingresos[$mediodepago] = 0;
			
		//ModPago: efectivo, tarjeta, etc..

		$IdModalidadPago = getIdFromMedio($mediodepago);
		$this->set("IdModalidadPago",$IdModalidadPago,FORCE);
		error(__FILE__ . __LINE__ ,"Info: medio es '$IdModalidadPago' ");
			
		//NOTA: era valor absoluto.
		$this->totalmovimiento += $entregado;				
		$this->ingresos[$mediodepago] += $entregado;			
		$this->localOperacion = $IdLocal;
	}
	
	function GuardaOperacion(){
		/*  	 IdOperacionCaja   	int(11) 
	 IdArqueoCaja  	int(11) 	 
	 IdLocal  	smallint(6) 	 
	 TipoOperacion  	enum('Ingreso', 'Gasto', 'Aportacion', 'Sustraccion') 	 
	 FechaCaja  	date 	  	
	 Concepto  	tinytext 	  	
	 IdFactura  	int(11) 	 
	 IdAlbaran  	smallint(6) 	 
	 Importe  	double 	 
	 IdModalidadPago  	tinyint(4) 	 
	 CuentaBancaria  	tinytext 	
	 FechaInsercion  	datetime*/ 
	 	$IdFactura 		= $this->IdFactura;
	 	$IdLocal		= $this->localOperacion;
		$Concepto 		= CleanRealMysql($this->Concepto);

	 	$TipoOperacion  = $this->GetTipoOperacion();
	 	$Importe		= $this->GetImporteOperacion();
	 	$Concepto 		= CleanRealMysql($this->Concepto);
	 	$IdModalidadPago = $this->get("IdModalidadPago");
	 	$IdArqueoCaja = $this->GetArqueoActivo($IdLocal);
	 	
	 	$values = "IdModalidadPago,Concepto, IdArqueoCaja,IdLocal   ,TipoOperacion  ,FechaCaja   ,IdFactura,  Importe,FechaInsercion";
	 	$keys   = "'$IdModalidadPago','$Concepto','$IdArqueoCaja' 		   ,'$IdLocal','$TipoOperacion',CURDATE(),'$IdFactura','$Importe', NOW()";
	 
	 
	 
	 	$sql = "INSERT INTO ges_dinero_movimientos ( $values ) VALUES ( $keys )";
	 	$res = query($sql,"Creando un movimiento de dinero");
		return $res;
	}
	
	function GetArqueoActivo($IdLocal){
			$sql = "SELECT IdArqueo FROM ges_arqueo_caja WHERE IdLocal='$IdLocal' AND Eliminado=0 AND esCerrada=0 ORDER BY FechaCierre DESC";
			$row = queryrow($sql,'Buscando arqueo abierto');
	
			$IdArqueo = $row["IdArqueo"];
			return intval($IdArqueo);		
	}
	
	
	function SetTipoOperacion($Tipo){
		$this->TipoOperacion = $Tipo;
	}
	
	function GetTipoOperacion(){
		//TipoOperacion  	enum('Ingreso', 'Gasto', 'Aportacion', 'Sustraccion')
		return $this->TipoOperacion;		 	
	}
	
	function GetImporteOperacion(){
		return $this->totalmovimiento;	
	}
	
    function movimiento() {
    	$this->localOperacion = 0;//no local
    	$this->ingresos = array();
    	$this->gastos = array();
    	$this->TipoOperacion = "Ingreso";
    	return $this;
    }
    
    function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("ges_dinero_movimientos", "IdOperacionCaja ", $id);
		return $this->getResult();
	}
	
	function Crea(){
		//$this->setNombre(_("Nuevo movimiento"));
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
	
		$sql = "INSERT INTO ges_dinero_movimientos ( $listaKeys ) VALUES ( $listaValues )";
		
		$res = query($sql,"Alta movimiento");
		
		if ($res) {		
			$id = $UltimaInsercion;	
			$this->set("IdOperacionCaja ",$id,FORCE);
			return $id;			
		}
						
		return false;				 		
	}

	function Listado($lang,$min=0){
			
    	if (!$lang)
    		$lang = getSesionDato("IdLenguajeDefecto");
    
		$sql = "SELECT		
		ges_dinero_movimientos.*		
		FROM
		ges_dinero_movimientos 		
		WHERE
		ges_dinero_movimientos.Eliminado = 0
		";
		
		$res = $this->queryPagina($sql, $min, 10);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
		}		
				
		return $res;
	}
	
	function SiguienteMovimiento() {
		$res = $this->LoadNext();
		if (!$res) {
			return false;
		}
		$this->setId($this->get("IdOperacionCaja "));		
		return true;			
	}	
		
	function Modificacion () {
		
		$data = $this->export();				
		
		$sql = CreaUpdateSimple($data,"ges_dinero_movimientos","IdOperacionCaja ",$this->get("IdOperacionCaja "));
		
		$res = query($sql);
		if (!$res) {			
			$this->Error(__FILE__ . __LINE__ , "W: no actualizo proveedor");
			return false;
		}		
		return true;
	}
}

?>
