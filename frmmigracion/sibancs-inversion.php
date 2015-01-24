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

$sql	= "SELECT *
    FROM captacion_cuentas
WHERE
	(`captacion_cuentas`.`tipo_cuenta` !=20) AND
	(`captacion_cuentas`.`saldo_cuenta` >0) ";
$rs = mysql_query($sql, cnnGeneral() );

echo "<table>";

while($rw = mysql_fetch_array($rs) ){
	$a		= $rw[ "numero_socio" ];
	$b		= $rw[ "numero_cuenta" ];
	$c		= $rw[ "fecha_afectacion" ];
	$d		= $rw[ "fecha_apertura" ];
	$e		= $rw[ "dias_invertidos" ];
    $f		= $rw[ "tasa_otorgada" ] * 100;
    $g		= $rw[ "saldo_cuenta" ];
    $h      = ($rw[ "saldo_cuenta" ] * $rw[ "tasa_otorgada" ] * $rw[ "dias_invertidos" ]) / EACP_DIAS_INTERES ;
    $i      = 0;
    $j      = 0;
    $k      = 0;
    $l      = 0;

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