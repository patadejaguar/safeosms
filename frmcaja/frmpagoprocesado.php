<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @package creditos
 * @since 1.0 27/02/2008
 * @version 1.1.0
 *  Archivo de proceso de pago
 * 		- 03/04/2008 -
 * 		- 27/05/2008 - Se Agrego el Formato Moneda en las cantidades Finales
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
$xHP				= new cHPage("Cobros de Credito");
$xSQL				= new cSQLListas();
$xT					= new cTipos();
$xF					= new cFecha();
$xQL					= new MQL();
$xLog				= new cCoreLog();

$params 			= $_GET["p"];
$procesado			= (isset($_REQUEST["procesar"])) ? $_REQUEST["procesar"] : SYS_NORMAL;
$pempresa			= parametro("periodoempresa", 0, MQL_INT);// (isset($_REQUEST["periodoempresa"])) ? $_REQUEST["periodoempresa"] : "";
$sumaoperaciones	= parametro("idtotaloperaciones", 0, MQL_FLOAT);
$montodesglose		= parametro("idplandesglose", 0, MQL_FLOAT);
$xBase				= new cBases();


$DPar 				= explode("|", $params);
ini_set("max_execution_time", 180);
//20052|2005214|33|7|0|pc|10|99

//socio|solicitud|parcialidad|[deprecated]periocidad|monto a operar|[optional]operacion
$limParms 			= ($procesado != SYS_AUTOMATICO) ? 7 : 7;
	for($ix=0; $ix <= $limParms; $ix++){
		if((!isset($DPar[$ix])) or (trim($DPar[$ix]) == "") ){
			saveError(210, $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Faltan Parametros($params) para el cobro, el Parametro $ix resulto " . $DPar[$ix] );
			header("location:../404.php?i=210");	//provocar error 404
			exit;
		}
	}
$xFRM						= new cHForm("frmpagoprocesado");
$capitalAfectado			= false;
$interesAfectado			= false;
///frmcaja/frmpagoprocesado.php?p=1000047|200004702|1|350.48|plc|efectivo|99|06-12-2013&procesar=automatico&periodoempresa=24
$socio						= $xT->cInt( $DPar[0] );
$solicitud					= $xT->cInt( $DPar[1] );
$parcialidad				= $xT->cInt( $DPar[2] );
$periocidad					= $xT->cInt( $DPar[3] );
$monto_a_operar				= $xT->cFloat( $DPar[4] );
$operacion					= (isset($DPar[5])) ? $DPar[5] : OPERACION_PAGO_COMPLETO;
$SRC						= $_POST;
$abonar_al_final			= 0;

$xCred						= new cCredito($solicitud, $socio);
$xCred->init();

if($procesado == SYS_AUTOMATICO){
	$arrINTS				= array(420);//Array de Intereses Devengados
	$SRC["ctipo_pago"]		= $DPar[5];
	$operacion				= $DPar[4];
	$monto_a_operar			= $xT->cFloat( $DPar[3] );
	$periocidad				= (isset($_REQUEST["periocidad"])) ? $_REQUEST["periocidad"] : DEFAULT_PERIOCIDAD_PAGO;
	$_SESSION[FECHA_OPERATIVA]	= $xF->getFechaISO($DPar[7]);
	$totalAutomatico			= 0;
	
	//6 = banco
	//7 = fecha
	$SRC["cobservaciones"]	= "($pempresa.$periocidad)L.$parcialidad:" . $xCred->getPagosAutorizados();
	//cargar credito y datos de la parcialidad
	$rs						= $xQL->getDataRecord($xSQL->getConceptosDePago($solicitud, $socio, $parcialidad));
	//setLog($xSQL->getConceptosDePago($solicitud, $socio, $parcialidad));
	//$msg					.= "SQL\t" . $xSQL->getConceptosDePago($solicitud, $socio, $parcialidad) . "\r\n";
	$curOps					= 0;
	foreach ($rs as $rx){
		$title	        	= $rx["descripcion_operacion"];
		$monto	        	= $xT->cFloat($rx["total_operacion"], 2);
		$MTipo          	= $rx["tipo_operacion"];
		if ( in_array($MTipo, $arrINTS) ){
			$xLog->add("WARN\tOPTS\tSe OMITE \t$title \t:\t$monto\r\n", $xLog->DEVELOPER);
		} else {
			$SRC["c-$MTipo"]	= $monto;
			$totalAutomatico	+= $monto;
			$xLog->add("WARN\tOPTS\tAgregando operacion:\t$title [" . $SRC["c-$MTipo"] . "]\t:\t$monto\r\n", $xLog->DEVELOPER);
			$curOps++;
		}
	}
	if($curOps < 2){
	//Cancelar si no hay Movientos
		$xho		= new cHObject();
		header("Content-type: text/xml");
		setLog("Pago Cancelado Letra incompleta $params");
		exit("<?xml version =\"1.0\" ?>\n<resultados>ERROR.CANCELADO. No se procesa No existe la letra ($params)</resultados>");
	}
}
$xLog->add("OK\tDATOS\tRecibiendo Datos Socio $socio, Credito $solicitud, Parcialidad $parcialidad, Periocidad $periocidad, Monto $monto_a_operar, Operacion $operacion \r\n");
	//header("Content-type: text/plain");
	//exit($msg);
//========================== Obtener parametros
//$cheque					= (isset($SRC["ccheque"])) ? $SRC["ccheque"] : DEFAULT_CHEQUE;
$cheque					= parametro("cheque", DEFAULT_CHEQUE); $cheque = parametro("idcheque", $cheque); $cheque = parametro("ccheque", $cheque);

//Obtener los Datos por POST
$total_recibo 			= (isset($SRC["ctotal"])) ?  $SRC["ctotal"] : 0;
$fecha_operacion		= (isset($_SESSION[FECHA_OPERATIVA]) ) ? $_SESSION[FECHA_OPERATIVA] : fechasys();
$observaciones			= (isset($SRC["cobservaciones"])) ? $SRC["cobservaciones"] : "";

$recibo_fiscal			= (isset($SRC["crecibo_fiscal"])) ? $SRC["crecibo_fiscal"] : DEFAULT_RECIBO_FISCAL;
$tipo_pago				= (isset($SRC["ctipo_pago"])) ? $SRC["ctipo_pago"] : DEFAULT_TIPO_PAGO;

$xLog->add("OK\tDATOS\tCheque $cheque, Recibo Fiscal $recibo_fiscal, Tipo de Pago $tipo_pago \r\n", $xLog->DEVELOPER);

$interescorriente 		= ( isset($SRC["c-corriente"]) ) ? $SRC["c-corriente"] : 0;
$interesremanente 		= ( isset($SRC["c-remanente"]) ) ?  $SRC["c-remanente"] : 0;
$interesmoroso			= ( isset($SRC["c-moroso"]) ) ? $SRC["c-moroso"] : 0;
$interesvencido			= ( isset($SRC["c-vencido"]) ) ? $SRC["c-vencido"] : 0;
$interesanticipado		= ( isset($SRC["c-anticipado"]) ) ? $SRC["c-anticipado"] : 0;
$ivaintereses			= ( isset($SRC["c-ivaintereses"]) ) ? $SRC["c-ivaintereses"] : 0;
$ivaotros				= ( isset($SRC["c-ivaotros"]) ) ? $SRC["c-ivaotros"] : 0;
$capital				= ( isset($SRC["c-capital"]) ) ?  $SRC["c-capital"] : 0;

$p_interescorriente 	= ( isset($SRC["p-corriente"]) ) ? $SRC["p-corriente"] : 0;
$p_interesremanente 	= ( isset($SRC["p-remanente"]) ) ? $SRC["p-remanente"] : 0;
$p_interesmoroso		= ( isset($SRC["p-moroso"]) ) ? $SRC["p-moroso"] : 0;
$p_interesvencido		= ( isset($SRC["p-vencido"] ) ) ? $SRC["p-vencido"] : 0;
$p_interesanticipado	= ( isset($SRC["p-anticipado"]) ) ? $SRC["p-anticipado"] : 0;
$p_ivaintereses			= ( isset($SRC["p-ivaintereses"]) ) ? $SRC["p-ivaintereses"] : 0;
$p_ivaotros				= ( isset($SRC["p-ivaotros"]) ) ? $SRC["p-ivaotros"] : 0;



$dsol 				= $xCred->getDatosDeCredito();
$estatus			= $xCred->getEstadoActual();
$grupo				= $dsol["grupo_asociado"];
$periocidad			= $xCred->getPeriocidadDePago();
$nuevo_saldo		= $xCred->getSaldoActual();

//Datos Fijos
	$percont 		= EACP_PER_CONTABLE;	// Periodo Contable
	$percbza 		= EACP_PER_COBRANZA;	// Periodo Cobranza.
	$perseg 		= EACP_PER_SEGUIMIENTO;	// Period de Seguimiento.
	$permens 		= date("m");			// Periodo de dias en el mes
	$persem 		= date("N");					// Periodo de dias en la semana.
	$peranual 		= date("Y");					// Ao Natural.

//Datos del Respeto al Plan de Pagos
$OPdto					= $xCred->getOProductoDeCredito();
$OPerx					= $xCred->getOPeriocidad();
$OEstado				= $xCred->getOEstado();
$respetar_plan_pagos 	= $xCred->getRespetarPlanDePago();
$empresa				= $xCred->getClaveDeEmpresa();
$tasa_iva				= $xCred->getTasaIVA();
$parcialidad_fecha_pago	= $fecha_operacion;
//Carga datos de Plan de Pagos
$LPlan					= null;
$parcialidad_capital	= 0;
$parcialidad_interes	= 0;
//===================================================================================== Fecha : 03 de Marzo 2015
if($xCred->isAFinalDePlazo() == false){
	$LPlan				= $xCred->getOPlanDePagos();
	if($LPlan !== null){
		
		$xLetra					= $LPlan->getOLetra($parcialidad);
		$LPlan->setPagosAutorizados($xCred->getPagosAutorizados());
		if($xLetra != null){
			$parcialidad_fecha_pago	= $xLetra->getFechaDePago();
			$parcialidad_interes	= $xLetra->getInteres();
			$parcialidad_capital	= $xLetra->getCapital();
		}
	}
}
if($procesado == SYS_AUTOMATICO){
	if(isset($SRC["c-" . OPERACION_CLAVE_PLAN_CAPITAL])){
		$aut_pago_capital	= $SRC["c-" . OPERACION_CLAVE_PLAN_CAPITAL];
		$aut_diferencia		= ($aut_pago_capital > $xCred->getSaldoActual()) ? ($aut_pago_capital - $xCred->getSaldoActual()) : 0;
		$aut_iva			= 0;
		//289.8 - 289.76 = 0.04
		//ajustar si es el ultimo pago y la diferencia es mayor a 1
		if($parcialidad == $xCred->getPagosAutorizados() AND ($aut_diferencia <= TOLERANCIA_SALDOS AND $aut_diferencia > 0) ){
			$SRC["c-" . OPERACION_CLAVE_PLAN_CAPITAL]	= $xCred->getSaldoActual();//289.76
			//ajustar los centavos a IVA e Interes
			$aut_diferencia	= round(( $aut_diferencia / (1+$xCred->getTasaIVA()) ),2);
			$aut_iva		= round(($aut_diferencia * $xCred->getTasaIVA()),2);
			$SRC["c-" . OPERACION_CLAVE_PLAN_INTERES]	= (isset($SRC["c-" . OPERACION_CLAVE_PLAN_INTERES])) ? $SRC["c-" . OPERACION_CLAVE_PLAN_INTERES] + $aut_diferencia : $aut_diferencia;

			if($aut_iva > 0){
				$SRC["c-" . OPERACION_CLAVE_PLAN_IVA] = isset($SRC["c-" . OPERACION_CLAVE_PLAN_IVA]) ? ($SRC["c-" . OPERACION_CLAVE_PLAN_IVA] + $aut_iva) : $aut_iva;
			}
			$aut_pago_capital=$SRC["c-" . OPERACION_CLAVE_PLAN_CAPITAL];
		}
		//Cancelar si el capital abonado es mayor al saldo
		if($aut_pago_capital > $xCred->getSaldoActual()){
			$xho		= new cHObject();
			header("Content-type: text/xml");
			setLog("Pago Cancelado Credito sin Saldo. ($aut_pago_capital - $aut_diferencia - $aut_iva) $params");
			exit("<?xml version =\"1.0\" ?>\n<resultados>ERROR.CANCELADO. No se procesa el pago saldo ($aut_pago_capital - $aut_diferencia - $aut_iva) ($params)</resultados>");
		}		
	}
}
//====================================================================
$xLog->add("WARN\tPLAN\tQue hacer con el Plan: $respetar_plan_pagos\r\n", $xLog->DEVELOPER);

$contrato_captacion 	= $xCred->getContratoCorriente();
$saldo_anterior			= $xCred->getSaldoActual();
if ( $grupo == 0||!isset($grupo)||$grupo == ""||$grupo == false){ $grupo		= DEFAULT_GRUPO; }

$xNRec			= new cReciboDeOperacion(RECIBOS_TIPO_PAGO_CREDITO);
$xNRec->setNuevoRecibo($socio, $solicitud, $fecha_operacion, $parcialidad, RECIBOS_TIPO_PAGO_CREDITO, $observaciones,
		       $cheque, $tipo_pago, $recibo_fiscal, $grupo, false, AML_CLAVE_MONEDA_LOCAL, 0, $empresa, $parcialidad);

$xLog->add($xNRec->getMessages(OUT_TXT), $xLog->DEVELOPER);
$recibo_pago		= $xNRec->getCodigoDeRecibo();
if($procesado == SYS_AUTOMATICO OR $pempresa > 0){
	$icxObs			= $SRC["cobservaciones"];
	$xLog->add("WARN\tTOTAL AUTOMATICO\t:$icxObs\t$totalAutomatico\r\n", $xLog->DEVELOPER);
	//my_query("UPDATE empresas_cobranza SET estado = 0, observaciones = CONCAT(observaciones, '')  WHERE clave_de_credito=$solicitud AND parcialidad=$parcialidad AND `clave_de_nomina`=$pempresa");
	$xEmpPer		= new cEmpresasCobranzaPeriodos($pempresa);
	if($xEmpPer->init() == true){
		$xEmpPer->setCancelarOperacion($solicitud, $parcialidad, "[$recibo_pago]$icxObs [$fecha_operacion]", $recibo_pago);
		$xLog->add("WARN\tEliminar Operacion $solicitud, $parcialidad, $recibo_pago]$icxObs [$fecha_operacion \r\n", $xLog->DEVELOPER);
	}
}

$xLog->add("OK\tRECIBO\tEl Recibo es $recibo_pago\r\n", $xLog->DEVELOPER);
$opends 			= "Pendientes de Cobro del Rec $recibo_pago";
$recibo_pendientes	= false;
$total_pendientes	= 0;
$xPlan				= false;
$nueva_parcialidad	= $parcialidad;

$tds 				= "";

/**
 * Elimina el Recibo de Pendientes
 **/
$sqlDTemp		= "SELECT
						`operaciones_recibos`.`idoperaciones_recibos` AS 'recibo',
						COUNT(`operaciones_mvtos`.`idoperaciones_mvtos`) AS `movimientos`
					FROM
						`operaciones_mvtos` `operaciones_mvtos`
							INNER JOIN `operaciones_recibos` `operaciones_recibos`
							ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.
							`idoperaciones_recibos`
					WHERE
						(`operaciones_recibos`.`numero_socio` = $socio )
						AND
						(`operaciones_recibos`.`docto_afectado` = $solicitud )
						AND
						(`operaciones_recibos`.`tipo_docto` = " . RECIBOS_TIPO_PAGOSPENDS . ")
						AND
						(`operaciones_mvtos`.`periodo_socio` = $parcialidad )
					GROUP BY
						`operaciones_mvtos`.`recibo_afectado`,
						`operaciones_mvtos`.`periodo_socio`
					ORDER BY
						`operaciones_recibos`.`idoperaciones_recibos`,
						`operaciones_mvtos`.`recibo_afectado`,
						`operaciones_recibos`.`tipo_docto`
	";
	$rsTDel		= $xQL->getDataRecord($sqlDTemp);

	foreach ( $rsTDel as $rw ){
		$reciboP		= $rw["recibo"];
		$xRTmp		= new cReciboDeOperacion(RECIBOS_TIPO_PAGOSPENDS, false, $reciboP);
		$xRTmp->setNumeroDeRecibo($reciboP);
		$xRTmp->setRevertir();
	}

if($procesado != SYS_AUTOMATICO){
	echo $xHP->getHeader(true);
	echo "<body onload=\"printrec();\">";
}

	$m			= array();
	$p			= array();

//============================ Obtener valor de Movimientos =================================
	$xB2		= new cBases($xBase->BASE_MVTOS_ELIMINAR);
	$xB2->init();
	$sqlMT		= "SELECT
						`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
						`eacp_config_bases_de_integracion_miembros`.`miembro`,
						`eacp_config_bases_de_integracion_miembros`.`afectacion`,
						`eacp_config_bases_de_integracion_miembros`.`descripcion_de_la_relacion`
					FROM
						`eacp_config_bases_de_integracion_miembros`
					WHERE
						(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = " . $xBase->BASE_MVTOS_RECIBO . ") ";
	$rs 		= $xQL->getDataRecord($sqlMT);
	
	foreach ($rs as $rw){

			$id			= $rw["miembro"];
			$MContable	= TM_ABONO;
			$m[$id] 	= (isset($SRC["c-$id"]) ) ? $SRC["c-$id"] : 0;
			$p[$id]		= (isset($SRC["p-$id"]) ) ? $SRC["p-$id"] : 0;
			$mextra		= $m[$id];
			$pextra		= $p[$id];
			//Modificar el Movimiento en caso de ser negativo
			if ( $rw["afectacion"] == (-1) ){	$MContable	= TM_CARGO; }
			//Recibe
			if($m[$id] != 0){
				//eliminar Mvtos
				if($xB2->getIsMember($id) == true){
					//Eliminar
					
					if($periocidad == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
						setEliminarMvto360( $id, $socio, $solicitud, $recibo_pago);
						$xLog->add("WARN\tPARCS\tEliminar 360 $id, $socio, $solicitud, $parcialidad, $recibo_pago\r\n", $xLog->DEVELOPER);
					} else {
				    	if($respetar_plan_pagos == true){
				      		setEliminarMvto( $id, $socio, $solicitud, $parcialidad, $recibo_pago);
				      		$xLog->add("WARN\tPARCS\tEliminar Letra $id, $socio, $solicitud, $parcialidad, $recibo_pago\r\n", $xLog->DEVELOPER);
				    	} else {
							setEliminarMvto360( $id, $socio, $solicitud, $recibo_pago);
							$xLog->add("WARN\tPARCS\tEliminar 360 $id, $socio, $solicitud, $parcialidad, $recibo_pago\r\n", $xLog->DEVELOPER);
						}
					}
				} else {
					$total_pendientes += setNoMenorQueCero($pextra);
					//setLog("$id $total_pendientes ---- > $mextra ---- pendiente $pextra ");
				}
				
            	/**
				* Condiciona si es Ahorro
				* 412 = 0.00;
				**/
				if ( $id != OPERACION_CLAVE_PLAN_AHORRO ){
					$documento	= $solicitud;
				} else {
					$documento	= $contrato_captacion;
				}

			    $tds .= "<tr>
			                <th class=\"$id\"> $id </th><td>" . $rw["descripcion_de_la_relacion"] . "</td>
			                <td class='mny'>" . getFMoney($m[$id]) . "</td>
			            </tr>";
			  //setPolizaProforma($recibo_pago, $id, $m[$id], $socio, $documento, $MContable);
			}
	}
//<!--	INSERTAR MOVIMIENTOS DE PENDIENTES -->
if($periocidad == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
	if($p_interescorriente>0){	$p[420]		+=	$p_interescorriente; }
	if($p_interesmoroso>0){	}
	if($p_interesremanente>0){	$p[431]		+=	$p_interesremanente; }
	if($p_interesvencido>0){ 	$p[432]		+=	$p_interesvencido; }
	if($p_ivaintereses>0){ 		$p[1203]	+=	$p_ivaintereses; }
	if($p_ivaotros>0){			$p[1204]	+=	$p_ivaotros; }
}
$icls 						=	 count($p);
if($xCred->isAFinalDePlazo() == false ){
//eliminar letra o neutralizarla en partes.
//TODO: Actualizar estos montos de amortizacion

	setEliminarMvto( OPERACION_CLAVE_PLAN_IVA, $socio, $solicitud, $parcialidad, $recibo_pago);	
	//parche 29dic2014
	setEliminarMvto( OPERACION_CLAVE_PLAN_CAPITAL, $socio, $solicitud, $parcialidad, $recibo_pago);
	setEliminarMvto( OPERACION_CLAVE_PLAN_INTERES, $socio, $solicitud, $parcialidad, $recibo_pago);
	setEliminarMvto( OPERACION_CLAVE_PLAN_AHORRO, $socio, $solicitud, $parcialidad, $recibo_pago);
	
	//=========================== Eliminar 414 ===========================
	if($montodesglose > 0){
		$xOp		= new cMovimientoDeOperacion();
		$xOp->setNeutralizarBatch($montodesglose, $solicitud, OPERACION_CLAVE_PLAN_DESGLOSE, $parcialidad);
	}
	//=========================== -------- ===========================
	if ( $icls >= 1  ){
		$mobserva 			= "MONTO_PENDIENTE_RECIBO_$recibo_pago";
		$nueva_parcialidad	= $parcialidad;
		$dxplan				= $xCred->getDatosDelPlanDePagos();
		$xPlan				= setNoMenorQueCero($xCred->getNumeroDePlanDePagos());
	
		$xRecP				= new cReciboDeOperacion(RECIBOS_TIPO_PAGOSPENDS, true);
		if ( $xPlan <= 0){
			$recibo_pendientes	= $xRecP->setNuevoRecibo($socio, $solicitud, $fecha_operacion, $parcialidad, false, $mobserva, "", "ninguno", "NA", $grupo, false, "",0, $empresa, $parcialidad);
			$xRecP->setNumeroDeRecibo( $recibo_pendientes );
			$xRecP->init();
		} else {
			$recibo_pendientes	= $xPlan;
			$xRecP->setNumeroDeRecibo( $recibo_pendientes );
			$xRecP->init($dxplan);
		}
		//recorre los pendientes
		
		//--29Dic2014
		foreach( $p as $clave_operacion => $monto_pendiente ){
			//===================================================================================== Fecha : 03 de Marzo 2015
			$fecha_de_pendiente		= $fecha_operacion;
			if($xCred->getPeriocidadDePago() != CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
				$fecha_de_pendiente	= $parcialidad_fecha_pago;
			}
			if ( $monto_pendiente > TOLERANCIA_SALDOS ){
				if(($clave_operacion == OPERACION_CLAVE_PLAN_INTERES) AND ($monto_pendiente > 0) AND ($tasa_iva >0) ){
					//agregar Interes
					$iva_pendiente		= $monto_pendiente * $tasa_iva;
					$xRecP->setNuevoMvto($fecha_de_pendiente, $iva_pendiente, OPERACION_CLAVE_PLAN_IVA, $parcialidad, $mobserva );
					$total_pendientes	+= $iva_pendiente;
				}
				$xRecP->setNuevoMvto($fecha_de_pendiente, $monto_pendiente, $clave_operacion, $parcialidad, $mobserva );
				$total_pendientes	+= $monto_pendiente;
			}
		}
		//setLog("Total $total_pendientes ");
		//Arreglo para no Eliminar el Recibo de pendientes
		if ( $recibo_pendientes != $xPlan ){
			$xLog->add("WARN\tPLAN_DIF\tTotal Pendientes a $total_pendientes\r\n");
			$xRecP->init();
			$xRecP->setSumaDeRecibo($total_pendientes);
			$xRecP->setFinalizarRecibo(true);
			$xLog->add($xRecP->getMessages(OUT_TXT), $xLog->DEVELOPER);
		}
		//la Parcialidad se resetea a la anterior
		if ( $total_pendientes > TOLERANCIA_SALDOS ){
			$nueva_parcialidad	= $parcialidad - 1;
			$xLog->add("WARN\tPARC_REST\tParcialidad regresada de $parcialidad a $nueva_parcialidad por pendientes $total_pendientes\r\n");
		}
	}
}

//echo "<!--	INSERTAR MOVIMIENTOS DEL RECIBO -->";

$baseM140 			= $m[142] + $m[420] + $m[421] + $m[411] + $m[415] + $interescorriente + $interesremanente;
$baseM141 			= $m[1005] + $m[431] + $m[432] + $m[433] + $m[434] + $interesmoroso + $interesvencido;
$MONTO_PAGO_CBZA 	= $m[OPERACION_CLAVE_CARGOS_COBRANZA];																//Cargos por COBRANZA
$baseM246 			= $m[OPERACION_CLAVE_CARGOS_VARIOS];
$baseM120 			= $capital + $m[410];
$baseM151 			= $m[1201] + $m[413] + $ivaintereses + $m[1203];
$baseM152 			= $m[1202] + $ivaotros + $m[1204];
$baseM303 			= $m[801] + $m[802] + $m[803];
$baseM220 			= $m[412];
$baseM352 			= $interesanticipado;
//================================================================ PARCHE 09/Mayo/2016
	//$xCred->setAbonoCapital($baseM120, $parcialidad, $cheque, $tipo_pago, $recibo_fiscal, $observaciones, $grupo, $fecha_operacion, $recibo_pago);
/*Tipo de Operacion 146.- Pago Fortaleciemiento ha cambiado a 246 2017-01-31  */ 
setNuevoMvto($socio, $solicitud, $recibo_pago, $fecha_operacion,  $baseM120, OPERACION_CLAVE_PAGO_CAPITAL, $parcialidad, $observaciones);
setNuevoMvto($socio, $solicitud, $recibo_pago, $fecha_operacion,  $baseM140, OPERACION_CLAVE_PAGO_INTERES, $parcialidad, $observaciones);
setNuevoMvto($socio, $solicitud, $recibo_pago, $fecha_operacion,  $baseM141, OPERACION_CLAVE_PAGO_MORA, $parcialidad, $observaciones);
setNuevoMvto($socio, $solicitud, $recibo_pago, $fecha_operacion,  $MONTO_PAGO_CBZA, OPERACION_CLAVE_PAGO_CBZA, $parcialidad, $observaciones); //comision
setNuevoMvto($socio, $solicitud, $recibo_pago, $fecha_operacion,  $baseM246, OPERACION_CLAVE_PAGO_COM_VARIAS, $parcialidad, $observaciones);
setNuevoMvto($socio, $solicitud, $recibo_pago, $fecha_operacion,  $baseM151, OPERACION_CLAVE_PAGO_IVA_INTS, $parcialidad, $observaciones);
setNuevoMvto($socio, $solicitud, $recibo_pago, $fecha_operacion,  $baseM152, OPERACION_CLAVE_PAGO_IVA_OTROS, $parcialidad, $observaciones);
setNuevoMvto($socio, $contrato_captacion, $recibo_pago, $fecha_operacion,  $baseM220, OPERACION_CLAVE_PAGO_CAPTACION, $parcialidad, $observaciones);

if($baseM303 >0){
	$BonMora	= setNoMenorQueCero($m[801], 2);
	$BonInt		= setNoMenorQueCero($m[802], 2);
	$BonOtro	= setNoMenorQueCero($m[OPERACION_CLAVE_BON_VARIAS], 2);
	if($BonInt >0){
		$xCred->setBonificacionesAfectado($BonInt, true, 802, $parcialidad);
	}	
	if($BonMora >0){
		$xCred->setBonificacionesAfectado($BonMora, true, 801, $parcialidad);
	}
	if($BonOtro >0){
		$xCred->setBonificacionesAfectado($BonOtro, true, 803, $parcialidad);
	}
}
//TODO:  terminar planes de pago
//------------------------------- Amortizacion de letras -------------------------
//--------------------------------------------------------------------------------
//================================ Int Capital ===========================================
if($baseM140 > 0){
	if($xCred->isAFinalDePlazo() == false){
		if($LPlan == null){ $LPlan = $xCred->getOPlanDePagos(); }
		$amortizable	= setNoMenorQueCero(($baseM140 - $parcialidad_interes));
		$NLetra			= $parcialidad+1;
		$LPlan->setPagosAutorizados($xCred->getPagosAutorizados());
		if($amortizable > 0.01){
			if($xCred->getPagosSinCapital() == true){
				$nueva_parcialidad = $LPlan->setAmortizarLetras($amortizable, $NLetra, OPERACION_CLAVE_PLAN_INTERES);
				//
			} else {
				$LPlan->setAmortizarLetras($amortizable, $NLetra, OPERACION_CLAVE_PLAN_INTERES);
			}
		}
	}	
}
//================================ Cred Capital ===========================================
if($baseM120 > 0 ){
	if($xCred->isAFinalDePlazo() == false){
		if($LPlan == null){ $LPlan = $xCred->getOPlanDePagos(); }
		$amortizable	= setNoMenorQueCero(($baseM120 - $parcialidad_capital));
		$NLetra			= $parcialidad+1;
		$LPlan->setPagosAutorizados($xCred->getPagosAutorizados());
		if($amortizable>0.01){
			if($xCred->getPagosSinCapital() == false){
				//Amortizar Capital
				$nueva_parcialidad =  $LPlan->setAmortizarLetras($amortizable, $NLetra, OPERACION_CLAVE_PLAN_CAPITAL);
			} else {
				$amortizable		= $baseM120;
				//Disminuir la ultima letra
				$LPlan->setAmortizarLetras($amortizable, $xCred->getPagosAutorizados(), OPERACION_CLAVE_PLAN_CAPITAL);
			}
		}
		
	}
}
//==================================== Ahorro ==============================================
if($baseM220 > 0){
//*************************** insertar SDPM ********************************************
$SQLX = "SELECT * FROM captacion_cuentas WHERE numero_cuenta = $contrato_captacion ";
	$dCap 					= obten_filas($SQLX);
	$fecha_ultima 			= $dCap["fecha_afectacion"];
	$saldo_cuenta			= $dCap["saldo_cuenta"];
	$monto_sdpm				= $dCap["ultimo_sdpm"];
	$tasa					= $dCap["tasa_otorgada"];
	$dias_mes				= date("j", strtotime($fecha_operacion));

	$diastrans 				= restarfechas($fecha_operacion, $fecha_ultima);
	if(($diastrans < 0) OR ($diastrans>$dias_mes)){
		$diastrans 			= restarfechas($fecha_operacion, date("Y-m", strtotime($fecha_operacion)) . "-01");
	}
	if ($diastrans > 0){
		$monto_sdpm 		= $saldo_cuenta * $diastrans;
	}
		//Guarda los datos del SDPM
		$ejer 		= date("Y", strtotime($fecha_operacion));
		$peri 		= date("m", strtotime($fecha_operacion));
		
		
		$sqlUS 		= "INSERT INTO captacion_sdpm_historico
							(ejercicio, periodo, cuenta, fecha, dias, tasa, monto, recibo)
		    				VALUES( $ejer, $peri,$contrato_captacion, '$fecha_operacion',
		    				$diastrans, $tasa, $monto_sdpm, $recibo_pago) ";
		$xLog->add("WARN\tAHORRO\t$socio\t$contrato_captacion\tAgregando y Actualizando el SDPM por $monto_sdpm del $fecha_ultima al $fecha_operacion agregando al saldo $baseM220\r\n", $xLog->DEVELOPER);
		//Afectar el Saldo de la Cuenta de Captacion
		$sqlUAh 	= "UPDATE captacion_cuentas
                            SET  fecha_afectacion='$fecha_operacion',
                            saldo_cuenta=(saldo_cuenta +($baseM220)),
                            ultimo_sdpm=$monto_sdpm WHERE numero_cuenta=$contrato_captacion ";
		
		$xQL->setRawQuery($sqlUAh);
		$xQL->setRawQuery($sqlUS);
		
}

//**************************************************************************************
$arrUCredito		= array();
setNuevoMvto($socio, $solicitud, $recibo_pago, $fecha_operacion,  $baseM303, 303, $parcialidad, $observaciones);
setNuevoMvto($socio, $solicitud, $recibo_pago, $fecha_operacion,  $baseM352, 352, $parcialidad, $observaciones);

//======================================== PURGAR LOS DEMAS MOVIENTOS Y GENERAR CARGOS X
unset($m[142]); unset($m[420]); unset($m[421]); unset($m[411]); unset($m[415]);
unset($m[1005]); unset($m[431]); unset($m[432]); unset($m[433]); unset($m[434]);
unset($m[601]); unset($m[600]);
unset($m[410]);
unset($m[1201]); unset($m[413]); unset($m[1203]);
unset($m[1202]); unset($m[1204]); 
unset($m[801]); unset($m[802]); unset($m[803]);
unset($m[412]);
//====================================== Los remanentes se envian a sus respectivos IDS
foreach ($m as $idx => $vv){
	setNuevoMvto($socio, $solicitud, $recibo_pago, $fecha_operacion,  $vv, $idx, $parcialidad, $observaciones);
} 

//si hay abono a capital
$actualizar_parcialidad						= true;
  
  	/**
  	 * Si cambio la Fecha de Abono de capital
  	 * se Actualiza la fecha de Ultima Operacion
  	 */
	if ($baseM120 > 0){
		
		//TODO : Unificar
		//calcular cambios de capital		//500 + ((4500 - 5000) [-500] *-1)[1000] / 5000 = 25%
											//1 - (500 / 4500 [0.1111])
		$proporcion_de_cambios					= 1 - ($baseM120 / $xCred->getSaldoActual()); 
		//calcular Interes diario
		$interes_diario							= $xCred->getInteresDiariogenerado() - ($xCred->getInteresDiariogenerado()  * $proporcion_de_cambios);
		$arrUCredito["interes_diario"]			= number_format($interes_diario,4);
		$arrUCredito["fecha_ultimo_capital"]	= $fecha_operacion;
		$xLog->add("WARN\tCambios de interes por capital queda en $interes_diario y proporcion de $proporcion_de_cambios\r\n", $xLog->DEVELOPER);
		if($xCred->getPagosSinCapital() == true){
//=============================== Ajuste de Interes
			$xPCred	= new cParcialidadDeCredito($socio, $solicitud, $parcialidad);
			$xPCred->setActualInteresPropCred($socio, $solicitud, $nueva_parcialidad, $baseM120, $xCred->getSaldoActual());
			/*$sqlTM	= "UPDATE operaciones_mvtos SET afectacion_real=(afectacion_real*$proporcion_de_cambios), 
					afectacion_cobranza =(afectacion_cobranza*$proporcion_de_cambios), 
					afectacion_estadistica=(afectacion_estadistica*$proporcion_de_cambios) WHERE socio_afectado=$socio AND docto_afectado=$solicitud AND (tipo_operacion=" . OPERACION_CLAVE_PLAN_INTERES . " OR tipo_operacion=" . OPERACION_CLAVE_PLAN_IVA . ") " ;
			my_query($sqlTM);*/
			$xLog->add("WARN\tAjustando Intereses de plan de pagos solo interes a una Proporcion $proporcion_de_cambios\r\n", $xLog->DEVELOPER);
		}
		//nuevo saldo
		$nuevo_saldo						= $xCred->getSaldoActual() - $baseM120;
		$nuevo_saldo						= ($nuevo_saldo < 0 ) ? 0 : $nuevo_saldo;
		$arrUCredito["saldo_actual"]		= $nuevo_saldo;
	}
	if(setNoMenorQueCero($baseM120) <=0 ){
		$actualizar_parcialidad				= false;
	}
	//Saldo ints devengados aldo_actual=(saldo_actual - ($baseM120)),
	//monto_parcialidad 	$arrUCredito["saldo_actual"]
	
	if($xCred->getPagosAutorizados() <= $nueva_parcialidad){
		if($nuevo_saldo	> TOLERANCIA_SALDOS){
			$xLog->add("WARN\tLa parcialidad no se Actualiza, el saldo queda a $nuevo_saldo de la Parcialidad $nueva_parcialidad\r\n", $xLog->DEVELOPER);
			$actualizar_parcialidad		= false;
		}
	}
	if( $nueva_parcialidad < $xCred->getPeriodoActual() ){
		$xLog->add("WARN\tLa Parcialidad $nueva_parcialidad no se mueve por ser menor al actual " . $xCred->getSaldoActual() . "\r\n", $xLog->DEVELOPER);
		$actualizar_parcialidad		= false;			
	}	
	if($actualizar_parcialidad == true){
		$arrUCredito["ultimo_periodo_afectado"]	= $nueva_parcialidad;
		//Corregir
		
	}
	$arrUCredito["fecha_ultimo_mvto"]			= $fecha_operacion;
	$arrUCredito["interes_normal_pagado"]		= $xCred->getInteresNormalPagado() + $baseM140;
	$arrUCredito["interes_moratorio_pagado"]	= $xCred->getInteresMoratorioPagado()	+ $baseM141;
	$xCred->setUpdate($arrUCredito);
//---------------------------- Agrega Movimientos extras ---------------------------------
if($interesanticipado!=0){
	    $tds .= "<tr>
                <th></th>
                <td>Compensacion Intereses Cobrado por Ant</td>
                <td class='mny'>" . getFMoney($interesanticipado) . "</td>
            </tr>";
         $SQL_ICA = "UPDATE operaciones_mvtos SET estatus_mvto=60, docto_neutralizador=$recibo_pago WHERE tipo_operacion=351 AND socio_afectado=$socio AND estatus_mvto=30";
					my_query($SQL_ICA);
//setPolizaProforma($recibo_pago, 352, $interesanticipado, $socio, $solicitud, TM_CARGO);
}

if($interescorriente!=0){
	    $tds .= "<tr><th></th>
                <td>Recalculo de Interes Corriente</td>
                <td class='mny'>" . getFMoney($interescorriente) . "</td></tr>";
//setPolizaProforma($recibo_pago, 436, $interescorriente, $socio, $solicitud, TM_ABONO);
}
if($interesmoroso!=0){
	    $tds .= "<tr><th></th>
                <td>Recalculo de Interes Moroso</td>
                <td class='mny'>" . getFMoney($interesmoroso) . "</td></tr>";
//setPolizaProforma($recibo_pago, 436, $interesmoroso, $socio, $solicitud, TM_ABONO);
}
if($interesremanente!=0){
	    $tds .= "<tr><th></th>
                <td>Recalculo de Interes Remanente</td>
                <td class='mny'>" . getFMoney($interesremanente) . "</td></tr>";
//setPolizaProforma($recibo_pago, 437, $interesremanente, $socio, $solicitud);
}
if($interesvencido!=0){
	    $tds .= "<tr><th></th>
                <td>Recalculo de Interes Vencido</td>
                <td class='mny'>" . getFMoney($interesvencido) . "</td></tr>";
//setPolizaProforma($recibo_pago, 437, $interesvencido, $socio, $solicitud, TM_ABONO);
}
if($ivaintereses!=0){
	    $tds .= "<tr><th></th>
                <td>Recalculo de I.V.A. por Intereses</td>
                <td class='mny'>" . getFMoney($ivaintereses) . "</td></tr>";
//setPolizaProforma($recibo_pago, 1203, $ivaintereses, $socio, $solicitud, TM_ABONO);
}
if($ivaotros!=0){
	    $tds .= "<tr><th></th>
                <td>Recalculo de I.V.A. por Otros Conceptos</td>
                <td class='mny'>" . getFMoney($ivaotros) . "</td></tr>";
//setPolizaProforma($recibo_pago, 1204, $ivaotros, $socio, $solicitud, TM_ABONO);
}
if($capital!=0){
	$tds .= "<tr><th></th>
                <td>Capital Directo Pagado</td>
                <td class='mny'>" . getFMoney($capital) . "</td></tr>";
	//setPolizaProforma($recibo_pago, OPERACION_CLAVE_PAGO_CAPITAL, $capital, $socio, $solicitud, TM_ABONO);
}

//-------------------------------- ELIMINAR EL RECIBO DE PENDIENTES ------------------------
//Si los Pendientes son CERO y el Numero de Recibo es diferente al dePendientes
if ( ($total_pendientes	<= TOLERANCIA_SALDOS) AND ($recibo_pendientes != $xPlan) ){
	$cRP 			= new cReciboDeOperacion( RECIBOS_TIPO_PAGOSPENDS, true, $recibo_pendientes);
	if($cRP->init() == true){
		$cRP->setRevertir();
		$xLog->add("========\tEliminando Recibo de Pendientes\t==========\r\n", $xLog->DEVELOPER);
		$xLog->add($cRP->getMessages(OUT_TXT), $xLog->DEVELOPER);
	}
}
$xCred->init();
	


//----------------------------------- Quitar Cargos de Cobranza -----------------------------
if($MONTO_PAGO_CBZA>0){
	$xPagos	= new cCreditosPagos();
	$xCred->setGastosCobranzaAfectado($MONTO_PAGO_CBZA, true);
}
//---------------------------------- Finalizar recibo de Cobro ------------------------------
	$oRec = new cReciboDeOperacion(RECIBOS_TIPO_PAGO_CREDITO, true);
	$oRec->setNumeroDeRecibo($recibo_pago);
	//Agrega un abono a la ultima letra
	if($procesado == SYS_AUTOMATICO){
		//$eqvalor			= round(($monto_a_operar - $totalAutomatico),2);
		$abonar_al_final	= setNoMenorQueCero(($monto_a_operar - $totalAutomatico), 2);
		if($abonar_al_final > TOLERANCIA_SALDOS){
			$xLog->add("ERROR\tRECIBO $recibo_pago ... $monto_a_operar ....( $totalAutomatico ).... $total_recibo .... [$abonar_al_final] \r\n", $xLog->DEVELOPER);
			$xCred->setAbonoCapital($abonar_al_final, $xCred->getPagosAutorizados(), DEFAULT_CHEQUE, 	$tipo_pago, $recibo_fiscal, $observaciones, $grupo, $fecha_operacion, $recibo_pago );
			$total_recibo	+= $abonar_al_final + $totalAutomatico;
		}
	}
	$oRec->init();
	$oRec->setSumaDeRecibo($total_recibo);
	$oRec->setFinalizarRecibo(true);


if($procesado != SYS_AUTOMATICO){
	
	$xFRM->addHElem( $oRec->getFicha(true) );
	$total_letras 	= convertirletras($total_recibo);
	//$xBtn			= new cHButton("");
	//$xFRM->addToolbar( $xBtn->getRegresar("./frmcobrosdecreditos.php", true)  );
	//$xFRM->addToolbar( $xBtn->getBasic("TR.Imprimir Recibo", "printrec()", $xFRM->ic()->IMPRIMIR, "cmdPrint", false) );
	///$xFRM->addImprimir("", "printrec()");
	
	if($xCred->getClaveDeEmpresa() != FALLBACK_CLAVE_EMPRESA){
		$xCobDet		= new cEmpresasCobranzaDetalle(false, false);
		$xCobDet->initByCreditoID($solicitud, $parcialidad);
		if($total_recibo >= $xCobDet->getMontoEnviado()){
			$xCobDet->setPagado("[$recibo_pago] $observaciones [$fecha_operacion]", $recibo_pago);
			$xLog->add("WARN\tEliminar Operacion de Empresa $solicitud, $parcialidad, $recibo_pago [$fecha_operacion] \r\n", $xLog->DEVELOPER);
		} else {
			$mMontoActualLetra	= ($xCobDet->getMontoEnviado()-$total_recibo);
			$xCobDet->setActualizarMontoEnviado($mMontoActualLetra, "[$recibo_pago] $observaciones [$fecha_operacion]", $recibo_pago);
			$xLog->add("WARN\tNo se actualiza Operacion de Empresa $solicitud - $parcialidad - $recibo_pago [$fecha_operacion] \r\n", $xLog->DEVELOPER);
		}
		
	}
	$xCred->setCuandoSeActualiza();
	$xCred->setRevisarSaldo();
	$saldo	= setNoMenorQueCero($xCred->getSaldoActual());
	
	$xNRec->setMontoHistorico($saldo);
	if($saldo == 0){
		$xFRM->OButton("TR.CARTA_FINIQUITO", "var xC=new CredGen();xC.getFormatoFiniquito($solicitud)", $xFRM->ic()->LEGAL);
	}

	if ( MODO_DEBUG == true ){
		$xLog->add($oRec->getMessages(OUT_TXT), $xLog->DEVELOPER);
		$xLog->add($xCred->getMessages(OUT_TXT), $xLog->DEVELOPER);
		
		$xFRM->addLog($xLog->getMessages());
	}
		
	$xFRM->addHTML("<table><tbody>$tds</tbody><tfoot><tr><th></th><th>TOTAL</th><th class='mny'>" . getFMoney($total_recibo) . "</th></tr><tr><th colspan=\"3\" class='warn'>$total_letras</th></tr></tfoot></table>");

	$xFRM->addJsInit("jsInitForm();");
	
echo $xFRM->get();
echo $oRec->getJsPrint(true);
?>
</body>
<script>
var Wo	= new Gen();
function jsCloseRecibo(){ window.close(); }
function getEdoCtaCredito(){ Wo.w({url: "../rpt_edos_cuenta/rptestadocuentacredito.php?credito=" + mCredito}); }
function jsInitForm(){
	if(window.parent.jsEnablePrint){
		window.parent.jsEnablePrint();
	}
}
</script>
</html>
<?php
} else {
	$xCred->setCuandoSeActualiza();
	$xCred->setRevisarSaldo();
	$saldo	= setNoMenorQueCero($xCred->getSaldoActual());
	$xNRec->setMontoHistorico($saldo);
	
	$xho	= new cHObject();
	header("Content-type: text/xml");
	//PRINT XML
	
	echo "<?xml version =\"1.0\" ?>\n<resultados>" . $xLog->getMessages() . "</resultados>";
	
}
//=========================== Amortizar Rentas

if($xCred->getEsArrendamientoPuro() == true){
	$xRenta	= new cLeasingRentas();
	$xRenta->setAmortizarRenta($sumaoperaciones, $parcialidad, $recibo_pago, $xCred->getClaveDeCredito());
	
	/*$xLeas	= new cCreditosLeasing($xCred->getClaveDeOrigen());
	if($xLeas->init()){
		
	}*/
}


if($xCred->getSaldoActual() <= TOLERANCIA_SALDOS){
	$xCred->setCreditoPagado($fecha_operacion);
}

?>