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
include_once("../reports/PHPReportMaker.php");

$oficial = elusuario($iduser);
//=====================================================================================================
/**
 * Filtrar si hay Fecha
 */
$fecha_inicial		= $_GET["on"];
$fecha_final 		= $_GET["off"];
$estatus_actual 	= $_GET["f2"];
$cedula 			= $_GET["f21"];				//Tipo de Cedula
$si_es_por_fecha 	= "";
$filtro_extra 		= "";

if ($fecha_inicial && $fecha_final){
	//$si_es_por_fecha = " AND creditos_solicitud.fecha_ministracion>='$fecha_inicial' AND creditos_solicitud.fecha_ministracion<='$fecha_final' ";
	$si_es_por_fecha = " AND creditos_solicitud.fecha_ministracion<='$fecha_final' ";
}
switch ($cedula){
	case "110501":
		$filtro_extra = " AND creditos_solicitud.estatus_actual!=20 AND creditos_solicitud.tipo_convenio=10 ";
			break;
	case "111201":
		$filtro_extra = " AND creditos_solicitud.estatus_actual=20 AND creditos_solicitud.tipo_convenio=10 ";
			break;
	case "110601":
		$filtro_extra = " AND creditos_solicitud.estatus_actual!=20 AND creditos_solicitud.tipo_convenio!=10 ";
			break;
	case "111203":
		$filtro_extra = " AND creditos_solicitud.estatus_actual=20 AND creditos_solicitud.tipo_convenio!=10 ";
			break;
	default:
		$filtro_extra = "";
		break;
}
$f3 = $_GET["f3"];
//Estatus
$f2 = $_GET["f2"];

$input = $_GET["out"];
	if (!$input) {
		$input = "default";
	}



	//	AND creditos_solicitud.saldo_actual>=0.99
	$setSql = "SELECT socios.nombre, creditos_solicitud.numero_socio AS 'socio',
	creditos_solicitud.numero_solicitud AS 'solicitud', creditos_modalidades.descripcion_modalidades AS 'modalidad',
	creditos_periocidadpagos.titulo_en_informe AS 'condiciones_de_pago', creditos_solicitud.fecha_ministracion AS 'fecha_de_otorgamiento',
	creditos_solicitud.monto_autorizado AS 'monto_original', creditos_solicitud.fecha_vencimiento AS 'fecha_de_vencimiento',
	creditos_solicitud.tasa_interes AS 'tasa_ordinaria_nominal_anual', creditos_solicitud.pagos_autorizados AS 'numero_de_pagos',
	creditos_solicitud.periocidad_de_pago AS 'frecuencia', creditos_solicitud.saldo_conciliado AS 'saldo_insoluto',
	creditos_solicitud.fecha_conciliada AS 'fecha_ultimo_mvto', creditos_estatus.titulo_general AS 'estatus',
	socios.genero, socios.tipo_ingreso, creditos_solicitud.tipo_autorizacion AS 'modaut'
	FROM creditos_solicitud, creditos_modalidades, creditos_periocidadpagos, socios, creditos_estatus
	WHERE creditos_modalidades.idcreditos_modalidades=creditos_solicitud.tipo_credito
	AND creditos_periocidadpagos.idcreditos_periocidadpagos=creditos_solicitud.periocidad_de_pago
	AND socios.codigo=creditos_solicitud.numero_socio
	AND creditos_solicitud.estatus_actual=creditos_estatus.idcreditos_estatus
	AND creditos_solicitud.estatus_actual!=50
	AND creditos_solicitud.estatus_actual!=98 AND creditos_solicitud.estatus_actual!=99
	$si_es_por_fecha
	$filtro_extra
	AND creditos_solicitud.saldo_conciliado>=0.99
	ORDER BY creditos_solicitud.tipo_autorizacion DESC,
	creditos_estatus.orden_clasificacion ASC,
	creditos_solicitud.numero_socio	";
	//echo  $setSql; exit()	;
if ($input!=OUT_EXCEL) {

		$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report32.xml");
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