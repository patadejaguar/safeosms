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
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../core/core.config.inc.php";
include_once "../core/core.creditos.inc.php";
include_once "../core/core.operaciones.inc.php";
include_once "../core/core.common.inc.php";
include_once "../core/core.html.inc.php";

$oficial = elusuario($iduser);

$xHPag		= new cHPage("Descuentos de Creditos");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Descuentos de Creditos</title>
</head>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<?php
jsbasic("myformdc", "", ".");
?>
<body>
<fieldset>
	<legend><script> document.write(document.title); </script></legend>
	
<form name='myformdc' action='frmcreditos_descuentos.php' method='post'>
	<table width='100%' border='0'  >
	<tr>
	<td>Clave de Persona</td>
	<td><input type="text" name="idsocio" onchange="envsoc();" size='12' maxlength='20' class='mny' />
	<?php echo CTRL_GOSOCIO; ?></td>
	<td  colspan='2'><input disabled name="nombresocio" type="text" size="50"></td>
	</tr>
		<td>N&uacute;mero de Solicitud</td>
		<td><input type="text" name="idsolicitud" onchange="envsol();" size='12' maxlength='20' class='mny'  />
		<?php echo CTRL_GOCREDIT; ?></td>
		<td colspan='2'><input disabled name="nombresolicitud" type="text" size="50"></td>
	</tr>
	<tr>
	<td>Parcialidad</td>
	<td><input type='text' name='idparcialidad' value='0'  class='mny' size="3"  /> 
	<?php echo CTRL_GOLETRAS; ?></td>
	</tr>
		<tr>
			<td>Concepto del Descuento</td><td colspan="3">
		<?php
		$gssql= "SELECT * FROM operaciones_tipos WHERE class_efectivo=8";
		$mGS = new cSelect("tipodescuento", "", $gssql);
		$mGS->setEsSql();
		$mGS->show(false);
		?></td>
		</tr>
		<tr>
			<td>Monto</td><td><input type='text' name='monto' value='0' class='mny' size="12" /></td>
		</tr>
		<tr>
			<td>Observaciones</td><td colspan="3"><input name='observaciones' type='text' value='' size="55" maxlength="100"></td>
		</tr>
	</table>
<input type='button' name='btsend' value='GUARDAR DATOS'onClick='frmSubmit();'>
</form>
</fieldset>
<?php
	$socio 				= $_POST["idsocio"];
	$documento 			= $_POST["idsolicitud"];
	$tipo 				= $_POST["tipodescuento"];
	$monto 				= $_POST["monto"];
	$parcialidad 		= $_POST["idparcialidad"];
	$observaciones  	= $_POST["observaciones"];
	$fecha_operacion	= fechasys();
if ( isset($socio) AND $monto>0) {

	$xBtn		= new cHButton("id-cmdImprimir");
	$xRec		= new cReciboDeOperacion(96, false);
	$recibo		= $xRec->setNuevoRecibo($socio, $documento, $fecha_operacion, $parcialidad, 96, $observaciones);
	
	$xRec->setNuevoMvto($fecha_operacion, $monto, $tipo, $parcialidad, $observaciones, -1, TM_ABONO);
	
	$xRec->setFinalizarRecibo();
		
	echo $xRec->getFichaSocio();
	echo $xRec->getFicha(true, "<tr><th colspan='4'>" . $xBtn->getImprimirRecibo() . "</th></tr>");

} // end if
?>
</body>
<script  >
<?php
if ( isset($socio) AND $monto>0) {
	echo $xRec->getJsPrint(); 
}
?>
</script>
</html>
