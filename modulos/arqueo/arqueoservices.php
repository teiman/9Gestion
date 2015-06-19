<?php

include("../../tool.php");
require_once "../../class/json.class.php";

SimpleAutentificacionAutomatica("novisual-services");

$json = new Services_JSON();
$modo = $_REQUEST["modo"];


switch($modo){
	
	case "getDatosActualizadosArqueo":
		$IdArqueo = intval($_GET["IdArqueo"]);
		$IdLocal = intval($_GET["IdLocal"]);	
		ActualizarArqueoDeLocal($IdArqueo,$IdLocal);
		$data = getDatosArqueo($IdArqueo);
		$output = $json->encode($data);
		echo $output;
		exit();					
		break;
	
	case "getListaUltimosDiez":
		error(__FILE__,"Info: modo: $modo");
		
		$IdLocal = intval($_GET["IdLocal"]);
		$ultimosDiez = getUltimasDiezAsArray($IdLocal);		
		$output = $json->encode($ultimosDiez);
		echo $output;
		exit();
		break;

	case "getMovimientos":
		$IdArqueo = intval($_GET["IdArqueo"]);
		$data = getMovimientosArqueo($IdArqueo);
		$output = $json->encode($data);
		echo $output;
		exit();					
		break;		
		
	case "arquearYAbrirNuevaCaja":			
		$IdLocal = intval($_GET["IdLocal"]);
		$row = CalcularUltimoArqueo($IdLocal);
		ActualizarArqueo($row["IdArqueo"], $row);
		MarcarArqueoCerrado($row["IdArqueo"]);
		$row["ImporteCierre"] = getImporteCierre($row["IdArqueo"]);
		echo InsertarNuevaCaja($row,$IdLocal);
		exit();			
		break;	
		
	case "actualizarCantidadCaja":
		$IdLocal = intval($_GET["IdLocal"]);
		$cantidad = CleanFloat($_GET["cantidad"]);
		actualizarCantidadCaja($IdLocal,$cantidad);	
		exit();
		break;
			
	case "hacerAporteDinero":		
		//function EntregarMetalico($IdLocal,$entregado,$concepto,$IdFactura=false){
		$IdLocal = intval($_GET["IdLocal"]);
		$cantidad = CleanFloat($_GET["cantidad"]);	
		$concepto = $_GET["concepto"];
		EntregarMetalico($IdLocal,$cantidad,$concepto,false,"Aportacion");
		exit();	
		break;		
					
	case "hacerIngresoDinero":				
		$IdLocal = intval($_GET["IdLocal"]);
		$cantidad = CleanFloat($_GET["cantidad"]);	
		$concepto = $_GET["concepto"];
		EntregarMetalico($IdLocal,$cantidad,$concepto,false,"Ingreso");
		exit();	
		break;			
		
	case "hacerGastoDinero":				
		$IdLocal = intval($_GET["IdLocal"]);
		$cantidad = CleanFloat($_GET["cantidad"]);	
		$concepto = $_GET["concepto"];
		EntregarMetalico($IdLocal,$cantidad,$concepto,false,"Gasto");
		exit();	
		break;	
		
	case "hacerSubstraccionDinero":				
		$IdLocal = intval($_GET["IdLocal"]);
		$cantidad = CleanFloat($_GET["cantidad"]);	
		$concepto = $_GET["concepto"];
		EntregarMetalico($IdLocal,$cantidad,$concepto,false,"Sustraccion");
		exit();	
		break;															
					
	default:
		break;	
}


function getImporteCierre($IdArqueo){
	$sql = "SELECT ImporteCierre FROM ges_arqueo_caja WHERE IdArqueo='$IdArqueo' ";	
	$row = queryrow($sql);
	
	return $row["ImporteCierre"];	
}


function getUltimasDiezAsArray($IdLocal){
	
	$datos = array();
	$sql = "SELECT * FROM ges_arqueo_caja WHERE IdLocal='$IdLocal' ORDER BY FechaApertura DESC, IdArqueo DESC LIMIT 10";
	$res = query($sql,'Ultimas diez..');
	if (!$res) return $datos;
	
	$n = 0;
	while ($row = Row($res)){
		$datos["arqueo_$n"] = $row; 		
		$n++;
	}
	return $datos;
}

function getDatosArqueo($IdArqueo){	
	$IdArqueo = intval($IdArqueo);
	return queryrow("SELECT * FROM ges_arqueo_caja WHERE IdArqueo='$IdArqueo'");	
}


function ActualizarArqueoDeLocal($IdArqueo,$IdLocal){
		$row = CalcularUltimoArqueo($IdLocal,$IdArqueo);
		ActualizarArqueo($IdArqueo, $row);
		return $row;						
}

function ActualizarArqueo($IdArqueo, $Datos){
	
	$IdArqueo = intval($IdArqueo);
	
	$modos = array("Ingreso","Sustraccion","Aportacion","Gasto");
	
	$sql = "UPDATE ges_arqueo_caja SET ".
		" ImporteTeoricoCierre= '". $Datos["TeoricoFinal"] ."', ".
		" ImporteIngresos= '". $Datos["Ingreso"] ."', ".
		" ImporteSustracciones= '". $Datos["Sustraccion"] ."', ".
		" ImporteAportaciones= '". $Datos["Aportacion"] ."', ".		
		" ImporteGastos= '". $Datos["Gasto"] ."' " .			
		" WHERE IdArqueo='$IdArqueo' ";		 	
	query($sql,'Actualizar importes');
	
	$sql = "UPDATE ges_arqueo_caja SET ImporteTeoricoCierre = ImporteApertura+ImporteAportaciones-ImporteGastos+ImporteIngresos-ImporteSustracciones ".
	" WHERE IdArqueo='$IdArqueo'";
		
	query($sql,'Actualizando teorico-cierre');	 		

	$sql = "UPDATE ges_arqueo_caja SET ".		
		" ImporteDescuadre = ImporteTeoricoCierre - ImporteCierre ".		
		" WHERE IdArqueo='$IdArqueo' ";			
	query($sql,'Actualizar descuadre');		
}


function MarcarArqueoCerrado($IdArqueo){
		$IdArqueo = intval($IdArqueo);
		
		$sql = "UPDATE ges_arqueo_caja SET ".
		" FechaCierre= NOW(), ".
		" esCerrada = 1 ".
		" WHERE IdArqueo='$IdArqueo' ";
		query($sql,"Marcando arqueo como cerrado");		
		
		$sql = "UPDATE ges_arqueo_caja SET ".
		" FechaCierre= NOW(), ".
		" esCerrada = 1 ".
		" WHERE esCerrada=0 ";
		query($sql,"Marcando arqueo como cerrado [modo forzado]");		
		
						
}


function CalcularUltimoArqueo($IdLocal,$IdArqueo=false){

	$datos = array();
	
	$IdLocal = CleanID($IdLocal);
	
	if (!$IdArqueo){
		$sql = "SELECT IdArqueo FROM ges_arqueo_caja WHERE IdLocal='$IdLocal' AND Eliminado=0 AND esCerrada=0 ORDER BY FechaCierre DESC";
		$row = queryrow($sql,'Buscando arqueo abierto');
	
		$IdArqueo = $row["IdArqueo"];	
	
		if (!$IdArqueo)
			return false;	
	}	
		
	$datos["IdArqueo"] = $IdArqueo;		
		
	
	$modos = array("Ingreso","Sustraccion","Aportacion","Gasto");
	
	foreach($modos as $tipo){	
		$sql = "SELECT sum( Importe ) AS SumaImporte
		FROM ges_dinero_movimientos
		WHERE Eliminado = 0
			AND IdLocal 		= '$IdLocal'
			AND IdArqueoCaja 	= '$IdArqueo'
			AND TipoOperacion 	= '$tipo'
			AND IdModalidadPago = 1";
			//NOTA: investigar si modalidad de pago 1 es correcto o hay que contemplar otros tipos
			
		$row = queryrow($sql);

		$datos[$tipo] = $row["SumaImporte"];
	}	
	
	$datos["TeoricoFinal"]= $row["Ingreso"]+$row["Aportacion"]-$row["Gasto"]-$row["Sustraccion"];
	
	error(__FILE__ . __LINE__ , "If: final:". $datos["TeoricoFinal"]);
	
	return $datos;				
}




function getMovimientosArqueo($IdArqueo){
	
	$IdArqueo = intval($IdArqueo);
	
	$datos = array();
	$sql = "SELECT * FROM ges_dinero_movimientos WHERE IdArqueoCaja='$IdArqueo' AND IdModalidadPago=1 AND Eliminado=0 ORDER BY FechaInsercion DESC";
	
	$res = query($sql);
	if (!$res) return $datos;
	
	$n = 0;
	while( $row = Row($res)){
		$datos["mov_$n"] = $row;
		$n++;		
	} 			
	return $datos;					
}


function InsertarNuevaCaja($datosArqueo,$IdLocal){
	global $UltimaInsercion;	
	$IdLocal = CleanID($IdLocal);	
	
	$ImporteApertura = $datosArqueo["ImporteCierre"];
	$ImporteTeoricoCierre = $ImporteApertura;
	
	$sql = "INSERT INTO ges_arqueo_caja ( 
		IdLocal, FechaApertura, FechaCierre, ImporteApertura, ImporteIngresos,
		ImporteGastos,ImporteAportaciones,ImporteSustracciones,ImporteTeoricoCierre,
		ImporteCierre,ImporteDescuadre,Eliminado )
		VALUES (
		'$IdLocal', NOW(),'0-00-00', '$ImporteApertura', '0',
		'0', '0', '0', '$ImporteTeoricoCierre',
		 '0', '0', '0' )";
	
	$res = query($sql,'Insertando nueva caja');
	
	if ($res)return $UltimaInsercion;
	return 0;	
}

function actualizarCantidadCaja($IdLocal,$cantidad){
	$IdLocal = CleanID($IdLocal);
	$cantidad = CleanFloat($cantidad);
	
	$cantidad = CleanRealMysql($cantidad);
	$sql = "UPDATE ges_arqueo_caja SET ImporteCierre = '$cantidad' WHERE IdLocal='$IdLocal' AND esCerrada=0 ";
	query($sql,'Actualizando cantidad de cierre');
	
	$sql = "UPDATE ges_arqueo_caja SET ImporteTeoricoCierre = ImporteApertura+ImporteAportaciones-ImporteGastos+ImporteIngresos-ImporteSustracciones ".
	" WHERE IdLocal='$IdLocal' AND esCerrada=0 ";
		
	query($sql,'Actualizando teorico');	 		
	
	$sql = "UPDATE ges_arqueo_caja SET ImporteDescuadre = ImporteCierre - ImporteTeoricoCierre";
	query($sql,'Actualizando descuadre');
	
	
}


?>