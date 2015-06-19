<?php
include("tool.php");

//SimpleAutentificacionAutomatica("visual-xul");


$_motivoFallo = "";

  
$NombreEmpresa = $_SESSION["GlobalNombreNegocio"];  
  

$modo = $_REQUEST["modo"];

$_log = "";

function AddLog($text){
	global $_log;
	$_log = $_log . $text . "\n";
}
 
AddLog("Empieza modo es '$modo'");
 

switch($modo){
    case "avisoUsuarioIncorrecto":
	case "login-usuario":
	case "login-user"://desde la TPV
	case "login-tpv":
	case "login-admin":
	
		$login = CleanLogin($_POST["login"]);
		$pass =  CleanPass($_POST["pass"]);
		AddLog("Cargando login/pass '$login/$pass'");

		$user = true;
		if ($login and $pass){
			$id = identificacionUsuarioValidoMd5($login,md5($pass));
			if ($id){		
				RegistrarUsuarioLogueado($id);
				AddLog("Se loguea id'$id'");			
				
                if($modo == "login-admin") {
                  AddLog("Se redirigie a xulmenu...");
                  session_write_close();
                  header("Location: xulmenu4.php");
                  exit;
                } else {
                  session_write_close();
                  header("Location: tpvmodular.php?r=" . rand(900000,999999) );
                  exit;
                }
				exit();	
			} else {
				$fail = "Nombre ('$login') o password ('$pass') incorrectas: $_motivoFallo";
				AddLog("Falla identificacion.");	
			}
		}		
		break;	
	case "tiendaDesconocida":
	case "login-local":
	default:
		$login = CleanLogin($_POST["login"]);
		$pass =  CleanPass($_POST["pass"]);
		
		$user = false;
		if ($login and $pass){
			$id = CleanID(identificacionLocalValidaMd5($login,md5($pass)));
			if ($id and $id != 0){		
				RegistrarTiendaLogueada($id);
				session_write_close();
				header("Location: xulentrar.php?modo=login-usuario");
				exit();	
			} else {
				$fail = "Nombre ('$login') o password ('$pass') incorrectas: $_motivoFallo";	
			}
		}
		break;
}

StartXul("Login 9Gestion");


?>
<box flex="1" style="background-image: url(img/mega2.png)">
<spacer flex="1" />
   <vbox>
   <spacer flex="1" />
	<groupbox style="width: 400px;height: 200px;background-color: #ECE8DE">
	 	<spacer flex="1"/>		
		<vbox>
			<description style="font-weight: bold;color: #e96f00"><?php	echo $NombreEmpresa; ?></description>
			<grid>
				<columns>
					<column style="width: 200px"/>
					<column/>
					<column flex="1"/>
				</columns>
				<rows>
					<row>
<hbox><spacer flex="1" style="width: 40px"/>
						<description>
<?php if ($user)
			echo _("Usuario");
		else					
			echo _("Local");
?>						
						</description>
</hbox>
						<textbox id='nombrelocal' type="normal"
						 onkeypress="if (event.which == 13) document.getElementById('passlocal').focus()"/>
					</row>
					<row>
<hbox><spacer flex="1"/>
						<description>
						<?php	echo _("Contraseña");?>													
						</description>
</hbox>
                        <textbox id='passlocal' type='password'/>      
					</row>
					<row  align="start">
						<description>
						<?php
						if ($user)
							echo '<image style="width: 48px; height: 48px" src="img/toctoc.gif" />';
						else
							echo '<image style="width: 48px; height: 48px" src="img/toctoc1.gif" />';												
						?>
						</description>
                            <hbox flex='1' >					
                          <?php
                          if ($user) {
                            echo "<button  label=\"". _("TPV") . "\" oncommand=\"SaltaLogin('login-tpv')\"/>";
                            echo "<button   label=\"". _("Admin.") ."\" oncommand=\"SaltaLogin('login-admin')\"/>";
                          } else {
                            echo "<button   label=\"". _("Entrar") ."\" oncommand=\"SaltaLogin('login-local')\"/>";
                          }                          						
?>
                            </hbox>
					</row>					
				</rows>												
			</grid>			
		</vbox>
		<hbox>
			<button class="borderless" label="Cambio empresa" onclick="VisitarLoginEmpresa()" collapsed="true"/>
			<spacer flex="1"/>
		</hbox>		
		<spacer flex="1"/>
   <!-- Es de buen nacido el ser agradecido -->
   <?php if ($config["mostrarbannerdga"]): ?>
   <groupbox>
   <hbox style="background-color: white;padding: 8px">
   <label value="Desarrollado por "  style="margin:0px;border: 1px solid white"/>
   <label value="Servicios-DPI"      style="margin:0px;border: 1px solid white;text-decoration: underline; color: blue"
                                 onclick="open('http://www.servicios-dpi.com')" 
                      />
   <label value=", liberado como " style="margin:0px;border: 1px solid white"/>
   <label value="LGPL"                   style="margin:0px;border: 1px solid white;text-decoration: underline; color: blue"
                                   onclick="open('http://www.gnu.org/licenses/lgpl.html')"
                       />
   <label value=" con ayuda de la "  style="margin:0px;border: 1px solid white"/>
   <label value="DGA"                    style="margin:0px;border: 1px solid white;text-decoration: underline; color: blue"
                                    onclick="open('http://www.aragob.es')" 
                       />
   <label value="  "                   style="margin:0px;border: 1px solid white"/>
   <image src="img/logodga1v120px.gif"  onclick="open('http://www.aragob.es')" />
   </hbox>
   </groupbox>
   <?php endif; ?>
   <!-- /Es de buen nacido el ser agradecido -->
		
	</groupbox>
	<spacer flex="1"/>	 
	</vbox>
<spacer flex="1"/>	
</box>

<box collapsed="true" hidden="true">
<html:form  collapsed="true" hidden="true"  
	id="form-enviar" action="xulentrar.php" method="post">
<html:input collapsed="true" hidden="true" style="visibility: none" 
	id="form-empresa" name="login" type="hidden" value=""/>
<html:input collapsed="true" hidden="true" style="visibility: none"
	id="form-pass" name="pass"  type="hidden" value=""/>
<html:input collapsed="true" hidden="true" style="visibility: none"
   	id="form-modo" name="modo" type="hidden" value=""/>
</html:form>
</box>
<script><![CDATA[

function id(nombreEntidad){
 return document.getElementById(nombreEntidad);
}


var findex = 1;
function SaltaLogin(pasoActual){
  var local = document.getElementById("nombrelocal").value;
  var pass = document.getElementById("passlocal").value;
  id("form-empresa").value = local;
  id("form-pass").value = pass;  
  id("form-modo").value = pasoActual;  
  id("form-enviar").submit();       
}


function VisitarLoginEmpresa(){
	document.location = "login.php";
}


// Corregimos el foco para situarse en el primer input box
var ventanamaestra = document.getElementById("login-9gestion");
ventanamaestra.setAttribute("onload","FixFocus()");

function FixFocus(){
	document.getElementById("nombrelocal").focus();
}



]]></script>
<?php

EndXul();

?>