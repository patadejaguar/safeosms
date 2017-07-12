<?php
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
	include_once "../core/core.html.inc.php";
	$xHP		= new cHPage();
	$xHP->setIncludes();
	
	include_once("../reports/PHPReportMaker.php");
	$oficial = elusuario($iduser);
//=====================================================================================================

$fecha_inicial 			= $_GET["on"];
$fecha_final 			= $_GET["off"];
$f1						= $_GET["f1"];
$recibo 				= $_GET["f10"];
$input 					= $_GET["out"];

if ( !isset($recibo) ){
	$recibo				= $_GET["recibo"];
}
if (!$input) {
		$input 			= "default";
}


$setSql = " SELECT
	`operaciones_recibos`.`fecha_operacion`             AS `fecha_de_recibo`,
	`operaciones_recibostipo`.`descripcion_recibostipo` AS `tipo_de_recibo`,
	`operaciones_recibos`.`total_operacion`             AS `total_recibo`,
	`operaciones_recibos`.`observacion_recibo`          AS `observaciones`,
	`operaciones_recibos`.`cheque_afectador`            AS `cheque`,
	`operaciones_recibos`.`tipo_pago`                   AS `forma_de_pago`,
	`operaciones_recibos`.`idoperaciones_recibos`       AS `recibo`,
	`operaciones_mvtos`.`socio_afectado`                AS `socio`,
	CONCAT(`socios_general`.`apellidopaterno`, ' ', `socios_general`.`apellidomaterno`, ' ',
     `socios_general`.`nombrecompleto`)                 AS `nombre`,
	`operaciones_mvtos`.`docto_afectado`                AS `documento`,
	`operaciones_tipos`.`descripcion_operacion`         AS `operacion`,
	`operaciones_mvtos`.`fecha_vcto`                    AS `fecha`,
	`operaciones_mvtos`.`afectacion_real`               AS `monto`,
	`operaciones_mvtos`.`periodo_socio`              	AS `periodo`
FROM
	`operaciones_mvtos` `operaciones_mvtos`
		INNER JOIN `operaciones_tipos` `operaciones_tipos`
		ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
		`idoperaciones_tipos`
			INNER JOIN `operaciones_recibos` `operaciones_recibos`
			ON `operaciones_recibos`.`idoperaciones_recibos` =
			`operaciones_mvtos`.`recibo_afectado`
				INNER JOIN `operaciones_recibostipo` `operaciones_recibostipo`
				ON `operaciones_recibos`.`tipo_docto` =
				`operaciones_recibostipo`.`idoperaciones_recibostipo`
					INNER JOIN `socios_general` `socios_general`
					ON `operaciones_mvtos`.`socio_afectado` = `socios_general`.
					`codigo`
WHERE
	(`operaciones_recibos`.`idoperaciones_recibos` = $recibo )
ORDER BY
	`operaciones_recibos`.`idoperaciones_recibos`,
	`operaciones_mvtos`.`socio_afectado`,
	`operaciones_mvtos`.`docto_afectado`,
	`operaciones_mvtos`.`periodo_socio`
";

if ($input!=OUT_EXCEL) {

	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report22-B.xml");
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