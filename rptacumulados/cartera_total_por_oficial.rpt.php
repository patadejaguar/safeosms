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
if($mSuc!="todas"){ $BySuc = " AND	(`creditos_solicitud`.`sucursal` ='$mSuc') "; }
$ByStat				= "";

$inputG 			= $_GET["outg"];

if ($Stat != "todas"){ $ByStat	= " AND	(`creditos_solicitud`.`estatus_actual` =$Stat)  "; }

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
			<td>REPORTE DE</td>
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
$x  = "SELECT
	`oficiales`.`nombre_completo`,
	SUM(`creditos_solicitud`.`saldo_actual`)       AS `monto`,
	COUNT(`creditos_solicitud`.`numero_solicitud`) AS `numero` 
FROM
	`creditos_solicitud` `creditos_solicitud` 
		INNER JOIN `oficiales` `oficiales` 
		ON `creditos_solicitud`.`oficial_credito` = `oficiales`.`id` 
WHERE
	(`creditos_solicitud`.`saldo_actual` >=" . TOLERANCIA_SALDOS . ")
	$BySuc
	$ByStat
GROUP BY
	`creditos_solicitud`.`idusuario` 
ORDER BY
	`creditos_solicitud`.`estatus_actual`";

	$rs 	= 	mysql_query($sql, cnnGeneral());
	$lbl	= array();
	$val	= array();

	$tds 	= "";

	while ($rw = mysql_fetch_array($rs)){
		$val[] = round( ($rw["monto"] / 1000) , 2);
		$lbl[] = $rw["nombre_completo"];

	}

	$x = new SAFEChart();
	$x->setValues($val);
	$x->setLabels($lbl);
	$x->setTitle("Concentacion de la Cartera por Sucursal(Miles)");
	$mFile	= $x->Chart3DBAR(25000);

open_flash_chart_object( 600, 300, $mFile, true, "../" );

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