<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

$tamPagina = 20;
$tamPaginaSel = 10;

function CambiarPreciosGlobalmente($Id,$PrecioVenta){
	$row = queryrow("SELECT IdProducto FROM ges_almacenes WHERE Id = '$Id'");
	$IdProducto = $row["IdProducto"];
	$sql = "UPDATE ges_almacenes SET PrecioVenta='$PrecioVenta' WHERE IdProducto ='$IdProducto'";
	query($sql);
} 


function getIdBaseFromId($mod){
	$sql = "SELECT IdProducto FROM ges_almacenes WHERE Id = '$mod'";	
	$row = queryrow($sql);
	if (!$row)
		return false;
	
	$idp = $row["IdProducto"]; 
	$sql = "SELECT IdProdBase FROM ges_productos WHERE IdProducto = '$idp'";
	$row = queryrow($sql);
	
	return $row["IdProdBase"];	
}

function AutoOpen(){

	$mod = $_SESSION["IdUltimoCambioAlmacen"];
	if (!$mod)	return "";
	
	$idBase = getIdBaseFromId($mod);	
	
	//$_SESSION["IdUltimoCambioAlmacen"] = false;
	return "\n autoexpand[$idBase]=1;\n AutoFocusIdBase='$idBase';\n";	
}

function genOpcionesBusqueda(){
	global $action;	if (!Admite("Stocks"))	return false;
	$ot = getTemplate("FormBusquedaAlmacenProducto");
			
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template busqueda no encontrado");
		return false; }
	
	
	$local = $_SESSION["BusquedaLocal"];
//	if (!$local)
//		$combo = getSesionDato("ComboAlmacenes");
//	else
		$combo = genComboAlmacenes($local);
	$ot->fijar("vIdLocal",$local);

	//error(0,"leeyendo carrito");
	$hayCarrito = getSesionDato("hayCarritoTrans");
	
	if  ($hayCarrito){
		$ot->fijar("tOperaCarro",_("Trabajar con selección"));
		$ot->fijar("tBorraCarro",_("Deseleccionar"));
		$ot->fijar("hackCarro");
		$ot->confirmaSeccion("carro");
	} else {
		$ot->fijar("hackCarro","noactivo");	
		$ot->fijar("tOperaCarro");		
		$ot->fijar("tBorraCarro");
		$ot->eliminaSeccion("carro");	
	}	
	
	$ot->fijar("tTallaycolores",_("Tallas/Colores"));	
	
	$ot->fijar("Referencia" , _("Referencia"));
	$ot->fijar("bEnviar" , _("Buscar"));
	$ot->fijar("ACTION", $action);
	$ot->fijar("comboAlmacenes" , $combo);
	$ot->fijar("CB", _("CB"));
	
	if ($_SESSION["BusquedaLocal"])
		$ot->confirmaSeccion("haylocal");
	else
		$ot->eliminaSeccion("haylocal");
		
	return $ot->Output();												
}

function BusquedaBasica(){
 //	echo genOpcionesBusqueda();
}

function ListarAlmacen($referencia,$donde,$marcadotrans=false,$cb=false,$idbase=false,$soloLlenos=false,$obsoletos=false){	
	global $action;
	
	$base = getSesionDato("BusquedaProdBase");
	
	//Creamos template
	$ot = getTemplate("ListadoMonoProductoMultiAlmacen");
			
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template no encontrado");
		return false; }
	
	//Extraemos datos
	$almacen = getSesionDato("Articulos");
	
	$IdLocal = "";
	if ($donde){
		$IdLocal = $donde;
		if ($IdLocal){
		//	echo gas("nota",_("Listando por local"));	
		}			
	}
	
	$IdProducto = "";
	
	
	if ($referencia){
		$id = genReferencia2IdProducto($referencia);
		$idbase = getProdBaseFromId($id); 
	}		

	if (!$IdProducto and !$base){
			//echo gas("aviso","Debug: cb $cb");
			$IdProducto = getIdFromCodigoBarras($cb);	
	}
	
	
	
	if (!$IdLocal and !$IdProducto) {
		echo gas("Aviso",_("Sin resultados"));
	
		if (!$IdProducto) {
			setSesionDato("BusquedaReferencia",false);
			if (!$base)
				setSesionDato("BusquedaCB",false);  
			//si no encontro nada, no se busca en esa ref 
		}
					
		return false;	
	}
	
	$indice = getSesionDato("PaginadorAlmacen");
	//$tamPagina = $ot->getPagina();
	$tamPagina = 1000;
	
	
	//if (!$base)
	//	$res = $almacen->ListadoBase($IdLocal,$IdProducto,$indice,$tamPagina);
	//else
	
	$nombre = "";
	if (isset($_SESSION["BusquedaNombre"]) and $_SESSION["BusquedaNombre"])
		$nombre = $_SESSION["BusquedaNombre"];
		
	$res = $almacen->ListadoModular($IdLocal,$IdProducto,$indice,$tamPagina,$idbase,$nombre,$soloLlenos,$obsoletos);

	$haytrans = is_array($marcadotrans) and count($marcadotrans);

	$num = 0;
	
	$jsOut = "";
	$jsLex = new jsLextable;
	
	$jsOut .= jsLabel("comprar",	_("Comprar"));
	$jsOut .= jsLabel("modificar",	_("Modificar"));
	$jsOut .= jsLabel("referencia",	_("Referencia"));
	$jsOut .= jsLabel("unid",		_("Unid"));
	$jsOut .= jsLabel("pv",			_("PV"));
	$jsOut .= jsLabel("seleccionar", _("Seleccionar"));
	$jsOut .= jsLabel("cuantasunidades",	_("¿Cuántas unidades?"),false);
	
	$oldId = -1;
	while($almacen->SiguienteArticulo() ){
			$num++;
		
			$transid = $almacen->get("Id");
		
			$ref = $almacen->get("Referencia");
			$nombre = $almacen->get("Nombre");
			if (getParametro("ProductosLatin1")){
				$nombre = iso2utf($nombre);	
			}			
			
			$unidades = $almacen->get("Unidades");
			$precio = $almacen->get("PrecioVenta");
			$ident = $almacen->get("Identificacion");
			$id = $almacen->get("IdProducto");
			$iconos = $almacen->Iconos();
			$talla = getIdTalla2Texto($almacen->get("IdTalla"));						
		 	$lextalla = $jsLex->add($talla);
			$color = getIdColor2Texto($almacen->get("IdColor"));						
		 	$lexcolor = $jsLex->add($color);
	 		$desc = "";
	 		$nombreLocal = getNombreLocalId($almacen->get("IdLocal"));
	 		$lexlocal = $jsLex->add($nombreLocal);
	 		
	 		
			$fam = getIdFamilia2Texto( $almacen->get("IdFamilia"));
			$sub = getIdSubFamilia2Texto($almacen->get("IdFamilia"), $almacen->get("IdSubFamilia"));
			
			$lexfam = $jsLex->add($fam);
			$lexsub = $jsLex->add($sub);
	 		
		if ($haytrans and in_array($transid, $marcadotrans) ){			
			$sel = 1;
		}	else { 
			$sel = 0;
		}		
		
		$idBase = $almacen->get("IdProdBase");
		if ($idBase != $oldId) {
			$numlex = $jsLex->add($ident);
			$nombre = addslashes($nombre);
			$ref	= addslashes($ref);
			$jsOut .= "cAH($idBase,'$nombre','$ref','$desc',$numlex,$lexfam,$lexsub);\n";
		}
		$jsOut .= "cA($id,'$iconos',$unidades,'$precio',$sel,$transid,$lextalla,$lexcolor,$lexlocal);\n";
		$oldId = $idBase;							
	}	
	
	$jsOut = $jsLex->jsDump() . $jsOut;
	
		$jsOut .= AutoOpen();	
	
	$paginador = $ot->jsPaginador($indice,$tamPagina,$num);
	$jsOut .= $paginador;	
	$jsOut .= "cListAlmacen();";	
	$jsOut .= $paginador;
		

		
	echo "<center>";
	echo jsBody($jsOut);
	echo "</center>";					
	
}

function FormularioCompras($id){
	global $action;

	//Creamos template
	$ot = getTemplate("FormCompras");
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template no encontrado");
		return false; }
	
	$producto = new producto;	
	
	if ($producto->Load($id)){		
		$ot->fijar("NombreProducto",$producto->getNombre());			
		$ot->fijar("Referencia",$producto->getReferencia());
		$ot->fijar("tTitulo",_("Petición de compra"));
		$ot->fijar("tCantidad",_("Cantidad:"));
		$ot->fijar("tEnviar",_("Enviar:"));
		$ot->fijar("tProveedorHabitual",_("Proveedor habitual:"));
	
		$ot->fijar("IdProducto",$producto->getId());			
		$ot->fijar("action",$action);
	
		echo $ot->Output();
	}	else {
		echo gas("Aviso",_("No se puede realizar la operación"));	
	}
}


function getOperacionesConSeleccion(){
	return;

}


function OperacionesConSeleccion(){
	return ;
}

function ListarSeleccion($seleccion){
	global $action;
	
	//Creamos template
	$ot = getTemplate("ListadoMultiAlmacenSeleccion");
			
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template no encontrado");
		return false; }	
		
	$articulo = new articulo;		
	
	$tamPagina = $ot->getPagina();
	
	$indice = getSesionDato("PaginadorSeleccionAlmacen");
	$num = 0;
	$salta = 0;
	
	$mover = getSesionDato("CarritoMover");
	
	
	$ot->resetSeries(array("Unidades","PrecioVenta",
				"IdProducto","Nombre","Referencia","NumTraspasar","NombreComercial","Comprar","marcatrans","iconos"));	
	foreach ($seleccion as $idarticulo ){
		$salta ++;
		if ($num <= $tamPagina and $salta>=$indice){		
			$num++;			
			$articulo->Load($idarticulo);
					
			$ot->fijarSerie("Referencia",$articulo->get("Referencia"));
			$ot->fijarSerie("Nombre",$articulo->get("Nombre"));
			$ot->fijarSerie("Unidades",$articulo->get("Unidades"));
			$precio = $articulo->get("PrecioVenta");			
			if (!$precio)
				$precio = "<font color='red'>$precio</font>";
			$ot->fijarSerie("PrecioVenta",$precio);
			$ot->fijarSerie("NombreComercial",$articulo->get("NombreComercial"));				
			$ot->fijarSerie("IdProducto",$articulo->get("IdProducto"));
			$ot->fijarSerie("Comprar","");		
			$ot->fijarSerie("NumTraspasar",$mover[$idarticulo]);
			$ot->fijarSerie("transid",$idarticulo);
			$ot->fijarSerie("iconos",$articulo->Iconos());
		}						
	}	
	
	$ot->paginador($indice,false,$num);	
	$ot->fijar("action",$action );
	
	$ot->terminaSerie();
	echo $ot->Output();
	//echo "hi! '$num'";		
}


function FormTrasladoSeleccion(){
	global $action;
	
	$ot = getTemplate("FormTraslado");
			
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template busqueda no encontrado");
		return false; }

	$combo = getSesionDato("ComboAlmacenes");

	$ot->fijar("tDestino" , _("Destino:"));
	$ot->fijar("bEnviar" , _("Enviar"));
	$ot->fijar("ACTION", $action);
	$ot->fijar("comboAlmacenes" , $combo);


	echo $ot->Output();
}


function MarcarGenerico($marcado,$marcador){
	if (!$marcado or !is_array($marcado))
		return 0;
	
	$num = 0;
	foreach ($marcado as $Id){
		$num++;
		$res = query("UPDATE ges_almacenes SET $marcador WHERE Id = '$Id'","Marcar cambios articulo");			
	}
	return $num;		
}

function getIdProducto($id){
	$id = CleanID($id);
	$row = queryrow("SELECT IdProducto FROM ges_almacenes WHERE Id='$id'");
	return $row["IdProducto"];	
}

function MarcarGenericoProducto($marcado,$marcador){
	if (!$marcado or !is_array($marcado))
		return 0;
	
	$num = 0;
	foreach ($marcado as $Id){
		$num++;
		$IdProducto = getIdProducto($Id);
		$res = query("UPDATE ges_productos SET $marcador WHERE IdProducto = '$IdProducto'","Marcar cambios producto");			
	}
	return $num;		
}


function FormularioEditarArticulo($id){
	global $action;
	
	$ot = getTemplate("FormEditarArticulo");
			
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Error: template busqueda no encontrado");
		return false; }
		
	$articulo = new articulo();
	
	if(!$articulo->Load($id)){
		error(__FILE__ . __LINE__ ,"Error: no puedo modificar ese producto");
		return false; }
	 
	$ot->fijar("tTituloAux",_("Otras tiendas"));
	$ot->fijar("tIgualar",_("Todas las tiendas el mismo precio"));
	 
	$ot->fijar("tMotivo",_("Motivo mod. existencias"));
	 
	$ot->fijar("tTitulo",_("Modificar existencias"));

	$ot->fijar("vNombre",$articulo->get("Nombre"));
	$ot->fijar("tNombre",_("Nombre"));
			
	$ot->fijar("vReferencia",$articulo->get("Referencia"));
	$ot->fijar("tReferencia",_("Referencia"));
	
	$ot->fijar("vDescripcion",$articulo->get("Descripcion"));
	$ot->fijar("tDescripcion",_("Descripción"));
	
	$ot->fijar("vUnidades",$articulo->get("Unidades"));
	$ot->fijar("tUnidades",_("Existencias"));
	
	$ot->fijar("vPrecioVenta",$articulo->get("PrecioVenta"));
	$ot->fijar("tPrecioVenta",_("PV"));
	
	$ot->fijar("tTipoImpuesto",_("Impuesto"));
	$ot->fijar("vTipoImpuesto",$articulo->get("TipoImpuesto"));
	$ot->fijar("vImpuesto",$articulo->get("Impuesto"));
	$ot->fijar("vStockMin",$articulo->get("StockMin"));
	
	if ($articulo->is("Disponible"))
		$ot->fijar("cDisponible","checked");
	else
		$ot->fijar("cDisponible","");

	if ($articulo->is("Oferta"))
		$ot->fijar("cOferta","checked");
	else
		$ot->fijar("cOferta","");

	if ($articulo->is("StockIlimitado"))
		$ot->fijar("cStockIlimitado","checked");
	else
		$ot->fijar("cStockIlimitado","");

	$ot->fijar("tDisponible",_("Disponible en venta"));
	$ot->fijar("tOferta",_("En oferta"));						
	$ot->fijar("tStockIlimitado",_("Stock ilimitado"));				
	$ot->fijar("tStockMin",_("Stock minimo"));
	
	$ot->fijar("action",$action);
	$ot->fijar("vId",$articulo->get("Id"));
	
		
	echo $ot->Output();
}

function ModificarArticulo($id,$Unidades,$PrecioVenta,$disponible,$oferta,$stockilimitado,$stockmin,$impuesto) {	
	
	if (!Admite("Stocks"))	return false;

	if (!$disponible)
		$disponible = 0;
	else
		$disponible = 1;
	
	$_SESSION["IdUltimoCambioAlmacen"] = $id;
		
	MarcarGenerico(array("$id"),"Impuesto='$impuesto',Unidades='$Unidades', PrecioVenta='$PrecioVenta', Disponible='$disponible',Oferta='$oferta',StockIlimitado='$stockilimitado',StockMin='$stockmin' ");
	return true;
}

function RegistrarMermaManual($id,$AntiguoUnidades,$Unidades,$Motivo){
	 $oArticulo = new articulo;
	 
 	if (!Admite("Stocks"))	return false;
	 
	 if ($AntiguoUnidades == $Unidades) {
	 	//No tiene sentido!!
	 	return;	
	 }
	 
	 
	 if (!$oArticulo->Load($id)){
	 	error(__FILE__ . __LINE__ ,"E: no pudo cargar el articulo que se quiere modificar".q($id,"id"));
	 	return;	
	 }
	 
	 $IdLocal 		= $oArticulo->get("IdLocal");
	 $IdProducto 	= $oArticulo->get("IdProducto");
	 $IdUser 		= getSesionDato("IdUsuario"); 	 
	 
	 $sql = "INSERT INTO ges_inventario_ajustes 
		(TipoAjuste,IdProducto,IdLocal,CantidadAntes,CantidadDespues,Usuario,FechaAjuste) VALUES
		('Manual: $Motivo','$IdProducto','$IdLocal','$AntiguoUnidades','$Unidades','$IdUser',NOW())";
	 
	 query($sql,"Registro de una merma");
}

//Acciones mudas
switch($modo){
	case "trans": //Agadir un producto al carrito de la 
		$id = CleanID($_GET["id"]);
		$u = CleanInt($_GET["u"]);
		AgnadirCarritoTraspaso($id,$u);
		exit();
		break;
	case "notrans": //desAgadir un producto al carrito de la 
		$id = CleanID($_GET["id"]);		
		QuitarDeCarritoTraspaso($id);

		exit();
		break;				
}		
		
PageStart();

//echo gas("cabecera",_("Gestion de Almacenes"));

switch($modo) {
	case "bases":	
		setSesionDato("ListaBases",true);	
		break;
		
	case "nobases":
		setSesionDato("ListaBases",false);	
		break;		
	
	case "pagmas":
		$index = getSesionDato("PaginadorAlmacen");		
		$index = $index + $tamPagina;		
		setSesionDato("PaginadorAlmacen",$index);		
		break;
		
	case "pagmenos":
		$index = getSesionDato("PaginadorAlmacen");		
		$index = $index - $tamPagina;
		if ($index<0)
			$index = 0;		
		setSesionDato("PaginadorAlmacen",$index);
		break;
	case "selpagmas":
		$index = getSesionDato("PaginadorSeleccionAlmacen");		
		$index = $index + $tamPaginaSel;		
		setSesionDato("PaginadorSeleccionAlmacen",$index);
		break;		
	case "selpagmenos":	
		$index = getSesionDato("PaginadorSeleccionAlmacen");
		
		$index = $index - $tamPaginaSel;
		if ($index<0)
			$index = 0;				
		setSesionDato("PaginadorSeleccionAlmacen",$index);
		break;		

	default:
		break;
	
}

function OperacionTrasladoResumida($destino) {
	$oTraslado = new traslado;
	 
	$oTraslado->OperacionTraslado($destino);
	
	echo "<p style='margin: 64px'>";
	echo "<div style='text-align: left'>". $oTraslado->Log(). "</div>";
	echo "<p><input class='noimprimir' type='button' onclick='print()' value='Imprimir'></p>";
	echo "</p>";				 
}

switch($modo){

	case "modificar":
	
		if (!Admite("Stocks"))	return false;
		
		$id				= CleanID($_POST["Id"]);
		$Unidades 		= CleanInt($_POST["Unidades"]);
		$PrecioVenta = CleanDinero($_POST["PrecioVenta"]);
		$disponible = ($_POST["Disponible"]=="on");
		$oferta = ($_POST["Oferta"]=="on");
		$stockilimitado = ($_POST["StockIlimitado"]=="on");				
		$stockmin	= CleanInt($_POST["StockMin"]);
		$impuesto = CleanFloat($_POST["Impuesto"]);
		$Motivo = $_POST["Motivo"];
		$AntiguoUnidades = intval($_POST["AntiguoUnidades"]);
		$igualar = ($_POST["igualarPrecios"]=="on");
		
		if ($igualar){
			CambiarPreciosGlobalmente($id,$PrecioVenta);	
		}
		
		
		if ($AntiguoUnidades != $Unidades) {			
			RegistrarMermaManual($id,$AntiguoUnidades,$Unidades,$Motivo);
		}
		
		ModificarArticulo($id,$Unidades,$PrecioVenta,$disponible,
			$oferta,$stockilimitado,$stockmin,$impuesto);		
	
		$ref = $_SESSION["BusquedaReferencia"];
		$local = $_SESSION["BusquedaLocal"];	
		
		$_SESSION["IdUltimoCambioAlmacen"] = $id;
		
		
		if (!$local)
			$local = getSesionDato("IdTienda");
			
		ListarAlmacen($ref,$local,getSesionDato("CarritoTrans"));				
		break;	
	case "editar":
		$id = $_GET["id"];		
		FormularioEditarArticulo($id);
		break;
	case "nosonoferta":
		if (!Admite("Stocks"))	return false;
		
		$marcado = getSesionDato("CarritoTrans");
		$num = MarcarGenerico($marcado,"Oferta=0");
		//	if(isVerbose())
		//	echo gas("nota",_("$num marcados sin oferta"));
		OperacionesConSeleccion();
		ListarSeleccion($marcado);
		break;	
	
	case "nosondisponibles":
		if (!Admite("Stocks"))	return false;
		
		$marcado = getSesionDato("CarritoTrans");
		$num = MarcarGenerico($marcado,"Disponible=0");
		
		
		//if(isVerbose())
		//	echo gas("nota",_("$num marcados como no disponibles"));
		OperacionesConSeleccion(); 		
		ListarSeleccion($marcado);
		break;	
	
	
	case "sondisponibles":
		$marcado = getSesionDato("CarritoTrans");
		$num = MarcarGenerico($marcado,"Disponible=1");
		BusquedaBasica();
		//if(isVerbose())
		//	echo gas("nota",_("$num marcados como disponibles"));
		OperacionesConSeleccion(); 		
		ListarSeleccion($marcado);
		break;	
	case "sonoferta":
		$marcado = getSesionDato("CarritoTrans");
		$num = MarcarGenerico($marcado,"Oferta=1");
	
		//if(isVerbose())
		//	echo gas("nota",_("$num marcados en oferta"));
		OperacionesConSeleccion();
		ListarSeleccion($marcado);
		break;	
		
	case "esobsoleto":
		$marcado = getSesionDato("CarritoTrans");
		$num = MarcarGenericoProducto($marcado,"Obsoleto=1");
	
		//if(isVerbose())
		//	echo gas("nota",_("$num marcados en oferta"));
		OperacionesConSeleccion();
		ListarSeleccion($marcado);
		break;	
		
	case "noobsoleto":
		$marcado = getSesionDato("CarritoTrans");
		$num = MarcarGenericoProducto($marcado,"Obsoleto=0");
	
		//if(isVerbose())
		//	echo gas("nota",_("$num marcados en oferta"));
		OperacionesConSeleccion();
		ListarSeleccion($marcado);
		break;			
				
	case "albaran":
		$IdLocal = CleanID(GET("IdLocal"));
		
		if(!$IdLocal) {
			FormTrasladoSeleccion();	
			break;
		}				
		
		OperacionTrasladoResumida($IdLocal);
		
		
		$_SESSION["CarritoTrans"]= false;
		setSesionDato("PaginadorSeleccionAlmacen",false);
		$_SESSION["CarritoMover"]=false;	
		
		break;	
	case "transsel":
		//Elige destino	
		FormTrasladoSeleccion();		
		break;		
	case "selpagmas": //navegando en la seleccion
	case "selpagmenos":		
		$marcadotrans = getSesionDato("CarritoTrans");
		OperacionesConSeleccion();		 		
		ListarSeleccion($marcadotrans);	
		break;
	case "seleccion": //operar seleccion
		if (isset($_POST["borraseleccion"]) and $_POST["borraseleccion"]){
			$_SESSION["CarritoTrans"]=false;
			BusquedaBasica();							
			$ref = $_SESSION["BusquedaReferencia"];
			$local = $_SESSION["BusquedaLocal"];
			$cb = $_SESSION["BusquedaCB"];
				
			ListarAlmacen($ref,$local,$marcadotrans,$cb);		
		} else {
			//BusquedaBasica();
			$marcadotrans = getSesionDato("CarritoTrans");
			//OperacionesConSeleccion();		 		
			ListarSeleccion($marcadotrans);						
		}							
		break;		
	case "obsoleto_trans": //Agadir un producto al carrito de la 
		$id = CleanID($_GET["id"]);
		$u = CleanInt($_GET["u"]);
		AgnadirCarritoTraspaso($id,$u);
		$marcadotrans = getSesionDato("CarritoTrans");  
		
		BusquedaBasica();
		
		//echo gas("nota",_("Producto seleccionado"));		
		$marcadotrans = getSesionDato("CarritoTrans");
		$ref = $_SESSION["BusquedaReferencia"];
		$local = $_SESSION["BusquedaLocal"];
		$cb = $_SESSION["BusquedaCB"];

		if ($local or $ref or $cb)
			ListarAlmacen($ref,$local,$marcadotrans,$cb);		
		break;	

	case "pagmas": //navegando en el listado almacen
	case "pagmenos":
		//BusquedaBasica();
		$ref = $_SESSION["BusquedaReferencia"];
		$local = $_SESSION["BusquedaLocal"];
		$cb = $_SESSION["BusquedaCB"];
		
		if (!$local)
			$local = getSesionDato("IdTienda");		
							
		if (($local or $ref) or $cb)
			ListarAlmacen($ref,$local,getSesionDato("CarritoTrans"),$cb);
					
		break;	
	case "hacercompra":
		$IdProducto = $_POST["IdProducto"];
		$Cantidad = $_POST["Cantidad"];
		$esHabitual = $_POST["habitual"] == "on";
		
		//RealizarPedido();
		//echo gas("TODO","Cuando trabajemos los albaranes, se continuara por aqui");
		break;	
	case "comprar":
		$IdProducto = $_GET["id"];
		FormularioCompras($IdProducto);
		break;		
	case "buscarproductos":
		setSesionDato("PaginadorAlmacen",0);
	
		$referencia = CleanReferencia(GET("Referencia"));		
		$donde = CleanID(GET("IdLocal"));
		$cb = CleanCB(GET("CodigoBarras"));		
		$completas = (GET("verCompletas")=="on");		
		$nombre = CleanText(GET("Nombre"));
		$soloLlenos = CleanID(GET("soloConStock"));
		$obsoletos = CleanID(GET("mostrarObsoletos"));

		if (intval($donde)<1)
			$donde = false;
			
		if (strlen($referencia)<1)
			$referencia = false;
		
		if (strlen($cb)<1)
			$cb = false;	
			
		if ($referencia) { //buscara para este código de barras.
		
			if ($cb)			
				$id = getIdFromCodigoBarras($cb);
			else {
				$id = getIdFromReferencia($referencia);
			}		
								
			$IdBase = getProdBaseFromId($id); 
			$_SESSION["BusquedaProdBase"] = $IdBase;									
		} 		else {
			$_SESSION["BusquedaProdBase"] = false;
		}				
		
		$_SESSION["BusquedaReferencia"] = $referencia;
		$_SESSION["BusquedaLocal"] = $donde;
		$_SESSION["BusquedaCB"] = $cb;
		$_SESSION["BusquedaNombre"] = $nombre;
		
		$_SESSION["BusquedaSoloLlenos"] = $soloLlenos;
		$_SESSION["BusquedaObsoletos"] = $obsoletos;
		$_SESSION["BusquedaSoloConStock"] = $soloLlenos;
		
		$marcadotrans = getSesionDato("CarritoTrans");  


		//Si no se dice dodne buscar, se busca por defecto en el local actual		
		if (!$donde)
			$donde = getSesionDato("IdTienda");
		
		BusquedaBasica();	
		if (($referencia or $donde) or ($cb or $nombre))
			ListarAlmacen($referencia,$donde,$marcadotrans,$cb,false,$soloLlenos,$obsoletos);
		else
			echo gas("Aviso",_("No especifico opciones de búsqueda"));		
		break;
	case "borrarseleccion":
		setSesionDato("PaginadorSeleccionAlmacen",false);
		$_SESSION["CarritoTrans"]= false;		
		$_SESSION["CarritoMover"]=false;	
		
		
		$ref = $_SESSION["BusquedaReferencia"];
		$local = $_SESSION["BusquedaLocal"];
		$cb = $_SESSION["BusquedaCB"];
		
		if ($local or $ref or $cb)
			ListarAlmacen($ref,$local,false, $cb);	
		break;	
	default:
		
		$ref = $_SESSION["BusquedaReferencia"];
		$local = $_SESSION["BusquedaLocal"];
		$cb = $_SESSION["BusquedaCB"];
		$soloLlenos = $_SESSION["BusquedaSoloConStock"];
		
		
		$marcadotrans = getSesionDato("CarritoTrans");  

		//Si no se dice donde buscar, se busca por defecto en el local actual		
		if (!$local)
			$local = getSesionDato("IdTienda");

		if ($local or $ref or $cb) {
			ListarAlmacen($ref,$local,$marcadotrans, $cb,false,$soloLlenos);			
		}	
		
		//echo "<!-- id:".getSesionDato("IdTienda")."  local:".$local."  -->";
					
		break;		
}


PageEnd();

?>
