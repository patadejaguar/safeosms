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


$NombreOficial		= elusuario($iduser);


$estatus 			= $_GET["f2"];
$frecuencia 		= $_GET["f1"];
$convenio 			= $_GET["f3"];
$tipo_pago			= $_GET["f7"];

$oficial 			= $_GET["f700"];

$fecha_inicial		= $_GET["on"];
$fecha_final		= $_GET["off"];

$ByTipoPago			= "";
$ByOperacion		= "";
$es_por_estatus 	= "";
$es_por_frecuencia 	= "";
$es_por_convenio 	= "";
$es_por_oficial		= "";

if($estatus != "todas"){
	$es_por_estatus		= " AND creditos_solicitud.estatus_actual=$estatus ";
}
//
if($frecuencia != "todas"){

	$es_por_frecuencia 	= " AND creditos_solicitud.periocidad_de_pago =$frecuencia ";
}
//
if($convenio != "todas"){
	$es_por_convenio 	= " AND creditos_solicitud.tipo_convenio = $convenio ";

}

if ($oficial != "todas"){
	$es_por_oficial	= " AND creditos_solicitud.oficial_credito = $oficial ";
}

if ( $tipo_pago != "todas"){
	$ByTipoPago	= "AND
			(`operaciones_recibos`.`tipo_pago` ='$tipo_pago') ";
}
$input = $_GET["out"];

$sql = "SELECT

			`oficiales`.`id`                                 	AS `oficial`,
			`oficiales`.`nombre_completo`                    	AS `nombre`,
			
			`creditos_estatus`.`descripcion_estatus` 			AS `estatus`,
			
			COUNT(`operaciones_mvtos`.`idoperaciones_mvtos`) 	AS `operaciones`,
			`operaciones_recibos`.`tipo_pago`                	AS `tipo_de_pago`,
			`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
			SUM(`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`)       AS `monto` 
		FROM
	`operaciones_mvtos` `operaciones_mvtos` 
		LEFT OUTER JOIN `operaciones_recibos` `operaciones_recibos` 
		ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.
		`idoperaciones_recibos` 
			INNER JOIN `creditos_solicitud` `creditos_solicitud` 
			ON `operaciones_mvtos`.`docto_afectado` = `creditos_solicitud`.
			`numero_solicitud` 
				INNER JOIN `oficiales` `oficiales` 
				ON `creditos_solicitud`.`oficial_credito` = `oficiales`.`id` 
					INNER JOIN `creditos_estatus` `creditos_estatus` 
					ON `creditos_solicitud`.`estatus_actual` = 
					`creditos_estatus`.`idcreditos_estatus` 
						INNER JOIN `eacp_config_bases_de_integracion_miembros` 
						`eacp_config_bases_de_integracion_miembros` 
						ON `operaciones_mvtos`.`tipo_operacion` = 
						`eacp_config_bases_de_integracion_miembros`.`miembro`
		WHERE
			(
				(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =2002)
				OR
				(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =2003)
			)
			AND
			(`operaciones_mvtos`.`fecha_operacion` >='$fecha_inicial') 
			AND
			(`operaciones_mvtos`.`fecha_operacion` <='$fecha_final')
			
			$es_por_convenio
			$es_por_estatus
			$es_por_frecuencia
			$es_por_oficial
			$ByTipoPago
			
		GROUP BY
			`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
			`creditos_solicitud`.`estatus_actual`,
			`creditos_solicitud`.`oficial_credito`,
			`operaciones_recibos`.`tipo_pago`
			
			/*`creditos_solicitud`.`periocidad_de_pago`,*/
			/*`creditos_solicitud`.`tipo_convenio`,*/
			
			
		ORDER BY
			`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
			/*`operaciones_mvtos`.`idoperaciones_mvtos`,*/
			`creditos_solicitud`.`oficial_credito`,
			`creditos_solicitud`.`estatus_actual`,
			`operaciones_recibos`.`tipo_pago`";
			
			
$tds		= "";

$TMvtos		= 0;
$SCap		= 0;
$SInt		= 0;

$arrNBases	= array (2002 => "INTERES", 2003 => "CAPITAL");

//<td>" . $arrNBases[ $rw["codigo_de_base"] ]  . " " . $rw["estatus"] . "</td>
	$rs = mysql_query($sql, cnnGeneral() );
		while( $rw = mysql_fetch_array($rs) ){
			$tds	.= "<tr>
							<td>" . $rw["oficial"] . "</td>
							<td>" . $rw["nombre"] . "</td>
							<td>" . $rw["estatus"] . "</td>
							<td>" . $rw["tipo_de_pago"] . "</td>
							<td>" . $rw["operaciones"] . "</td>
						";
				$TMvtos		+= $rw["operaciones"];
			if ( $rw["codigo_de_base"] == 2002){
				$tds	.= "<td class='mny'></td>
							<td class='mny'>" . getFMoney($rw["monto"]) . "</td>
							</tr>";
					$SInt	+= $rw["monto"];
			} else {
				$tds	.= "<td class='mny'>" . getFMoney($rw["monto"]) . "</td>
							<td class='mny'></td>
							</tr>";
					$SCap	+= $rw["monto"];
			}
		}
		
$t =  "<table width='100%'>
				<thead>
					<th>Numero de Oficial</th>
					<th>Nombre</th>
					<th>Estatus del Credito</th>
					<th>Forma de Pago</th>
					<th>Operaciones</th>
					<th>Capital Recuperado</th>
					<th>Interes Recuperado</th>
				</thead>
				<tbody>
					$tds
				</tbody>
				<tfoot>
					<td />
					<td />
					<td />
					<th>TOTALES</th>
					<th class='mny'>$TMvtos</th>
					<th class='mny'>" . getFMoney($SCap) . "</th>
					<th class='mny'>" . getFMoney($SInt) . "</th>
				</tfoot>
			</table>";
			
if ( $input != OUT_EXCEL ){
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<link href="<?php echo CSS_REPORT_FILE; ?>" rel="stylesheet" type="text/css">
<link href="../css/flags.css" rel="stylesheet" type="text/css">
<body onLoad="initComponents();">
<?php
echo getRawHeader();
?>
<!-- -->
<table       >
	<thead>
		<tr>
			<th colspan="3">REPORTE DE BENCHMARK DE INGRESOS POR OFICIAL DE CREDITO</th>
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
			<td>Fecha de Inicio del Reporte:</td>
			<td><?php echo $fecha_inicial ; ?></td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td>Fecha de Final del Reporte</td>
			<td><?php echo $fecha_final; ?></td>
		</tr>
	
		<tr>
			<td>&nbsp;</td>
			<td>Estatus de los Creditos </td>
			<td><?php echo $estatus; ?></td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td>Frecuencia de Pago (Periocidad)</td>
			<td><?php echo $frecuencia; ?></td>
		</tr>
	
		<tr>
			<td>&nbsp;</td>
			<td>Tipo de Pago del Credito</td>
			<td><?php echo $tipo_pago; ?></td>
		</tr>
	
	</thead>
</table>
<?php

echo $t;

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

	echo $t;

}
?>