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
/**
 * Filtrar si Existe Caja Local
 */

$frecuencia 			= (isset($_GET["f1"])) ? $_GET["f1"] : SYS_TODAS;


$estatus 				= (isset($_GET["estado"])) ? $_GET["estado"] : $estatus;
$frecuencia 			= (isset($_GET["periocidad"])) ? $_GET["periocidad"] : $frecuencia;
$frecuencia 			= (isset($_GET["frecuencia"])) ? $_GET["frecuencia"] : $frecuencia;
$convenio 				= (isset($_GET["convenio"])) ? $_GET["convenio"] : $convenio;

$es_por_estatus 		= "";
//$si_es_por_fecha
$es_por_frecuencia 	= "";
$es_por_convenio 		= "";
$BySaldo				= " AND creditos.saldo_actual>=0.99 ";
$ByFecha				= "";


if($estatus != SYS_TODAS){
	$es_por_estatus	= " AND creditos.estatus='$estatus' ";
	$BySaldo			= "";
	
}
//
if($frecuencia != SYS_TODAS){
	$nfreq 					= eltipo("creditos_periocidadpagos", $frecuencia);
	$es_por_frecuencia 	= " AND creditos.periocidad='$nfreq' ";
}
//
if($convenio != SYS_TODAS){
	$nconv 					= eltipo("creditos_tipoconvenio", $convenio);
	$es_por_convenio 		= " AND creditos.convenio = '$nconv' ";
}

$f3 						= $_GET["f3"];
$input 						= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;

	
	$setSql = "
	SELECT socios_cajalocal.idsocios_cajalocal AS 'id',
	socios.numero_caja_local,
	socios_cajalocal.descripcion_cajalocal AS 'caja_local',
	socios.nombre, creditos.*
	FROM socios_cajalocal,
	socios,
	creditos
	WHERE socios.numero_caja_local=socios_cajalocal.idsocios_cajalocal
	AND creditos.numero_socio=socios.codigo
	$BySaldo
	$ByFecha
	$es_por_convenio
	$es_por_frecuencia
	$es_por_estatus
	ORDER BY socios.numero_caja_local
	";

//exit($setSql);
if ($input!=OUT_EXCEL) {

	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report31a.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();		//	*/
} else {

  $xEx		= new cHExcel();
  $xEx->convertTable($setSql, "Creditos Generales");
}
?>