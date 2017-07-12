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
$xHP		= new cHPage("TR.ESTADO DE CUENTA DE APORTACIONES", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$xQL		= new MQL();
$xFil		= new cSQLFiltros();

//===========  Individual
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$recibo		= parametro("recibo", 0, MQL_INT); $recibo		= parametro("idrecibo", $recibo, MQL_INT);
//===========  General
$out 			= parametro("out", SYS_DEFAULT);
$FechaInicial	= parametro("on", $xF->getFechaMinimaOperativa(), MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false, MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);
$detalle		= parametro("detalle", false, MQL_BOOL);

$xB				= new cBases();
$sql			= $xL->getListadoDeOperacionesPorBase($xB->BASE_ESTADO_APORTACIONES, $persona, false, $FechaInicial, $FechaFinal);
$sqlAlt			= "SELECT	`ejercicio`, `enero`,`febrero`,`marzo`,`abril`,`mayo`,`junio`,`julio`,`agosto`,`septiembre`,`octubre`,`noviembre`,`diciembre`,
	(`enero`+`febrero`+`marzo`+`abril`+`mayo`+`junio`+`julio`+`agosto`+`septiembre`+`octubre`+`noviembre`+`diciembre`) AS `total`, '" . HP_REPLACE_DATA . "' AS `ahorro` 
FROM `vw_cuotas_pagadas_por_mes` WHERE persona=$persona ";
$sql			= ($detalle == true) ? $sql : $sqlAlt;
$titulo			= "";
$archivo		= "";
//============= Ahorro
$rsAh			= $xQL->getDataRecord("SELECT
	
	YEAR(`operaciones_mvtos`.`fecha_operacion`) AS `ejercicio`,
	SUM(`operaciones_mvtos`.`afectacion_real`)  AS `monto`
FROM
	`operaciones_mvtos` `operaciones_mvtos` 
	
WHERE
	(`operaciones_mvtos`.`tipo_operacion` =220)
	AND `operaciones_mvtos`.`socio_afectado` = $persona 
GROUP BY
	
	`operaciones_mvtos`.`socio_afectado`,
	`operaciones_mvtos`.`tipo_operacion` ,
	YEAR(`operaciones_mvtos`.`fecha_operacion`)
ORDER BY
	YEAR(`operaciones_mvtos`.`fecha_operacion`)");

$DAhorro	= array();
foreach ($rsAh as $rwA){
	$DAhorro[$rwA["ejercicio"]]	= $rwA["monto"];
}
$rsAh = null;
//============== END Ahorro
$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
$xSoc			= new cSocio($persona);
if($xSoc->init() == true){
	$xRPT->addContent($xSoc->getFicha(true, false));

	$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
	$xRPT->setBodyMail($body);
	$xT		= new cTabla($sql, 2);
	
	if($detalle == false){
		$xT->setOmitidos("tipo");
		$xRPT->setSQL($sql);
		$xRPT->setOmitir("tipo");
		$xRPT->setOmitir("persona");
		$xRPT->addCampoSuma("total");
		$xRPT->setKeyUnique("ejercicio");
		$xRPT->setDataReplace($DAhorro);
		$xRPT->addCampoSuma("ahorro");
		$xRPT->setProcessSQL();
		
		
	} else {
		
		$xT->setTipoSalida($out);
		$xT->setOmitidos("activo");
		$xT->setOmitidos("pasivo");
		
		//$xT->setOmitidos("parcialidad");
		$xT->setTitulo("parcialidad", "mes");
		$xT->setOmitidos("total_recibo");
		$xT->setOmitidos("tipo_de_operacion");
				
		$xT->setFootSum(array(
				5 => "monto"
		));
		$xRPT->addContent( $xT->Show() );		
	}
	

}
//============ Agregar HTML
$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);
?>