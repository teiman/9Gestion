<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

$tamPagina = 25;

function ListarProductos($idprov,$idmarca,$idcolor,$idtalla,$seleccion,$idprod,$idbase,$nombre=false,$ref=false,$cb=false,$obsoletos=false){	
	global $action;
	$oProducto = new producto;

	
	$base = $idbase;
	
	//$idprod???	
	 
	$indice = getSesionDato("PaginadorCompras");
	$tamPagina = 100;
	
	$hayProductos = $oProducto->ListadoFlexible($idprov,$idmarca,$idcolor,$idtalla,false,$indice,$base,false,
			false,$tamPagina	,$ref,$cb,$nombre,$obsoletos=false	);

	$num = 0;
	
	$jsOut = "";
	$jsLex = new jsLextable;

	$jsOut .= jsLabel("color",_("Color"));	
	$jsOut .= jsLabel("talla",_("Talla"));
	$jsOut .= jsLabel("comprar",_("Comprar"));
	//$jsOut .= jsLabel("modificar",_("Modificar"));
	//$jsOut .= jsLabel("referencia",_("Referencia"));
	$jsOut .= jsLabel("unid",_("Unid"));
	$jsOut .= jsLabel("pv",_("PV"));
	$jsOut .= jsLabel("nuevatallacolor",_("Nueva talla o color"));

	
	$oldId = -1;
	$num =0;		
	while ($oProducto->SiguienteProducto()){

			$num++;	
			$id = $oProducto->getId();							
			$nombre = $oProducto->getNombre();
			$ref = $oProducto->getReferencia();
			$talla = getIdTalla2Texto( $oProducto->get("IdTalla"));
			$color = getIdColor2Texto( $oProducto->get("IdColor"));
						
			$lextalla = $jsLex->add($talla);
			$lexcolor = $jsLex->add($color);
			
			$fam = getIdFamilia2Texto( $oProducto->get("IdFamilia"));
			$sub = getIdSubFamilia2Texto($oProducto->get("IdFamilia"), $oProducto->get("IdSubFamilia"));
			

			$lexfam = $jsLex->add($fam);
			$lexsub = $jsLex->add($sub);
				
		$idBase = $oProducto->get("IdProdBase");
		if ($idBase != $oldId) {
			$ref = addslashes($ref);
			$nombre = addslashes($nombre);
			$jsOut .= "cLH($id,'$nombre','$ref',$lexfam,$lexsub);\n";
		}
		$jsOut .= "cL($id,$lextalla,$lexcolor);\n";
		$oldId = $idBase;							
	}	
	
	$jsOut = $jsLex->jsDump() . $jsOut;
	
	$paginador = jsPaginador($indice,100,$num);
	$jsOut .= $paginador;	
	$jsOut .= "cListProductos();";	
	$jsOut .= $paginador;
	
		
	echo "<center>";
	echo jsBody($jsOut);
	echo "</center>";					
	
}

function old_ListarProductos($idprov,$idmarca,$idcolor,$idtalla,$seleccion,$idprod,$idbase) {
	//OBSOLETO
}



function PaginaBasica(){
	$actual = getSesionDato("CarritoCompras");
	$idprov = getSesionDato("FiltraProv");
	$idmarca = getSesionDato("FiltraMarca");
	$idcolor = getSesionDato("FiltraColor");
	$idtalla = getSesionDato("FiltraTalla");
	$idprod = getSesionDato("FiltraIdProducto");
	$idbase = getSesionDato("FiltraBase");	
	$nombre = getSesionDato("FiltraNombre");
	$obsoletos = getSesionDato("FiltraObsoletos");
	$ref = getSesionDato("FiltraReferencia");
	$cb = getSesionDato("FiltraCB");
	
	//echo q($idcolor,"color a mostrar");

	ListarProductos($idprov,$idmarca,$idcolor,$idtalla,$actual,$idprod,$idbase,$nombre,$ref,$cb,$obsoletos);
	//OperacionesConProductos();	
}


function QuitarDeCarritoCompras($id){
	$actual = getSesionDato("CarritoCompras");
	$cantidad = getSesionDato("CarroCostesCompra");
	
	$actual[$id] = false;
	$cantidad[$id] = false;
				
	setSesionDato("CarritoCompras",$actual);	
	setSesionDato("CarroCostesCompra",$cantidad);
}

function ListarOpcionesSeleccion(){
	echo gas("titulo",_("Operaciones sobre la selección"));
	echo "<table border=1>";
	echo "<tr><td>"._("Hacer una compra a proveedores")."</td><td>".gModo("comprar",_("Comprar"))."</td></tr>";
	echo "<tr><td>"._("Buscar en el almacén")."</td><td>".gModo("transsel",_("Buscar"))."</td></tr>";
	//echo "<tr><td>"._("Cambio global de precio")."</td><td>".gModo("preciochange",_("Precios"))."</td></tr>";
	echo "</table>";	
}



function ListaFormaDeUnidades() {
	
	//Se usa esto aqui?
	//FormaListaCompraCantidades	
	global $action;
	$oProducto = new producto; 
	
	$ot = getTemplate("FormaListaCompraCantidades");
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template no encontrado");
		return false; }

	$ot->resetSeries(array("IdProducto","Referencia","Nombre",
				"tBorrar","tEditar","tSeleccion","vUnidades"));
	
	$tamPagina = $ot->getPagina();
	
	$indice = getSesionDato("PaginadorSeleccionCompras2");			
	$carrito = getSesionDato("CarritoCompras");
	$costescarrito = getSesionDato("CarroCostesCompra");
	
	$quitar = _("Quitar");
	$ot->fijar("tTitulo",_("Carrito de compra"));
	//$ot->fijar("comboAlmacenes",getSesionDato("ComboAlmacenes"));
	
	$DestinoAlmacen = getSesionDato("DestinoAlmacen");	
	if (!$DestinoAlmacen)		$DestinoAlmacen = getParametro("AlmacenCentral");
	$ot->fijar("comboAlmacenes",genComboAlmacenes($DestinoAlmacen));
	
	$salta = 0;
	$num = 0;
	foreach ( $carrito as $key=>$value){		
		$salta ++;
		if ($num <= $tamPagina and $salta>=$indice){		
			$num++;			
		
			if ($oProducto->Load($key)) {
				$referencia = $oProducto->getReferencia();
				$nombre 	= $oProducto->getNombre();	
			} else {
				$referencia = "";
				$nombre = "";			
			}
			$ot->fijarSerie("vReferencia",$referencia);		
			$ot->fijarSerie("vNombre",$nombre);
			$ot->fijarSerie("tBorrar",$quitar);
			$ot->fijarSerie("vUnidades",$value);
			$ot->fijarSerie("vPrecio",$costescarrito[$key]);
			$ot->fijarSerie("IdProducto",$oProducto->getId());
		}
	}
	$ot->paginador($indice,false,$num);	
	$ot->fijar("action",$action );
	$ot->terminaSerie();
	
	echo $ot->Output();	
}

function ActualizarCantidad($Id, $UnidNew, $PrecioNew){
	if (!$Id or $Id=="")
		return;
	
	$data = getSesionDato("CarritoCompras");
	$data2 = getSesionDato("CarroCostesCompra");
			
	$data[$Id] = $UnidNew;
	$data2[$Id] = $PrecioNew;
		
	setSesionDato("CarritoCompras",$data);		
	setSesionDato("CarroCostesCompra",$data2);
}


function ActualizarAlmacen(){
	$IdLocal = CleanID($_POST["IdLocal"]);
	if ($IdLocal)
		setSesionDato("DestinoAlmacen",$IdLocal);							
}

function ReseleccionarLocal() {
	global $action;
	
	$ot = getTemplate("ElijeLocalCompra");
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template no encontrado");
		return false; }

	$ot->fijar("tTitulo",_("Elije local destino"));
	$ot->fijar("comboAlmacenes",getSesionDato("ComboAlmacenes"));
	
	$ot->fijar("action",$action);	
	echo $ot->Output();
}




function VaciarPedidosBasedatos() {
	if (!isUsuarioAdministradorWeb())
		return;
	query("DELETE FROM ges_pedidos");
	query("DELETE FROM ges_compras");
}


function CreardeCeroCarro() { 
		$unidades = array();
		$precios = array();
		$carro = getSesionDato("CarritoCompras");
		foreach ($carro as $key=>$value){			
			$unidades[$key]=0;
			$precios[$key]=0;
		}	
		setSesionDato("CarritoCompras",$unidades);
		setSesionDato("CarroCostesCompra",$precios);
}


PageStart();

//echo gas("cabecera",_("Compras"));


//Paginadores
switch($modo){		

		case "buscarproductos":
			//QuitarFiltrosAvanzados();
			setSesionDato("FiltraCB",false);	
			setSesionDato("FiltraIdProducto",false);			
			setSesionDato("FiltraReferencia",false);
			setSesionDato("FiltraNombre",false);
			setSesionDato("FiltraObsoletos",false);
			
			setSesionDato("PaginadorCompras",0);
	
			$referencia = CleanReferencia($_GET["Referencia"]);		
			$donde 		= CleanID($_GET["IdLocal"]);
			$cb 		= CleanCB($_GET["CodigoBarras"]);		
			$completas 	= ($_GET["verCompletas"]=="on");		
			$nombre 	= CleanText($_GET["Nombre"]);
			$obsoletos 	= CleanID($_GET["Obsoletos"]);
		
			if (strlen($referencia)<1)
				$referencia = false;
		
			if (strlen($cb)<1)
				$cb = false;	
			
			/*
			if ($cb){
				$id = getIdFromCodigoBarras($cb);	
				setSesionDato("FiltraIdProducto",$id);					
			}*/		
					

			if ($cb)
				setSesionDato("FiltraCB",$cb);	
			/*
			if ($ref){
				$id = getIdFromReferencia($ref);
				setSesionDato("FiltraIdProducto",$id);		
			}*/
					
			setSesionDato("FiltraReferencia",$referencia);			
			setSesionDato("FiltraNombre",$nombre);
			setSesionDato("FiltraObsoletos",$obsoletos);
			
			PaginaBasica();
			break;
		
		case "buscaporcb":
			$cb = CleanCB($_POST["CodigoBarras"]);
			if (!$cb)
				$cb = CleanCB($_GET["CodigoBarras"]);
			
			$completas = ($_POST["verCompletas"]=="on");
			
			$id = getIdFromCodigoBarras($cb);				
			
			
			if ($id) {
				if ($completas) {
					$base = getProdBaseFromId($id);		
					setSesionDato("FiltraBase",$base);	
				} else {										
					setSesionDato("FiltraIdProducto",$id);
				}
			} else {
				setSesionDato("FiltraBase",false);	
				setSesionDato("FiltraIdProducto",false);				
			}
			setSesionDato("FiltraNombre",false);
			
			PaginaBasica();
			
			break;
			
		case "mostrar":
		
		$reset = false;
		$id =  CleanID($_GET["IdProveedor"]);
		if ($id != getSesionDato("FiltraProv") ) {
			setSesionDato("FiltraProv",$id);
			$reset = true;
		}
			
		$id =  CleanID($_GET["IdTalla"]);			
		if ($id != getSesionDato("FiltraTalla")) {
			setSesionDato("FiltraTalla",$id);
			$reset = true;
		}

		$id =  intval($_GET["IdColor"]);
		//echo q($id,"color leido");								
		if ($id != getSesionDato("FiltraColor")) {
			setSesionDato("FiltraColor",$id);
			$reset = true;			
			//echo q($id,"nuevo color");
		}
		
		$id =  CleanID($_GET["IdMarca"]);
		if ($id != getSesionDato("FiltraMarca")) {
			setSesionDato("FiltraMarca",$id);
			$reset = true;			
		}		
				
		setSesionDato("FiltraBase",false);
		setSesionDato("FiltraIdProducto",false);
		setSesionDato("FiltraNombre",false);
						
		if ($reset) {
			setSesionDato("PaginadorCompras",0);//Puede haber ahora muchos menos
		}						
				
		PaginaBasica();
		break;

				
	case "spagmenos":
		$indice = getSesionDato("PaginadorSeleccionCompras");
		$indice = $indice - $tamPagina;
		if ($indice<0)
			$indice = 0;
		setSesionDato("PaginadorSeleccionCompras",$indice);
		PaginaBasica();
		break;	
	case "spagmas":
		$indice = getSesionDato("PaginadorSeleccionCompras");
		$indice = $indice + $tamPagina;
		setSesionDato("PaginadorSeleccionCompras",$indice);
		PaginaBasica();
		break;			
	case "pagmenos":
		$indice = getSesionDato("PaginadorCompras");
		$indice = $indice - $tamPagina;
		if ($indice<0)
			$indice = 0;
		setSesionDato("PaginadorCompras",$indice);
		PaginaBasica();
		break;	
	case "pagmas":
		$indice = getSesionDato("PaginadorCompras");
		$indice = $indice + $tamPagina;
		setSesionDato("PaginadorCompras",$indice);
		PaginaBasica();
		break;				
    
	case "agnadircb":
		$cb = CleanCB($_GET["CodigoBarras"]);		
		$id = getIdFromCodigoBarras($cb);
		
		if ($id) {
			AgnadirCarritoCompras($id);			
			if (isVerbose())	  					
				echo gas("nota",_("Producto seleccionado ($id)"));
		} else {		
			if (isVerbose())
				echo gas("nota",_("Producto no encontrado"));
		}		
		PaginaBasica();		
		break;		
		
    case "agnadirref":
	case "agnadirporreferencia":
		$ref = CleanReferencia($_GET["referencia"]);		
		$id = BuscaProductoPorReferencia($ref);
		
		if ($id) {
			AgnadirCarritoCompras($id);			
			if (isVerbose())	  					
				echo gas("nota",_("Producto seleccionado ($id)"));
		} else {		
			if (isVerbose())
				echo gas("nota",_("Producto no encontrado"));
		}		
		PaginaBasica();		
		break;		
	case "vaciarpedidos":
		VaciarPedidosBasedatos();
		break;	
	case "ajustarcantidades":		
		ActualizarAlmacen();
		ActualizarCantidades();
		if(isVerbose())
			echo gas("aviso","cantidades actualizadas");
		ListaFormaDeUnidades();
		break;
	
	case "comprarPaso3":
		ActualizarAlmacen();
		ActualizarCantidades();
		//echo gas("aviso","comprando...");
		
		$IdLocal = getSesionDato("DestinoAlmacen");
		if ( $IdLocal and $IdLocal!="nada" ) {		
			$idOrden = CrearOrdenDeCompra($IdLocal);
			EjecutaRecepcionarPedido($idOrden);
			ResetearCarritoCompras();//Vaciamos carrito, pues fue ejecutado
			//Separador();			
			echo gas("aviso",_("Compra terminada"));							
		}
		else {
			ReseleccionarLocal();	
		} 		
		break;	
	case "borrarpaso2": //Desseleccionar articulo
		ActualizarAlmacen();
		ActualizarCantidades();
				
		$id = CleanID($_GET["id"]);		
		QuitarDeCarritoCompras($id);
		if (isVerbose())
			echo gas("nota",_("Producto sacado de carrito"));						
	case "continuarCompra":
//.... antes se creaba el carro aqui
		$id = CleanID(GET("IdLocal"));
		if ($id) {
			setSesionDato("DestinoAlmacen",$id);
		}		
	case "editarCompra":
		ListaFormaDeUnidades();
		break;		
	case "filtrarproveedor":
		$id= CleanID($_GET["IdProveedor"]);
				
		if ($id) {
			setSesionDato("CompraProveedor",$id);
			setSesionDato("PaginadorCompras",0);//Puede haber ahora muchos menos
		}		
		
		//Reseteamos carrito (no queremos mezclar productos de diferentes proveedores
		//setSesionDato("CarritoCompras",false);
		
		
				
		PaginaBasica();
		break;		

	case "desselec": //Desseleccionar articulo
		
		$id = CleanID($_GET["id"]);
		
		QuitarDeCarritoCompras($id);
		
		if (isVerbose()) echo gas("nota",_("Producto sacado de carrito"));		

		PaginaBasica();							
		
		break;
	case "selec"://Seleccion articulo
		$id = CleanID($_GET["id"]);
		AgnadirCarritoCompras($id);
				  		
		if (isVerbose()) echo gas("nota",_("Producto seleccionado"));		

		PaginaBasica();							
			
		break;
	case "borrar":
		$id = CleanID($_GET["id"]);
		
		if (!productoEnAlmacen($id)){		
			BorrarProducto($id);
		} else {
			echo gas("nota",_("No se puede borrar porque aun hay existencias. Primero vacié en almacenes.") );
		} 				
		Separador();
		PaginaBasica();	
		break;	

	default:
		if(strlen($modo)>0) {
			if (isVerbose())
				echo "<br>No se capturo el evento '$modo'<br>";
		}
		PaginaBasica();
				
		break;		
}

PageEnd();

?>
