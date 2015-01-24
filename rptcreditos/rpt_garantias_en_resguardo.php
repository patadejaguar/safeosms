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
include_once "../libs/sql.inc.php";
include_once "../reports/PHPReportMaker.php";

$oficial = elusuario($iduser);
//=====================================================================================================
$fecha_inicial 		= $_GET["on"];
$fecha_final 		= $_GET["off"];
$es_por_fechas 		= "";
$si_fechas 			= $_GET["f73"];

$si_est 			= $_GET["f70"];
$si_freq 			= $_GET["f71"];
$si_conv 			= $_GET["f72"];
$estatus 			= $_GET["f2"];
$frecuencia 		= $_GET["f1"];
$convenio 			= $_GET["f3"];
$es_por_estatus 	= "";
//$si_es_por_fecha
$es_por_frecuencia 	= "";
$es_por_convenio 	= "";
if($si_est==1){
	//$nest = eltipo("creditos_estatus", $estatus);
	$es_por_estatus 	= " AND creditos_solicitud.estatus_actual=$estatus ";
}
//
if($si_freq == 1){
	//$nfreq = eltipo("creditos_periocidadpagos", $frecuencia);
	$es_por_frecuencia 	= " AND creditos_solicitud.periocidad_de_pago=$frecuencia ";
}
//
if($si_conv == 1){
	//$nconv = eltipo("creditos_tipoconvenio", $convenio);
	$es_por_convenio 	= " AND creditos_solicitud.tipo_convenio = $convenio ";
}
//
if($si_fechas == 1){
	$es_por_fechas 		= " AND creditos_garantias.fecha_resguardo>='$fecha_inicial' AND creditos_garantias.fecha_resguardo<='$fecha_final' ";
}


$input = $_GET["out"];
	if (!$input) {
		$input = "default";
	}






	$setSql .= "SELECT creditos_garantias.socio_garantia AS 'socio',
	socios.nombre,
	creditos_garantias.solicitud_garantia AS 'solicitud',
	creditos_garantias.idcreditos_garantias AS 'codigo_control',
	creditos_tgarantias.descripcion_tgarantias AS 'tipo_de_gtia',
	creditos_garantias.fecha_resguardo,
	creditos_garantias.monto_valuado AS 'valor'

FROM socios, creditos_garantias, creditos_tgarantias
	WHERE creditos_garantias.socio_garantia=socios.codigo
	AND creditos_tgarantias.idcreditos_tgarantias=creditos_garantias.tipo_garantia
	AND creditos_garantias.estatus_actual = 2
	$es_por_fechas
	";
	//echo $setSql; exit;
if ($input!=OUT_EXCEL) {
//echo $setSql;
		$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report41.xml");
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