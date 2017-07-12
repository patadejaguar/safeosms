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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../core/core.config.inc.php");
include_once("../libs/open_flash_chart_object.php");

$oficial = elusuario($iduser);

$fecha_inicial 		= $_GET["on"];
$fecha_final 		= $_GET["off"];
$si_es_por_fecha 	= "";
$Mvto 				= $_GET["f3"];
$Stat 				= $_GET["f2"];

$BySuc				= "";
$mSuc				= $_GET["s"];
if($mSuc!="todas"){
	$BySuc			= " 	AND
	(`creditos_solicitud`.`sucursal` ='$mSuc') ";
}
$ByStat				= "";


$inputG 			= $_GET["outg"];

if ($Stat != "todas"){
	$ByStat	= " AND
				(`creditos_solicitud`.`estatus_actual` =$Stat)  ";
}

$input 				= $_GET["out"];
	if (!$input) {
		$input = "default";
	}

if ( $input != OUT_EXCEL ){

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body onLoad="initComponents();">
<?php
echo getRawHeader();
?>
<!-- -->
<table       >
	<thead>
		<tr>
			<td>REPORTE COMPARATIVO DE CREDITOS MIGRADOS VS MOVIMIENTOS MIGRADOS</td>
		</tr>
<!-- DATOS GENERALES DEL REPORTE  -->
		<tr>
			<td width="60%">&nbsp;</td>
			<td width="20%">Fecha de Elaboracion:</td>
			<td width="20%"><?php echo fecha_larga(); ?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>Preparado por:</td>
			<td><?php echo $oficial; ?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>Estatus</td>
			<td><?php echo $Stat; ?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>Sucursal</td>
			<td><?php echo $mSuc ; ?></td>
		</tr>
	</thead>
</table>
<?php
} else {

	$filename = $_SERVER['SCRIPT_NAME'];
	$filename = str_replace(".php", "", $filename);
	$filename = str_replace("rpt", "", $filename);
	$filename = str_replace("-", "", 	$filename);
  	$filename = "$filename-" . date("YmdHi") . "-from-" .  $iduser . ".xls";

  	header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=$filename");
	header("Pragma: no-cache");
	header("Expires: 0");
}


$sqx  = "SELECT
	`creditos_solicitud`.`tipo_convenio`,
	`creditos_tipoconvenio`.`descripcion_tipoconvenio`,
	COUNT(`creditos_solicitud`.`numero_solicitud`)          AS
	`numero_de_creditos`,
	SUM(`migracion_tcb_prestamos_mvtos`.`pagos`)            AS `numero_de_pagos`
	,
	SUM(`migracion_tcb_prestamos_mvtos`.`capital`)          AS `suma_de_capital`
	,
	SUM(`creditos_solicitud`.`saldo_actual`)                AS `suma_de_saldos`,
	SUM(`creditos_solicitud`.`monto_autorizado`)            AS
	`suma_de_monto_original`,
	SUM(`migracion_tcb_prestamos_mvtos`.`pagos_de_capital`) AS `suma_de_pagos`,
	SUM(`creditos_solicitud`.`saldo_actual`- (`creditos_solicitud`.`monto_autorizado` - `migracion_tcb_prestamos_mvtos`.`pagos_de_capital`) ) AS `diferencias`
FROM
	`creditos_solicitud` `creditos_solicitud`
		INNER JOIN `migracion_tcb_prestamos_mvtos`
		`migracion_tcb_prestamos_mvtos`
		ON `creditos_solicitud`.`numero_solicitud` =
		`migracion_tcb_prestamos_mvtos`.`numero_de_credito`
			INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio`
			ON `creditos_tipoconvenio`.`idcreditos_tipoconvenio` =
			`creditos_solicitud`.`tipo_convenio`
		GROUP BY
			`creditos_solicitud`.`tipo_convenio`
ORDER BY diferencias
";
	$cTbl	= new cTabla($sqx, 0);
	$cTbl->Show("", false);

if ( $input != OUT_EXCEL ){

echo getRawFooter();
?>
</body>
<script  >
<?php

?>
function initComponents(){
	window.print();
}
</script>
</html>
<?php
}
?>