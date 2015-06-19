<?php 

$modulos = array();
$config = array();


# Este es el fichero de configuracion principal de la aplicación


#### Modulos  ####
$modulos["generadorlistados"] = true;
	// Generador de listados
	
$modulos["mensajeria"] = true;
	// Sistema de mensajes en tiempo real 
	
$modulos["datepicker"] = true;
	// Selector de fechas avanzado 
	
$modulos["arreglodecaja"] = true;
	// Sistema de arqueos de caja

#### Detalles  #### 

$config["fotosproductos"] = true;
	// Si aparece la foto del producto en la tpv
	
$config["mostrarbannerdga"] = false;
	// Si aparece el banner indicando la ayuda de la dga

#### Contraseñas ####

$module_password = "";
	// Contraseña para modulos auxiliares (recomendado modificar)
	// Requieren su propia autentificación porque funcionan 
	// de manera separada con la aplicación.


#### Instalacion ####

$_BasePath = "http://localhost/9gestion/";
    // Direccion absoluta donde esta la aplicacion instalada
    // Debe modificarla para apuntar al directorio donde tiene instalada
    // la aplicacion.

define ("CORREO_ADMIN","admin@localhost");


#### Base de datos ####

$_SESSION["GlobalNombreNegocio"] = '9Gestion ';
$_SESSION["GlobalHostDatabase"] = 'localhost';	
$_SESSION["GlobalGesDatabase"]  = '9gestionTest5';				
$_SESSION["GlobalUserDatabase"] = 'root';	
$_SESSION["GlobalPassDatabase"] = '';  
	// Debe configurar aqui los datos de acceso a su base de datos  
  

#### A partir de aquí no cambiar nada ####
  
$ges_database 	= $_SESSION["GlobalGesDatabase"];
$global_host_db 	= $_SESSION["GlobalHostDatabase"];
$global_user_db 	= $_SESSION["GlobalUserDatabase"];
$global_pass_db 	= $_SESSION["GlobalPassDatabase"];

	
	
	  
  
  

?>