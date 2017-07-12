<?php
/**
 * Reporte de migracion de cuentas a la vista
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package migracion
 * @subpackage tcb
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


$oficial 	= elusuario($iduser);
$input 		= $_GET["out"];

ini_set("max_execution_time", 240);

if ($input!=OUT_EXCEL) {
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
			<th colspan="3">REPORTE DE MIGRACION DE CUENTAS A LA VISTA</th>
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
$sql = "
SELECT SQL_CACHE
	`captacion_cuentas`.`numero_socio`     AS `numero_de_socio`,
	`captacion_cuentas`.`numero_cuenta`    AS `numero_de_cuenta`,
	`captacion_subproductos`.`descripcion_subproductos` AS `producto`,
	`captacion_cuentas`.`sucursal`,
	`captacion_cuentas`.`fecha_apertura`   AS `fecha_de_apertura`,
	`captacion_cuentas`.`saldo_cuenta`     AS `saldo`
FROM
	`captacion_cuentas` `captacion_cuentas` 
		INNER JOIN `captacion_subproductos` `captacion_subproductos` 
		ON `captacion_cuentas`.`tipo_subproducto` = `captacion_subproductos`.
		`idcaptacion_subproductos`  
WHERE
	(`captacion_cuentas`.`tipo_cuenta` =10)
	AND
	(`captacion_cuentas`.`saldo_cuenta` >0)
";
$cTbl = new cTabla($sql);
$cTbl->setWidth();
$cTbl->Show("", false);

if ($input!=OUT_EXCEL) {
echo getRawFooter();
?>
</body>
<script  >
<?php

?>
function initComponents(){
	//window.print();
}
</script>
</html>
<?php
}
?>
