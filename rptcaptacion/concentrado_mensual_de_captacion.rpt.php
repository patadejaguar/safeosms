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
$xHP		= new cHPage("TR.REPORTE DE ", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
	
$estatus 		= parametro("estado", SYS_TODAS);
$frecuencia 	= parametro("periocidad", SYS_TODAS);
$producto 		= parametro("convenio", SYS_TODAS);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS);
$out 			= parametro("out", SYS_DEFAULT);
	
$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);
	
$xHP->init();

//ini_set("display_errors", "on");

$oficial = elusuario($iduser);
$out = $_GET["out"];
$TDcss = "";

if ($out != OUT_EXCEL){
	$TDcss = " class='mny' ";
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
}

$input 				= $_GET["out"];
$fecha_inicial 		= $_GET["on"];
$fecha_final 		= $_GET["off"];
$sucursal			= $_GET["f700"];
$anno_mes			= date("Y-m", strtotime($fecha_final)) ;
$dia_final			= date("t", strtotime($fecha_final)) ;
$fecha_inicial		= $anno_mes . "-01";
$fecha_final		= $anno_mes . "-" . $dia_final;

$BySucursal			= "";
if ( isset($sucursal) AND $sucursal != "todas" ){
	$BySucursal		= " AND (`operaciones_mvtos`.`sucursal` ='$sucursal') ";
}
if ($out == OUT_EXCEL){
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

	//Tipos de Conversion
	$xTip = new cTipos();

	
	//Obtener por consulta el SDPM
	
	
	//Obtener por consulta los Mvtos
$sqlM = "SELECT
	`operaciones_mvtos`.`docto_afectado` AS 'cuenta',
	`operaciones_mvtos`.`fecha_afectacion`,
	DATE_FORMAT(`operaciones_mvtos`.`fecha_afectacion`, '%d') AS 'dia',
	SUM(`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion` ) AS `monto`
FROM
	`operaciones_mvtos` `operaciones_mvtos` 
		INNER JOIN `eacp_config_bases_de_integracion_miembros` 
		`eacp_config_bases_de_integracion_miembros` 
		ON `operaciones_mvtos`.`tipo_operacion` = 
		`eacp_config_bases_de_integracion_miembros`.`miembro` 
WHERE
	(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 2602) AND
	(`operaciones_mvtos`.`fecha_afectacion` >='$fecha_inicial') 
	AND
	(`operaciones_mvtos`.`fecha_afectacion` <='$fecha_final') 
	$BySucursal
GROUP BY
	`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
	`operaciones_mvtos`.`docto_afectado`,
	`operaciones_mvtos`.`fecha_afectacion` 
ORDER BY
	`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
	`operaciones_mvtos`.`fecha_afectacion` ";
//Arrays de mvtos y otros tipos
$arrMvtos	= array();
$arrSums	= array();

$rs = mysql_query($sqlM, cnnGeneral());
	while($rw = mysql_fetch_array($rs)){
		$arrMvtos[ $rw["cuenta"] ][ $rw["dia"] ] = $xTip->cFloat( $rw["monto"], 2);
	}
	//Sql de las cuentas
	
	$sqlC = "SELECT
	`socios_general`.`codigo`,
	CONCAT(`socios_general`.`apellidopaterno`, ' ',
	`socios_general`.`apellidomaterno`, ' ',
	`socios_general`.`nombrecompleto`) AS 'nombre',
	`captacion_cuentas`.`numero_cuenta`,
	`captacion_cuentas`.`saldo_cuenta` 
FROM
	`captacion_cuentas` `captacion_cuentas` 
		INNER JOIN `socios_general` `socios_general` 
		ON `captacion_cuentas`.`numero_socio` = `socios_general`.`codigo`
WHERE
	(`captacion_cuentas`.`tipo_cuenta` =10)
	ORDER BY
		`socios_general`.`codigo`,
		`captacion_cuentas`.`numero_cuenta`,
		`captacion_cuentas`.`saldo_cuenta`	";

	//imprimir la tabla
	
	//Header
	$tdH		= "";
	$thB		= "";
	//sumas
	$sumVars	= 0;
	$sumSInit	= 0;
	$sumSFin	= 0;
	
	$tdH	.=  "<table width='100%' align='center' border='0'>
			<thead>
				<tr>
					<th>Socio</th>
					<th>Nombre</th>
					<th>Cuenta</th>
					";
		for ($i = 1; $i <= $dia_final; $i++){
			$tdH	.=  "<th>" . $i . "</th>";
			//inicializa el array de totales
			$arrSums[$i]	= 0;
		}
	$tdH	.=  "
				<th>Variacion</th>
				<th>Saldo Inicial</th>
				<th>Saldo Final</th></tr>
			</thead>
			<tbody> ";
	
	echo $tdH;
	
	//Mostrar las cuentas mediante un SQL
	$rs = mysql_query($sqlC, cnnGeneral() );
	while($rwC = mysql_fetch_array($rs) ){
		$socio		= $rwC["codigo"];
		$nombre		= $rwC["nombre"];
		$cuenta		= $rwC["numero_cuenta"];
		$saldo		= $xTip->cFloat($rwC["saldo_cuenta"], 2);
		$sdo_inicial= 0;
		$MvtosCta	= 0;
		
		$td			= "<tr>";
		$td			.= "<td>$socio</td>
						<td>$nombre</td>
						<td>$cuenta</td>";
		for ($i = 1; $i <= $dia_final; $i++){
			$monto			= $xTip->cFloat( $arrMvtos[$cuenta][$i], 2 );
			$td				.= "<td $TDcss>" . $monto . "</td>";
			$arrSums[$i]	+= $monto;
			$MvtosCta		+= $monto;
		}
		$sdo_inicial	= $xTip->cFloat( ($saldo - $MvtosCta), 2);
		$MvtosCta		= $xTip->cFloat($MvtosCta, 2);
		
		$sumSFin		+= $saldo;
		$sumSInit		+= $sdo_inicial;
		$sumVars		+= $MvtosCta;
		
		
		$td			.= "<td $TDcss>" . $MvtosCta  . "</td>
						<td $TDcss>" . $sdo_inicial . "</td>
						<td $TDcss>" . $saldo . "</td>
						</tr>";
		//Si la Cuenta Tiene Movimientos o el Saldo es diferente a cero
		if ($MvtosCta != 0 OR $saldo != 0 ){
			echo $td;
		}
	}
		$thB	.= "<th colspan='3' />";
	//Imprime las sumas de los totales
	for ($i = 1; $i <= $dia_final; $i++){
		$thB .= "<th $TDcss> " . $xTip->cFloat( $arrSums[ $i  ], 2 ) . "</th>";
	}
		$thB .= "<th $TDcss> " . $xTip->cFloat( $sumVars, 2 ) . "</th>";
		$thB .= "<th $TDcss> " . $xTip->cFloat( $sumSInit, 2 ) . "</th>";
		$thB .= "<th $TDcss> " . $xTip->cFloat( $sumSFin, 2 ) . "</th>";
	$thB .= "</tbody>
		</table>";
	echo $thB;
	
if ($out != OUT_EXCEL){

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