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
$fecha_inicial 	= $_GET["on"];
$fecha_final 	= $_GET["off"];
$f3 			= $_GET["f3"];
$input			= $_GET["out"];
	if (!$input) {
		$input = "default";
	}

$mSuc				= $_GET["s"];
if($mSuc!="todas" AND isset($mSuc) ){
	$BySuc			= " WHERE `socios_general`.`sucursal`= '$mSuc'";
}


	$setSql = " SELECT
	`socios_estatus`.`nombre_estatus`          AS `estatus`,
	`socios_estatus`.`tipo_estatus`,
	`socios_cajalocal`.`idsocios_cajalocal`    AS `numero_caja_local`,
	`socios_cajalocal`.`descripcion_cajalocal` AS `nombre_caja_local`,
	COUNT(`socios_general`.`codigo`)				AS `numero_de_socios` ,
	`socios_cajalocal`.`sucursal`
FROM
	`socios_general` `socios_general` 
		INNER JOIN `socios_estatus` `socios_estatus` 
		ON `socios_general`.`estatusactual` = `socios_estatus`.`tipo_estatus` 
			INNER JOIN `socios_cajalocal` `socios_cajalocal` 
			ON `socios_general`.`cajalocal` = `socios_cajalocal`.
			`idsocios_cajalocal` 
$BySuc
		GROUP BY
			`socios_estatus`.`tipo_estatus`,
			`socios_cajalocal`.`idsocios_cajalocal` 
		ORDER BY
			`socios_estatus`.`tipo_estatus`,
			`socios_cajalocal`.`idsocios_cajalocal`";


if ($input!=OUT_EXCEL) {
//echo $setSql;
		$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report40.xml");
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