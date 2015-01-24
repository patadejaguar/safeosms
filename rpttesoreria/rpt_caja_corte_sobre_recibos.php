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
$xHP				= new cHPage("TR.Recibos de cobranza", HP_RPTXML );

$oficial = elusuario($iduser);
$xF				= new cFecha();
$xSQL				= new cSQLListas();
//=====================================================================================================
$fecha_inicial 		= (isset($_GET["on"])) ? $_GET["on"] : fechasys();
$fecha_final 		= (isset($_GET["off"])) ? $_GET["off"] : fechasys();
$cajero 			= (isset($_GET["f3"])) ? $_GET["f3"] : getUsuarioActual() ;						// Numero de Cajero
$output				= (isset($_GET["out"])) ? $_GET["out"] :OUT_DEFAULT;
$ByCajero			= "";
$ByAll				= "";
$senders			= getEmails($_REQUEST);
$empresa			= parametro("dependencia", SYS_TODAS, MQL_INT);
$empresa			= parametro("empresa", $empresa, MQL_INT);

if( isset($_REQUEST["fechaMX"]) ){
	$fecha_inicial		= $xF->getFechaISO($_REQUEST["fechaMX"]);
	$fecha_final		= $xF->getFechaISO($_REQUEST["fechaMX"]);
}
$ByDependencia			= ( $empresa == SYS_TODAS ) ? "" : " AND `socios`.`iddependencia`=" . $empresa ;

$ByUsuario			= ($cajero == SYS_TODAS) ? "" : " AND operaciones_recibos.idusuario=$cajero ";

$ByFecha			= " AND (operaciones_recibos.fecha_operacion>='$fecha_inicial' AND operaciones_recibos.fecha_operacion<='$fecha_final') ";
$nombre_empresa		= "";
if ( $empresa != SYS_TODAS ){
	$xEmp			= new cEmpresas($empresa); $xEmp->init();
	$nombre_empresa	= $xEmp->getNombreCorto();
}

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
$output		= ($output == SYS_DEFAULT) ? OUT_RXML : $output;		
$xRPT		= new cReportes($xHP->getTitle() . "-$nombre_empresa");
$xRPT->setSenders($senders);
$xRPT->setFile("report38");
$xRPT->setOut($output);
$xRPT->setSQL($setSql);
echo $xRPT->render(true);
//exit( $setSql);
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