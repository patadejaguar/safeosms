<?php
/**
 * Reporte de Calendario de Pagos de Creditos (Tabular)
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package creditos
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


$oficial = elusuario($iduser);


$fecha_inicial			= $_GET["on"];
$fecha_final			= $_GET["off"];

$dias					= restarfechas($fecha_final, $fecha_inicial);
if ( $dias > 31) {
	echo JS_CLOSE;
}

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
			<th colspan="3">Calendario de Pagos de Creditos (Tabular)</th>
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
	`operaciones_mvtos`.`fecha_afectacion`,
	`operaciones_mvtos`.`socio_afectado`,
	`operaciones_mvtos`.`docto_afectado`,
	SUM(`operaciones_mvtos`.`afectacion_real` *
	`eacp_config_bases_de_integracion_miembros`.`afectacion`) AS 'monto'

FROM
	`operaciones_mvtos` `operaciones_mvtos`
		INNER JOIN `eacp_config_bases_de_integracion_miembros`
		`eacp_config_bases_de_integracion_miembros`
		ON `operaciones_mvtos`.`tipo_operacion` =
		`eacp_config_bases_de_integracion_miembros`.`miembro`
WHERE
	(`operaciones_mvtos`.`fecha_afectacion` >='')
	AND
	(`operaciones_mvtos`.`fecha_afectacion` <='')
	AND
	(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 2601)
	GROUP BY
		`operaciones_mvtos`.`fecha_afectacion`,
		`operaciones_mvtos`.`socio_afectado`,
		`operaciones_mvtos`.`docto_afectado`,
		`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`
	ORDER BY
		`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
		`operaciones_mvtos`.`fecha_afectacion`,
		`operaciones_mvtos`.`socio_afectado`";


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