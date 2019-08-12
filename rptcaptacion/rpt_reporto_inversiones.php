<?php
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once ("../core/core.error.inc.php");
	$permiso = getSIPAKALPermissions(__FILE__);
	if($permiso === false){
		header ("location:../404.php?i=999");	
	}
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");

$fecha_inicial = $_GET["on"];
$fecha_final = $_GET["off"];
$input = $_GET["out"];
	if (!$input) {
		$input = "default";
	}


  include_once "../reports/PHPReportMaker.php";

	$sql_set = "SELECT socios_general.codigo, CONCAT(socios_general.nombrecompleto, ' ', socios_general.apellidopaterno, ' ', socios_general.apellidomaterno) AS 'nombre_completo', 
	captacion_cuentas.numero_cuenta AS 'numero_de_cuenta',  captacion_cuentas.fecha_apertura AS 'fecha_de_apertura',
	captacion_cuentastipos.descripcion_cuentastipos AS 'tipo_de_cuenta' , captacion_cuentas.saldo_cuenta AS 'saldo_actual', 
	captacion_cuentas.inversion_fecha_vcto AS 'proximo_vencimiento', captacion_cuentas.tasa_otorgada, captacion_cuentas.dias_invertidos
	AS 'numero_de_dias', captacion_cuentas.observacion_cuenta AS 'observaciones', operaciones_mvtos.fecha_afectacion AS 'fecha_de_operacion', 
	operaciones_tipos.descripcion_operacion AS 'tipo_de_operacion', operaciones_mvtos.afectacion_real AS 'monto', operaciones_mvtos.saldo_anterior, 
	operaciones_mvtos.saldo_actual FROM socios_general, captacion_cuentas, operaciones_mvtos, captacion_cuentastipos,  operaciones_tipos 
	WHERE captacion_cuentas.tipo_cuenta=20 and captacion_cuentas.saldo_cuenta>0 and captacion_cuentas.numero_socio=socios_general.codigo 
	AND operaciones_mvtos.docto_afectado=captacion_cuentas.numero_cuenta AND operaciones_mvtos.tipo_operacion=operaciones_tipos.idoperaciones_tipos
	AND captacion_cuentas.tipo_cuenta=captacion_cuentastipos.idcaptacion_cuentastipos
	AND operaciones_mvtos.fecha_afectacion>='$fecha_inicial' AND operaciones_mvtos.fecha_afectacion<='$fecha_final'
	ORDER BY socios_general.codigo,captacion_cuentas.numero_cuenta,  operaciones_mvtos.fecha_afectacion";
	//exit($sql_set);
if ($input!=OUT_EXCEL) {	
	
		$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($sql_set);
	$oRpt->setXML("../repository/report4.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();		//	*/
} else {
  $filename = "export_from_" . date("YmdHi") . "_to_uid-" .  $iduser . ".xls";
	header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=$filename");
	header("Pragma: no-cache");
	header("Expires: 0");
	sqltabla($sql_set, "", "fieldnames");
} 
?>