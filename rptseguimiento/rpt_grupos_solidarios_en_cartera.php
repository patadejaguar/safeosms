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
$f3 				= $_GET["f3"];
$input 				= $_GET["out"];
	if (!$input) {
		$input = "default";
	}


	$setSql = " SELECT socios_grupossolidarios.nombre_gruposolidario,
			socios_grupossolidarios.colonia_gruposolidario AS 'colonia',
			socios_grupossolidarios.representante_numerosocio,
			socios_grupossolidarios.representante_nombrecompleto,
			creditos_solicitud.numero_solicitud,
			creditos_solicitud.fecha_ministracion AS 'ministrado',
			creditos_solicitud.fecha_vencimiento AS
			'vencimiento',
			creditos_solicitud.fecha_ultimo_mvto AS 'ultima_operacion',
			creditos_solicitud.saldo_actual,
			creditos_solicitud.periocidad_de_pago,
			DATEDIFF(CURDATE(), creditos_solicitud.fecha_ultimo_mvto) AS 'dias_inactivos'
			FROM socios_grupossolidarios, creditos_solicitud
			WHERE socios_grupossolidarios.idsocios_grupossolidarios=creditos_solicitud.grupo_asociado
				AND creditos_solicitud.grupo_asociado!=99
				AND creditos_solicitud.saldo_actual>0
			HAVING dias_inactivos > creditos_solicitud.periocidad_de_pago
			ORDER BY fecha_vencimiento ";


if ($input!=OUT_EXCEL) {
//echo $setSql;
		$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report39.xml");
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
}
?>