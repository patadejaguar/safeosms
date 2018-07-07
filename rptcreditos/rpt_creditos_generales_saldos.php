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
$xHP		= new cHPage("TR.REPORTE DE ", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$xQL		= new MQL();
$xFil		= new cSQLFiltros();
$xLi		= new cSQLListas();


$periocidad 			= parametro("f1", SYS_TODAS);
$periocidad 			= parametro("periocidad", $periocidad);
$periocidad 			= parametro("frecuencia", $periocidad);

$estado 				= parametro("estado", SYS_TODAS);
$estado 				= parametro("estatus", $estado);
$producto 				= parametro("convenio", SYS_TODAS);
$producto 				= parametro("producto", $producto);
$fechaInicial			= parametro("on", EACP_FECHA_DE_CONSTITUCION);
$fechaFinal				= parametro("off", fechasys());
$fechaInicial			= $xF->getFechaISO($fechaInicial);
$fechaFinal				= $xF->getFechaISO($fechaFinal);
$formato				= parametro("out", SYS_DEFAULT, MQL_RAW);
$sucursal				= parametro("sucursal", SYS_TODAS, MQL_RAW);
$xRPT					= new cReportes($xHP->getTitle());

$ByProducto				= $xLi->OFiltro()->CreditosPorProducto($producto);
$BySucursal				= $xLi->OFiltro()->CreditosPorSucursal($sucursal);

$idmunicipio			= parametro("municipioactivo", "");
$ByMunicipio			= $xLi->OFiltro()->CreditosPorMunicipioAct($idmunicipio);

$titulo					= $xHP->getTitle();

if($ByMunicipio !== ""){
	$xMun		= new cDomicilioMunicipio(); $xMun->initByIDUnico($idmunicipio);
	$municipio	= $xMun->getNombre();
	$entidadfed	= $xMun->getOEstado()->getNombre();
	$titulo		= $titulo . " / Municipio : $entidadfed - $municipio";
}
$ByFecha				= $xLi->OFiltro()->CreditosPorFechaDeMinistracion($fechaInicial, $fechaFinal);
$ByTipoAut				= $xLi->OFiltro()->CreditosPorAutorizacion($tipoautorizacion);
$ByDestino				= $xLi->OFiltro()->CreditosPorDestino($destino);
/*	$es_por_estatus
	$es_por_frecuencia
	$es_por_convenio
	$es_por_operacion*/



	/* ******************************************************************************/
  

$setSql = "SELECT socios.nombre, creditos_solicitud.numero_socio AS 'socio', 
	creditos_solicitud.numero_solicitud AS 'solicitud', 
	creditos_tipoconvenio.descripcion_tipoconvenio AS 'modalidad', 
	creditos_periocidadpagos.descripcion_periocidadpagos AS 'condiciones_de_pago', 
	creditos_solicitud.fecha_ministracion AS 'fecha_de_otorgamiento', 
	creditos_solicitud.monto_autorizado AS 'monto_original', 
	creditos_solicitud.fecha_vencimiento AS 'fecha_de_vencimiento', 
	creditos_solicitud.tasa_interes AS 'tasa_ordinaria_nominal_anual',
	 creditos_solicitud.pagos_autorizados AS 'numero_de_pagos', 
	creditos_solicitud.periocidad_de_pago AS 'frecuencia', 
	creditos_solicitud.saldo_actual AS 'saldo_insoluto', 
	creditos_solicitud.fecha_ultimo_mvto, 
	creditos_estatus.descripcion_estatus AS 'estatus', 
	socios.genero, socios.tipo_ingreso, 
	creditos_solicitud.tipo_autorizacion AS 'modaut',
	`operaciones_no_estadisticas`.* 
	FROM 
	`creditos_solicitud` `creditos_solicitud` 
		INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos` 
		ON `creditos_solicitud`.`periocidad_de_pago` = 
		`creditos_periocidadpagos`.`idcreditos_periocidadpagos` 
			INNER JOIN `socios` `socios` 
			ON `creditos_solicitud`.`numero_socio` = `socios`.`codigo` 
				INNER JOIN `operaciones_no_estadisticas` 
				`operaciones_no_estadisticas` 
				ON `creditos_solicitud`.`numero_solicitud` = 
				`operaciones_no_estadisticas`.`documento` 
					INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio` 
					ON `creditos_solicitud`.`tipo_convenio` = 
					`creditos_tipoconvenio`.`idcreditos_tipoconvenio` 
						INNER JOIN `creditos_estatus` `creditos_estatus` 
						ON `creditos_solicitud`.`estatus_actual` = 
						`creditos_estatus`.`idcreditos_estatus`
	
	WHERE creditos_solicitud.saldo_actual>=0.99
	AND
	(`creditos_solicitud`.`fecha_ultimo_mvto`>='$fechaInicial')
$ByTipoAut $ByProducto $BySucursal $ByMunicipio $ByFecha 

	
	AND creditos_solicitud.estatus_actual != " . CREDITO_ESTADO_CASTIGADO . "
	ORDER BY creditos_solicitud.tipo_autorizacion DESC,
				creditos_solicitud.fecha_ministracion, 
			creditos_estatus.orden_clasificacion ASC, 
			creditos_solicitud.numero_socio, creditos_solicitud.numero_solicitud,
			`operaciones_no_estadisticas`.fecha";
	//exit($setSql);
	
	
	
	$sql			= $setSql;
	$titulo			= $xHP->getTitle();
	$archivo		= $xHP->getTitle();
	
	
	
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
?>