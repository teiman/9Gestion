<?php

	//Funciones para reducir cadenas: "332","33CASA","3" => 332,"33CASA",3 
	
	function is_intval($a) {
   		return ((string)$a === (string)(int)$a);
	}

	function qminimal($a){
		if (is_intval($a)){
			return (string)$a;			
		}	
		return qq($a);
	}

	/* - ------------------------------ */


	$NombreClienteContado = _("Cliente Contado");

	$IdLocalActivo = getSesionDato("IdTienda");

	$localActivo = new local;
	
	if (	$localActivo->Load($IdLocalActivo) ) {
		$NombreLocalActivo 	= CleanTo( $localActivo->get("NombreComercial")," " );
		$MOTDActivo 		= CleanTo( $localActivo->get("MensajeMes")," " );		
	}

	//--------------------------------------------------
	// Indice de Ticket
	// $numSerieTicketLocalActual
	
	
	$miserie = "B" . $IdLocalActivo;//Nos aseguramos de coger el valor correcto preguntando tambien por 		
		// ..la serie. Esto ayudara cuando un mismo local tenga mas de una serie, como va a ser el 
		// ..caso luego. 
	

	$sql = "SELECT Max(NFactura) as NFacturaMax FROM ges_facturas WHERE (IdLocal = '$IdLocalActivo') AND (SerieFactura='$miserie')";
	$row = queryrow($sql);
	
	if ($row){
		$numSerieTicketLocalActual =  intval($row["NFacturaMax"]) + 1; 
	}	
	//--------------------------------------------------


// LISTADO DE DEPENDIENTES
// con identificador


	//Apuntamos todos los perfiles que pueden actuar en la TPV.
	$perfilesdependiente =  array();
	$dependientes = array();
	
	$sql = "SELECT IdPerfil FROM ges_perfiles_usuario WHERE TPV=1 ";
	
	$res = query($sql);
	
	
	if ($res) {	
		while($row = Row($res)){
			$perfilesdependiente[$row["IdPerfil"]]=1;		
			//error(0,"Info: perfiles activos " .var_export($row,true) );
		}		
	}

	$numDependientes = 0;
	
	$NombreDependienteDefecto = false;
	$IdDependienteDefecto = false;
	
	
	$sql = "SELECT IdUsuario, Nombre, IdPerfil FROM ges_usuarios WHERE Eliminado=0";			
	
	$res = query($sql);
	if($res) {	
		$t = 0;
		while($row = Row($res)){
			$IdPerfil 	= $row["IdPerfil"];
			$IdUsuario = $row["IdUsuario"];
			$nombre 	= $row["Nombre"];			
			
			//error(0,"Info: usuarios activos " .var_export($row,true) );
					
			if ( isset($perfilesdependiente[$IdPerfil]) ) {
				$t++;
				
				/*
				if (!$IdDependienteDefecto) {
					$IdDependienteDefecto = $IdUsuario;
					$NombreDependienteDefecto  = $nombre;
				}*/
				
				//Hace que si quien ha logueado es dependiente, se lo ponga por defecto
				$sesname = getSesionDato("NombreUsuario") ;
				if ( ($sesname == $nombre) and $nombre){
					$IdDependienteDefecto = $IdUsuario;
					$NombreDependienteDefecto  = $nombre;	
					//error(__FILE__ . __LINE__ ,"Info: OK '$sesname'=='$nombre'");					
				}	 else {
					//error(__FILE__ . __LINE__ ,"Info: '$sesname'!='$nombre'");
				}
				
				
				$dependientes[$nombre]	= $IdUsuario;
				//error(__LINE__,"Info: entro id '$IdUsuario', nombre '$nombre'");	
				$numDependientes = $numDependientes + 1;
			}		
		}	
	}
	
	if ((getSesionDato("NombreUsuario")  != $NombreDependienteDefecto) or !$NombreDependienteDefecto){
		$usuarioActivoNoEsDependiente = 1;
		error(__FILE__ . __LINE__ ,"Info: '$sesname'!='$nombre'");
	}	

	 
	$out = "";
	$t = 0;	 	
	foreach ( $dependientes as $nombre => $IdUsuario){
		//error(__LINE__ , "Info: salio n $nombre, id $IdUsuario");
		$out .= "<menuitem id='dep_". $t ."' image='chrome://mozapps/skin/profile/Zprofileicon.gif' type='radio' name='radio' label='$nombre' value='$IdUsuario'/>\n"; 
		$t++;		
	}
	
	$generadorDeDependientes = $out;


// LISTADO DE PRODUCTOS
// con sus caracteristicas

	$out = "";

	$CondicionPruebas = " ges_almacenes.Unidades >0 AND ";
	//$CondicionPruebas = " ges_almacenes.Unidades =666 AND ";

	function qq($val) {
	  return "\"$val\"";
	}
     
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
	$jsLex = new jsLextable();
	
	$res = query($sql);
	while ($row = Row($res)){
	
		$talla = getIdTalla2Texto($row["IdTalla"]);						
		$lexTalla = $jsLex->add($talla);
		$color = getIdColor2Texto($row["IdColor"]);						
		$lexColor = $jsLex->add($color);
		$nombre = $row["Nombre"];
		
		//INFO: ProductosLatin1 indica que la tabla productos esta codificado en 
		// Latin1, y no en utf8
		$lexNombre = $jsLex->add($nombre, getParametro("ProductosLatin1") );

		$jsOut .= "tA(" . qminimal($row["CodigoBarras"]) . ",".
			 $lexNombre . ",".
			 qminimal($row["Referencia"]) . ",".
			 qminimal($row["PrecioVenta"]*100) . ",".
			 qminimal($row["Impuesto"]) .",".
			$lexTalla . "," . $lexColor .	",".		
			qminimal($row["Descuento"] * 1.0). ");\n";			  	 			
			
	}

   $out .=  $jsLex->jsDump("L","xul",false);//vamos a defininir en fuera.
   $out .=  $jsOut;
   
   $generadorJSDeProductos = $out;
   
/////////////////////////////////////////////
   
   //LISTADO DE CLIENTES 
   // con su identificador
   
	$out = "";

	$clientedebe = Array();
	$clientes = Array();

	$sql = "SELECT ges_clientes.IdCliente, ges_clientes.NombreComercial FROM ges_clientes ".
		"WHERE ges_clientes.Eliminado =0 AND (ges_clientes.IdLocal=0 OR ges_clientes.IdLocal='$IdLocalActivo' ) ".
		"ORDER BY NombreComercial ASC";
		
	$res = query($sql);
		
	while( $row = Row( $res ) ){
		$id = $row["IdCliente"];
		$clientedebe[$id] = 0;
		$clientes[$id] = $row["NombreComercial"];	
	}
	
	$sql = "SELECT SUM(ImportePendiente) as Debe, IdCliente FROM ges_facturas WHERE ImportePendiente > 0 AND ges_facturas.Status IN(1,3) GROUP BY IdCliente";
	
	$res= query($sql);
	while( $row = Row( $res )) {
		$id = $row["IdCliente"];
		$clientedebe[$id] = $row["Debe"];	
	}
	
	foreach ($clientes as $id=>$nombre) {
		$out .= "aU( ". qq($nombre) . ", $id, ". ($clientedebe[$id] * 1.0). " );\n";
	}


	$generadorJsDeClientes = $out;

	$i = 0;
	$out = "";
	$sql = "SELECT IdModisto, NombreComercial FROM ges_modistos WHERE Eliminado=0";

	$res = query($sql);
	if ($res){
		while( $row = Row($res)){
			$i=$i+1;
			$value = $row["IdModisto"];
			$label = $row["NombreComercial"];
			$out .= "<menuitem class='media' id='modisto_".$i."' value='".$value."' label='".$label."'/>\n";
		}		
	}
	

	$genModistos = $out;


?>