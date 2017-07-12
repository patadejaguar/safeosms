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

	$xHP		= new cHPage("TR.Creditos Pagados", HP_REPORT );
	$xF			= new cFecha();
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
$out 					= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;

$es_por_estatus 		= "";
$BySaldo				= "";// " AND (creditos_solicitud.saldo_actual>=0.99) ";
$ByEmpresa				= ( $empresa == SYS_TODAS ) ? "" : " AND socios.iddependencia = $empresa ";

/*if($estatus != SYS_TODAS){ 
	$es_por_estatus = " AND creditos_solicitud.estatus_actual=$estatus ";
	if($estatus == CREDITO_ESTADO_AUTORIZADO OR $estatus == CREDITO_ESTADO_SOLICITADO){ $BySaldo		= ""; }
 }*/

$BySaldo				= " AND creditos_solicitud.estatus_actual != " . CREDITO_ESTADO_AUTORIZADO . " ";
$BySaldo				.= " AND creditos_solicitud.estatus_actual != " . CREDITO_ESTADO_SOLICITADO . " ";
$BySaldo				.= " AND creditos_solicitud.estatus_actual != " . CREDITO_ESTADO_CASTIGADO . " ";

$BySaldo				.= " AND creditos_solicitud.monto_autorizado > 0 ";
$BySaldo				.= " AND creditos_solicitud.tipo_autorizacion != "  . CREDITO_AUTORIZACION_REESTRUCTURADO . " ";

$es_por_frecuencia 		= ($frecuencia != SYS_TODAS) ? " AND creditos_solicitud.periocidad_de_pago =$frecuencia " : "";
$es_por_convenio 		= ($convenio != SYS_TODAS) ? " AND creditos_solicitud.tipo_convenio = $convenio " : "";

$mx 					= (isset($_GET["mx"])) ? true : false;
if($mx == true){
	$FechaInicial		= (isset($_GET["on"])) ? $xF->getFechaISO( $_GET["on"]) : FECHA_INICIO_OPERACIONES_SISTEMA;
	$FechaFinal			= (isset($_GET["off"])) ? $xF->getFechaISO( $_GET["off"]) : fechasys();
} else {
	$FechaInicial		= (isset($_GET["on"])) ? $_GET["on"] : FECHA_INICIO_OPERACIONES_SISTEMA;
	$FechaFinal			= (isset($_GET["off"])) ? $_GET["off"] : fechasys();
}
$ByFecha				= " AND (`creditos_solicitud`.`fecha_ultimo_capital` >='$FechaInicial' AND `creditos_solicitud`.`fecha_ultimo_capital` <='$FechaFinal') ";
/* ******************************************************************************/
$OperadorFecha			= ($out == OUT_EXCEL) ?  " " : "getFechaMX";
$senders		= getEmails($_REQUEST);
$FEmp					= (PERSONAS_CONTROLAR_POR_EMPRESA == true) ? "socios.alias_dependencia AS 'empresa'," : "";
$sql = "SELECT 

	creditos_solicitud.numero_socio AS 'socio',
	socios.nombre,	
	$FEmp
	
	
	creditos_solicitud.numero_solicitud AS 'solicitud',
	
	creditos_tipoconvenio.descripcion_tipoconvenio 				AS `producto`,
	
	creditos_periocidadpagos.descripcion_periocidadpagos 		AS `periocidad_de_pago`, 
	$OperadorFecha(creditos_solicitud.fecha_ultimo_capital) 	AS `fecha_de_pago`,

	(`creditos_solicitud`.`tasa_interes` * 100)					AS `tasa_anual`,
	`creditos_solicitud`.`pagos_autorizados` 						AS `numero_de_pagos`,
	`creditos_solicitud`.`ultimo_periodo_afectado`				AS `ultima_parcialidad`,
	
	`creditos_solicitud`.`monto_autorizado` 					AS `monto_original`,
	`creditos_solicitud`.`recibo_ultimo_capital`				AS `recibo`,

	
	$OperadorFecha(`operaciones_recibos`.`fecha_de_registro`) 	AS `fecha_de_captura`,
	`operaciones_recibos`.`tipo_pago`							AS `tipo_de_pago` 
	
FROM
	`creditos_solicitud` `creditos_solicitud` 
		INNER JOIN `socios` `socios` 
		ON `creditos_solicitud`.`numero_socio` = `socios`.`codigo` 
			INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio` 
			ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
			`idcreditos_tipoconvenio` 
				INNER JOIN `operaciones_recibos` `operaciones_recibos` 
				ON `creditos_solicitud`.`recibo_ultimo_capital` = 
				`operaciones_recibos`.`idoperaciones_recibos` 
					INNER JOIN `creditos_periocidadpagos` 
					`creditos_periocidadpagos` 
					ON `creditos_solicitud`.`periocidad_de_pago` = 
					`creditos_periocidadpagos`.`idcreditos_periocidadpagos` 
						INNER JOIN `creditos_estatus` `creditos_estatus` 
						ON `creditos_solicitud`.`estatus_actual` = 
						`creditos_estatus`.`idcreditos_estatus`

	WHERE 
	(`creditos_solicitud`.`saldo_actual` <= " . TOLERANCIA_SALDOS . ")
	AND `creditos_solicitud`.`estatus_actual` != " . CREDITO_ESTADO_AUTORIZADO . " 
	AND `creditos_solicitud`.`estatus_actual` != " . CREDITO_ESTADO_AUTORIZADO . " 
	AND `creditos_solicitud`.`estatus_actual` != " . CREDITO_ESTADO_CASTIGADO ." 
	$BySaldo
	$es_por_estatus
	$es_por_frecuencia
	$es_por_convenio
	$ByEmpresa
	$ByFecha
	ORDER BY creditos_solicitud.fecha_ultimo_mvto, socios.nombre ";

//setLog($sql);

$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($xHP->getTitle());
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
$xT		= new cTabla($sql, 2);
$xT->setTipoSalida($out);


$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);

$xRPT->addContent($body);

//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");
$xRPT->addContent( $xT->Show(  ) );
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
//$xRPT->addContent( $xHP->end() );


$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);

	//echo $setSql; exit();
/*if ($out!= OUT_EXCEL) {

	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	//$oRpt->setConnection( cnnGeneral() );
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report45e.xml");
	$oOut = $oRpt->createOutputPlugin($out);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();
} else {
	$xHP	= new cHExcel();
	$xHP->convertTable($setSql);
}*/
?>