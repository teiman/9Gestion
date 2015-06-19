<?php

ini_set("session.gc_maxlifetime",    "86400");
ini_alter("session.cookie_lifetime", "86400" );
ini_alter("session.entropy_file","/dev/urandom" );
ini_alter("session.entropy_length", "512" );

session_start();

include("include/legacy.inc.php");
include("include/debug.inc.php");


//SUPERGLOBALES
if (isset($_GET["modo"]))
	$modo = $_GET["modo"];

if (isset($_GET["cargarmodoget"]))
	$modo = $_POST["modo"];
	
if (!isset($modo))	$modo = false; //Evita algunos warnings	

//NOTA: para release esto en off
$debug_mode = false;	
	
//include("include/multidatabase.inc.php");
include("config/configuration.php");

//INCLUDES
include("include/db.inc.php");
include("include/clean.inc.php");
include("include/combos.inc.php");
include("include/supersesion.inc.php");
include("include/xul.inc.php");
include("include/auth.inc.php");
include("include/pedidos.inc.php");
include("include/js.ini.php");


//CLASES
include ("class/cursor.class.php");
include ("class/template.class.php");
include ("class/local.class.php");
include ("class/perfil.class.php");
include ("class/usuario.class.php");
include ("class/producto.class.php");
include ("class/almacen.class.php");
include ("class/familia.class.php");
include ("class/proveedor.class.php");
include ("class/pedidos.class.php");
include ("class/cliente.class.php");
include ("class/modisto.class.php");
include ("class/movimiento.class.php");
include ("class/albaran.class.php");

/////////////////////////////////
// Constantes

$link = false;
$UltimaInsercion = false;
$FilasAfectadas = false;
$debug_sesion = false;	
$modo_verbose = false;
$querysRealizadas = array();

if(!$enProcesoDeInstalacion){
	//Durante la instalacion, el dato de lenguaje no esta aun disponible
	$lang = getSesionDato("IdLenguajeDefecto");
}


//
////////////////////////////////


?>
