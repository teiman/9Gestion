<?php

function getIdMarcaFromMarca($marca){
	
	$marca = CleanRealMysql($marca);	
	$sql = "SELECT IdMarca FROM ges_marcas WHERE Marca='$marca'";
	$row = queryrow($sql);
	
	if ($row){
		return $row["IdMarca"];	
	} 
	
	return 0;	
}
		
	
function getComboTipoImpuesto ($IdPais) {
	
	$IdPais = CleanID($IdPais);

	$sql = "SELECT TipoImpuestoDefecto, NombrePais FROM ges_paises		
	WHERE IdPais = '$IdPais'";
	
	$res = query($sql);
	
	if (!$res)
		return false;
		
	$out = "";
	while($row = Row($res)) {
		$key = $row["TipoImpuestoDefecto"];
		$value = $row["NombrePais"];
	 	$out .= "<option value='$key'>$value</option>";	
	}
	
	return $out;
}


function getComboNumeracionFacturas($selected=false){

	$sql = "SELECT IdFormato, Formato FROM ges_factura_formatos ORDER BY Formato ASC";		
	
	$res = query($sql);	
	if (!$res)	return false;
		
	$out = "";
	while($row = Row($res)) {
		$key 	= $row["IdFormato"];
		$value 	= $row["Formato"];
		if ($key!=$selected)
	 		$out .= "<option value='$key'>$value</option>";
	 	else	
	 		$out .= "<option selected value='$key'>$value</option>";
	}
	
	return $out;
}

function genComboIdiomas($selected=false){
	$sql = "SELECT  IdIdioma,Idioma FROM ges_idiomas WHERE Traducido = 1 AND Eliminado = 0 ORDER BY Idioma ASC";
	$res = query($sql);
	if (!$res)
		return "";	
	while($row=Row($res)){
		$key 	= $row["IdIdioma"];
		$value 	= $row["Idioma"];
				
		if(getParametro("IdiomasLatin1")){
			$value = iso2utf($value);//Ha requerido una conversion, pues la tabla esta en Latin1.
		}
		
		$value_s = CleanParaWeb($value);		
		
		if ($key!=$selected)
			$out .= "<option value='$key'>$value_s</option>";
		else	
			$out .= "<option selected value='$key'>$value_s</option>";
	}
	return $out;		
}


function genComboPerfiles($selected=false){
	$sql = "SELECT  IdPerfil,NombrePerfil FROM ges_perfiles_usuario  WHERE Eliminado=0 ORDER BY NombrePerfil ASC";
	$res = query($sql);
	if (!$res)
		return "";	
	while($row=Row($res)){
		$key = $row["IdPerfil"];
		$value = $row["NombrePerfil"];
		if ($key!=$selected)
			$out .= "<option value='$key'>$value</option>";
		else	
			$out .= "<option selected value='$key'>$value</option>";
	}
	return $out;		
}

function genComboFamilias($selected=false){
	$sql = "SELECT IdFamilia,Familia  FROM ges_familias  WHERE Eliminado=0 ORDER BY Familia ASC";
	$res = query($sql);
	if (!$res)
		return "";	
	while($row=Row($res)){
		$key = $row["IdFamilia"];
		$value = $row["Familia"];
		if ($key!=$selected)
			$out .= "<option value='$key'>$value</option>";
		else	
			$out .= "<option selected value='$key'>$value</option>";
	}
	return $out;		
}

function genComboSubFamilias($selected=false, $IdFamilia=0){
	$sql = "SELECT IdSubFamilia,SubFamilia  FROM ges_subfamilias  WHERE Eliminado=0 AND IdFamilia = '$IdFamilia' ORDER BY SubFamilia ASC";
	$res = query($sql);
	if (!$res)
		return "";	
	while($row=Row($res)){
		$key = $row["IdSubFamilia"];
		$value = $row["SubFamilia"];
		if ($key!=$selected)
			$out .= "<option value='$key'>$value</option>";
		else	
			$out .= "<option selected value='$key'>$value</option>";
	}
	return $out;		
}



function genArraySubFamilias($IdFamilia){
	$sql = "SELECT IdSubFamilia,SubFamilia  FROM ges_subfamilias  WHERE Eliminado=0 AND IdFamilia = '$IdFamilia' ORDER BY SubFamilia ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out =array();	
			
	while($row=Row($res)){
		$key = $row["IdSubFamilia"];
		$value = $row["SubFamilia"];
		$out[$key]=$value;
	}
	return $out;		
}

function genArrayFamilias(){
	$sql = "SELECT IdFamilia,Familia  FROM ges_familias  WHERE Eliminado=0 ORDER BY Familia ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out =array();	
			
	while($row=Row($res)){
		$key = $row["IdFamilia"];
		$value = $row["Familia"];
		$out[$key]=$value;
	}
	return $out;		
}

function genArrayProveedores(){
	$sql = "SELECT IdProveedor,NombreComercial  FROM ges_proveedores WHERE Eliminado=0 ORDER BY NombreComercial ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out =array();	
			
	while($row=Row($res)){
		$key = $row["IdProveedor"];
		$value = $row["NombreComercial"];
		$out[$key]=$value;
	}
	return $out;		
}


function genArrayColores(){
	$IdIdioma = getSesionDato("IdLenguajeDefecto");
	$sql = "SELECT IdColor,Color  FROM ges_colores  WHERE Eliminado=0 AND IdIdioma = '$IdIdioma' ORDER BY Color ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out =array();	
			
	while($row=Row($res)){
		$key = $row["IdColor"];
		$value = $row["Color"];
		$out[$key]=$value;
	}
	return $out;		
}


function genArrayMarcas(){
	
	$sql = "SELECT IdMarca,Marca  FROM ges_marcas  WHERE Eliminado=0 ORDER BY Marca ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out =array();	
			
	while($row=Row($res)){
		$key = $row["IdMarca"];
		$value = $row["Marca"];
		$out[$key]=$value;
	}
	return $out;		
}

function genArrayTallas($IdTallaje=5){
	$IdIdioma = getSesionDato("IdLenguajeDefecto");
	$sql = "SELECT IdTalla,Talla  FROM ges_tallas WHERE Eliminado=0  AND IdTallaje='$IdTallaje' AND IdIdioma = '$IdIdioma' ORDER BY Talla ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out =array();	
			
	while($row=Row($res)){
		$key = $row["IdTalla"];
		$value = $row["Talla"];
		
		if (getParametro("TallasLatin1")){
			$value = iso2utf($value);
		}	
		$out[$key]=$value;
	}
	return $out;		
}

function genComboAlmacenes($selected=false) {
	$alm = new almacenes;
	$arrayTodos = $alm->listaTodosConNombre();
		
	$out = "<option value='nada'></option>";	
	foreach($arrayTodos as $key=>$value){
		if ($key!=$selected)
			$out .= "<option value='$key'>$value</option>";
		else	
			$out .= "<option selected value='$key'>$value</option>";
	}
	return $out;
}

function genXulComboAlmacenes($selected=false,$xul="menuitem",$callback=false) {
	$alm = new almacenes;
	$arrayTodos = $alm->listaTodosConNombre();
		
	$out = "";	
	$call = "";
	foreach($arrayTodos as $key=>$value){
		if ($callback) 
			$call = "oncommand=\"$callback('$key')\"";
			
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value' $call/>";
		else	
			$out .= "<$xul selected value='$key' label='$value' $call/>";

			
	}
	return $out;
}
	
//genComboProveedores
function genComboProveedores($selected=false) {
	$sql = "SELECT IdProveedor,NombreComercial  FROM ges_proveedores  WHERE Eliminado=0 ORDER BY NombreComercial ASC";
	$res = query($sql);
	if (!$res)
		return "";	
	while($row=Row($res)){
		$key = $row["IdProveedor"];
		$value = $row["NombreComercial"];
		if ($key!=$selected)
			$out .= "<option value='$key'>$value</option>";
		else	
			$out .= "<option selected value='$key'>$value</option>";
	}
	return $out;		
}


function genXulComboFamilias($selected=false,$xul="listitem"){
	$sql = "SELECT IdFamilia,Familia  FROM ges_familias  WHERE Eliminado=0 ORDER BY Familia ASC";
	$res = query($sql);
	$out = "";
	if (!$res)
		return "";	
	while($row=Row($res)){
		$key = $row["IdFamilia"];
		$value = CleanXulLabel($row["Familia"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>\n";
		else	
			$out .= "<$xul selected value='$key' label='$value'/>\n";
	}
	return $out;		
}

function genXulComboModistos($selected=false,$xul="listitem"){
	$sql = "SELECT IdModisto, NombreComercial as Modisto  FROM ges_modistos  WHERE Eliminado=0 ORDER BY  NombreComercial ASC";
	$res = query($sql);
	$out = "";
	if (!$res)
		return "";	
	while($row=Row($res)){
		$key = $row["IdModisto"];
		$value = CleanXulLabel($row["Modisto"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>\n";
		else	
			$out .= "<$xul selected value='$key' label='$value'/>\n";
	}
	return $out;		
}

function genXulComboStatusTrabajo($selected=false,$xul="listitem"){

	$estados = array('Pdte Envio', 'Enviado', 'Recibido', 'Entregado');

	$key = 0;
	foreach ($estados as $value){		
		$value = CleanXulLabel($value);
		if ($key!=$selected)
			$out .= "<$xul value='$value' label='$value'/>\n";
		else	
			$out .= "<$xul value='$value' label='$value'/>\n";
		
		$key = $key + 1;
	}
	return $out;		
}

function genXulComboSubFamilias($selected=false, $IdFamilia=0,$xul="listitem"){
	$sql = "SELECT IdSubFamilia,SubFamilia  FROM ges_subfamilias  WHERE Eliminado=0 AND IdFamilia = '$IdFamilia' ORDER BY SubFamilia ASC";
	$res = query($sql);
	if (!$res)
		return "";	
	while($row=Row($res)){
		$key = $row["IdSubFamilia"];
		$value = CleanXulLabel($row["SubFamilia"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>\n";
		else	
			$out .= "<listitem selected value='$key' label='$value'/>\n";
	}
	return $out;		
}


function genXulComboProveedores($selected=false,$xul="listitem") {
	$sql = "SELECT IdProveedor,NombreComercial  FROM ges_proveedores  WHERE Eliminado=0 ORDER BY NombreComercial ASC";
	$res = query($sql);
	if (!$res)
		return "";
			
	$out = "";
				
	while($row=Row($res)){
		$key = $row["IdProveedor"];
		$value = CleanXulLabel($row["NombreComercial"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>";
		else	
			$out .= "<$xul selected='yes' value='$key' label='$value'/>";
	}
	return $out;		
}



function genXulComboColores($selected=false,$xul="listitem"){
	$IdIdioma = getSesionDato("IdLenguajeDefecto");
	$sql = "SELECT IdColor,Color  FROM ges_colores  WHERE Eliminado=0 AND IdIdioma = '$IdIdioma' ORDER BY Color ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = "";	
			
	while($row=Row($res)){
		$key = $row["IdColor"];
		$value = CleanXulLabel($row["Color"]);
		if ($key!=$selected)
			$out .= "<$xul label='$value' value='$key' />\n";
		else	
			$out .= "<$xul label='$value' value='$key' selected='yes'/>\n";
	}
	return $out;		
}

function genComboColores($selected=false){
	$IdIdioma = getSesionDato("IdLenguajeDefecto");
	$sql = "SELECT IdColor,Color  FROM ges_colores  WHERE Eliminado=0 AND IdIdioma = '$IdIdioma' ORDER BY Color ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = "";	
			
	while($row=Row($res)){
		$key = $row["IdColor"];
		$value = $row["Color"];
		if ($key!=$selected)
			$out .= "<option value='$key'>$value</option>";
		else	
			$out .= "<option selected='yes' value='$key'>$value</option>";
	}
	return $out;		
}

function genXulComboTallas($selected=false,$xul="listitem",$IdTallaje=5, $autoid=false){
	$IdIdioma = getSesionDato("IdLenguajeDefecto");
	$sql = "SELECT IdTalla,Talla FROM ges_tallas   WHERE Eliminado=0 AND IdTallaje='$IdTallaje' AND IdIdioma = '$IdIdioma' ORDER BY Talla ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = "";	
	$num = 0;		
	$id = "";
			
	while($row=Row($res)){
		if ($autoid) {		
			$ident = " id='talla_".$autoid."_".$num."'";
			$num ++;
		}	
	
		$key = $row["IdTalla"];
		$value = $row["Talla"];
		if (getParametro("TallasLatin1")){
			$value = iso2utf($value);
		}	
		
		$value = CleanXulLabel($value);
		if ($key!=$selected)
			$out .= "<$xul".$ident." label='$value' value='$key'/>\n";
		else	
			$out .= "<$xul".$ident." selected='yes' value='$key' label='$value'/>\n";
	}
	return $out;		
}




function genXulComboMarcas($selected=false,$xul="listitem"){

	$sql = "SELECT IdMarca,Marca FROM ges_marcas  WHERE Eliminado=0 ORDER BY Marca ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = "";	
			
	while($row=Row($res)){
		$key = $row["IdMarca"];
		$value = CleanXulLabel($row["Marca"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>";
		else	
			$out .= "<$xul selected='yes' value='$key' label='$value'/>";
	}
	return $out;		
}

function genComboTallas($selected=false){
	$IdIdioma = getSesionDato("IdLenguajeDefecto");
	$sql = "SELECT IdTalla,Talla FROM ges_tallas  WHERE Eliminado=0 AND IdIdioma = '$IdIdioma' ORDER BY Talla ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out =array();	
			
	while($row=Row($res)){
		$key = $row["IdTalla"];
		$value = $row["Talla"];
		if ($key!=$selected)
			$out .= "<option value='$key'>$value</option>";
		else	
			$out .= "<option selected value='$key'>$value</option>";
	}
	return $out;		
}

function genComboMarcas($selected=false){

	$sql = "SELECT IdMarca,Marca FROM ges_marcas  WHERE Eliminado=0 ORDER BY Marca ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out =array();	
			
	while($row=Row($res)){
		$key = $row["IdMarca"];
		$value = $row["Marca"];
		if ($key!=$selected)
			$out .= "<option value='$key'>$value</option>";
		else	
			$out .= "<option selected value='$key'>$value</option>";
	}
	return $out;		
}


function genComboModPagoHabitual($selected=false) {
	
	
	$datos =array(_("Tarjeta"),_("Transferencia"),_("Giro"),_("Envio"));	
			
	$key = 0;
	foreach ($datos as $value){
		$key++;
		if ($key!=$selected)
			$out .= "<option value='$key'>$value</option>";
		else	
			$out .= "<option selected value='$key'>$value</option>";
	}
	return $out;		
}

function genComboPaises($selected=false){
	$sql = "SELECT IdPais,NombrePais  FROM ges_paises  WHERE Eliminado=0 ORDER BY NombrePais ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out =array();	
			
	while($row=Row($res)){
		$key = $row["IdPais"];
		$value = $row["NombrePais"];
		
		if (getParametro("PaisesLatin1")){
			$value = iso2utf($value);//Puede necesitar una conversion, si la tabla de paises esta en Latin1
		}					
		
		$value_s = CleanParaWeb($value);
		
		if ($key!=$selected)
			$out .= "<option value='$key'>$value_s</option>";
		else	
			$out .= "<option selected value='$key'>$value_s</option>";
	}
	return $out;		
}

function genXulComboUsuarios($selected=false,$xul="listitem"){

	$sql = "SELECT IdUsuario,Nombre FROM ges_usuarios  WHERE Eliminado=0 ORDER BY Nombre ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = "";	
			
	while($row=Row($res)){
		$key = $row["IdUsuario"];
		$value = CleanXulLabel($row["Nombre"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>";
		else	
			$out .= "<$xul selected='yes' value='$key' label='$value'/>";
	}
	return $out;		
}






?>