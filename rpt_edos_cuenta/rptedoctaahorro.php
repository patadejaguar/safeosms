<?php
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
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";

$oficial = elusuario($iduser);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Estado de Cuenta por Credito</title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body>
<?php echo getRawHeader(); ?>
<span class="Estilo1">
<hr>
<p class="bigtitle">ESTADO DE CUENTA DE CAPTACION</p>
<hr>
<?php
$idcuenta = $_GET["cuenta"];
	if (!$idcuenta) {
		exit($msg_rpt_exit);
	}
	$sqlcred = "SELECT * FROM captacion_cuentas WHERE numero_cuenta=$idcuenta";
	$rscred = mysql_query($sqlcred);
	while($rwc = mysql_fetch_array($rscred)) {
	// datos generales del socio
	$idsocio = $rwc[5];	// Numero de Socio
	$sqlmysoc = "SELECT * FROM socios_general WHERE codigo=$idsocio";
	$rsmysoc = mysql_query($sqlmysoc);
	$mynom = getNombreSocio($idsocio);
	while($rwy = mysql_fetch_array($rsmysoc)) {
			echo "
			<table border='0'  >
				<tr>
					<td class='ths'>Clave de Persona</td><td>$rwy[0]</td>
					<td class='ths'>Nombre(s)</td><td>$rwy[1]</td>
				</tr>
				<tr>
					<td class='ths'>Apellido Paterno</td><td>$rwy[2]</td>
					<td class='ths'>Apellido Materno</td><td>$rwy[3]</td>
				</tr>
				<tr>
					<td class='ths'>R. F. C.</td><td>$rwy[4]</td>
					<td class='ths'>C. U. R. P.</td><td>$rwy[5]</td>
				</tr>
				<tr>
					<td class='ths'>Genero</td><td>$rwy[15]</td>
					<td class='ths'>Estado Civil</td><td>$rwy[14]</td>
				</tr>
			</table>
			<hr>";
		}
	// datos de la cuenta
	$tipocuenta = eltipo("captacion_cuentastipos", $rwc[4]);
	$tasa = $rwc[14] * 100;
	$sdoact = number_format($rwc[9], 2, '.', ',');
	echo "<table  >
		<tr>
		<td class='ths'>Numero de cuenta</td><td>$idcuenta</td>
		<td class='ths'>Modalidad de la Cuenta</td><td>$tipocuenta</td>
		</tr>
		<tr>
		<td class='ths'>Tasa Actual</td><td>$tasa %</td>
		<td class='ths'>Fecha de Apertura</td><td>$rwc[5]</td>
		</tr>
		<tr>
		<td class='ths'>Saldo Actual</td><td>$ $sdoact</td>
		<td class='ths'>Fecha de Ult. Afectacion</td><td>$rwc[6]</td>
		</tr>
		</table>
		<hr>";


	} // end while
	@mysql_free_result($rscred);

	$sqlmvto = "SELECT * FROM operaciones_mvtos WHERE docto_afectado=$idcuenta ORDER BY fecha_afectacion";
	$rsmvto = mysql_query($sqlmvto);
	echo "<table   border='0'><tr>
	<th scope='col'>Fecha Afectacion</th>
	<th scope='col'>Numero Recibo</th>
	<th scope='col'>Tipo Operacion</th>
	<th scope='col'>Afectacion</th>
	<th scope='col'>Valor Afectacion</th>
	<th scope='col'>Vencimiento</th>
	<th scope='col'>Estatus</th>
	<th scope='col'>Saldo Inicial</th>
	<th scope='col'>Saldo Final</th>
	<th scope='col'>Detalles</th>
	</tr>";
			$sdo_i = 0;
			$sdo_f = 0;
		while($ryx = mysql_fetch_array($rsmvto)) {
			$tipoop = eltipo("operaciones_tipos", $ryx[6]);
			$estatus = eltipo("operaciones_mvtosestatus", $ryx[12]);
			$montoc = number_format($ryx[7], 2, '.', ',');
			$sdo_i = $sdo_f;
			$sdo_f = $sdo_i + ($ryx[7] * $ryx["valor_afectacion"]);
			$sdofin = number_format($sdo_f, 2, '.', ',');
			$sdoin = number_format($sdo_i, 2, '.', ',');

			echo "<tr>
			<td>$ryx[2]</td>
			<td>$ryx[3]</td>
			<td>$tipoop</td>
			<td class='mny'>$montoc</td>
			<td class='numc'>$ryx[10]</td>
			<td>$ryx[11]</td>
			<td>$estatus</td>
			<td>$sdoin</td>
			<td>$sdofin</td>
			<td>$ryx[23]</td>
			</tr>";
		}
	echo "</table>";
	@mysql_free_result($rsrec);
	$pie_pagina;
?>
</body>
</html>