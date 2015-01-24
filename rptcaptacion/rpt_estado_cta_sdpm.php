<?php
/**
 * @author 		Balam Gonzalez Luis
 * @version 	07/04/2008 1.0
 * @since 		2007-04-07
 * @package 	captacion
 *  		Modificaciones
 * 		-07/04/2008 Primera Version
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
$xHP		= new cHPage("TR.Estado de Cuenta de Depositos a la Vista", HP_RPTXML);
$xQL		= new MQL();
$xF			= new cFecha();

$oficial = elusuario($iduser);
//=====================================================================================================

$idcuenta				= parametro("f100", false, MQL_INT);
$idcuenta 				= parametro("cuenta", $idcuenta, MQL_INT);
$AppByFechas			= parametro("f73");		//Boolean por fechas
$fecha_inicial 			= parametro("on", EACP_FECHA_DE_CONSTITUCION, MQL_DATE);
$fecha_final 			= parametro("off", $xF->getFechaMaximaOperativa(), MQL_DATE);
$output					= parametro("out", SYS_DEFAULT);



	$setSql = "SELECT
	`socios`.`codigo`,
	`socios`.`nombre`,
	`captacion_cuentas`.`numero_cuenta`                 AS `numero_de_cuenta`,
	`captacion_cuentastipos`.`descripcion_cuentastipos` AS `tipo`,
	`captacion_subproductos`.`descripcion_subproductos` AS `producto`,
	`captacion_cuentas`.`tasa_otorgada`,
	`captacion_cuentas`.`dias_invertidos`,
	`captacion_cuentas`.`saldo_cuenta`,
	`captacion_cuentas`.`sucursal`,
	`captacion_sdpm_historico`.`ejercicio`,
	`captacion_sdpm_historico`.`periodo`,
	`captacion_sdpm_historico`.`fecha`,
	`captacion_sdpm_historico`.`recibo`,
	`captacion_sdpm_historico`.`dias`,
	`captacion_sdpm_historico`.`tasa`,
	`captacion_sdpm_historico`.`monto`
FROM
	`captacion_cuentas` `captacion_cuentas`
		INNER JOIN `captacion_sdpm_historico` `captacion_sdpm_historico`
		ON `captacion_cuentas`.`numero_cuenta` = `captacion_sdpm_historico`.
		`cuenta`
			INNER JOIN `captacion_cuentastipos` `captacion_cuentastipos`
			ON `captacion_cuentas`.`tipo_cuenta` = `captacion_cuentastipos`.
			`idcaptacion_cuentastipos`
				INNER JOIN `socios` `socios`
				ON `socios`.`codigo` = `captacion_cuentas`.`numero_socio`
					INNER JOIN `captacion_subproductos` `captacion_subproductos`
					ON `captacion_cuentas`.`tipo_subproducto` =
					`captacion_subproductos`.`idcaptacion_subproductos`
WHERE
	(`captacion_cuentas`.`numero_cuenta` =$idcuenta)
	AND
	(`captacion_sdpm_historico`.`fecha`>='$fecha_inicial')
	AND
	(`captacion_sdpm_historico`.`fecha`<='$fecha_final')
ORDER BY
	`socios`.`codigo`,
	`captacion_cuentas`.`numero_cuenta`,
	`captacion_sdpm_historico`.`ejercicio` ASC,
	`captacion_sdpm_historico`.`periodo` ASC,	
	`captacion_sdpm_historico`.`fecha` ASC";

//exit($setSql);

if ($output!=OUT_EXCEL) {

	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report70.xml");
	$oOut = $oRpt->createOutputPlugin($output);
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