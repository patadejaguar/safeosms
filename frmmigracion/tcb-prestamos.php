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
		<th>Producto</th>
		<th>Sucursal</th>
		<th>fecha de ministracion</th>
		<th>fecha de vencimiento</th>
		<th>fecha de ultimo pago</th>
		<th>tasa normal</th>
		<th>tasa moratorio</th>
		<th>numero de pagos</th>
		<th>periocidad de pago</th>
		<th>unidad de medida</th>
		<th>importe prestado</th>
		<th>saldo actual</th>
		<th>importe de la proxima cuota</th>
		<th>cuenta eje</th>
		<th>cuenta de garantia liquida</th>
		<th>capital pagado</th>
		<th>interes normal pagado</th>
		<th>iva interes normal pagado</th>
		<th>interes moratorio pagado</th>
		<th>iva interes moratorio pagado</th>
		<th>multas pagadas</th>
		<th>iva multas pagadas</th>
		<th>comision apertura pagadas</th>
		<th>gastos de investigacion pagados</th>
		<th>monto de la garantia liquida</th>
	</tr>";
	$tr		= "";

$sql = "
SELECT
	*
FROM
	`creditos_solicitud` `creditos_solicitud`
WHERE saldo_actual > " . TOLERANCIA_SALDOS . "
	/* AND
	sucursal	= '" . getSucursal() . "' */
	AND
	estatus_actual != 50
/* LIMIT 0,100 */
";
$rs	= mysql_query($sql, cnnGeneral() );
while (  $rw = mysql_fetch_array($rs) ){
	$socio				= $rw["numero_socio"];
	$credito			= $rw["numero_solicitud"];
	$producto			= $rw["tipo_convenio"];
	$sucursal			= $rw["sucursal"];
	$fechamin			= $rw["fecha_ministracion"];
	$fechavenc			= $rw["fecha_vencimiento"];
	$tasa_normal			= $rw["tasa_interes"];
	$tasa_moratorio			= $rw["tasa_moratorio"];
	$pagos				= $rw["pagos_autorizados"];
	$periocidad			= $rw["periocidad_de_pago"];
	$monto				= $rw["monto_autorizado"];
	$saldo				= $rw["saldo_actual"];
	$fecha_ultimo_mvto		= $rw["fecha_ultimo_mvto"];
	$proxima_cuota			= 0;
	$cuenta_eje			= 0;
	$cuenta_g_liquida		= 0;
	$g_liquida_pagada		= 0;
	$capital_pagado			= $monto - $saldo;
	$interes_normal_pagado		= $rw["interes_normal_pagado"];
	$iva_pagado			= $interes_normal_pagado * 0.15;
	$interes_moratorio_pagado	= $rw["interes_moratorio_pagado"];
	$iva_interes_moratorio		= $interes_moratorio_pagado * 0.15;
	//datos obtenidos por codigo
	$monto_proxima_cuota		= 0;
	$multas				= 0;
	$iva_multas			= $multas * 0.15;
	$comisiones			= 0;
	$gastos				= 0;
	
	//datos de la garantia liquida, obtenidos mediante codigo
		$xCred			= new cCredito($credito, $socio);
		$xCred->init($rw);
		$g_liquida_pagada	= $xCred->getGarantiaLiquidaPagada();
		$cuenta_g_liquida	= $xCred->getCuentaGarantiaLiquida();
		$msg			= $xCred->getMessages();
	echo  "	<tr>
				<td>$socio</td>
				<td>$credito</td>
				<td>$producto</td>
				<td>$sucursal</td>
				<td>$fechamin</td>
				<td>$fechavenc</td>
				<td>$fecha_ultimo_mvto</td>
				<td>$tasa_normal</td>
				<td>$tasa_moratorio</td>
				<td>$pagos</td>
				<td>$periocidad</td>
				<td>D</td>
				<td>" . round($monto, 2) ."</td>
				<td>" . round($saldo, 2) ."</td>
				<td>" . round($monto_proxima_cuota, 2) ."</td>
				<td>$cuenta_eje</td>
				<td>$cuenta_g_liquida</td>
				<td>" . round($capital_pagado, 2) ."</td>
				<td>" . round($interes_normal_pagado, 2) ."</td>
				<td>" . round($iva_pagado, 2) ."</td>
				<td>" . round($interes_moratorio_pagado, 2) ."</td>
				<td>" . round($iva_interes_moratorio, 2) ."</td>
				<td>" . round($multas, 2) ."</td>
				<td>" . round($iva_multas, 2) ."</td>
				<td>" . round($comisiones, 2) ."</td>
				<td>" . round($gastos, 2) ."</td>
				<td>" . round($g_liquida_pagada, 2) ."</td>
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
