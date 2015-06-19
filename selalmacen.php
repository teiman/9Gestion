<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

function FormularioEntrada($local) {
	global $action;
	$ot = getTemplate("SeleccionRapidaAlmacen");
			
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template busqueda no encontrado");
		return false; }
		
	$ot->fijar("action", $action . "?modo=agnade");
	$ot->fijar("IdLocal",$local);	
				
	echo $ot->Output();					
}


function getIdFromAlmacen($IdProducto,$local) {
	$IdProducto = CleanID($IdProducto);
	$local = CleanID($local);
	$sql = "SELECT Id FROM ges_almacenes WHERE (IdLocal='$local') AND (IdProducto='$IdProducto')";
	
	$row = queryrow($sql);
	if (!$row)
		return false;
	
	return $row["Id"];	
}


function AgnadirCodigoCarrito($cb,$local) {
	$id = getIdFromAlmacen($cb,$local);
	
	if (!$id)
		return false;
		
	AgnadirCarritoTraspaso($id);
	return true;
}


switch($modo){
	case "agnademudo_almacen":
		$listacompra = $_POST["listacompra"];
		$idlocal = CleanID($_POST["IdLocal"]);
		$nuevos = 0;
		foreach (split("\n",$listacompra) as $cb ){
			$cb = CleanCB($cb);	
			$id = getIdFromCodigoBarras($cb);
			if($id)	{
				$nuevos++;
				AgnadirCodigoCarrito($id,$idlocal);				
			}					
		}	
		echo CleanParaWeb(_("Añadidos $nuevos productos al carrito"));		
		exit();
		break;
	case "agnademudo_compras":
		$listacompra = $_POST["listacompra"];		
		
		$num = 0;
		$nuevos = 0;
		foreach (split("\n",$listacompra) as $cb ){
			$cb = CleanCB($cb);	
			$id = getIdFromCodigoBarras($cb);
			$num ++;
			if($id)	{
				AgnadirCarritoCompras($id,1);	
				$nuevos ++;			
			}				
		}	
				
		echo CleanParaWeb(_("Añadidos $nuevos productos al carrito"));
			
		exit();
		break;		
}

PageStart();

switch($modo){
	case "agnadeuna":
		$id = CleanID($_GET["id"]);//Id en almacen
		$u = intval($_GET["u"]);//Unidades	
		
		if ($id)
			AgnadirCarritoTraspaso($id);
				
		echo "<script> 
			window.close();
			</script>";				
		break;
	case "agnade":
		$listacompra = $_POST["listacompra"];
		$idlocal = CleanID($_POST["IdLocal"]);
		
		foreach (split("\n",$listacompra) as $cb ){
			$cb = CleanCB($cb);	
			$id = getIdFromCodigoBarras($cb);
			if($id)	{
				AgnadirCodigoCarrito($id,$idlocal);				
			}					
		}		

		echo "<script> 
				//opener.location.href='modalmacenes.php';
				if (opener.solapa)
					opener.solapa('modalmacenes.php?modo=refresh');
				window.close();
			</script>";				
		break;	
	default:
		$local = $_GET["IdLocal"];
		FormularioEntrada($local);
		break;	
}

PageEnd();
 
?>
