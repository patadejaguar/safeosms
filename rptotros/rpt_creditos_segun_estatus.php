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
$caja_local = $_GET["pa3"];

$si_es_caja_local = "  AND socios.numero_caja_local=$caja_local ";
if (!$caja_local) {
	$si_es_caja_local = " "	;
}
//$fecha_final = $_GET["off"];
$f3 = $_GET["f3"];
$input = $_GET["out"];
	if (!$input) {
		$input = "default";
	}

	if (stristr(PHP_OS,"WIN")) {
		ini_set("include_path",ini_get("include_path")."");
	} else {
		ini_set("include_path","/usr/share/php5/reports");
	}


	/* ******************************************************************************/


	$setSql = "
	SELECT socios_cajalocal.idsocios_cajalocal AS 'id', socios.numero_caja_local, socios_cajalocal.descripcion_cajalocal AS 'caja_local', socios.nombre, creditos.*
	FROM socios_cajalocal, socios, creditos
	WHERE socios.numero_caja_local=socios_cajalocal.idsocios_cajalocal AND creditos.numero_socio=socios.codigo
	AND creditos.saldo_actual>=0.99
	$si_es_caja_local

	";


if ($input!=OUT_EXCEL) {

		$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report31.xml");
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