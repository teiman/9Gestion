<?php

include("../tool.php");


$sql = "SELECT * FROM ges_almacenes WHERE IdLocal=13";

$res = query($sql);

$IdLocal = 14;
while( $row = Row($res) ){

	$IdProducto = $row["IdProducto"];
	$PrecioVenta = $row["PrecioVenta"];
	$Descuento = $row["Descuento"];
	$TipoImpuesto = $row["TipoImpuesto"];
	$Impuesto = $row["Impuesto"];
	$Disponible = 1;
	$Oferta = $row["Oferta"];
	$Eliminado = $row["Eliminado"];
	
	
  $newsql = "INSERT INTO `ges_almacenes` ( 
	`IdLocal` , `IdProducto` , `Unidades` , `StockMin` , `StockMinOnline` , 
`PrecioVenta` , `Descuento` , `PrecioVentaOnline` , `DescuentoOnline` ,
 `TipoImpuesto` , `Impuesto` , `StockIlimitado` , `Disponible` , 
`DisponibleOnline` , `Eliminado` , `Oferta` , `OfertaOnline` 
	)VALUES (  	
	$IdLocal , '$IdProducto' , '$Unidades' , '$StockMin' , '$StockMinOnline'
 , '$PrecioVenta' , '$Descuento' , '$PrecioVentaOnline' , '$DescuentoOnline' ,
  '$TipoImpuesto' , '$Impuesto' , '$StockIlimitado' , '$Disponible' , '$DisponibleOnline' ,
  '$Eliminado' , '$Oferta' , '$OfertaOnline'
    )";
	
  echo $newsql. "<br>\n";
  query($newsql);
	
}






?>