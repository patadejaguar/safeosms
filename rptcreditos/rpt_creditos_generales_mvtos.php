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
$xHP		= new cHPage("TR.REPORTE DE ", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();

/**
 * Filtrar si Existe Caja Local
 */

$estatus 				= (isset($_GET["f2"])) ? $_GET["f2"] : SYS_TODAS;
$frecuencia 			= (isset($_GET["f1"])) ? $_GET["f1"] : SYS_TODAS;
$convenio 				= (isset($_GET["f3"])) ? $_GET["f3"] : SYS_TODAS;
//$fecha_inicial			= (isset($_GET["on"])) ? $_GET["on"] : EACP_FECHA_DE_CONSTITUCION;
//$fecha_final			= (isset($_GET["off"])) ? $_GET["off"] : fechasys();
$tipo_operacion			= parametro("f711", SYS_TODAS, MQL_INT);
$tipo_operacion			= parametro("operacion", $tipo_operacion, MQL_INT);
$estatus 				= (isset($_GET["estado"])) ? $_GET["estado"] : $estatus;
$frecuencia 			= (isset($_GET["periocidad"])) ? $_GET["periocidad"] : $frecuencia;
$frecuencia 			= (isset($_GET["frecuencia"])) ? $_GET["frecuencia"] : $frecuencia;
$convenio 				= (isset($_GET["convenio"])) ? $_GET["convenio"] : $convenio;
$tipo_autorizacion		= (isset($_GET["tipoautorizacion"])) ? $_GET["tipoautorizacion"] : SYS_TODAS;
$senders				= getEmails($_REQUEST);

$FechaInicial	= parametro("on", $xF->getFechaMinimaOperativa(), MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", $xF->getFechaMaximaOperativa(), MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);

$es_por_estatus 		= "";
//$si_es_por_fecha
$es_por_frecuencia 		= "";
$es_por_convenio 		= "";
$es_por_operacion		= "";

$sumSDO					= 0;
$sumCAP					= 0;

if($estatus != SYS_TODAS){ $es_por_estatus = " AND creditos_solicitud.estatus_actual=$estatus "; }
//
if($frecuencia != SYS_TODAS){	$es_por_frecuencia = " AND creditos_solicitud.periocidad_de_pago=$frecuencia "; }
//
if($convenio != SYS_TODAS){ $es_por_convenio = " AND creditos_solicitud.tipo_convenio = $convenio "; }
//$fecha_final = $_GET["off"];
$ByAutorizacion		= ($tipo_autorizacion != SYS_TODAS) ? " AND (`creditos_solicitud`.`tipo_autorizacion` = $tipo_autorizacion) " : " ";
//XXX: Revisar
if( setNoMenorQueCero($tipo_operacion) > 0){	$es_por_operacion = " AND (`operaciones_no_estadisticas`.`tipo_de_operacion` ='$tipo_operacion')"; }

$f3 				= $_GET["f3"];
$out 				= parametro("out", SYS_DEFAULT);


	/* ******************************************************************************/
 $TR_head			= "";

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
	(`creditos_solicitud`.`fecha_ultimo_mvto`>='$FechaInicial')
	$ByAutorizacion
	$es_por_estatus
	$es_por_frecuencia
	$es_por_convenio
	$es_por_operacion
	AND
	(`operaciones_no_estadisticas`.`fecha` >='$FechaInicial')
	AND
	(`operaciones_no_estadisticas`.`fecha` <='$FechaFinal')
	
	ORDER BY creditos_solicitud.tipo_autorizacion DESC,
				creditos_solicitud.fecha_ministracion, 
			creditos_estatus.orden_clasificacion ASC, 
			creditos_solicitud.numero_socio, creditos_solicitud.numero_solicitud,
			`operaciones_no_estadisticas`.fecha";
	//exit($setSql);
	
	
	
	$credito_anterior 	= false;
	$TR_parent			= "";
	$TR_child			= "";
			//echo $setSql;	
	$rs 				= $query->getDataRecord($setSql);// getRecordset($setSql);
	$rows				= $query->getNumberOfRows();// mysql_num_rows($rs);
	$counter	= 0;
		
		//while($rw = mysql_fetch_array($rs)){
	foreach($rs as $rw){
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
  						<table>
  							<thead>
  								<th width='10%'>Recibo</th>
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
  						<td colspan='4'></td>
  						<td colspan='4'></td>
  					</tr>
  				</tr>
  				";
				$TR_child 	= "";
				$TR_head	= "";
			}
			$TR_child	.= "
			<tr>
				<td>" . $rw["recibo"] . "</td>
				<td>" . $xF->getFechaMX($rw["fecha"]) . "</td>
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
		<table>
			<thead>
				<th width='10%'>Recibo</th>
				<th width='15%'>Fecha</th>
				<th width='25%'>Operacion</th>
				<th width='20%'>Monto</th>
				<th width='30%'>Detalles</th>
			<thead>
		<tbody> $TR_child </tbody>
		</table>
	</td>
</tr>";
/*		<!-- <tr>
		<td colspan='4'> 
		<hr />
		</td>
	  			<tr>
	  				<th class='izq'>[$counter]Monto Ministrado</th>
	  				<td class='mny'>" .  getFMoney($sumCAP) . "</td>
	  				<th class='izq'>Saldo Actual</th>
	  				<td class='mny'>" .  getFMoney($sumSDO) . "</td>
  				</tr>		
		</tr> -->
		</tr>*/

$sql			= $setSql;
$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
$xT		= new cTabla($sql, 2);
$xT->setTipoSalida($out);


$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);

$xRPT->addContent($body);
//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");
//$xRPT->addContent( $xT->Show( $xHP->getTitle() ) );
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
//$xRPT->addContent( $xHP->end() );
$xRPT->addContent("<table>$TR_parent</table>");

$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);

?>