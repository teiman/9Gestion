<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

$tamPagina = 10;

function ListaFormaDeUnidades() {
	//FormaListaCompraCantidades	
	global $action;
	$jsOut = "";
	
	$oProducto = new producto; 
	
	$ot = getTemplate("PopupCarritoCompra");
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template no encontrado");
		return false; }

	$ot->resetSeries(array("IdProducto","Referencia","Nombre",
				"tBorrar","tEditar","tSeleccion","vUnidades","vTalla","vColor"));
	
	$tamPagina = $ot->getPagina();
	
	$indice = getSesionDato("PaginadorSeleccionCompras2");			
	$carrito = getSesionDato("CarritoCompras");

	//echo q($carrito,"Carrito Cantidades");
	
	
	$costescarrito = getSesionDato("CarroCostesCompra");
	
	$quitar = _("Quitar");
	$ot->fijar("tTitulo",_("Carrito de compra"));
	//$ot->fijar("comboAlmacenes",getSesionDato("ComboAlmacenes"));
	$ot->fijar("comboAlmacenes",genComboAlmacenes(getParametro("AlmacenCentral")));
	
	$salta = 0;
	$num = 0;
	if ($carrito){
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
				
				$ot->fijarSerie("vTalla",$oProducto->getTallaTexto());		
				$ot->fijarSerie("vColor",$oProducto->getColorTexto());		
				$ot->fijarSerie("vReferencia",$referencia);		
				$ot->fijarSerie("vNombre",$nombre);
				$ot->fijarSerie("tBorrar",$quitar);
				$ot->fijarSerie("vUnidades",$value);
				$ot->fijarSerie("vPrecio",$costescarrito[$key]);
				$ot->fijarSerie("IdProducto",$oProducto->getId());
			}
		}
	}
	
	if (!$salta){
		$ot->fijar("aviso",gas("aviso",_("Carrito vacío")));
		$ot->eliminaSeccion("haydatos");			
	} else {
		$ot->fijar("aviso");
		$ot->confirmaSeccion("haydatos");
	}
	
	$jsOut .= jsPaginador($indice,$ot->getPagina(),$num);
	
	$ot->fijar("CLIST",$jsOut );
	
	
	$ot->paginador($indice,false,$num);	
	$ot->fijar("action",$action );
	$ot->terminaSerie();
	
	echo $ot->Output();	
}

function ListadoModificableImpresionPorLote(){
	$oProducto = new producto; 

	$carrito 		= getSesionDato("CarritoCompras");
	$costescarrito 	= getSesionDato("CarroCostesCompra");
	//$etiquetascarro = getSesionDato("etiquetascarro");
	
	echo "<center><form method='post' action='$action?modo=impresionMultipleEjecutar'>";
	echo "<table class='listado' width='50%'><tbody>";
	echo "<tr>".
					"<td class='lh'></td>".
					"<td class='lh' width='10%'><nobr>". _("Número de etiquetas")."</nobr></td>".
					"<td class='lh'><nobr>". _("Nombre")."</nobr></td>".
					"<td class='lh'>PV</td>".
					"<td class='lh'>"._("Talla")."</td>".
					"<td class='lh'>"._("Color")."</td>".
					"</tr>";							



	$etiquetaIdProducto = array();
	$serie = 0;
	foreach ($carrito as $key=>$value){
			if ($oProducto->Load($key)) {
				$referencia = $oProducto->getReferencia();
				$nombre 	= $oProducto->getNombre();	
				$IdProducto = $key;
				
				$precio = getPrecioGenerico($IdProducto);
				if ($precio>0){
					$serie++;							
					echo "<tr class='f'>".
					"<td width='16'><img src='img/producto16.png'></td>".
					"<td  width='10%'><input type='text' name='Unidades_$serie' value='$value'>".
					"<input type='hidden' name='Serie_IdProducto_$serie' value='".$IdProducto."'></td>".
					"<td class='nombre'>$nombre</td>".
					"<td class='precio'><nobr>".CleanParaWeb(FormatMoney($precio))."</nobr></td>".
					"<td class='talla'>".CleanParaWeb($oProducto->getTextTalla())."</td>".
					"<td class='color'>".CleanParaWeb($oProducto->getTextColor())."</td>".
					"<input type='hidden' name='Serie_Precio_$serie' value='".($precio*1)."'>".
					"</td>".					
					"</tr>";	
				} 
			}			
	}		
	if ($serie>0){
		echo "<tr class='f'><td></td><td></td><td colspan='4'><input type='submit' value='"._("Imprimir múltiple")."'></td></tr>";
	} else {
		echo "<tr class='f'><td></td><td></td><td colspan='4'>"._("No se encontraron productos listos para etiquetar.")."</td></tr>";
	}
	echo "</tbody></table>";	
	echo "<input type='hidden' name='numSeries' value='$serie'>";
	echo "</form></center>";
}


function getPrecioGenerico($IdProducto){
	$sql = "SELECT PrecioVenta FROM ges_almacenes WHERE IdProducto = '$IdProducto' AND PrecioVenta>0";
	$row = queryrow($sql);
	
	if(!$row)
		return 0;
	
	return $row["PrecioVenta"];
}

function RecepcionarImpresionPorLote(){
	$unidadesSerie 	= array();
	$preciosSerie 	= array();
	$idProductoSerie = array();
	
	$numSeries = CleanInt($_POST["numSeries"]);
	for($t=0;$t<=$numSeries;$t++){
		if (isset($_POST["Unidades_$t"])){
			$IdProducto					= CleanInt($_POST["Serie_IdProducto_$t"]);	
			$Unidades 					= CleanInt($_POST["Unidades_$t"]);
			$unidadesSerie[$IdProducto] = $Unidades;			
			$preciosSerie[$IdProducto] 	= CleanFloat($_POST["Serie_Precio_$t"]);			
		}	
	}
	
	foreach ($unidadesSerie as $IdProducto=>$unidades){
		$precio = $preciosSerie[$IdProducto] ;
		if ($precio>0){
			//echo "$IdProducto pedido imprimir con $unidades y precio $precio<br>";
			for($t=0;$t<$unidades;$t++){
				GenEtiqueta($IdProducto,$precio);
			}
		}
	}
	echo "<script>window.print()</script>";
}

PageStart();

switch($modo){
	case "impresionMultipleEjecutar":		
		RecepcionarImpresionPorLote();
	
		break;
	case "imprimirtodas":
		ListadoModificableImpresionPorLote();
	
	
		break;
	case "noseleccion":	
	case "noselecion":		
		//Reseteamos carrito y su paginador
		ResetearCarritoCompras();

		ListaFormaDeUnidades();
		break;
		
	case "pagmenos":
		ActualizarCantidades();
		$indice = getSesionDato("PaginadorSeleccionCompras2");
		$indice = $indice - $tamPagina;
		if ($indice<0)
			$indice = 0;
		setSesionDato("PaginadorSeleccionCompras2",$indice);
		ListaFormaDeUnidades();
		break;	
	case "pagmas":
		ActualizarCantidades();
		$indice = getSesionDato("PaginadorSeleccionCompras2");
		$indice = $indice + $tamPagina;
		setSesionDato("PaginadorSeleccionCompras2",$indice);
		ListaFormaDeUnidades();
		break;		
	case "guardarcambios":
	
		ActualizarCantidades();
		ListaFormaDeUnidades();	
		break;	
	default:
	case "check":
	ListaFormaDeUnidades();
	break;	
}


PageEnd();


?>
