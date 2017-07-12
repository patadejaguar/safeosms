<?php
/**
 * Reporte de
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
$xHP			= new cHPage("TR.Ingresos", HP_REPORT);
$xL				= new cSQLListas();

$periodo		= (isset($_GET["periodo"])) ? $_GET["periodo"]: SYS_TODAS;
//$estado		= (isset($_GET["estado"])) ? $_GET["estado"]: SYS_TODAS;
/**
 */
$xF				= new cFecha();
$estatus 		= (isset($_GET["estado"]) ) ? $_GET["estado"] : SYS_TODAS;
$frecuencia 	= (isset($_GET["periocidad"]) ) ? $_GET["periocidad"] : SYS_TODAS;
$convenio 		= (isset($_GET["convenio"]) ) ? $_GET["convenio"] : SYS_TODAS;
$convenio 		= (isset($_GET["producto"]) ) ? $_GET["producto"] : $convenio;

$empresa		= parametro("empresa", SYS_TODAS, MQL_INT);
$out 			= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;

$FechaInicial	= parametro("on", $xF->getFechaMinimaOperativa(), MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", $xF->getFechaMaximaOperativa(), MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);

$es_por_estatus 	= $xL->OFiltro()->CreditosPorEstado($estatus);
$es_por_frecuencia 	= $xL->OFiltro()->CreditosPorFrecuencia($frecuencia);
$es_por_convenio 	= $xL->OFiltro()->CreditosPorProducto($convenio);

$ByEmpresa			= $xL->OFiltro()->VSociosPorEmpresa($empresa);
$ByFecha			= $xL->OFiltro()->OperacionesPorFecha($FechaInicial, $FechaFinal);
/* ******************************************************************************/
$sql = "SELECT
	`socios`.`iddependencia`                         AS `empresa`,
	`socios`.`dependencia`                           AS `nombre`,
	`operaciones_mvtos`.`tipo_operacion`             AS `tipo`,
	`operaciones_tipos`.`descripcion_operacion`      AS `nombre`,
	COUNT(`operaciones_mvtos`.`idoperaciones_mvtos`) AS `operaciones`,
	SUM(`operaciones_mvtos`.`afectacion_real`)       AS `monto`
FROM
	`operaciones_mvtos` `operaciones_mvtos` 
		INNER JOIN `operaciones_tipos` `operaciones_tipos` 
		ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
		`idoperaciones_tipos` 
			INNER JOIN `socios` `socios` 
			ON `operaciones_mvtos`.`socio_afectado` = `socios`.`codigo`
WHERE
	(`socios`.`iddependencia` > 0) $ByEmpresa
	$ByFecha
GROUP BY
	`socios`.`iddependencia`,
	`operaciones_mvtos`.`tipo_operacion`
	";
$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
$xT		= new cTabla($sql, 0);
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
	
?>