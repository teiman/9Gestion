<?php

include("tool.php");

SimpleAutentificacionAutomatica("novisual-services");

require_once "class/json.class.php";
$json = new Services_JSON();

$idprod = CleanID($_GET["IdProducto"]);

if (!$idprod) {
	$cod = CleanCB($_GET["CodigoBarras"]);
	$idprod = getIdFromCodigoBarras($cod);
}

$value = genListadoCruzadoAsArray($idprod);

$output = $json->encode($value);

print($output);



function genListadoCruzadoAsArray($IdProducto,$IdTallaje = false,$IdLang=false){	
	$IdProducto = CleanID($IdProducto);
	$IdTallaje = CleanID($IdTallaje);
	
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
		AND	ges_colores.IdIdioma = 1
		AND ges_locales.Eliminado = 0 
		GROUP BY ges_almacenes.IdLocal, ges_productos.IdColor, ges_productos.IdTalla
		ORDER BY ges_almacenes.IdLocal, ges_productos.IdColor, ges_productos.IdTalla";
		
	$data = array();
	$colores = array();
	$tallas = array();
	$locales = array();
	$tallasTallaje = array();
	$listaColores = array();
	
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
	
	//$out .= "<table class='forma'>";	
	//$out .= "<tr><td class='nombre'>".$tReferencia."</td>";
	$out_nombretabla = $tReferencia;
	
	
	
	$out_tallas = array();
	$out_tallas["talla_0"] = "$tReferencia/Tienda";
	$out_tallas["talla_1"] = "C o l o r";  
	
	$num = 2;
	
	foreach ($tallasTallaje as $k=>$v) {
		$out_tallas["talla_$num"] = $v;
		$num++;
	}

	
	$out_base = array();	
	$out_rows = array();
	
	$numrow = 0;
	
	$out_filas = array();
	$out_bloques = array();
	
	foreach ($locales as $l=>$v2){	
		$out_base["nombre"] = $l;		
		$out_bloques[] = $l;				 
		foreach ($colores as $c=>$v1){	
	
			$row = array();
				
			$row[] = $l;
			$row[] = $c;
			 						
			foreach ($tallasTallaje as $k2=>$t) {
				
				if (isset($data[$c][$t][$l]))
					$u = $data[$c][$t][$l];
				else
					$u = "";
				//$out .= "<td class='unidades' align='center'>" . $u . "</td>";
				
				$row[] = $u;										
			}
			$out_rows[] = $row;	
			
			//$out .= "</tr>";
		}
					
	}
	//$out .= "</table>";
	
	
	$out_final = array();
	$out_final["heads"] = $out_tallas;
	$out_final["rows"] = $out_rows;
	$out_final["numheads"] = count($out_tallas);
	//$out_final["rowheads"] = $out_filas;	
	$out_final["nombretabla"] = $out_nombretabla;
	//$out_final["bloques"] = $out_bloques;
	return $out_final;
}




?>