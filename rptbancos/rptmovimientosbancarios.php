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

$xF		= new cFecha();
$out 		= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;

$mx 		= (isset($_GET["mx"])) ? true : false;
if($mx == true){
	$fechaInicial	= (isset($_GET["on"])) ? $xF->getFechaISO( $_GET["on"]) : FECHA_INICIO_OPERACIONES_SISTEMA;
	$fechaFinal	= (isset($_GET["off"])) ? $xF->getFechaISO( $_GET["off"]) : fechasys();
} else {
	$fechaInicial	= (isset($_GET["on"])) ? $_GET["on"] : FECHA_INICIO_OPERACIONES_SISTEMA;
	$fechaFinal	= (isset($_GET["off"])) ? $_GET["off"] : fechasys();
}


$cuenta				= (isset($_GET["cuenta"]))  ? $_GET["cuenta"] : SYS_TODAS;
$operacion			= (isset($_GET["operacion"])) ? $_GET["operacion"] : SYS_TODAS;

$ByCuenta		= ($cuenta != SYS_TODAS AND $cuenta != "") ? " AND `bancos_cuentas`.`idbancos_cuentas`=$cuenta " : "";
$ByOperaciones		= ($operacion != SYS_TODAS AND $operacion != "") ? " AND `bancos_operaciones`.`tipo_operacion`='$operacion' " : "";


	$setSql = " SELECT
	`bancos_cuentas`.`idbancos_cuentas`,
	`bancos_cuentas`.`descripcion_cuenta`,
	`bancos_cuentas`.`tipo_de_cuenta`,
	`bancos_operaciones`.`tipo_operacion`,
	`bancos_operaciones`.`numero_de_documento`,
	`bancos_operaciones`.`recibo_relacionado`,
	getFechaMX(`bancos_operaciones`.`fecha_expedicion`) AS `fecha_expedicion`,
	`bancos_operaciones`.`beneficiario`,
	`bancos_operaciones`.`monto_descontado`,
	`bancos_operaciones`.`monto_real` 
FROM
	`bancos_operaciones` `bancos_operaciones` 
		INNER JOIN `bancos_cuentas` `bancos_cuentas` 
		ON `bancos_operaciones`.`cuenta_bancaria` = 
		`bancos_cuentas`.`idbancos_cuentas`
WHERE
	(`bancos_operaciones`.`fecha_expedicion`>= '$fechaInicial' )
	AND
	(`bancos_operaciones`.`fecha_expedicion`<= '$fechaFinal' )
	$ByCuenta $ByOperaciones
	ORDER BY `bancos_cuentas`.`idbancos_cuentas`, `bancos_operaciones`.`fecha_expedicion`, `bancos_operaciones`.`tipo_operacion`
	";

//exit($setSql);		
if ($out != OUT_EXCEL) {	

	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report60.xml");
	$oOut = $oRpt->createOutputPlugin($out);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();
	
} else {
	$xEc	= new cHExcel();
	$xEc->convertTable($setSql);
}
?>