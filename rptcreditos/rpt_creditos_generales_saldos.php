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
include_once "../libs/sql.inc.php";
include_once "../reports/PHPReportMaker.php";
$oficial = elusuario($iduser);
//=====================================================================================================
/**
 * Filtrar si Existe Caja Local
 */

$estatus 				= (isset($_GET["f2"])) ? $_GET["f2"] : SYS_TODAS;
$frecuencia 			= (isset($_GET["f1"])) ? $_GET["f1"] : SYS_TODAS;
$convenio 				= (isset($_GET["f3"])) ? $_GET["f3"] : SYS_TODAS;

$fecha_inicial			= (isset($_GET["on"])) ? $_GET["on"] : EACP_FECHA_DE_CONSTITUCION;
$fecha_final			= (isset($_GET["off"])) ? $_GET["off"] : fechasys();
$estatus 				= (isset($_GET["estado"])) ? $_GET["estado"] : $estatus;
$frecuencia 			= (isset($_GET["periocidad"])) ? $_GET["periocidad"] : $frecuencia;
$frecuencia 			= (isset($_GET["frecuencia"])) ? $_GET["frecuencia"] : $frecuencia;
$convenio 				= (isset($_GET["convenio"])) ? $_GET["convenio"] : $convenio;

$tipo_autorizacion		= (isset($_GET["tipoautorizacion"])) ? $_GET["tipoautorizacion"] : SYS_TODAS;

$es_por_estatus 		= "";
//$si_es_por_fecha
$es_por_frecuencia 		= "";
$es_por_convenio 		= "";
$es_por_operacion		= "";

$sumSDO					= 0;
$sumCAP					= 0;

if($estatus != SYS_TODAS){
	//$nest = eltipo("creditos_estatus", $estatus);
	$es_por_estatus = " AND creditos_solicitud.estatus_actual=$estatus ";
}
//
if($frecuencia != SYS_TODAS){
	//$nfreq = eltipo("creditos_periocidadpagos", $frecuencia);
	$es_por_frecuencia = " AND creditos_solicitud.periocidad_de_pago=$frecuencia ";
}
//
if($convenio != SYS_TODAS){
	//$nconv = eltipo("creditos_tipoconvenio", $convenio);
	$es_por_convenio = " AND creditos_solicitud.tipo_convenio = $convenio ";	
}
//$fecha_final = $_GET["off"];
$ByAutorizacion		= ($tipo_autorizacion != SYS_TODAS) ? " AND (`creditos_solicitud`.`tipo_autorizacion` = $tipo_autorizacion) " : " ";
//XXX: Revisar
if($tipo_operacion != "Todas"){
	$es_por_operacion = " AND
	(`operaciones_no_estadisticas`.`tipo_de_operacion` ='$tipo_operacion')";
}

$f3 = $_GET["f3"];
$input = $_GET["out"];
	if (!$input) {
		$input = "default";
	}


	/* ******************************************************************************/
  

$setSql = "SELECT socios.nombre, creditos_solicitud.numero_socio AS 'socio', 
	creditos_solicitud.numero_solicitud AS 'solicitud', 
	creditos_tipoconvenio.descripcion_tipoconvenio AS 'modalidad', 
	creditos_periocidadpagos.descripcion_periocidadpagos AS 'condiciones_de_pago', 
	creditos_solicitud.fecha_ministracion AS 'fecha_de_otorgamiento', 
	creditos_solicitud.monto_autorizado AS 'monto_original', 
	creditos_solicitud.fecha_vencimiento AS 'fecha_de_vencimiento', 
	creditos_solicitud.tasa_interes AS 'tasa_ordinaria_nominal_anual',
	 creditos_solicitud.pagos_autorizados AS 'numero_de_pagos', 
	creditos_solicitud.periocidad_de_pago AS 'frecuencia', 
	creditos_solicitud.saldo_actual AS 'saldo_insoluto', 
	creditos_solicitud.fecha_ultimo_mvto, 
	creditos_estatus.descripcion_estatus AS 'estatus', 
	socios.genero, socios.tipo_ingreso, 
	creditos_solicitud.tipo_autorizacion AS 'modaut',
	`operaciones_no_estadisticas`.* 
	FROM 
	`creditos_solicitud` `creditos_solicitud` 
		INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos` 
		ON `creditos_solicitud`.`periocidad_de_pago` = 
		`creditos_periocidadpagos`.`idcreditos_periocidadpagos` 
			INNER JOIN `socios` `socios` 
			ON `creditos_solicitud`.`numero_socio` = `socios`.`codigo` 
				INNER JOIN `operaciones_no_estadisticas` 
				`operaciones_no_estadisticas` 
				ON `creditos_solicitud`.`numero_solicitud` = 
				`operaciones_no_estadisticas`.`documento` 
					INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio` 
					ON `creditos_solicitud`.`tipo_convenio` = 
					`creditos_tipoconvenio`.`idcreditos_tipoconvenio` 
						INNER JOIN `creditos_estatus` `creditos_estatus` 
						ON `creditos_solicitud`.`estatus_actual` = 
						`creditos_estatus`.`idcreditos_estatus`
	
	WHERE creditos_solicitud.saldo_actual>=0.99
	AND
	(`creditos_solicitud`.`fecha_ultimo_mvto`>='$fecha_inicial')
	$ByAutorizacion
	$es_por_estatus
	$es_por_frecuencia
	$es_por_convenio
	$es_por_operacion
	AND
	(`operaciones_no_estadisticas`.`fecha` >='$fecha_inicial')
	AND
	(`operaciones_no_estadisticas`.`fecha` <='$fecha_final')
	
	ORDER BY creditos_solicitud.tipo_autorizacion DESC,
				creditos_solicitud.fecha_ministracion, 
			creditos_estatus.orden_clasificacion ASC, 
			creditos_solicitud.numero_socio, creditos_solicitud.numero_solicitud,
			`operaciones_no_estadisticas`.fecha";
	//exit($setSql);
	
if ($input!=OUT_EXCEL) {	
	
	$credito_anterior 	= false;
	$TR_parent			= "";
	$TR_child			= "";
			//echo $setSql;	
	$rs 			= getRecordset($setSql);
		$rows		= mysql_num_rows($rs);
		$counter	= 0;
		
		while($rw = mysql_fetch_array($rs)){

			//$TR_head		= "";
			if($credito_anterior == false)	{
				$credito_anterior = $rw["solicitud"];
			}
			$credito 	= $rw["solicitud"];
			if($credito!=$credito_anterior){
				

  				//resetear TR_child
  				$TR_parent	.= "

  				$TR_head

  				<tr>
  					<td colspan='4'>
  						<table width='100%'>
  							<thead>
  								<th width='10%'>[$counter]Recibo</th>
  								<th width='15%'>Fecha</th>
  								<th width='25%'>Operacion</th>
  								<th width='20%'>Monto</th>
  								<th width='30%'>Detalles</th>
  							<thead>
  							<tbody>
  								$TR_child
  							</tbody>
  						</table>
  					</td>
  					<tr>
  						<td colspan='4'> <hr /></td>
  					</tr>
  				</tr>
  				";
				$TR_child 	= "";
				$TR_head	= "";
			}
			$TR_child	.= "
			<tr>
				<td>" . $rw["recibo"] . "</td>
				<td>" . getFechaMX($rw["fecha"]) . "</td>
				<td>" . $rw["tipo_de_operacion"] . "</td>
				<td class='mny'>" . getFMoney($rw["monto"]) . "</td>
				<td>" . $rw["detalles"] . "</td>
			</tr>";
				$sumCAP		+= $rw["monto_original"];
				$sumSDO		+= $rw["saldo_insoluto"];
				$TR_head = "
				<tr>
					<th class='izq'>Socio</th>
					<td>" . $rw["socio"] . "</td>
	  				<th class='izq'>Nombre
	  				</th><td>" . $rw["nombre"] . "</td>
	  			</tr>
	  			<tr>
  					<th class='izq'>Numero de Solicitud</th>
  					<td>" . $rw["solicitud"] . "</td>
  					<th class='izq'>Fecha de Ministracion</th>
  					<td>" . $rw["fecha_de_otorgamiento"] . "</td>
  				</tr>
  				<tr>
	  				<th class='izq'>Tipo de Convenio</th>
	  				<td>" . $rw["modalidad"] . "</td>
	  				<th class='izq'>Fecha de Vencimiento</th>
	  				<td>" . $rw["fecha_de_vencimiento"] . "</td>
	  			</tr>
	  			<tr>
 					<th class='izq'>Periocidad</th>
 					<td>" . $rw["condiciones_de_pago"] . "</td>
 					<th class='izq'>Estatus</th>
 					<td>" . $rw["estatus"] . "</td>
	  			</tr>
	  			<tr>
	  				<th class='izq'>Monto Ministrado</th>
	  				<td class='mny'>" .  getFMoney($rw["monto_original"]) . "</td>
	  				<th class='izq'>Saldo Actual</th>
	  				<td class='mny'>" .  getFMoney($rw["saldo_insoluto"]) . "</td>
  				</tr>"; 

			$credito_anterior	= $credito;
			$counter++;
		}
		//Corregir el último
		//resetear TR_child
		$TR_parent	.= "
		
		$TR_head
		
		<tr>
		<td colspan='4'>
		<table width='100%'>
		<thead>
		<th width='10%'>[$counter]Recibo</th>
		<th width='15%'>Fecha</th>
		<th width='25%'>Operacion</th>
		<th width='20%'>Monto</th>
		<th width='30%'>Detalles</th>
		<thead>
		<tbody>
		$TR_child
		</tbody>
		</table>
		</td>
		<tr>
		<td colspan='4'> <hr /></td>
	  			<tr>
	  				<th class='izq'>Monto Ministrado</th>
	  				<td class='mny'>" .  getFMoney($sumCAP) . "</td>
	  				<th class='izq'>Saldo Actual</th>
	  				<td class='mny'>" .  getFMoney($sumSDO) . "</td>
  				</tr>		
		</tr>
		</tr>
		";
		?>
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title></title>
		</head>
		<link href="../css/reporte.css" rel="stylesheet" type="text/css">
		<body>
<!-- -->

<?php
		echo getRawHeader();

		//Imprimir toda la Tabla
		echo "
		<table width='100%'>
			$TR_parent
		</table>";
		echo getRawFooter();
		?>
		</body>
		</html>
		<?php
	@mysql_free_result($rs);
	unset($rs);
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
	
	$cTbl = new cTabla($setSql);
	$cTbl->setWidth();
	$cTbl->Show("", false);
}
?>