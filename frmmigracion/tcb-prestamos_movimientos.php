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


$oficial 		= elusuario($iduser);
$input 			= $_GET["out"];


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
			<th colspan="3">REPORTE DE MIGRACION DE PRESTAMOS - TCB</th>
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

echo "<table width='100%' >
	<tr>
		<th>Socio</th>
		<th>Credito</th>
		<th>Numero de Recibo</th>
		<th>Fecha de Amortizacion</th>
		<th>Capital a Pagar</th>
		<th>Interes a Pagar</th>
		<th>IVA por pagar</th>
		<th>Capital Pagado</th>
		<th>Interes Pagado</th>
		<th>IVA de Pagos</th>
		<th>Interes Moratorio</th>
		<th>IVA de Moratorios</th>
		<th>Comisiones</th>
		<th>IVA de Comisiones</th>
	</tr>";
	$tr		= "";

$sql = "
SELECT numero_de_cliente, numero_de_credito, numero_de_pago, fecha_de_amortizacion,
    capital_a_pagar, interes_a_pagar, iva_por_el_interes_a_pagar, capital_pagado, interes_pagado,
    iva_pagado, interes_moratorio, iva_interes_moratorio, comisiones, iva_comisiones, indice
    FROM tcb_prestamos_movimientos
";
$rs	= mysql_query($sql, cnnGeneral() );
while (  $rw = mysql_fetch_array($rs) ){
	$socio				= $rw["numero_de_cliente"];
	$credito			= $rw["numero_de_credito"];
	$recibo				= $rw["numero_de_pago"];
	$fecha				= $rw["fecha_de_amortizacion"];
	$PagarCapital		= $rw["capital_a_pagar"];
	$PagarInteres		= $rw["interes_a_pagar"];
	$PagarIVA			= $rw["iva_por_el_interes_a_pagar"];
	$PagadoCapital		= $rw["capital_pagado"];
	$PagadoInteres		= $rw["interes_pagado"];
	$PagadoIVA			= $rw["iva_pagado"];
	$moratorios			= $rw["interes_moratorio"];
	$moratoriosIVA		= $rw["iva_interes_moratorio"];
	$comisiones			= $rw["comisiones"];
	$comisionesIVA		= $rw["iva_comisiones"];
	echo  "	<tr>
				<td>$socio</td>
				<td>$credito</td>
				<td>$recibo</td>
				<td>$fecha</td>
				<td>$PagarCapital</td>
				<td>$PagarInteres</td>
				<td>$PagarIVA</td>
				<td>$PagadoCapital</td>
				<td>$PagadoInteres</td>
				<td>$PagadoIVA</td>
				<td>$moratorios</td>
				<td>$moratoriosIVA</td>
				<td>$comisiones</td>
				<td>$comisionesIVA</td>
			</tr>";
}
echo "</table>";


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
