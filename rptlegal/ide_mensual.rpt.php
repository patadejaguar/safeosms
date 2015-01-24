<?php
/**
 * Reporte de IDE Mensual
 * 
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package taxs
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
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.captacion.inc.php");
ini_set("max_execution_time", 1200);

$oficial = elusuario($iduser);


$input 					= $_GET["out"];
$fecha_inicial 			= $_GET["on"];
$fecha_final 			= $_GET["off"];
$sucursal				= $_GET["f700"];

$BySucursal			= "";
if ( isset($sucursal) && $sucursal != "todas" ){
	$BySucursal		= " AND `operaciones_recibos`.`sucursal`  = '$sucursal' ";
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
			<th colspan="3">REPORTE DE I.D.E. MENSUAL</th>
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
	$tbl	= "";
	$tds	= "";
	$gravados	= 0;
	$pendiente	= 0;
	$retenido	= 0;
		$sql = "SELECT
						`operaciones_mvtos`.`socio_afectado`,
						`operaciones_recibos`.`tipo_pago`,
						`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
						SUM(`operaciones_mvtos`.`afectacion_real`) AS 'monto'
					FROM
						`operaciones_mvtos` `operaciones_mvtos`
							INNER JOIN `operaciones_recibos` `operaciones_recibos`
							ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.
							`idoperaciones_recibos`
								INNER JOIN `eacp_config_bases_de_integracion_miembros`
								`eacp_config_bases_de_integracion_miembros`
								ON `operaciones_mvtos`.`tipo_operacion` =
								`eacp_config_bases_de_integracion_miembros`.`miembro`
				WHERE
					(`operaciones_mvtos`.`fecha_afectacion` >='$fecha_inicial')
					AND
					(`operaciones_mvtos`.`fecha_afectacion` <='$fecha_final')
					AND
					(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 2600)
					AND
					(`operaciones_recibos`.`tipo_pago` = 'efectivo' )
					$BySucursal
					GROUP BY
					`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
					`operaciones_mvtos`.`socio_afectado`,
					`operaciones_recibos`.`tipo_pago`";

			$rsIDE	= mysql_query($sql, cnnGeneral());
			while($rwIDE = mysql_fetch_array($rsIDE)){
				$socio			= $rwIDE["socio_afectado"];
				$monto			= $rwIDE["monto"];
				if ($monto > EXCENCION_IDE ){
					$cSoc			= new cSocio($socio, true);
					$nombre			= $cSoc->getNombreCompleto();
					$DSoc			= $cSoc->getDatosInArray();
					$rfc			= $DSoc["rfc"];
					
					$ide_pagado		= $cSoc->getIDEPagadoByPeriodo($fecha_final);
					$ide_pendiente	= $cSoc->getIDExPagarByPeriodo($fecha_final);
					$base_gravada	= $cSoc->getBaseGravadaIDE();
					
					$pendiente		+= $ide_pendiente;
					$gravados		+= $base_gravada;
					$retenido		+= $ide_pagado;
					
					$tds			.= "
					<tr>
						<td>$socio</td>
						<td>$nombre</td>
						<td>$rfc</td>
						
						<td class='mny'>" . getFMoney($base_gravada) . "</td>
						<td class='mny'>" . getFMoney($ide_pagado) . "</td>
						<td class='mny'>" . getFMoney($ide_pendiente) . "</td>
					</tr>";
				}
			}
	$tbl	.= "<table width=\"100%\" aling=\"center\" border=\"0\">
				<thead>
					<th>Num. Socio</th>
					<th>Nombre</th>
					<th>R.F.C.</th>
					<th>Operaciones <br />Gravadas</th>
					<th>IDE <br />Retenido</th>
					<th>IDE <br />Pendiente</th>
					
				</thead>
					<tbody>
						$tds
					</tbody>
					<tfoot>
					<td />
					<th >TOTALES</th>
					<td />
						<th class='mny'>" . getFMoney($gravados) . "</th>
						<th class='mny'>" . getFMoney($retenido) . "</th>
						<th class='mny'>" . getFMoney($pendiente) . "</th>					
					</tfoot>
				</table> ";
	echo $tbl;

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
