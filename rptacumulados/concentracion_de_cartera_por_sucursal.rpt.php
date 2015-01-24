<?php
/**
 * @author Balam Gonzalez Luis
 * @version 1.2
 * @since 2007-06-01
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
//include_once "../libs/graph.oo.php";
include_once "../core/core.config.inc.php";
include_once "../core/core.common.inc.php";
include_once "../libs/open_flash_chart_object.php";

$oficial = elusuario($iduser);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Concentrado de la Cartera de Credito por Sucursal</title>
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
	(`creditos_solicitud`.`sucursal` ='$mSuc') ";
}
$ByStat				= "";


$inputG 			= $_GET["outg"];

if ($Stat != "todas"){
	$ByStat	= " AND
				(`creditos_solicitud`.`estatus_actual` =$Stat)  ";
}

$sql = "SELECT
	`creditos_solicitud`.`sucursal`,
	`general_sucursales`.`nombre_sucursal`,
	COUNT(`creditos_solicitud`.`numero_solicitud`) AS `creditos`,
	SUM(`creditos_solicitud`.`saldo_actual`)       AS `saldo_insoluto_total`

FROM
	`creditos_solicitud` `creditos_solicitud`
		INNER JOIN `general_sucursales` `general_sucursales`
		ON `creditos_solicitud`.`sucursal` = `general_sucursales`.
		`codigo_sucursal`
WHERE
	(`creditos_solicitud`.`saldo_actual` >=0.99)
	$ByStat
	$BySuc
GROUP BY
	`creditos_solicitud`.`sucursal` ";
	$rs 	= 	mysql_query($sql, cnnGeneral());
	$lbl	= array();
	$val	= array();

	$tds 	= "";
?>
<!-- -->
<table       >
	<thead>
		<tr>
			<td class="subtitle">REPORTE DE CONCENTRACION DE LA CARTERA POR SUCURSAL</td>
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
		$val[] = round( ($rw["saldo_insoluto_total"] / 1000) , 2);
		$lbl[] = $rw["sucursal"];

		$tds .= "
			<tr>
				<td>" . $rw["sucursal"] . "</td>
				<td class ='mny'>" . round( ($rw["saldo_insoluto_total"] / 1000) , 2) . "</td>
			</tr>
		";

	}
	$x = new SAFEChart();
	$x->setValues($val);
	$x->setLabels($lbl);
	$x->setTitle("Concentacion de la Cartera por Sucursal(Miles)");
	$mFile	= $x->ChartPIE();
	


open_flash_chart_object( 512, 512, $mFile, true, "../" );

echo getRawFooter();
?>
</body>
</html>
