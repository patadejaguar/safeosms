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
    require("../libs/jsrsServer.inc.php");
    include_once("../core/core.deprecated.inc.php");
	include_once("../core/entidad.datos.php");
	include_once("../core/core.config.inc.php");
	include_once("../core/core.common.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	
	include_once("../core/core.creditos.inc.php");
	include_once("../core/core.operaciones.inc.php");
	include_once("../core/core.captacion.inc.php");

jsrsDispatch("Common_97de3870795ecc1247287ab941d9719br, Common_b05dfbfaf8125673c6dc350143777ee1, Common_86d8b5015acb366cec42bf1556d8258a, Common_a92d70128878fe0e88050362ac797763, Common_d7823d8fb813a0f5223b914a9bf892d4, Common_c5fe0408555dbf392918c6f77a4d01b2, Common_1fff3ce8ffd3d2dfdee69fd04ba831ac");
/**
 * Funcion que califica a las Referencias
 *
 * @param string $params
 */
function Common_97de3870795ecc1247287ab941d9719br($params){
	$stdDiv		= STD_LITERAL_DIVISOR;
	$DPar		= explode($stdDiv, $params, STD_MAX_ARRAY_JS);

}
/**
 * Funcion que retorna la descripcion Corta de una Solicitud de Credito
 *
 * @param integer $params
 * @return string
 */
function Common_b05dfbfaf8125673c6dc350143777ee1($params){
	$solicitud 		= setNoMenorQueCero($params);
	$description	= "";
	$xCred		= new cCredito($solicitud);
	if( $xCred->init() == false ){
		$description = MSG_NO_PARAM_VALID;
	} else {
		$description	= $xCred->getShortDescription();
	}
return $description;
}
/**
 * Funcion que retorna el Primer Credito con prioridad
 *
 * @param integer $idsocio
 * @return string
 */
function Common_86d8b5015acb366cec42bf1556d8258a($strPedido){
	$ByTipo = " AND monto_autorizado > 0 ";//($tipo == "todos") ? "AND monto_autorizado > 0" : "  ";
	$socio	= DEFAULT_SOCIO;
	if( strpos($strPedido, STD_LITERAL_DIVISOR) !== false ){
		$D	= explode(STD_LITERAL_DIVISOR, $strPedido);
		$socio	= setNoMenorQueCero($D[0]);
		$tipo	= setNoMenorQueCero($D[1]);
	} else {
		$socio	= setNoMenorQueCero($strPedido);
	}
	switch($tipo){
		case CREDITO_ESTADO_SOLICITADO:
			$ByTipo	= " AND getEsCancelado(numero_solicitud) = 0 AND `estatus_actual` =  " . CREDITO_ESTADO_SOLICITADO;
			break;
		case CREDITO_ESTADO_AUTORIZADO:
			$ByTipo	= " AND `estatus_actual` =  " . CREDITO_ESTADO_AUTORIZADO;
			break;
		default:
			$ByTipo	= " AND monto_autorizado > 0 ";
			break;
	}
	$sqllc  = "SELECT numero_solicitud  FROM creditos_solicitud
			WHERE
				numero_socio=$socio
					$ByTipo
					ORDER BY saldo_actual DESC,
					fecha_vencimiento ASC,
					fecha_solicitud DESC
	 		LIMIT 0,1";
		$miro = 0;
		$miro = mifila($sqllc, "numero_solicitud");
		return $miro;
}
/**
 * Funcion que Guarda la Planeacion de Autorizacion Grupal
 * @param array $params Array compuesto de $grupo, $credito, $socio, $monto, $nota
 * @return string Mensaje de Afirmacion
 */
function Common_a92d70128878fe0e88050362ac797763($params){
 	$stdDiv		= STD_LITERAL_DIVISOR;
	$DPar		= explode($stdDiv, $params, STD_MAX_ARRAY_JS);
	//mensajes
	$msg		= "";
 	//grupo
 	$xTip		= new cTipos();
 	//
 	$grupo		= $xTip->cInt($DPar[0]);
 	//credito
 	$credito	= $xTip->cInt($DPar[1]);
	//Numero de Socia
	$socio		= $xTip->cInt($DPar[2]);
	//Monto Autorizado
	$monto		= $xTip->cFloat($DPar[3]);
	//observaciones
	$nota		= $xTip->cChar($DPar[4]);

	if ( isset($_SESSION["recibo_en_proceso"]) ){
		$recibo = $_SESSION["recibo_en_proceso"];
		$fecha	= fechasys();

		$xRec 	= new cReciboDeOperacion(40, false, $recibo);
		$xRec->setNumeroDeRecibo($recibo, true);
		$xRec->setGrupoAsociado($grupo);
		$xRec->setNuevoMvto($fecha, $monto, 50, 1, $nota, 1, false, $socio, $credito);
		$msg .= $xRec->getMessages();
	} else {
		$msg .= "NO_SE_HA_DEFINIDO_UN_RECIBO";
	}
	return $msg;
}
//Guardado de cedula grupal
function Common_d7823d8fb813a0f5223b914a9bf892d4(){

	$msg		= "";
	//$msg		.= $params;
 	//grupo
	if ( isset($_SESSION["recibo_en_proceso"]) ){
		$recibo = $_SESSION["recibo_en_proceso"];
		$xRec 	= new cReciboDeOperacion(40, false, $recibo);
		$xRec->setNumeroDeRecibo($recibo, false);
		$xRec->setForceUpdateSaldos(true);
		$xRec->setFinalizarRecibo(true);
		$msg .= $xRec->getMessages();
	} else {
		$msg .= "NO_SE_HA_DEFINIDO_UN_RECIBO";
	}
	return $msg;
}
//Agrega factura global
function Common_c5fe0408555dbf392918c6f77a4d01b2($params){
 	$stdDiv		= STD_LITERAL_DIVISOR;
	$DPar		= explode($stdDiv, $params, 10);

	//mensajes

 	//grupo
 	$xTip				= new cTipos();
 	//
 	$socio				= $xTip->cInt($DPar[0]);

 	$credito			= $xTip->cInt($DPar[1]);

	$letra				= $xTip->cInt($DPar[2]);

	$capital			= $xTip->cFloat($DPar[3]);
	$interes			= $xTip->cFloat($DPar[4]);
	$iva				= $xTip->cFloat($DPar[5]);
	$ahorro				= $xTip->cFloat($DPar[6]);

	$nota				= $xTip->cChar($DPar[7]);
	$numero				= $xTip->cInt($DPar[8]);
	$limit				= $xTip->cInt($DPar[9]);
	$msg		= "====================== MOVIMIENTO $numero DE $limit ====================\r\n";

	//Datos del Credito
	$Cred 					= new cCredito($credito, $socio);
	$Cred->initCredito();
	$DCred					= $Cred->getDatosDeCredito();
	$saldo					= $DCred["saldo_actual"] - $capital;
	$contrato_captacion 	= $DCred["contrato_corriente_relacionado"];
	$grupo					= $DCred["grupo_asociado"];
	$msg					.= $Cred->getMessages();

	if ( isset($_SESSION["recibo_en_proceso"]) ){
		$recibo 			= $_SESSION["recibo_en_proceso"];
		$fecha				= fechasys();
		$_SESSION["total_recibo_en_proceso"] += ($capital + $interes + $ahorro);

		$xRec 				= new cReciboDeOperacion(200, false, $recibo);
		$xRec->setNumeroDeRecibo($recibo, true);
			$xRec->setNumeroDeRecibo($recibo, true);
			$DRec			= $xRec->getDatosInArray();
			$cheque			= $DRec["cheque_afectador"];
			$tipopago		= $DRec["tipo_pago"];
			$recibofiscal	= $DRec["recibo_fiscal"];
			
		$xRec->setGenerarPoliza();

			$msg	.= "$socio\t$credito\tCREDITO\tMovimiento $i del Credito $credito del Socio $socio con Saldo $saldo\r\n ";
			$msg	.= "$socio\t$credito\tMONTOS\tCapital: $capital || Interes: $interes || Ahorro: $ahorro \r\n";
		//Agregando Capital
		if( $capital > 0 ){
			$xRec->setNuevoMvto($fecha, $capital, 120, $letra, $nota . ";SDO:" . $saldo, 1, TM_ABONO, $socio, $credito );
			$arrCred	= array("saldo_actual" => $saldo);
			$Cred->setUpdate($arrCred);
		}

		//Agregando Interes
		if( $interes > 0 ){
			$xRec->setNuevoMvto($fecha, $interes, 140, $letra, $nota, 1, TM_ABONO, $socio, $credito );
		}
		//agregando el IVA
		if( $interes > 0 ){
			$xRec->setNuevoMvto($fecha, $iva, 151, $letra, $nota, 1, TM_ABONO, $socio, $credito );
		}		
		//Agregando Ahorro
		if( $ahorro > 0 ){
			$xC = new cCuentaALaVista($contrato_captacion);

			if ( !isset($contrato_captacion) OR $contrato_captacion == CTA_GLOBAL_CORRIENTE OR $contrato_captacion == 0 ){
				$contrato_captacion = $xC->setNuevaCuenta(2, 1, $socio, "CUENTA_AUTOMATICA", $credito);
				$msg	.= "$socio\t$credito\tNuevaCta\tse dio de alta a la cuenta $contrato_captacion\r\n";
				//2011-nov-30 se agrego la actualizacion del contrato relacionado
				$arrCred	= array("contrato_corriente_relacionado" => $contrato_captacion);
				$Cred->setUpdate($arrCred);				
			}

			$xC 	= new cCuentaALaVista($contrato_captacion);

			$xC->setSocioTitular($socio);
			$xC->setReciboDeOperacion($recibo);
			$xC->setDeposito($ahorro, $cheque, $tipopago, $recibofiscal, $nota, $grupo, $fecha, $recibo );
			$msg	.= $xC->getMessages("txt");
			$msg	.= "$socio\t$credito\tAhorro\t El Saldo Quedo en " . $xC->getNuevoSaldo() . "\r\n";
		}

		$msg	.= "$socio\t$credito\tObservacion\t $nota\r\n";

		$msg .= $xRec->getMessages("txt");
	} else {
		$msg .= "NO_SE_HA_DEFINIDO_UN_RECIBO";
	}
	$xLog 		= new cFileLog("log_de_recibo_" . $recibo);
	$xLog->setWrite($msg);
	$xLog->setClose();
	$MsgEnd		= "";
	if ($numero == $limit ){
		$xRec->setForceUpdateSaldos();
		$xRec->setFinalizarRecibo(true);
		//$MsgEnd		.= "**** proceso terminado ****";
	}
	//retorna el id del control de origen para neutralizar
	return "-$numero";
}
/**
 * Funcion que Guarda el Monto de la Parcialidad
 * @param string $params Indica los parametros a cruzar
 **/
function Common_1fff3ce8ffd3d2dfdee69fd04ba831ac($params){


}

?>