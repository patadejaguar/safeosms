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

$oficial = elusuario($iduser);
//=====================================================================================================
$fecha_inicial = $_GET["on"];
$fecha_final = $_GET["off"];
$f3 = $_GET["f3"];				//?
$tipo_de_cuenta = "";			//Tipo de Cuenta ;-)

// Opcion fecha

// Opcion Tipo de Cuenta

$input = $_GET["out"];

	if (!$input) {
		$input = "default";
	}
	//manejar por fechas
	$campofecha = "fecha_apertura";
	$fecha_1 = "";
	$fecha_2 = "";
	

  include_once "../reports/PHPReportMaker.php";


	$setSql = "SELECT socios.codigo, socios.nombre, captacion_cuentas.numero_cuenta, 
	captacion_cuentastipos.descripcion_cuentastipos AS 'tipo_cuenta', 
	captacion_cuentas.$campofecha, captacion_cuentas.tasa_otorgada, 
	captacion_cuentas.saldo_cuenta FROM captacion_cuentas, socios, 
	captacion_cuentastipos 
	WHERE captacion_cuentas.numero_socio=socios.codigo AND 
	captacion_cuentastipos.idcaptacion_cuentastipos=captacion_cuentas.tipo_cuenta";
	
		
if ($input!=OUT_EXCEL) {	
//echo $setSql;
		$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report35.xml");
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
