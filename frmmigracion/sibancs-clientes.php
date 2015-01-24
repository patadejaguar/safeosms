<?php
/**
 * Reporte de Migracion de Socios
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package migracion
 * @subpackage clientes
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
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");

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
//Arrays de Conversion

$arrTipoPersona = array(
	9	=> 1,
	1	=> 2,
	3	=> 3,
	4	=> 4,
	2	=> 5,
	5	=> 6,
);
//Array de Estado Civil
$arrEstadoCivil	= array (
	2 => "S",
	3 => "S",
	5 => "S",
	99 => "S",
	6 => "V",
	1 => "C",
	4 => "U"
);

$arrTipoVivienda	= array (
	1 => "P",
	2 => "R",
	3 => "P",
	4 => "F",
	5 => "P",
	99 => "P"
);

$sql = "SELECT
	*
FROM
	`socios_general` `socios_general`
	";
$rs = mysql_query($sql, cnnGeneral() );

//

echo "<table>";

while($rw = mysql_fetch_array($rs) ){

	$soc	= new cSocio( $rw["codigo"] );
	$soc->init($rw);

	$a		= $rw[ "codigo" ];
	$b		= $rw[ "grupo_solidario" ];
	$c		= $rw[ "apellidopaterno" ];
	$d		= $rw[ "apellidomaterno" ];
	$e		= $rw[ "nombrecompleto" ];
	$f		= "S";
	$g		= $arrTipoPersona[ $rw[ "personalidad_juridica" ] ];
	$h		= $rw[ "fechaalta" ];

	$dom	= $soc->getDatosDomicilio();

	$i		= $dom["calle"] .  " " . $dom[ "numero_exterior" ];

	$j		= "";
	$k		= "";

	$l		= $dom["codigo_postal"];

	$m		= $rw[ "fechanacimiento" ];
	$n		= $rw[ "rfc" ];

	$o		= $soc->getIngresosMensuales();

	$p		= $arrEstadoCivil[ $rw[ "estadocivil" ] ];
	$q		= $arrTipoVivienda[ $dom["tipo_regimen"] ];
	$r		= "";

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
				<td>$l</td>
				<td>$m</td>
				<td>$n</td>
				<td>$o</td>
				<td>$p</td>
				<td>$q</td>
				<td>$r</td>
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

?>