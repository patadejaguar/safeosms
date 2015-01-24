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
<title>Reporte de Colocacion en un Rango de Fechas</title>
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
$Stat 				= $_GET["f2"];
$Mvto 				= $_GET["f3"];
$mSuc				= $_GET["s"];

$ByMvto				= "";
$BySuc				= "";
$ByStat				= "";
$si_es_por_fecha 	= "";

$inputG 			= $_GET["outg"];


if( isset($mSuc) AND $mSuc!="todas"){
	$BySuc			= " AND `socios_general`.`sucursal`= '$mSuc'";
}

if (isset($fecha_inicial) && isset($fecha_final) ){
	$si_es_por_fecha = " fechaalta >='$fecha_inicial' AND fechaalta <='$fecha_final' ";
}

if( isset($Mvto) AND $Mvto != "todas" ){
	$ByMvto	= " AND
				(`operaciones_mvtos`.`tipo_operacion` = $Mvto) ";
}


if ( isset($Stat) AND $Stat != "todas"){
	$ByStat	= " AND
				(`creditos_solicitud`.`estatus_actual` =$Stat)  ";
}

$sql = "
SELECT
	
	`socios_tipoingreso`.`descripcion_tipoingreso` AS `concepto`,
	COUNT(`socios_general`.`codigo`)               AS `monto`,
	`socios_general`.`tipoingreso`
FROM
	`socios_general` `socios_general` 
		INNER JOIN `socios_tipoingreso` `socios_tipoingreso` 
		ON `socios_general`.`tipoingreso` = `socios_tipoingreso`.
		`idsocios_tipoingreso` 
WHERE
$si_es_por_fecha
$BySuc
	GROUP BY
		`socios_general`.`tipoingreso`
";
	
?>
<!-- -->
<table       >
	<thead>
		<tr>
			<td class="subtitle">REPORTE DE PERSONAS POR TIPO DE INGRESO POR FECHA DADA</td>
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
		
	
	$sm += $rw[1];
	
	
	
	$tds = $tds . "<tr>
	<td>$rw[0]</td>
	<td class='mny'>" . getFMoney($rw[1]) . "</td>
	
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
		<td>$sm</td>
	</tr>
	
	</table>";
	$x = new SAFEChart();
	$x->setValues($val);
	$x->setLabels($lbl);
	$x->setTitle("SALDOS DE CREDITOS MINISTRADOS EN UN RANGO DE FECHAS(Miles)");
	
	$mFile	= $x->ChartPIE();
	open_flash_chart_object( 800, 512, $mFile, true, "../" );
	echo getRawFooter();
?>
</body>
</html>