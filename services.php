<?php

include("tool.php");

SimpleAutentificacionAutomatica("novisual-services");

include_once("class/filaticket.class.php");
include_once("class/arreglos.class.php");

define ("ALTA_MUDA",true);

	//Posibles estados de una factura
define("FAC_PENDIENTE_PAGO",1);
define("FAC_PAGADA",		2);
define("FAC_IMPAGADA",		3);
define("FAC_ANULADA",		4);


function OperarPagoSobreTicket($idfactura,$pago_efectivo, $pago_bono, $pago_tarjeta){

	/* Movimientos de dinero */
	$IdLocal =  getSesionDato("IdTienda");
	EntregarCantidades("Abonando pendiente", $IdLocal,$pago_efectivo, $pago_bono, $pago_tarjeta);	
	
	$pago = $pago_efectivo + $pago_bono + $pago_tarjeta;
	
	/* Estudiamos el estado final */
		
	$sql = "SELECT * FROM ges_facturas WHERE IdFactura='$idfactura'";	
	$row = queryrow($sql);
	
	$pendiente = $row["ImportePendiente"];
	$resto = $pendiente - $pago;
	
	if($resto<0.01){
		$newstatus = FAC_PAGADA;
		$newpendiente = 0;
	}	else {
		$newstatus = FAC_PENDIENTE_PAGO;
		$newpendiente = $resto;
	}
	
	/* Actualizamos estado y cantidades pendientes */	
	$sql = "UPDATE  ges_facturas SET Status='$newstatus', ImportePendiente = '$newpendiente' WHERE IdFactura = '$idfactura'";	
	query($sql,"Abonando un ticket");
		
	return $newpendiente;		
}



function JSenquote($var) {
                    $ascii = '';
                    $strlen_var = strlen($var);
    
                    for($c = 0; $c < $strlen_var; $c++) {
                        
                        $ord_var_c = ord($var{$c});
                
                        if($ord_var_c == 0x08) {
                            $ascii .= '\b';
                        
                        } elseif($ord_var_c == 0x09) {
                            $ascii .= '\t';
                        
                        } elseif($ord_var_c == 0x0A) {
                            $ascii .= '\n';
                        
                        } elseif($ord_var_c == 0x0C) {
                            $ascii .= '\f';
                        
                        } elseif($ord_var_c == 0x0D) {
                            $ascii .= '\r';
                        
                        } elseif(($ord_var_c == 0x22) || ($ord_var_c == 0x2F) || ($ord_var_c == 0x5C)) {
                            $ascii .= '\\'.$var{$c}; // double quote, slash, slosh
                        
                        } elseif(($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)) {
                            // characters U-00000000 - U-0000007F (same as ASCII)
                            $ascii .= $var{$c}; // most normal ASCII chars
                
                        } elseif(($ord_var_c & 0xE0) == 0xC0) {
                            // characters U-00000080 - U-000007FF, mask 110XXXXX, see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                            $char = pack('C*', $ord_var_c, ord($var{$c+1})); $c+=1;
                            $ascii .= sprintf('\u%04s', bin2hex(mb_convert_encoding($char, 'UTF-16', 'UTF-8')));
    
                        } elseif(($ord_var_c & 0xF0) == 0xE0) {
                            // characters U-00000800 - U-0000FFFF, mask 1110XXXX, see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                            $char = pack('C*', $ord_var_c, ord($var{$c+1}), ord($var{$c+2})); $c+=2;
                            $ascii .= sprintf('\u%04s', bin2hex(mb_convert_encoding($char, 'UTF-16', 'UTF-8')));
    
                        } elseif(($ord_var_c & 0xF8) == 0xF0) {
                            // characters U-00010000 - U-001FFFFF, mask 11110XXX, see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                            $char = pack('C*', $ord_var_c, ord($var{$c+1}), ord($var{$c+2}), ord($var{$c+3})); $c+=3;
                            $ascii .= sprintf('\u%04s', bin2hex(mb_convert_encoding($char, 'UTF-16', 'UTF-8')));
    
                        } elseif(($ord_var_c & 0xFC) == 0xF8) {
                            // characters U-00200000 - U-03FFFFFF, mask 111110XX, see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                            $char = pack('C*', $ord_var_c, ord($var{$c+1}), ord($var{$c+2}), ord($var{$c+3}), ord($var{$c+4})); $c+=4;
                            $ascii .= sprintf('\u%04s', bin2hex(mb_convert_encoding($char, 'UTF-16', 'UTF-8')));
    
                        } elseif(($ord_var_c & 0xFE) == 0xFC) {
                            // characters U-04000000 - U-7FFFFFFF, mask 1111110X, see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                            $char = pack('C*', $ord_var_c, ord($var{$c+1}), ord($var{$c+2}), ord($var{$c+3}), ord($var{$c+4}), ord($var{$c+5})); $c+=5;
                            $ascii .= sprintf('\u%04s', bin2hex(mb_convert_encoding($char, 'UTF-16', 'UTF-8')));
    
                        }
                    }
                    
                    return sprintf('"%s"', $ascii);
}                    
                    


function Traducir2atrib($datos){	
	if (!is_array($datos)){
		return "";
	}
		
	$out = "";
	foreach ( $datos as $key => $value ){
		if ($key and !is_numeric($key)){
			$value = addslashes($value);
			$value = str_replace("&","&amp;",$value);
			$value = str_replace("<","&lt;",$value);
			$value = str_replace(">","&gt;",$value);
			$out .= "$key='$value' " ;			
		}					
	}	
	return $out;
}	
	
function Traducir3XML($datos){
	
	if (!is_array($datos)){
		return $datos;
	}
	
	$out = "";
	foreach ( $datos as $key => $values ){
		if ($key and !is_numeric($key)){
			$out .= "<$key ";
			//if (is_array($values)){
				$out .= Traducir2atrib($values);	
			//} else {
			//	$out .= $values;	
			//}
			$out .= "/>";	
		}					
	}	
	return $out;	
}
	
	
function Traducir2XML($datos){
	
	if (!is_array($datos)){
		return $datos;
	}
	
	$out = "";
	foreach ( $datos as $key => $values ){
		if ($key and !is_numeric($key)){
			$out .= "<$key>";
			if (is_array($values)){
				$out .= Traducir2XML($values);	
			} else {
				$out .= $values;	
			}
			$out .= "</$key>";	
		}					
	}	
	return $out;	
	
	/*
	 * Ejemplo de uso:
	$prueba = array();
	$prueba["mensaje"] = array("autor"=>"Pedro", "texto"=>"hola mundo");
	echo VolcarDatosEnXML($prueba);
	*/
}


function DetallesVenta($idfactura){	
	$sql = "SELECT ges_productos.Referencia, ges_productos_idioma.Nombre, ges_tallas.Talla, ges_colores.Color, " .
 		"ges_facturas_det.Cantidad as Cantidad, ges_facturas_det.Descuento as Descuento, ges_facturas_det.Importe as Importe," .
 		"ges_productos.IdProducto, ges_productos.CodigoBarras
FROM
ges_facturas_det INNER JOIN ges_productos ON ges_facturas_det.IdProducto = ges_productos.IdProducto INNER JOIN ges_productos_idioma ON ges_productos.IdProdBase =ges_productos_idioma.IdProdBase INNER JOIN ges_tallas ON ges_productos.IdTalla = ges_tallas.IdTalla INNER JOIN ges_colores ON  ges_productos.IdColor = ges_colores.IdColor

WHERE ges_facturas_det.IdFactura = '$idfactura'
AND ges_productos_idioma.IdIdioma = 1
AND ges_tallas.IdIdioma = 1
AND ges_colores.IdIdioma = 1
AND ges_facturas_det.Eliminado = 0 ";
	
	$res = query($sql);
	if (!$res) return false;
	$ventas = array();
	$t = 0;
	while($row = Row($res)){
		$nombre = "detalles_" . $t++;
		
		//INFO: se reintegra el descuento para que el ticket tenga sentido
		if ($row["Descuento"]>0){
			$row["Importe"] =  $row["Importe"]/( 1 - ($row["Descuento"]/100) );			
		}					
		$ventas[$nombre] = $row; 		
	}		
	return $ventas;
}

function DetallesArreglos($idmodisto, $status, $ticket){
	
	$idmodisto	= CleanID($idmodisto);
	$status 	= CleanRealMysql( $status );
	$ticket		= CleanRealMysql($ticket);
	
	
	$extraModisto	= ($idmodisto)?" AND ges_modistos_tbjos.IdModisto = '$idmodisto' ":"";
	$extraStatus 	= ($status)?" AND ges_modistos_tbjos.Status = '$status' ":"";
	$extraTicket 	= ($ticket)?" AND ges_modistos_tbjos.NTicket LIKE '%$ticket%' ":"";
	
	
	$sql = "SELECT ges_modistos.NombreComercial AS NombreModisto, ges_modistos_tbjos.DescripcionProducto, 
		ges_modistos_tbjos.Arreglos, ges_modistos_tbjos.NTicket, 
		ges_modistos_tbjos.Status, ges_modistos_tbjos.FechaEnvio, ges_modistos_tbjos.FechaRecepcion, ges_modistos_tbjos.IdTbjoModisto  
		FROM ges_modistos_tbjos INNER JOIN ges_modistos ON ges_modistos_tbjos.IdModisto = ges_modistos.IdModisto
		WHERE ges_modistos_tbjos.Eliminado = 0 
		$extraModisto $extraStatus $extraTicket ";
	
	
	$res = query($sql);
	if (!$res) return false;
	$arreglos = array();
	$t = 0;
	while($row = Row($res)){
		$nombre ="detalles_" . $t++;
		$arreglos[$nombre] = $row; 		
	}		
	return $arreglos;		
}


function DetallesCliente($idcliente){	
	$cliente = new cliente;
	
	if ($cliente->Load($idcliente)){
		$row = $cliente->export();	
	} else return false;
		
	$clientes = array();
	$t = 0;
			
	$row["id"] = "cliente";
	$clientes["cliente"] = $row; 		
			
	return $clientes;
}


function VentasPeriodo($local,$desde,$hasta,$esSoloPendientes=false,$nombre=false,$esSoloCesion=false,$forzarfacturaid=false){
	
	
	if ($nombre and $nombre != ""){
		$nombre = CleanRealMysql($nombre);
		$extraRequisitoNombre = " AND ges_clientes.nombreComercial LIKE '%$nombre%' ";
	} else {
		$extraRequisitoNombre = "";
	}
	
	if ($forzarfacturaid>0){
		$extraID = " AND ges_facturas.IdFactura = '$forzarfacturaid' ";
	} else {
		/* Si no se fuerza un id, se admite buscar por rango de fechas */
		$extraFechas = " AND ges_facturas.FechaFactura >= '$desde'
     			AND ges_facturas.FechaFactura <= '$hasta' "; 		
	}
	
	$extraRequisito = ($esSoloPendientes)?" AND ges_facturas.Status = 1 ":"";
	$extraCesion 	= ($esSoloCesion)?" AND ges_facturas.SerieFactura LIKE 'CS%' ":""; 	 			    
        
	$desde = CleanRealMysql($desde);
	$hasta = CleanRealMysql($hasta);

	$sql = "SELECT ges_usuarios.Nombre As Vendedor, ges_facturas.SerieFactura, ges_facturas.NFactura,
             ges_facturas.FechaFactura, ges_facturas.TotalImporte, ges_facturas.ImportePendiente,
             ges_status_facturas.Status, ges_facturas.IdFactura, ges_clientes.nombreComercial as Cliente
    		FROM ges_facturas " .
    		"LEFT JOIN ges_clientes ON ges_facturas.IdCliente = ges_clientes.IdCliente
    INNER JOIN ges_status_facturas ON ges_facturas.Status = ges_status_facturas.IdStatus
    INNER JOIN ges_locales ON ges_facturas.IdLocal = ges_locales.IdLocal
    INNER JOIN ges_usuarios ON ges_facturas.IdUsuario = ges_usuarios.IdUsuario
    WHERE ges_facturas.Eliminado = 0
    AND ges_facturas.IdLocal = '$local'
	$extraID
	$extraFechas
	$extraRequisito $extraRequisitoNombre $extraCesion" .
    " ORDER BY ges_facturas.FechaFactura ASC ";  
	
	$res = query($sql);
	if (!$res) return false;
	$ventas = array();
	$t = 0;
	while($row = Row($res)){
		$nombre = "venta_" . $t++;
		$ventas[$nombre] = $row; 		
	}		
	return $ventas;
}

function VolcandoXML($codigoxml,$raiz){
	header("Content-type: text/xml");
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo "<$raiz>";
	echo $codigoxml;
	echo "</$raiz>";	
}

	function qq($val) {
	  $val = addslashes($val);
	  $val = str_replace("\n","\\n",$val);
	  //$val = JSenquote($val);
	  return "\"$val\"";
	}
	
	function is_intval($a) {
   		return ((string)$a === (string)(int)$a);
	}

	function qminimal($a){
		if (is_intval($a)){			
			return (string)$a;			
		}	
		return qq($a);
	}

function VolcarGeneracionJSParaProductos($nombre=false,$referencia=false,$cb=false){
		$nombre_up = CleanRealMysql(strtoupper($nombre));
		$referencia_s = CleanRealMysql(strtoupper(trim($referencia)));
		$cb_s = CleanRealMysql($cb);
		$IdLocalActivo = getSesionDato("IdTienda");
		
		if ($nombre)
			$CondicionPruebas = " UPPER(ges_productos_idioma.Nombre) LIKE '%$nombre_up%' AND ";
			
		if ($referencia)
			$CondicionPruebas .= " ges_productos.Referencia LIKE '%$referencia_s%' AND ";

		if ($cb)
			$CondicionPruebas .= " ges_productos.CodigoBarras = '$cb_s' AND ";

			
		$sql = "SELECT ges_almacenes.IdProducto,
			ges_productos.IdProdBase,  
			ges_almacenes.Id,
			ges_almacenes.Unidades,
			ges_almacenes.Descuento,
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
			ges_almacenes.IdLocal = '$IdLocalActivo' AND  
			$CondicionPruebas 
			ges_productos_idioma.IdIdioma = '1'
			AND ges_productos.Eliminado = 0 ORDER BY ges_productos.IdProdBase ";
	
		$jsOut = "";
		$jsListar = "";
		$res = query($sql);
		while($row = Row($res)){
			$talla = getIdTalla2Texto($row["IdTalla"]);									
			$color = getIdColor2Texto($row["IdColor"]);			
		
			$jsOut .= "tAL(" . qminimal($row["CodigoBarras"]) . ",".
			 qminimal($row["Nombre"]). ",".
			 qminimal($row["Referencia"]) . ",".
			 qminimal($row["PrecioVenta"]*100) . ",".
			 qminimal($row["Impuesto"]) .",".
			 qminimal($talla) . "," . qminimal($color).	",".		
			 qminimal($row["Descuento"] * 1.0). ");\n";
			
			$jsListar .= "CEEP(".qminimal($row["CodigoBarras"]).");\n";
		}
		$jsListar .= "OcultarAjax();";
		
		return $jsOut . $jsListar;
}

switch($modo) {
	case "buscaproductoref":
		$ref = CleanRef($_REQUEST["ref"]);		
		echo VolcarGeneracionJSParaProductos(false,$ref);
		
		break;
	case "buscaproducto":	
		$nombre = $_REQUEST["nombre"];
		echo VolcarGeneracionJSParaProductos($nombre);		
		break;	
	
	case "buscarproductocb":
		$cb = CleanCB($_REQUEST["cb"]);
		echo VolcarGeneracionJSParaProductos(false,false,$cb);
		break;		
	
	case "eliminarcliente":
		$idcliente 	= CleanID($_GET["idcliente"]);
		
		$cliente = new cliente;
		if($cliente->Load($idcliente))		
			echo $cliente->MarcarEliminado();
		else
			echo 0;
		break;
	
	
	case "mostrarCliente":		
		$idcliente 	= CleanID($_GET["idcliente"]);
		
		$datos = DetallesCliente($idcliente);		
		
		VolcandoXML( Traducir3XML($datos),"clientes");
		exit(); 		
		break;	

	
	case "realizarAbono":
		$id = CleanID($_GET["idfactura"]);
		$pago = CleanFloat($_GET["pago"]);
		
		/*
		+ "&pago_efectivo=" + parseFloat(abono_efectivo)
		+ "&pago_bono=" + parseFloat(abono_bono)
		+ "&pago_tarjeta=" + parseFloat(abono_tarjeta)*/
		
		$pago_efectivo 	= CleanFloat($_GET["pago_efectivo"]);
		$pago_bono 		= CleanFloat($_GET["pago_bono"]);
		$pago_tarjeta 	= CleanFloat($_GET["pago_tarjeta"]);				
		
		$newpendiente = OperarPagoSobreTicket($id,$pago_efectivo, $pago_bono, $pago_tarjeta);
		echo $newpendiente;//Cantidad pendiente o cero.				
		break;
	
	case "numeroSiguienteDeFacturaParaNuestroLocal":	
		$IdLocalActivo = getSesionDato("IdTienda");
		$moticket = $_GET["moticket"];
		$numSerieTicketLocalActual = GeneraNumDeTicket($IdLocalActivo,$moticket);
		echo $numSerieTicketLocalActual;// . " con $moticket";
		exit();	
		break;	
	case "altaproducto":
		if ( $id = AltaDesdePostProducto(ALTA_MUDA) ) {
			$unidades = CleanInt($_POST["Unidades"]);
			AgnadirCarritoCompras($id,$unidades);				
			echo $id;
		}  else {
			echo "0";
		}
		exit();
		break;
		
	case "mostrarArreglos":		
		$idmodisto 	= CleanID($_GET["idmodisto"]);
		$status 	= CleanText($_GET["status"]);
		$ticket		= CleanText($_GET["ticket"]);
		$datos = DetallesArreglos($idmodisto, $status, $ticket);
		VolcandoXML( Traducir3XML($datos),"arreglos");
		exit(); 		
		break;	
			
	case "mostrarDetallesVenta":
		$idfactura = CleanID($_GET["idfactura"]);
		$datos = DetallesVenta($idfactura);
		VolcandoXML( Traducir2XML($datos),"detalles");				
		exit();				
		break;
					
	case "mostrarVentas":
		$local = getSesionDato("IdTienda");
		$desde 	= CleanFechaES($_GET["desde"]);
		$hasta	= CleanFechaES($_GET["hasta"]);
		$nombre = CleanText($_GET["nombre"]);
		$modoserie 		= $_GET["modoserie"];
		$modoconsulta  	= $_GET["modoconsulta"];		
		$esSoloPendientes 	= ($modoconsulta == "pendientes");
		$esSoloCesion 		= ($modoserie == "cedidos");	
		
		$forzarfacturaid = CleanID($_GET["forzarfactura"]);		
		 		
		if (!$hasta or $hasta == ""){
			$mm = intval(date("m"));$dd = intval(date("d"));$aaaa = intval(date("Y"));
			$hasta = "$aaaa-$mm-$dd";
		}
		if (!$desde or $desde == ""){
			$desde = "1900-01-01";
		}
					
		$datos = VentasPeriodo($local,$desde,$hasta,$esSoloPendientes,$nombre,$esSoloCesion,$forzarfacturaid);
		VolcandoXML( Traducir2XML($datos),"ventas");
		exit();
		break;
		
	//http://www.casaruralvirtual.com/misc/9gextra/services.php?modo=setStatusTrabajoModisto&idtrabajo=12&status=Entregado		
	case "setStatusTrabajoModisto":
		$idtrabajo 	= CleanID($_GET["idtrabajo"]);
		$status 	= CleanText($_GET["status"]);
		
		$job 		= new job;		
		$job->qModificacionEstado($status,$idtrabajo);								
		exit();
		break;
}






?>