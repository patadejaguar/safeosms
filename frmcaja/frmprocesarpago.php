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
$msg							= "";

$xLog		= new cCoreLog();

function jsaActualizarLetra($persona, $credito, $letra, $monto){
	$NLetra	= $letra +1;
	$xCred	= new cCredito($credito,$persona); $xCred->init();
	if($xCred->isAFinalDePlazo() == false){
		if($NLetra < $xCred->getPagosAutorizados() ){
			$xPlan	= new cPlanDePagos();
			$xPlan->initByCredito($credito);
			$xPlan->setOperarCapital($NLetra, "+$monto");
		}
	}
}
function jsaAmortizarLetras($persona, $credito, $letra, $amortizable){
	$NLetra			= $letra +1;
	$xCred			= new cCredito($credito);
	$xCred->init();
	if($xCred->isAFinalDePlazo() == false){
		$xPlan			= new cPlanDePagos();
		$xPlan->initByCredito($credito);
		$msg			= "";
		$DPlan			= $xPlan->getLetrasInArray(OPERACION_CLAVE_PLAN_CAPITAL, $NLetra);
		$amortizable	= setNoMenorQueCero($amortizable);
		
		for($ixletra = $NLetra; $ixletra <= $xCred->getPagosAutorizados(); $ixletra++){
			if(isset($DPlan[$ixletra])){
				$monto		= setNoMenorQueCero( $DPlan[$ixletra] );
				if($amortizable > 0){
					if( $amortizable >= $monto ){
						//cancelar
						$xPlan->setNeutralizarParcialidad($ixletra);
					} else {
				
						$xPlan->setActualizarParcialidad($ixletra, ($monto - $amortizable), false, false );
					}
				}
				$msg	.= "WARN\t  $ixletra --- $amortizable $monto;\r\n";
				$amortizable	-= $monto;			
			}
		}
		$msg	.= $xPlan->getMessages();
		if(MODO_DEBUG == true){ setLog($msg); }
	}
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
if( $aPagaCompleto[$operacion] == true ){ $pago_total = true ; $monto_a_operar	=   TESORERIA_MONTO_MAXIMO_OPERADO; }
 
$mTipoPago			= (isset($DPar[6])) ?  trim($DPar[6]) : DEFAULT_TIPO_PAGO;
$mReciboFiscal		= (isset($DPar[7])) ? $DPar[7] : DEFAULT_RECIBO_FISCAL;
$procesado			= (isset($_GET["procesar"]) ) ? $_GET["procesar"] : "normal";

$total				= 0;
$tdCobranza			= "";
$claveCbza			= OPERACION_CLAVE_DE_COBRANZA;

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
	//$OEstado					= $xCred->getOEstado();
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
	$fecha_de_pago					= $fecha_vencimiento;
	
	if($xCred->getPeriocidadDePago() != CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
		$xLetra						= new cParcialidadDeCredito();
		if( $xLetra->init($xCred->getClaveDePersona(), $xCred->getNumeroDeCredito(), $parcialidad) == true){
			$fecha_de_pago				= $xLetra->getFechaDePago();
			$xLog->add("OK\tFecha de Pago establecida a $fecha_de_pago\r\n", $xLog->DEVELOPER);
		} else {
			$xLog->add("ERROR\tParcialidad $parcialidad no inicializada\r\n", $xLog->DEVELOPER);
			$xLog->add("ERROR\tNo existe el Periodo de Pago\r\n", $xLog->COMMON);
		}
	}

//=========================================================
//Datos del Respeto al Plan de Pagos

// ================================== VERIFICAR EL PLAN DE PAGOS =======================================
//======================================= END VERIFICATION =============================================
	$interes_normal					= 0;
	$interes_moratorio				= 0;

	$base_iva_otros					= 0;
	$base_iva_intereses 			= 0;

	$monto_iva_intereses			= 0;
	$monto_iva_otros				= 0;
	$monto_capital_operado			= 0;
	
	$xLog->add("WARN\tINTS_N\t( $interes_normal_devengado - $interes_normal_pagado ) + $interes_normal_calculado\r\n", $xLog->DEVELOPER);
	$interes_normal					= ( $interes_normal_devengado - $interes_normal_pagado ) + $interes_normal_calculado;
	$xLog->add("WARN\tINTS_M\t$interes_moratorio_devengado - $interes_moratorio_pagado ) + $interes_moratorio_calculado\r\n", $xLog->DEVELOPER);
	$interes_moratorio				= ( $interes_moratorio_devengado - $interes_moratorio_pagado ) + $interes_moratorio_calculado;
//====================== INICIAR INTERESES
//Codigo que ejecuta la formula si se respeta el Plan de Pagos
if ( $xCred->getPeriocidadDePago() != CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
	$xLog->add("WARN\tPARCIALIDAD\tEl pago es por parcialidad numero $parcialidad\r\n", $xLog->DEVELOPER);
	if ( $xCred->getRespetarPlanDePago() == false ){
		$interes_normal				= ( $interes_normal_devengado - $interes_normal_pagado ) + $interes_normal_calculado;
		$interes_moratorio			= ( $interes_moratorio_devengado - $interes_moratorio_pagado ) + $interes_moratorio_calculado;
		
		$xLog->add("WARN\tNO_PLAN\tEl Plan de Pagos es ignorado\r\n", $xLog->COMMON);
		$xLog->add("WARN\tINTERES_NORM\tEL Interes Normal Resulto en $interes_normal\r\n", $xLog->DEVELOPER);
		$xLog->add("WARN\tINTERES_MOR\tEL Interes Moratorio Resulto en $interes_moratorio\r\n", $xLog->DEVELOPER);
	} else {
		//2014-07-22 calcular mora por letra
		if($pago_total == false){
			$interes_moratorio	= $DInteres[SYS_INTERES_MORATORIO];
			$interes_normal			= 0;
			$xLog->add("WARN\tOMITIR_INT\tEL Interes Normal Resulto en $interes_normal Y EL Interes Moratorio Resulto en $interes_moratorio, SE OMITEN\r\n", $xLog->DEVELOPER);
			//$xLog->add("WARN\tOMITIR_INT\tEL Interes Normal Resulto en $interes_normal Y EL Interes Moratorio Resulto en $interes_moratorio, SE OMITEN\r\n", $xLog->COMMON);
		}
		if($xCred->getEstadoActual() == CREDITO_ESTADO_VIGENTE){	}
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

$xFRM->addFootElement("<input type='hidden' name='cobservaciones' value='' id=\"idobservaciones\" />
<input type='hidden' name='ctipo_pago' id=\"idtipo_pago\" value='$mTipoPago' /> <input type='hidden' name='crecibo_fiscal' id=\"idrecibo_fiscal\" value='$mReciboFiscal' />
<input type=\"hidden\" name='procesar' id=\"procesar\" value=\"$procesado\" />
<input type='hidden' name='ccheque' id=\"idcheque\" value='NA'  /><input type='hidden' name='idcomodin' id=\"idcomodin\" value='0'  />
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
		
		$rsm =  $ql->getDataRecord($SQLBody);

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
				if($MTipo == OPERACION_CLAVE_PLAN_INTERES){
					if($xCred->getPagosSinCapital() == true AND ($xF->getInt($fecha_operacion)< $xF->getInt($fecha_de_pago)) ){
						$interes_calculado = ( $interes_normal_devengado - $interes_normal_pagado ) + $interes_normal_calculado;
						if($monto > $interes_calculado){
							$xLog->add("WARN\t$MTipo\tINT.DEV\tInteres de la Letra Ajustado de $monto a $interes_calculado por ser menos\r\n", $xLog->COMMON);
							$monto 		= ($interes_calculado > 0) ? $interes_calculado : 0;
							$cls		= " class='warn' ";
						}
					} else {
						if($pago_total == false){
							$xLog->add("WARN\tSin cambios en Intereses $MTipo\r\n", $xLog->DEVELOPER);
						} else {
							$interes_normal	= setNoMenorQueCero(($interes_normal - $monto));
						}
					}
				}
				if ( in_array($MTipo, $aActosDeIva) ){
					$xLog->add("WARN\t$MTipo\tIVA\tEl Monto por $monto Del Movimiento $MTipo se lleva a CERO\r\n",$xLog->DEVELOPER);
					$monto_a_operar	-= $monto;
					$monto	   		= 0;
				}
				if ( $MTipo == OPERACION_CLAVE_DE_COBRANZA ){
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
			eval($rwm["codigo_de_valoracion"]);
			if($monto != 0){
				$monto				= number_format($monto, 2, '.', '');
				//class='tags green'
				$tds 				= $tds . "
				<tr>
					<th>$MTipo</th>
					<td>$title</td>
					<th>
						<input type=\"text\" name=\"c-$MTipo\" id=\"id-$MTipo\" value=\"$monto\" class='mny' onfocus=\"pushMny(event);\" onchange=\"jsEval(this);chPendiente('id-$MTipo');\" />
						<input type=\"hidden\" name=\"p-$MTipo\" id=\"idp-$MTipo\" value=\"$pendiente\" class='mny' />
					</th>
				</tr>";
			}
			$total		+= $monto;
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
				/*<input type=\"hidden\" name=\"p-moroso\" id=\"idp-moroso\" value=\"$pendiente\"/>*/
			$tds = $tds . "
			<tr>
				<th></th>
				<td>" . $xLng->getT("TR.INTERESES MORATORIOS") . "</td>
				<th><input type=\"text\" name=\"c-moroso\" id=\"id-moroso\" value=\"$monto\" class='mny' onfocus=\"pushMny(event);\" onchange=\"jsEval(this);\" />
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
				$total			+= $monto;
				$monto					= number_format($monto, 2, '.', '');
			$tds = $tds . "
			<tr>
				<th></th>
				<td>" . $xLng->getT("TR.INTERESES CORRIENTE") . "</td>
				<th><input type=\"text\" name=\"c-corriente\" id=\"id-corriente\" value=\"$monto\" class='mny'  onfocus=\"pushMny(event);\" onchange=\"jsEval(this);chPendiente('id-corriente');\" />
				<input type=\"hidden\" name=\"p-corriente\" id=\"idp-corriente\" value=\"$pendiente\"/></th>
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
				<td>" . $xLng->getT("TR.IVA SOBRE INTERESES") . "</td>
				<th><input type=\"text\" name=\"c-ivaintereses\" id=\"id-ivaintereses\" value=\"$monto\" class='mny'  onfocus=\"pushMny(event);\" onchange=\"jsEval(this);chPendiente('id-ivaintereses');\" />
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
				<th><input type=\"text\" name=\"c-ivaotros\" id=\"id-ivaotros\" value=\"$monto\" class='mny' onchange=\"jsEval(this);chPendiente('id-ivaotros');\" />
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
		$monto_capital_operado	= $saldo_actual - $mAfectCapital;
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
	$monto	        = number_format($monto, 2, '.', '');
	if( ((OPERACION_CUADRAR_CON_COBRANZA == true) AND ($operacion == OPERACION_PAGO_LETRA_VARIABLE )) AND ($monto <= OPERACION_RECIBOS_COBRANZA_LIM)){
		$xLog->add("OK\tGTOS_CBZA\tGastos de Cobranza Calculado $gastos_de_cobranza_calculado\r\n", $xLog->DEVELOPER);
		$monto		+= $gastos_de_cobranza_calculado + $montoCobranza;
		$monto		= $monto * (1/(1+$tasa_iva));//($generarIVA == false) ? $monto : $monto * (1/(1+$tasa_iva));
		$gastos_de_cobranza_calculado	= $monto;
		//$monto	        = number_format($monto, 2, '.', '');
	} else {
		$tds .= "<tr><td></td>
		<td>" . $xLng->getT("TR.MONTO DE CAPITAL DIRECTO") . "</td>
		<th><input type=\"text\" name=\"c-capital\" id=\"id-capital\" value=\"$monto\" class='mny' onchange=\"jsEval(this);\"  /></th></tr>";
	}
}
if($gastos_de_cobranza_calculado > 0){
	$gastos_de_cobranza_calculado	= number_format($gastos_de_cobranza_calculado, 2, '.', '');
	$tds 	.= "<tr><th>600</th><td>" . $xLng->getT("TR.GASTOS DE COBRANZA") . "</td>
		<th><input type=\"text\" name=\"c-$claveCbza\" id=\"id-$claveCbza\" value=\"$gastos_de_cobranza_calculado\" class='mny' onchange=\"jsEval(this);\"  /></th></tr>";
}

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
		$cEsps->setOptionSelect(OPERACION_CLAVE_DE_CARGOS_VARIOS);
		$cEsps->setEsSql();
		$cEsps->addEvent("onblur", "addEspMvto");
		$SEsp = $cEsps->show();
$xBtn		= new cHButton();

$xFRM->addToolbar($xBtn->getBasic("Ajustar", "jsGetPagoAjustado()", "dinero", "idajust", false));
$xFRM->addToolbar($xBtn->getBasic("Sin Mora", "jsEliminarCargos()", "trabajo", "idsinmora", false));
$xFRM->addToolbar($xBtn->getBasic("Vista Previa", "showVistaPago()", "ver", "idprev", false));
$xFRM->addToolbar( $xBtn->getBasic("Guardar Pago", "FormSucess()", "guardar", "idsave", false) );
$xFRM->addFootElement("<table>
	<tbody id=\"tbCobros\">	$tds </tbody>
	<tfoot>
	<tr>
		<td></td>
		<td colspan='2'>$SEsp</td></tr>
    	<tr>
    		<th><mark>Letra : $parcialidad</mark></th>
      		<th>TOTAL</th>
      		<th><input type=\"text\" name=\"ctotal\" id=\"idtotal\" value=\"$total\" class='mny' /></th>
    	</tr>
	</tfoot>
</table>");
$xFRM->addAviso("", "id_letras_de_numeros", false, "success");

if (MODO_DEBUG == true ){
	$xLog->add($xCred->getMessages(OUT_TXT), $xLog->DEVELOPER);
	$xFRM->addLog($xLog->getMessages());
}
$xFRM->addAviso($xLog->getMessages(), "sysmsg", false, "notice");


echo $xFRM->get();
echo $xHP->setBodyEnd();
$jxc ->drawJavaScript(false, true);
//XXX: Verificar generación de IVA
?>
<script>
var Wo				= new Gen();
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
var EsFinalPlazo	= <?php echo ($xCred->isAFinalDePlazo() == true) ? "true" : "false"; ?>;
<?php
$xB					= new cBases(0);
$strGrav			= $xB->getMembers_InString(true, BASE_IVA_INTERESES);
$strGrav			.= ", 'corriente-1', 'moroso-1', 'vencido-1', 'remanente-1'";
$strOGrav			= $xB->getMembers_InString(true, BASE_IVA_OTROS);
if($tasa_iva <= 0){
	echo  "var BaseGravados = new Array();\n";
} else {
	echo  "var BaseGravados = new Array($strGrav);\n";
}
echo "var BaseGravadosO = new Array($strOGrav);\n";
?>
var aIvaCalculado 	= new Array("id-ivaintereses", "id-ivaotros", "id-413", "id-1201", "id-1202", "id-1203", "id-1204");
var aCapital 		= new Array("id-410", "id-capital");
//No es efectivo, no causa IVA
function chPendiente(idTrat){
	var kID		= String(idTrat).split("-", 2);
	var mID 	= kID[1];
	var mGetID 	= document.getElementById(idTrat);
	var remMny 	= redondear(mMny) - redondear(mGetID.value);
	if (document.getElementById("idp-" + mID)) {
		var mSetID 	= document.getElementById("idp-" + mID);
		mSetID.value 	= redondear((flotante(mSetID.value) + flotante(remMny)), 2);
		setLog("El pendiente de cobro queda en " +  mSetID.value);
	}
}
function pushMny(evt){
	mMny 		= evt.target.value; 
	var idevt	= evt.target.id;
	if( $.inArray(idevt, aIvaCalculado) !== -1){
		$("#idMvtosEsp").focus();
	} else {
		evt.target.select();
	}
	getTotal(); 
}
function getTotal(){
	//Recalcula el IVA
	recalcIVA();
	var neto 	= new Number(0);	//suma de cobros
  	var isLims 	= Frm.elements.length - 1;
	var dynCap	= redondear(mSaldoCred);
	var mCaptAb	= 0;				//Capital abonado en el recibo
	for(var i=0; i<=isLims; i++){
  	  		try {
	  			var mNam = Frm.elements[i].getAttribute("name");
	  			if ((mNam!=null) && (mNam.indexOf("c-")!= -1)){
	  				if ( isNaN(Frm.elements[i].value) || Frm.elements[i].value == "" ){ Frm.elements[i].value = 0; }
					var mCurrVal	= redondear(Frm.elements[i].value, 2);
					//verificar capital
					var osid	= Frm.elements[i].getAttribute("id");
					if( $.inArray(osid, aCapital) != -1 ){
						if (mCurrVal > dynCap) {
							console.log("No puede Abonar un cantidad Mayor al Saldo de Credito de " + mSaldoCred);
							$("#" + osid).val(dynCap);	//set id a saldo
							mCurrVal	= redondear(dynCap);		//cambiar cantidad a saldo
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
  	Frm.ctotal.value	= redondear(neto);
  	getLetras();
}
function FormSucess(){
	getTotal();
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
					if ($("#idp-410").val() <= mToleraCant) {
						var mmP		= redondear($("#idp-410").val());
						if (mmP > 0) {
							var siPend	= confirm("EL PENDIENTE " + mmP + " DE CAPITAL ES MINIMO\nDESEA INCLUIRLO EN LA PROXIMA PARCIALIDAD\nY DESCARTAR LA PARCIALIDAD ACTUAL?");
							if (siPend) {
								$("#idcomodin").val( mmP );
								$("#idp-410").val(0);
								jsaActualizarLetra();
							}
						}
					}
					if((mCapitalAmort > mMontoCapital) && (mCapitalAmort < mSaldoCred) ){
						var mValorPorAmort		= redondear((mCapitalAmort - mMontoCapital));
						$("#idcomodin").val( redondear(mValorPorAmort) );
						jsaAmortizarLetras();
						setLog("AMORTIZANDO PARCIALIDADES PARA CONSUMIR " + redondear(mValorPorAmort));
						tip("#frmprocesarpago", "AMORTIZANDO PARCIALIDADES PARA CONSUMIR " + redondear(mValorPorAmort));
						setTimeout("terminarCaptura()", 6000);
						goSucess = false;							
					}
				}//Verificar cuanto tiene de capital e ir amortizando letras una a una
  			}
  		}
	//Verificar el Tipo de Pago
	if (goSucess == true) { terminarCaptura(); }
}
function terminarCaptura() {
	if (procesar== SYS_AUTOMATICO) {
		Frm.submit();
	} else {
		var siGo	= confirm("CONFIRMA GUARDAR LOS DATOS CAPTURADOS?");
		if ( siGo) {
			Frm.submit();
		}
	}
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
	}
	if(IVAInts != 0){
		if(document.getElementById("id-ivaintereses")){
			$("#id-ivaintereses").val(redondear(IVAInts) );
		} else {
			addEspMvto("ivaintereses", redondear(IVAInts), "INTERESES.- IVA POR RECALCULO");
		}
		setLog("IVA recalculado a " + redondear(IVAInts) );
	}
	//--29dic2014
	//-- setea el iva a cero si no hay base de interes
	if(IVAInts == 0){
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
		console.log("EL CONCEPTO YA EXISTE!!!");
	} else {
		nTR.innerHTML = "<th>" + TRRef + "</th>" +
						"<td>" + TRNam + "</td>" +
						"<th><input type=\"text\" name=\"c-" + TRVal + "\" id=\"id-" + TRVal + "\" value=\"" + OpValue + "\" class='mny' onfocus=\"pushMny(event);\" onchange=\"jsEval(this);\" /></th>";
		document.getElementById("id_letras_de_numeros").innerHTML = "";
	}
	document.getElementById("id-" + TRVal).focus();
	document.getElementById("id-" + TRVal).select();
}
function initComponents(){ setTimeout("getTotal()", 1000); }
function getEdoCtaCredito(){ Wo.w({url: "../rpt_edos_cuenta/rptestadocuentacredito.php?pb=" + mCredito}); }
function jsEval(origen){
	var org		= origen;
	var idR		= String(org.id).replace("id-", "");
	var mAju	= 0;
	var mTasa	= 0;
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
		
		if (desdeAjuste == true) {
		} else {
			var si2 	= confirm("CONFIRMA DESGLOSAR EL IVA DE ESTE CONCEPTO?\nQUEDARIA EN " + redondear(org.value * mFactIva));
			if (si2) { org.value	= redondear(org.value * mFactIva);  }
		}
		desdeAjuste = false;
	}
	getTotal();
}
function jsGetPagoAjustado(){
	var ixtotal	= $("#idtotal").val();
	var ajustar	= window.prompt("Monto",ixtotal);
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
	if(document.getElementById("id-ivaintereses")){ document.getElementById("id-ivaintereses").value = 0; }
	if(document.getElementById("id-600")){ document.getElementById("id-600").value = 0; }
	if(document.getElementById("id-601")){ document.getElementById("id-601").value = 0; }
	//if(document.getElementById("id-432")){ document.getElementById("id-432").value = 0; }
	if(document.getElementById("id-moroso")){ document.getElementById("id-moroso").value = 0; }
	getTotal();
}
</script>
<?php
$xHP->end(); 
?>