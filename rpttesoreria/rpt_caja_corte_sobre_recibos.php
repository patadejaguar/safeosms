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
$xHP				= new cHPage("TR.Recibos de cobranza", HP_REPORT );


$xF					= new cFecha();
$xSQL				= new cSQLListas();
//=====================================================================================================
$cajero 			= parametro("f3", getUsuarioActual(), MQL_INT); $cajero = parametro("cajero", $cajero, MQL_INT); $cajero = parametro("usuarios", $cajero, MQL_INT);
$out				= parametro("out", OUT_HTML, MQL_RAW);
$mails				= getEmails($_REQUEST);
$empresa			= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$FechaInicial		= parametro("fechaMX", $xF->getFechaMinimaOperativa(), MQL_DATE);
$FechaInicial		= parametro("on", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal			= parametro("off", $xF->getFechaMaximaOperativa(), MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$TipoDePago			= parametro("tipodepago", SYS_TODAS, MQL_RAW); $TipoDePago	= parametro("formadepago", $TipoDePago, MQL_RAW);

$ByAll				= "";


$ByEmpresa			= $xSQL->OFiltro()->RecibosPorPersonaAsociada( $empresa);
$ByCajero			= $xSQL->OFiltro()->RecibosPorCajero($cajero);
//if(MODO_DEBUG == true){	$ByCajero		= ""; }
$ByFecha			= $xSQL->OFiltro()->RecibosPorFecha($FechaInicial, $FechaFinal);
$ByTipoDePago		= $xSQL->OFiltro()->RecibosPorTipoDePago($TipoDePago);

$titulo				= $xHP->getTitle();

if ( $ByEmpresa ){
	$xEmp			= new cEmpresas($empresa); $xEmp->init();
	$titulo			= $titulo . " / " . $xEmp->getNombreCorto();
}
if($ByCajero != ""){
	$xCaj			= new cSystemUser($cajero);
	if($xCaj->init() == true){
		$titulo			= $titulo . " / " . $xCaj->getNombreCompleto();
	}
}
if($ByTipoDePago != ""){
	$xTipoP			= new cTesoreriaTiposDePagoCobro($TipoDePago);
	if($xTipoP->init() == true){
		$titulo			= $titulo . " / " . $xTipoP->getNombre();
	}
}
$sql				= "SELECT
	`operaciones_recibos`.`idoperaciones_recibos`              AS `recibo`,
	`operaciones_recibos`.`fecha_operacion`                    AS `fecha`,
	`operaciones_recibos`.`numero_socio`                       AS `persona`,
	`personas`.`nombre`,
	`socios_aeconomica_dependencias`.`descripcion_dependencia` AS `empresa`,
	`operaciones_recibostipo`.`descripcion_recibostipo`        AS `tipo`,
	`operaciones_recibos`.`tipo_pago`                          AS `tipo_de_pago`
	,
	`operaciones_recibos`.`total_operacion`                    AS `monto`,
	`operaciones_recibos`.`observacion_recibo`                 AS `observaciones`,
	`usuarios`.`nombrecompleto` AS `cajero` 
FROM
	`operaciones_recibos` `operaciones_recibos` 
		INNER JOIN `personas` `personas` 
		ON `operaciones_recibos`.`numero_socio` = `personas`.`codigo` 
			INNER JOIN `operaciones_recibostipo` `operaciones_recibostipo` 
			ON `operaciones_recibos`.`tipo_docto` = `operaciones_recibostipo`.
			`idoperaciones_recibostipo` 
				INNER JOIN `socios_aeconomica_dependencias` 
				`socios_aeconomica_dependencias` 
				ON `operaciones_recibos`.`persona_asociada` = 
				`socios_aeconomica_dependencias`.
				`idsocios_aeconomica_dependencias` 
					INNER JOIN `usuarios` `usuarios` 
					ON `operaciones_recibos`.`idusuario` = `usuarios`.
					`idusuarios`

	WHERE operaciones_recibostipo.mostrar_en_corte!='0'
			$ByFecha
			$ByCajero
			$ByEmpresa
			$ByTipoDePago
			
		ORDER BY
			`operaciones_recibos`.`fecha_operacion`,
			`operaciones_recibos`.`tipo_pago`,
			`operaciones_recibos`.`tipo_docto`,
			`operaciones_recibos`.`idoperaciones_recibos`
		";
//exit($sql);
$xRPT		= new cReportes($titulo);
$xRPT->addContent($xRPT->getEncabezado($xRPT->getTitle(), $FechaInicial, $FechaFinal));
//$html)
$xRPT->setSenders($mails);
$xRPT->setOut($out);
$xRPT->setConfig("CORTE-RECIBOS");

$xRPT->setSQL($sql);
if($ByEmpresa != "" OR PERSONAS_CONTROLAR_POR_EMPRESA == false){ $xRPT->setOmitir("empresa"); }
if($ByCajero != ""){ $xRPT->setOmitir("cajero"); }
if($ByTipoDePago != ""){ $xRPT->setOmitir("tipo_de_pago"); }
$xRPT->addCampoSuma("monto");
$xRPT->setFormato("fecha", $xRPT->FMT_FECHA);
$xRPT->setToPrint();
$xRPT->setProcessSQL();

echo $xRPT->render(true);

exit;


//XXX: Hacer un UNION para captacion 
$setSql = "
SELECT
	`operaciones_recibos`.`idoperaciones_recibos`,
	`operaciones_recibos`.`fecha_operacion`,
	`operaciones_recibos`.`docto_afectado`,
	`operaciones_recibos`.`numero_socio`,
	`socios`.`nombre`,
	
	(CASE WHEN (`creditos_solicitud`.`persona_asociada` = " . DEFAULT_EMPRESA . ")
	THEN ''
	ELSE `socios`.`dependencia` END) AS 'dependencia',
	
	`operaciones_recibos`.`tipo_docto`,
	`operaciones_recibostipo`.`descripcion_recibostipo` AS `tipo_de_recibo`,
	`operaciones_recibos`.`tipo_pago`,
	`operaciones_recibos`.`recibo_fiscal`,	
	
	`operaciones_recibos`.`total_operacion`,
	`operaciones_recibos`.`observacion_recibo`,

	
	`operaciones_recibos`.`idusuario`
	
	
	FROM
	`operaciones_recibos` `operaciones_recibos` 
		LEFT OUTER JOIN `creditos_solicitud` `creditos_solicitud` 
		ON `operaciones_recibos`.`docto_afectado` = `creditos_solicitud`.
		`numero_solicitud` 
			INNER JOIN `operaciones_recibostipo` `operaciones_recibostipo` 
			ON `operaciones_recibos`.`tipo_docto` = `operaciones_recibostipo`.
			`idoperaciones_recibostipo` 
				INNER JOIN `socios` `socios` 
				ON `operaciones_recibos`.`numero_socio` = `socios`.`codigo`
				
	WHERE operaciones_recibostipo.mostrar_en_corte!='0'
			$ByFecha
			$ByUsuario
			$ByDependencia
		ORDER BY
			`operaciones_recibos`.`tipo_pago`,
			`operaciones_recibos`.`fecha_operacion`,
			`operaciones_recibos`.`tipo_docto`,
			`socios`.`dependencia`,
			`operaciones_recibos`.`idoperaciones_recibos`
	";
exit( $setSql);

$output		= ($output == SYS_DEFAULT) ? OUT_DEFAULT : $output;		
$xRPT		= new cReportes($xHP->getTitle() . "-$nombre_empresa");
$xRPT->setSenders($senders);
$xRPT->setFile("report38");
$xRPT->setOut($output);
$xRPT->setSQL($setSql);

//->setGrupo(idcampo, $formato);
//->setGrupo
//->setProcessSQL




echo $xRPT->render(true);

/*if ($input!=OUT_EXCEL) {
	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report38.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();
} else {
	$xHEx	= new cHExcel();
	$xHEx->convertTable($setSql);
}*/
?>