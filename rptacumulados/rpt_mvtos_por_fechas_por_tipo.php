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
//include_once "../libs/graph.oo.php";
include_once "../core/core.config.inc.php";
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

$BySuc				= "";
$mSuc				= $_GET["s"];

if($mSuc!="todas"){
	$BySuc			= " 	AND
	(`operaciones_mvtos`.`sucursal` ='$mSuc') ";
}
$ByMvto				= "";


$inputG 			= $_GET["outg"];

if ($Mvto != "todas"){
	$ByMvto	= " AND
				(`operaciones_mvtos`.`tipo_operacion` = $Mvto) ";
}


$sql = "SELECT
	`operaciones_mvtos`.`tipo_operacion`			AS 'tipo',
	`operaciones_tipos`.`descripcion_operacion`      AS `operacion`,
	COUNT(`operaciones_mvtos`.`idoperaciones_mvtos`) AS `numero`,
	SUM(`operaciones_mvtos`.`afectacion_real`)		AS 'monto'
FROM
	`operaciones_mvtos` `operaciones_mvtos`
		INNER JOIN `operaciones_tipos` `operaciones_tipos`
		ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
		`idoperaciones_tipos`
WHERE
	(`operaciones_mvtos`.`fecha_afectacion` >='$fecha_inicial')
	AND
	(`operaciones_mvtos`.`fecha_afectacion` <='$fecha_final')
	$BySuc
	$ByMvto
GROUP BY
	`operaciones_mvtos`.`tipo_operacion`

HAVING 
monto != 0 
	ORDER BY monto DESC, numero
	";

?>
<!-- -->
<table       >
	<thead>
		<tr>
			<td class="subtitle">REPORTE DE OPERACIONES ACUMULADAS EN FECHA DADA</td>
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
	//echo $sql; exit;
	$gvalues 	= "";
	$gvalues2 	= "";
	$gnames 	= "";
	$i 			= 0;
	$tds 		= "";
	$mnt 		= 0;
	$sm 		= 0;
	$nm 		= 0;

	while ($rw = mysql_fetch_array($rs)){

		$val[] = round( ($rw["monto"] / 1000) , 2);
		$lbl[] = $rw["operacion"];

	$sm += $rw["monto"];
	$nm += $rw["numero"];

	$tds = $tds . "<tr>
	<td>" . $rw["operacion"] .  "</td>
	<td>" . $rw["numero"] .  "</td>
	<td class='mny'>". getFMoney($rw["monto"]) . "</td>
	</tr>";
		$i++;
	}
	//echo $gnames;
	//echo $gvalues;


	echo "<table width='100%'>
	<tr>
		<th>Tipo de Operacion</th>
		<th>Numero de Operaciones</th>
		<th>monto</th>
	</tr>

	$tds

	<tr>
		<td>Sumas</td>
		<th>". getFMoney($nm) ."</th>
		<th>". getFMoney($sm) ."</th>
	</tr>
	</td>";
	$x = new SAFEChart();
	$x->setValues($val);
	$x->setLabels($lbl);
	$x->setTitle("Reporte de Operaciones Acumuladas por Tipo(Miles)");
	$mFile	= $x->Chart3DBAR(10000);



open_flash_chart_object( 800, 500, $mFile, true, "../" );
	echo getRawFooter();
?>
</body>
</html>