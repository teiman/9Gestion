<?php

require("../../tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

$tabla = "ges_listados";

$IdListado = CleanID($_GET["id"]);

$sql = "SELECT * FROM $tabla WHERE (IdListado='$IdListado')";

$row = queryrow($sql);

if ( $row) {
	$CodigoSQL 		= $row["CodigoSQL"];
	$NombrePantalla = $row["NombrePantalla"];	
	$CodigoSQL = PostProcesarSQL( $CodigoSQL);
}

$genCabecera = "";
$genListado = "";
$genListCol = "";
		
$res = query( $CodigoSQL,"Listado: " . CleanText($NombrePantalla) ); //Sirve para extraer headers

$maximoPorcentaje = 0;
$nombrePorcentaje = "";

$totales = array();
$totalesNombre = array();

$ponerBotonImprimir = false;

if ($res){
	$row = Row($res);
	if ($row){
		$genCabecera = "<tr class='head'>\n";
		$genTotales  = "<tr class='head'>\n";

		foreach ( $row as $key=>$value){
			if (!esNumero2($key)){
				$totales[$key] = 0;				
				$titulo = ReformateaTitulo($key);//Cosa__Euro --> Cosa

				$genCabecera .= "\t<td class='headitem' key='$key'>$titulo</td>\n";
				
				if ( esPorcentaje($key) ) {
					$nombrePorcentaje = $key;//Requiere total al final 
				}	
					
				if ( esAutoSuma($key) ){
					$genTotales .= "\t<td class='headitem' autosuma='1' key='$key' align='right'>%TOTAL:".$key."%</td>\n";
					$totalesNombre[$key] = $key;//requiere computo de totales								
				} else if ( esPorcentaje($key) ){
					$nombrePorcentaje = $key;//donde se colocara el total de porcentaje
					$genTotales .= "\t<td class='headitem' porcentaje='1' key='$key'>%PORCENTAJE%</td>\n";
				}  else {
					$genTotales .= "\t<td class='headitem'></td>\n";
				}
			}
		}
		$genCabecera .= "</tr>\n";
		$genTotales  .= "</tr>\n";

		if ($nombrePorcentaje){
			$res = query( $CodigoSQL);

			while( $row = Row($res)){
				$maximoPorcentaje = $maximoPorcentaje + $row[$nombrePorcentaje] * 1;
			}
			
			$res = query( $CodigoSQL);
		}

		$listado = array();
		$ilistado = 0;
		
		$genListado .= Datos2Codigo($row);
		$listado[$ilistado]= $row;		
		
		$ilistado = $ilistado + 1;		
		
		$odd = 1;

		//Genera listado
		while( $row= Row($res)){
			$genListado .= Datos2Codigo($row,$odd%2);
			$odd = $odd + 1;
			$listado[$ilistado]= $row;
			$ilistado = $ilistado + 1;
			$ponerBotonImprimir = true;
		}

		//Completa autosumas
		foreach ($totalesNombre as $key=>$texto){
			$subformat = str_replace("AutoSuma","",SubKey($key));//Noseque__AutoSumaEuro -> AutoSumaEuro-> Euro			
			$genTotales = str_replace( "%TOTAL:".$texto."%", "<!-- key:$key, sub:$subformat -->TOTAL: " . SubFormateo($subformat,$totales[$key]), $genTotales  );	
		}

		//Completa porcentaje
		if ($nombrePorcentaje){
			$maximoPorcentaje = SubFormateo(str_replace("Porcentaje","",SubKey($nombrePorcentaje)),$maximoPorcentaje);
			$genTotales = str_replace( "%PORCENTAJE%", "<!-- nP:$nombrePorcentaje -->TOTAL: $maximoPorcentaje", $genTotales  );				
		}
	} else {
	 	$genCabecera="<tr class='head'>
				<td class='headitem'>Resultado</td></tr>";
	 	
	 	$genListado = "<tr class='lineadatos'>
				<td class='dato'> <div style='text-align:center;font-weight: bold;'>"."El listado resulto vacio"."</div> </td></tr>";	
	 	$genTotales = "<tr class='head'>
				<td class='headitem'></td></tr>";
	 	
	}
}

?>
<html>
<head>
<style type='text/css'>
.headitem {
	background-color: #eee;
}

.lineadatos, .dato {
	border-bottom: 1px solid #eee;
}

td {
	font-size: 12px;
}
</style>
<style type='text/css' media='print'>

input {
	visibility: hidden;
}

</style>
</head>
<body>

<?php if ($ponerBotonImprimir) { ?>
<input type="button" value="Imprimir" onclick="window.print()"/>
<?php } ?>

<table style="background-color: #fefefe" width='100%'>
<?php
	echo $genCabecera;
	echo $genListado;
	echo $genTotales;
	
	echo "</table>";
?>
<script>
function RecargarPagina(){
	document.location='<?php echo $action . "?id=" . $_GET["id"] ?>';
}
<?php

echo "</script></body></html>";

// 
///////////////////////////////////////////////////////////////////////////////////
//  Funciones auxiliares


function SubKey($clave){
	$variable = split("__",$clave);
	$num	= count($variable);
	if ($num<2) {
		return false;
	}
	$modoformato = $variable[1];	
	return $modoformato;	
}

function AutoformatoSQL( $clave, $valor){
	global $maximoPorcentaje;
		
	
	$modoformato = SubKey($clave);
	if (!$modoformato) {
		//return htmlentities($valor) . "<!-- no formato -->";
		return htmlentities($valor,ENT_QUOTES,'UTF-8');
	}
	
	
	switch( $modoformato ){
		//Devuelven html
		case "AutoSumaPorcentaje":
		case "Porcentaje":
		case "ModUserButton":
		case "decode64":			
		case "FechaHora":
		case "Fecha":
		case "DiaSemana":
			$val = subFormateo($modoformato,$valor);			
			return $val;
		//Devuelven un valor que se puede formatear en html			
		default:
			$val = subFormateo($modoformato,$valor);
			$val = CleanParaWeb($val);
			return "<div style='align:right;float:right'>$val</div>";
	}

	//return htmlentities($valor);
	return htmlentities($valor,ENT_QUOTES,'UTF-8');
}

function SubFormateo($modoformato,$valor){
	global $maximoPorcentaje;
	
	$val = $valor . "<!-- formato desconocido: $modoformato -->";
		
	$modoformato = str_replace("__","",$modoformato);
	$submodo = str_replace("AutoSuma","",$modoformato);
	switch($submodo){
		case "Entero":
			$val = intval($valor);
			break;
		default:
			$val = $valor;
			break;
		case "Dec2":
			$val = sprintf("%01.2f", $valor);
			break;
		case "Moneda":
		case "Euro":
			$val = sprintf("%01.2f", $valor) . " EUR";
			break;					
		case "Porcentaje":
			$val = ((($valor*1)/$maximoPorcentaje)*100);
			$val = (intval($val*100))/100;//recorta a solo dos digitos de precision
			return GenCol($val);		
		case "decode64":
			//error(0,"Info: decode base64");
			//$valor = str_replace("'","&#39;", base64_decode($valor));				
			$val = base64_decode($valor);				
			break;
		case "FechaHora":
			$fechahora = split(" ",$valor);
			$val = $fechahora[1] . " " . $fechahora[0];
			break;					
		case "ModUserButton":
			$val = "<input type='button'value='Modificar' onclick='cmdPadre(\"IdUsuario\",".$valor.")'/>";
			return $valor;
			//GenCol
			
		case "Tarta":
			//$val = ((($valor*1)/$maximoPorcentaje)*100);
			//$val = (intval($val*100))/100;//recorta a solo dos digitos de precision
			//return GenCol($val);					
			break;
		case "Porcentaje":
			$val = ((($valor*1)/$maximoPorcentaje)*100);
			$val = (intval($val*100))/100;//recorta a solo dos digitos de precision
			return GenCol($val);		
		case "Fecha":
			$val = CleanFechaFromDB($valor);
			break;
		case "DiaSemana":
			$val = NumDia2DiaES($valor);
			break;
		case "Mes":
			$val = NumMes2MesES($valor);
			break;													
	}
	
	return $val;	
}

function ReformateaTitulo( $titulo ){
	$variable = split("__",$titulo);
	$num	= count($variable);
	if($num<2){
		return $titulo;
	}
	return $variable[0];
}

function Datos2Codigo( $datos,$par=false ){
	global $totalesNombre,$totales;
	
	if(!$datos or !is_array($datos))
		return;

	$out ="\n<tr class='lineadatos'>";		
	foreach ($datos as $key=>$value){		
		if (isset($totalesNombre[$key])){
			$original = $totales[$key];
			$suma = $original + $value;
			$totales[$key] = $suma;
			//echo "<p>org:$original, suma:$suma, value:$value";
		} else {
			//echo "<p>$key no esta en totalesNombre";	
		}
			
		if (!esNumero2($key)) {
			$value = AutoformatoSQL($key,$value);
						
			$out .= "\t<td class='dato'>$value &nbsp;</td>\n";
		}
	}
	return $out . "</tr>\n";
}

function esAutoSuma($key){ //contiene __AutoSuma
	 return strpos($key,"__AutoSuma")>0;	
}

function esPorcentaje($key){	
	if (strpos($key,"__AutoSumaPorcentaje")>0) return true;
	return strpos($key,"__Porcentaje")>0; 
}


function esNumero2($cadena) {
	if ($cadena == "0")
		return true;

	return (( $cadena * 1 ) >0);
}

function GenCol($cent,$ponTex=false,$color='green'){
	//$centOpac = intval(($cent+100)/2);
	$centOpac = (($cent+100)/2)/100;
	$pix = "green_h.gif";
	//pixpaint2.gif
	return "<img src='$pix' style='width: ".$cent."%; height: 16px; -moz-opacity: ".$centOpac.";'/> " . $cent . "%";
}


function NumDia2DiaES($dia){
	$dia = intval($dia);
	//$dias = array('Monday', 'Tuesday', 'Wednesday','Thursday', 'Friday', 'Saturday','Sunday');
	$dias = array('Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo');
	
	return $dias[$dia];
}

function NumMes2MesES($mes){
   $meses = array('','Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
   
   return $meses[$mes];
}
											
function RecortaPrecision($flotante){
	$flotante = sprintf("%01.2f", $flotante);
	return $flotante;
}											


function getProdBaseFromCB($cb){
	if (!$cb)
		return false;
	
	$cb_s = CleanCB($cb);
	$sql = "SELECT  IdProdBase FROM `ges_productos` WHERE CodigoBarras='$cb_s'";
	$row = queryrow($sql);
	return $row["IdProdBase"];
}

function PostProcesarSQL( $cod ) {

	if( function_exists("getSesionDato"))
		$IdLang = getSesionDato("IdLenguajeDefecto");

	if (!$IdLang)
		$IdLang = 1;

	if (isset($_GET["cb"])) {
		$cod = str_replace("%IDPRODBASEDESDECB%",getProdBaseFromCB($_GET["cb"]),$cod);
		$cod = str_replace("%CODIGOBARRAS%",CleanRealMysql($_GET["cb"]),$cod);
	}

	$cod = str_replace("%IDIDIOMA%",$IdLang,$cod);
	$cod = str_replace("%DESDE%",		CleanFechaES($_GET["Desde"]),$cod);
	$cod = str_replace("%HASTA%",		CleanFechaES($_GET["Hasta"]),$cod);
	$cod = str_replace("%IDTIENDA%",	CleanID($_GET["IdLocal"]),$cod);
	$cod = str_replace("%GENERICO%",	CleanText($_GET["Generico"]),$cod);
	$cod = str_replace("%FAMILIA%",		CleanID($_GET["IdFamilia"]),$cod);	
	$cod = str_replace("%IDACTIVIDAD%",	CleanID($_GET["IdActividad"]),$cod);	
	$cod = str_replace("%IDMODISTO%",	CleanID($_GET["IdModisto"]),$cod);
	$cod = str_replace("%STATUSTBJOMODISTO%",	CleanText($_GET["StatusTrabajoModisto"]),$cod);
	$cod = str_replace("%IDPROVEEDOR%",	CleanID($_GET["IdProveedor"]),$cod);
	$cod = str_replace("%IDCENTRO%",	CleanID($_GET["IdCentro"]),$cod);
	$cod = str_replace("%IDUSUARIO%",	CleanID($_GET["IdUsuario"]),$cod);
	$cod = str_replace("%REFERENCIA%",	CleanRealMysql($_GET["Referencia"]),$cod);				

	return $cod;
}

?>
