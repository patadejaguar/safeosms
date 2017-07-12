<?php
/**
 * Reporte de
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package migracion
 * @subpackage captacion
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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../core/core.config.inc.php");


$oficial = elusuario($iduser);
$input 				= $_GET["out"];
	if (!$input) {
		$input = "default";
	}
$tblHD			= "";

if ( $input != "excel" ){
?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title></title>
	</head>
	<link href="<?php echo CSS_REPORT_FILE; ?>" rel="stylesheet" type="text/css">
	<body onLoad="initComponents();">
<?php
$tblHD	= "
	<tr>
		<th>SOCIO</th>
		<th>CREDITO</th>
		<th>SALDO VIGENTE</th>
		<th>SALDO VENCIDO</th>
		<th>SALDO INTERES</th>
		<th>SALDO INTERES DESPUES DEL VENCIMIENTO</th>
		<th>SALDO INTERES VENCIDO</th>
		<th>SALDO INTERES MORATORIO</th>
		<th>SALDO IVA INT NORMAL</th>
		<th>SALDO IVA INTERES MORATORIO</th>
	</tr>
";
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

$sql	= "
SELECT
	`sisbancs_amortizaciones`.`socio`,
	`sisbancs_amortizaciones`.`credito`,
	SUM(`sisbancs_amortizaciones`.`saldo_vigente`)         AS `c`,
	SUM(`sisbancs_amortizaciones`.`saldo_vencido`)         AS `d`,
	SUM(`sisbancs_amortizaciones`.`interes_vigente`)       AS `e`,
	SUM(`sisbancs_amortizaciones`.`interes_vencido`)       AS `f`,
	SUM(`sisbancs_amortizaciones`.`saldo_interes_vencido`) AS `g`,
	SUM(`sisbancs_amortizaciones`.`interes_moratorio`)     AS `h`,
	SUM(`sisbancs_amortizaciones`.`iva_interes_normal`)    AS `i`,
	SUM(`sisbancs_amortizaciones`.`iva_interes_moratorio`) AS `j`
FROM
	`sisbancs_amortizaciones` `sisbancs_amortizaciones`
GROUP BY
	`sisbancs_amortizaciones`.`socio`,
	`sisbancs_amortizaciones`.`credito`
";

$rs = mysql_query($sql, cnnGeneral() );

echo "<table>
	$tblHD";

while($rw = mysql_fetch_array($rs) ){
	$a		= $rw[ "socio" ];
	$b		= $rw[ "credito" ];
	$c		= $rw[ "c" ];
	$d		= $rw[ "d" ];
	$e		= $rw[ "e" ];
    $f		= $rw[ "f" ];
    $g		= $rw[ "g" ];
    $h      = $rw[ "h" ];
    $i      = $rw[ "i" ];
    $j      = $rw[ "j" ];


	echo	"
			<tr>
				<td>$a</td>
				<td>$b</td>
				<td>$c</td>
				<td>$d</td>
				<td>$e</td>
				<td>$f</td>
				<td>$g</td>
				<td>$h</td>
				<td>$i</td>
				<td>$j</td>
			</tr>";

}

echo "</table>";

if ( $input != "excel" ){
?>
</body>
<script  >
function initComponents(){
	window.print();
}
</script>
</html>
<?php
}