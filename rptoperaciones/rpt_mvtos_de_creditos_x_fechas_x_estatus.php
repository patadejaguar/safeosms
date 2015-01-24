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
include_once "../reports/PHPReportMaker.php";

$oficial = elusuario($iduser);
//=====================================================================================================
$on_f 		= $_GET["on"];
$off_f 		= $_GET["off"];
$f1 		= $_GET["f1"];
$input 		= $_GET["out"];
$estatus 	= $_GET["f2"];
	if (!$input) {
		$input = "default";
	}

	$setSql = "SELECT socios_general.codigo, CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ', socios_general.nombrecompleto) AS 'nombre_completo', creditos_solicitud.fecha_ministracion AS 'fecha_de_ministracion', operaciones_recibos.recibo_fiscal, ";
	$setSql .= "operaciones_mvtos.fecha_afectacion AS 'fecha_de_pago', operaciones_mvtos.docto_afectado AS 'documento', operaciones_tipos.descripcion_operacion AS 'tipo_operacion',operaciones_mvtos.periodo_socio AS 'parcialidad', operaciones_mvtosestatus.descripcion_mvtosestatus AS 'estatus',  ";
	$setSql .= "operaciones_mvtos.afectacion_real AS 'monto', operaciones_mvtos.saldo_actual, operaciones_recibos.observacion_recibo FROM socios_general, creditos_solicitud, operaciones_mvtos, operaciones_recibos, operaciones_tipos, operaciones_mvtosestatus WHERE  ";
	$setSql .= " operaciones_mvtos.estatus_mvto!=40 AND ";
	$setSql .= "creditos_solicitud.numero_socio=socios_general.codigo AND operaciones_mvtos.docto_afectado=creditos_solicitud.numero_solicitud  AND operaciones_mvtos.socio_afectado=socios_general.codigo AND operaciones_tipos.tipo_operacion=operaciones_mvtos.tipo_operacion AND ";
	$setSql .= "operaciones_mvtos.estatus_mvto=operaciones_mvtosestatus.idoperaciones_mvtosestatus AND operaciones_mvtos.recibo_afectado=operaciones_recibos.idoperaciones_recibos ";
	$setSql .= " AND creditos_solicitud.periocidad_de_pago=$f1 ";
	$setSql .= " AND operaciones_mvtos.fecha_afectacion>='$on_f' AND operaciones_mvtos.fecha_afectacion<='$off_f' ";
	$setSql .= " AND creditos_solicitud.estatus_actual=$estatus ";
	$setSql .= " ORDER BY  operaciones_mvtos.fecha_afectacion, socios_general.codigo LIMIT 0,10000";
//exit($setSql);
if ($input!=OUT_EXCEL) {
	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report15.xml");
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