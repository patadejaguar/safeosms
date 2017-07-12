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
$xHP		= new cHPage("TR.REPORTE DE EVOLUCION DE ESTATUS EN CREDITOS", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
$xFil		= new cSQLFiltros();

$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT); $credito = parametro("pb", $credito, MQL_INT);
$out 			= parametro("out", SYS_DEFAULT);

$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);


$sql			= "SELECT
	`operaciones_mvtos`.`recibo_afectado`       AS `recibo`,
	`operaciones_mvtos`.`fecha_afectacion`      AS `fecha`,
	`operaciones_mvtos`.`tipo_operacion`        AS `operacion`,
	`operaciones_tipos`.`descripcion_operacion` AS `descripcion`,
	`operaciones_mvtos`.`afectacion_real`       AS `monto`,
	`eacp_config_bases_de_integracion_miembros`.`afectacion`
FROM
	`operaciones_mvtos` `operaciones_mvtos`
		INNER JOIN `operaciones_tipos` `operaciones_tipos`
		ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
		`idoperaciones_tipos`
			INNER JOIN `eacp_config_bases_de_integracion_miembros`
			`eacp_config_bases_de_integracion_miembros`
			ON `eacp_config_bases_de_integracion_miembros`.`miembro` =
			`operaciones_mvtos`.`tipo_operacion`
WHERE
									(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =1111)
									AND
									(`operaciones_mvtos`.`docto_afectado` = $credito )
								GROUP BY
									`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
									`operaciones_mvtos`.`socio_afectado`,
									`operaciones_mvtos`.`docto_afectado`,
									`operaciones_mvtos`.`fecha_operacion`
								ORDER BY
									`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
									`operaciones_mvtos`.`socio_afectado`,";
$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
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
$xRPT->addContent( $xT->Show( $xHP->getTitle() ) );
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
//$xRPT->addContent( $xHP->end() );


$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);

?>