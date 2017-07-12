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
include_once("../core/go.login.inc.php");
include_once("../core/core.error.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.init.inc.php");
include_once("../core/core.db.inc.php");
$theFile			= __FILE__;
$permiso			= getSIPAKALPermissions($theFile);
if($permiso === false){	header ("location:../404.php?i=999");	}
$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("TR.REPORTE DE ", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();

/*$estatus 		= parametro("estado", SYS_TODAS);
$frecuencia 	= parametro("periocidad", SYS_TODAS);
$producto 		= parametro("convenio", SYS_TODAS);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS);
*/
$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$out 			= parametro("out", SYS_DEFAULT);
$porcredito		= parametro("credito", false, MQL_BOOL);

$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);




$lista_por_gen	= "SELECT
	`socios_genero`.`idsocios_genero`      AS `genero`,
	`socios_genero`.`descripcion_genero`   AS `descripcion`,
	`general_sucursales`.`nombre_sucursal` AS `sucursal`,
	COUNT(`socios_general`.`codigo`)       AS `personas` 
FROM
	`socios_general` `socios_general` 
		INNER JOIN `socios_genero` `socios_genero` 
		ON `socios_general`.`genero` = `socios_genero`.`idsocios_genero` 
			INNER JOIN `general_sucursales` `general_sucursales` 
			ON `general_sucursales`.`codigo_sucursal` = `socios_general`.
			`sucursal` 
		GROUP BY
			`socios_genero`.`idsocios_genero`,
			`general_sucursales`.`codigo_sucursal` ";

if($porcredito == true){
	$lista_por_gen	= "SELECT
		`socios_genero`.`idsocios_genero`            AS `genero`,
		`socios_genero`.`descripcion_genero`         AS `descripcion`,
		`general_sucursales`.`nombre_sucursal`       AS `sucursal`,
		COUNT(`socios_general`.`codigo`)             AS `personas`,
		SUM(`creditos_solicitud`.`monto_solicitado`) AS `solicitado`,
		SUM(`creditos_solicitud`.`monto_autorizado`) AS `ministrado`,
		SUM(`creditos_saldo_mensuales`.`septiembre`) AS `insoluto` 
	FROM
	`creditos_solicitud` `creditos_solicitud` 
		INNER JOIN `socios_general` `socios_general` 
		ON `creditos_solicitud`.`numero_socio` = `socios_general`.`codigo` 
			INNER JOIN `creditos_saldo_mensuales` `creditos_saldo_mensuales` 
			ON `creditos_solicitud`.`numero_solicitud` = 
			`creditos_saldo_mensuales`.`numero_solicitud` 
				INNER JOIN `socios_genero` `socios_genero` 
				ON `socios_general`.`genero` = `socios_genero`.`idsocios_genero` 
					INNER JOIN `general_sucursales` `general_sucursales` 
					ON `general_sucursales`.`codigo_sucursal` = `socios_general`
					.`sucursal` 
				GROUP BY
					`socios_genero`.`idsocios_genero`,
					`general_sucursales`.`codigo_sucursal` ";
}

$sql			= $lista_por_gen;
$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
$xT		= new cTabla($sql, 2);

$xT->setFootSum(array(
	3 =>"personas",
	4 =>"solicitado",
	5 =>"ministrado",
	6 =>"insoluto"
));

$xT->setTipoSalida($out);

$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);
$xRPT->addContent($body);

//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");

$xRPT->addContent( $xT->Show( $xHP->getTitle() ) );
//============ Agregar HTML

//$xRPT->addContent( $xHP->init($jsEvent) );
//$xRPT->addContent( $xHP->end() );

$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);

exit;
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
include_once("../libs/open_flash_chart_object.php");

$oficial = elusuario($iduser);

$fecha_inicial 		= $_GET["on"];
$fecha_final 		= $_GET["off"];
$si_es_por_fecha 	= "";
$Mvto 				= $_GET["f3"];
$Stat 				= $_GET["f2"];
$BySuc				= "";
$mSuc				= $_GET["s"];
if($mSuc!="todas"){	$BySuc			= " AND (`socios_general`.`sucursal` ='$mSuc') "; }
$ByStat				= "";

$inputG 			= $_GET["outg"];

if ($Stat != "todas"){

}

if (isset($fecha_inicial) && isset($fecha_final) ){
	$si_es_por_fecha = " AND fechaalta >='$fecha_inicial' AND fechaalta <='$fecha_final' ";
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
			<th colspan="3">REPORTE DE ACUMULADO DE PERSONAS ACTIVAS POR GENERO</th>
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
			<td>Sucursal</td>
			<td><?php echo $mSuc ; ?></td>
		</tr>		
		<tr>
			<td>&nbsp;</td>
			<td>Fecha Inicial:</td>
			<td><?php echo fecha_corta($fecha_inicial) ; ?></td>
		</tr>	
								<tr>
			<td>&nbsp;</td>
			<td>Fecha Final</td>
			<td><?php echo fecha_corta($fecha_final) ; ?></td>
		</tr>			
	</thead>
</table>
<?php
$sql  = "SELECT
	COUNT(`socios_general`.`codigo`) AS 'numero',
	`socios_genero`.`descripcion_genero` AS  'genero'

FROM
	`socios_general` `socios_general` 
		INNER JOIN `socios_genero` `socios_genero` 
		ON `socios_general`.`genero` = `socios_genero`.`idsocios_genero` 
WHERE
	(`socios_general`.`estatusactual` != 20) 
	$BySuc
	$si_es_por_fecha
GROUP BY
	`socios_general`.`genero`
	
";
//exiT($x);

	$rs 	= 	mysql_query($sql, cnnGeneral());
	$lbl	= array();
	$val	= array();

	$tds 	= "";

	while ($rw = mysql_fetch_array($rs)){
		$val[] = $rw["numero"];
		$lbl[] = $rw["genero"];

	}

	$x = new SAFEChart();
	$x->setValues($val);
	$x->setLabels($lbl);
	$x->setTitle("ACUMULADO DE PERSONAS ACTIVAS POR GENERO");
	$mFile	=  $x->ChartPIE();

open_flash_chart_object( 600, 300, $mFile, true, "../" );

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