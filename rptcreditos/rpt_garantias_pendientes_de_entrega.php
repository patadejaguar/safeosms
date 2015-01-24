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


	$setSql = "SELECT
	`socios`.`codigo`,
	`socios`.`nombre`,
	`creditos_solicitud`.`numero_solicitud`,
	`creditos_solicitud`.`saldo_actual`,
	`creditos_garantias`.`idcreditos_garantias`,
	`creditos_tgarantias`.`descripcion_tgarantias`,
	`creditos_garantias`.`fecha_resguardo`,
	`creditos_garantias`.`monto_valuado`
FROM
	`socios` `socios`
		INNER JOIN `creditos_solicitud` `creditos_solicitud`
		ON `socios`.`codigo` = `creditos_solicitud`.`numero_socio`
			INNER JOIN `creditos_garantias` `creditos_garantias`
			ON `creditos_solicitud`.`numero_solicitud` = `creditos_garantias`.
			`solicitud_garantia`
				INNER JOIN `creditos_tgarantias` `creditos_tgarantias`
				ON `creditos_garantias`.`tipo_garantia` = `creditos_tgarantias`.
				`idcreditos_tgarantias`
WHERE
	(`creditos_solicitud`.`saldo_actual` <=0)
	AND
	(`creditos_garantias`.`fecha_resguardo` >='$fecha_inicial')
	AND
	(`creditos_garantias`.`fecha_resguardo` <='$fecha_final')
	AND
	(creditos_garantias.estatus_actual = 2) ";
//			*/

if ($input!=OUT_EXCEL) {
//echo $setSql;
	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report62.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();		//	*/
} else {
	$filename = $_SERVER['SCRIPT_NAME'];
	$filename = str_replace(".php", "", $filename);
	$filename = str_replace("rpt", "", $filename);
	$filename = str_replace("-", "", 	$filename);
  	$filename = "$filename-" . date("YmdHi") . "-from-" .  $iduser . ".xls";

  	header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=$filename");
	header("Pragma: no-cache");
	header("Expires: 0");

	$cTbl = new cTabla($setSql);
	$cTbl->setWidth();
	$cTbl->Show("", false);
}
?>