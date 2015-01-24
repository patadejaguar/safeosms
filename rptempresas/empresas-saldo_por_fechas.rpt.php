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
$xHP					= new cHPage("TR.Saldos de Credito", HP_REPORT);
$xF						= new cFecha();
$xQL					= new MQL();
$xLi					= new cSQLListas();
//=====================================================================================================


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


$xF->set($fechaFinal);
$ejercicio		= $xF->anno();
my_query("SET @ejercicio:=$ejercicio;");

$sql					= "
SELECT
	`socios`.`iddependencia`,
	`socios`.`dependencia`,
	COUNT(`creditos_saldos_por_ejercicio`.`numero_solicitud`) AS `creditos`,
	SUM(`creditos_saldos_por_ejercicio`.`diciembre`) AS `saldo` 
FROM
	`socios` `socios` 
		INNER JOIN `creditos_saldos_por_ejercicio` 
		`creditos_saldos_por_ejercicio` 
		ON `socios`.`codigo` = `creditos_saldos_por_ejercicio`.`numero_socio` 
	GROUP BY
		`socios`.`iddependencia`
";
//$sql				= "CALL sp_saldos_al_cierre('$fechaFinal')";
//exit($sql);
$xTbl					= new cTabla($sql);
$xTbl->setFootSum(array(
		2 => "creditos",
		3 => "saldo"
		));
/*$xTbl->setFootSum(array(
	3 => "monto_autorizado",
		52 => "abonos",
		53 => "saldo"
));*/

$xRPT->setSQL($xTbl->getSQL());
$xTbl->setTipoSalida($formato);
$xRPT->setOut($formato);
$xRPT->addContent($xTbl->Show());
//$xRPT->setResponse();
echo $xRPT->render(true);
?>