<?php
/**
 * Reporte de
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package seguimiento
 * @subpackage reports
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
include_once "../libs/open_flash_chart_object.php";

$oficial = elusuario($iduser);
$fecha_inicial 		= $_GET["on"];
$fecha_final 		= $_GET["off"];
$input 				= $_GET["out"];

	if (!$input) {
		$input = "default";
	}

$logType 		= $_GET["f1"];
$siType 		= $_GET["f71"];
$errType		= $_GET["codigo"];
$cUsuario		= $_GET["usuario"];


$ByNivel 		= "";

$ByCodigo		= "";
$ByUsuario		= "";

if( (isset($logType) ) and ($logType != "todas") ){
	$ByNivel = " AND (`general_error_codigos`.`type_err`='$logType') ";
}
//Codigo de error
if((isset($errType)) and ($errType != "todas") ){
	$ByCodigo ="  AND `general_log`.`type_error`='$errType' ";
}

if((isset($cUsuario)) and ($cUsuario != "todas") ){
	$ByUsuario ="  AND `general_log`.`usr_log`='$cUsuario' ";
}

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
echo getRawHeader();
?>
<!-- -->
<table       >
	<thead>
		<tr>
			<th colspan="3">REPORTE DE</th>
		</tr>
<!-- DATOS GENERALES DEL REPORTE  -->
		<tr>
			<td  >&nbsp;</td>
			<td width="20%">Fecha de Elaboracion:</td>
			<td width="30%"><?php echo fecha_larga(); ?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>Preparado por:</td>
			<td><?php echo $oficial; ?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td></td>
			<td></td>
		</tr>

	</thead>
</table>
<?php


$sql = "SELECT
	`general_log`.`type_error`	AS `tipo`,
	`general_error_codigos`.`description_error` AS `descripcion`,
	getUserByID(`general_log`.`usr_log`) AS `usuario`,
	COUNT(`general_log`.`idgeneral_log`) AS `errores`

FROM
	`general_log` `general_log`
		INNER JOIN `general_error_codigos` `general_error_codigos`
		ON `general_log`.`type_error` = `general_error_codigos`.
		`idgeneral_error_codigos`

WHERE

	(`general_log`.`fecha_log` >='$fecha_inicial')
AND
	(`general_log`.`fecha_log` <='$fecha_final')

	$ByNivel
	$ByCodigo
	$ByUsuario
	GROUP BY
		`general_log`.`type_error`,
		`general_log`.`usr_log`


HAVING /*errores > 20
	AND*/
	usuario != \"_NO_REGISTRADO_\"

	";



	$rs 	= mysql_query($sql, cnnGeneral());
	$lbl	= array();
	$val	= array();

	$tds 	= "";

	while ($rw = mysql_fetch_array($rs)){
		$val[] = $rw["usuario"];
		$lbl[] = $rw["errores"];

		$tds .= "
			<tr>
				<td>" . $rw["usuario"] . "</td>
				<td class ='mny'>" . $rw["errores"] . "</td>
			</tr>
		";

	}
	//
	echo "<table width='100%'>
			$tds
			</table> ";
	//
	/*$x = new SAFEChart();
	$x->setValues($val);
	$x->setLabels($lbl);
	$x->setTitle("Acumulado de Incidencias de error");
	$mFile	= $x->ChartPIE();



	open_flash_chart_object( 512, 512, $mFile, true, "../" );
*/


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