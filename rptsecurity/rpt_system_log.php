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
$xHP		= new cHPage("TR.Eventos del sistema", HP_RPTXML);

$oficial = elusuario($iduser);
//=====================================================================================================
$fecha_inicial 		= $_GET["on"];
$fecha_final 		= $_GET["off"];
$input 				= $_GET["out"];

	if (!$input) {
		$input = "default";
	}

$logType 		= $_GET["f1"];
$siType 		= $_GET["f71"];
$errType		= $_GET["codigo"];
$cUsuario		= $_GET["usuario"];
$cBuscar		= $_GET["buscar"];

$ByNivel 		= "";

$ByCodigo		= "";
$ByUsuario		= "";
$ByLike			= "";


//TIPO: developer, comun, etc
if( (isset($logType) ) and ($logType != "todas") ){
	$ByNivel = " AND (`general_error_codigos`.`type_err`='$logType') ";
}
//Codigo de error
if((isset($errType)) and ($errType != "todas") ){
	$ByCodigo ="  AND `general_log`.`type_error`='$errType' ";
}

if((isset($cUsuario)) and ($cUsuario != "todas") ){
	$ByUsuario ="  AND `general_log`.`usr_log`='$cUsuario' ";
}

if ( isset($cBuscar) AND $cBuscar != "" ){
	$ByLike		= "  AND `general_log`.`text_log` LIKE '%$cBuscar%' ";
}

	$setSql = " SELECT
	`general_log`.`fecha_log`            AS `fecha`,
	`general_log`.`hour_log`             AS `hora`,
	`general_error_codigos`.`description_error` AS `Descripcion`,
	getUserByID(`general_log`.`usr_log`)        AS `usuario`,
	`general_log`.`text_log`             AS `texto`,
	`general_error_codigos`.`type_err`	AS `tipo`
FROM
	`general_error_codigos` `general_error_codigos`
		INNER JOIN `general_log` `general_log`
		ON `general_error_codigos`.`idgeneral_error_codigos` =
		`general_log`.`type_error`
	WHERE (`general_log`.`fecha_log` >='$fecha_inicial')
		AND
		(`general_log`.`fecha_log` <='$fecha_final')
	$ByNivel
	$ByCodigo
	$ByUsuario
	$ByLike
";


if ($input!=OUT_EXCEL) {

	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report53.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();
} else {
  $filename = "export_from_" . date("YmdHi") . "_to_uid-" .  $iduser . ".xls";
	header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=$filename");
	header("Pragma: no-cache");
	header("Expires: 0");

	$cTbl = new cTabla($setSql);
	$cTbl->setWidth();
	$cTbl->Show("", false);
}
?>
