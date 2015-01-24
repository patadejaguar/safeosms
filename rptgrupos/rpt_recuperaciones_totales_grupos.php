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
include_once "../core/core.config.inc.php";
include_once "../reports/PHPReportMaker.php";

$oficial = elusuario($iduser);
//=====================================================================================================
$fecha_inicial = $_GET["on"];
$fecha_final = $_GET["off"];
$f3 = $_GET["f3"];
$input = $_GET["out"];
$grupo = $_GET["id"];
$si_grupo = $_GET["f74"];
	if (!$input) {
		$input = "default";
	}
$estatus_actual = $_GET["f2"];
$rango_de_fechas = "";
if ($fecha_inicial && $fecha_final){
	//$si_es_por_fecha = " AND creditos_solicitud.fecha_ministracion>='$fecha_inicial' AND creditos_solicitud.fecha_ministracion<='$fecha_final' ";
	$rango_de_fechas = " AND `recuperaciones_netas`.`fecha`>='$fecha_inicial'
	AND `recuperaciones_netas`.`fecha`<='$fecha_final' ";
}

//=====================================================================================================
$idgrupo = $_GET["id"];
$si_est = $_GET["f70"];
$si_freq = $_GET["f71"];
$si_conv = $_GET["f72"];
$estatus = $_GET["f2"];
$frecuencia = $_GET["f1"];
$convenio = $_GET["f5"];

$es_por_estatus = "";
$es_por_frecuencia = "";
$es_por_convenio = "";
$es_por_grupo = "";
if($si_est==1){

	$es_por_estatus = " AND (creditos_solicitud.estatus_actual=$estatus) ";
}
//
if($si_freq == 1){

	$es_por_frecuencia = " AND (creditos_solicitud.periocidad_de_pago=$frecuencia) ";
}
//
if($si_conv == 1){
	$es_por_convenio = " AND (creditos_solicitud.tipo_convenio = $convenio) ";
}
if($si_grupo ==1){
	$es_por_grupo =  " AND (`socios_grupossolidarios`.`idsocios_grupossolidarios` = $grupo) ";
}


  /******************************************************************************
	*																										*
	*	Use this file to see a sample of PHPReports.											*
	*	Please check the PDF manual for see how to use it.									*
	*	It need to be placed on a directory reached by the web server.					*
	*																										*
	******************************************************************************/


	$setSql = "SELECT
	`socios_grupossolidarios`.`idsocios_grupossolidarios`
	AS `grupo`,
	`socios_grupossolidarios`.`nombre_gruposolidario`
	AS `nombre`,
	`socios_grupossolidarios`.`representante_nombrecompleto`
	AS `representante`,
	`creditos_solicitud`.`numero_solicitud`
	AS `solicitud`,
	`creditos_solicitud`.`numero_socio`
	AS `socio`,
	`creditos_solicitud`.`fecha_ministracion`
	AS `ministracion`,
	`creditos_solicitud`.`fecha_vencimiento`
	AS `vencimiento`,
	`creditos_tipoconvenio`.`descripcion_tipoconvenio`
	AS `convenio`,
	`creditos_solicitud`.`monto_autorizado`
	AS `autorizado`,
	`recuperaciones_netas`.`recuperado`,
	`recuperaciones_netas`.`fecha`,
	`creditos_solicitud`.`saldo_actual`
	AS `saldo`,
	`recuperaciones_netas`.`mvtos`
	AS `pagos`,
	(`creditos_solicitud`.`monto_autorizado` - `recuperaciones_netas`.`recuperado`) AS 'saldo_segun_mvtos'
FROM
	`socios_grupossolidarios` `socios_grupossolidarios`
		INNER JOIN `creditos_solicitud` `creditos_solicitud`
		ON `socios_grupossolidarios`.
		`idsocios_grupossolidarios` = `creditos_solicitud`.
		`grupo_asociado`
			INNER JOIN `recuperaciones_netas`
			`recuperaciones_netas`
			ON `recuperaciones_netas`.`solicitud` =
			`creditos_solicitud`.`numero_solicitud`
				INNER JOIN `creditos_tipoconvenio`
				`creditos_tipoconvenio`
				ON `creditos_tipoconvenio`.
				`idcreditos_tipoconvenio` =
				`creditos_solicitud`.`tipo_convenio`
WHERE
	/** (`socios_grupossolidarios`.`idsocios_grupossolidarios`!=99)*/
	(`creditos_tipoconvenio`.`tipo_en_sistema` = " . CREDITO_PRODUCTO_GRUPOS . " )
	$rango_de_fechas
	$es_por_frecuencia
	$es_por_estatus
	HAVING saldo_segun_mvtos <= 0.99
	ORDER BY `recuperaciones_netas`.`fecha`";			//			*/

if ($input!=OUT_EXCEL) {

		$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report50.xml");
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