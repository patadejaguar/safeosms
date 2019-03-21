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
include_once "../core/core.config.inc.php";
include_once "../reports/PHPReportMaker.php";

$oficial = elusuario($iduser);
//=====================================================================================================
$si_est 			= $_GET["f70"];
$si_freq 			= $_GET["f71"];
$si_conv 			= $_GET["f72"];
$estatus 			= $_GET["f2"];
$frecuencia 		= $_GET["f1"];
$convenio 			= $_GET["f5"];

$on_f 				= $_GET["on"];
$off_f 				= $_GET["off"];
$f1 				= $_GET["f1"];
$input 				= $_GET["out"];
$fecha_inicial 		= $on_f;
$fecha_final 		= $off_f;

$es_por_estatus 	= "";
//$si_es_por_fecha
$es_por_frecuencia 	= "";
$es_por_convenio 	= "";
if($si_est==1){
	//$nest = eltipo("creditos_estatus", $estatus);
	$es_por_estatus = " AND creditos_solicitud.estatus_actual=$estatus ";
}
//
if($si_freq == 1){
	//$nfreq = eltipo("creditos_periocidadpagos", $frecuencia);
	$es_por_frecuencia = " AND creditos_solicitud.periocidad_de_pago=$frecuencia ";
	//AND creditos_solicitud.periocidad_de_pago=$f1 
}
//
if($si_conv == 1){
	//$nconv = eltipo("creditos_tipoconvenio", $convenio);
	$es_por_convenio = " AND creditos_solicitud.tipo_convenio = $convenio ";	
}
//AND operaciones_mvtos.fecha_afectacion>='$on_f' AND operaciones_mvtos.fecha_afectacion<='$off_f' 
if ($fecha_inicial && $fecha_final){
	$rango_de_fechas = " AND operaciones_mvtos.fecha_afectacion>='$on_f' AND operaciones_mvtos.fecha_afectacion<='$off_f' ";
	// AND captacion_cuentas.fecha_apertura>='$fecha_inicial' AND captacion_cuentas.fecha_apertura<='$fecha_final' ";
}
//=====================================================================================================


	if (!$input) {
		$input = "default";
	}



$setSql = "SELECT socios_general.codigo, 
	CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ', socios_general.nombrecompleto) AS 'nombre_completo', 
	creditos_solicitud.fecha_ministracion AS 'fecha_de_ministracion', operaciones_recibos.recibo_fiscal, 
			operaciones_mvtos.fecha_afectacion AS 'fecha_de_pago', operaciones_mvtos.docto_afectado AS 'documento', 
			operaciones_tipos.descripcion_operacion AS 'tipo_operacion',operaciones_mvtos.periodo_socio AS 'parcialidad', 
			operaciones_mvtosestatus.descripcion_mvtosestatus AS 'estatus',  
			operaciones_mvtos.afectacion_real AS 'monto', operaciones_mvtos.saldo_actual, operaciones_recibos.observacion_recibo,
			`creditos_tipoconvenio`.`descripcion_tipoconvenio` AS 'tipoconvenio'
			FROM
	`operaciones_tipos` `operaciones_tipos` 
		INNER JOIN `operaciones_mvtos` `operaciones_mvtos` 
		ON `operaciones_tipos`.`idoperaciones_tipos` = 
		`operaciones_mvtos`.`tipo_operacion` 
			INNER JOIN `operaciones_recibos` 
			`operaciones_recibos` 
			ON `operaciones_recibos`.`idoperaciones_recibos` 
			= `operaciones_mvtos`.`recibo_afectado` 
				INNER JOIN `creditos_solicitud` 
				`creditos_solicitud` 
				ON `operaciones_mvtos`.`docto_afectado` = 
				`creditos_solicitud`.`numero_solicitud` 
					INNER JOIN `socios_general` 
					`socios_general` 
					ON `creditos_solicitud`.`numero_socio` = 
					`socios_general`.`codigo` 
						INNER JOIN `creditos_tipoconvenio` 
						`creditos_tipoconvenio` 
						ON `creditos_tipoconvenio`.
						`idcreditos_tipoconvenio` = 
						`creditos_solicitud`.`tipo_convenio` 
							INNER JOIN 
							`operaciones_mvtosestatus` 
							`operaciones_mvtosestatus` 
							ON `operaciones_mvtosestatus`.
							`idoperaciones_mvtosestatus` = 
							`operaciones_mvtos`.
							`estatus_mvto`		
			WHERE  
			operaciones_mvtos.estatus_mvto!=40 
			/** AND creditos_solicitud.numero_socio=socios_general.codigo  */
			/** AND operaciones_mvtos.docto_afectado=creditos_solicitud.numero_solicitud   */
			/** AND operaciones_mvtos.socio_afectado=socios_general.codigo */
			/** AND operaciones_tipos.tipo_operacion=operaciones_mvtos.tipo_operacion */
			/** AND operaciones_mvtos.estatus_mvto=operaciones_mvtosestatus.idoperaciones_mvtosestatus */
			/** AND operaciones_mvtos.recibo_afectado=operaciones_recibos.idoperaciones_recibos */
			AND `operaciones_tipos`.`es_estadistico` = '0'
			$es_por_convenio
			$es_por_estatus
			$es_por_frecuencia
			$rango_de_fechas
			ORDER BY  operaciones_mvtos.fecha_afectacion, socios_general.codigo 
			/** LIMIT 0,10000 */";
//echo $setSql; exit;

if ($input!=OUT_EXCEL) {
		$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report15.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();
} else {
  $filename = "export_from_" . date("YmdHi") . "_to_uid-" .  $iduser . ".xls";
	header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=$filename");
	header("Pragma: no-cache");
	header("Expires: 0");
	sqltabla($setSql, "", "fieldnames");
}
?>