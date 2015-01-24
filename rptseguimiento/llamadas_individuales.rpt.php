<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @since 2008-04-08
 * @version 1.0
 * @package seguimiento
 *  Cambios en el Archivo
 * 		2008-04-08 Creacion
 *
 */
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
$credito			= $_GET["c"];
$socio				= $_GET["s"];
/**
 * @var $sOrden Indica que una cadena compuesta va a pasar en vez de parametros
 * tipo compuesto socio/solicitud
 * */

$sOrden				= $_GET["o"];
if ( isset($sOrden) ){
	$DO 		= explode("|", $sOrden);
	$socio		= $DO[0];
	$credito	= $DO[1];
}
$compCredito		= "";
$compFecha			= "";
$BySocio			= "";

if (isset($credito)){
	$compCredito	= "	AND
	(`seguimiento_llamadas`.`numero_solicitud` = $credito)";
}

if ( isset($fecha_final) ){
	if ( isset($fecha_inicial) ){
		$compFecha	= " AND
		(`seguimiento_llamadas`.`fecha_llamada` >= '$fecha_inicial')
		AND
		(`seguimiento_llamadas`.`fecha_llamada` <= '$fecha_final')";
	} else {
		$compFecha	= "
		AND
		(`seguimiento_llamadas`.`fecha_llamada` = '$fecha_final')";
	}
}

$input 				= $_GET["out"];
	if (!$input) {
		$input = "default";
	}


	$setSql = "

SELECT
	`oficiales`.`id`								AS `oficial`,
	`oficiales`.`nombre_completo`					,
	`oficiales`.`puesto`,
	`oficiales`.`sucursal`,
	`socios`.`codigo`,
	`socios`.`nombre`,
	`seguimiento_llamadas`.`numero_solicitud`       AS `credito`,
	`seguimiento_llamadas`.`idseguimiento_llamadas` AS `clave`,
	`seguimiento_llamadas`.`fecha_llamada`          AS `fecha`,
	`seguimiento_llamadas`.`hora_llamada`           AS `hora`,
	`seguimiento_llamadas`.`estatus_llamada`        AS `estatus`,
	`seguimiento_llamadas`.`observaciones`          AS `resultados`
FROM
	`seguimiento_llamadas` `seguimiento_llamadas`
		INNER JOIN `socios` `socios`
		ON `seguimiento_llamadas`.`numero_socio` = `socios`.`codigo`
			INNER JOIN `oficiales` `oficiales`
			ON `seguimiento_llamadas`.`oficial_a_cargo` = `oficiales`.`id`
WHERE
	(`socios`.`codigo` =	$socio)
	$compCredito
	$compFecha
ORDER BY
	`oficiales`.`id`,
	`seguimiento_llamadas`.`fecha_llamada`,
	`seguimiento_llamadas`.`hora_llamada`,
	`seguimiento_llamadas`.`estatus_llamada`";

//	exit($setSql);

if ($input!=OUT_EXCEL) {

	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report76.xml");
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