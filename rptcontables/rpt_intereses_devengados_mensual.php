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
include_once("../core/core.html.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");
include_once("../reports/PHPReportMaker.php");

$oficial = elusuario($iduser);
//=====================================================================================================

$mes			 	= $_GET["m"];
$anno				= $_GET["a"];
$input				= $_GET["out"];
$tipo				= $_GET["t"];
$ica				= $_GET["ica"];

$InTable			= "";

$xF					= new cFecha(0, "$anno-$mes-01");
$dtitle				= $xF->getFechaMediana();

switch($ica){
	case "todas":
		$InTable	= "interes_normal_devengado";
		break;
	case "1":
		$InTable	= "interes_normal_devengado_solo_ica";
		break;
	case "0":
		$InTable	= "interes_normal_devengado_sin_ica";
		break;
}
	$sSql[2] = " SELECT
	`socios`.`codigo`,
	`socios`.`nombre`,
	`creditos_solicitud`.`numero_solicitud`            AS `solicitud`,
	`creditos_tipoconvenio`.`descripcion_tipoconvenio` AS `convenio`,
	`creditos_solicitud`.`fecha_ministracion`,
	`creditos_solicitud`.`monto_autorizado`            AS `saldo_historico`,
	`creditos_solicitud`.`pagos_autorizados`           AS `pagos`,
	`creditos_solicitud`.`periocidad_de_pago`            AS `periocidad`,
	`creditos_solicitud`.`tipo_autorizacion`,
	`creditos_solicitud`.`fecha_conciliada`           AS `ultima_operacion`,
	`creditos_solicitud`.`saldo_conciliado`           AS `saldo_insoluto`,
	
	`creditos_solicitud`.`fecha_conciliada`,
	`creditos_solicitud`.`saldo_conciliado`,
	`$InTable`.`ejercicio`,
	`$InTable`.`periodo`,
	`$InTable`.`interes`,
	`creditos_solicitud`.`estatus_actual`
FROM
	`socios` `socios`
		INNER JOIN `creditos_solicitud` `creditos_solicitud`
		ON `socios`.`codigo` = `creditos_solicitud`.`numero_socio`
			INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio`
			ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
			`idcreditos_tipoconvenio`
				INNER JOIN `$InTable` `$InTable`
				ON `creditos_solicitud`.`numero_solicitud` =
				`$InTable`.`docto_afectado`
WHERE
	(`$InTable`.`periodo` = $mes)
	AND
	(`$InTable`.`ejercicio` =$anno)

ORDER BY
	`creditos_solicitud`.`estatus_actual`,
	`creditos_tipoconvenio`.`tipo_autorizacion` DESC,
	`creditos_solicitud`.`tipo_convenio`,
	`$InTable`.`ejercicio`,
	`$InTable`.`periodo`,
	`socios`.`codigo`,
	`creditos_solicitud`.`numero_solicitud`
";

	$uPer = date("Ym", strtotime("$anno-$mes-01") );

	$sSql[1] = "SELECT
	`socios`.`codigo`,
	`socios`.`nombre`,
	`creditos_solicitud`.`numero_solicitud`            AS `solicitud`,
	`creditos_tipoconvenio`.`descripcion_tipoconvenio` AS `convenio`,
	`creditos_solicitud`.`fecha_ministracion`,
	`creditos_solicitud`.`monto_autorizado`            AS `saldo_historico`,
	`creditos_solicitud`.`pagos_autorizados`           AS `pagos`,
	`creditos_solicitud`.`periocidad_de_pago`            AS `periocidad`,
	`creditos_solicitud`.`tipo_autorizacion`,
	`creditos_solicitud`.`fecha_conciliada`           AS `ultima_operacion`,
	`creditos_solicitud`.`saldo_conciliado`           AS `saldo_insoluto`,

	`creditos_solicitud`.`fecha_conciliada`,
	`creditos_solicitud`.`saldo_conciliado`,
	

	MAX(`interes_devengado_por_cobrar`.`ejercicio`) AS 'ejercicio',
	MAX(`interes_devengado_por_cobrar`.`periodo`) AS 'periodo',
	setNoMenorCero(
		SUM(`interes_devengado_por_cobrar`.`interes`)
	)	AS 'interes',
	`creditos_solicitud`.`interes_normal_devengado`,
	`creditos_solicitud`.`interes_normal_pagado`,
	`creditos_solicitud`.`sdo_int_ant`	AS 'saldo_ica',
	setNoMenorCero(
		(`creditos_solicitud`.`interes_normal_devengado` -
		(`creditos_solicitud`.`interes_normal_pagado` + `creditos_solicitud`.`sdo_int_ant`) )
	) AS 'saldo_interes',
	`creditos_solicitud`.`estatus_actual`

FROM
	`socios` `socios`
		INNER JOIN `creditos_solicitud` `creditos_solicitud`
		ON `socios`.`codigo` = `creditos_solicitud`.`numero_socio`
			INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio`
			ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
			`idcreditos_tipoconvenio`
				INNER JOIN `interes_devengado_por_cobrar` `interes_devengado_por_cobrar`
				ON `creditos_solicitud`.`numero_solicitud` =
				`interes_devengado_por_cobrar`.`docto_afectado`
WHERE
	(`interes_devengado_por_cobrar`.`indice` <= $uPer)
	AND
	(`creditos_solicitud`.`saldo_conciliado` >" . TOLERANCIA_SALDOS . ")
	AND
	(`creditos_solicitud`.`estatus_actual` != 50)
GROUP BY
	`creditos_solicitud`.`numero_solicitud`
ORDER BY
	`creditos_solicitud`.`estatus_actual`,
	`creditos_solicitud`.`tipo_convenio`,
	`socios`.`codigo`,
	`creditos_solicitud`.`numero_solicitud` ";

$setSql = $sSql[$tipo];


//exit( $setSql );

if ($input!=OUT_EXCEL) {
//
	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report71.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();		//	*/
} else {
	//$xHTO		= new cHObject();
	$filename	= "interes-devengado-$dtitle";
  	$filename = "$filename-" . date("YmdHi") . ".xls";

  	header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=$filename");
	header("Pragma: no-cache");
	header("Expires: 0");

	$cTbl = new cTabla($setSql);
	$cTbl->setWidth();
	$cTbl->Show("", false);
}
?>