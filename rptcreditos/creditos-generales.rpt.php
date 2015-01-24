<?php
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

	$xHP		= new cHPage("Reporte de Creditos", HP_RPTXML );
	$xF			= new cFecha();
	$xLi		= new cSQLListas();
//=====================================================================================================
/**
 * Filtrar si Existe Caja Local
 */
$estatus 				= (isset($_GET["f2"]) ) ? $_GET["f2"] : SYS_TODAS;
$frecuencia 			= (isset($_GET["f1"]) ) ? $_GET["f1"] : SYS_TODAS;
$convenio 				= (isset($_GET["f3"]) ) ? $_GET["f3"] : SYS_TODAS;
$estatus 				= (isset($_GET["estado"])) ? $_GET["estado"] : $estatus;
$frecuencia 			= (isset($_GET["periocidad"])) ? $_GET["periocidad"] : $frecuencia; $frecuencia 			= (isset($_GET["frecuencia"])) ? $_GET["frecuencia"] : $frecuencia;
$convenio 				= (isset($_GET["convenio"])) ? $_GET["convenio"] : $convenio; $convenio 				= (isset($_GET["producto"])) ? $_GET["producto"] : $convenio;
$empresa				= (isset($_GET["empresa"]) ) ? $_GET["empresa"] : SYS_TODAS;
$out 					= parametro("out", SYS_DEFAULT, MQL_RAW);
$tipoautorizacion		= parametro("tipoautorizacion", SYS_TODAS, MQL_INT);

$ByOficial				= $xLi->OFiltro()->CreditosPorOficial(parametro("oficial", SYS_TODAS ,MQL_INT));
$BySucursal				= $xLi->OFiltro()->CreditosPorSucursal(parametro("sucursal", ""));
$es_por_estatus 		= $xLi->OFiltro()->CreditosPorEstado($estatus);
$BySaldo				= $xLi->OFiltro()->CreditosPorSaldos("0.99", ">=");
$ByEmpresa				= $xLi->OFiltro()->CreditosPorEmpresa($empresa);
$ByTipoAut				= $xLi->OFiltro()->CreditosPorAutorizacion($tipoautorizacion);


$fechaInicial			= $xF->getFechaISO( parametro("on", fechasys()) );
$fechaFinal				= $xF->getFechaISO( parametro("off", fechasys()) );

if($estatus == CREDITO_ESTADO_AUTORIZADO OR $estatus == CREDITO_ESTADO_SOLICITADO){ $BySaldo		= ""; }

$es_por_frecuencia 		= $xLi->OFiltro()->CreditosPorFrecuencia($frecuencia);
$es_por_convenio 		= $xLi->OFiltro()->CreditosPorProducto($convenio);

$ByFecha				= " AND (creditos_solicitud.fecha_ministracion >='$fechaInicial' AND creditos_solicitud.fecha_ministracion <= '$fechaFinal') ";
/* ******************************************************************************/
$OperadorFecha			= ($out == OUT_EXCEL) ?  " " : "getFechaMX";

$setSql = "SELECT socios.nombre,	socios.alias_dependencia AS 'empresa',
		creditos_solicitud.numero_socio AS 'socio',
	socios.genero, socios.tipo_ingreso AS 'tipo_de_ingreso',
	
	creditos_solicitud.numero_solicitud AS 'solicitud',
	`creditos_solicitud`.`tipo_convenio` AS 'producto',
	creditos_tipoconvenio.descripcion_tipoconvenio AS 'modalidad',
	creditos_periocidadpagos.descripcion_periocidadpagos AS 'condiciones_de_pago', 
	$OperadorFecha(creditos_solicitud.fecha_ministracion) AS 'fecha_de_otorgamiento',
	creditos_solicitud.monto_autorizado AS 'monto_original', 
	$OperadorFecha(creditos_solicitud.fecha_vencimiento) AS 'fecha_de_vencimiento',
	(creditos_solicitud.tasa_interes * 100) AS 'tasa_anual',
	CONCAT(creditos_solicitud.ultimo_periodo_afectado, '/', creditos_solicitud.pagos_autorizados) AS 'numero_de_pagos',
	creditos_solicitud.periocidad_de_pago AS 'frecuencia', 
	creditos_solicitud.saldo_actual AS 'saldo_insoluto',
	creditos_solicitud.fecha_ultimo_mvto AS 'fecha_de_ultimo_pago', 
	creditos_estatus.descripcion_estatus AS 'estatus',
	creditos_solicitud.tipo_autorizacion AS 'modalidad_de_autorizacion',
	`creditos_tipo_de_pago`.`descripcion` AS `tipo_de_pago`

FROM
	`creditos_solicitud` `creditos_solicitud` 
		INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio` 
		ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
		`idcreditos_tipoconvenio` 
			INNER JOIN `creditos_estatus` `creditos_estatus` 
			ON `creditos_solicitud`.`estatus_actual` = `creditos_estatus`.
			`idcreditos_estatus` 
				INNER JOIN `creditos_tipo_de_pago` `creditos_tipo_de_pago` 
				ON `creditos_tipo_de_pago`.`idcreditos_tipo_de_pago` = 
				`creditos_solicitud`.`tipo_de_pago` 
					INNER JOIN `socios` `socios` 
					ON `creditos_solicitud`.`numero_socio` = `socios`.`codigo` 
						INNER JOIN `creditos_periocidadpagos` 
						`creditos_periocidadpagos` 
						ON `creditos_solicitud`.`periocidad_de_pago` = 
						`creditos_periocidadpagos`.`idcreditos_periocidadpagos`

	WHERE (creditos_solicitud.numero_solicitud != 0)
	$BySaldo
	$es_por_estatus
	$es_por_frecuencia
	$es_por_convenio
	$ByEmpresa
	$ByFecha
	$ByTipoAut
	$BySucursal
	$ByOficial
	ORDER BY `creditos_solicitud`.`tipo_convenio`, socios.nombre ";

	//echo $setSql; exit();
if ($out!= OUT_EXCEL) {

	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	//$oRpt->setConnection( cnnGeneral() );
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report45d.xml");
	$oOut = $oRpt->createOutputPlugin($out);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();
} else {
	$xHP	= new cHExcel();
	$xHP->convertTable($setSql);
}
?>