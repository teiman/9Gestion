<?php

session_start();
session_unset();
session_destroy();

if ($AUTOLOGIN){
	$redireccionweb = $AUTOLOGIN;		
}	else {
	$redireccionweb = "xulentrar.php";		
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD>
<META http-equiv=Content-Type content="text/html; charset=utf8"></HEAD>
<script>
//document.location="<?php echo $redireccionweb ?>";
</script>
<BODY>
<center>Ya puede cerrar el navegador</center>
</BODY>
</HTML>
