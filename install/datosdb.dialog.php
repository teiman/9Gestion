
<p>Para realizar la instalación, rellene los siguientes datos.</p>


<form method="post" action="?modo=EntradaDatosDB"> 

	<fieldset>
	<legend>Datos del servidor</legend>
	
<br>Base de datos</br>	
<label for="hostname">Hostname</label><br />
<input id="hostname" value="localhost" style="width: 40em" class="textinput" type="text" name="hostname"><br />

<label for="usuario">Usuario</label><br />
<input id="usuario" value="root" style="width: 20em" class="textinput"  type="text" name="usuario"><br />


<label for="password">Contraseña</label><br />
<input id="password" value="" style="width: 20em" class="textinput" type="text" name="password"><br />


<label for="password">Nueva base de datos (intentara crearla, o utilizar una existente)</label><br />
<input id="password" value="9gestionTest" style="width: 20em" class="textinput" type="text" name="database"><br />
</fieldset>

<fieldset>
	<legend>Datos de la aplicación</legend>

<br>Configuración basica</br>

<label for="baseurl">Dirección web de la aplicación</label><br />
<input id="baseurl" value="http://localhost/9gestion/" style="width: 40em" class="textinput" type="text" name="baseurl"><br />

<label for="adminemail">Email del contacto administrador</label><br />
<input id="adminemail" value="admin@localhost" style="width: 20em" class="textinput" type="text" name="adminemail"><br />

<br>Otros</br>

<label for="passmodulos">Contraseña para modulos auxiliares (en blanco para desactivar)</label><br />
<input id="passmodulos" value="" style="width: 20em" class="textinput" type="text" name="passmodulos"><br />


<input type="submit"  class="buttonSubmit" value="Instalar">

	</fieldset>

</form>
