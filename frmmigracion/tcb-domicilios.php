<?php
/**
 * Reporte de
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
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.creditos.inc.php");


$oficial 	= elusuario($iduser);
ini_set("max_execution_time", 690);
$input 		= $_GET["out"];

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

$xDB = new cSAFEData();
///* `socios_vivienda`.`estado`, */
///* `socios_vivienda`.`municipio`, */
$sql = "
	SELECT DISTINCT SQL_CACHE
		`socios_vivienda`.`socio_numero`,
		`socios_vivienda`.`fecha_alta`,
                `socios_vivienda`.`telefono_residencial`,
		`socios_vivienda`.`calle`,
		`socios_vivienda`.`numero_exterior`,
		`socios_vivienda`.`colonia`,
                `socios_vivienda`.`municipio`,               	
		
		`socios_vivienda`.`codigo_postal`,
		(SELECT codigo_de_estado FROM
			general_colonias WHERE general_colonias.codigo_postal = `socios_vivienda`.`codigo_postal`
		LIMIT 0,1) AS 'estado_numerico',
		
		LEFT(`socios_vivienda`.`numero_interior`, 3) AS 'num_interior',
'CL' AS 'tipo_de_calle'
FROM
	`socios_vivienda` `socios_vivienda`
		INNER JOIN `socios_general` `socios_general`
		ON `socios_vivienda`.`socio_numero` = `socios_general`.`codigo`
WHERE
	(`socios_general`.`estatusactual` !=20)
GROUP BY
`socios_vivienda`.`socio_numero`
	ORDER BY
	principal ASC

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
	window.print();
}
</script>
</html>
<?php
}
?>
