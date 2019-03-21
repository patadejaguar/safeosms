<?php
/**
 * @author Balam Gonzalalez Luis H
 * @version 1.0
 * @package seguimiento
 * @since 2008/04/16
 * 
 * 		Cambios en la version
 * 		2008/04/06
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

/** $id = $_GET["id"];
	if (!$id){
		echo $regresar;
		exit;
	}	*/

$frecuencia		= $_GET["f1"];
$ByFrecuencia	= "";
if ( isset($frecuencia) ){
	$ByFrecuencia	= " AND creditos_solicitud.periocidad_de_pago = $frecuencia ";
}
$paginar	= $_GET["v"];
if($paginar== 1){
	$paginar = true;
} else {
	$paginar = false;
}
$oficial = elusuario($iduser);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<link href="../css/flags.css" rel="stylesheet" type="text/css" media="screen">
<body onLoad="initComponents();">
<!-- -->
<?php
  $fecha_de_reporte		= fechasys();

  $VEstatus		= array(
  						10	=> "VIGENTE",
  						20	=> "VENCIDA",
  						30	=> "MOROSO",
  						60	=> "REESTRUCTURADO",
  						50	=> "CASTIGADO",
  						99	=> "DESCONOCIDO"
  						);

/**
 * ejecuta una consulta de las letras por todos los creditos
 */
 	$aInfoLetra		= array();
 		$sqlLetras = "
SELECT
	`creditos_letras_morosas`.`socio_afectado`,
	`creditos_letras_morosas`.`docto_afectado`,
	`creditos_letras_morosas`.`periodo_inicial`,
	`creditos_letras_morosas`.`periodo_final`,
	`creditos_letras_morosas`.`fecha_inicial`,
	`creditos_letras_morosas`.`fecha_final`,
	`creditos_letras_morosas`.`monto`

FROM
	`creditos_letras_morosas` `creditos_letras_morosas`";
	$rsL = mysql_query($sqlLetras, cnnGeneral());
	while($rL = mysql_fetch_array($rsL) ){
		$aInfoLetra[ $rL["docto_afectado"] ]["PInicial"] 	= $rL["periodo_inicial"];
		$aInfoLetra[ $rL["docto_afectado"] ]["PFinal"] 		= $rL["periodo_final"];
		$aInfoLetra[ $rL["docto_afectado"] ]["FInicial"] 	= $rL["fecha_inicial"];
		$aInfoLetra[ $rL["docto_afectado"] ]["FFinal"] 		= $rL["fecha_final"];
	}

echo getRawHeader();
$sql = "SELECT
	`socios_general`.`codigo`,
	CONCAT(`socios_general`.`nombrecompleto`, ' ',
	`socios_general`.`apellidopaterno`, ' ',
	`socios_general`.`apellidomaterno`) AS 'nombre',
	`creditos_solicitud`.*
FROM
	`creditos_solicitud` `creditos_solicitud`
		INNER JOIN `socios_general` `socios_general`
		ON `creditos_solicitud`.`numero_socio` = `socios_general`.`codigo`

	WHERE saldo_actual > "  . TOLERANCIA_SALDOS .  "
		$ByFrecuencia
		/* AND estatus_actual!=50 */
		/* HAVING DATE_ADD(fecha_ultimo_mvto, INTERVAL periocidad_de_pago DAY) <= CURDATE() */
		ORDER BY fecha_ministracion
		";
$rs 	= mysql_query($sql, cnnGeneral());
$trs	= "";
while($rw = mysql_fetch_array($rs)){
	$credito			= $rw["numero_solicitud"];
	$socio				= $rw["numero_socio"];
	$periocidad			= $rw["periocidad_de_pago"];

	$letras_pagadas		= 0;
	$letras_pendientes	= 1;
	$letras_que_debio	= 0;

	$dias_autorizados	= $rw["dias_autorizados"];
	$pagos				= $rw["pagos_autorizados"];
	$saldo_historico	= $rw["monto_autorizado"];
	$fecha_ultimo_mvto	= $rw["fecha_ultimo_mvto"];
	$saldo_actual		= $rw["saldo_actual"];
	$nombre				= $rw["nombre"];
	$fecha_ministracion	= $rw["fecha_ministracion"];
	$estatus_actual		= $rw["estatus_actual"];

	$nombre_estatus		= $VEstatus[$estatus_actual];
	$en_mora			= false;
	$dias_vencidos		= 0;
	$dias_transcurridos	= restarfechas($fecha_ministracion, $fecha_de_reporte);



	$css_class			= "";

	$periodo_en_desfase	= round(($dias_autorizados /  $rw["pagos_autorizados"]), 0);

	if ($periocidad != 360){

		$DLetras		= $aInfoLetra[$credito];
		//en vencimiento es la proxima letra a vencer
		$FMora			= $DLetras["FInicial"];
		$FVenc			= sumardias($FMora, DIAS_PAGO_VARIOS);

		if ( !isset($FMora) ){
			$FMora					= sumardias($fecha_ultimo_mvto, ($periocidad + $periodo_en_desfase)) ;
			$FVenc					= sumardias($FMora, DIAS_PAGO_VARIOS );
			$en_mora				= false;
		} else {
			$en_mora				= true;
		}
			$letras_pagadas			= ($saldo_historico - $saldo_actual) / ($saldo_historico / $pagos);
			$letras_pagadas			= round($letras_pagadas, 0);
			$letras_pendientes		= $saldo_actual / ($saldo_historico / $pagos);
			$letras_pendientes		= round($letras_pendientes, 0);
			$letras_que_debio		= round($dias_transcurridos / ($dias_autorizados / $pagos), 0);

	} else {
		$FMora			= $rw["fecha_vencimiento"];
		$FVenc			= sumardias($rw["fecha_vencimiento"], DIAS_PAGO_UNICOS);
		$en_mora		= true;
	}
	//if ($en_mora == true){
		$dias_vencidos	= restarfechas($fecha_de_reporte, $FMora);
	$trs	.= "<tr>
					<td>$socio</td>
					<td>$nombre</td>
					<td>$credito</td>
					<td >$periocidad</td>
					<td class='mny'>$pagos</td>
					<td class='$nombre_estatus'>$nombre_estatus</td>
					<td>$fecha_ministracion</td>
					<td>$fecha_ultimo_mvto</td>
					<td>$FMora</td>
					<td>$FVenc</td>

					<td class='mny'>$saldo_historico</td>
					<td class='mny'>$saldo_actual</td>
					<td class='mny'>$letras_pagadas</td>
					<td class='mny'>$letras_pendientes</td>
					<td class='mny'>$letras_que_debio</td>
					<td>$dias_vencidos</td>
				</tr>";
	//}
}
echo "<table width='100%' border='0'>
	<tbody>
		<tr>
			<th>Codigo</th>
			<th>Nombre</th>
			<th>#Credito</th>
			<th>Periocidad</th>
			<th>Num. Pagos</th>
			<th>Estatus</th>
			<th>Fecha de Ministracion</th>
			<th>Fecha de Ultima Op.</th>
			<th>Fecha de Mora</th>
			<th>Fecha de Vencimiento</th>
			<th>Monto Ministrado</th>
			<th>Saldo Insoluto</th>
			<th>Letras Pagadas</th>
			<th>Letras Pendientes</th>
			<th>Letras que debio pagar</th>
			<th>Dias Vencidos</th>
		</tr>

		$trs
	</tbody>
</table>";
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