<?php

include("tool.php");

SimpleAutentificacionAutomatica("novisual-service");

include_once("class/filaticket.class.php");
include_once("class/arreglos.class.php");

/* INCIALIZACIONES */

$alm = new almacenes();

	//Posibles estados de una factura
define("FAC_PENDIENTE_PAGO",1);
define("FAC_PAGADA",		2);
define("FAC_IMPAGADA",		3);
define("FAC_ANULADA",		4);

$ImporteNeto 	= 0;//lo que paga el cliente, menos los impuestos
$IvaImporte 	= 0;//cuando de lo que hay que pagar es debido a impuestos
$TotalImporte 	= 0;//Lo que tiene que pagar el cliente

$carrito 		= array(); //acumularemos aqui las lineas de ticket ticket
$icarrito 		= 0;

$trabajos 		= array(); //acumularemos aqui los trabajos a enviar al modisto


/* LEEMOS ALGUNOS DATOS GENERALES DEL TICKET */

//Guardamos numero de serie, para referencia posterior
$serie 			= CleanText($_POST["serieticket"]);
$numticket 		= CleanInt($_POST["numticket"]);

$entregado		= CleanFloat($_POST["entrega"]);
$cambio			= CleanFloat($_POST["cambio"]);//dinero devuelto al cliente
		if ($cambio>0)
			$entregado 	= $entregado - $cambio;//se elimina el cambio que no tiene sentido aqui
		// Cambio: sera positivo si hay que devolverle al cliente
		// y negativo si el cliente nos debe.
	
	
# Sacamos local
$local 			= getSesionDato("IdTienda");

# Sacamos dependiente
$dependiente 	= CleanTo($_POST["dependiente"]," ");
$idDependiente 	= getIdFromDependiente($dependiente); 

# Quien compra
$idClienteSeleccionado = CleanID($_POST["UsuarioSeleccionado"]);

# Dinero entregado en metalico
$entregaEfectivo 	= CleanFloat($_POST["entrega_efectivo"]);
		//No se llega a entregar la totalidad, sino solo la diferencia con el cambio
		if ($cambio>0) $entregaEfectivo 	= $entregaEfectivo - $cambio;
	
# Dinero entregado mediante bono o tarjeta
$entregaBono 		= CleanFloat($_POST["entrega_bono"]);
$entregaTarjeta  	= CleanFloat($_POST["entrega_tarjeta"]);

/* VERIFICACIONES */

	# Verificamos si la peticion ha sido duplicada
	# Si es el caso, vendra con un serialrnd igual al anterior
	
	$oldvalue_tpv_serialrand = $_SESSION["TPV_SerialRand"];
	
	if (isset($_POST["TPV_SerialRand"])){
		$newvalue = $_POST["tpv_serialrand"];				
	
		if ($newvalue and ($newvalue == $oldvalue_tpv_serialrand) ){
			//Tenemos un serial, y es el mismo que usamos la otra vez.
			// por tanto es una peticion repetida, y la evitamos saliendo.
			
			//TODO: salimos con 0, ó informamos del problema a la TPV de una manera mejor?.									
			echo 0;
			exit();				
		}						
	}
	
	// Recordaremos el serial utilizado, para evitar repetirlo.
	$_SESSION["TPV_SerialRand"] = $_POST["tpv_serialrand"];


# Verificamos la fiabilidad del $numticket
$IdLocalActivo 	= getSesionDato("IdTienda");
$modoTicket 	= $_GET["moticket"];

$numeroTeorico 	= CleanInt(GeneraNumDeTicket($IdLocalActivo,$modoTicket));

if ( ($numeroTeorico > $numticket) or !$IdLocalActivo){ 
	//Si el ticket es menor de lo que deberia
	// ..asumimos ha habido algun error y abortamos.
	
	//Si se ha perdido el login, tambien abortamos.
	echo 0;
	exit();		
}

setSesionDato( "numSerieTicketLocalActual", $numticket );

/* VAMOS A LEER EL TICKET LINEA A LINEA */

//¿Cuantos datos hay para recoger?
$numlines = CleanInt($_POST["numlines"]);


for($t=0;$t<$numlines;$t++) {
	$firma = "line_".$t."_";

	$codigo = $_POST[$firma . "cod"];
	if ($codigo) {
		$unidades 	= CleanInt($_POST[$firma . "unid"]);
		$precio 	= CleanFloat($_POST[$firma . "precio"]);
		$descuento 	= CleanFloat($_POST[$firma . "descuento"]);
		$impuesto 	= CleanFloat($_POST[$firma . "impuesto"]);
		$concepto 	= CleanText($_POST[$firma . "concepto"]);
		$nombre		= CleanText($_POST[$firma . "nombre"]);
		$talla 		= CleanText($_POST[$firma . "talla"]);
		$color 		= CleanText($_POST[$firma . "color"]);
		$referencia = CleanRef($_POST[$firma . "referencia"]);
		$cb			= CleanCB($_POST[$firma . "cb"]);
		$idmodisto	= CleanCB($_POST[$firma . "idmodisto"]);		
						
		AgnadirTicket($codigo, $unidades, $precio, $descuento, $impuesto,$concepto,$talla,$color,$referencia,$cb,$idmodisto,$nombre);
	}
}

/* OPERAMOS SOBRE LOS DATOS QUE HEMOS COLECCIONADO */

EjecutarTicket( $idDependiente, $entregado, $local, $numticket,
		 $serie,$idClienteSeleccionado,$modoTicket, $entregaEfectivo, $entregaBono, $entregaTarjeta,$cambio 	 
	 ) ;

switch($modoTicket){
	case "venta":
	case "cesion":
	case "devolucion":
		EjecutarRetiradaDeAlmacen( $local );
		break;
	case "interno": break; //En interno no se hacen cambios en stock
}

/* SALIMOS DEL PROCESO */
	
echo 1;

////////////////////////////////////////////////////////////////////////////////7
//
// Funciones 


/*
 *  popula una fila de ticket con los datos que llegan de la terminal
 */

function AgnadirTicket($codigo,$unidades, $precio, $descuento,  $impuesto 
		,$concepto,$talla,$color, $referencia,$cb,$idmodisto, $nombre) {
	global $ImporteNeto, $IvaImporte, $TotalImporte;
	global $icarrito, $carrito;
	
	//Valores para este elemento
	$costeneto = $precio * $unidades;
	$coste = $costeneto - ($costeneto * ($descuento/100.0) );	
	$iva = $coste * ($impuesto);
	
	//Actualizar globales
	$IvaImporte = $IvaImporte + $iva; //Se actualiza cuanto es debido a los impuestos 
	$TotalImporte = $TotalImporte + $coste; //Se actualiza cuanto debe pagar el clientes
	//$ImporteNeto = $ImporteNeto + $coste - $iva;  <-- seria esto, pero lo calcularemos 
	// al final por no hacer el pijas y que falle por decimales o algo asi.

	//Creamos registro de almacenaje	
	$fila = new filaTicket;
	$fila->Set($codigo,$unidades, $precio, $descuento, $impuesto,$coste,$concepto,$talla,$color,$referencia,$cb,$idmodisto,$nombre);

	//Guardamos en carrito
	$carrito[$icarrito] = $fila;$icarrito = $icarrito + 1;	
}

/*
 * ** AGRUPA JOB **
 * cada prenda que se envia a un modisto representa un "trabajo" distinto que 
 * tendra uno o varios arreglos.
 */
  
function AgrupaJob( &$arreglo ){
	global $trabajos;
	
	if (!$arreglo->esArreglo())
		return;	
	
	$codigojob = $arreglo->codigojob;
	error(0,"AgrupaJob: codjob:". $codigojob);
	
	if (!isset($trabajos[$codigojob])){
		//Tenemos un job nuevo
		$trabajos[$codigojob] = new job;
		$trabajos[$codigojob]->CreaDesdeArreglo($arreglo);
		return;				
	}	
	$arreglo->AltaArreglo($trabajos[$codigojob]);
}






/**** EJECUTAR TICKET **
 * crear recuerdo de ticket de compra
 */
function EjecutarTicket( $idDependiente, $entregado ,$IdLocal, $Num, 
		$Serie,$IdCliente,$modoTicket ,$entregaEfectivo, $entregaBono, $entregaTarjeta, $cambio 
		){
	global $TotalImporte;
	global $ImporteNeto;
	global $IvaImporte;
	global $carrito, $UltimaInsercion;
	global $trabajos;
	
	switch($modoTicket){
		case "venta":
			//Lo que sea
			$ImportePendiente = intval( (abs($TotalImporte) - abs($entregado)) *100 )/100.0;
			if ($ImportePendiente<0)//Se entrego mas de lo que se dio
				$ImportePendiente = 0;
			
			//En una venta hay movimiento de dinero	
			//EntregarCantidades("Ticket devolución", $IdLocal,$entregaEfectivo, $entregaBono, $entregaTarjeta);
			break;
		case "devolucion":
			//En una devolucion no se debe dinero
			$ImportePendiente = 0;
			
			//Si el cliente devuelve una prensa, no entrega dinero, luego debera ser cero,
			// ...pero si se le entrega el bono.
			//EntregarCantidades("Ticket Devolución", $IdLocal,$entregaEfectivo, $entregaBono, $entregaTarjeta);
			break;
		case "cesion":
			//Normalmente la totalidad del coste
			$ImportePendiente = abs(intval( (abs($TotalImporte) - abs($entregado)) *100 )/100.0) ;
			
			//Normalmente a cero, en cesion no se paga
			//EntregarCantidades("Ticket cesión", $IdLocal,$entregaEfectivo, $entregaBono, $entregaTarjeta);											
			break;
		case "interno":
			$ImportePendiente = 0;
			break;
		default:
			$modoTicket = "tipoError:" + CleanRealMysql(CleanParaWeb($modoTicket));
			$ImportePendiente = abs(intval( (abs($TotalImporte) - abs($entregado)) *100 )/100.0) ;
			break;
	}		
		
	$IdLocal = CleanID($IdLocal);
			
	if ( abs($ImportePendiente) > 0.009 ) 
		$Status = FAC_PENDIENTE_PAGO ;
	else		
		$Status = FAC_PAGADA ;			
			
	$ImporteNeto = $TotalImporte - $IvaImporte;
	
	$sql = "INSERT INTO ges_facturas ( IdLocal, IdUsuario, SerieFactura,NFactura,FechaFactura,ImporteNeto, IvaImporte, TotalImporte,ImportePendiente, Status,IdCliente) "
	 . "VALUES ( '$IdLocal','$idDependiente','$Serie','$Num',NOW(),'$ImporteNeto','$IvaImporte','$TotalImporte','$ImportePendiente','$Status','$IdCliente')";
	
	$SerialNum = "$Serie-$Num";
		
	$res = query($sql,"Creando Ticket ($modoTicket)");
	
	if ($res) {
		//Procesar lineas
		$IdFactura = $UltimaInsercion;
	
		if ($modoTicket!="interno"){
			$TipoOperacion = "Ingreso";//venta, otros
			switch($modoTicket){
				case "devolucion":
					$TipoOperacion = "Gasto"; //devolucion
					//EntregarCantidades("Ticket Venta", $IdLocal,$entregaEfectivo, $entregaBono, $entregaTarjeta,$IdFactura,$TipoMovimiento);
					EntregarMetalico($IdLocal,$TotalImporte,"Ticket devolución",$IdFactura,$TipoOperacion);					
					break;
				default:
					EntregarCantidades("Ticket venta", $IdLocal,$entregaEfectivo, $entregaBono, $entregaTarjeta,$IdFactura,$TipoOperacion);
					break;
			}
			
		}
						
		foreach ($carrito as $fila) {			
			$fila->Alta($IdFactura,$SerialNum);
			AgrupaJob( $fila );		
		}		
		
		foreach ($carrito as $fila) {
			$codigojob	= $fila->codigojob;
			$concepto 	= $fila->concepto;
			
			if ($trabajos[$codigojob]){
				$trabajos[$codigojob]->AgnadeConcepto($concepto);
			}else {
				error(__LINE__.__FILE__ , "Error: no acepto $codigojob ");
			}
		}	
		
		foreach( $trabajos as $job){
			$job->SaveConceptoArreglo();	
		} 		
	}	
}

function EjecutarRetiradaDeAlmacen( $IdLocal ){
	global $carrito, $UltimaInsercion;
		
	foreach ($carrito as $fila) {
			if (!$fila->esArreglo())
				$fila->RetiradaDeAlmacen($IdLocal);		
	}		
}

?>
