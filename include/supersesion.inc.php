<?php

// El objetivo de este modulo es cachear informacion
// en la sesion sin que sea necesario repetir busquedas



function getParametro($nombre) {
	$params = getSesionDato("Parametros");
	if (!is_array($params)){
		error(__FILE__ . __LINE__ , "E: lectura fallida de parametros");
		return false;	
	}
	
	return $params[$nombre];	
}



function getSesionDato($nombre){
	global $debug_mode;
	
	switch($nombre){
		case "CarritoProd":
		case "CarritoTrans":
			if (!is_array($_SESSION[$nombre]))
				$_SESSION[$nombre]=array();
			return $_SESSION[$nombre];

		
		case "CarritoMover":
		case "PerfilActivo":
		case "CarroCostesCompra":
		case "CarritoCompras":
			//Esta mal pero funciona (?) y si lo arreglas deja de funcionar (?!)		
			if (is_array($_SESSION[$nombre]))
				$_SESSION[$nombre]=array();
				
			return unserialize($_SESSION[$nombre]);		
		
		case "Parametros":		
			if (isset($_SESSION[$nombre])){
				return $_SESSION[$nombre];
			}		
		
			$row = queryrow("SELECT * FROM ges_parametros","Cargando parametros");
			$_SESSION[$nombre] = $row;
			
			return $row;						
		
		case "IdLenguajeDefecto": //Idioma para productos en altas, bajas, etc...
		
			if (isset($_SESSION[$nombre])){
				return $_SESSION[$nombre];
			}		
			
			$lang = getIdFromLang("es");
			$_SESSION[$nombre] = $lang;
			return $lang;
	
		case "IdLenguajeInterface": //Idioma del usuario
			//TODO:
			// leer del usuario 	
				
			return getSesionDato("IdLenguajeDefecto");

		case "ComboAlmacenes":			
			if ($_SESSION[$nombre]){
				return $_SESSION[$nombre];
			}
		
			$out = genComboAlmacenes();
			$_SESSION[$nombre] = $out;
			return $out;						
		case "Almacen":		
			return new almacenes; //obsoleto
		case "Articulos":		
			return new articulo;	
		case "AlmacenCentral":
			$local = new local;
			if ($local->LoadCentral()){
				return $local;	
			}
			error(__FILE__ . __LINE__ , "E: no pudo cargar el almacén central");
			return false;				
		case "ArrayTiendas":
			if ($_SESSION["ArrayTiendas"]){
				return $_SESSION["ArrayTiendas"];
			}
			
			$alm = new almacenes;
			$arrayTodos = array_keys($alm->listaTodosConNombre());

			$_SESSION["ArrayTiendas"] = $arrayTodos;
			return $arrayTodos;
	
		case "hayCarritoCompras":
			if (!isset($_SESSION["CarritoCompras"])){
				return false;
			}
			$val = $_SESSION["CarritoCompras"];
			if(!is_array($val) and count($val) ){
				return false;
			}
			return true;	
		case "hayCarritoTrans":
			if (!isset($_SESSION["CarritoTrans"])){
				return false;
			}
			$val = $_SESSION["CarritoTrans"];
			if(!is_array($val) and count($val)){
				return false;
			}
			
			if ($val==0 or $val == array())
				return false;
				
			if (count($val)==0)
				return false;
			
			return true;
	
		case "hayCarritoProd":
			if (!isset($_SESSION["CarritoProd"])){
				return false;
			}
			$val = $_SESSION["CarritoProd"];
			if(!is_array($val) and count($val)){
				return false;
			}
			return true;
			
		case "hayCarritoFam":
			if (!isset($_SESSION["CarritoFam"])){
				return false;
			}
			$val = $_SESSION["CarritoFam"];
			if(!is_array($val) and count($val)){
				return false;
			}
			return true;
	
		case "PaginadorCliente":	
		case "PaginadorSeleccionCompras2":	
		case "PaginadorSeleccionCompras":
		case "PaginadorCompras":
		case "PaginadorProv":
		case "PaginadorListaProv":				
		case "PaginadorAlmacen":
		case "PaginadorListaProd":		
		case "PaginadorSeleccionAlmacen":
		case "PaginadorListaFam":
		case "PaginadorListaSubFam":
			return intval($_SESSION[$nombre]);
														
		default:	
			return $_SESSION[$nombre];	
	}		
	
}

function invalidarSesion($clase) {
	
	switch($clase){
		case "ListaTiendas":
			$_SESSION["ArrayTiendas"] = false;
			$_SESSION["ComboAlmacenes"] = false;
			break;
		default:
			$_SESSION[$clase] =false;	
	} 
		
	
}


function setSesionDato($dato,$valor) {	
	global $_SESSION;
	
	if (is_object($valor)){
	 	$_SESSION[$dato] = serialize($valor);
	 	return;		
	}
	
	switch($dato){
		case "PerfilActivo":
		case "CarritoMover":
		case "CarroCostesCompra":
		case "CarritoCompras":
		$_SESSION[$dato] = serialize($valor);
		return;		
	}
	
	$_SESSION[$dato] = $valor;
}











?>
