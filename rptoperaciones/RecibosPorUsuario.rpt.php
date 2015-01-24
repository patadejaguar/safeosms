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
$oficial 		= elusuario($iduser);
//=====================================================================================================
$xHP			= new cHPage("Reporte de Recibos por Usuarios", HP_REPORT);

$xF			= new cFecha();
$tipo_pago		= ( isset($_GET["tipodepago"])) ? $_GET["tipodepago"] : SYS_TODAS;
$tipo_de_recibo		= ( isset($_GET["tipoderecibo"]) ) ? $_GET["tipoderecibo"] : SYS_TODAS;
$oficial 		= (isset($_REQUEST["usuario"])) ? $_REQUEST["usuario"] : SYS_TODAS;

$tipo_de_recibo	= parametro("tiporecibo", $tipo_de_recibo, MQL_INT);

$mx 			= (isset($_GET["mx"])) ? true : false;
if($mx == true){
	$fechaInicial	= (isset($_GET["on"])) ? $xF->getFechaISO( $_GET["on"]) : FECHA_INICIO_OPERACIONES_SISTEMA;
	$fechaFinal	= (isset($_GET["off"])) ? $xF->getFechaISO( $_GET["off"]) : fechasys();
} else {
	$fechaInicial	= (isset($_GET["on"])) ? $_GET["on"] : FECHA_INICIO_OPERACIONES_SISTEMA;
	$fechaFinal	= (isset($_GET["off"])) ? $_GET["off"] : fechasys();
}
$ByOperacion		= "";

$es_por_oficial		= ($oficial != SYS_TODAS) ? " AND (`usuarios`.`idusuarios`  = $oficial ) " : "";
$ByTipoPago		= ($tipo_pago != SYS_TODAS) ? " AND (`operaciones_recibos`.`tipo_pago` ='$tipo_pago') " : "";
$ByTRecibo		= ( setNoMenorQueCero($tipo_de_recibo) > 0) ? " AND (`operaciones_recibos`.`tipo_docto` ='$tipo_de_recibo') " : "";
$input 			= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;

	$setSql = "SELECT
		`usuarios`.`nombreusuario`                          AS `usuario`,
		`operaciones_recibos`.`idoperaciones_recibos`       AS `numero`,
		`operaciones_recibos`.`fecha_operacion`             AS `fecha`,
		`operaciones_recibos`.`numero_socio`                AS `socio`,
		`socios`.`nombre` ,
		`operaciones_recibos`.`docto_afectado`              AS `documento`,
		`operaciones_recibostipo`.`descripcion_recibostipo` AS `tipo`,
		/*`operaciones_recibos`.`cheque_afectador`            AS `cheque`,*/
		`operaciones_recibos`.`tipo_pago`                   AS `forma_de_pago`,
		/*`operaciones_recibos`.`recibo_fiscal`,*/
		/*`operaciones_recibos`.`sucursal` ,*/
		`operaciones_recibos`.`total_operacion`             AS `total`,
		`operaciones_recibos`.`observacion_recibo` 
	FROM
		`operaciones_recibos` `operaciones_recibos` 
			INNER JOIN `socios` `socios` 
			ON `operaciones_recibos`.`numero_socio` = `socios`.`codigo` 
				INNER JOIN `operaciones_recibostipo` `operaciones_recibostipo` 
				ON `operaciones_recibostipo`.`idoperaciones_recibostipo` = 
				`operaciones_recibos`.`tipo_docto` 
					INNER JOIN `usuarios` `usuarios` 
					ON `operaciones_recibos`.`idusuario` = `usuarios`.`idusuarios`
				
	WHERE 
		operaciones_recibos.fecha_operacion>='$fechaInicial'
		AND operaciones_recibos.fecha_operacion<='$fechaFinal'
		$es_por_oficial
		$ByTipoPago
		$ByTRecibo
	ORDER BY
		`operaciones_recibos`.`fecha_operacion`,
		`operaciones_recibos`.`tipo_pago`,
		`usuarios`.`idusuarios`,
		`operaciones_recibos`.`numero_socio`
		/*operaciones_recibos.tipo_docto*/
	";
//exit($setSql);



if ($input==OUT_EXCEL) {
	$xXls	= new cHExcel();
	$xXls->convertTable($setSql, $xHP->getTitle());
} else {
	$xRpt	= new cReportes();
	$cTbl 	= new cTabla($setSql);
	echo $xHP->getHeader();
	echo $xHP->setBodyinit();
	echo $xHP->getEncabezado();
	echo $xRpt->getEncabezado($xHP->getTitle(), $fechaInicial, $fechaFinal, $oficial );
	$cTbl->setTdClassByType();
	$cTbl->Show("", false);
	echo $xHP->setBodyEnd();
}

?>