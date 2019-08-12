<?php
/**
 * Reporte de
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package seguimiento
 * @subpackage reports
 */
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	include_once("../core/core.db.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP			= new cHPage("REPORTE DE ", HP_RPTXML);
$periodo		= (isset($_GET["periodo"])) ? $_GET["periodo"]: "todas";
//$estado		= (isset($_GET["estado"])) ? $_GET["estado"]: "todas";
/**
 * Filtrar si Existe Caja Local
 */
$estatus 		= (isset($_GET["estado"]) ) ? $_GET["estado"] : "todas";
$frecuencia 		= (isset($_GET["periocidad"]) ) ? $_GET["periocidad"] : "todas";
$convenio 		= (isset($_GET["convenio"]) ) ? $_GET["convenio"] : "todas";
$empresa		= (isset($_GET["empresa"]) ) ? $_GET["empresa"] : "todas";
$input 			= (isset($_GET["out"])) ? $_GET["out"] : "default";

$es_por_estatus 	= "";
$es_por_frecuencia 	= "";
$es_por_convenio 	= "";

$ByEmpresa		= ( $empresa == "todas" ) ? "" : " AND socios.iddependencia = $empresa ";

if($estatus != "todas"){ $es_por_estatus = " AND creditos_solicitud.estatus_actual=$estatus "; }
//
if($frecuencia != "todas"){ $es_por_frecuencia 	= " AND creditos_solicitud.periocidad_de_pago =$frecuencia ";}
//
if($convenio != "todas"){ $es_por_convenio = " AND creditos_solicitud.tipo_convenio = $convenio "; }
/* ******************************************************************************/
$setSql = "SELECT
	`socios`.`dependencia`  AS 'empresa',
	socios.nombre,
	creditos_solicitud.numero_socio AS 'socio',
	creditos_solicitud.numero_solicitud AS 'solicitud', 
	creditos_tipoconvenio.descripcion_tipoconvenio AS 'modalidad',
	creditos_periocidadpagos.descripcion_periocidadpagos AS 'condiciones_de_pago', 
	getFechaMX(creditos_solicitud.fecha_ministracion) AS 'fecha_de_otorgamiento',
	creditos_solicitud.monto_autorizado AS 'monto_original', 
	getFechaMX(creditos_solicitud.fecha_vencimiento) AS 'fecha_de_vencimiento',
	(creditos_solicitud.tasa_interes *100) AS 'tasa_ordinaria_nominal_anual',
	CONCAT(creditos_solicitud.ultimo_periodo_afectado, '/', creditos_solicitud.pagos_autorizados) AS 'numero_de_pagos',
	creditos_solicitud.periocidad_de_pago AS 'frecuencia', 
	creditos_solicitud.saldo_actual AS 'saldo_insoluto',
	creditos_solicitud.fecha_ultimo_mvto, 
	creditos_estatus.descripcion_estatus AS 'estatus',
	socios.genero, socios.tipo_ingreso, creditos_solicitud.tipo_autorizacion AS 'modaut'
		FROM
	`creditos_solicitud` `creditos_solicitud`
		INNER JOIN `creditos_estatus` `creditos_estatus`
		ON `creditos_solicitud`.`estatus_actual` = `creditos_estatus`.
		`idcreditos_estatus`
			INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos`
			ON `creditos_solicitud`.`periocidad_de_pago` =
			`creditos_periocidadpagos`.`idcreditos_periocidadpagos`
				INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio`
				ON `creditos_solicitud`.`tipo_convenio` =
				`creditos_tipoconvenio`.`idcreditos_tipoconvenio`
					INNER JOIN `socios` `socios`
					ON `creditos_solicitud`.`numero_socio` = `socios`.`codigo`

	WHERE creditos_solicitud.saldo_actual >= 0.99
	$es_por_estatus
	$es_por_frecuencia
	$es_por_convenio
	$ByEmpresa
	ORDER BY `socios`.`iddependencia`,
	`creditos_solicitud`.`tipo_convenio`,
	creditos_solicitud.numero_socio";
if ($input!= OUT_EXCEL) {

	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report45c.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();
} else {
  $filename = "export_from_" . date("YmdHi") . "_to_uid-" .  $iduser . ".xls";
    $xO		= new cHExcel();
    $xO->convertTable($setSql, "");
}
?>