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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../core/core.config.inc.php");
//include_once("../libs/host.inc.php");

$oficial			= elusuario($iduser);



$input 				= $_GET["out"];
$fecha_inicial 		= $_GET["on"];
$fecha_final 		= $_GET["off"];
$sucursal			= $_GET["f700"];
$exportTo			= $_GET["out"];

$BySucursal			= "";
if ( isset($sucursal) and $sucursal != "todos" ){
	$BySucursal		= " AND sucursal = '$sucursal' ";
}


if ( !isset($exportTo) ){
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

}
if ($exportTo == OUT_EXCEL){
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
?>


<table    >
	<thead>
		<tr>
			<th colspan="3">REPORTE SIMPLE DE SOCIOS ORIENTADO AL GRUPO</th>
		</tr>

		<tr>
			<td width="60%">&nbsp;</td>
			<th width="20%">Fecha de Elaboracion:</th>
			<th width="20%"><?php echo fecha_larga(); ?></th>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<th>Preparado por:</th>
			<th><?php echo $oficial; ?></th>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td></td>
			<td></td>
		</tr>

	</thead>
</table>
<?php


$fechaop	= fechasys();
$msg		= "============================  LOG DE GENERACION DE REPORTE =========================\r\n";
$msg		.= "============================  GENERADO POR $oficial \r\n";
$msg		.= "============================  FECHA " . date("Y-m-d H:s:i") . " \r\n";
$sucursal	=
	$aliasFils	= "$sucursal--$fechaop";
	//Elimina el Archivo
	//unlink(PATH_TMP . $aliasFils . ".txt");
	//Abre Otro, lo crea si no existe
	//$URIFil		= fopen(PATH_TMP . $aliasFils . ".txt", "a+");
	//saveError(10,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "EL Usuario $oficial Utilizo la Utileria $command, Params $id|$id2|$id3");

	//Construir Solicitud
	$arrCreds		= array();
	$sqlC = "SELECT
	`creditos_solicitud`.`numero_socio`,
	`creditos_solicitud`.`numero_solicitud`,
	`creditos_solicitud`.`monto_autorizado`
		FROM
	`creditos_solicitud` `creditos_solicitud`";
	$rsC 			= mysql_query($sqlC, cnnGeneral() );
	while ($rwc = mysql_fetch_array($rsC)){
		$arrCreds[$rwc["numero_socio"]]["credito"]		= $rwc["numero_solicitud"];
		$arrCreds[$rwc["numero_socio"]]["monto"]		= $rwc["monto_autorizado"];
	}
	//Construir Cuentas
	$arrCaps		= array();
	$sqlA	="SELECT numero_socio,  MAX(saldo_cuenta) AS 'saldo'
				FROM captacion_cuentas
			GROUP BY numero_socio";

	$rsA			= mysql_query($sqlA, cnnGeneral());
	while ($rwa = mysql_fetch_array($rsA)){
		$arrCaps[ $rwa["numero_socio"] ]["saldo"]	= $rwa["saldo"];
	}
	//fecha
	$sql = "SELECT
	`socios_general`.`codigo`,
	`socios_general`.`nombrecompleto`  AS `nombre`,
	`socios_general`.`apellidopaterno` AS `apellido_paterno`,
	`socios_general`.`apellidomaterno` AS `apellido_materno`,
	`socios_general`.`fechaalta`       AS `fecha_de_alta`
FROM
	`socios_general` `socios_general`
WHERE
	(`socios_general`.`fechaalta` >='$fecha_inicial')
	AND
	(`socios_general`.`fechaalta` <='$fecha_final')
ORDER BY
	`socios_general`.`fechaalta`";

		$rsT	= mysql_query($sql, cnnGeneral());

		while($rw = mysql_fetch_array( $rsT ) ){
			$socio		= $rw["codigo"];
			$fecha		= date( "d/m/y", strtotime($rw["fecha_de_alta"]) );
			//Seleccionar la cuenta de captacion
			$numero_cuenta		= "";
			$saldo_cuenta		= 0;
			$fecha_cuenta		= "";
			$saldo_carga		= $arrCaps[$socio]["saldo"];

			$credito			= $arrCreds[$socio]["credito"];
			$credito_monto		= $arrCreds[$socio]["monto"];

			if ( !isset($credito) ){
				$credito		= "";
				$credito_monto	= 0;
			}

			if ( isset( $saldo_carga ) ){
				$sqlSQ = "SELECT numero_socio, fecha_apertura, numero_cuenta, saldo_cuenta
							FROM captacion_cuentas
						WHERE
						(numero_socio= $socio)
							AND
						(saldo_cuenta = $saldo_carga)";
						$xD = obten_filas($sqlSQ);
						if ( isset( $xD["numero_socio"] ) ){
							$saldo_cuenta	= $xD["saldo_cuenta"];
							$numero_cuenta	= $xD["numero_cuenta"];
							$fecha_cuenta	= $xD["fecha_apertura"];
						}
			}

			$tdBody .= "<tr>
						<td>" . $fecha . "</td>
						<td>" . $socio . "</td>
						<td>" . $rw["apellido_paterno"] . "</td>
						<td>" . $rw["apellido_materno"] . "</td>
						<td>" . $rw["nombre"] . "</td>
						<td>$fecha_cuenta</td>
						<td>$numero_cuenta</td>
						<td>$saldo_cuenta</td>
						<td>$credito</td>
						<td>$credito_monto</td>
						</tr>";
		}

		echo "
			<table width='100%' align='center' >
			</thead>
			<tr>
				<th>Fecha de <br />Ingreso</th>
				<th>Clave de Persona</th>
				<th>Apellido <br />Paterno</th>
				<th>Apellido <br />Materno</th>
				<th>Nombre(s)</th>
				<th>Fecha de Apertura <br /> de su Cuenta de Ahorro</th>
				<th>Numero de Cuenta <br />de Ahorro</th>
				<th>Saldo Actual <br />de la Cuenta <br />de ahorro</th>
				<th>Numero de Credito</th>
				<th>Monto Ministrado</th>
			</tr>
			</thead>
			<tbody>
$tdBody
			</tbody>
			</table>
			";

if ( !isset($exportTo) ){
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