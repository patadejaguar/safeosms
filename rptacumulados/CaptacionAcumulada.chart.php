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
<title>Reporte de Captacion en un Rango de Fechas</title>
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
$f3 				= $_GET["f3"];
$input 				= $_GET["out"];
$f50 				= $_GET["f50"];
$sucursal			= $_GET["f700"];


$BySucursal 	= "";
if ($sucursal != "todas" and isset($sucursal)){
	$BySucursal = "  AND operaciones_mvtos.sucursal = '$sucursal'  ";
}



$ByStat				= "";
$si_es_por_fecha 	= "";

$inputG 			= $_GET["outg"];




$sql = "
SELECT
	/* `eacp_config_bases_de_integracion_miembros`.`codigo_de_base`, */
	`operaciones_mvtos`.`sucursal`,
	/* COUNT(`operaciones_mvtos`.`idoperaciones_mvtos`) AS `operaciones`, */
	SUM(`eacp_config_bases_de_integracion_miembros`.`afectacion` * `operaciones_mvtos`.`afectacion_real`)       AS `monto`

FROM
	`operaciones_mvtos` `operaciones_mvtos`
		INNER JOIN `eacp_config_bases_de_integracion_miembros`
		`eacp_config_bases_de_integracion_miembros`
		ON `operaciones_mvtos`.`tipo_operacion` =
		`eacp_config_bases_de_integracion_miembros`.`miembro`
WHERE
	(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =2006)
	AND
	(`operaciones_mvtos`.`fecha_operacion` >='$fecha_inicial')
	AND
	(`operaciones_mvtos`.`fecha_operacion` <='$fecha_final')
	$BySucursal
GROUP BY
	`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
	`operaciones_mvtos`.`sucursal`
ORDER BY
	monto DESC
";

?>
<!-- -->
<table       >
	<thead>
		<tr>
			<td class="subtitle" colspan='3'>REPORTE DE DEPOSITOS DE CAPTACION POR SUCURSAL EN UN RANGO DE FECHAS(MILES)</td>
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

	$rs 		= mysql_query($sql, cnnGeneral());
	$gvalues 	= "";
	$gnames 	= "";
	$i 			= 0;
	$tds 		= "";
	$mnt 		= 0;
	$sm 		= 0;

	while ($rw = mysql_fetch_array($rs)){
		$val[] 	= round( ($rw[1] / 1000) , 2);
		$lbl[] 	= $rw[0];


	$sm += round( ($rw[1] / 1000) , 2);



	$tds = $tds . "<tr>
	<td>$rw[0]</td>
	<td class='mny'>" . getFMoney(round( ($rw[1] / 1000) , 2)) . "</td>

	</tr>";
		$i++;
	}
	//echo $gnames;
	//echo $gvalues;
	$sm = getFMoney($sm);

	echo "<table width='100%' aling='center'>
	<tr>
		<th>Concepto</th>
		<th>Monto</th>
	</tr>

	$tds

	<tr>
		<td>Sumas</td>
		<th class='mny'>$sm</th>
	</tr>

	</table>";
	$x = new SAFEChart();
	$x->setValues($val);
	$x->setLabels($lbl);
	$x->setTitle("DEPOSITOS DE CAPTACION POR SUCURSAL EN UN RANGO DE FECHAS(MILES)");

	$mFile	= $x->Chart3DBAR(20000);
	open_flash_chart_object( 768, 512, $mFile, true, "../" );
	echo getRawFooter();
?>
</body>
</html>
