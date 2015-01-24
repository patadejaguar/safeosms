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
		<th>MONTO</th>
		<th>MINISTRACION</th>
		<th>TASA INTERES</th>
		<th>TASA MORATORIA</th>
		<th>FACTOR MORA</th>
		<th>PLAZO</th>
		<th>PERIODO</th>
		<th>PERIOCIDAD</th>
		<th>TIPO DE PLAN DE PAGO</th>
		<th>GRAVA IVA</th>
		<th>FUENTE</th>
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
//Arrays de Importacion
$arrConvPeriocidad	= array (
	"7" => "1",
	"15" => "2",
	"360" => "9",
	"30" => "3",
	"60" => "4",
	"90" => "6",
	"120" => "7",
	"180" => "8",
	"14" => "5"
);

$sql	= "SELECT
	`creditos_solicitud`.`numero_socio`,
	`creditos_solicitud`.`numero_solicitud`,
	`creditos_tipoconvenio`.`descripcion_tipoconvenio`,
	`creditos_solicitud`.`monto_autorizado`,
	`creditos_solicitud`.`fecha_ministracion`,
	`creditos_solicitud`.`tasa_interes`,
	`creditos_solicitud`.`tasa_moratorio`,
	`creditos_solicitud`.`dias_autorizados`,
	`creditos_solicitud`.`periocidad_de_pago`,
	`creditos_solicitud`.`tipo_de_calculo_de_interes`,
	`creditos_tipoconvenio`.`tasa_iva`,
	`creditos_solicitud`.`saldo_actual`
FROM
	`creditos_solicitud` `creditos_solicitud`
		INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio`
		ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
		`idcreditos_tipoconvenio`
WHERE
	(`creditos_solicitud`.`saldo_actual` >0) ";
$rs = mysql_query($sql, cnnGeneral() );

echo "<table>
	$tblHD";

while($rw = mysql_fetch_array($rs) ){
	$a		= $rw[ "numero_socio" ];
	$b		= $rw[ "numero_solicitud" ];
	$c		= $rw[ "descripcion_tipoconvenio" ];
	$d		= $rw[ "monto_autorizado" ];
	$e		= $rw[ "fecha_ministracion" ];
	$f		= $rw[ "tasa_interes" ] * 100;
    $g		= $rw[ "tasa_moratorio" ] * 100;
    $h		= 0;
    $i      = $rw[ "dias_autorizados" ];
    $j      = "D";
    $k      = $arrConvPeriocidad[ $rw[ "periocidad_de_pago" ] ];

    $l      = $rw[ "tipo_de_calculo_de_interes" ];
    if ( $rw[ "periocidad_de_pago" ] == 360 ){
    	$k	= 5;
    }

	$m		= "S";
	$n		= 1;
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