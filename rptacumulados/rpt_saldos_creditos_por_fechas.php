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
//=====================================================================================================
$xHP		= new cHPage("TR.Acumulado de Creditos por producto", HP_REPORT);
$xRPT		= new cReportes();
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();

$estatus 		= parametro("estado", SYS_TODAS);
$frecuencia 	= parametro("periocidad", SYS_TODAS);
$producto 		= parametro("convenio", SYS_TODAS);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS);
$out 			= parametro("out", SYS_DEFAULT);
$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);
$sucursal		= parametro("s", SYS_TODAS);
$si_es_por_fecha 		= "";

$graficos		= parametro("graficos", false, MQL_BOOL);

$BySuc					= ($sucursal == SYS_TODAS) ? "": " AND `creditos_solicitud`.`sucursal`= '$sucursal'";

$si_es_por_fecha = " AND fecha_ministracion>='$FechaInicial' AND fecha_ministracion<='$FechaFinal' ";


$sql = "
SELECT
	`creditos_tipoconvenio`.`descripcion_tipoconvenio` AS 'convenio',
		
	COUNT(`creditos_solicitud`.`numero_solicitud`) AS 'numero',
	(AVG(`creditos_solicitud`.`tasa_interes`) * 100) AS 'tasa_promedio',
	AVG(`creditos_solicitud`.`saldo_actual`) AS 'saldo_promedio',
		
	SUM(`creditos_solicitud`.`monto_autorizado`) AS 'ministrado',
	
	SUM(`creditos_solicitud`.`saldo_actual`) AS 'saldo'
	 
FROM
	`creditos_solicitud` `creditos_solicitud` 
		INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio` 
		ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
		`idcreditos_tipoconvenio` 
WHERE
	`estatus_actual` != 50 
	AND
	(`creditos_solicitud`.`monto_autorizado` >=" . TOLERANCIA_SALDOS . ")
	
	$si_es_por_fecha 
	$BySuc
GROUP BY
	`creditos_solicitud`.`tipo_convenio` ";
	
$xT		= new cTabla($sql);
$xT->setFootSum(array(
	1 => "numero",
	5 => "saldo"
));
$xRPT->setOut($out);
$xRPT->addContent( $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal) );
if($graficos == true){
	//$xT->setPrepareChart();
}

$xT->setTipoSalida($out);
$xRPT->addContent( $xT->Show() );
//$xRPT->addContent("<script>setTimeout('mychart',1500);  function mychart() { $('#sqltable').visualize({type: 'bar', width: '450px'}).appendTo('body'); }</script>");
echo $xRPT->render(true);
?>