<?php
/**
* @author Balam Gonzalez Luis Humberto
* @package creditos.formas
* @since 1.8 - 03/04/2008
* @since 2.0 - 09/09/2014
* @version 1.8.0
*  Archivo de Guardado de Ministraciones de Credito
* 		- 03/04/2008 -
* 		- 27/05/2008 - Se agrego el Reporte de Mandato
* @version 2.0.0
* 		- Reescritura total
* 		- Nuevas formas
* 		- nuevo recibos de abono a capital y comisiones
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
$xHP				= new cHPage("TR.Desembolso de Creditos");
$xBtn				= new cHButton();
$uPagare			= "";
$uContrato 			= "";
$msg				= "";
$recibo				= SYS_CERO;
$xLog				= new cCoreLog();
$xQL				= new MQL();
$xLi				= new cSQLListas();
/* ------------------------------ CONTROL DE USUARIOS  -----------------------------------------*/

//----------------------------------------------------------------------------------------------
$idsolicitud 		= parametro("idsolicitud", 0, MQL_INT);
$idsocio			= parametro("idsocio", 0, MQL_INT);
$cheque 			= parametro("idnumerocheque", DEFAULT_CHEQUE, MQL_INT);
$observaciones 		= parametro("idobservaciones");
$cuenta_cheques 	= parametro("idcodigodecuenta", DEFAULT_CUENTA_BANCARIA, MQL_INT);

$monto_cheque1		= parametro("idmontocheque", 0, MQL_FLOAT);
$cuenta_cheques2 	= DEFAULT_CUENTA_BANCARIA;
$cheque2 			= 0;

$recibo_fiscal 		= parametro("idfoliofiscal");
$fecha				= parametro("idfechaactual", false, MQL_DATE);
$creditodescontado	= parametro("idcreditodescontado", 0, MQL_INT);
$montocreditodesc	= parametro("idmontocreditodescontado", 0, MQL_FLOAT);
$montocomision		= parametro("idmontocomisiondescontado", 0, MQL_FLOAT);
$sumadescuentos		= parametro("idsumadescuentos", 0, MQL_FLOAT);
$idcuentadeposito	= parametro("idcuentaspersona", 0, MQL_INT);
$idestrans			= parametro("idestransferencia", false, MQL_BOOL);
$sucess				= false;
$xHP->init();
$xFRM				= new cHForm("frmacciones", "./");
$xBase				= new cBases(BASE_IVA_OTROS);
$TipoPagoCobros		= TESORERIA_COBRO_DESCTO;
$TipoPagoPagos		= TESORERIA_PAGO_CHEQUE;

if($idestrans == true){
	$TipoPagoPagos	= TESORERIA_PAGO_TRANSFERENCIA; 	
}




$xBase->init();
if($idsolicitud <= DEFAULT_CREDITO OR $monto_cheque1 <= TOLERANCIA_SALDOS){
	$xLog->add("ERROR\tEl Credito es $idsolicitud y el Monto del Cheque $monto_cheque1 NO SE GUARDA\r\n");
} else {
	$xCred			= new cCredito($idsolicitud, $idsocio);
	$xCred->init();
	if($xCred->getEstadoActual() == CREDITO_ESTADO_AUTORIZADO){
		$persona	= $xCred->getClaveDePersona();
		$recibo		= $xCred->setMinistrar($recibo_fiscal, $cheque, $monto_cheque1, $cuenta_cheques, $cheque2, $cuenta_cheques2, $observaciones, $fecha, false, $TipoPagoPagos );
		
		if(setNoMenorQueCero($recibo) > 0){
			$sucess			= true;			
			$xFRM->OButton("TR.RECIBO DE MINISTRACION", "var xC=new CredGen();xC.getImprimirReciboDeDesembolso($idsolicitud)", $xFRM->ic()->REPORTE, "id4");
			
			$uPagare 		= "elUrl='" . $xCred->getOProductoDeCredito()->getPathPagare($idsolicitud) . "';";
			$uContrato 		= "esUrl='" . $xCred->getPathDelContrato() . "';";
			
	
			if($sumadescuentos > 0){
				$xRec				= new cReciboDeOperacion(RECIBOS_TIPO_PAGO_CREDITO, true);
				$idrecibo			= $xRec->setNuevoRecibo($idsocio, $idsolicitud, $fecha, PERIODO_CERO, RECIBOS_TIPO_PAGO_CREDITO, $observaciones, $cheque, $TipoPagoCobros, $recibo_fiscal);
				//Se abona diferente porque se relaciona con otro credito	
				if($creditodescontado >= DEFAULT_CREDITO AND $montocreditodesc >0){
					$xDCred		= new cCredito($creditodescontado);
					if($xDCred->init() == true){
						$saldodesc			= $xDCred->getSaldoActual($fecha);
						$cuotadescontada	= $xDCred->getPeriodoActual();
						if($saldodesc < $montocreditodesc ){ $montocreditodesc	= $saldodesc; } //No mayor al saldo
						
						$idrecibocap			= $xDCred->setAbonoCapital($montocreditodesc, $cuotadescontada, $cheque, $TipoPagoCobros, $recibo_fiscal, $observaciones, false, $fecha);
						if(setNoMenorQueCero($idrecibocap) > 0){
							$xRecCapt	= new cReciboDeOperacion(false, true, $idrecibocap); $xRecCapt->init();
							if($xRecCapt->setFinalizarRecibo(true) == true){
								$xFRM->OButton("TR.Recibo de pago", "jsImprimirReciboCapital()", "imprimir");
								$xFRM->addHTML($xRecCapt->getJsPrint(true, "jsImprimirReciboCapital"));
								//finalizar tesoreria
								$xRecCapt->setFinalizarTesoreria(array(
									"cuenta" => $cuenta_cheques,
									"cheque" => $cheque
								));
								
							}
							$xLog->add( $xRecCapt->getMessages() , $xLog->DEVELOPER);
						}
					}
					//TODO: Agregar operacion Bancaria
				}

				$rs			= $xQL->getDataRecord($xLi->getListadoDeCargosPorProductoCred($xCred->getClaveDeProducto()));
				
				$montoIVA	= 0;
				$BaseIVA	= 0;
				//TODO. Operacion de IVA Incluido o No incluido
				
				foreach ($rs as $rw){
					$xLCosto		= new cCreditos_productos_costos();
					$xLCosto->setData($xLCosto->query()->initByID($rw["clave"]));
					$idx			= "idm-" . $xLCosto->clave_de_operacion()->v();
					$monto			= parametro($idx,0, MQL_FLOAT);
					if($monto > 0){
						if($xLCosto->clave_de_operacion()->v() == OPERACION_CLAVE_PAGO_CAPTACION){
							if($idcuentadeposito == DEFAULT_CUENTA_CORRIENTE OR $idcuentadeposito <= 0){
								$xSoc	= new cSocio($persona);
								$xSoc->init();
								$idcuentadeposito	= $xSoc->getCuentaDeCaptacionPrimaria(CAPTACION_TIPO_VISTA, CAPTACION_PRODUCTO_GARANTIALIQ);
								if($idcuentadeposito == 0){
									$xCta				= new cCuentaDeCaptacion(false);
									$idcuentadeposito	= $xCta->setNuevaCuenta(DEFAULT_CAPTACION_ORIGEN, CAPTACION_PRODUCTO_GARANTIALIQ, $persona, $observaciones, $idsolicitud, "", "", 0, $fecha);
								}
							}
							$xCta		= new cCuentaALaVista($idcuentadeposito);
							if($xCta->init() == true){
								$recibo_de_ahorro = $xCta->setDeposito($monto, $cheque, $TipoPagoCobros, "", $observaciones, false, $fecha, false, false, $cuenta_cheques);
								//Agregar Impirmir recibo
								$xARec	= new cReciboDeOperacion(false, false, $recibo_de_ahorro);
								if($xARec->init() == true){
									if($xARec->setFinalizarRecibo(true) == true){
										$xFRM->addImprimir("TR.RECIBO AHORRO", "jsImprimirRecibo2()");
										$xFRM->addHTML($xARec->getJsPrint(true, "jsImprimirRecibo2"));
										$xARec->setFinalizarTesoreria(array(
												"cuenta" => $cuenta_cheques,
												"cheque" => $cheque
										));
									}
								}								
							}
						} else {
							$TasaIVA	= ($xBase->getIsMember($xLCosto->clave_de_operacion()->v()) == true) ? $xCred->getTasaIVAOtros() : 0;
							$factorIVA	= (1/(1+$TasaIVA));
							$monto		= setNoMenorQueCero( ($monto * $factorIVA),2);
							$montoIVA	+= setNoMenorQueCero(($monto*$TasaIVA),2);
							$xRec->setNuevoMvto($fecha, $monto, $xLCosto->clave_de_operacion()->v(), PERIODO_CERO, $observaciones, 1, TM_ABONO, $persona);
						}
					}
				}
				if($montoIVA >0){
					$xRec->setNuevoMvto($fecha, $montoIVA, OPERACION_CLAVE_PAGO_IVA_OTROS, PERIODO_CERO, $observaciones, 1, TM_ABONO, $persona);
				}
				if($xRec->setFinalizarRecibo(true) == true){
					$xFRM->OButton("TR.Recibo de Cargos", "jsImprimirRecibo()", "imprimir");
					$xFRM->addHTML($xRec->getJsPrint(true));
					$xRec->setFinalizarTesoreria(array(
							"cuenta" => $cuenta_cheques,
							"cheque" => $cheque
					));
				}
				$xLog->add( $xRec->getMessages() , $xLog->DEVELOPER);				
			} //end suma descuentos
			//Buttons
			$xFRM->OButton("TR.IMPRIMIR PAGARE", "var xC=new CredGen();xC.getImprimirPagare($idsolicitud)", $xFRM->ic()->DINERO);
			$xFRM->OButton("TR.IMPRIMIR CONTRATO", "var xC=new CredGen();xC.getImprimirContrato($idsolicitud)", $xFRM->ic()->CONTRATO);
			$xFRM->OButton("TR.IMPRIMIR POLIZA", "var xC=new CredGen();xC.getImprimirPolizaCheque($idsolicitud)", $xFRM->ic()->IMPRIMIR);

			$xFRM->OButton("TR.IMPRIMIR MANDATO", "var xC=new CredGen();xC.getImprimirMandato($idsolicitud)", $xFRM->ic()->IMPRIMIR);
			$xCred->init();
			$xFRM->addHTML($xCred->getFicha(true, "", false, true) );
		} else {
			$xLog->add("ERROR\tEl Credito no se Otorga, el Recibo $recibo No se agrego\r\n", $xLog->DEVELOPER);
		}
		
		
	} else {
		//ESTADO NO APPLICABLE
		//echo JS_CLOSE;
		$xLog->add("ERROR\tEl Credito es $idsolicitud y no se puede volver a AFECTAR\r\n");
	}
	$xLog->add($xCred->getMessages());
	
}
if($sucess == false){
	$xFRM->addAtras();
	$xFRM->addAvisoRegistroError($xLog->getMessages());
		
} else {
	$xFRM->addAvisoRegistroOK($xLog->getMessages());
}

$xFRM->addCerrar();

if(MODO_DEBUG == true){ $xFRM->addLog($xLog->getMessages()); }

echo $xFRM->get();

$xHP->fin();
?>
