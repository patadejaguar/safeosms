<?php
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	$permiso = getSIPAKALPermissions(__FILE__);
	if($permiso === false){
		header ("location:../404.php?i=999");	
	}
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Lineas de Credito.- Altas</title>
</head>
	<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
	<script   src="../jsrsClient.js"></script>
<?php
jsbasic("frmlinea", "", ".");
?>
<body>
<fieldset>
<legend>Lineas de Credito.- Altas</legend>
<form name="frmlinea" action="clscreditoslineas.php" method="post">
	<table    >
		<tr>
			<td>Clave de Persona</td>
			<td><input type='text' name='idsocio' value='1' onchange="envsoc();" size='12' class='mny' /></td>
			<td colspan='2'><input disabled name='nombresocio' type='text' value='' size="40"></td>
		</tr>
		<tr>
			<td>Monto de la Linea</td>
			<td><input type='text' name='montolinea' value='0' class='mny' /></td>
			<td>Fecha de Vencimiento</td>
			<td><?php echo ctrl_date(0); ?></td>
		</tr>
		<tr>
			<td>Numero de Hipoteca</td>
			<td><input type='text' name='numerohipoteca' value='' /></td>
			<td>Monto Hipoteca</td>
			<td><input type='text' name='montohipoteca' value='0' onchange="verifymonto();" class='mny' /></td>
		</tr>
		<tr>
			<td>Observaciones</td><td colspan="2"><input name='observaciones' type='text' value='' size="80" onFocus="alert0(); verifymonto();"></td>
		</tr>
	</table>
	<input type="button" name="btnsend" value="ENVIAR REGISTRO" onClick="frmlinea.submit();">
</form>
</fieldset>
</body>
	<script>
	function verifymonto() {
	minm = document.frmlinea.montohipoteca.value;
	mint = document.frmlinea.montolinea.value * 2;

		if (mint > minm) {
		alert ('EL MONTO DE LA HIPOTECA NO PUEDE SER MENOR \n AL DOBLE DEL MONTO DE LA LINEA \n MONTO MINIMO DE LA HIPOTECA : $' + mint);
			if (document.frmlinea.montohipoteca.value > 1 ) {
				document.frmlinea.montolinea.value = document.frmlinea.montohipoteca.value / 2;
			}
			return 0;
		}
	}
	function alert0() {
		if (document.frmlinea.montohipoteca.value <=0 ) {
		alert("EL MONTO  DE LA HIPOTECA DEBE SER MAYOR A CERO");
		return 0;
		}
	}
	</script>
</html>
