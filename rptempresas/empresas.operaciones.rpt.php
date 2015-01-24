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
$periodo		= (isset($_GET["periodo"])) ? $_GET["periodo"]: SYS_TODAS;
//$estado		= (isset($_GET["estado"])) ? $_GET["estado"]: SYS_TODAS;
/**
 */
$xF				= new cFecha();
$estatus 		= (isset($_GET["estado"]) ) ? $_GET["estado"] : SYS_TODAS;
$frecuencia 	= (isset($_GET["periocidad"]) ) ? $_GET["periocidad"] : SYS_TODAS;
$convenio 		= (isset($_GET["convenio"]) ) ? $_GET["convenio"] : SYS_TODAS;
$convenio 		= (isset($_GET["producto"]) ) ? $_GET["producto"] : $convenio;

$empresa		= (isset($_GET["empresa"]) ) ? $_GET["empresa"] : SYS_TODAS;
$out 			= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;
$fechaInicial	= (isset($_GET["on"])) ? $xF->getFechaISO( $_GET["on"]) : FECHA_INICIO_OPERACIONES_SISTEMA;
$fechaFinal		= (isset($_GET["off"])) ? $xF->getFechaISO( $_GET["off"]) : fechasys();

$es_por_estatus 	= "";
$es_por_frecuencia 	= "";
$es_por_convenio 	= "";

$ByEmpresa		= ( $empresa == SYS_TODAS ) ? "" : " AND socios.iddependencia = $empresa ";

if($estatus != SYS_TODAS){ $es_por_estatus = " AND creditos_solicitud.estatus_actual=$estatus "; }
//
if($frecuencia != SYS_TODAS){ $es_por_frecuencia 	= " AND creditos_solicitud.periocidad_de_pago =$frecuencia ";}
//
if($convenio != SYS_TODAS){ $es_por_convenio = " AND creditos_solicitud.tipo_convenio = $convenio "; }
/* ******************************************************************************/
$setSql = "SELECT
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
	(`socios`.`iddependencia` =$empresa) AND
	(`operaciones_mvtos`.`fecha_afectacion` >='$fechaInicial') AND (`operaciones_mvtos`.`fecha_afectacion` <='$fechaFinal')
GROUP BY
	`socios`.`iddependencia`,
	`operaciones_mvtos`.`tipo_operacion`
	";
	$xT		= new cTabla($setSql, 0);
	
	echo $xHP->getHeader();
	echo $xHP->setBodyinit();
	$xRPT	= new cReportes();
	echo $xRPT->getEncabezado($xHP->getTitle(), $fechaInicial, $fechaFinal);	
	//echo $setSql;
	
	echo $xT->Show();

	echo $xRPT->getPie();
	echo $xHP->setBodyEnd();
	echo $xHP->end();
?>