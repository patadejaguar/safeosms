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

	$xHP		= new cHPage("Reporte de Creditos", HP_RPTXML );
	
	;
$oficial = elusuario($iduser);
//=====================================================================================================
/**
 * Filtrar si Existe Caja Local
 */
$estatus 		= (isset($_GET["f2"]) ) ? $_GET["f2"] : SYS_TODAS;
$frecuencia 	= (isset($_GET["f1"]) ) ? $_GET["f1"] : SYS_TODAS;
$convenio 		= (isset($_GET["f3"]) ) ? $_GET["f3"] : SYS_TODAS;
$empresa		= (isset($_GET["empresa"]) ) ? $_GET["empresa"] : SYS_TODAS;
$input 			= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;

$es_por_estatus 	= "";
$es_por_frecuencia 	= "";
$es_por_convenio 	= "";
$BySaldo			= "";

$ByEmpresa		= ( $empresa == SYS_TODAS ) ? "" : " AND socios.iddependencia = $empresa ";

if($estatus != SYS_TODAS){ $es_por_estatus = " AND creditos_solicitud.estatus_actual=$estatus "; }
//
if($frecuencia != SYS_TODAS){ $es_por_frecuencia 	= " AND creditos_solicitud.periocidad_de_pago =$frecuencia ";}
//
if($convenio != SYS_TODAS){ 
	$es_por_convenio = " AND creditos_solicitud.tipo_convenio = $convenio ";
	if( $estatus == CREDITO_ESTADO_SOLICITADO OR $estatus == CREDITO_ESTADO_AUTORIZADO ){
	$BySaldo			= " AND (creditos_solicitud.saldo_actual>=0.99) ";
	}
}


/* ******************************************************************************/


$setSql = "SELECT socios.nombre, creditos_solicitud.numero_socio AS 'socio',
	creditos_solicitud.numero_solicitud AS 'solicitud', 
	creditos_tipoconvenio.descripcion_tipoconvenio AS 'modalidad',
	creditos_periocidadpagos.descripcion_periocidadpagos AS 'condiciones_de_pago', 
	getFechaMX(creditos_solicitud.fecha_ministracion) AS 'fecha_de_otorgamiento',
	creditos_solicitud.monto_autorizado AS 'monto_original', 
	getFechaMX(creditos_solicitud.fecha_vencimiento) AS 'fecha_de_vencimiento',
	(creditos_solicitud.tasa_interes *100) AS 'tasa_anual',
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

	WHERE 
	creditos_solicitud.numero_solicitud != 0
	$BySaldo
	$es_por_estatus
	$es_por_frecuencia
	$es_por_convenio
	$ByEmpresa
	ORDER BY `creditos_solicitud`.`estatus_actual`, 
	`creditos_solicitud`.`tipo_convenio`,
	creditos_solicitud.numero_socio";

	//echo $setSql; exit();
if ($input!= OUT_EXCEL) {

	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report45b.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();
} else {
	$xHP	= new cHExcel();
	$xHP->convertTable($setSql);
}
?>