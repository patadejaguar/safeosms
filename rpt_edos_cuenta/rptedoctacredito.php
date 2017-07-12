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
$xHP		= new cHPage("TR.REPORTE DE OPERACIONES DE CREDITO", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
$xFil		= new cSQLFiltros();

$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT); $credito = parametro("pb", $credito, MQL_INT);
$operacion		= parametro("operacion", SYS_TODAS, MQL_INT);
$estatus 		= parametro("estado", SYS_TODAS);
$frecuencia 	= parametro("periocidad", SYS_TODAS);
$producto 		= parametro("convenio", SYS_TODAS);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS);
$grupo			= parametro("grupo", SYS_TODAS, MQL_INT);
$sucursal		= parametro("sucursal", SYS_TODAS, MQL_RAW);
$out 			= parametro("out", SYS_DEFAULT);

$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);

$ByTipo			= $xFil->OperacionesPorTipo($operacion);
$ByFecha		= $xFil->OperacionesPorFecha($FechaInicial, $FechaFinal);

$sql			= "SELECT
	`operaciones_mvtos`.`fecha_operacion`       AS `fecha`,
	`operaciones_mvtos`.`recibo_afectado`       AS `recibo`,
	`operaciones_recibos`.`tipo_pago`           AS `tipo_de_pago`,
	`operaciones_recibos`.`recibo_fiscal`       AS `recibo_fiscal`,

	`operaciones_mvtos`.`periodo_socio`         AS `parcialidad`,
	`operaciones_tipos`.`descripcion_operacion` AS `operacion`,
	`operaciones_mvtos`.`afectacion_real`       AS `monto`,

	`operaciones_mvtos`.`detalles`              AS `observaciones`
FROM
	`operaciones_mvtos` `operaciones_mvtos` 
		INNER JOIN `operaciones_recibos` `operaciones_recibos` 
		ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.
		`idoperaciones_recibos` 
			INNER JOIN `operaciones_tipos` `operaciones_tipos` 
			ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
			`idoperaciones_tipos` 
WHERE
	(`operaciones_mvtos`.`docto_afectado` =$credito )
	$ByFecha
	$ByTipo
ORDER BY
	`operaciones_mvtos`.`fecha_operacion`,
	`operaciones_tipos`.`descripcion_operacion` ";
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
$xCred		= new cCredito($credito);
if($xCred->init() == true){
	$xRPT->addContent($xCred->getFicha(true, "", true, true));
}

$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);

$xRPT->addContent($body);


$xRPT->addContent( $xT->Show( ) );
//============ Agregar HTML

$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);

?>