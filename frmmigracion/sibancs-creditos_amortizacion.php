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
//f_82cc9867.php
$sql	= "SELECT socio, credito, parcialidad, fecha_de_vencimiento, fecha_de_abono, saldo_vigente,
			    saldo_vencido, interes_vigente, interes_vencido, saldo_interes_vencido, interes_moratorio,
			    estatus, iva_interes_normal, iva_interes_moratorio
			    FROM sisbancs_amortizaciones
		  ";
$rs = mysql_query($sql, cnnGeneral() );

echo "<table>";

while($rw = mysql_fetch_array($rs) ){
	$a		= $rw["socio"];
	$b		= $rw["credito"];
	$c		= $rw["parcialidad"];
	$d		= $rw["fecha_de_vencimiento"];
	$e		= $rw["saldo_vigente"];
    $f		= $rw["saldo_vencido"];
    $g		= $rw["interes_vigente"];
    $h      = $rw["interes_vencido"];
    $i      = $rw["saldo_interes_vencido"];
    $j      = $rw["interes_moratorio"];
    $k      = $rw["estatus"];
    $l      = $rw["iva_interes_normal"];
	$m      = $rw["iva_interes_moratorio"];
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
				<td>$k</td>
				<td>$m</td>
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