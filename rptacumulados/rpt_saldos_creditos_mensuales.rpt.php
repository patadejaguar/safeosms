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

$BySuc			= ($sucursal == SYS_TODAS) ? "": " AND `creditos_solicitud`.`sucursal`= '$sucursal'";

$si_es_por_fecha = " AND fecha_ministracion>='$FechaInicial' AND fecha_ministracion<='$FechaFinal' ";
$xF->set($FechaFinal);
$ejercicio		= $xF->anno();


$sql = "
SELECT
	`creditos_tipoconvenio`.`descripcion_tipoconvenio`   AS `convenio`,
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
	`creditos_saldo_mensuales` `creditos_saldo_mensuales` 
		INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio` 
		ON `creditos_saldo_mensuales`.`tipo_convenio` = `creditos_tipoconvenio`.
		`idcreditos_tipoconvenio`
	GROUP BY
		
		`creditos_saldo_mensuales`.`tipo_convenio`
		ORDER BY `creditos_tipoconvenio`.`descripcion_tipoconvenio`
		 ";
//Por modalidad
/*
$sql = "
SELECT
	
	`creditos_modalidades`.`descripcion_modalidades` ,
		
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
	`creditos_tipoconvenio` `creditos_tipoconvenio` 
		INNER JOIN `creditos_modalidades` `creditos_modalidades` 
		ON `creditos_tipoconvenio`.`tipo_de_credito` = `creditos_modalidades`.
		`idcreditos_modalidades` 
			INNER JOIN `creditos_saldo_mensuales` `creditos_saldo_mensuales` 
			ON `creditos_saldo_mensuales`.`tipo_convenio` = 
			`creditos_tipoconvenio`.`idcreditos_tipoconvenio` 
		
	GROUP BY
			`creditos_tipoconvenio`.`tipo_de_credito`
		 ";*/
//=============== Por sucursal
/*$sql = "
SELECT

	`socios_general`.`sucursal`	,

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
	`socios_general` `socios_general` 
		INNER JOIN `creditos_saldo_mensuales` `creditos_saldo_mensuales` 
		ON `socios_general`.`codigo` = `creditos_saldo_mensuales`.`numero_socio` 

	GROUP BY
		`socios_general`.`sucursal`
		 ";
*/


$xT		= new cTabla($sql);
$xT->setFootSum(array(
	1 => "creditos",
	2 => "enero",
	3 => "febrero",
	4 => "marzo",
	5 => "abril",
	6 => "mayo",
	7 => "junio",
	8 => "julio",
	9 => "agosto",
	10 => "septiembre",
	11 => "octubre", 
	12 => "noviembre",
	13 => "diciembre"
));
$xRPT->setOut($out);
$xRPT->addContent( $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal) );

$xRPT->setSQL($sql);
$xT->setFechaCorte($FechaFinal);
$xT->setTipoSalida($out);
$xRPT->addContent( $xT->Show() );
//$xRPT->addContent("<script>setTimeout('mychart',1500);  function mychart() { $('#sqltable').visualize({type: 'bar', width: '450px'}).appendTo('body'); }</script>");
echo $xRPT->render(true);
?>