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
$xHP			= new cHPage("TR.Reporte de Recibos", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
$xFil		= new cSQLFiltros();


$tipo_pago			= (isset($_GET["tipodepago"])) ? $_GET["tipodepago"] : SYS_TODAS;
$tipo_de_recibo		= (isset($_GET["tipoderecibo"]) ) ? $_GET["tipoderecibo"] : SYS_TODAS;
$oficial 			= (isset($_REQUEST["usuario"])) ? $_REQUEST["usuario"] : SYS_TODAS;
$tipo_de_recibo		= parametro("tiporecibo", $tipo_de_recibo, MQL_INT);

$mx 			= (isset($_GET["mx"])) ? true : false;
if($mx == true){
	$fechaInicial	= (isset($_GET["on"])) ? $xF->getFechaISO( $_GET["on"]) : FECHA_INICIO_OPERACIONES_SISTEMA;
	$fechaFinal		= (isset($_GET["off"])) ? $xF->getFechaISO( $_GET["off"]) : fechasys();
} else {
	$fechaInicial	= (isset($_GET["on"])) ? $_GET["on"] : FECHA_INICIO_OPERACIONES_SISTEMA;
	$fechaFinal		= (isset($_GET["off"])) ? $_GET["off"] : fechasys();
}
$ByOperacion		= "";

$es_por_oficial		= ($oficial != SYS_TODAS) ? " AND (`usuarios`.`idusuarios`  = $oficial ) " : "";

$empresa			= parametro("empresa", SYS_TODAS, MQL_INT);

$ByTipoPago			= $xFil->RecibosPorTipoDePago($tipo_pago);// ($tipo_pago != SYS_TODAS) ? " AND (`operaciones_recibos`.`tipo_pago` ='$tipo_pago') " : "";
$ByTRecibo			= $xFil->RecibosPorTipo($tipo_de_recibo);
$ByEmpresa			= $xFil->RecibosPorPersonaAsociada($empresa);

$out 				= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;


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
		$ByEmpresa
	ORDER BY
		`operaciones_recibos`.`fecha_operacion`,
		`operaciones_recibos`.`tipo_pago`,
		`usuarios`.`idusuarios`,
		`operaciones_recibos`.`numero_socio`
		/*operaciones_recibos.tipo_docto*/
	";
//exit($setSql);



if($out == OUT_EXCEL){
	$xXls	= new cHExcel();
	$xXls->convertTable($setSql, $xHP->getTitle());
} else {
	$xRpt	= new cReportes($xHP->getTitle());
	$xRpt->setOut($out);
	//$cTbl 	= new cTabla($setSql);
	
	$xRpt->addContent( $xRpt->getEncabezado($xHP->getTitle(), $fechaInicial, $fechaFinal, $oficial ) );
	//$cTbl->setTdClassByType();
	$xRpt->setSQL($setSql);
	$xRpt->addCampoSuma("total");
	$xRpt->setProcessSQL();
	//$xRpt->addContent($html)
	echo $xRpt->render(true);
}

?>