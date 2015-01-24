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
	(`creditos_solicitud`.`sucursal` ='$mSuc') ";
}
$ByStat				= "";


$inputG 			= $_GET["outg"];

if ($Stat != "todas"){
	$ByStat	= " AND
				(`creditos_solicitud`.`estatus_actual` =$Stat)  ";
}

$sql = "SELECT
	`creditos_periocidadpagos`.`descripcion_periocidadpagos` AS `periocidad`,
	COUNT(`creditos_solicitud`.`numero_solicitud`)           AS `numero`,
	SUM(`creditos_solicitud`.`saldo_actual`)                 AS
	`monto`,
	`creditos_solicitud`.`periocidad_de_pago`
FROM
	`creditos_solicitud` `creditos_solicitud`
		INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos`
		ON `creditos_solicitud`.`periocidad_de_pago` =
		`creditos_periocidadpagos`.`idcreditos_periocidadpagos`
WHERE
	(`creditos_solicitud`.`saldo_actual` >=0.99)
	$ByStat
	$BySuc
	GROUP BY
		`creditos_solicitud`.`periocidad_de_pago`";
	$rs 	= 	mysql_query($sql, cnnGeneral());
	$lbl	= array();
	$val	= array();


	while ($rw = mysql_fetch_array($rs)){

		$val[] 	= round( ($rw["monto"] / 1000) , 2);
		$lbl[] 	= $rw["destino"];

	$sm += $rw["monto"];
	$nm += $rw["numero"];


	$tds = $tds . "<tr>
	<td>" . $rw["periocidad"] .  "</td>
	<td>" . $rw["numero"] .  "</td>
	<td class='mny'>". getFMoney($rw["monto"]) . "</td>
	</tr>";
		$i++;
	}
	//echo $gnames;
	//echo $gvalues;


	echo "<table align=\"center\" width='100%'>
	<tr>
		<th>Destino del Credito</th>
		<th>Numero de Credito</th>
		<th>Saldo del Capital Insoluto</th>
	</tr>

	$tds

	<tr>
		<td>Sumas</td>
		<th>". getFMoney($nm) ."</th>
		<th>". getFMoney($sm) ."</th>
	</tr>
	</td>
	</table>
";

	$x = new SAFEChart();
	$x->setValues($val);
	$x->setLabels($lbl);
	$x->setTitle("REPORTE DE CONCENTRACION DE LA CARTERA POR DESTINO ECONOMICO(Miles)");

	$mFile	= $x->ChartPIE();
	open_flash_chart_object( 768, 512, $mFile, true, "../" );
	echo getRawFooter();
?>
</body>
</html>