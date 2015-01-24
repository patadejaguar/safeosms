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
	(`seguimiento_notificaciones`.`numero_solicitud` = $credito)";
}

if ( isset($fecha_final) ){
	if ( isset($fecha_inicial) ){
		$compFecha	= " AND
		(`seguimiento_notificaciones`.`fecha_notificacion` >= '$fecha_inicial')
		AND
		(`seguimiento_notificaciones`.`fecha_notificacion` <= '$fecha_final')";
	} else {
		$compFecha	= "
		AND
		(`seguimiento_notificaciones`.`fecha_notificacion` = '$fecha_final')";
	}
}

$input 				= $_GET["out"];
	if (!$input) {
		$input = "default";
	}


	$setSql = "
SELECT
	`oficiales`.`id`                                    AS `oficial`,
	`oficiales`.`nombre_completo`,
	`oficiales`.`puesto`,
	`oficiales`.`sucursal`,
	`socios`.`codigo`,
	`socios`.`nombre`,
	`seguimiento_notificaciones`.`numero_solicitud`     AS `solicitud`,
	`seguimiento_notificaciones`.`numero_notificacion`  AS `clave`,
	`seguimiento_notificaciones`.`fecha_notificacion`   AS `fecha`,
	`seguimiento_notificaciones`.`fecha_vencimiento`    AS `vencimiento`,
	`seguimiento_notificaciones`.`estatus_notificacion` AS `estatus`,
	`seguimiento_notificaciones`.`observaciones`        AS `observaciones` 
FROM
	`seguimiento_notificaciones` `seguimiento_notificaciones` 
		INNER JOIN `oficiales` `oficiales` 
		ON `seguimiento_notificaciones`.`oficial_de_seguimiento` = `oficiales`.
		`id` 
			INNER JOIN `socios` `socios` 
			ON `seguimiento_notificaciones`.`socio_notificado` = `socios`.
			`codigo` 
WHERE
	(`socios`.`codigo` =	$socio)
	$compCredito
	$compFecha
	
	ORDER BY
		`oficiales`.`id`,
		`socios`.`codigo`";

//	exit($setSql);

if ($input!=OUT_EXCEL) {

	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report77.xml");
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