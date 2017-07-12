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
$destino				= parametro("destino", SYS_TODAS ,MQL_INT);
$otros					= parametro("otrosdatos", false, MQL_BOOL);
$compacto				= parametro("compacto", false, MQL_BOOL);
$conseguimiento			= parametro("conseguimiento", false, MQL_BOOL);

$ByOficial				= $xLi->OFiltro()->CreditosPorOficial(parametro("oficial", SYS_TODAS ,MQL_INT));
$BySucursal				= $xLi->OFiltro()->CreditosPorSucursal(parametro("sucursal", ""));
$es_por_estatus 		= $xLi->OFiltro()->CreditosPorEstado($estatus);
$BySaldo				= $xLi->OFiltro()->CreditosPorSaldos(TOLERANCIA_SALDOS, ">=");
$ByEmpresa				= $xLi->OFiltro()->CreditosPorEmpresa($empresa);
$ByTipoAut				= $xLi->OFiltro()->CreditosPorAutorizacion($tipoautorizacion);
$ByDestino				= $xLi->OFiltro()->CreditosPorDestino($destino);

$FechaInicial			= $xF->getFechaISO( parametro("on", fechasys()) );
$FechaFinal				= $xF->getFechaISO( parametro("off", fechasys()) );
$senders				= getEmails($_REQUEST);

if($estatus == CREDITO_ESTADO_AUTORIZADO OR $estatus == CREDITO_ESTADO_SOLICITADO){ $BySaldo		= ""; }

$es_por_frecuencia 		= $xLi->OFiltro()->CreditosPorFrecuencia($frecuencia);
$es_por_convenio 		= $xLi->OFiltro()->CreditosPorProducto($convenio);
$FProducto				= " `creditos_tipoconvenio`.`descripcion_tipoconvenio` AS `producto`, ";
if($es_por_convenio != ""){
	$xCon	= new cProductoDeCredito($convenio);
	$xCon->init();
	$FProducto			= "";
	$xHP->setTitle($xHP->getTitle() . " - " . $xCon->getNombre() );
}
$TxtOtros				= "";
if($otros == true){
	$TxtOtros			= "`personas`.`correo_electronico`,`personas`.`telefono`, `personas`.`figura_juridica` ,getViviendaPorPersona(`personas`.`codigo`) AS `vivienda`,";
}
$BySeguimiento			= "";
$TxtSeguimiento			= "";
if($conseguimiento == true){
	$BySeguimiento		= $xLi->OFiltro()->CreditosProductosPorSeguimiento(0);
	$TxtSeguimiento		= "getDiasDeMora(`creditos_solicitud`.`numero_solicitud`,`creditos_solicitud`.`periocidad_de_pago`)                                	AS `dias_de_mora`,";
}


$ByFecha				= " AND (creditos_solicitud.fecha_ministracion >='$FechaInicial' AND creditos_solicitud.fecha_ministracion <= '$FechaFinal') ";
/* ******************************************************************************/
$OperadorFecha			= ($out == OUT_EXCEL) ?  " " : "getFechaMX";
/* ********************* CAMPOS EXTRAS *******************************************/
$FEmpresa				= ($ByEmpresa == "") ? " `personas`.`dependencia` AS	`empresa`, " : "";
if(PERSONAS_CONTROLAR_POR_EMPRESA == false){ $FEmpresa = ""; }
$saldo_migrado			= (MODO_MIGRACION == true) ? " `saldo_conciliado` AS `migrado`, " : "";
$setSql	= "SELECT
	`creditos_solicitud`.`sucursal`,
	`personas`.`codigo`,
	`personas`.`nombre`,
	$TxtOtros
	$FEmpresa
	`creditos_solicitud`.`numero_solicitud`                            AS `credito`,
	$FProducto
	`creditos_periocidadpagos`.`descripcion_periocidadpagos`           AS `frecuencia`,
	`creditos_estatus`.`descripcion_estatus`                           AS `estado_actual`,
	`creditos_tipo_de_autorizacion`.`descripcion_tipo_de_autorizacion` AS `autorizacion`,
	$OperadorFecha(`creditos_solicitud`.`fecha_ministracion`)         	AS `fecha_de_desembolso`,
	`creditos_tipo_de_pago`.`descripcion`                              AS `forma_de_pagos`,
	ROUND((`creditos_solicitud`.`tasa_interes`*100),2)					AS `tasa_interes`,
	ROUND((`creditos_solicitud`.`tasa_moratorio`*100),2)				AS `tasa_moratorio`,
	`creditos_solicitud`.`pagos_autorizados`							AS `total_pagos`,
	creditos_solicitud.ultimo_periodo_afectado							AS `pago`,
	$OperadorFecha(`creditos_solicitud`.`fecha_ultimo_mvto`)         	AS `ultimo_pago`,
	
	
	/*$OperadorFecha(`creditos_solicitud`.`fecha_vencimiento_dinamico`)  	AS `fecha_de_vencimiento_calculado`,*/
	$TxtSeguimiento
	`creditos_solicitud`.`monto_autorizado`                            	AS `monto_original`,
	`creditos_solicitud`.`saldo_actual`                                	AS `saldo_capital`,
	$saldo_migrado
	`oficiales`.`nombre_corto` AS	`oficial`
	
FROM
	`creditos_solicitud` `creditos_solicitud` 
		INNER JOIN `oficiales` `oficiales` 
		ON `creditos_solicitud`.`oficial_seguimiento` = `oficiales`.`id` 
			INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio` 
			ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
			`idcreditos_tipoconvenio` 
				INNER JOIN `creditos_tipo_de_autorizacion` 
				`creditos_tipo_de_autorizacion` 
				ON `creditos_solicitud`.`tipo_autorizacion` = 
				`creditos_tipo_de_autorizacion`.
				`idcreditos_tipo_de_autorizacion` 
					INNER JOIN `personas` `personas` 
					ON `creditos_solicitud`.`numero_socio` = `personas`.`codigo` 
						INNER JOIN `creditos_estatus` `creditos_estatus` 
						ON `creditos_solicitud`.`estatus_actual` = 
						`creditos_estatus`.`idcreditos_estatus` 
							INNER JOIN `creditos_tipo_de_pago` 
							`creditos_tipo_de_pago` 
							ON `creditos_solicitud`.`tipo_de_pago` = 
							`creditos_tipo_de_pago`.`idcreditos_tipo_de_pago` 
								INNER JOIN `creditos_periocidadpagos` 
								`creditos_periocidadpagos` 
								ON `creditos_solicitud`.`periocidad_de_pago` = 
								`creditos_periocidadpagos`.
								`idcreditos_periocidadpagos`
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
	
	$ByDestino
	$BySeguimiento
	ORDER BY `creditos_solicitud`.`tipo_convenio`, `personas`.`nombre` ";

/*$setSql = "SELECT socios.nombre,	socios.alias_dependencia AS 'empresa',
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
	ORDER BY `creditos_solicitud`.`tipo_convenio`, socios.nombre ";*/
$titulo			= $xHP->getTitle();
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($setSql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte

$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);
$xRPT->addContent($body);
if($ByOficial != ""){
	$xRPT->setGrupo("oficial");
}
if($es_por_convenio !== "" AND $FProducto !== ""){
	//$xRPT->setGrupo("producto");
}
$xRPT->addCampoSuma("monto_original");
$xRPT->addCampoSuma("saldo_capital");
if(MODO_MIGRACION == true){
	$xRPT->addCampoSuma("migrado");
}
$xRPT->addCampoContar("credito");
if($otros == false){
	$xRPT->setOmitir("sucursal");
	$xRPT->setOmitir("fecha_de_vencimiento_calculado");
	$xRPT->setOmitir("tasa_moratorio");
	$xRPT->setOmitir("autorizacion");
	//$xRPT->setOmitir("codigo");
}
//if($es_por_convenio != ""){ $xRPT->setGrupo(""); }
$xRPT->setProcessSQL();
//============ Agregar HTML

$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);

?>