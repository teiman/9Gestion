
<h2>Instalación terminada con exito</h2>

Se han completado las labores de instalación con exito.
Ya puede entrar en la aplicación:<br>

<a href="../xulentrar.php">9Gestion</a>

Tambien puede utilizar el siguiente codigo en una pagina web:
<pre style="margin-left: 32px;background-color:#eee">
&lt;script language="JavaScript"&gt;
function AbrirAplicacion() {
	var direccion = "<?php echo $datos["urlServicio"]; ?>?r="+Math.random();
	var titulo = "9Gestion";
	var modo = "resizable=yes,fullscreen=yes,toolbar=no,menubar=no,location=no,status=yes";
	var ventana = open(direccion,titulo,modo);
}
&lt;/script&gt;
&lt;a href="#" onclick="AbrirAplicacion()"&gt;Iniciar 9Gestion&lt;/a&gt;
</pre>

<p>
Recuerde:
<ul>
 <li>Borre la carpeta install, para evitar una reinstalación por accidente</li>
 <li>Modifique las contraseñas por defecto del local "demo"</li>
 <li>Modifique las contraseñas por defecto del usario "demo" y "administrador"</li> 
</ul>
</p>