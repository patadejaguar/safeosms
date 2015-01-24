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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");
include_once("../reports/PHPReportMaker.php");

$oficial = elusuario($iduser);
//=====================================================================================================
$fecha_inicial 		= $_GET["on"];
$fecha_final 		= $_GET["off"];
$f1 				= $_GET["f1"];
$f71 				= $_GET["f71"];
$input 				= $_GET["out"];
	if (!$input) {
		$input 		= "default";
	}
$filter_01 = "";

if($f71==1){
$filter_01 			= " AND `creditos_solicitud`.`periocidad_de_pago` = $f1";
}

	$setSql = " SELECT
	`socios_grupossolidarios`.`nombre_gruposolidario`,
	/* `socios_grupossolidarios`.`representante_numerosocio`, */
	`socios_grupossolidarios`.`representante_nombrecompleto`,
	`creditos_solicitud`.`numero_solicitud`,
	`creditos_solicitud`.`numero_socio`,
	`creditos_solicitud`.`fecha_ministracion`,
	`creditos_solicitud`.`fecha_vencimiento`,
	`creditos_solicitud`.`fecha_ultimo_mvto`,
	`creditos_tipoconvenio`.`descripcion_tipoconvenio`,
	`creditos_solicitud`.`saldo_actual`,
	`creditos_solicitud`.`periocidad_de_pago` AS 'periocidad',
	/* `creditos_periocidadpagos`.`descripcion_periocidadpagos`,*/
	DATEDIFF(CURDATE(), `creditos_solicitud`.`fecha_ultimo_mvto`) AS 'dias_inactivos',
	ROUND(DATEDIFF(CURDATE(), `creditos_solicitud`.`fecha_ultimo_mvto`) /`creditos_solicitud`.`periocidad_de_pago`) AS 'periodos_vencidos'
FROM
	`creditos_tipoconvenio` `creditos_tipoconvenio`
		INNER JOIN `creditos_solicitud` `creditos_solicitud`
		ON `creditos_tipoconvenio`.`idcreditos_tipoconvenio` =
		`creditos_solicitud`.`tipo_convenio`
			INNER JOIN `socios_grupossolidarios` `socios_grupossolidarios`
			ON `socios_grupossolidarios`.`idsocios_grupossolidarios` =
			`creditos_solicitud`.`grupo_asociado`
				RIGHT OUTER JOIN `creditos_periocidadpagos`
				`creditos_periocidadpagos`
				ON `creditos_periocidadpagos`.`idcreditos_periocidadpagos` =
				`creditos_solicitud`.`periocidad_de_pago`
WHERE
	(`creditos_tipoconvenio`.`tipo_en_sistema` = " . CREDITO_PRODUCTO_GRUPOS . ")
	AND (`creditos_solicitud`.`saldo_actual`)>0
	$filter_01
	HAVING periodos_vencidos >0
ORDER BY
	dias_inactivos,
	`creditos_solicitud`.`fecha_vencimiento`,
	`creditos_solicitud`.`fecha_ultimo_mvto`,
	`creditos_tipoconvenio`.`tipo_de_integracion`	";

	//echo $setSql; exit;
if ($input!=OUT_EXCEL) {
//echo $setSql;
		$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report47.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();
} else {
  $filename = "export_from_" . date("YmdHi") . "_to_uid-" .  $iduser . ".xls";
	header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=$filename");
	header("Pragma: no-cache");
	header("Expires: 0");
	sqltabla($setSql, "", "fieldnames");
	//echo JS_CLOSE;
}
?>