<?php
/**
 * @author Balam Gonzalez Luis
 * @version 1.2
 * @since 2007-06-01
 * 
 * Changes:
 * 05/05/2008 Reescritura
 */
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
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../libs/sql.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../core/core.config.inc.php";
include_once "../core/core.common.inc.php";
include_once "../libs/open_flash_chart_object.php";

$oficial = elusuario($iduser);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Reporte de Recuperacion en un Rango de Fechas</title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body onLoad="javascript:window.print();">
<!--  -->
<?php
echo getRawHeader();
/**
 * Filtrar si hay Fecha
 */
$fecha_inicial 		= $_GET["on"];
$fecha_final 		= $_GET["off"];
$si_es_por_fecha 	= "";
$Mvto 				= $_GET["f3"];
$Stat 				= $_GET["f2"];

$BySuc				= "";
$mSuc				= $_GET["s"];
if($mSuc!="todas"){
	$BySuc			= " 	AND
	(`captacion_cuentas`.`sucursal` ='$mSuc') ";
}
$ByStat				= "";


$inputG 			= $_GET["outg"];

if ($Stat != "todas"){

}


$sql = "SELECT
	`captacion_cuentas`.`inversion_fecha_vcto` AS 'fecha',
	COUNT(`captacion_cuentas`.`numero_cuenta`) AS 'numero',
	SUM(`captacion_cuentas`.`saldo_cuenta`)  AS 'monto'
FROM
	`captacion_subproductos` `captacion_subproductos`
		INNER JOIN `captacion_cuentas` `captacion_cuentas`
		ON `captacion_subproductos`.`idcaptacion_subproductos` =
		`captacion_cuentas`.`tipo_subproducto`
WHERE
	(`captacion_subproductos`.`metodo_de_abono_de_interes` ='AL_VENCIMIENTO')
	AND
	(`captacion_cuentas`.`inversion_fecha_vcto` >='2008-01-01')
	AND
	(`captacion_cuentas`.`inversion_fecha_vcto` <='2008-05-31')
	AND
	(`captacion_cuentas`.`saldo_cuenta` > " . TOLERANCIA_SALDOS . ")
	$BySuc
	$ByStat
GROUP BY
	`captacion_cuentas`.`inversion_fecha_vcto`
 ";


	$rs = mysql_query($sql);
	//echo $sql; exit;
	$gvalues 	= "";
	$gvalues2 	= "";
	$gnames 	= "";
	$i 			= 0;
	$tds 		= "";
	$mnt 		= 0;
	$sm 		= 0;
	$nm 		= 0;
	$val		= array();
	$lbl		= array();
?>
<!-- -->
<table       >
	<thead>
		<tr>
			<td class="subtitle">REPORTE DE VENCIMIENTOS DE INVERSION POR FECHA DADA</td>
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
		<tr>
			<td>&nbsp;</td>
			<td>Fecha Inicial:</td>
			<td><?php echo fecha_corta($fecha_inicial) ; ?></td>
		</tr>
								<tr>
			<td>&nbsp;</td>
			<td>Fecha Final</td>
			<td><?php echo fecha_corta($fecha_final) ; ?></td>
		</tr>
	</thead>
</table>
<?php

	while ($rw = mysql_fetch_array($rs)){

		$val[]	= round( ($rw["monto"] / 1000), 2);
		$lbl[]	= date("d/m", strtotime($rw["fecha"]) );

		$sm += $rw["monto"];
		$nm += $rw["numero"];
		$i++;
	}


	$x = new SAFEChart();
	$x->setValues($val);
	$x->setLabels($lbl);
	$x->setTitle("Calendario de Vencimientos de Inversion(Miles de Pesos)");
	$mFile	= $x->Chart3DBar(500);

open_flash_chart_object( 800, 400, $mFile, true, "../" );
echo getRawFooter();
?>
</body>
</html>