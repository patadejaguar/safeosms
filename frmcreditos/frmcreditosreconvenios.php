<?php
/**
 * @see Modulo de Reconvenios de Creditos
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package creditos
 *  reconvenios de creditos
 *		2008-06-16 	funcion jsaGetDatosDelCredito
 *					soporte de llamada al Plan de pagos por GET
 */
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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.creditos.inc.php");
require_once("." . TINYAJAX_PATH . "/TinyAjax.php");

$oficial = elusuario($iduser);
function jsaGetDatosDelCredito($credito, $socio){
	$cred 	= new cCredito($credito, $socio);
	$cred->initCredito();

	$DC = $cred->getDatosDeCredito();

			$tab = new TinyAjaxBehavior();
			$tab -> add(TabSetValue::getBehavior('idMonto', $DC["saldo_actual"]));
			$tab -> add(TabSetValue::getBehavior('idIntereses', $DC["interes_normal_devengado"]));
			$tab -> add(TabSetValue::getBehavior('idTasa', $DC["tasa_interes"]));
			return $tab -> getString();

}
$jxc = new TinyAjax();
$jxc ->exportFunction('jsaGetDatosDelCredito', array('idsolicitud', "idsocio"));
$jxc ->process();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Reconvenios de Credito.- M&oacute;dulo</title>
</head>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<?php
jsbasic("frmreconvenios", "", ".");
$jxc ->drawJavaScript(false, true);
?>

<body>
<?php
$action 		= $_GET["a"];
if (!isset($action)){
?>
<fieldset>
<legend>Reestructuraci&oacute;n de Creditos.- M&oacute;dulo</legend>
<form name="frmreconvenios" action="frmcreditosreconvenios.php?a=i" method="post">
	<table   border='0'>
		<tr>
		<td>Clave de Persona</td>
		<td><input type="text" name="idsocio" onchange="envsoc();" id="idsocio" />
		<?php echo CTRL_GOSOCIO; ?></td>
		<td>Nombre Completo</td>
		<td><input disabled name="nombresocio" type="text" size="40"></td>
	</tr>
	<tr>
		<td>N&uacute;mero de Solicitud</td>
		<td><input type="text" name="idsolicitud" onchange="envsol();jsaGetDatosDelCredito()" onblur="jsaGetDatosDelCredito()" id ="idsolicitud" />
		<?php echo CTRL_GOCREDIT; ?></td>
		<td>Descripci&oacute;n General</td>
		<td><input disabled name="nombresolicitud" type="text" size="40" /></td>
	</tr>
	<tr>
		<td>Fecha de Convenio</td>
		<td><?php echo ctrl_date(0); ?></td>
	</tr>
	<tr>
		<td>Capital Reconvenido</td>
		<td><input type='text' name='monto' value='0' class="mny" id="idMonto" /></td>
	</tr>
	<tr>
		<td>Tasa Reconvenida</td>
		<td><input type='text' name='tasa' value='0.00' class="mny" id="idTasa" /></td>
	</tr>
	<tr>
		<td>Intereses Devengados a Reconvenir</td>
		<td><input type='text' name='intereses' value='0.00' class="mny" id="idIntereses" /></td>
	</tr>
	<tr>

	</tr>
	<tr>
		<td>Modalidad de Pagos</td>
		<td><?php ctrl_select("creditos_periocidadpagos", " name='periocidad' "); ?></td>


			<td>Numero de Pagos</td>
			<td><input type='text' name='pagos' value='1'></td>
	</tr>
	<tr>
		<td>Observaciones</td>
		<td colspan="3"><input type="text" size="40" maxlength="50" name="cObservaciones" id="idObservaciones" /></td>
	</tr>
	</table>
	<input type="button" name="sendme" value="ENVIAR RECONVENIO DE CREDITO" onClick="frmSubmit();">
	<p class="aviso">Este Modulo no cuenta con asignacion automatica de criterios, verifique los datos antes de enviarlo</p>
</form>
</fieldset>
<?php

} elseif (isset($action) && $action == "i"){
	
$idsolicitud 	= $_POST["idsolicitud"];
$idsocio 		= $_POST["idsocio"];
	//if($idsocio
	$mFCred = new cFicha(iDE_CREDITO, $idsolicitud);
	$mFCred->setTableWidth();
	$mFCred->show();

//---------------------------------------------------
$monto 			= $_POST["monto"];
$tasa 			= $_POST["tasa"];
$fechaconv 		= $_POST["elanno0"] . "-" . $_POST["elmes0"] . "-" . $_POST["eldia0"];;
$periocidad 		= $_POST["periocidad"];
$pagos 			= $_POST["pagos"];
$interesNoPag		= $_POST["intereses"];
$observaciones		= $_POST["cObservaciones"];

$cCred			= new cCredito($idsolicitud, $idsocio);
$cCred->initCredito();
$cCred->setReconvenido($monto, $interesNoPag, $tasa, $periocidad, $pagos, $observaciones, $fechaconv);

		$urlDMA		= "c=$idsolicitud&s=$idsocio&o=$interesNoPag&i=420";
		echo "<p class='aviso'>EL RECONVENIO SE REGISTRO SATISFACTORIAMENTE</p>
		<input type='button' name='miplan' value='GENERAR PLAN DE PAGOS' onClick='generarplan(\"$urlDMA\");'>";
}

?>
</body>
<script  >
	function generarplan(urldma) {
		var rpURL = "./frmcreditosplandepagos.php?r=1&" + urldma;
		rptplan = window.open(rpURL);
		rptplan.focus();

	}
</script>
</html>
