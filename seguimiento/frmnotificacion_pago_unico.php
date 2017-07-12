<?php
//=====================================================================================================
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
	include_once "../core/core.deprecated.inc.php";
	include_once "../core/core.fechas.inc.php";
	include_once "../core/entidad.datos.php";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>NOTIFICACIONES DE COBRO</title>
</head>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<?php 
jsbasic("frmnotificacion", "1", ".");
?>

<body>
<fieldset>
<legend>Notificaciones de Creditos a Final de Plazo</legend>
<form name="frmnotificacion" action="" method="POST" >

	<table   border='0'>
		<tr>
			<td>Desde el Socio: </td><td>
			<input type='text' name='desde' value='01001' onchange="">
			</td>
			<td>Nombre:</td><td><input disabled name='nombredesde' type='text' value='' size="60"></td>
		</tr>
		<tr>
			<td>Hasta el Socio: </td><td>
			<input type='text' name='hasta' value='99999' onchange="">
			</td>
			<td>Nombre:</td><td><input disabled name='nombrehasta' type='text' value='' size="60"></td>
		</tr>
		<tr>
			<td>Tipo de Notificacion desea ver</td><td colspan="2"><?php ctrl_select("general_reports", "name='ereport'", " WHERE aplica='notificacion_pago_unico'"); ?></td>
		</tr>

	</table>
<input type='button' name='btsend' value='VER INFORME'onClick='checksubmit();'>
</form>
</fieldset>
</body>
<script  >
function checksubmit() {
	var vDesde = parseFloat(document.frmnotificacion.desde.value);
	var vHasta = parseFloat(document.frmnotificacion.hasta.value);
	var ynotif = document.frmnotificacion.ereport.value;
	if (vDesde > vHasta) {
		alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---!!!RANGO INVALIDO!!!!---");
	} else if (vHasta < vDesde) {
		alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---!!!RANGO INVALIDO!!!!---");
	} else {
			var sURL = ynotif + '?on=' +  vDesde + '&off=' + vHasta;
		rptNotif = window.open( sURL);
		//, "window", "width=600,height=400,scrollbars=yes,dependent"
		rptNotif.focus();

	}
}
</script>
</html>
