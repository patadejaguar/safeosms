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
//my_query("SET @ejercicio:=$ejercicio;");

/*$sql					= "
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
";*/
$sql		= "SELECT
	`socios_aeconomica_dependencias`.`idsocios_aeconomica_dependencias` AS `empresa`,
	`socios_aeconomica_dependencias`.`descripcion_dependencia` AS `descripcion`,
	COUNT(`creditos_saldo_mensuales`.`numero_solicitud`) AS `creditos`,
	SUM(`creditos_saldo_mensuales`.`enero`)      AS `enero`,
	SUM(`creditos_saldo_mensuales`.`febrero`)    AS `febrero`,
	SUM(`creditos_saldo_mensuales`.`marzo`)      AS `marzo`,
	SUM(`creditos_saldo_mensuales`.`abril`)      AS `abril`,
	SUM(`creditos_saldo_mensuales`.`mayo`)       AS `mayo`,
	SUM(`creditos_saldo_mensuales`.`junio`)      AS `junio`,
	SUM(`creditos_saldo_mensuales`.`julio`)      AS `julio`,
	SUM(`creditos_saldo_mensuales`.`agosto`)     AS `agosto`,
	SUM(`creditos_saldo_mensuales`.`septiembre`) AS `septiembre`,
	SUM(`creditos_saldo_mensuales`.`octubre`)    AS `octubre`,
	SUM(`creditos_saldo_mensuales`.`noviembre`)  AS `noviembre`,
	SUM(`creditos_saldo_mensuales`.`diciembre`)  AS `diciembre` 
FROM
	`creditos_solicitud` `creditos_solicitud` 
		INNER JOIN `creditos_saldo_mensuales` `creditos_saldo_mensuales` 
		ON `creditos_solicitud`.`numero_solicitud` = `creditos_saldo_mensuales`.
		`numero_solicitud` 
			INNER JOIN `socios_aeconomica_dependencias` 
			`socios_aeconomica_dependencias` 
			ON `creditos_solicitud`.`persona_asociada` = 
			`socios_aeconomica_dependencias`.`idsocios_aeconomica_dependencias`
		GROUP BY
			`creditos_solicitud`.`persona_asociada`		
			";

//$sql				= "CALL sp_saldos_al_cierre('$fechaFinal')";

$xTbl					= new cTabla($sql);
//$xTbl->setFechaCorte($fechaFinal);

$xTbl->setFootSum(array(
	2 => "creditos",
	3 => "enero",
	4 => "febrero",
	5 => "marzo",
	6 => "abril",
	7 => "mayo",
	8 => "junio",
	9 => "julio",
	10 => "agosto",
	11 => "septiembre",
	12 => "octubre", 
	13 => "noviembre",
	14 => "diciembre"
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