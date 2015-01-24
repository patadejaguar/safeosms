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
include_once("../core/core.creditos.inc.php");

$oficial = elusuario($iduser);

$output					= $_GET["out"];

$idsolicitud 			= $_GET["pb"];		//Numero de Solicitud
$id 					= $_GET["pa"];		//Numero de Socio
$f15 					= $_GET["f15"];
$f14 					= $_GET["f14"];
$f16 					= $_GET["f16"];
$f18 					= $_GET["f18"];		//Mostrar Movimiento Especifico
$TOperacion 			= $_GET["f19"];		//Codigo de Tipo de Operacion.- Mvto Especifico

$fecha_inicial 			= $_GET["on"];
$fecha_final 			= $_GET["off"];


if ( !isset($output) ){
	$output = "default";
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
			<th colspan="3">REPORTE DE EVOLUCION DE ESTATUS EN CREDITOS</th>
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


$cCred	= new cCredito($idsolicitud);
$cCred->init();
//TODO: Modificar
echo $cCred->getFicha();

$sql = "
SELECT
	`operaciones_mvtos`.`recibo_afectado`       AS `recibo`,
	`operaciones_mvtos`.`fecha_afectacion`      AS `fecha`,
	`operaciones_mvtos`.`tipo_operacion`        AS `operacion`,
	`operaciones_tipos`.`descripcion_operacion` AS `descripcion`,
	`operaciones_mvtos`.`afectacion_real`       AS `monto`,
	`eacp_config_bases_de_integracion_miembros`.`afectacion`
FROM
	`operaciones_mvtos` `operaciones_mvtos`
		INNER JOIN `operaciones_tipos` `operaciones_tipos`
		ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
		`idoperaciones_tipos`
			INNER JOIN `eacp_config_bases_de_integracion_miembros`
			`eacp_config_bases_de_integracion_miembros`
			ON `eacp_config_bases_de_integracion_miembros`.`miembro` =
			`operaciones_mvtos`.`tipo_operacion`
WHERE
									(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =1111)
									AND
									(`operaciones_mvtos`.`docto_afectado` = $idsolicitud )
								GROUP BY
									`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
									`operaciones_mvtos`.`socio_afectado`,
									`operaciones_mvtos`.`docto_afectado`,
									`operaciones_mvtos`.`fecha_operacion`
								ORDER BY
									`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
									`operaciones_mvtos`.`socio_afectado`,
									`operaciones_mvtos`.`fecha_operacion`
";

	$cTbl = new cTabla($sql);
	$cTbl->setWidth();
	$cTbl->Show("", false);


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