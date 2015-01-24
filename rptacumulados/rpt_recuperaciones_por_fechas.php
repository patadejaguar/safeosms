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
$fecha_inicial 			= $_GET["on"];
$fecha_final 			= $_GET["off"];
$si_es_por_fecha 		= "";

//Tipo de Operacion
$def_type = 120;

$mSuc					= $_GET["s"];
if($mSuc!="todas"){
	$BySuc				= "operaciones_mvtos.sucursal='$mSuc'";
}

$inputG 				= $_GET["outg"];

if ($fecha_inicial && $fecha_final){
	$si_es_por_fecha = " AND operaciones_mvtos.fecha_operacion>='$fecha_inicial' AND operaciones_mvtos.fecha_operacion<='$fecha_final' ";
}
$sql = "SELECT creditos.convenio AS 'tipo_de_convenio', 
			COUNT(operaciones_mvtos.idoperaciones_mvtos) AS 'numero_pagos', 
			SUM(operaciones_mvtos.afectacion_real) AS 'total_recuperado' 
			FROM creditos, operaciones_mvtos 
			WHERE operaciones_mvtos.docto_afectado=creditos.solicitud
			AND operaciones_mvtos.tipo_operacion=$def_type 
			$si_es_por_fecha
			$BySuc
			GROUP BY creditos.convenio";
?>
<!-- -->
<table       >
	<thead>
		<tr>
			<td>REPORTE DE RECUPERACION DE CREDITOS BASADO EN MOVIMIENTOS</td>
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

	$rs = mysql_query($sql);
	//echo $sql;
	$gvalues = "";
	$gvalues2 = "";
	$gnames = "";
	$i = 0;
	$tds = "";
	$mnt = 0;
	$sm = 0;
	$nm = 0;
	
	while ($rw = mysql_fetch_array($rs)){
		$val[] 	= round( ($rw[2] / 1000) , 2);
		$lbl[] 	= $rw[0];
		
	$mnt = cmoney($rw[2]);
	$sm = $sm + $rw[2];
	$nm = $nm + $rw[1];
	
	
	$tds = $tds . "<tr>
	<td>$rw[0]</td>
	<td>$rw[1]</td>
	<td class='mny'>$mnt</td>
	</tr>";
		$i++;
	}
	//echo $gnames;
	//echo $gvalues;
	$sm = cmoney($sm);
	
	echo "<table  >
	<tr>
		<th>Tipo de Convenio</th>
		<th>Numero de Operaciones</th>
		<th>Cantidad</th>
	</tr>
	
	$tds
	
	<tr>
		<td>Sumas</td>
		<th>$nm</th>
		<th>$sm</th>
	</tr>
	</td>";
	$x = new SAFEChart();
	$x->setValues($val);
	$x->setLabels($lbl);
	$x->setTitle("REPORTE DE RECUPERACION DE CREDITOS BASADO EN MOVIMIENTOS(Miles)");
	
	$mFile	= $x->Chart3DBAR(1000);
	open_flash_chart_object( 768, 512, $mFile, true, "../" );
	
	echo getRawFooter();
	echo getRawFooter();
?>
</body>
</html>