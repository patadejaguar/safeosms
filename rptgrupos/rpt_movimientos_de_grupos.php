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
//=====================================================================================================
$xHP			= new cHPage("TR.TR.Estado_de_cuenta de Grupo ", HP_REPORT);
$xL				= new cSQLListas();
$xF				= new cFecha();
$xQL			= new MQL();
$xFil			= new cSQLFiltros();

$estatus 		= parametro("estado", SYS_TODAS);
$frecuencia 	= parametro("periocidad", SYS_TODAS);
$producto 		= parametro("convenio", SYS_TODAS);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS);
$grupo			= parametro("grupo", SYS_TODAS, MQL_INT);
$grupo			= parametro("id", $grupo, MQL_INT);
$sucursal		= parametro("sucursal", SYS_TODAS, MQL_RAW);
$out 			= parametro("out", SYS_DEFAULT);

$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);

$ByFecha		= $xFil->CreditosPorFechaDeSolicitud($FechaInicial, $FechaFinal);
$ByProducto		= $xFil->CreditosPorProducto($producto);
$ByEstado		= $xFil->CreditosPorEstado($estatus);
$ByFrecuencia	= $xFil->CreditosPorFrecuencia($frecuencia);
$ByGrupo		= $xFil->CreditosPorGrupo($grupo);

$sql			= "SELECT
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
	`creditos_solicitud`.`saldo_actual`
	AS `saldo`,
	`operaciones_detalle_ne`.`operacion`,
	`operaciones_detalle_ne`.`recibo`,
	`operaciones_detalle_ne`.`fecha`,
	`operaciones_detalle_ne`.`tipo_de_operacion`,
	`operaciones_detalle_ne`.`monto`,
	`operaciones_detalle_ne`.`detalles`
FROM
	`creditos_tipoconvenio` `creditos_tipoconvenio`
		INNER JOIN `creditos_solicitud` `creditos_solicitud`
		ON `creditos_tipoconvenio`.`idcreditos_tipoconvenio`
		= `creditos_solicitud`.`tipo_convenio`
			INNER JOIN `socios_grupossolidarios`
			`socios_grupossolidarios`
			ON `creditos_solicitud`.`grupo_asociado` =
			`socios_grupossolidarios`.
			`idsocios_grupossolidarios`
				INNER JOIN `operaciones_detalle_ne` `operaciones_detalle_ne`
				ON `operaciones_detalle_ne`.`documento` =
				`creditos_solicitud`.`numero_solicitud`
WHERE
	(`creditos_tipoconvenio`.`tipo_en_sistema` = " . CREDITO_PRODUCTO_GRUPOS . ")
	/** AND (`socios_grupossolidarios`.`idsocios_grupossolidarios`!=99) */
	$ByFecha
	$ByProducto
	$ByEstado
	$ByFrecuencia
	$ByGrupo
	GROUP BY
		`socios_grupossolidarios`.`idsocios_grupossolidarios`,
		`creditos_solicitud`.`numero_solicitud`,
		`operaciones_detalle_ne`.`operacion`
	/* ORDER BY fecha */
	/* LIMIT 0,100 */ ";

$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte

$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);

$xRPT->addContent($body);
$xRPT->setProcessSQL();

$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);


exit;
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
$fecha_inicial 		= $_GET["on"];
$fecha_final 		= $_GET["off"];
$f3 			= $_GET["f3"];
$input 			= $_GET["out"];
$grupo 			= $_GET["id"];
$si_grupo		= $_GET["f74"];
	if (!$input) {
		$input 	= "default";
	}
$estatus_actual 	= $_GET["f2"];
$rango_de_fechas	= "";
if ($fecha_inicial && $fecha_final){
	//$si_es_por_fecha = " AND creditos_solicitud.fecha_ministracion>='$fecha_inicial' AND creditos_solicitud.fecha_ministracion<='$fecha_final' ";
	$rango_de_fechas = " AND `operaciones_detalle_ne`.`fecha`>='$fecha_inicial'
	AND `operaciones_detalle_ne`.`fecha`<='$fecha_final' ";
}

//=====================================================================================================
$idgrupo 		= $_GET["id"];
$si_est 		= $_GET["f70"];
$si_freq 		= $_GET["f71"];
$si_conv 		= $_GET["f72"];
$estatus 		= $_GET["f2"];
$frecuencia 		= $_GET["f1"];
$convenio 		= $_GET["f5"];

$es_por_estatus 	= "";
$es_por_frecuencia 	= "";
$es_por_convenio 	= "";
$es_por_grupo 		= "";
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
	`creditos_solicitud`.`saldo_actual`
	AS `saldo`,
	`operaciones_detalle_ne`.`operacion`,
	`operaciones_detalle_ne`.`recibo`,
	`operaciones_detalle_ne`.`fecha`,
	`operaciones_detalle_ne`.`tipo_de_operacion`,
	`operaciones_detalle_ne`.`monto`,
	`operaciones_detalle_ne`.`detalles`
FROM
	`creditos_tipoconvenio` `creditos_tipoconvenio`
		INNER JOIN `creditos_solicitud` `creditos_solicitud`
		ON `creditos_tipoconvenio`.`idcreditos_tipoconvenio`
		= `creditos_solicitud`.`tipo_convenio`
			INNER JOIN `socios_grupossolidarios`
			`socios_grupossolidarios`
			ON `creditos_solicitud`.`grupo_asociado` =
			`socios_grupossolidarios`.
			`idsocios_grupossolidarios`
				INNER JOIN `operaciones_detalle_ne` `operaciones_detalle_ne`
				ON `operaciones_detalle_ne`.`documento` =
				`creditos_solicitud`.`numero_solicitud`
WHERE
	(`creditos_tipoconvenio`.`tipo_en_sistema` = " . CREDITO_PRODUCTO_GRUPOS . ")
	/** AND (`socios_grupossolidarios`.`idsocios_grupossolidarios`!=99) */
	$rango_de_fechas
	$es_por_convenio
	$es_por_estatus
	$es_por_frecuencia
	$es_por_grupo
	GROUP BY
		`socios_grupossolidarios`.`idsocios_grupossolidarios`,
		`creditos_solicitud`.`numero_solicitud`,
		`operaciones_detalle_ne`.`operacion`
	/* ORDER BY fecha */
	/* LIMIT 0,100 */
	";			//
echo $setSql; exit;
if ($input!=OUT_EXCEL) {
//echo $setSql;
		$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report48.xml");
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