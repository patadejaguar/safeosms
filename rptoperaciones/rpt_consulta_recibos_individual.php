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
include_once( "../core/entidad.datos.php");
include_once( "../core/core.deprecated.inc.php");
include_once( "../core/core.fechas.inc.php");
include_once( "../libs/sql.inc.php");
include_once( "../core/core.config.inc.php");
include_once "../reports/PHPReportMaker.php";

$oficial = elusuario($iduser);
//=====================================================================================================

$fecha_inicial 		= $_GET["on"];
$fecha_final 		= $_GET["off"];
$f1					= $_GET["f1"];
$recibo 			= parametro("f10", false, MQL_INT);
$recibo				= parametro("recibo", $recibo, MQL_INT);
$input 				= parametro("out", SYS_DEFAULT, MQL_RAW);

  /******************************************************************************
	*																										*
	*	Use this file to see a sample of PHPReports.											*
	*	Please check the PDF manual for see how to use it.									*
	*	It need to be placed on a directory reached by the web server.					*
	*																										*
	******************************************************************************/

$setSql = "SELECT operaciones_recibos.idoperaciones_recibos, operaciones_recibos.recibo_fiscal, operaciones_recibostipo.descripcion_recibostipo AS 'tipo_de_recibo', `operaciones_recibos`.`observacion_recibo` AS `observaciones`, ";
$setSql .= " operaciones_recibos.fecha_operacion AS 'fecha', operaciones_recibos.numero_socio, ";
$setSql .= " CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ',socios_general.nombrecompleto) AS 'nombre_completo', ";
$setSql .= " operaciones_recibos.docto_afectado AS 'documento', operaciones_recibos.total_operacion AS 'total', operaciones_recibos.tipo_pago AS 'tipo_de_pago', ";
$setSql .= " operaciones_tipos.descripcion_operacion AS 'tipo_de_operacion', operaciones_mvtosestatus.descripcion_mvtosestatus AS 'estatus', ";
$setSql .= " operaciones_mvtos.afectacion_real AS 'monto', operaciones_mvtos.fecha_afectacion AS 'fecha_de_afectacion', ";
$setSql .= " operaciones_mvtos.fecha_vcto AS 'fecha_de_vencimiento', operaciones_mvtos.periodo_socio AS 'periodo_del_socio', ";
$setSql .= " operaciones_mvtos.docto_neutralizador, operaciones_mvtos.saldo_actual FROM operaciones_recibos,operaciones_recibostipo,  ";
$setSql .= " socios_general, operaciones_mvtos, operaciones_tipos, operaciones_mvtosestatus ";
$setSql .= " WHERE socios_general.codigo=operaciones_recibos.numero_socio AND  operaciones_mvtos.recibo_afectado=operaciones_recibos.idoperaciones_recibos ";
$setSql .= " AND operaciones_tipos.idoperaciones_tipos=operaciones_mvtos.tipo_operacion AND operaciones_recibostipo.idoperaciones_recibostipo=operaciones_recibos.tipo_docto ";
$setSql .= " AND operaciones_mvtosestatus.idoperaciones_mvtosestatus=operaciones_mvtos.estatus_mvto ";
$setSql .= " AND operaciones_recibos.idoperaciones_recibos=$recibo ";
$setSql .= " ORDER BY operaciones_recibos.idoperaciones_recibos, operaciones_recibos.fecha_operacion, ";
$setSql .= " operaciones_mvtos.idoperaciones_mvtos ";

if ($input!=OUT_EXCEL) {
//echo $setSql;
		$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report22.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();		//	*/
} else {
  $filename = "export_from_" . date("YmdHi") . "_to_uid-" .  $iduser . ".xls";
	header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=$filename");
	header("Pragma: no-cache");
	header("Expires: 0");
	sqltabla($setSql, "", "fieldnames");
}
?>