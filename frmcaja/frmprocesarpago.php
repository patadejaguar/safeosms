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
$xHP		= new cHPage("TR.PROCESAMIENTO DE PAGO. PART 01", HP_FORM);
$xLng		= new cLang();
$params 	= ( isset($_GET["p"]) ) ? $_GET["p"] : false;
$jxc 		= new TinyAjax();
$xT			= new cTipos();
$xSQL		= new cSQLListas();
$xF			= new cFecha();
$ql			= new MQL();
$capital_original_de_letra		= 0;			//Monto original de capital de la letra
$msg		= "";
$xLog		= new cCoreLog();
$xRuls		= new cReglaDeNegocio();

$useMoraBD		= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_USE_MORA_BD);
$LockCobros		= $xRuls->getValorPorRegla($xRuls->reglas()->RECIBOS_COBRO_BLOQ);
$NoMoraNom		= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_NOMINA_NOMORA);
$NoCaptAntI		= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_NO_CAP_ANT_INT); //No pagar Capital antes de los Intereses
$NoArrPena		= $xRuls->getValorPorRegla($xRuls->reglas()->RECIBOS_NOARRASTRA_PENAS); //No arrastrar penas
$SoloExigibles	= $xRuls->getValorPorRegla($xRuls->reglas()->RECIBOS_LIQ_SOLO_EXIG); //No arrastrar penas

$EsPagoAdelantado	= false;
$EsLiquidacion		= false;

$interes_exigible	= 0;
$interes_a_pagar	= 0;

$vendedor			= parametro("vendedor", 0, MQL_INT);



function jsaActualizarLetra($persona, $credito, $letra, $monto){
	$monto	= setNoMenorQueCero($monto);
	$NLetra	= $letra +1;
	$xCred	= new cCredito($credito,$persona); $xCred->init();
	if($xCred->isAFinalDePlazo() == false){
		if($NLetra < $xCred->getPagosAutorizados() ){
			$xPlan	= new cPlanDePagos();
			$xPlan->initByCredito($credito);
			$xPlan->setOperarCapital($NLetra, "+$monto");
		}
	}
	return true;
}
function jsaAmortizarLetras($persona, $credito, $letra, $amortizable){
	return true;
}
function getLetras($total){ return ($total > 0) ? "(" . convertirletras($total) . ")" : ""; }
//Agrega parametros para cobros especiales
$jxc ->exportFunction('getLetras', array('idtotal'), "#id_letras_de_numeros");
$jxc ->exportFunction('jsaActualizarLetra', array('idpersona','idcredito','idletra', 'idcomodin'), "#id_mensajes");
$jxc ->exportFunction('jsaAmortizarLetras', array('idpersona','idcredito','idletra', 'idcomodin'), "#id_mensajes");
$jxc ->process(); //ninguno documento multiple descuento ||"ivaotros", "413", "1201", "1202", "1203", "1204"
$xBaGrav			= new cBases(7020);
$aActosDeIva		= $xBaGrav->getMembers_InArray();
$aActosNoGravados	= array(); /*array(
				"ninguno" => false,
				"documento" => false,
				"multiple" => false,
				"descuento" => false
				);*/
$generarIVA		= true;
/**
 * Array de Evaluacion del Tipo de Pago del recibo
 */

$aPagaCompleto		= array (
				OPERACION_PAGO_COMPLETO 	=> true,
				"plc"	=> false,
				"pli"	=> false,
				"ao"	=> false
				);
$pago_total			= false;
if (!$params) { exit();	};
$fecha_operacion	= ( isset($_SESSION[FECHA_OPERATIVA]) ) ? $_SESSION[FECHA_OPERATIVA] : fechasys();
$xLog->add("WARN\tFecha de Recibo a $fecha_operacion \r\n", $xLog->DEVELOPER);
$limParms 			= 7;

$DPar 				= explode("|", $params);
//socio-0//credito//Letra//periocidad//Monto a Operar-4//operacion//Tipo de Pago//Recibo Fiscal-7
$socio 				= $DPar[0];
$solicitud 			= $DPar[1];
$parcialidad 		= $DPar[2]; //$periocidad 		= $DPar[3];
$monto_a_operar 	= $DPar[4];
$operacion 			= $DPar[5];


if( $aPagaCompleto[$operacion] == true ){ 
	$pago_total 	= true ;
	$monto_a_operar	=   TESORERIA_MONTO_MAXIMO_OPERADO; 
	$msg	.= "WARN\tOperacion de Pago Completo\r\n";
	$EsLiquidacion	= true;
}
 
$mTipoPago			= (isset($DPar[6])) ?  trim($DPar[6]) : DEFAULT_TIPO_PAGO;
$mReciboFiscal		= (isset($DPar[7])) ? $DPar[7] : DEFAULT_RECIBO_FISCAL;


$procesado			= (isset($_GET["procesar"]) ) ? $_GET["procesar"] : "normal";
$cheque				= parametro("cheque", DEFAULT_CHEQUE); $cheque = parametro("idcheque", $cheque); $cheque = parametro("ccheque", $cheque);

$total				= 0;
$tdCobranza			= "";
$claveCbza			= OPERACION_CLAVE_CARGOS_COBRANZA;

$generarIVA			= ( isset($aActosNoGravados[ $mTipoPago ] )) ? false : true;
$montoCobranza		= 0;

for($ix=0; $ix <= $limParms; $ix++){
	if((!isset($DPar[$ix])) or ($DPar[$ix] == "")){
		saveError(210, $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Faltan Parametros($params) para el cobro, el Parametro $ix resulto " . $DPar[$ix] );
		header("location:../404.php?i=210");	//provocar error 404
		exit;
	}
}
	$xCred 					= new cCredito($solicitud, $socio);
	$xCred->init();
	$xLetra					= null;
	$tds 					= "";
	$esp_tds 				= "";
	$mMvtos 				= array();
	$alerts					= "";
	
	//Movimientos que afectan al Capital
	$cAfectCapital				= new cBases(2004);
	$aAfectCapital				= $cAfectCapital->getMembers_InArray();
	$mAfectCapital				= 0;
	$plan_de_pagos 				= false;
	$socio						= $xCred->getClaveDePersona();
	$dcredito 					= $xCred->getDatosDeCredito();
	$fecha_ministracion 		= $xCred->getFechaDeMinistracion();// $dcredito["fecha_ministracion"];
	$fecha_ult_mvto				= $xCred->getFechaUltimoDePago();
	$fecha_vencimiento			= $xCred->getFechaDeVencimiento();// $dcredito["fecha_vencimiento"];
	$numero_de_pagos			= $xCred->getPagosAutorizados();
	$estatus_del_credito		= $xCred->getEstadoActual();
	$saldo_actual				= $xCred->getSaldoActual(); $saldo_del_credito = $saldo_actual;
	$interes_anticipado 		= $dcredito["sdo_int_ant"];
	$grupo						= $xCred->getClaveDeGrupo();
	$OProducto					= $xCred->getOProductoDeCredito();
	$pagosautorizados			= $xCred->getPagosAutorizados();
	$tasa_iva					= ( $generarIVA == false) ? 0 : $xCred->getTasaIVA();
	

	$interes_normal_devengado		= $xCred->getInteresNormalDevengado();
	$interes_normal_pagado			= $xCred->getInteresNormalPagado();
	$interes_moratorio_pagado		= $xCred->getInteresMoratorioPagado();
	$interes_moratorio_devengado	= $xCred->getInteresMoratorioDev();
	$periocidad						= $xCred->getPeriocidadDePago();
	$ByLetra						= "";
	$solo_mora_corriente			= ($pago_total == true) ? true : false;
	
	//Corrige calculo de Interes.- Si es primer pago
	$DInteres						=  $xCred->getInteresDevengado($fecha_operacion, $parcialidad, false, $solo_mora_corriente);
	$interes_normal_calculado		=  $DInteres[SYS_INTERES_NORMAL];
	$interes_moratorio_calculado	=  $DInteres[SYS_INTERES_MORATORIO];
	$gastos_de_cobranza_calculado	=  $DInteres[SYS_GASTOS_DE_COBRANZA];
	$monto_penas					=  $DInteres[SYS_PENAS];
	
	$fecha_de_pago					= $fecha_vencimiento;

	//Inicializa la Parcialidad Historica
	if($xCred->isAFinalDePlazo() == false ){
		$xLetra						= new cParcialidadDeCredito($xCred->getClaveDePersona(), $xCred->getNumeroDeCredito(), $parcialidad);
		if($xLetra->init() == true){
			$fecha_de_pago			= $xLetra->getFechaDePago();
			$xLog->add("OK\tFecha de Pago establecida a $fecha_de_pago\r\n", $xLog->DEVELOPER);
			
			if($monto_penas <= 0){
				
				$xLetra->setTasaMora($xCred->getTasaDeMora());
				$xLetra->setIDProducto($xCred->getClaveDeProducto());
				$xLetra->setPeriocidadDePago($xCred->getPeriocidadDePago());
				if($xLetra->getEsAdelantadoSegunFecha($fecha_operacion) == true){
					$EsPagoAdelantado	= true;
					$NoCaptAntI			= false;//Puede pagar solo Interes
					$xLog->add("WARN\tPago Adelantado en $fecha_operacion por "  . $xLetra->getFechaDePago() .  "\r\n", $xLog->DEVELOPER);
				}
				
				
				$DPena				= $xLetra->setCalcularPenas_Y_Mora($fecha_operacion, true);
				$monto_penas		= $DPena[SYS_PENAS];
				$xLog->add("WARN\tPenas de la Parcialidad $parcialidad A $monto_penas\r\n", $xLog->DEVELOPER);
			}
			
		} else {
			$xLog->add("ERROR\tParcialidad $parcialidad no inicializada\r\n", $xLog->DEVELOPER);
			$xLog->add("ERROR\tNo existe el Periodo de Pago\r\n", $xLog->COMMON);
		}
	}
	

	

	//2015-10-10 Agregar Gastos de Cobranza Pendientes.
	$xPag							= $xCred->getOPagos();
	$gastos_de_cobranza_calculado	+= $xPag->getCargosDeCobranza();
	
	//$gastos_de_cobranza_calculado	= $
	
	//$xLog->add($DInteres[SYS_MSG], $xLog->DEVELOPER);
	


//=========================================================
//Datos del Respeto al Plan de Pagos
//======================================= VERIFICAR EL PLAN DE PAGOS =======================================
//======================================= END VERIFICATION =============================================
	$interes_normal					= 0;
	$interes_moratorio				= 0;

	$base_iva_otros					= 0;
	$base_iva_intereses 			= 0;

	$monto_iva_intereses			= 0;
	$monto_iva_otros				= 0;
	$monto_capital_operado			= 0;
	$cargos_mora					= 0;		//Solo evalua si existe la mora
	
	if($pago_total == true){
		$monto_penas				= $xCred->getPenasPorCobrar();
		$interes_exigible			= $xCred->getInteresNormalPorPagar($fecha_operacion, $parcialidad);
	}
	$interes_normal					= ( $interes_normal_devengado - $interes_normal_pagado ) + $interes_normal_calculado;
	$xLog->add("WARN\tINTS_N\t$interes_normal = ( $interes_normal_devengado - $interes_normal_pagado ) + $interes_normal_calculado\r\n", $xLog->DEVELOPER);
	$interes_moratorio				= ( $interes_moratorio_devengado - $interes_moratorio_pagado ) + $interes_moratorio_calculado;
	$xLog->add("WARN\tINTS_M\t$interes_moratorio = $interes_moratorio_devengado - $interes_moratorio_pagado ) + $interes_moratorio_calculado\r\n", $xLog->DEVELOPER);
//====================== INICIAR INTERESES
//Codigo que ejecuta la formula si se respeta el Plan de Pagos
if ( $xCred->isAFinalDePlazo() == false ){
	$xLog->add("WARN\tPARCIALIDAD\tEl pago es por parcialidad numero $parcialidad\r\n", $xLog->DEVELOPER);
	if ( $xCred->getRespetarPlanDePago() == false ){
		$interes_normal				= ( $interes_normal_devengado - $interes_normal_pagado ) + $interes_normal_calculado;
		$interes_moratorio			= ( $interes_moratorio_devengado - $interes_moratorio_pagado ) + $interes_moratorio_calculado;
		
		$xLog->add("WARN\tNO_PLAN\tEl Plan de Pagos es ignorado\r\n", $xLog->COMMON);
		$xLog->add("WARN\tINTS_NOR\tEL Interes Normal Resulto en $interes_normal\r\n", $xLog->DEVELOPER);
		$xLog->add("WARN\tINTS_MOR\tEL Interes Moratorio Resulto en $interes_moratorio\r\n", $xLog->DEVELOPER);
	} else {
		//2014-07-22 calcular mora por letra
		if($pago_total == false){
			$interes_moratorio		= $DInteres[SYS_INTERES_MORATORIO];
			$interes_normal			= 0;
			$xLog->add("WARN\tOMITIR_INT\tEL Interes Normal Resulto en $interes_normal Y EL Interes Moratorio Resulto en $interes_moratorio, SE OMITEN\r\n", $xLog->DEVELOPER);
			//$xLog->add("WARN\tOMITIR_INT\tEL Interes Normal Resulto en $interes_normal Y EL Interes Moratorio Resulto en $interes_moratorio, SE OMITEN\r\n", $xLog->COMMON);
		}
		//Razonamiento : Si la letra se devengo, entonces
		if($xCred->getEstadoActual() == CREDITO_ESTADO_VIGENTE){	}
		//================= Si se usa intereses de la BD
		if($useMoraBD == true){
			if($pago_total == true){
				if($xLetra->initSuma($fecha_operacion) == true){

					$interes_moratorio		= $xLetra->getMora();
					$interes_normal			= $xLetra->getInteres();
					
					$xLog->add("WARN\tINTS_NOR\tEL Interes Normal Ajustado en $interes_normal - Usar BD\r\n", $xLog->DEVELOPER);
					$xLog->add("WARN\tINTS_MOR\tEL Interes Moratorio Ajustado en $interes_moratorio - Usar BD\r\n", $xLog->DEVELOPER);
				}
				
			}  else {
				//Pago Parcial
				if($xLetra->init($xCred->getClaveDePersona(), $xCred->getClaveDeCredito(), $parcialidad) == true){
					$interes_moratorio		= $xLetra->getMora();
					//$interes_normal			= $xLetra->getInteres();
					$xLog->add("WARN\tINTS_NOR\tEL Interes Normal Ajustado en $interes_normal - Usar BD\r\n", $xLog->DEVELOPER);
					$xLog->add("WARN\tINTS_MOR\tEL Interes Moratorio Ajustado en $interes_moratorio - Usar BD\r\n", $xLog->DEVELOPER);
				}
			}
			
		}
		
		//Indica llevar a Cero la mora de creditos de nomina
		if($xCred->getEsNomina() == true AND $NoMoraNom == true){
			$interes_moratorio				= 0;
		}
		
	}
	

	
	$SQLBody	= $xSQL->getConceptosDePago($solicitud, $socio, $parcialidad);
} else {
	$SQLBody	= $xSQL->getConceptosDePago($solicitud, $socio);
}

$xLog->add("OK\tNORMAL\tSe ha DEVENGADO $interes_normal_devengado y el socio ha PAGADO $interes_normal_pagado, se ha CALCULADO $interes_normal_calculado\r\n", $xLog->DEVELOPER);
$xLog->add("OK\tMORA\tSe ha DEVENGADO $interes_moratorio_devengado y el socio ha PAGADO $interes_moratorio_pagado, se ha CALCULADO $interes_normal_devengado\r\n", $xLog->DEVELOPER);
//====================== EN INTERESES


$xJsBasic	= new jsBasicForm("frmprocesarpago");

echo $xHP->getHeader(true);

$tasa_iva_otros			= TASA_IVA;

echo $xHP->setBodyinit("initComponents()");
$xFRM	= new cHForm("frmprocesarpago", "frmpagoprocesado.php?p=$params|$estatus_del_credito|$grupo", "frmprocesarpago");

$xFRM->OHidden("ctipo_pago", $mTipoPago);
$xFRM->OHidden("vendedor", $vendedor);
$xFRM->OHidden("oficialcobranza", $vendedor);


$xFRM->addFootElement("<input type='hidden' name='cobservaciones' value='' id=\"idobservaciones\" />
 <input type='hidden' name='crecibo_fiscal' id=\"idrecibo_fiscal\" value='$mReciboFiscal' />
<input type=\"hidden\" name='procesar' id=\"procesar\" value=\"$procesado\" />
<input type='hidden' name='ccheque' id=\"idcheque\" value='$cheque'  /><input type='hidden' name='idcomodin' id=\"idcomodin\" value='0'  />
<input type='hidden' name='idletra' id=\"idletra\" value='$parcialidad'  /><input type='hidden' name='idpersona' id=\"idpersona\" value='$socio'  />
<input type='hidden' name='idcredito' id=\"idcredito\" value='$solicitud'  />");
//<input type=\"hidden\" id=\"iTasaIvaO\" value=\"$tasa_iva_otros\" /> <input type=\"hidden\" id=\"iTasaIva\" value=\"$tasa_iva\" />
/**
* @since 1.9.41 -patch 1.9.23
**/
//Verificar Incidencias
$arrINTS			= array(420);
if ( $xCred->getRespetarPlanDePago() == false){ $arrINTS	= array(411, 413, 420, 412); }
if($monto_a_operar <=0 ){	$xLog->add( "WARN\tNecesita un Monto a Operar, que debe ser el efectivo que el socio trae \r\n", $xLog->COMMON); }
/**
 * Obtiene los Movimientos mediante una Sentencia SQL
 *
 */
$monto_desglose			= 0;
$arr_desglose			= array();

$rsm 					=  $ql->getDataRecord($SQLBody);
//Operaciones Rebuscadas .- Operaciones traidas de una consulta
foreach ($rsm as $rwm ){
		$pendiente      = 0;
		$title	        = $rwm["descripcion_operacion"];
		$monto	        = $xT->cFloat($rwm["total_operacion"], 2);
		$MTipo          = $rwm["tipo_operacion"];
		$cls			= "";
		$xLog->add( "WARN\tIX\tEl Movimiento es $MTipo\r\n", $xLog->DEVELOPER);
				//Omite los Intereses Pactados y tod lo concerniente al credito segun la base
				if ( in_array($MTipo, $arrINTS) ){
					$xLog->add("WARN\t$MTipo\tINTERES\tEl Monto por $monto Del Movimiento $MTipo se lleva a CERO\r\n", $xLog->DEVELOPER);
					$monto	   	= 0;
				}
				if($NoArrPena == true AND $MTipo == OPERACION_CLAVE_PAGO_PENAS){
					$xLog->add("WARN\t$MTipo\tPENAS\tEl Monto por $monto Del Movimiento $MTipo se lleva a CERO por nos arrastrar PENAS\r\n", $xLog->DEVELOPER);
					$monto	   	= 0;
				}
				if($MTipo == OPERACION_CLAVE_PLAN_DESGLOSE){
					$xLog->add("WARN\t$MTipo\tDESGLOSE\tEl Monto por $monto Del Movimiento $MTipo se lleva a CERO.- DESGLOSE\r\n", $xLog->DEVELOPER);
					if($xCred->getEsCreditoYaAfectado() == true){
						$OProducto->initOtrosCargos($xCred->getFechaDeMinistracion(), $xCred->getMontoAutorizado());
					} else {
						$OProducto->initOtrosCargos();
					}
					$xFRM->OHidden("idplandesglose", $monto);
					
					$monto_desglose	= $monto;
					$arr_desglose	= $OProducto->getListaOtrosCargosEnParcs();	
					$monto	   		= 0;
					
					//Si es Credito es Arrendamiento Puro
					if($xCred->getEsArrendamientoPuro() == true){
						$xLeas		= new cCreditosLeasing($xCred->getClaveDeOrigen());
						$xLeas->init();
						
						$totalCNC	= $xLeas->getCuotasNoCapitalizadas();
						
						//return $this->mCuotaMtto+$this->mCuotaSeguro+$this->mCuotaTenencia+$this->mCuotaAccesorios + $this->mCuotaGtiaExtendida; }
						
						$arr_desglose[OPERACION_CLAVE_PAGO_SEGURO_V]	= $xLeas->getCuotaSeguro() / $totalCNC;
						$arr_desglose[OPERACION_CLAVE_PAGO_MTTO_V]		= $xLeas->getCuotaMtto() / $totalCNC;
						$arr_desglose[OPERACION_CLAVE_PAGO_TENEN_V]		= $xLeas->getCuotaTenencia() / $totalCNC;
						$arr_desglose[OPERACION_CLAVE_PAGO_ACC_V]		= $xLeas->getCuotaAccesorios() / $totalCNC;
						$arr_desglose[OPERACION_CLAVE_PAGO_GTIAE_V]		= $xLeas->getCuotaGtiaExtendida() / $totalCNC;
						$arr_desglose[OPERACION_CLAVE_PAGO_IVA_ARR]		= $xLeas->getCuotaIVA() / $totalCNC;
					}
					
					
					//Obtener el listado de cargos para hacer 100
					//Solo aceptar Tasas
					//setLog("TIP $MTipo");
				}
				if($MTipo == OPERACION_CLAVE_PLAN_INTERES AND $monto > 0){
					if($xCred->getPagosSinCapital() == true AND ($xF->getInt($fecha_operacion)< $xF->getInt($fecha_de_pago)) ){
						$interes_calculado = ( $interes_normal_devengado - $interes_normal_pagado ) + $interes_normal_calculado;
						//Si el interes es mayor a lo calculado y es pago total y es pago solo interes
						//AND ($xCred->getPagosSinCapital() == true)
						if(($monto > $interes_calculado)  AND ($pago_total == true) ){
							$xLog->add("WARN\t$MTipo\tINT.DEV\tInteres de la Letra Ajustado de $monto a $interes_calculado por ser menos y es Pago Total\r\n", $xLog->COMMON);
							$monto 		= ($interes_calculado > 0) ? $interes_calculado : 0;
							$cls		= " class='warn' ";
							$interes_normal	= 0;// ($interes_normal - $monto);
						} else {
//====================== Verificar cumplimiento de letra
							//$xLog->add("WARN\t$MTipo\tINT.DEV.B\tInteres de la Letra Ajustado de $monto a $interes_normal por ser menos\r\n", $xLog->DEVELOPER);
							$interes_normal	= setNoMenorQueCero($interes_normal - $monto); //interes normal - interes de la letra
						}
					} else {
						if($pago_total == false){
							$xLog->add("WARN\tSin cambios en Intereses $MTipo\r\n", $xLog->DEVELOPER);
						} else {
							if($SoloExigibles == true AND $pago_total == true){
								
								$monto			= $interes_exigible;
								$interes_normal	= $interes_exigible;
								
								$xLog->add("WARN\t$MTipo\tINT.DEV.C\tInteres Exigible a $interes_exigible\r\n", $xLog->DEVELOPER);
								
							}
							$xLog->add("WARN\t$MTipo\tINT.DEV.C\tInteres de la Letra Restando $monto a  $interes_normal\r\n", $xLog->DEVELOPER);
							$interes_normal	= setNoMenorQueCero(($interes_normal - $monto));
							
							
						}
					}
				}
				if ( in_array($MTipo, $aActosDeIva) ){
					$xLog->add("WARN\t$MTipo\tIVA\tEl Monto por $monto Del Movimiento $MTipo se lleva a CERO\r\n",$xLog->DEVELOPER);
					$monto_a_operar	-= $monto;
					$monto	   		= 0;
				}
				if ( $MTipo == OPERACION_CLAVE_CARGOS_COBRANZA ){
					if(OPERACION_CUADRAR_CON_COBRANZA == true){
						$xLog->add("WARN\t$MTipo\tCOBRANZA\tEl Monto por $monto Del Movimiento $MTipo se lleva a CERO\r\n", $xLog->DEVELOPER);
						$montoCobranza	+= $monto;
						$monto	   		= 0;
					}
				}
				//Si el el Array de Afectaciones al capital
				if ( in_array($MTipo, $aAfectCapital) ){
					$monto		= ( $xCred->getRespetarPlanDePago() == false AND $operacion != "plc") ? 0 : $monto;
					if($monto > $xCred->getSaldoActual()){
						$xLog->add("WARN\t$MTipo\tCAPITAL.AJ\tCapital ajustado de $monto a " . $xCred->getSaldoActual() . "\r\n", $xLog->COMMON);
						$monto	= $xCred->getSaldoActual();
						$cls	= " class='warn' ";
					}
					$mAfectCapital	+= $monto;
					$xLog->add("WARN\t$MTipo\tCAPITAL\tEl Monto por $monto Del Movimiento $MTipo Capital, queda en $mAfectCapital\r\n", $xLog->DEVELOPER);
				}
				
			if($monto > 0) {
				if ($monto > $monto_a_operar){
					$pendiente		= $monto - $monto_a_operar;
					$monto			= $monto_a_operar;
					$monto_a_operar = 0;
				} else {
					$monto_a_operar = $monto_a_operar - $monto;
					$pendiente		= 0;
				}
			} else {
				$monto_a_operar		= $monto_a_operar - $monto;
				$pendiente			= 0;
			}
			//============================================ Faltantes
			$xCUtils				= new cUtileriasParaCreditos();
			$docto					= $solicitud;
			//============================================ 2015-05-01
			eval($rwm["codigo_de_valoracion"]);
			if($monto != 0){
				$monto				= number_format($monto, 2, '.', '');
				//class='tags green'
				$tds 				= $tds . "
				<tr>
					<th>$MTipo</th>
					<td>$title</td>
					<th>
						<input type=\"text\" name=\"c-$MTipo\" id=\"id-$MTipo\" value=\"$monto\" data-original=\"$monto\" class='mny' onfocus=\"pushMny(event);\" onchange=\"jsEval(this);\" />
						<input type=\"hidden\" name=\"p-$MTipo\" id=\"idp-$MTipo\" value=\"$pendiente\" class='mny' />
					</th>
				</tr>";
			}
			$total		+= $monto;
} //End operaciones rebuscadas
//============================================================= DESGLOSE DE SALDOS
		foreach ($arr_desglose as $idx=> $vv){
			$monto		= setNoMenorQueCero(($monto_desglose * $vv),2);
			$xTipoOp	= new cTipoDeOperacion($idx);
			$xTipoOp->init();
			$nnote		= "";
			if($pago_total == true){
				
				if($xTipoOp->getExigenciaEnPagTotCred($xCred->getClaveDeProducto()) == true ){
					$ntemp	= setNoMenorQueCero(($xCred->getPagosAutorizados()-$parcialidad))+1;
					$nnote	= " EXIG($ntemp * $monto)";
					$monto	= $ntemp * $monto;
					$xLog->add("WARN\t$idx\tADD\tExigencia TOTAL del Monto por $monto a la Parcialidad Num $parcialidad por $ntemp Periodos\r\n", $xLog->DEVELOPER);
					
				} else {
					if($xCred->isAtrasado() == true){
						if($xCred->isAFinalDePlazo() == false){
							$xPlan	= new cPlanDePagos($xCred->getNumeroDePlanDePagos());
							$xPlan->init();
							
							$xPlan->initParcsPendientes(0, $fecha_operacion);
							
							$ntemp	= setNoMenorQueCero($xPlan->getPagosAtrasados());
							$nnote	= " VENC($ntemp * $monto)";
							$monto	= $ntemp * $monto;
							
							$xLog->add("WARN\t$idx\tADD\tPagos atrasados por $monto a la Parcialidad Num $parcialidad por $ntemp Periodos\r\n", $xLog->DEVELOPER);
							
						}
					}
				}
				
			}
			if( ($monto > 0) AND ($monto_a_operar >0)){
				//Evauar pendiente
				$pendiente 				= 0;
				if ($monto > $monto_a_operar){
					$pendiente 		= $monto - $monto_a_operar;
					$monto 			= $monto_a_operar;
					$monto_a_operar = 0;
				} else {
					$monto_a_operar = $monto_a_operar - $monto;
					$pendiente 		= 0;
				}
				$base_iva_intereses 	+= (isset($aActosNoGravados[$idx])) ? $monto : 0;
				$total					+= $monto;
				$monto					= number_format($monto, 2, '.', '');

				$xLog->add("WARN\t$idx\tADD\tAgregar Concepto por $monto\r\n", $xLog->DEVELOPER);
				
				$tds = $tds . "
			<tr>
				<th>$idx</th>
				<td class='notice'>" . $xTipoOp->getNombre() . "$nnote</td>
								<th><input type=\"text\" name=\"c-$idx\" id=\"id-$idx\" value=\"$monto\" data-original=\"$monto\" data-nomod=\"1\" class='mny' onfocus=\"pushMny(event);\" onchange=\"jsEval(this);\" />
									<input type=\"hidden\" name=\"p-$idx\" id=\"idp-$idx\" value=\"0\"/>
								</th>
								</tr>";
			}
			//$xLog->add("WARN\t$MTipo\t$idx\tCargo extra por $vv \r\n", $xLog->DEVELOPER);
		
		}

//Obtiene los Datos de Cobro segun el Saldo en Intereses
			//Interes Moroso
			if( ( $interes_moratorio>0 ) and ( $monto_a_operar>0 ) ){
				//Evauar pendiente
				$pendiente 				= 0;
				$monto 					= $interes_moratorio;
				if ($monto>$monto_a_operar){

						$pendiente 		= $monto - $monto_a_operar;
						$monto 			= $monto_a_operar;
						$monto_a_operar = 0;
				} else {
						$monto_a_operar = $monto_a_operar - $monto;
						$pendiente 		= 0;
				}
				$base_iva_intereses 	+= $monto;
				$total			+= $monto;
				$monto					= number_format($monto, 2, '.', '');
				
			$tds = $tds . "
			<tr>
				<th>141</th>
				<td class='error'>" . $xLng->getT("TR.INTERESES MORATORIO") . "</td>
				<th><input type=\"text\" name=\"c-moroso\" id=\"id-moroso\" value=\"$monto\" data-original=\"$monto\" class='mny' onfocus=\"pushMny(event);\" onchange=\"jsEval(this);\" />
					<input type=\"hidden\" name=\"p-moroso\" id=\"idp-moroso\" value=\"0\"/>
				</th>
			</tr>";
			}
			//interes normal
			if($interes_normal > 0 and $monto_a_operar>0){
				//Evauar pendiente
				$pendiente 				= 0;
				$monto 					= $interes_normal;
				if ($monto>$monto_a_operar){
						$pendiente 		= $monto - $monto_a_operar;
						$monto 			= $monto_a_operar;
						$monto_a_operar = 0;
				} else {
						$monto_a_operar = $monto_a_operar - $monto;
						$pendiente      = 0;
				}
				$base_iva_intereses 	+= $monto;
				$total					+= $monto;
				$monto					= number_format($monto, 2, '.', '');
			$tds = $tds . "
			<tr>
				<th>140</th>
				<td class='notice'>" . $xLng->getT("TR.INTERESES NORMAL") . "</td>
				<th><input type=\"text\" name=\"c-corriente\" id=\"id-corriente\" value=\"$monto\" data-original=\"$monto\" class='mny'  onfocus=\"pushMny(event);\" onchange=\"jsEval(this);\" />
				<input type=\"hidden\" name=\"p-corriente\" id=\"idp-corriente\" value=\"$pendiente\"/></th>
			</tr>";
			}
//===================== PENAS
			//interes normal
			if($monto_penas > 0 and $monto_a_operar>0){
				$idx					= OPERACION_CLAVE_PAGO_PENAS;//clave de operacion
				//Evauar pendiente
				$pendiente 				= 0;
				$monto 					= $monto_penas;
				if ($monto>$monto_a_operar){
					$pendiente 		= $monto - $monto_a_operar;
					$monto 			= $monto_a_operar;
					$monto_a_operar = 0;
				} else {
					$monto_a_operar = $monto_a_operar - $monto;
					$pendiente      = 0;
				}
				$base_iva_otros 	+= $monto;
				$total				+= $monto;
				$monto				= number_format($monto, 2, '.', '');
				$tds = $tds . "
			<tr>
				<th>$idx</th>
				<td class='notice'>" . $xLng->getT("TR.PENAS_POR_ATRASOS") . "</td>
							<th><input type=\"text\" name=\"c-$idx\" id=\"id-$idx\" value=\"$monto\"  data-original=\"$monto\" class='mny'  onfocus=\"pushMny(event);\" onchange=\"jsEval(this);\" />
							<input type=\"hidden\" name=\"p-$idx\" id=\"idp-$idx\" value=\"$pendiente\"/></th>
							</tr>";
			}
//Agregar IVA Intereses
			$monto_iva_intereses = ($base_iva_intereses * $tasa_iva) + $monto_iva_intereses;
			if($monto_iva_intereses>0){ // and $monto_a_operar>0
				//Evauar pendiente
				$pendiente = 0;
				$monto = $monto_iva_intereses;
				if(FORZAR_IVA_AL_PAGO == "1"){
					if ($monto>$monto_a_operar){
						$pendiente      = $monto - $monto_a_operar;
						$monto          = $monto_a_operar;
						$monto_a_operar = 0;
					} else {
						$monto_a_operar = $monto_a_operar - $monto;
						$pendiente      = 0;
					}
				}
				//EL IVA ES EN EL MOMENTO ASI QUE NO SE GUARDARA PENDIENTE
				$total			+= $monto;
				$monto			= number_format($monto, 2, '.', '');
				$tds 		= $tds . "<tr><th></th>
				<td>" . $xLng->getT("TR.IVA por INTERESES") . "</td>
				<th><input type=\"text\" name=\"c-ivaintereses\" id=\"id-ivaintereses\" value=\"$monto\" data-original=\"$monto\" class='mny'  onfocus=\"pushMny(event);\" onchange=\"jsEval(this);\" />
				<input type=\"hidden\" name=\"p-ivaintereses\" id=\"idp-ivaintereses\" value=\"$pendiente\" class='mny' /></th>
			</tr>";
			}
//Agregar Iva Otros
			$monto_iva_otros 			= ($base_iva_otros * $tasa_iva) + $monto_iva_otros;
			if($monto_iva_otros > 0){ // and $monto_a_operar>0
				//Evauar pendiente
				$pendiente = 0;
				$monto = $monto_iva_otros;
				if(FORZAR_IVA_AL_PAGO == "1"){
					if ($monto>$monto_a_operar){

						$pendiente      = $monto - $monto_a_operar;
						$monto          = $monto_a_operar;
						$monto_a_operar = 0;
					} else {
						$monto_a_operar = $monto_a_operar - $monto;
						$pendiente      = 0;
					}
				}
				//EL IVA ES EN EL MOMENTO ASI QUE NO SE GUARDARA PENDIENTE
				$total			+= $monto;
				$monto	                = number_format($monto, 2, '.', '');
//==========================================================================
				$tds = $tds . "<tr>
				<th></th><td>" . $xLng->getT("TR.IVA POR OTROS CONCEPTOS") . "</td>
				<th><input type=\"text\" name=\"c-ivaotros\" id=\"id-ivaotros\" value=\"$monto\" data-original=\"$monto\" class='mny' onchange=\"jsEval(this);\" />
				<input type=\"hidden\" name=\"p-ivaotros\" id=\"idp-ivaotros\" value=\"$pendiente\" class='mny' /></th></tr>";
			}

				$pendiente 		= 0;
				$monto 			= $interes_anticipado * -1;
				if ($monto>$monto_a_operar){

						$pendiente          = $monto - $monto_a_operar;
						$monto              = $monto_a_operar;
						$monto_a_operar     = 0;
				} else {
						$monto_a_operar     = $monto_a_operar - $monto;
						$pendiente          = 0;
				}
				$total			+= $monto;
				$monto	                    = number_format($monto, 2, '.', '');
//Imprime el Abono al Capital
//1		//Pago Unico a Final de Plazo
//2		//Pago Periodico Capital mas Interes
//3		//Pago Periodico de Interes y Capital a Final de Plazo
	if($pago_total == true OR $periocidad == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
		$monto_capital_operado	= setNoMenorQueCero( ($saldo_actual - $mAfectCapital),2);
	}


if($monto_capital_operado > 0 and $monto_a_operar > 0){
	//Evauar pendiente
	$pendiente 	= 0;
	$monto 		= $monto_capital_operado;
	if ($monto > $monto_a_operar){
		$pendiente      = $monto - $monto_a_operar;
		$monto          = $monto_a_operar;
		$monto_a_operar = 0;
	} else {
		$monto_a_operar = $monto_a_operar - $monto;
		$pendiente      = 0;
	}
	$total			+= $monto;
	$monto	        = setNoMenorQueCero($monto,2);
	if( ((OPERACION_CUADRAR_CON_COBRANZA == true) AND ($operacion == OPERACION_PAGO_LETRA_VARIABLE )) AND ($monto <= OPERACION_RECIBOS_COBRANZA_LIM)){
		$xLog->add("OK\tGTOS_CBZA\tGastos de Cobranza Calculado $gastos_de_cobranza_calculado\r\n", $xLog->DEVELOPER);
		$monto		+= $gastos_de_cobranza_calculado + $montoCobranza;
		$monto		= $monto * (1/(1+$tasa_iva));//($generarIVA == false) ? $monto : $monto * (1/(1+$tasa_iva));
		$gastos_de_cobranza_calculado	= $monto;
		//$monto	        = number_format($monto, 2, '.', '');
	} else {
		$tds .= "<tr><th>120</th>
		<td class='warning'>" . $xLng->getT("TR.MONTO DE CAPITAL DIRECTO") . "</td>
		<th><input type=\"text\" name=\"c-capital\" id=\"id-capital\" value=\"$monto\" data-original=\"$monto\" class='mny' onchange=\"jsEval(this);\"  /></th></tr>";
	}
}
if($gastos_de_cobranza_calculado > 0){
	$gastos_de_cobranza_calculado	= number_format($gastos_de_cobranza_calculado, 2, '.', '');
	$tds 	.= "<tr><th>600</th><td>" . $xLng->getT("TR.GASTOS DE COBRANZA") . "</td>
		<th><input type=\"text\" name=\"c-$claveCbza\" id=\"id-$claveCbza\" value=\"$gastos_de_cobranza_calculado\" data-original=\"$gastos_de_cobranza_calculado\" class='mny' onchange=\"jsEval(this);\"  /></th></tr>";
}
$cargos_mora	= $gastos_de_cobranza_calculado + $interes_moratorio;
//Sql de especiales
		$sqlEsp = "SELECT
			`eacp_config_bases_de_integracion_miembros`.`miembro`,
			`operaciones_tipos`.`descripcion_operacion`

		FROM
			`operaciones_tipos` `operaciones_tipos`
				INNER JOIN `eacp_config_bases_de_integracion_miembros`
				`eacp_config_bases_de_integracion_miembros`
				ON `operaciones_tipos`.`idoperaciones_tipos` =
				`eacp_config_bases_de_integracion_miembros`.`miembro`
		WHERE
			(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 1001)
			AND
			(`operaciones_tipos`.`estatus` =  1)
			AND (`eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 1)
		ORDER BY
			`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`";

		$cEsps = new cSelect("cMvtosEsp","idMvtosEsp", $sqlEsp);
		$cEsps->setOptionSelect(OPERACION_CLAVE_CARGOS_VARIOS);
		$cEsps->setEsSql();
		$cEsps->addEvent("onblur", "addEspMvto");
		$SEsp = $cEsps->show();
$xBtn		= new cHButton();

if($cargos_mora > 0){
	$xFRM->OButton("TR.SIN MORA", "jsEliminarCargos()", $xFRM->ic()->RESTAR, "idsinmora", "white");
}

//============================================ INIT.- COBRO DE GARANTIA LIQUIDA

if(GARANTIA_LIQUIDA_EN_CAPTACION == false AND $pago_total == true){
	if($xCred->getGarantiaLiquidaPagada() > 0){
		$gtia_Liquida		= $xCred->getGarantiaLiquidaPagada();

		$monto				= number_format($gtia_Liquida, 2, '.', '')*-1;
		$pendiente			= 0;
		$MTipo				= OPERACION_CLAVE_DEV_GLIQ;
		$xOp				= new cTipoDeOperacion($MTipo); $xOp->init();
		$title				= $xOp->getNombre();
		//class='tags green'
		$tds 				= $tds . "
			<tr>
				<th>$MTipo</th>
				<td>$title</td>
				<th>
					<input type=\"text\" name=\"c-$MTipo\" id=\"id-$MTipo\" value=\"$monto\" data-original=\"$monto\" class='mny' onfocus=\"pushMny(event);\" onchange=\"jsEval(this);\" />
					<input type=\"hidden\" name=\"p-$MTipo\" id=\"idp-$MTipo\" value=\"$pendiente\"/>
				</th>
			</tr>";

		$total		+= $monto;
	}
}


//============================================ END.- COBRO DE GARANTIA LIQUIDA
//$xFRM->OButton("Vista Previa", "showVistaPago()", $xFRM->ic()->VER, "idprev");

$xFRM->OHidden("idtotaloperaciones", 0);

$xFRM->addHElem("<table>
	<tbody id=\"tbCobros\">	$tds </tbody>
	<tfoot>
	<tr>
		<td></td>
		<td colspan='2'>$SEsp</td></tr>
    	<tr>
    		<th class='notice'> " . $xFRM->l()->getT("TR.PARCIALIDAD") . " : $parcialidad</th>
      		<th>TOTAL</th>
      		<th><input type=\"text\" name=\"ctotal\" id=\"idtotal\" value=\"$total\" class='mny' /></th>
    	</tr>
	</tfoot>
</table>");
$xFRM->addAviso("", "id_letras_de_numeros", false, "success");
$msg	.= $xLog->getMessages();
if (MODO_DEBUG == true ){
	
	$xLog->add($xCred->getMessages(OUT_TXT), $xLog->DEVELOPER);
	$xFRM->addLog($xLog->getMessages());
	$msg	= "";
}
$xFRM->addAviso($msg, "sysmsg", false, "notice");

$xFRM->addJsInit("jsAsLoaded();");
echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
//XXX: Verificar generacion de IVA
?>
<script>
var Wo				= new Gen();
var xG				= new Gen();
var mMny 			= 0;
var goSucess 		= true;
var Frm 			= document.frmprocesarpago;
var jsWorkForm 		= document.frmprocesarpago;
var vTipoPago		= "<?php echo $mTipoPago; ?>";
var tasaIVA			= <?php echo $tasa_iva; ?>;
var tasaIVAO		= <?php echo $tasa_iva_otros; ?>;
var mLetra			= <?php echo $parcialidad; ?>;

var mReciboIncomp	= "<?php echo OPERACION_PAGO_LETRA_VARIABLE; ?>";
var mTipoRecibo		= "<?php echo $operacion; ?>";
var mIDCredito		= "<?php echo $solicitud; ?>";
var procesar		= "<?php echo $procesado; ?>";
var delayCaptura	= 3500;

var idPendinteCap	= "id-410";
var mToleraCant		= <?php echo (TOLERANCIA_SALDOS*10); ?>;
var mSaldoCred		= <?php echo $xCred->getSaldoActual(); ?>;
var desdeAjuste		= false;
var mMontoCapital	= <?php echo $mAfectCapital; ?>;
var mCapitalAmort	= 0;
var mInteresAmort	= 0;
var mCapitalPend	= 0;
var mInteresPend	= 0;
var mNumsLetras		= <?php echo $pagosautorizados; ?>;
var mNoCaptAntInt	= <?php echo ($NoCaptAntI == true) ? "true" : "false"; ?>;

var EsFinalPlazo	= <?php echo ($xCred->isAFinalDePlazo() == true) ? "true" : "false"; ?>;
var LockCobro		= <?php echo ($LockCobros == true) ? "true" : "false"; ?>;
<?php
$xB					= new cBases(0);
$strGrav			= $xB->getMembers_InString(true, BASE_IVA_INTERESES);
$strGrav			.= ", 'corriente-1.0000', 'vencido-1.0000', 'remanente-1.0000'";//, 'moroso-1'

$strOGrav			= $xB->getMembers_InString(true, BASE_IVA_OTROS);
$strOGrav			.= ",'moroso-1.0000'";
if($tasa_iva <= 0){
	echo  "var BaseGravados = new Array();\n";
} else {
	echo  "var BaseGravados = new Array($strGrav);\n";
}
echo "var BaseGravadosO = new Array($strOGrav);\n";
?>
var aIvaCalculado 	= new Array("id-ivaintereses", "id-ivaotros", "id-413", "id-1201", "id-1202", "id-1203", "id-1204");
var aCapital 		= new Array("id-410", "id-capital");
var aInteres 		= new Array("id-411", "id-corriente");
var xG				= new Gen();


var vPuedeSoloCap	= false;
var oInfo			= {errors:0};
var oEsta			= {pendiente : "idp-", normal: "id-"};
//No es efectivo, no causa IVA
function pushMny(evt){
	mMny 		= evt.target.value; 
	var idevt	= evt.target.id;
	if( $.inArray(idevt, aIvaCalculado) !== -1){
		$("#idMvtosEsp").focus();
	} else {
		if(LockCobro == true && flotante(mMny) > 0 ){
			$("#idMvtosEsp").focus();
		} else {
			evt.target.select();
		}
	}
	getTotal(); 
}
function getTotal(){
	//Recalcula el IVA
	recalcIVA();
	var neto 		= new Number(0);	//suma de cobros
  	var isLims 		= Frm.elements.length - 1;
	var dynCap		= redondear(mSaldoCred,2);
	var mCaptAb		= 0;				//Capital abonado en el recibo
	var mIntAb		= 0;				//Interes abonado en el recibo
	var mIntPend	= 0;				//Interes pendiente en el recibo
	
	for(var i=0; i<=isLims; i++){
  	  		try {
	  			var mNam = Frm.elements[i].getAttribute("name");
	  			if ((mNam!=null) && (mNam.indexOf("c-")!= -1)){
	  				if ( isNaN(Frm.elements[i].value) || Frm.elements[i].value == "" ){ Frm.elements[i].value = 0; }
					var mCurrVal	= redondear(Frm.elements[i].value, 2);
					//verificar capital
					var osid	= Frm.elements[i].getAttribute("id");
					
					if( $.inArray(osid, aInteres) != -1 ){
						var idpend	= jsGetIDRaw(osid); idpend = oEsta.pendiente + idpend;
						mIntPend	+= redondear($("#" + idpend).val(),2);
						mIntAb		+= mCurrVal;
					}
					if( $.inArray(osid, aCapital) != -1 ){
						if (redondear(mCurrVal,2) > redondear(dynCap,2) ) {
							
							xG.informar({msg : "No puede Abonar un cantidad Mayor al Saldo de Credito de " + mSaldoCred});
							
							oInfo.errors++;
							
							mCurrVal	= redondear(dynCap, 2);		//cambiar cantidad a saldo
							$("#" + osid).val(mCurrVal);	//set id a saldo
							dynCap		= 0;			//establecer a cero
							$("#idMvtosEsp").focus();
						} else {
							dynCap	= dynCap - mCurrVal;
						}
						mCaptAb	+= mCurrVal;			//Sumar Capital abonado
					}
					
				neto += mCurrVal;
	  			}
  	  		} catch(e){
  	  	  		
  	  		}
	}
	mCapitalAmort		= redondear(mCaptAb);			//Actualizar el Capital Abonado
	mInteresAmort		= redondear(mIntAb);			//Actualizar el Interes Abonado
	if(mIntPend > 0){
		if(mCapitalAmort > 0){
			if(mLetra == mNumsLetras){
				console.log("Capital a ultima letra factible : " +  mLetra + "/" + mNumsLetras);
			} else {
				if(mNoCaptAntInt == true){
					xG.informar({msg : "ALERT_PAGAR_ANTES_INT : " + getFMoney(mIntPend)});
					oInfo.errors++;
				} else {
					console.log("Pago Adelantado : " +  mLetra + "/" + mNumsLetras);
				}
			}
		} 
	}
	//setLog("Error... " + oInfo.errors);
  	Frm.ctotal.value	= redondear(neto);
  	$("#idtotaloperaciones").val( redondear(neto) );
  	
  	getLetras();

  	//Heredar
	if(window.parent){
		if(window.parent.document.getElementById("idobservaciones")){ $("#idobservaciones").val(window.parent.document.getElementById("idobservaciones").value);	}
		if(window.parent.document.getElementById("id-foliofiscal")){ $("#idrecibo_fiscal").val(window.parent.document.getElementById("id-foliofiscal").value);	}
		if(window.parent.document.getElementById("cheque")){ $("#idcheque").val(window.parent.document.getElementById("cheque").value); 	}
		if(window.parent.jsFrameTotalActualizado){ window.parent.jsFrameTotalActualizado(); }
	}
}
function FormSucess(){
	getTotal();
	if(oInfo.errors >0){
		xG.informar({msg : "MSG_NO_DATA"});
		return false;
	}
	//Verificar si los montos son validos
  	var isLims = Frm.elements.length - 1;
  		for(i=0; i<=isLims; i++){
			var elm		= Frm.elements[i];
  			var mNam 	= elm.getAttribute("class");
			var mTyp 	= elm.getAttribute("type");

			if ((mNam!=null) && (mNam== "mny") && (mTyp=="text")){
  				//Verificar si es mayor a cero o no nulo
				//checar si pendiente 410 es menor a 0.99, si es menor tratar propuesta de enviar a la siguiente letra
				if (elm.id == idPendinteCap ) {
					var mmP		= redondear($("#idp-410").val());
					if (mmP >0 && mmP <= mToleraCant) {
						var siPend	= confirm("EL PENDIENTE " + mmP + " DE CAPITAL ES MINIMO\nDESEA INCLUIRLO EN LA PROXIMA PARCIALIDAD\nY DESCARTAR LA PARCIALIDAD ACTUAL?");
						if (siPend) {
							$("#idcomodin").val( mmP );
							$("#idp-410").val(0);
							jsaActualizarLetra();
						}
					}
					if((mCapitalAmort > mMontoCapital) && (mCapitalAmort < mSaldoCred) ){
						var mValorPorAmort		= redondear((mCapitalAmort - mMontoCapital));
						$("#idcomodin").val( redondear(mValorPorAmort) );
						setTimeout("terminarCaptura()", 1000);
						goSucess = false;							
					}
				}//Verificar cuanto tiene de capital e ir amortizando letras una a una
  			}
  		}
	//Verificar el Tipo de Pago
	//
	if (goSucess == true) { terminarCaptura(); }
}
function terminarCaptura() {
	if (procesar== SYS_AUTOMATICO) {
		Frm.submit();
	} else {
		var idmny	= Configuracion.moneda.simbolo + " " + getFMoney(jsGetTotal());
		xG.confirmar({msg:"¿ Confirma guardar el Pago por " + idmny + " ?", callback: setEnvioConfirmado, cancelar : setEnvioNoConfirmado, close : setEnvioNoConfirmado});
	}
}
function setEnvioNoConfirmado(){
	if(window.parent.jsEnableSave){
		window.parent.jsEnableSave();
	}	
}
function setEnvioConfirmado(){
	if(window.parent.jsRemoveSave){
		window.parent.jsRemoveSave();
	}
	xG.spinInit();
	Frm.submit();	
}
function showVistaPago(){ getTotal(); Wo.w({url: "../utils/rpt_preview_html.php?credito=" + mIDCredito}); }
function recalcIVA(){
	var IvaRecalculado		= new Number(0);
	
	var MontoIva			= new Number(0);
	var MontoIvaO			= new Number(0);
	var vILim 				= aIvaCalculado.length;
	//a traves de un for obtiene los valores
	var vLim 				= BaseGravados.length;
	var vLimO 				= BaseGravadosO.length;
	var NetoGravados		= new Number(0);
	var NetoGravadosO		= new Number(0);
	
	for(var i=0; i < vLim; i++){
		var osuKey 		= BaseGravados[i].split("-", 1);
		var isKey		= "id-" + osuKey[0];
		//Si el Control  existe
		if(document.getElementById(isKey)){
			if (isNaN(document.getElementById(isKey).value) || document.getElementById(isKey).value == ""){
				document.getElementById(isKey).value = 0;
			}
			NetoGravados += flotante(document.getElementById(isKey).value);
		}
	}
	for(var iO=0; iO < vLimO; iO++){
		var osuKeyO 	= BaseGravadosO[iO].split("-", 1);
		var isKeyO		= "id-" + osuKeyO[0];
		//Otros
		if(document.getElementById(isKeyO)){
			if (isNaN(document.getElementById(isKeyO).value) || document.getElementById(isKeyO).value == ""){
				document.getElementById(isKeyO).value = 0;
			}
			NetoGravadosO += flotante(document.getElementById(isKeyO).value);
		}
	}
	IvaRecalculado 	= redondear(((NetoGravados  * tasaIVA) + (NetoGravadosO*tasaIVAO)) - MontoIva);
	IVAOtros		= redondear((NetoGravadosO * tasaIVAO));
	IVAInts			= redondear((NetoGravados * tasaIVA));
	
	if(IVAOtros != 0){
		if(document.getElementById("id-ivaotros")){
			$("#id-ivaotros").val(redondear(IVAOtros) );
		} else {
			addEspMvto("ivaotros", redondear(IVAOtros), "VARIOS.- IVA POR RECALCULO");
		}
		setLog("OTROS: IVA recalculado a " + flotante(redondear(IVAOtros)) );
	} else {
		//setea si existen otros. 2016-09-07
		if(document.getElementById("id-ivaotros")){
			if( redondear($("#id-ivaotros").val()) > 0){
				$("#id-ivaotros").val(0);
			}
		}
	}
	if(IVAInts != 0){
		if(document.getElementById("id-ivaintereses")){
			$("#id-ivaintereses").val(redondear(IVAInts) );
		} else {
			addEspMvto("ivaintereses", redondear(IVAInts), "INTERESES.- IVA POR RECALCULO");
		}
		setLog("IVA recalculado a " + redondear(IVAInts) );
	} else {
		//--29dic2014
		//-- setea el iva a cero si no hay base de interes
		if(document.getElementById("id-ivaintereses")){
			if( redondear($("#id-ivaintereses").val()) > 0){
				$("#id-ivaintereses").val(0);
			}
		}
		if(document.getElementById("id-413")){
			if( redondear($("#id-413").val()) > 0){
				$("#id-413").val(0);
			}
		}		
	}
	setLog( "Total Gravados : $ " + NetoGravados + "; Total Recalculo: $ " + redondear(IvaRecalculado) + ";OTROS " + IVAOtros + "; INTERESES : " + IVAInts + "");
	//Resetear IVA //ninguno documento multiple descuento
	if ( redondear(NetoGravados+NetoGravadosO) <= 0 ) { for(i=0; i < vILim; i++){ $("#" + aIvaCalculado[i]).val(0); } }
}
function addEspMvto(id, OpValue, vTitle){
	var TRVal	= id;
	var TRRef	= "";
	var TRNam	= vTitle;
	
	if (!OpValue){OpValue = 0;}
	if (!id){
		var issetSelect = document.getElementById("idMvtosEsp");
		var CurID	= issetSelect.selectedIndex;
		var TRVal 	= issetSelect.value;
		var TRRef	= TRVal;
		var TRNam 	= issetSelect[CurID].text;
	}
	var iLims = document.getElementById("tbCobros");
	var	nTR = iLims.insertRow(iLims.rows.length);

	if(document.getElementById("id-" + TRVal)){
		//xG.alerta({ msg : "El concepto existe !", type : "warn"});
	} else {
		nTR.innerHTML = "<th>" + TRRef + "</th>" +
						"<td class='success'>" + TRNam + "</td>" +
						"<th><input type=\"text\" name=\"c-" + TRVal + "\" id=\"id-" + TRVal + "\" value=\"" + OpValue + "\" class='mny' onfocus=\"pushMny(event);\" onchange=\"jsEval(this);\" /></th>";
		document.getElementById("id_letras_de_numeros").innerHTML = "";
	}
	document.getElementById("id-" + TRVal).focus();
	document.getElementById("id-" + TRVal).select();
}
function initComponents(){ setTimeout("getTotal()", 1000); }
function getEdoCtaCredito(){ Wo.w({url: "../rpt_edos_cuenta/rptestadocuentacredito.php?credito=" + mCredito}); }
function jsGetIDRaw(str){
	str	= String(str).replace("id-", "");
	str	= String(str).replace("idp-", "");
	
	return str;
}
function jsEval(origen){

	var org		= origen;
	var idR		= String(org.id).replace("id-", "");
	var mAju	= 0;
	var mTasa	= 0;
	var dsrc	= new String($(origen).attr("id")).split("-");
	var vorg	= flotante($(origen).attr("data-original"));
	var nomod	= (typeof $(origen).attr("data-nomod") == "undefined") ? false : true;
	var morg	= flotante($(origen).val());
	var psrc	= "idp-" + dsrc[1];
	//========================================== Montos Pendientes
	
	if(document.getElementById(psrc)){
		if(nomod ==  true){
			xG.alerta( { msg: "OPERACION_INMUTABLE" } );
			$(origen).val( redondear(vorg) );
		} else {
			var nnm	= redondear((vorg - morg),2);
			$("#" + psrc).val(nnm);
		}
	}
	//Bonificaciones
	if ( BaseGravados.indexOf( idR + "--1.0000") != -1 ) {
		if (flotante(org.value) > 0) {
			mAju		= flotante(org.value) * -1;
			org.value	= flotante(org.value) * -1;
			mTasa		= tasaIVA;
		}
	}
	if ( BaseGravadosO.indexOf( idR + "--1.0000") != -1 ) {
		if (flotante(org.value) > 0) {
			mAju		= flotante(org.value) * -1;
			org.value	= flotante(org.value) * -1;
			mTasa		= tasaIVAO;
		}
	}
	//Cargos
	if ( BaseGravados.indexOf( idR + "-1.0000") != -1 ) {
		if (flotante(org.value) > 0) {
			mAju		= flotante(org.value) * -1;
			org.value	= flotante(org.value);
			mTasa		= tasaIVA;
		}
	}
	if ( BaseGravadosO.indexOf( idR + "-1.0000") != -1 ) {
		if (flotante(org.value) > 0) {
			mAju		= flotante(org.value) * -1;
			org.value	= flotante(org.value);
			mTasa		= tasaIVAO;
		}
	}
	mAju	= redondear(mAju);
	
	if(mAju != 0 && mTasa != 0){
		var mFactIva	= 1 / (1+flotante(mTasa));
		
		if (desdeAjuste == true){
			
		} else {
			if(flotante(org.value) > 0){
				var si2 	= confirm("DESEA SEPARAR EL IVA EN ESTE MONTO?\nQUEDARIA EN " + redondear(org.value * mFactIva));
				if (si2) { org.value	= redondear(org.value * mFactIva);  }
			}
		}
		desdeAjuste = false;
	}
	recalcIVA();
	getTotal();
}
function jsGetPagoAjustado(mMonto){
	var ixtotal	= $("#idtotal").val();
	mMonto		= (typeof mMonto == "undefined") ? window.prompt("Monto",ixtotal) : mMonto; 
	var ajustar	= mMonto;
	
	if ( flotante(ajustar) > 0 && flotante(ajustar) > flotante(ixtotal)) {
		ajustar	= flotante(ajustar) - flotante(ixtotal);
		ajustar	= ajustar / (1 + flotante(tasaIVAO));
		if(document.getElementById("id-600")){
			ajustar		= flotante($("#id-600").val()) + ajustar;
			document.getElementById("id-600").value = redondear(ajustar);
		} else {
			addEspMvto("600", redondear(ajustar), "GASTOS DE COBRANZA");
		}
		desdeAjuste = true;
	}
	getTotal();
}
function jsEliminarCargos(){
	if(document.getElementById("id-ivaotros")){ document.getElementById("id-ivaotros").value = 0; }
	if(document.getElementById("id-141")){ document.getElementById("id-141").value = 0; }
	if(document.getElementById("id-ivaintereses")){ document.getElementById("id-ivaintereses").value = 0; }
	if(document.getElementById("id-600")){ document.getElementById("id-600").value = 0; }
	if(document.getElementById("id-601")){ document.getElementById("id-601").value = 0; }
	//if(document.getElementById("id-432")){ document.getElementById("id-432").value = 0; }
	if(document.getElementById("id-moroso")){ document.getElementById("id-moroso").value = 0; }
	getTotal();
}
function jsAsLoaded(){
	if(window.parent){
		if(window.parent.jsEndCarga){ window.parent.jsEndCarga();	} else { setLog("NO LAYER");}
	}
}
function jsGetTotal(){
	var vv = $("#idtotal").val();
	return vv;
}
</script>
<?php
$xHP->fin(); 
?>