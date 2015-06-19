<?php

define("TALLAJE_VARIOS",5);
define("TALLAJE_VARIOS_TALLA",4);

function AccionesTrasAlta(){
	global $action;
	$ot = getTemplate("AccionesTrasAlta");
			
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template busqueda no encontrado");
		return false; }
	
	$IdProducto = getSesionDato("UltimaAltaProducto");
				
	$ot->fijar("IdProducto", $IdProducto);
	
	//$ot->fijar("tEnviar" , _("Enviar"));
	$ot->fijar("action", $action);	
				
	echo $ot->Output();												
}

function AltaDesdePostProducto($esMudo=false) {
	
		$nombre 		= CleanText($_POST["Nombre"]);			
		$referencia 	= CleanReferencia($_POST["Referencia"]);
		$descripcion 	= CleanText($_POST["Descripcion"]);
		
		$precioventa 	= CleanDinero($_POST["PrecioVenta"]);
		$precioonline 	= CleanDinero($_POST["PrecioOnline"]);
		$coste 			= CleanDinero($_POST["CosteSinIVA"]);
		$idfamilia 		= CleanID($_POST["IdFamilia"]);
		$idsubfamilia 	= CleanID($_POST["IdSubFamilia"]);
		$idprovhab 		= CleanID($_POST["IdProvHab"]);			
		if (!isset($_POST["IdProvHab"])){		
			$idprovhab 		= CleanID($_POST["ProvHab"]);
		}				
		$codigobarras 	= CleanCB($_POST["CodigoBarras"]);
		$refprovhab 	= CleanReferencia($_POST["RefProv"]);
		if (!isset($_POST["RefProv"])){
			$refprovhab 	= CleanReferencia($_POST["RefProvHab"]);
		}
	
		$idcolor 	= CleanID($_POST["IdColor"]);
		$idtalla 	= CleanID($_POST["IdTalla"]);
		$idmarca 	= CleanID($_POST["IdMarca"]);
		if (!isset($_POST["IdMarca"])){
			$idmarca = CleanID($_POST["Marca"]);
			if ($idmarca<1){
				$idmarca = getIdMarcaFromMarca($_POST["Marca"]);
			}
		}		
		
		if ($id = CrearProducto($esMudo,$nombre,$referencia,
				$descripcion, $precioventa,
				$precioonline,$coste,$idfamilia,$idsubfamilia,$idprovhab,
				$codigobarras,$idtalla,$idcolor,
				$idmarca,$refprovhab)) {
					
			if(!$esMudo)
				AccionesTrasAlta();
			return $id;
		} else {
			//INFO: no llega aqui, porque remuestra un formulario erroneo dentro de CrearProducto
			return false;
		} 
}

		
/* LISTADO COMBINADO */


function GetOrdenVacio($arreglo, $posicion=0){
	//Auxiliar.
	// Busca un slot vacio para colocar una talla.
	// Aunque las tallas tienen un orden 
	// este orden puede corromperse, y perderiamos tallas.
	
	if (!isset($arreglo[$posicion])){
		return $posicion;	
	}
	while( isset($arreglo[$posicion])){
		$posicion = $posicion + 1;	 
	}
	
	return $posicion;	
}



function genListadoCruzado($IdProducto,$IdTallaje = false,$IdLang=false){	
	$IdProducto = CleanID($IdProducto);
	$IdTallaje 	= CleanID($IdTallaje);
	
	$out = "";//Cadena de salida
	
	if(!$IdLang)	$IdLang = getSesionDato("IdLenguajeDefecto");
	
	$sql = "SELECT Referencia, IdTallaje FROM ges_productos WHERE IdProducto='$IdProducto' AND Eliminado='0'";
	$row = queryrow($sql);
	if (!$row)	return false;
	
	$tReferencia  = CleanRealMysql($row["Referencia"]);
	
	if(!$IdTallaje)	$IdTallaje = $row["IdTallaje"];
	if(!$IdTallaje) $IdTallaje = 2;//gracefull degradation
	
	$sql = "SELECT  ges_locales.NombreComercial,ges_colores.Color,
		ges_tallas.Talla, SUM(ges_almacenes.Unidades) as TotalUnidades FROM ges_almacenes INNER
		JOIN ges_locales ON ges_almacenes.IdLocal = ges_locales.IdLocal INNER
		JOIN ges_productos ON ges_almacenes.IdProducto =
		ges_productos.IdProducto INNER JOIN ges_colores ON
		ges_productos.IdColor = ges_colores.IdColor INNER JOIN ges_tallas ON
		ges_productos.IdTalla = ges_tallas.IdTalla
		WHERE
		ges_productos.Referencia = '$tReferencia'
		AND
		ges_colores.IdIdioma = 1
		AND ges_locales.Eliminado =0
		GROUP BY ges_almacenes.IdLocal, ges_productos.IdColor, ges_productos.IdTalla
		ORDER BY ges_almacenes.IdLocal, ges_productos.IdColor";
		
	$data 			= array();
	$colores 		= array();
	$tallas 		= array();
	$locales 		= array();
	$tallasTallaje 	= array();
	$listaColores 	= array();
	
	$res = query($sql,"Generando Listado Cruzado");

	while( $row = Row($res) ){
		$color 		= $row["Color"];
		$talla 		= $row["Talla"];		
		$nombre 	= $row["NombreComercial"];
		$unidades 	= CleanInt($row["TotalUnidades"]);
		$colores[$color] = 1;
		$tallas[$talla] = 1;
		$locales[$nombre] = 1;
	
		$num = 0;
		
		//echo "Adding... c:$color,t:$talla,n:$nombre,u:$unidades<br>";
		
		$data[$color][$talla][$nombre] =$unidades;
		
		
	}
		
	$sql = "SELECT Talla,SizeOrden FROM ges_tallas WHERE IdTallaje= '$IdTallaje' AND IdIdioma='$IdLang' AND Eliminado='0'" .
			"	 ORDER BY SizeOrden ASC, Talla ASC";
	$res = query($sql);

	$numtallas =0;
	while($row = Row($res)){
		$orden = intval($row["SizeOrden"]);
		$talla = $row["Talla"];
		$posicion = GetOrdenVacio($tallasTallaje,$orden); 
		$tallasTallaje[$posicion]  = $talla;
		$numtallas++; 
	}
	
	$out .= "<table class='forma'>";
	$num = 0;
		
	/*$out .= "<tr><td class='nombre'>".$tReferencia."</td>";
	
	foreach ($tallasTallaje as $k=>$v) {
		$out .= "<td class='lh' id='talla_$num'>".($v)."</td>";
		$num++;
	}
	$out .= "</tr>";*/
	
	foreach ($locales as $l=>$v2){
		
					
		$out .= "<tr class='f'><td></td><td class='lh' colspan='".($numtallas)."'>".($l)."</td></tr>";	
				 

		$out .= "<tr><td class='nombre'>".$tReferencia."</td>";		
		foreach ($tallasTallaje as $k=>$v) {
			$out .= "<td class='lh' id='talla_$num'>".($v)."</td>";
			$num++;
		}
		$out .= "</tr>";
						 
		foreach ($colores as $c=>$v1){	
			$out .= "<tr class='f'><td class='lh'>".($c)."</td>"; 	
			foreach ($tallasTallaje as $k2=>$t) {
				
				if (isset($data[$c][$t][$l]))
					$u = $data[$c][$t][$l];
				else
					$u = "-";
				$out .= "<td class='unidades' align='center'>" . ($u) . "</td>";		
			}
			$out .= "</tr>";
		}
		
		
		$out .= "<tr><td><font color='white'>-</font></td></tr>\n";
		
		
	}
	$out .= "</table>";
	
	return $out;
}


/* LISTADO COMBINADO */

/* CARRITO DE COMPRA */

function ActualizarCantidades() {
		$data = getSesionDato("CarritoCompras");
		$data2 = getSesionDato("CarroCostesCompra");
		
		for($t=1;$t<200;$t++){
			if (isset($_POST["Id$t"])){
				$id = $_POST["Id$t"];
				$unid =$_POST["Cantidad$t"];
				$coste =$_POST["Precio$t"];
				
				if ($id) {
					$preval = $data[$id];
					$data[$id] = $unid;
					$data2[$id] = $coste;
				}
				
				//echo "Desde data[id]=$preval, para id=$id, cargando Cantidad$t=$valor<br>";
			}			
		}
		setSesionDato("CarritoCompras",$data);		
		setSesionDato("CarroCostesCompra",$data2);
}

/* CARRITO DE COMPRA */

/* BUSQUEDA DE DATOS*/

function getIdProductoFromIdArticulo($id){
	$id = CleanID($id);
	
	if ( isset($_SESSION["tIDALMACEN2IDPRODUCTO_$id"]) and intval($_SESSION["tIDALMACEN2IDPRODUCTO_$id"]) > 0 ) {
		return $_SESSION["tIDALMACEN2IDPRODUCTO_$id"];
	}
	

	$sql = "SELECT IdProducto FROM ges_almacenes WHERE Id = '$id'";
	$row = queryrow($sql);
	
	if (!$row)	return false;
	
	$idprod = $row["IdProducto"];
	
	$_SESSION["tIDALMACEN2IDPRODUCTO_$id"] = $idprod;
	
	return $idprod;		
}

function getIdFromReferencia ($ref){
	if (!$ref)
		return false;
	
	$ref= CleanReferencia($ref);
	return genReferencia2IdProducto($ref);
}

function getProdBaseFromId($id){
	$id = CleanID($id);
	
	$key ="tPRODBASEFROMID_" . $id;
	
	if ( isset($_SESSION[$key]) and intval($_SESSION[$key]) > 0 ) {
		return $_SESSION[$key];
	}
	
	
	$sql = "SELECT IdProdBase FROM ges_productos WHERE IdProducto = '$id'";
	$row = queryrow($sql);
	if (!$row)
		return false;
	
	$_SESSION[$key] = $row["IdProdBase"];
	
	return $row["IdProdBase"];
}

function getCosteDefectoProducto($id) {
	$id = CleanID($id);
	$sql = "SELECT CosteSinIVA FROM ges_productos WHERE IdProducto = '$id'"; 
	$row = queryrow($sql);
	if (!$row) return false;
	
	return $row["CosteSinIVA"]; 	
}

function getIdProveedorFromIdProducto($id){	
	$sql = "SELECT IdProvHab FROM ges_productos WHERE IdProducto='$id' ";
	$row = queryrow($sql);
	
	return $row["IdProvHab"];	
}


function AgnadirCarritoCompras($id,$unidades=1) {
	
	if(!$id)
		return;
	
	$actual = getSesionDato("CarritoCompras");
	$costes = getSesionDato("CarroCostesCompra");
	
	$val = $actual[$id] + $unidades;	
	$actual[$id] = $val;	
	
	if(!$costes[$id]) {
		$costes[$id] = getCosteDefectoProducto($id);
	}
				
	setSesionDato("CarritoCompras",$actual);
	setSesionDato("CarroCostesCompra",$costes);
}

function ProductoFactory($res) {
	if(!$res){
		error(__FILE__ . __LINE__ ,"ERROR en factory");
		return false;	
	}
	
	$row = Row($res);
	if (!is_array($row))
		return false;	
	$id = $row["IdProducto"];
	
	$oProducto = new producto;
		
	if ($oProducto->Load($id))
		return $oProducto;
		
	error(__FILE__ . __LINE__ ,"ERROR no pudo cargar id '$id'");
		
	return false;
}


function CrearProducto($mudo,$nombre,$referencia,
				$descripcion, $precioventa,
				$precioonline,$coste,$idfamilia,$idsubfamilia,$idprovhab,
				$codigobarras,$idtalla,$idcolor,$idmarca,$refprovhab){
	global $action;
	$oProducto = new producto;

	$oProducto->Crea();
	
	if (!$idfamilia)	$idfamilia = getParametro("IdFamiliaDefecto");
	if (!$idsubfamilia)	$idfamilia = getParametro("IdFamiliaDefecto");
	
	$oProducto->setNombre($nombre);
	$oProducto->setReferencia($referencia);	
	$oProducto->setDescripcion($descripcion);
	$oProducto->setLang(getSesionDato("IdLenguajeDefecto"));	
	$oProducto->setPrecioVenta($precioventa);
	$oProducto->setPrecioOnline($precioonline);
	$oProducto->set("CosteSinIVA",$coste,FORCE);
	$oProducto->set("IdFamilia",$idfamilia,FORCE);
	$oProducto->set("IdSubFamilia",$idsubfamilia,FORCE);
	$oProducto->set("IdProvHab",$idprovhab,FORCE);
	$oProducto->set("CodigoBarras",$codigobarras,FORCE);
	$oProducto->set("RefProvHab",$refprovhab,FORCE);			
	
	$oProducto->set("IdTalla",$idtalla,FORCE);
	$oProducto->set("IdColor",$idcolor,FORCE);
	
	$oProducto->set("IdMarca",$idmarca,FORCE);
	
	//		
	if ($oProducto->Alta()){
			
		//Guardamos el id de la ultima alta para procesos posteriores 
		// que quieran usarlo (encadenacion de acciones)
		setSesionDato("UltimaAltaProducto",$oProducto->getId());
		
		//TODO
		// una vez creado el producto, lo vamos a stockar en los almacenes
		// con cantidad cero
		
		$alm = getSesionDato("Almacen");
		
		error(__FILE__ . __LINE__ ,"Infor: Precio aqui es ". $oProducto->getPrecioVenta());
		
		$alm->ApilaProductoTodos($oProducto);
		return $oProducto->getId();
						
	} else {
		setSesionDato("UltimaAltaProducto",false);//por si acaso
		//setSesionDato("FetoProducto",$oProducto);
		if (!$mudo)
			echo $oProducto->formEntrada($action,false);	
		//echo gas("aviso",_("No se ha podido registrar el nuevo producto"));
		return false;
	}
}


function productoEnAlmacen($id) {
	global $FilasAfectadas;
	$sql = "SELECT Id FROM ges_almacenes WHERE Unidades>0 and IdProducto = '$id'";	
	$res = query($sql);
	$num = intval($FilasAfectadas);
	
	if (!$res){
		error(__FILE__ . __LINE__ ,"E: no se pudo contar en almacenes para $sql");
		return true;	
	}		
//	error(0,"Info: num es $num, con sql $sql"); 
	return ($num > 0);		
}

//eliminar uno de los dos

function getIdFromCodigoBarras($cb){
	$cb = CleanCB($cb);	
	if (!$cb or $cb=="")
		return false;
	
	$sql = 	"SELECT IdProducto FROM ges_productos WHERE (CodigoBarras = '$cb')";
	$row = queryrow($sql);
	if (!$row){ 
		return false;
	}
	return $row["IdProducto"];
}

function getCBfromIdProducto($IdProducto) {
	$IdProducto = CleanID($IdProducto);	
	$sql = 	"SELECT CodigoBarras FROM ges_productos WHERE IdProducto = '$IdProducto'";
	$row = queryrow($sql,"Busca CB de producto");
	if (!$row){ 
		return false;
	}
	return $row["CodigoBarras"];
}


function genReferencia2IdProducto($ref){
	
	$sql = 	"SELECT IdProducto FROM ges_productos WHERE (Referencia = '$ref')";
	$row = queryrow($sql);
	if (!$row){
		return false;
	}
	
	$id = $row["IdProducto"];
	
	return $id ;
}

function BuscaProductoPorReferencia($ref){	
	$sql = "SELECT IdProducto FROM ges_productos WHERE (Referencia='$ref')";
	$row = queryrow($sql);
	if ($row){
		return $row["IdProducto"];	
	}	else {
		return false;	
	}
}

/*
    * Tipo Impuesto - Obligatorio - 

    No se Almacena, es indicativo para dar de alta el producto en almacenes. 
    Por defecto se tomara valor "TipoImpuesto" de la tabla "ges_paises". 
    Un nuevo producto toma el tipo por defecto del pais en que esta el almacén central. 
	
	Producto->TipoImpuesto = AlmacenCentral->Pais->TipoImpuesto
	
	* Impuesto - Obligatorio - 

    Se almacena en "ges_productos_idioma". 
    Por defecto se tomara el valor "Impuesto" de la tabla "ges_productos_idioma". 
    Producto->Idioma->Impuesto = ??? Producto->Idioma->Impuesto
    
*/

function getTipoImpuesto($oProducto=false,$local=false) {
		$key = "tIMPUESTOCENTRALTIPO";

		if( isset($_SESSION[$key]))
			return $_SESSION[$key];


		$central = new local;
		if(!$central->LoadCentral())
			return false;
			
		
		$IdPais = CleanID($central->get("IdPais"));
		$sql = "SELECT TipoImpuestoDefecto FROM ges_paises WHERE IdPais='$IdPais'";
		$row = queryrow($sql,"Cargando TIPO impuesto de la central");
		
		if ($row) {
			$val = $row["TipoImpuestoDefecto"];
			$_SESSION[$key] = $val;
			return $val;
		}
			
		return "IVA";	
}

function getValorImpuestoDefectoCentral() {
		$central = new local;
		$key = "tIMPUESTOCENTRAL";

		if( isset($_SESSION[$key]))
			return $_SESSION[$key];	
		
		if(!$central->LoadCentral())
			return false;
	
	
		
		$IdPais = CleanID($central->get("IdPais"));
		$sql = "SELECT ImpuestoDefecto FROM ges_paises WHERE IdPais='$IdPais'";
		$row = queryrow($sql,"Cargando VALOR impuesto de la central");
		
		if ($row) {
			$val = $row["ImpuestoDefecto"];
			$_SESSION[$key] = $val;
			return $val;
		}
			

		return "16";//Si algo falla, se ajusta a 16	
}


function getIdColor2Texto($IdColor, $IdIdioma=false) {
	$IdColor = CleanID($IdColor);
	if (!$IdIdioma)
		$IdIdioma = getSesionDato("IdLenguajeDefecto");
		
	$keyname = "tCOLOR_" . $IdColor;		
	//Cacheamos traduccion de talla en color	
	if ( $_SESSION[$keyname] ) 	return $_SESSION[$keyname];
		
	
	$IdIdioma = CleanID($IdIdioma);		
	$sql = "SELECT Color  FROM ges_colores  WHERE Eliminado=0 AND IdIdioma = '$IdIdioma' AND IdColor = '$IdColor'";
	$row = queryrow($sql);
	if (!$row)		return false;
	
	$_SESSION[$keyname] = $row["Color"];		
	return $row["Color"];	
}


function getIdTalla2Texto($IdTalla, $IdIdioma=false) {
	$IdColor = CleanID($IdTalla);
	if (!$IdIdioma)
		$IdIdioma = getSesionDato("IdLenguajeDefecto");
	
	//Cacheamos traduccion de talla en color	
	if ( $_SESSION["tTALLA_$IdTalla"] ) {
		return $_SESSION["tTALLA_$IdTalla"];
	}		
				
	$IdIdioma = CleanID($IdIdioma);				
	$sql = "SELECT Talla FROM ges_tallas  WHERE Eliminado=0 AND (IdIdioma = '$IdIdioma') AND (IdTalla = '$IdTalla')";
	$row = queryrow($sql);
	if (!$row)
		return false;
	$_SESSION["tTALLA_$IdTalla"] = $row["Talla"];
	return $row["Talla"];	
}

function getIdMarca2Texto($IdMarca) {
	$IdMarca = CleanID($IdMarca);
		
	$sql = "SELECT Marca FROM ges_marcas WHERE Eliminado=0 AND (IdMarca = '$IdMarca')";
	$row = queryrow($sql);
	if (!$row)
		return false;
		
	return $row["Marca"];	
}

function getIdFamilia2Texto($IdFamilia) {
	$IdFamilia 	= CleanID($IdFamilia);
	$IdIdioma 	= getSesionDato("IdLenguajeDefecto");
	
	if (!$IdFamilia){
		return "";
	}		
	
	$keyname = "tFAMILIA_". $IdFamilia;
	
	if (isset(	$_SESSION[$keyname]) and $_SESSION[$keyname]){
		return $_SESSION[$keyname];
	}		
		
	//query("SELECT '$keyname buscando familia'");
			
	$sql = "SELECT Familia FROM ges_familias WHERE IdFamilia = '$IdFamilia' AND IdIdioma='$IdIdioma'";
	$row = queryrow($sql,"Cargando $keyname");
	if (!$row) {	
		$_SESSION[$keyname] = "";
		return "";
	}
	
	$familia = $row["Familia"];
	
	if (getParametro("FamiliaLatin1")){		
		$familia = iso2utf($familia);	
	}	
	
	$_SESSION[$keyname] = $familia;
	
	//query("SELECT '$keyname sera $familia'");
		
	return $familia;	
}

function getIdSubFamilia2Texto($IdFamilia,$IdSubFamilia) {
	$IdSubFamilia 	= CleanID($IdSubFamilia);
	$IdFamilia 		= CleanID($IdFamilia);
	
	if (!$IdFamilia){
		return "";
	}	
	
	$IdIdioma = getSesionDato("IdLenguajeDefecto");
	
	$keyname = "tSUBFAMILIA_".$IdFamilia."_".$IdSubFamilia;
	
	if (isset(	$_SESSION[$keyname]) and $_SESSION[$keyname]){
		return $_SESSION[$keyname];
	}			
	
		
	$sql = "SELECT SubFamilia FROM ges_subfamilias WHERE Eliminado=0 AND IdSubFamilia = '$IdSubFamilia' AND IdFamilia='$IdFamilia' AND IdIdioma='$IdIdioma'";
	$row = queryrow($sql);
	if (!$row)		return "";
	
	$subfamilia = $row["SubFamilia"];
	if (getParametro("SubFamiliaLatin1")){		
		$subfamilia = iso2utf($subfamilia);	
	}			
	
	$_SESSION[$keyname]  = $subfamilia;
			
	return $subfamilia;	
}


function getFirstNotNull($tabla,$id){
	$sql = "SELECT $id as IdCosa FROM $tabla WHERE Eliminado=0";
	$row = queryrow($sql);
	if (!$row) return 0;
	return $row["IdCosa"];
}

function getSubFamiliaAleatoria($IdFamilia){

	$sql = "SELECT IdSubFamilia as IdCosa FROM ges_subfamilias WHERE IdFamilia='$IdFamilia' AND Eliminado=0";
	$row = queryrow($sql);
	if (!$row) return 0;
	return $row["IdCosa"];
}


/* BUSQUEDA DE DATOS*/

/* CLASE */

class producto extends Cursor {
	
	var $lastLang;
	var $ges_productos;
	var $ges_productos_idioma;
	var $_fallodeintegridad;
	
    function producto() {
    	return $this;
    }
      
    function Init(){
    	$this->ges_productos = array("Referencia","CodigoBarras","RefProvHab",
			"IdProdBase","IdProvHab","IdTalla","IdNumeroZapato","IdColor",
			"IdFamilia","CosteSinIVA","IdSubFamilia","IdProvHab","IdMarca","IdTallaje");	
		$this->ges_productos_idioma = array("IdProdBase","IdIdioma","Nombre","Descripcion");			    	
    }  
      
    function SiguienteProducto() {
		$res = $this->LoadNext();
		if (!$res) {
			return false;
		}
		$this->setId($this->get("IdProducto"));		
		return true;			
	}

	function ListadoFlexible($idprov,$idmarca,$idcolor,$idtalla,$lang,$min=0,
		$base=false,$idprod=false,$idfamilia=false,$tamPag=10,
		$ref,$cb,$nombre,$obsoletos=false	){
		
	//	error(__FILE__ . __LINE__ ,"($cb)($ref)($nombre)$idprov,$idmarca,$idcolor,$idtalla,$lang,$min=0,$base=false,$idprod=false,$idfamilia=false,$tamPag=10");
			
		$extra = "";
		
    	if (!$lang)
    		$lang = getSesionDato("IdLenguajeDefecto");
    
    	if ($idprov)
    		$extra .= "AND ges_productos.IdProvHab  = '$idprov' ";
    	if ($idmarca)
    		$extra .= "AND ges_productos.IdMarca  = '$idmarca' ";
    	if ($idcolor and $idcolor>0)
    		$extra .= "AND ges_productos.IdColor  = '$idcolor' ";
    	if ($idtalla)
    		$extra .= "AND ges_productos.IdTalla  = '$idtalla' ";
    	if ($base)
    		$extra .= "AND ges_productos.IdProdBase  = '$base' ";
    	if ($idprod)
    		$extra .= "AND ges_productos.IdProducto  = '$idprod' ";
    	if ($idfamilia)
    		$extra .= "AND ges_productos.IdFamilia  = '$idfamilia' ";
    	if ($ref)
    		$extra .= "AND ges_productos.Referencia  = '$ref' ";
    	if ($cb)
    		$extra .= "AND ges_productos.CodigoBarras  = '$cb' ";
    	if ($nombre)
    		$extra .= "AND ges_productos_idioma.Nombre  LIKE '%".$nombre."%' ";    		
    	if (!$obsoletos)
    		$extra .= "AND ges_productos.Obsoleto=0 ";
    		    		    
		$sql = "SELECT		
		ges_productos.*,
		ges_productos_idioma.IdProdIdioma,
		ges_productos_idioma.Nombre,
		ges_productos_idioma.Descripcion
		
		FROM
		ges_productos INNER JOIN ges_productos_idioma ON
		ges_productos.IdProdBase = ges_productos_idioma.IdProdBase
		
		WHERE
		ges_productos_idioma.IdIdioma = '$lang'
		AND ges_productos.Eliminado = 0
		$extra		".
		"ORDER BY ".		
		" ges_productos_idioma.Nombre ASC, " .
		" ges_productos.IdProdBase ASC ";
		
		
		
		$res = $this->queryPagina($sql, $min, $tamPag);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
		}	else {				
			$this->setLang($lang);
		}		
				
		return $res;				
	}	

	function ListadoProveedor($IdProvHab,$lang,$min=0){
			
    	if (!$lang)
    		$lang = getSesionDato("IdLenguajeDefecto");
    
		$sql = "SELECT		
		ges_productos.*,
		ges_productos_idioma.IdProdIdioma,
		ges_productos_idioma.Nombre,
		ges_productos_idioma.Descripcion
		
		FROM
		ges_productos INNER JOIN ges_productos_idioma ON
		ges_productos.IdProdBase = ges_productos_idioma.IdProdBase
		
		WHERE
		ges_productos_idioma.IdIdioma = '$lang'
		AND ges_productos.Eliminado = 0
		AND ges_productos.IdProvHab  = '$IdProvHab'
		AND ges_productos_idioma.Eliminado = 0";
		
		$res = $this->queryPagina($sql, $min, 10);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
		}	else {				
			$this->setLang($lang);
		}		
				
		return $res;				
	}	

	function Listado($lang,$min=0,$tamPagina=10){
			
    	if (!$lang)
    		$lang = getSesionDato("IdLenguajeDefecto");
    
		$sql = "SELECT		
		ges_productos.*,
		ges_productos_idioma.IdProdIdioma,
		ges_productos_idioma.Nombre,
		ges_productos_idioma.Descripcion
		
		FROM
		ges_productos INNER JOIN ges_productos_idioma ON
		ges_productos.IdProdBase = ges_productos_idioma.IdProdBase
		
		WHERE
		ges_productos_idioma.IdIdioma = '$lang'
		AND ges_productos.Eliminado = 0
		AND ges_productos_idioma.Eliminado = 0";
		
		$res = $this->queryPagina($sql, $min, $tamPagina);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
		}	else {				
			$this->setLang($lang);
		}		
				
		return $res;				
	}	
	  
      
    function Load($id,$lang=false){
    	$this->Init();
    	$id = CleanID($id);
    	if (intval($id)==0){
    		error(__FILE__ . __LINE__ , "Info: cargando id, pero '$id' es cero");
    		return false;    		
    	}   
    	    	
    	if (!$lang)
    		$lang = getSesionDato("IdLenguajeDefecto");
    
    /*
		$sql = "SELECT
		ges_productos.*,
		ges_productos_idioma.IdProdIdioma,
		ges_productos_idioma.Nombre,
		ges_productos_idioma.Descripcion
		
		FROM
		ges_productos INNER JOIN ges_productos_idioma ON
		ges_productos.IdProducto = ges_productos_idioma.IdProducto
		
		WHERE
		ges_productos_idioma.IdIdioma = '$lang'
		AND ges_productos.Eliminado = 0
		AND ges_productos_idioma.Eliminado = 0
		AND ges_productos.IdProducto = '$id'";*/
		
		$sql = "SELECT
		ges_productos.*,
		ges_productos_idioma.IdProdIdioma,
		ges_productos_idioma.Nombre,
		ges_productos_idioma.Descripcion,
		ges_familias.Familia,
		ges_subfamilias.SubFamilia
		
		FROM
		ges_productos
		INNER JOIN ges_productos_idioma ON ges_productos.IdProdBase =
		ges_productos_idioma.IdProdBase
		INNER JOIN ges_familias ON ges_productos.IdFamilia = ges_familias.IdFamilia
		INNER JOIN ges_subfamilias ON (ges_productos.IdSubFamilia =
		ges_subfamilias.IdSubFamilia AND ges_productos.IdFamilia =
		ges_subfamilias.IdFamilia)
		
		WHERE
		ges_productos_idioma.IdIdioma = '$lang'
		AND ges_familias.IdIdioma = '$lang'
		AND ges_subfamilias.IdIdioma = '$lang'
		AND ges_productos.Eliminado = 0
		AND ges_productos.IdProducto = '$id' ";

		
		
		
		$res = $this->queryrow($sql);//pregunta e importa fila
		if (!$res){
			$this->Error(__FILE__ . __LINE__ , "E: cargando producto");
			return false;			
		}				
		$this->setId($id);
		//$this->set("IdProducto",$id);
		$this->setLang($lang);				
    	return true;
    }
        
    function setLang($lang){    	    	
		$this->set("IdIdioma",$lang,FORCE);
		$this->lastLang = $lang;    	
    }           
    
    function getLang(){
    	return $this->lastLang;	
    }
    
    function getNombre(){
    	if (!getParametro("ProductosLatin1"))
    		return $this->get("Nombre");//ya es UTF8
    	else
    		return iso2utf($this->get("Nombre"));//requiere conversion a utf8
    }
    
	//Formulario de modificaciones y altas
	function formEntrada($action,$esModificar,$lang=false,$esPopup=false){

		if (!$esModificar)				
			$ot = getTemplate("AltaProductoOnlineMulti");
		else
			$ot = getTemplate("ModProductoOnlineMulti");
			
		if (!$ot){	return false; }
		
		if ($esModificar) {
			$modo = "modsave";
			$titulo = _("Modificando producto");	
		} else {
			$modo = "newsave";
			$titulo = _("Nuevo producto");			
		}
		
		if ($esPopup)
			$onClose = "window.close();";	
		else
			$onClose = "location.href='modproductos.php'";
		
		if ($esPopup or !$esModificar)
			$ListadoCombinado = "";
		else {
			if ($esModificar)
				$ListadoCombinado = genListadoCruzado($this->getId(),$this->get("IdTallaje"));				
		}
		
						
		$cambios = array(
			"tNuevaTallaOColor" => _("Nueva talla o color"),
			"ListadoCombinado" => $ListadoCombinado,
			"vIdProducto" => $this->getId(),
			"onClose" => $onClose,
			"tImprimirCodigoBarras" => _("Imprimir código barras"),
			"vRefProvHab"=> $this->get("RefProvHab"),
			"tRefProvHab"=> _("Ref. proveedor"),
			"vIdMarca" =>  $this->get("IdMarca"),
			"Imagen" =>  $this->get("Imagen"),
			"tMarca" => _("Marca"),
			"vMarca" => getIdMarca2Texto($this->get("IdMarca")),			
			"vIdTalla" =>  $this->get("IdTalla"),
			"tTalla" => _("Talla"),
			"vTalla" => getIdTalla2Texto($this->get("IdTalla"),$this->get("IdIdioma")),			
			"vIdColor" =>  $this->get("IdColor"),
			"tColor" => _("Color"),
			"vColor" => getIdColor2Texto($this->get("IdColor"),$this->get("IdIdioma")),
			"tCodigoBarras" => _("Código barras"),
			"vCodigoBarras" => $this->get("CodigoBarras"),
			"tTitulo" => $titulo,	
			"HIDDENDATA" => Hidden("id",$this->getId()),
			"ACTION" => "$action?modo=$modo",
			"Referencia" => _("Referencia"),
			"vReferencia" =>  $this->getReferencia(),
			"Nombre" => _("Nombre"),
			"vNombre" => $this->getNombre(),
			"tCosteSinIVA" => _("Coste"),
			"vCosteSinIVA" => $this->get("CosteSinIVA")*1,
			"Descripcion" => _("Descripción"),
			"vDescripcion" => $this->getDescripcion(),			
			"PrecioVenta" => _("Precio venta"),
			"vPrecioVenta" => $this->getPrecioVenta(),
			"PrecioOnline" => _("Precio online"),
			"vPrecioOnline" => $this->getPrecioOnline(),
			//"comboFamilias" => genComboFamilias($this->get("IdFamilia")),
			
			"tFamilia" => _("Familia..."),
			"tSubFamilia" => _("Sub familia..."),
			
			"TipoImpuesto" => _("Impuesto"),
			
			"tIdProvHab" => _("Proveedor hab."),
			"vIdProvHab" => $this->get("IdProvHab"),
			"vProveedorHab" => getNombreProveedor($this->get("IdProvHab")),
			
			"vTipoImpuesto" => $this->getTipoImpuesto(),
			"vIdFamilia" => $this->get("IdFamilia"),
			"vIdSubFamilia" => $this->get("IdSubFamilia"),						
			
			"vFamilia" => getIdFamilia2Texto($this->get("IdFamilia")),
			"vSubFamilia" =>getIdSubFamilia2Texto( $this->get("IdFamilia"),$this->get("IdSubFamilia") ),						
			"vImpuesto" => $this->getImpuesto()						
		);

		return $ot->makear($cambios);									
	}
  
  
    
	//Formulario de modificaciones y altas
	function formEntradaBar($action,$lang=false,$esPopup=false){

		$ot = getTemplate("ModBarFicha");
			
		if (!$ot){	return false; }
		
		$modo = "modsavebar";		
		$titulo = _("Modificando producto");	
		
		$onClose = "location.href='modproductos.php'";
		
		$ListadoCombinado = genListadoCruzado($this->getId(),$this->get("IdTallaje"));				
						
		$cambios = array(
			"tNuevaTallaOColor" => _("Nueva talla o color"),
			"ListadoCombinado" => $ListadoCombinado,
			"vIdProducto" => $this->getId(),
			"onClose" => $onClose,
			"tImprimirCodigoBarras" => _("Imprimir código barras"),
			"vRefProvHab"=> $this->get("RefProvHab"),
			"tRefProvHab"=> _("Ref. proveedor"),
			"vIdMarca" =>  $this->get("IdMarca"),
			"Imagen" =>  $this->get("Imagen"),
			"tMarca" => _("Marca"),
			"vMarca" => getIdMarca2Texto($this->get("IdMarca")),			
			"vIdTalla" =>  $this->get("IdTalla"),
			"tTalla" => _("Talla"),
			"vTalla" => getIdTalla2Texto($this->get("IdTalla"),$this->get("IdIdioma")),			
			"vIdColor" =>  $this->get("IdColor"),
			"tColor" => _("Color"),
			"vColor" => getIdColor2Texto($this->get("IdColor"),$this->get("IdIdioma")),
			"tCodigoBarras" => _("Código barras"),
			"vCodigoBarras" => $this->get("CodigoBarras"),
			"tTitulo" => $titulo,	
			"HIDDENDATA" => Hidden("id",$this->getId()),
			"ACTION" => "$action?modo=$modo",
			"Referencia" => _("Referencia"),
			"vReferencia" =>  $this->getReferencia(),
			"Nombre" => _("Nombre"),
			"vNombre" => $this->getNombre(),
			"tCosteSinIVA" => _("Coste"),
			"vCosteSinIVA" => $this->get("CosteSinIVA")*1,
			"Descripcion" => _("Descripción"),
			"vDescripcion" => $this->getDescripcion(),			
			"PrecioVenta" => _("Precio venta"),
			"vPrecioVenta" => $this->getPrecioVenta(),
			"PrecioOnline" => _("Precio online"),
			"vPrecioOnline" => $this->getPrecioOnline(),
			//"comboFamilias" => genComboFamilias($this->get("IdFamilia")),
			
			"tFamilia" => _("Familia..."),
			"tSubFamilia" => _("Sub familia..."),
			
			"TipoImpuesto" => _("Impuesto"),
			
			"tIdProvHab" => _("Proveedor hab."),
			"vIdProvHab" => $this->get("IdProvHab"),
			"vProveedorHab" => getNombreProveedor($this->get("IdProvHab")),
			
			"vTipoImpuesto" => $this->getTipoImpuesto(),
			"vIdFamilia" => $this->get("IdFamilia"),
			"vIdSubFamilia" => $this->get("IdSubFamilia"),						
			
			"vFamilia" => getIdFamilia2Texto($this->get("IdFamilia")),
			"vSubFamilia" => $this->get("SubFamilia"),						
			"vImpuesto" => $this->getImpuesto()						
		);

		return $ot->makear($cambios);									
	}
	  
	//Formulario de modificaciones y altas
	function formClon($action, $lang=false,$volver=false){

		$ot = getTemplate("ClonProducto");
			
		if (!$ot){	return false; }
		
		$modo = "salvaclon";
		$titulo = _("Nueva talla/color de producto");	
				
		$ListadoCombinado = genListadoCruzado($this->getId(),$this->get("IdTallaje"));
		
		
		if ($volver=="modcompras"){
			$volver = "modcompras.php";		
		} else {
			$volver = "modproductos.php";		
		}				
					
		$cambios = array(
			"tPrecioVenta" => _("Previo venta"),
			"vPrecioVenta" => $this->get("PrecioVenta"),
			"phpPageVolver" => $volver,
			"vIdTallaje" => $this->get("IdTallaje"),
			"ListaCombinada" => $ListadoCombinado,
			"tImprimirCodigoBarras" => _("Imprimir código barras"),
			"vRefProvHab"=> $this->get("RefProvHab"),
			"tRefProvHab"=> _("Ref. proveedor"),
			"vIdMarca" =>  $this->get("IdMarca"),
			"tMarca" => _("Marca"),
			"vMarca" => getIdMarca2Texto($this->get("IdMarca")),			
			"vIdTalla" =>  $this->get("IdTalla"),
			"tTalla" => _("Talla"),
			"vTalla" => getIdTalla2Texto($this->get("IdTalla"),$this->get("IdIdioma")),			
			"vIdColor" =>  $this->get("IdColor"),
			"tColor" => _("Color"),
			"vColor" => getIdColor2Texto($this->get("IdColor"),$this->get("IdIdioma")),
			"tCodigoBarras" => _("Código barras"),
			"vCodigoBarras" => $this->get("CodigoBarras"),
			"tTitulo" => $titulo,	
			"HIDDENDATA" => Hidden("id",$this->getId()),
			"action" => "$action?modo=$modo",
			"Referencia" => _("Referencia"),
			"vReferencia" =>  $this->getReferencia(),
			"Nombre" => _("Nombre"),
			"vNombre" => $this->getNombre(),
			"tCosteSinIVA" => _("Coste"),
			"vCosteSinIVA" => $this->get("CosteSinIVA")*1,
			"Descripcion" => _("Descripción"),
			"vDescripcion" => $this->getDescripcion(),			
			"PrecioVenta" => _("Precio venta"),
			"vPrecioVenta" => $this->getPrecioVenta(),
			"PrecioOnline" => _("Precio online"),
			"vPrecioOnline" => $this->getPrecioOnline(),
			//"comboFamilias" => genComboFamilias($this->get("IdFamilia")),
			
			"tFamilia" => _("Familia..."),
			"tSubFamilia" => _("Sub familia..."),
			
			"TipoImpuesto" => _("Impuesto"),
			
			"tIdProvHab" => _("Proveedor hab."),
			"vIdProvHab" => $this->get("IdProvHab"),
			"vProveedorHab" => getNombreProveedor($this->get("IdProvHab")),
			
			"vTipoImpuesto" => $this->getTipoImpuesto(),
			
			"vIdFamilia" => $this->get("IdFamilia"),
			"vIdSubFamilia" => $this->get("IdSubFamilia"),						
			
			"vFamilia" => $this->get("Familia"),
			"vSubFamilia" => $this->get("SubFamilia"),						
			"vImpuesto" => $this->getImpuesto()						
		);

		return $ot->makear($cambios);									
	}
	
	function getReferencia(){
		return $this->get("Referencia");	
	}

	function getDescripcion(){
    	return $this->get("Descripcion");					
	}

	function getPrecioVenta(){		
		return (float)$this->get("PrecioVenta");	
	}
	
	function getPrecio(){		
		return (float)$this->get("PrecioVenta");	
	}
	
	function getPrecioFormat(){
		return money_format('%!i &euro;', $this->getPrecioVenta());	
	}

	function getPrecioOnline(){
		return $this->get("PrecioOnline");	
	}

	function getTipoImpuesto(){
		return $this->get("TipoImpuesto");		
	}
	
	function getImpuesto(){
		return $this->get("Impuesto");	
	}
	
	function getCB(){
		return $this->get("CodigoBarras");	
	}
	
	function getTextTalla() {
		$lang = $this->getLang();
		$IdTalla = $this->get("IdTalla");
		$sql = "SELECT Talla FROM ges_tallas WHERE IdIdioma='$lang' AND IdTalla='$IdTalla'";
		$row = queryrow($sql,"Lee texto talla");
		if (!$row)
			return false;
		
		if (getParametro("TallasLatin1")) //detecta si necesita conversión
			return iso2utf($row["Talla"]);
		else 	
			return $row["Talla"]; 		
	}
	
	function getTextColor() {
		$lang = $this->getLang();
		$IdColor = $this->get("IdColor");
		$sql = "SELECT Color FROM ges_colores WHERE IdIdioma='$lang' AND IdColor='$IdColor'";
		$row = queryrow($sql,"Lee texto color");
		if (!$row)
			return false;
			
		if (getParametro("ColoresLatin1"))			
			return iso2utf($row["Color"]);
		else
			return $row["Color"]; 			
	}
					
	function Crea(){
		$this->Init();
		$this->regeneraCodigos();
						
		$this->setNombre(_("Nuevo producto"));
									
		$this->setPrecioVenta(0);
		$this->setPrecioOnline(0);
		$this->set("CosteSinIVA",0,FORCE);
		$fam = getFirstNotNull("ges_familias","IdFamilia");
		$this->set("IdFamilia",$fam,FORCE);
		$this->set("IdSubFamilia",getSubFamiliaAleatoria($fam), FORCE);
		$this->set("IdProvHab",getFirstNotNull("ges_proveedores","IdProveedor"),FORCE);
		$this->set("IdMarca",getFirstNotNull("ges_marcas","IdMarca"),FORCE);
		
		$this->set("IdTallaje",TALLAJE_VARIOS,FORCE);
		$this->set("IdTalla",TALLAJE_VARIOS_TALLA,FORCE);
		
		$oAlmacen = getSesionDato("AlmacenCentral");
		
		if ($oAlmacen){
			//$this->set("");
			$this->set("TipoImpuesto",getTipoImpuesto(),FORCE);	
			$this->set("Impuesto",getValorImpuestoDefectoCentral(),FORCE);
		}
		//$this->set("IdProvHab",
		
	}
		
	function regeneraCodigos() {
		$minval = "0000";					
		$sql = "SELECT Max(IdProducto) as RefSugerido, Max(CodigoBarras) as MaxBarras FROM ges_productos";
		$row = queryrow($sql,"Imaginando referencia apropiada");
		if ($row) {
			$sugerido =  $row["RefSugerido"];
			$maxbarras = $row["MaxBarras"];		
			$minval = $sugerido + 1001;
		}							
		
		$letra = strtoupper(chr(ord('a')+rand()%25));
		$this->setReferencia($letra . $minval); 
		
		$this->regeneraCB();
	}
	
	function CBRepetido(){
	
		$cb = $this->get("CodigoBarras");
		$sql = "SELECT IdProducto FROM ges_productos WHERE (CodigoBarras='$cb') AND Eliminado=0";
		$row = queryrow($sql,"¿Esta repetido?");
		if (!$row)
			return false;
			
		return (intval($row["IdProducto"])>0);		
	}
	
	
	function regeneraCB() {
		$minval = 0;					
		$sql = "SELECT Max(IdProducto) as RefSugerido, Max(CodigoBarras+1001) as MaxBarras FROM ges_productos";
		$row = queryrow($sql,"Sugiriendo CB Valido");
		if ($row) {
			$sugerido 	= intval($row["RefSugerido"]);
			$maxbarras 	= intval($row["MaxBarras"]);
			if (intval($maxbarras) > intval($sugerido))
				$minval = intval($maxbarras);
			else
				$minval = intval($sugerido) + 90000001;
											
		} else {
			$minval = 90000001+ rand()*10000;	
		}
				
		$extra = 1001;
		$cb = intval($minval)+intval($extra);
		$this->set("CodigoBarras", $cb,FORCE);
		
		while($this->CBRepetido()){
			$extra = $extra + 1001;		
			$cb = intval($minval) + intval($extra);
			$this->set("CodigoBarras", $cb ,FORCE);
		}  
	}
	
	function Alta(){
		global $UltimaInsercion;

		$this->Init();	//antibug squad		
		
		if (!$this->AutoIntegridad()){
			$this->Error(__FILE__ . __LINE__, "Info: no pudo crear producto, fallo de integridad: [" . $this->getFallo() . "]");
			return false;
		}
		
		//$sql = "SELECT Max(IdProdBase) FROM ges_productos_idioma";
		
		$ref = CleanRef( $this->get("Referencia") );
		$sql = "SELECT IdProdBase FROM ges_productos WHERE Referencia='$ref'";	
		$row = queryrow($sql);
		
		if ($row) {
			//Ya conocemos esta referencia, luego le corresponde este prodbase
			$this->set("IdProdBase",$row["IdProdBase"],FORCE);
			error(0,"Info: prodbase fue " . $row["IdProdBase"] );			
			$existeIdioma = true;
		} else 	{
			//No conocemos esta referencia, luego es un nuevo prodbase		
			$sql = "SELECT Max(IdProdBase) as IdProdBase FROM ges_productos";
			$row = queryrow($sql);
			if ($row){
				$IdProdBase = intval($row["IdProdBase"]) + 1;	
			} else {
				error (__FILE__ . __LINE__ , "E: $sql no saco idprodbase adecuado");
				return false;	
			} 
			error(0,"Info: prodbase sera " . $IdProdBase );			
			$this->set("IdProdBase",$IdProdBase,FORCE);
			$existeIdioma = false;
		}

		//error(__FILE__ . __LINE__ , "Info: export sera .." . var_export($this->export(),true ) );						
		
		$sql = CreaInsercion($this->ges_productos,$this->export(),"ges_productos");
		
		//error(__FILE__ . __LINE__ ,"Info: va a ejecutar '$sql' para objeto" . var_export($this,true));
		
		$res = query($sql,"alta producto");
		$IdProducto = $UltimaInsercion;
		$this->setId($IdProducto);
		
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"E: no pudo insertar el producto");
			return false;
		}		
					
		if (!$existeIdioma) {
			//Solo creamos idioma cuando es primera vez para este prodbase
			$sql = CreaInsercion($this->ges_productos_idioma,$this->export(),"ges_productos_idioma");
			$res = query($sql,"alta producto idioma");
			if (!$res) {
				$this->Error(__FILE__ . __LINE__ ,"E: no pudo insertar el producto");
				return false;
			}		
		}
		return true;		 						 	
	}		
		
	function Clon(){
		global $UltimaInsercion;

		$this->Init();		
		
		if (!$this->AutoIntegridadClon()){
			//$this->Error(__FILE__ . __LINE__, "Info: no pudo crear producto, fallo de integridad");
			return false;
		}				
		
		$sql = CreaInsercion($this->ges_productos,$this->export(),"ges_productos");
		$res = query($sql,"clon producto");
		$IdProducto = $UltimaInsercion;
		$this->setId($IdProducto);
		
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"E: no pudo insertar el producto");
			return false;
		}		
		/*	
		 * Los datos de idioma no son necesarios de clonar
		 * 
		$sql = CreaInsercion($this->ges_productos_idioma,$this->export(),"ges_productos_idioma");
		$res = query($sql,"clon producto idioma");
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"E: no pudo insertar el producto");
			return false;
		}*/		
		
		return true;		 						 	
	}		
		
	function setFallo($fallo=false){
		$this->_fallodeintegridad = $fallo;
	}	
	
	function getFallo(){
		return $this->_fallodeintegridad;
	}			

	//INTEGRIDAD NORMAL

	function IntegridadNombre(){
		$nombre = $this->get("Nombre");
						
		if (!$nombre or strlen($nombre)<1) {
		  	$this->setFallo(_("Nombre demasiado corto"));
			return false;
		}
		
		if ($nombre=="Nuevo producto") {
			$this->setFallo(_("Nombre genérico no valido"));
			return false;	
		}
			
		return true;			
	}

	function IntegridadFamilia(){
		
		$lang = $this->getLang();
		
		$oFamilia = new Familia;
		
		$IdFamilia = intval($this->get("IdFamilia"));
		$IdSubFamilia = intval($this->get("IdSubFamilia"));
		if (!$IdFamilia or !$IdSubFamilia){
			$this->setFallo(_("Familia o subfamilia incorrecta"));
			return false;	
		}
		
		//Si la familia no existe, no tiene sentido utilizarla.
		$sql = "SELECT Id FROM ges_familias WHERE IdFamilia='$IdFamilia' ";
		$row = queryrow($sql,"Existe la familia?");		
		if (!$row){
			$this->setFallo(_("Familia incorrecta") );
			return false;						
		}		
				
		$sql = "SELECT Id FROM ges_subfamilias WHERE IdFamilia = '$IdFamilia' AND IdSubFamilia = '$IdSubFamilia'";
		$row = queryrow($sql,'Existe la subfamilia?');
				
		if (!is_array($row)){
			//
			//A peticion del cliente, se quiere que la gestion de subfamilias se autocorrija.
			//asi que haremos que el fallo aqui no sea fatal.
			
			$sql = "SELECT MIN(IdSubFamilia) as IdSubFamilia, SubFamilia
					FROM ges_subfamilias
					WHERE IdFamilia = '$IdFamilia'
					AND Eliminado = 0
					AND IdIdioma = '$lang'";
			$row = queryrow($sql,"Intentamos un arreglo de subfamilia");
			if(!$row or !$row["IdSubFamilia"]) {
				$this->setFallo(_("Subfamilia incorrectos"));
				return false;
			}
									
			$this->set("IdSubFamilia",$row["IdSubFamilia"],FORCE);
			return true;
		}  
						
		if (!$oFamilia->LoadSub($row["Id"])){
			$this->setFallo(_("Subfamilia incorrecta"));
			return false;			
		}				
		
		return true;		
	}
	
	function IntegridadReferencia() {
	
		return true;
		
		//No hacemos integridad de referencia para permitir que el usuario
		// asigne "prodbase" a mano mediante cambios en la referencia.
	
		$id = $this->getId();			
							
		$ref = $this->getReferencia();
		
		$sql = "SELECT IdProducto,IdProdBase FROM ges_productos WHERE (Referencia = '$ref') AND (IdProducto != '$id')";
		$res = query($sql);
		
		
		if (!$res){
			$this->Error(__FILE__ . __LINE__ , "E: $sql, error desconocido");
			return true;	
		}
	
		$row = Row($res);		
		if (!is_array($row)){
			return true;	
		}		
								
		$ViejoProdbase = $row["IdProdBase"];
		
		if ($ViejoProdbase != $this->get("IdProdBase")) {
			$this->setFallo(_("Referencia duplicada: ya existe un producto con esa referencia"));		
			$this->Error(__FILE__ . __LINE__ , "Info: prodbase $ViejoProdbase colisiona con $id de ref $ref");
			return false;
		}				

/*		
		if ($IdViejo and $id and $IdViejo != $id){
			// duplicado!
			$this->setFallo(_("Referencia duplicada: ya existe un producto con esa referencia"));
			return false;			
		} 				
		
		if (!$id and $IdViejo){
			//Ya existe uno!
			$this->setFallo(_("Ya existe un producto con esa referencia"));
			return false;	
		}*/
		
		
		return true;					 
	}
		
	function IntegridadCodigoBarras() {
		$id = $this->getId();			
						
		$ref = $this->get("CodigoBarras");
		
		$sql = "SELECT IdProducto FROM ges_productos WHERE (CodigoBarras = '$ref') ";
		
		$row = queryrow($sql);
		
		if (!$row){
			return true;	
		}		
						
		$IdViejo = $row["IdProducto"];
		
		if ($IdViejo and $id and $IdViejo != $id){
			// duplicado!
			$this->setFallo(_("Referencia duplicada: ya existe un producto con ese código de barras"));
			return false;			
		} 				
		
		if (!$id and $IdViejo){
			//Ya existe uno!
			$this->setFallo(_("Ya existe un producto con ese código de barras"));
			error(__FILE__ . __LINE__ ,"Info: Validacion: viejoid '$IdViejo' tiene '$ref'cb, luego '$id' no puede usarlo");
			return false;	
		}
		
		return true;					 
	}		

	function IntegridadTallasyColores() {
		$id = $this->getId();			
							
		$talla = $this->get("IdTalla");
		$color = $this->get("IdColor");
		$idprodbase = $this->get("IdProdBase");
		
		$sql  = "SELECT IdProducto FROM ges_productos WHERE (IdTalla = '$talla') AND (IdColor = '$color') AND (IdProdBase = '$idprodbase') ";
		
		$row = queryrow($sql,"..comprobando integridad de talla y color");
		if(!$row) {			
			return true;
		}
		
		$IdViejo = $row["IdProducto"];
		
		if ($IdViejo and $id and $IdViejo != $id){
			// duplicado!
			$this->setFallo(_("Ya existe esa talla y color para el producto"));
			return false;			
		} 				
		
		if (!$id and $IdViejo){
			//Ya existe uno!
			$this->setFallo(_("Ya existe el producto que quiere inserta con esa talla y color"));
			return false;	
		}
		
		return true;	
		
	}	
			
	//INTEGRIDAD CLON

	function IntegridadReferenciaClon() {		
		
		//TODO: actualizar considerando que otro de la misma prodbase debe usar misma ref			
		$ref = $this->getReferencia();
		
		$sql = "SELECT IdProducto FROM ges_productos WHERE (Referencia = '$ref') ";
		$row = queryrow($sql);		
				
		if (!$row) {			return true;		}		
						
		$IdViejo = $row["IdProducto"];
		
		if ($IdViejo){
			// duplicado!
			$this->setFallo(_("Referencia duplicada: ya existe un producto con esa referencia"));
			return false;			
		} 				
		
		
		return true;					 
	}
		
						
	function IntegridadFamiliaClon() {
		return $this->IntegridadFamilia();	
	}		
	
	function IntegridadCodigoBarrasClon() {
		//Bloquea productos con codigobarras repetido
				
		$ref = $this->get("CodigoBarras");	
		
		if(!$ref)	
			return false;
		
		$sql = "SELECT IdProducto FROM ges_productos WHERE (CodigoBarras = '$ref') ";
		
		$row = queryrow($sql);
		
		if (!$row){			return true;		}		
						
		$IdViejo = $row["IdProducto"];
		
		if ($IdViejo){
			// duplicado!
			$this->setFallo(_("Referencia duplicada: ya existe un producto con ese código de barras"));
			return false;			
		} 				
				
		return true;					 
	}		
		
			
	function IntegridadTallasyColoresClon() {						
		//Bloquea productos con igual triplete IdTalla+IdColor+IdProdBase	
		$talla = $this->get("IdTalla");
		$color = $this->get("IdColor");
		$idprodbase = $this->get("IdProdBase");
		
		$sql  = "SELECT IdProducto FROM ges_productos WHERE (IdTalla = '$talla') AND (IdColor = '$color') AND (IdProdBase = '$idprodbase') ";
		
		$row = queryrow($sql,"..comprobando integridad");
		if(!$row) {			
			return true;
		}
		
		$IdViejo = $row["IdProducto"];
		
		if ($IdViejo){
			// duplicado!
			$this->setFallo(_("Ya existe esa talla y color para el producto"));
			return false;			
		} 				
			
		return true;			
	}	
					
	// AUTO INTEGRIDAD CLON
		
	function AutoIntegridadClon(){
		if (!$this->IntegridadTallasyColoresClon()){
			return false;	
		}		
		
		if (!$this->IntegridadCodigoBarrasClon()){
			return false;
		}		
		
		if (!$this->IntegridadFamiliaClon()){
			return false;	
		}		
		
		/*//TODO:
		if (!$this->IntegridadReferenciaClon()){
			return false;	
		}*/			
			
		return true;					
	}	

	// AUTO INTEGRIDAD
		
	function AutoIntegridad(){
		
		if (!$this->IntegridadTallasyColores()){
			return false;	
		}		
		
		if (!$this->IntegridadCodigoBarras()){
			return false;
		}		
		
		if (!$this->IntegridadFamilia()){
			return false;	
		}		
		
		if (!$this->IntegridadReferencia()){
			return false;	
		}			
			
		$this->AjustaTallaje();
			
		return true;			
	}	
				
	function AjustaTallaje() {
		//Detecta si es necesario un cambio de tallaje, y ajusta apropiadamente.
		
		$IdTalla = CleanID($this->get("IdTalla"));
		$sql = "SELECT IdTallaje FROM ges_tallas WHERE IdTalla = '$IdTalla'";
		$row = queryrow($sql,"¿Es tallaje correcto?");
		if (!$row) return true;//??.. no hay talla?
		$IdTallaje = $row["IdTallaje"];
		$this->set("IdTallaje",$IdTallaje,FORCE); 						
	}				
				
				
	function Modificacion(){

		$this->Init();						

		if (!$this->AutoIntegridad()){
			$this->Error(__FILE__ . __LINE__, "Info: no pudo modificar producto, fallo de integridad");
			return false;
		}
		
		$sql = CreaUpdate($this->ges_productos,$this->export(),"ges_productos","IdProducto",$this->getId());
		$res = query($sql);
		
		if (!$res) {
			$this->Error(__FILE__ . __LINE__, "E: no pudo modificar producto");
			return false;	
		}				
		
		$sql = CreaUpdate($this->ges_productos_idioma,$this->export(),"ges_productos_idioma","IdProdIdioma",$this->get("IdProdIdioma"));
		$res = query($sql);
		if (!$res){
			$this->Error(__FILE__ . __LINE__, "E: no pudo modificar producto, datos idioma");
			return false;	
		}		

		return true;		
	}		
				
	function setNombre($nombre){
		$this->set("Nombre",$nombre,FORCE);						
	}	

	function setReferencia($ref){
		$this->set("Referencia",$ref, FORCE);						
	}	
	
	function setDescripcion($Descripcion){
		$this->set("Descripcion",$Descripcion,FORCE);									
	}
	
	function EliminarProducto(){
		$id = $this->getId();
		
		$sql = "UPDATE ges_productos SET Eliminado = 1 WHERE IdProducto = '$id'";
		$res = query($sql);
		if (!$res)
			error(__FILE__ . __LINE__ , "W: no pudo borrar registro");
			
		$idbase = $this->get("IdProdBase"); 
					
		$sql = "SELECT IdProducto FROM ges_productos WHERE (IdProdBase='$idbase') AND Eliminado=0";
		$row = queryrow($sql);
		
		$existe = false;
		if ($row)
			$existe = $row["IdProducto"];
		
		if (!$existe) {
			//Ya no quedan prodictos para este prodbase				
			$sql = "UPDATE ges_productos_idioma SET Eliminado = 1 WHERE IdProdBase = '$id'";
			$res = query($sql);
			if (!$res)
				error(__FILE__ . __LINE__ , "W: no pudo borrar registro en idioma");
		}
			
		$sql = "UPDATE ges_almacenes SET Eliminado = 1 WHERE IdProducto = '$id'";
		$res = query($sql);
		if (!$res)
			error(__FILE__ . __LINE__ , "W: no pudo borrar registros de almacen");			
									
	}	
		
	function setPrecioVenta($value){
		$this->set("PrecioVenta",$value,FORCE);	
	}
				
	function setPrecioOnline($value){
		$this->set("PrecioOnline",$value,FORCE);	
	}

	
	function getTallaTexto(){
		return getIdTalla2Texto($this->get("IdTalla"));
	}
	
	function getColorTexto(){
		return getIdColor2Texto($this->get("IdColor"));
	}
	
	function getMarcaTexto(){
		return getIdMarca2Texto($this->get("IdMarca"));
	}

}

/* CLASE */

?>
