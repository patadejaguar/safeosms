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


$oficial 	= elusuario($iduser);
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
			<th colspan="3">REPORTE DE MIGRACION DE PERSONAS MORALES - TCB</th>
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

	$tr		= "";
//TODO: validar por sucursal
$sql = "
	SELECT
		*
	FROM
		`socios_general` `socios_general`
	WHERE
			/* `socios_general`.`sucursal`	= '" . getSucursal() . "'
			AND */
			(`socios_general`.`personalidad_juridica` = 2
			OR
			`socios_general`.`personalidad_juridica` = 5)
			AND
			(`socios_general`.`estatusactual` != 20)
	ORDER BY
		`socios_general`.`fechaalta` DESC
";
$rs	= mysql_query($sql, cnnGeneral() );
while (  $rw = mysql_fetch_array($rs) ){
	$socio			= $rw["codigo"];
	$empresa		= $rw["nombrecompleto"];
	$sucursal		= $rw["sucursal"];
	$fecha_de_alta		= $rw["fechaalta"];
	$fecha_constitucion	= $rw["fechanacimiento"];
	$rfc			= $rw["rfc"];
	$lugar_constitucion	= 412; //$rw["lugarnacimiento"];
	//Obtener la informaci�n laboral
	$tr	.= "	<tr>
				<td>$socio</td>
				<td>$empresa</td>
				<td>$sucursal</td>
				<td>$fecha_de_alta</td>
				<td>2</td>
				<td>0</td>
				<td>$fecha_constitucion</td>
				<td>$lugar_constitucion</td>
				<td>$rfc</td>
			</tr>";
}
echo "<table width='100%' >
	<tr>
		<th>Socio</th>
		<th>Empresa</th>
		<th>Sucursal</th>
		<th>Fecha de Alta</th>
		<th>Sector</th>
		<th>Capital Social</th>
		<th>Fecha de Constitucion</th>
		<th>Lugar de Constitucion</th>
		<th>R.F.C.</th>
	</tr>
		$tr
</table>";


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
