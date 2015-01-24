<?php
/**
 * Core  de Utilerias de Creditos
 * @author Balam Gonzalez Luis Humberto
 * @package creditos
 */
include_once("core.deprecated.inc.php");
include_once("entidad.datos.php");
include_once("core.config.inc.php");
include_once("core.fechas.inc.php");
include_once("core.contable.inc.php");
include_once("core.operaciones.inc.php");
include_once("core.creditos.inc.php");
include_once("core.common.inc.php");
include_once("core.html.inc.php");
@include_once("../libs/sql.inc.php");

class cUtileriasParaCreditos{
	private $mNoSimular 		= false;
	private $mMessages			= "";
	function __construct(){

	}

	function setAfectable($afectar = true){
		$this->mNoSimular = $afectar;
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	/**
	 * Funcion que establece un estatus segun los criterios sobre politicas de creditos
	 *
	 * @param integer $recibo		//Numero numero de recibo al que se agregan los movimientos
	 * @param date $fecha			//Fecha de Operacion
	 * @param date $AppSucursal		//si Aplica para todas las sucursales
	 * @return string $msg			//Mensages del LOG
	 */
	function setEstatusDeCreditos($recibo, $fecha = false, $AppSucursal = true, $force_updates = false, $credito = false){
			$fecha 				= ( $fecha == false) ? fechasys() : $fecha;
			
			$cierre_sucursal	= "";
			$credito			= setNoMenorQueCero($credito);
			$ql					= new MQL();
			$xLi				= new cSQLListas();
			$xLog				= new cCoreLog();
			if ($AppSucursal == true){
				//$cierre_sucursal = "AND (`creditos_solicitud`.`sucursal`='" . getSucursal() . "')";
			}
			$xLog->add("==\t\t\tMODIFICACION_DE_ESTATUS_EN_CREDITOS\r\n", $xLog->DEVELOPER);
			if($force_updates == true){$xLog->add("==\tSe Actualizaran ESTADOS\r\n", $xLog->DEVELOPER);}
			$xRec						= new cReciboDeOperacion(10);
			$tolerancia_en_pesos		= TOLERANCIA_SALDOS;
						
			$ByCredito			= $xLi->OFiltro()->CreditoPorClave($credito);
			$sqlH				= "SELECT
				`historial_de_pagos`.* FROM
			`historial_de_pagos` `historial_de_pagos` INNER JOIN `creditos_solicitud` `creditos_solicitud` 
				ON `historial_de_pagos`.`credito` = `creditos_solicitud`.
				`numero_solicitud` WHERE (`creditos_solicitud`.`estatus_actual`!=50) $cierre_sucursal $ByCredito AND (`creditos_solicitud`.`saldo_actual`>$tolerancia_en_pesos) ORDER BY `creditos_solicitud`.`fecha_vencimiento` ";
			$rsPagos		= $ql->getDataRecord($sqlH);
			$DPagos			= array();
			
			foreach ($rsPagos as $rwpagos){
				$id				= $rwpagos["credito"];
				$DPagos[$id][]	= $rwpagos;
			}
			$rsPagos			= null;
			//setLog($sqlH);
			/*INICIALIZA EL RECIBO*/
			/** @since 2010-12-27 */
			$sql 			= $xLi->getInicialDeCreditos() . " WHERE (`creditos_solicitud`.`estatus_actual`!=50) $cierre_sucursal $ByCredito AND (`creditos_solicitud`.`saldo_actual`>$tolerancia_en_pesos) ORDER BY `creditos_solicitud`.`fecha_vencimiento` ";
			$rs 			= $ql->getDataRecord($sql);
			$xDD	= new cCreditos_solicitud();
			foreach ($rs as $rw){
				$xDD->setData($rw);
				$idcredito	= $xDD->numero_solicitud()->v();
				$xCred		= new cCredito($idcredito);
				$xCred->init($rw);
				$DPago		= (isset($DPagos[$idcredito])) ? $DPagos[$idcredito] : false;
				$xCred->setDetermineDatosDeEstatus($fecha, false, $force_updates, $DPago );
				$xLog->add($xCred->getMessages(), $xLog->DEVELOPER);
			}
			$DPagos			= null;
		$this->mMessages	.= $xLog->getMessages();
		return $xLog->getMessages() ;
	}			//END FUNCTION
	function setValidarCreditos($fecha, $AppSucursal = true, $ReportToOficial = false){
			if ( !isset($fecha) ){
				$fecha 				= fechasys();
			}
			$msg	= "====\t\tVALIDADOR DE CREDITOS V 1.0.01\r\n";
			$cierre_sucursal	= "";
			if ($AppSucursal == true){
				$cierre_sucursal = "AND (`creditos_solicitud`.`sucursal`='" . getSucursal() . "')";
			}
			//
			/*INICIALIZA EL RECIBO*/
			//
			$xRec	= new cReciboDeOperacion(10);
			$tolerancia_en_pesos	= TOLERANCIA_SALDOS;

		$sqlValidacion = "SELECT
								`creditos_solicitud`.*
							FROM
									`creditos_solicitud`
							WHERE
								(`creditos_solicitud`.`estatus_actual`!=50)
								$cierre_sucursal
								AND	(`creditos_solicitud`.`saldo_actual`>$tolerancia_en_pesos)
							";

				$rs_PAM = mysql_query($sqlValidacion, cnnGeneral());
				if(!$rs_PAM){
					$msg	.= "LA CONSULTA NO SE EJECUTO (CODE: " . mysql_errno() . ")";
				}
				$i = 1;
				while($rw = mysql_fetch_array($rs_PAM)){
					$solicitud		= $rw["numero_solicitud"];
					$socio			= $rw["numero_socio"];
					$oficial		= $rw["oficial_credito"];
					$txt			= "";

					$msg			.= "$i\tVERIFICANDO EL CREDITO $solicitud DE LA PERSONA # $socio\r\n";
					$clsCred	= new cCredito($solicitud, $socio);
					$clsCred->init($rw);
					$txt		= $clsCred->setVerificarValidez($i);
					if ( $ReportToOficial == true AND (trim($txt) != "") ){
						$cOficial	= new cOficial(99);
						$cOficial->addNote(iDE_CREDITO, $oficial , $socio, $solicitud, $txt);
					}
					$msg	.= $txt;
					$i++;
				}
		$this->mMessages	.= $msg;
		return $msg;
	}
	function setReestructurarICA($fecha_corte){
		$periodo_de_calculo		= date("m", strtotime($fecha_corte));
		$ejercicio				= date("Y", strtotime($fecha_corte));
		$fecha_operacion		= $fecha_corte;
		$tipo_operacion			= 451;
		$msg					.= "============== REESTRUCTURAR EL ICA \r\n";
		/**
		 * llevar a cero los Intereses
		 */

		$sqlUICA = "UPDATE creditos_solicitud SET sdo_int_ant=0";
		my_query($sqlUICA);
		/**
		 * Eliminar el ica
		 */
				$sqlDEL = "DELETE FROM operaciones_mvtos WHERE tipo_operacion = 451 AND periodo_mensual<=$periodo_de_calculo AND periodo_anual <= $ejercicio";
				$myq = my_query($sqlDEL);

		/**
		 * Agregar el Recibo
		 *
		 */

		$NRecibo = setNuevoRecibo(1, 1, $fecha_operacion, 1, 10, "CALCULO_GENERADO_EN_UTIL_835", "NA", "ninguno",
								 "NA", 99, 0);

		$sqlConICA = "SELECT
						`creditos_solicitud`.*,
						`creditos_tipoconvenio`.*
					FROM
						`creditos_solicitud` `creditos_solicitud`
							INNER JOIN `creditos_tipoconvenio`
							`creditos_tipoconvenio`
							ON `creditos_solicitud`.`tipo_convenio` =
							`creditos_tipoconvenio`.`idcreditos_tipoconvenio`
					WHERE
						(`creditos_tipoconvenio`.`porcentaje_ica` >0) AND
						(`creditos_solicitud`.`saldo_actual` >0.99) AND

						(`creditos_solicitud`.`estatus_actual` !=50) AND
						(`creditos_solicitud`.`estatus_actual` !=98) AND
						(`creditos_solicitud`.`estatus_actual` !=99)

						/*AND
						(`creditos_solicitud`.`fecha_vencimiento` >=
						'$fecha_operacion')*/
						AND
						(`creditos_solicitud`.`fecha_ministracion` <='$fecha_operacion')
					ORDER BY
						`creditos_solicitud`.`fecha_ministracion`
					";
			//echo $sqlConICA;
			$rs = mysql_query($sqlConICA, cnnGeneral());
			while($rw = mysql_fetch_array($rs)){
				$socio				= $rw["numero_socio"];
				$solicitud			= $rw["numero_solicitud"];
				$monto_ministrado	= $rw["monto_autorizado"];
				$tasa_interes		= $rw["tasa_interes"];
				$dias_autorizados	= $rw["dias_autorizados"];
				$porcentaje_ica		= $rw["porcentaje_ica"];
				$fecha_ult_mvto		= $rw["fecha_ultimo_mvto"];
				$fecha_ministracion	= $rw["fecha_ministracion"];
				$dias_autorizados	= $rw["dias_autorizados"];
				$saldo_historico 	= $monto_ministrado;
				$iva_incluido		= $rw["iva_incluido"];
				$tasa_iva			= $rw["tasa_iva"];
				$factor_interes		= 1;

				if ($iva_incluido == "1"){
					$factor_interes	= 1 / (1 + $tasa_iva);
				}


				$dias_transcurridos 		= restarfechas($fecha_corte, $fecha_ministracion);
				$ica 						= (($dias_autorizados * $monto_ministrado * ($tasa_interes *  $factor_interes) ) / EACP_DIAS_INTERES) * $porcentaje_ica;
				$interes_generado 			= ($dias_transcurridos * $monto_ministrado * ($tasa_interes *  $factor_interes) ) / EACP_DIAS_INTERES;
				$ica_a_amortizar			= $interes_generado;

				if ( $ica_a_amortizar > $ica ){
					$ica_a_amortizar	= $ica;
				}


				//purgar posibles errores
				if ($interes_generado < 0){
					$interes_generado	= 0;
				}
				//lleva a cero el Ica a Amortizar si ya se amort
				if ($ica_a_amortizar > 0){
					$xn = setNuevoMvto($socio, $solicitud, $NRecibo, $fecha_operacion, $ica_a_amortizar,
										$tipo_operacion, 1, "REESTRUCTURACION AUTOMATICA HECHA POR $iduser");
				} else {
					$ica_a_amortizar = 0;
				}

				if ($interes_generado < $ica) {
					$ica = $ica - $interes_generado;

						if ( $ica < 0)	{
							$ica = 0;
						}

						$UICA_sql = "UPDATE creditos_solicitud SET sdo_int_ant = $ica
						WHERE numero_solicitud=$solicitud AND numero_socio=$socio";
						my_query($UICA_sql);

				}
				$msg	.= date("Y-m-d") . "\t$socio\t$solicitud\tMonto: $saldo_historico, Tasa: $tasa_interes, Factor: $factor_interes, Tasa ICA $porcentaje_ica\r\n";
				$msg	.= date("Y-m-d") . "\t$socio\t$solicitud\tI.C.A. $ica, Int. Generado: $interes_generado, Dias Trans.: $dias_transcurridos/$dias_autorizados, ICA A AMort: $ica_a_amortizar\r\n";
			}
		return $msg;
	}
	/**
	 * Fuction que cuadra los creditos en base a los movimientos del mismo
	 * @return string		Logs de Mensajes del proceso
	 */
	function setCuadrarCreditosByMvtos($credito = false){
		$SQLAbs = "SELECT * FROM creditos_abonos_acumulados";
		$SQLAbs	.= ($credito == false ) ? "" : " WHERE docto_afectado=$credito LIMIT 0,1 ";
		$rsAbs	= mysql_query($SQLAbs, cnnGeneral());
		$fecha	= fechasys();
		$msg	= "";
				while ($rw = mysql_fetch_array($rsAbs)){
					$docto 	= $rw["docto_afectado"];
					$socio	= $rw["socio_afectado"];
					$monto	= $rw["total_abonado"];

					$sqlUcred	= "UPDATE creditos_solicitud SET saldo_actual = (monto_autorizado - $monto),
									saldo_vencido = (monto_autorizado - $monto),
									notas_auditoria = 'Util_842_$fecha'
									WHERE numero_solicitud = $docto
									AND numero_socio	= $socio ";
							$x 	= my_query($sqlUcred);
							if ($x["stat"] == false ){
								$msg	.= date("H:i:s") . "\t$socio\t$docto\tSe **FALLO** AL ACTUALIZAR (" . $x["error"] . ") \r\n";
							} else {
							 	$msg	.= date("H:i:s") . "\t$socio\t$docto\tRestando al credito un monto de $monto \r\n";
							}
				}
		return $msg;
	}
	function setCuadrarCreditosBySaldo($mFechaInicial = false){
		if ( $mFechaInicial == false ){
			$mFechaInicial	= fechasys();
		}
		$msg	.= "=================== CUADRAR CREDITOS A PARTIR DEL SALDO EN CREDITOS\r\n";
		$cRec		= new cReciboDeOperacion(10);
		$xRec		= $cRec->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO, fechasys(), 1, 10, "RECIBO_DE_AJUSTES_DE_CAPTACION");
		$msg		.= "\t\tRECIBO\tEl Recibo de Operacion es $xRec\r\n";
		$cRec->setNumeroDeRecibo($xRec, true);

		//Array de Abonos Acumulados
		$arrAbAcumulados	= array();
		$SQLAbs = "SELECT * FROM creditos_abonos_acumulados";
		$rsAbs	= mysql_query($SQLAbs, cnnGeneral());

				while ($rw = mysql_fetch_array($rsAbs)){
					$docto 	= $rw["docto_afectado"];
					$socio	= $rw["socio_afectado"];
					$monto	= $rw["total_abonado"];
					$arrAbAcumulados["$socio-$docto"] = round( $monto , 2);
				}

		$sql = "SELECT
					`creditos_solicitud`.*,
					`creditos_tipoconvenio`.*,
					`creditos_periocidadpagos`.*,
					`creditos_estatus`.*,
					`creditos_solicitud`.`tasa_interes` AS `tasa_ordinaria_anual`,
					`creditos_solicitud`.`tipo_autorizacion` AS `tipo_de_autorizacion`,
					`creditos_solicitud`.`tasa_ahorro` AS `tasa_de_ahorro`
				FROM
					`creditos_tipoconvenio` `creditos_tipoconvenio`
						INNER JOIN `creditos_solicitud` `creditos_solicitud`
						ON `creditos_tipoconvenio`.`idcreditos_tipoconvenio`
						= `creditos_solicitud`.`tipo_convenio`
							INNER JOIN `creditos_periocidadpagos`
							`creditos_periocidadpagos`
							ON `creditos_periocidadpagos`.
							`idcreditos_periocidadpagos` =
							`creditos_solicitud`.`periocidad_de_pago`
								INNER JOIN `creditos_estatus`
								`creditos_estatus`
								ON `creditos_estatus`.`idcreditos_estatus` =
								`creditos_solicitud`.`estatus_actual`
				WHERE
					(`creditos_solicitud`.`fecha_ministracion` >='$mfechaInicial' ";
			$rs	= mysql_query($sql, cnnGeneral());

				while ($rw = mysql_fetch_array($rsAbs)){
					$docto 	= $rw["numero_solicitud"];
					$socio	= $rw["numero_socio"];
					$saldo	= $rw["saldo_actual"];
					$minis	= $rw["monto_autorizado"];
					$pagos	= $minis - $saldo;	//100-10=90
					//
					$monto	= $arrAbAcumulados["$socio-$docto"];
					if ( !isset($monto) OR ( is_null($monto) )  ){
						$monto	= 0;
					}
					$dif	= round( ($pagos - $monto), 2);	//90 - 80 = 10 OR 80 - 90 = -10
					//si el
					if ( $dif != 0 ){
								//Se inicializa una nueva instancia de crédito
								$cCredito 		= new cCredito($docto, $socio);
								//y se neutralizara con su valor absoluto.
								$cCredito->init($rw);
								$cCredito->setReciboDeOperacion($xRec);
								//Generar un abono a Capital
								$cCredito->setAbonoCapital($dif);
								$msg	.= "$socio\t$credito\tDIFF\tAjustando una Diferencia de $dif \r\n";
								$msg	.=  $cCredito->getMessages("txt");
					} else {
						$msg	.= "$socio\t$docto\tNO_DIFF\tNo Existe Diferencias Sustantivas\r\n";
					}
				}
		return $msg;
	}

	function setReestructurarSDPM_Planes($SoloConSaldos = false, $creditoFiltrado = false, $forzarTodos = false, $fechaCorte = false, $fechaInicial = false, $EliminarTodo = true){
		$msg			= "";
		$xLog			= new cCoreLog();
		$xF				= new cFecha();
		$ql				= new MQL();
		$creditoFiltrado= setNoMenorQueCero($creditoFiltrado);
		if($creditoFiltrado > DEFAULT_CREDITO){ $xLog->add("Socio\tCredito\tFecha\tMonto\tSaldo\tDias\tOperacion\tEstatus\tInteres\tMoratorios\r\n"); }
		$fechaCorte		= $xF->getFechaISO($fechaCorte);
		$fechaInicial	= ($fechaInicial == false) ? EACP_FECHA_DE_CONSTITUCION : $fechaInicial;
		$wCredito1		= ( $creditoFiltrado > DEFAULT_CREDITO) ? "  `creditos_mvtos_asdpm_planes`.`documento` = $creditoFiltrado " : " `documento` > 0";
		$wCredito2		= ( $creditoFiltrado > DEFAULT_CREDITO) ? "  numero_de_credito = $creditoFiltrado " : " numero_de_credito > 0 ";
		$ByRTM			= ( $creditoFiltrado > DEFAULT_CREDITO) ? " " : " SQL_CACHE " ;
		
		$wFecha1		= " AND (fecha >='$fechaInicial' AND fecha <='$fechaCorte' ) ";
		$wFecha2		= " AND (`fecha_actual` >= '$fechaInicial' AND `fecha_actual`<='$fechaCorte' ) ";
		
		
		$sql 			= "SELECT $ByRTM `creditos_mvtos_asdpm_planes`.* FROM `creditos_mvtos_asdpm_planes` WHERE $wCredito1 $wFecha1 ";
		$rs				= $ql->getDataRecord($sql);
		if($EliminarTodo == true){ $DAction		= my_query("DELETE FROM creditos_sdpm_historico WHERE $wCredito2 $wFecha2");		}
		
		$saldo				= 0;
		$creditoA			= 0;
		$xT					= new cTipos();
		$xF					= new cFecha();
		
		$FECHA_DE_ULTIMO_PAGO	= EACP_FECHA_DE_CONSTITUCION;
		$CREDITO_SALDO_ANTERIOR	= 0;
		$MvtoAnterior			= OPERACION_CLAVE_MINISTRACION;
		$ESTADO_ACTUAL			= CREDITO_ESTADO_VIGENTE;
		$DIVISOR_DE_INTERESES	= EACP_DIAS_INTERES;
		$DCred					= array();
		$IsCredNew				= true;
		$xCred					= null;		
		
		foreach ( $rs as $rw ){
			$socio			= $xT->cInt($rw["socio"]);
			$credito		= $xT->cInt($rw["documento"]);
			$operacion		= $xT->cInt($rw["operacion"]);
			$periodo		= $xT->cInt($rw["periodo"]);
			$afectacion		= $rw["afectacion"];
			$monto			= $xT->cFloat($rw["monto"], 2);
			$fecha			= $rw["fecha"];
			$nota			= "";
			$dias_tolerados	= DIAS_PAGO_VARIOS;
			
			$IsCredNew		= true;
			if( $creditoA != $credito ){
				$saldo					= 0;
				$FECHA_DE_ULTIMO_PAGO	= $fecha;
				$ESTADO_ACTUAL			= CREDITO_ESTADO_VIGENTE;
				$xCred					= new cCredito($credito, $socio); $xCred->init();
				$DCred					= $xCred->getDatosDeCredito();
				$CREDITO_SALDO_ANTERIOR	= 0;
				$dias_tolerados			= $xCred->getOPeriocidad()->getDiasToleradosEnVencer();
				//si es Ministracion
				if($MvtoAnterior	== OPERACION_CLAVE_MINISTRACION){ $FECHA_DE_ULTIMO_PAGO	= $xCred->getFechaDeMinistracion(); }
				$xLog->add("======\t\tINIT-CREDITO : $credito\r\n");
				$letras_en_mora			= array();
				if($EliminarTodo == false){ my_query("DELETE FROM creditos_sdpm_historico WHERE numero_de_credito = $credito $wFecha2"); }
			} else {
				$IsCredNew		= false;
			}
			$interes				= 0;
			$moratorio				= 0;
			if($operacion == OPERACION_CLAVE_PLAN_CAPITAL AND $monto > 0){
				if( $xF->getInt($fecha) <= $xF->getInt($fechaCorte)  ){
					$letras_en_mora[$periodo][SYS_MONTO]		= $monto;
					$letras_en_mora[$periodo][SYS_FECHA]		= $fecha;
					$letras_en_mora[$periodo][SYS_DEFAULT]		= $fecha;
					$letras_en_mora[$periodo][SYS_INTERES_MORATORIO]		= ($monto * $xCred->getTasaDeMora()) / EACP_DIAS_INTERES; //TODO: Formular en SQL.- tasa mora para planes
					$xLog->add( "WARN\tAgregando letra en mora por $monto del periodo $periodo de $fecha\r\n", $xLog->DEVELOPER);
				}
			}
			if($operacion == OPERACION_CLAVE_PLAN_CAPITAL AND $monto >0){
				if(!isset($FECHA_DE_COMPROMISO)){
					$FECHA_DE_COMPROMISO	= $fecha;
					$xLog->add( "WARN\tAgregando fecha de primer atraso a $fecha del pago $periodo\r\n", $xLog->DEVELOPER);
					//$xLog->add( , $xLog->DEVELOPER);
				}
			}
			if($operacion == OPERACION_CLAVE_PLAN_INTERES OR $operacion == OPERACION_CLAVE_PAGO_INTERES){
				$interes				= $monto;
			} else {
				$interes				= 0;
			}
			//XXX: Checar
			$saldo_calculado			= 0;
			if($operacion == OPERACION_CLAVE_PAGO_CAPITAL OR $operacion == OPERACION_CLAVE_MINISTRACION){
				$dias_transcurridos		= $xF->setRestarFechas($fecha, $FECHA_DE_ULTIMO_PAGO);
				$saldo_calculado		= $saldo * $dias_transcurridos;
				$saldo					+= ($monto * $afectacion);
				$FECHA_DE_ULTIMO_PAGO	= $fecha;
				//disminuye de la letra
				if($operacion == OPERACION_CLAVE_PAGO_CAPITAL){
					if( isset($letras_en_mora[$periodo])){
						$letras_en_mora[$periodo][SYS_MONTO] -= $monto;
						$xLog->add("WARN\t$periodo\tDisminuir base de mora por $monto\r\n" , $xLog->DEVELOPER);
						if(setNoMenorQueCero($letras_en_mora[$periodo][SYS_MONTO]) <= 0){
							unset($letras_en_mora[$periodo]);
						}
					}
				}
			} else {
				$monto					= 0;
			}
			if($operacion == OPERACION_CLAVE_FIN_DE_MES){
				$moratorio		= 0;
				//if(MODO_DEBUG == true){ $msg	.= "WARN\tFIN DE MES $operacion\r\n"; }
				//Calcular moratorios
				//setLog($letras_en_mora);
				foreach($letras_en_mora as $id => $atrasos ){
					
					$fecha_letra	= $atrasos[SYS_DEFAULT];
					$xLog->add("WARN\t$periodo\t$id $fecha_letra\r\n" , $xLog->DEVELOPER);

					if( $xF->getInt($fecha) >= $xF->getInt($fecha_letra)  ){
						$dias_mora			=  $xF->setRestarFechas( $fecha, $fecha_letra);
						$xLog->add("WARN\t$periodo\tDias de Mora del periodo $id por dias $dias_mora\r\n" , $xLog->DEVELOPER);
						$moratorio			+= $atrasos[SYS_INTERES_MORATORIO] * $dias_mora;
						$letras_en_mora[$id][SYS_DEFAULT] = $fecha;
					}
				}
			}
			if( isset($FECHA_DE_COMPROMISO) ){
				$dias_de_atraso			= $xF->setRestarFechas($fecha, $FECHA_DE_COMPROMISO);
				if($dias_de_atraso > 1){
					$ESTADO_ACTUAL		= CREDITO_ESTADO_MOROSO;
				}
				if($dias_de_atraso > $dias_tolerados){
					$ESTADO_ACTUAL		= CREDITO_ESTADO_VENCIDO;
					$xLog->add( "WARN\t$periodo\tPeriodo a Vencido por $dias_de_atraso|$dias_tolerados\r\n", $xLog->DEVELOPER);
				}
			}
			if($creditoFiltrado > DEFAULT_CREDITO){ $xLog->add("$socio\t$credito\t$fecha\t$monto\t$saldo\t$dias_transcurridos\t$operacion\t$ESTADO_ACTUAL\t$interes\t$moratorio\t$nota\r\n"); }
			
			if($xF->getInt($fecha) <= $xF->getInt(SYS_FECHA_DE_MIGRACION) ){
				$interes				= 0;
				$moratorio				= 0;
				$xLog->add("WARN\tOMITIR Interes $interes y MORA por $moratorio por estar antes de la migracion $fecha\r\n" , $xLog->DEVELOPER);
			}
			//agregando letras en Intereses en mora
			$mm							= $monto + $interes + $moratorio;
			if(($xF->getInt($fecha) <= $xF->getInt($fechaCorte) AND ($mm >0 ) )){
				$xCred->addSDPM($interes, $moratorio, $FECHA_DE_ULTIMO_PAGO, $saldo, $ESTADO_ACTUAL, $fecha, $operacion, $saldo_calculado, $periodo);
			}
	 
		
			if ( ($saldo <= TOLERANCIA_SALDOS) ){ $xLog->add("======\t\tEND-CREDITO : $credito\r\n"); }
			$creditoA					= $credito;
			
			$CREDITO_SALDO_ANTERIOR		= $saldo;
			$MvtoAnterior				= $operacion;
			
		}	
		return $xLog->getMessages();	
	}
	function setReestructurarSDPM($SoloConSaldos = false, $credito = false, $forzarTodos = false, $fechaCorte = false, $fechaInicial = false, $EliminarTodo = true){
		//FIXME: probar con saldo anterior
		$fechaCorte	= ($fechaCorte == false) ? fechasys() : $fechaCorte;
		$msg		= "";
		$wCredito1	= ( $credito != false) ? "  `creditos_mvtos_asdpm`.`documento` = $credito" : " `documento` > 0 ";
		$wCredito2	= ( $credito != false) ? "  numero_de_credito = $credito " : " numero_de_credito > 0";
		$ByRTM		= ( $credito != false) ? " " : " SQL_CACHE " ;
		$wFecha1		= " AND (fecha >='$fechaInicial' AND fecha <='$fechaCorte' ) ";
		$wFecha2		= " AND (`fecha_actual` >= '$fechaInicial' AND `fecha_actual`<='$fechaCorte' ) ";
		$ql				= new MQL();
		
		
		$arrEstatusD	= array ( OPERACION_CLAVE_MINISTRACION, 111,	114, 115);
		$arrEstatus		= array (
				OPERACION_CLAVE_MINISTRACION => CREDITO_ESTADO_VIGENTE, 111 => CREDITO_ESTADO_VENCIDO,
				114 => CREDITO_ESTADO_VIGENTE, 115 => CREDITO_ESTADO_MOROSO
		);
		//Eliminar el SDPM
		if($EliminarTodo == true){
			$DAction		= $ql->setRawQuery("DELETE FROM creditos_sdpm_historico WHERE $wCredito2 $wFecha2 ");
			if(MODO_DEBUG == true){ $msg			.= "WARN\tEliminando SDPM\r\n"; }
		}
		
		if($credito != false){
			$ql->setRawQuery("DELETE FROM operaciones_mvtos WHERE docto_afectado=$credito AND tipo_operacion = 420");
			if(MODO_DEBUG == true){ $msg		.= "WARN\tEliminando Operaciones 420 del credito $credito\r\n"; }
		}
		$msg		.= "Socio\tCredito\tFecha\tMonto\tSaldo\tDias\tOperacion\tEstatus\tInteres\tMoratorios\r\n";
		//Generar saldos de credito por mes
		$sql = "SELECT $ByRTM
		`creditos_mvtos_asdpm`.`socio`,
		`creditos_mvtos_asdpm`.`documento`,
		`creditos_mvtos_asdpm`.`recibo`,
		`creditos_mvtos_asdpm`.`fecha`,
		`creditos_mvtos_asdpm`.`operacion`,
		`creditos_mvtos_asdpm`.`monto`,
		`creditos_mvtos_asdpm`.`afectacion`
		FROM `creditos_mvtos_asdpm`	WHERE $wCredito1 $wFecha1 ";
		//setLog($sql);
		
		$rsM				= $ql->getDataRecord($sql);
	
		$saldo				= 0;
		$creditoA			= 0;
		$xT					= new cTipos();
		$xF					= new cFecha();
	
		$FECHA_DE_ULTIMO_PAGO	= "1998-01-01";
		$CREDITO_SALDO_ANTERIOR	= 0;
		$MvtoAnterior			= OPERACION_CLAVE_MINISTRACION;
		$ESTADO_ACTUAL			= CREDITO_ESTADO_VIGENTE;
		$DIVISOR_DE_INTERESES	= EACP_DIAS_INTERES;
		$DCred					= array();
		$IsCredNew				= true;
		$xCred					= null;
	
		foreach ($rsM as $rw){
				
			$socio			= $xT->cInt($rw["socio"]);
			$credito		= $xT->cInt($rw["documento"]);
			$fecha			= $rw["fecha"];
			$nota			= "";
			//
			$IsCredNew		= true;
			if( $creditoA != $credito ){
				$saldo					= 0;
				$FECHA_DE_ULTIMO_PAGO	= $fecha;
				$ESTADO_ACTUAL			= CREDITO_ESTADO_VIGENTE;
				$xCred					= new cCredito($credito, $socio); $xCred->init();
				$DCred					= $xCred->getDatosDeCredito();
				$CREDITO_SALDO_ANTERIOR	= 0;
					
				//si es Ministracion
				if($MvtoAnterior	== OPERACION_CLAVE_MINISTRACION){ $FECHA_DE_ULTIMO_PAGO	= $xCred->getFechaDeMinistracion(); }
				$msg	.= "------------------------\tNUEVO_CREDITO : $credito------------------------------\r\n";
				if($EliminarTodo == false){ my_query("DELETE FROM creditos_sdpm_historico WHERE numero_de_credito = $credito $wFecha2"); }
			} else {
				$IsCredNew		= false;
			}
			$OProd					= $xCred->getOProductoDeCredito();
	
			$recibo					= $rw["recibo"];
			$operacion				= $rw["operacion"];
			$afectacion				= $rw["afectacion"];
			$monto					= $xT->cFloat($rw["monto"], 2);
			$periocidad				= $xCred->getPeriocidadDePago();
			$FechaVencimiento		= $xCred->getFechaDeVencimiento(); //(!isset( $DCred["fecha_vencimiento_dinamico"])) ? $xCred->getFechaDeVencimiento() : $DCred["fecha_vencimiento_dinamico"];
	
			$DiaInteresMax			= $xF->setSumarDias(INTERES_DIAS_MAXIMO_A_DEVENGAR, $FechaVencimiento);
			$dias_transcurridos		= $xF->setRestarFechas($fecha, $FECHA_DE_ULTIMO_PAGO);
			$saldo_calculado		= $dias_transcurridos * $saldo;
			//No poner la afectacion
			$saldo					+= $xT->cFloat( ($monto * $afectacion), 2 );
			// si es normal, calcular normal, si es mora: Calcular mora y normal, si es vencido: calcular normal y mora
			$interes				= 0;
			$moratorio				= 0;
			$TASA_NORMAL			= $xCred->getTasaDeInteres();
			$TASA_MORA				= $xCred->getTasaDeMora();
			$TIPO_DE_PAGO			= $xCred->getTipoDePago();
			$PAGOS_SIN_CAPITAL		= $xCred->getPagosSinCapital();
			$MONTO_ORIGINAL_DE_CREDITO	= $xCred->getMontoAutorizado();
			$saldoBase					= $xT->cFloat($saldo, 2 );
	
			if ( ($operacion == OPERACION_CLAVE_PAGO_CAPITAL) AND ($saldo == 0)  ){	$saldoBase			= $CREDITO_SALDO_ANTERIOR; }
			if($MvtoAnterior == OPERACION_CLAVE_MINISTRACION){
				$saldoBase			= $CREDITO_SALDO_ANTERIOR;
				$saldo_calculado	= $dias_transcurridos * $CREDITO_SALDO_ANTERIOR;
			}
			$SALDO_ACTUAL			= $xCred->getSaldoActual();
			$BASE_NORMAL			= $saldo_calculado;
			$BASE_MORA				= $saldo_calculado;
			eval($OProd->getPreModInteres());
	
			
			//considerar si es un maximo de x dias devengar en vencidos
			switch( $ESTADO_ACTUAL ){
				case CREDITO_ESTADO_VIGENTE:
					$interes		= ($BASE_NORMAL * $TASA_NORMAL) / $DIVISOR_DE_INTERESES;
					break;
				case CREDITO_ESTADO_VENCIDO:
					//validar si tiene un maximo de dias transcurridos de devengado
					if( $xF->getInt($fecha) <= $xF->getInt($DiaInteresMax) ){
						$interes		= ($BASE_NORMAL * $TASA_NORMAL) / $DIVISOR_DE_INTERESES;
						$moratorio		= ($BASE_MORA * $TASA_MORA) / $DIVISOR_DE_INTERESES;
					} else {
						$interes		= 0;
						$moratorio		= 0;
						$nota			= "Despues del $DiaInteresMax no se acumula Interes, Vencimiento : $FechaVencimiento";
					}
					break;
				case CREDITO_ESTADO_MOROSO:
					$interes		= ($BASE_NORMAL * $TASA_NORMAL) / $DIVISOR_DE_INTERESES;
					$moratorio		= ($BASE_MORA * $TASA_MORA) / $DIVISOR_DE_INTERESES;
					break;
			} //END_SWITCH
			$interes			= $xT->cFloat($interes, 2);
			if($periocidad == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
				$moratorio			= $xT->cFloat($moratorio, 2);//0; //2012-04-10
			} else {
				//$moratorio			= 0;
			}
			eval($OProd->getPosModInteres());
			$msg		.= "$socio\t$credito\t$fecha\t$monto\t$saldo\t$dias_transcurridos\t$operacion\t$ESTADO_ACTUAL\t$interes\t$moratorio\t$nota\r\n";
			
			if($xF->getInt($fecha) <= $xF->getInt(SYS_FECHA_DE_MIGRACION) ){
				$interes				= 0;
				$moratorio				= 0;
				if(MODO_DEBUG == true){ $msg					.= "WARN\tOMITIR Interes $interes y MORA por $moratorio por estar antes de la migracion $fecha\r\n"; }
			}
			
			if($xF->getInt($fecha) <= $xF->getInt($fechaCorte)){
				$msgSDPM				= $xCred->addSDPM($interes, $moratorio, $FECHA_DE_ULTIMO_PAGO, $saldo, $ESTADO_ACTUAL, $fecha, $operacion, $saldo_calculado);
				//if(MODO_DEBUG == true){ $msg	.= $msgSDPM;	}
			}
	
			if ( ($saldo <= TOLERANCIA_SALDOS) ){ $msg	.= "------------------------------------------------------\r\n"; }
			$creditoA					= $credito;
			$FECHA_DE_ULTIMO_PAGO		= $fecha;
			$CREDITO_SALDO_ANTERIOR		= $saldo;
			$MvtoAnterior				= $operacion;
			/*
			 * si existe la operacion de cambio de estatus, validar en el array, y actualizar el valor
			* Este estatus se aplicara en el siguiente
			*/
			if ( in_array($operacion, $arrEstatusD ) ){ $ESTADO_ACTUAL		= $arrEstatus[$operacion]; }
		}
		return $msg;
	}
	
	function setRegenerarInteresDevengado($credito = false, $fechaInicial = false, $fechaFinal = false, $ForceMoratorios = false){
		$fechaInicial	= ( $fechaInicial == false ) ? "2009-01-01" : $fechaInicial;
		$fechaFinal		= ( $fechaFinal == false ) ? fechasys() : $fechaFinal;
		//==============================================================================
		$fechaRec		= $fechaFinal;
		$observaciones	= "REGENERACION AUTOMATICA DE INTERESES";
		//==============================================================================
		$CRecibo 		= new cReciboDeOperacion(10, false);
		$xF				= new cFecha();
		$xQL			= new MQL();
		$recibo 		= $CRecibo->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO,
									$fechaRec, 1, 10,
									$observaciones, DEFAULT_CHEQUE, "ninguno", DEFAULT_RECIBO_FISCAL, DEFAULT_GRUPO );
		$CRecibo->setNumeroDeRecibo($recibo);
		
		$_SESSION["recibo_en_proceso"]		= $recibo;
		
		$msg			= "FECHA INICIAL: $fechaInicial\tFECHA FINAL: $fechaFinal\r\n";
		//Equivalencias de movimientos
		//TODO: Solo generar creditos 360
		$wSDPM			= "";
		$wMvto			= "";
		$cFols			= 0;
		$wDLimit		= " WHERE ( fecha_actual >= '$fechaInicial'  AND fecha_actual <='$fechaFinal' )";
		$wDLimitM		= " AND ( fecha_operacion >= '$fechaInicial' AND fecha_operacion <='$fechaFinal' ) ";
		if ( $credito != false ){
			$wSDPM		= " AND numero_de_credito = $credito ";
			$wMvto		= " AND docto_afectado = $credito ";
		}
		$sqlDM	= "DELETE FROM operaciones_mvtos
													WHERE
													(tipo_operacion=420
													OR tipo_operacion= 421
													OR tipo_operacion=422
													OR tipo_operacion=431
													OR tipo_operacion=432
													OR tipo_operacion=433
													OR tipo_operacion=434
													OR tipo_operacion=435
													OR tipo_operacion=436
													OR tipo_operacion=437
													OR tipo_operacion=451) $wDLimitM $wMvto ";
		my_query($sqlDM);		
		$sqlM	= "SELECT SQL_CACHE
					`creditos_sdpm_historico`.`numero_de_socio`,
					`creditos_sdpm_historico`.`numero_de_credito`,
					`creditos_sdpm_historico`.`tipo_de_operacion`,
					`creditos_sdpm_historico`.`estatus`,
					`creditos_sdpm_historico`.`fecha_actual`,
					`creditos_sdpm_historico`.`fecha_anterior`,
					`creditos_sdpm_historico`.`dias_transcurridos`,
					`creditos_sdpm_historico`.`monto_calculado`,
					`creditos_sdpm_historico`.`saldo`,
					`creditos_sdpm_historico`.`interes_normal`,
					`creditos_sdpm_historico`.`interes_moratorio`,
					`creditos_sdpm_historico`.`periodo`
				FROM
					`creditos_sdpm_historico` `creditos_sdpm_historico` $wDLimit $wSDPM ";
		$rs 		= $xQL->getDataRecord($sqlM);// getRecordset( $sqlM );
		foreach ($rs as $rw ){
			$socio			= $rw["numero_de_socio"];
			$solicitud		= $rw["numero_de_credito"];
			$observaciones	= "Dias:" . $rw["dias_transcurridos"] . " FechaAnterior: " . $rw["fecha_anterior"] . " Saldo:" . $rw["saldo"];
			$fecha			= $rw["fecha_actual"];
			$estatus		= $rw["estatus"];
			$moratorio		= $rw["interes_moratorio"];
			$interes		= $rw["interes_normal"];
			$periodo		= $rw["periodo"];
			$OpNormal		= 420;
			$OpMora			= 431;
			switch ( $estatus ){
				case CREDITO_ESTADO_VIGENTE:
					//$OpNormal		= 420;
					//$OpMora			= 431;
					break;
				case CREDITO_ESTADO_VENCIDO:
					//$OpNormal		= 432;
					//$OpMora			= 435;					
					break;
				case CREDITO_ESTADO_MOROSO:
					//$OpNormal		= 421;
					//$OpMora			= 431;
					break;
			}
			//repara los folios por ser eficiente
			if ( $cFols >= 1000 ){
				$cFols = 0;
				setFoliosAlMaximo();	
			}
			if ( $moratorio > 0 ){
				/* 2012-04-10 : No registrar Moratorio */
				//if($ForceMoratorios == true){
					$CRecibo->setNuevoMvto($fecha, $moratorio, $OpMora, $periodo, $observaciones, 1, TM_CARGO, $socio, $solicitud);
				//}
			}
			if ( $interes > 0 ){
				$CRecibo->setNuevoMvto($fecha, $interes, $OpNormal, $periodo, $observaciones, 1, TM_CARGO, $socio, $solicitud);
			}
			$cFols++;
		}
		$CRecibo->setFinalizarRecibo();
		$msg	.= $CRecibo->getMessages();
		return $msg;		
	}
	function setGenerarMvtoFinDeMes($fecha_inicial, $fecha_final, $NumeroDeCredito = false, $ForzarEnPeriodo = false){
		/**
		 * @var $ForzarPeriodo
		 * se refiere a que debe actualizar el saldo conciliado segun los pagos acumulados a la fecha
		 * */
		$msg				= "==\t\tGENERAR MOVIMIENTOS DEL FIN DE MES\t\t==\r\n";
		$arrPagosF			= array();
		$arrPagosM			= array();
		$xQl				= new MQL();
	    //$wByCredito			= " AND ( (`creditos_solicitud`.`fecha_ministracion` >= '$fecha_inicial' ) AND (`creditos_solicitud`.`fecha_ministracion` <= '$fecha_final' ) ) ";
	    $wByCredito			= "";
	    $wByCredito			= ( $NumeroDeCredito != false ) ? " AND (`creditos_solicitud`.`numero_solicitud` = $NumeroDeCredito) " : $wByCredito ;
	    
	    $wByMvto			= " AND fecha_operacion>='$fecha_inicial' AND fecha_operacion<='$fecha_final'  ";
	    $wByMvto			= ( $NumeroDeCredito != false ) ? " AND docto_afectado = $NumeroDeCredito " : $wByMvto;
		//==============================================================================
		$fechaRec			= fechasys();
		$observaciones		= "GENERADO_EN_LA_UTILERIA_888";
		//==============================================================================
		$sqlDM				= "DELETE FROM operaciones_mvtos WHERE tipo_operacion=999 $wByMvto ";
		// Agregado el 2012-01-17- Ya lo habia corregido???
		my_query($sqlDM);
		//==============================================================================
		if ( $ForzarEnPeriodo == true ){
			$sqlP			= "SELECT
								`operaciones_mvtos`.`socio_afectado`       AS `socio`,
								`operaciones_mvtos`.`docto_afectado`       AS `documento`,
								`operaciones_mvtos`.`tipo_operacion`       AS `operacion`,
								MAX(`operaciones_mvtos`.`fecha_operacion`) AS `fecha`,
								SUM(`operaciones_mvtos`.`afectacion_real`) AS `monto` 
							FROM
								`operaciones_mvtos` `operaciones_mvtos` 
							WHERE
								(`operaciones_mvtos`.`tipo_operacion` = 120) 
								AND
								(`operaciones_mvtos`.`fecha_operacion` <='$fecha_final')
								$wByMvto
							GROUP BY
								`operaciones_mvtos`.`docto_afectado`,
								`operaciones_mvtos`.`tipo_operacion` ";
			$rsP			= $xQl->getDataRecord($sqlP);
			$msg			.= "============ ULTIMOS MVTOS HASTA LA FECHA $fecha_final \r\n";	
			foreach ($rsP as $rwP){
				$mSocio			= $rwP["socio"];
				$mCredito		= $rwP["documento"];
				$mFecha			= $rwP["fecha"];
				$mMonto			= $rwP["monto"];
				$arrPagosF[ $mCredito ]	= $mFecha;
				$arrPagosM[ $mCredito ]	= $mMonto;
				$msg			.= "$mSocio\t$mCredito\tFecha $mFecha\t Monto: $mMonto\r\n";
			}
		}
		//==============================================================================
		$CRecibo 	= new cReciboDeOperacion(10, false);			
			$sql = "SELECT SQL_CACHE
					`creditos_solicitud`.*,
					`creditos_tipoconvenio`.*,
					`creditos_periocidadpagos`.*,
					`creditos_estatus`.*,
					`creditos_solicitud`.`tasa_interes` AS `tasa_ordinaria_anual`,
					`creditos_solicitud`.`tipo_autorizacion` AS `tipo_de_autorizacion`,
					`creditos_solicitud`.`tasa_ahorro` AS `tasa_de_ahorro`
				FROM
					`creditos_tipoconvenio` `creditos_tipoconvenio`
						INNER JOIN `creditos_solicitud` `creditos_solicitud`
						ON `creditos_tipoconvenio`.`idcreditos_tipoconvenio`
						= `creditos_solicitud`.`tipo_convenio`
							INNER JOIN `creditos_periocidadpagos`
							`creditos_periocidadpagos`
							ON `creditos_periocidadpagos`.
							`idcreditos_periocidadpagos` =
							`creditos_solicitud`.`periocidad_de_pago`
								INNER JOIN `creditos_estatus`
								`creditos_estatus`
								ON `creditos_estatus`.`idcreditos_estatus` =
								`creditos_solicitud`.`estatus_actual`
				WHERE
								(`creditos_solicitud`.`estatus_actual` !=50)
								$wByCredito
								 ";
			$rsMx				= $xQl->getDataRecord($sql);// mysql_query($sql, cnnGeneral() );
			//$msg				.= $sql ."\r\n" . $sqlDM  . "\r\n";
			$msg				.= "============ GENERANDO MOVIMIENTOS 999: Saldos al Fin del mes \r\n";	
			$xF					= new cFecha();
			$recibo 	= $CRecibo->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO,
									$fechaRec, 1, 10,
									$observaciones, DEFAULT_CHEQUE, "ninguno", DEFAULT_RECIBO_FISCAL, DEFAULT_GRUPO );
			$CRecibo->setNumeroDeRecibo($recibo);

				
			foreach ( $rsMx as $rw ){
				//corregir cuando yo me acuerde
				//Corregi accion de forzado... que mas?
				$solicitud				= $rw["numero_solicitud"];
				$socio					= $rw["numero_socio"];
				$fechaMinistracion		= $rw["fecha_ministracion"];
				$montoMinistrado		= $rw["monto_autorizado"];
				//Datos a conciliar
				$fechaUltimoMvto		= $rw["fecha_ultimo_mvto"];
				$saldoActual			= $rw["saldo_actual"];
				$FechaInicial			= $fechaMinistracion;
				
				if ( $ForzarEnPeriodo == true ){
					$pagos				= ( isset( $arrPagosM[$solicitud]) ) ? $arrPagosM[$solicitud] : 0;
					$saldo				= $montoMinistrado - $pagos;
					$fechaUltimoMvto	= ( isset( $arrPagosF[$solicitud]) ) ? $arrPagosF[$solicitud] : $fechaUltimoMvto;
					//notas_auditoria= '', fecha_revision='2011-10-23',
					//si la fecha de ministracion es mayor a la final, invalidad fecha y saldo a cero
					$saldo_conciliado	= ( $fechaMinistracion > $fecha_final ) ? 0 : $saldo;
					$fecha_conciliada	= ( $fechaMinistracion > $fecha_final ) ? $fechaMinistracion : $fechaUltimoMvto;
					$sqlUC				= "UPDATE creditos_solicitud SET saldo_conciliado=$saldo_conciliado, fecha_conciliada='$fecha_conciliada'
									    	WHERE numero_solicitud=$solicitud ";
					$msg				.= "$socio\t$solicitud\tActualizar Saldo a $saldo_conciliado, Fecha a $fecha_conciliada, Ministrado: $montoMinistrado, Monto Pagado: $pagos\r\n";
					my_query($sqlUC);
				}
				$FechaFinal				= ($saldoActual <= TOLERANCIA_SALDOS ) ? $fechaUltimoMvto : fechasys();
				
				$dias					= $xF->setRestarFechas($FechaFinal, $FechaInicial);
				$monto					= 1; //$saldoActual;
				//$msg					.= "Dias $dias \r\n";
				for ($i = 0; $i <= $dias; $i++){
					$fecha				= $xF->setSumarDias($i, $FechaInicial);
					$fin_de_mes			= $xF->getDiaFinal($fecha);
					if ( $fecha == $fin_de_mes ){
						//guardar solo los datos del fin de mes
						if ( ($fecha >= $fecha_inicial) AND ($fecha <= $fecha_final) ){
							$observaciones	= "Cierre a $fecha, Fin de mes $fin_de_mes";
							$operacion		= $CRecibo->setNuevoMvto($fecha, $monto, OPERACION_CLAVE_FIN_DE_MES, 1, $observaciones, 0, TM_CARGO, $socio, $solicitud);
							$msg			.= "$i\t$socio\t$solicitud\t$fecha\t$fin_de_mes\t$saldoActual\t$operacion\r\n";
						} else {
							$msg			.= "$i\t$socio\t$solicitud\t$fecha\t$fin_de_mes\t$saldoActual\tNO_REG\r\n";
						}
					}
				}
			}
			$CRecibo->setFinalizarRecibo();
		return $msg;
	}
	function setAcumularIntereses($Forzar = false, $credito = false){
		$msg		= "";
		$ql			= new MQL();
		if ( $Forzar == true ){
			$msg	.= "======\t\tActualizacion FORZADA\r\n";
		}
		$ByWCredito	= ($credito == false) ? "" : " WHERE numero_solicitud=$credito ";
		
		$sqlACero	= "UPDATE creditos_solicitud
					SET sdo_int_ant=0, interes_normal_devengado=0, interes_normal_pagado=0,
					interes_moratorio_devengado=0, interes_moratorio_pagado=0 $ByWCredito";
		my_query($sqlACero);

		$xB		= new cBases(0);
		$xB->setClave(2200);
		$aMorDev	= $xB->getBaseMvtosInArray();
		$xB->setClave(2210);
		$aMorPag	= $xB->getBaseMvtosInArray();
		$xB->setClave(2100);
		$aNorDev	= $xB->getBaseMvtosInArray();
		$xB->setClave(2110);
		$aNorPag	= $xB->getBaseMvtosInArray();

		$conteo		= 1;

		$sql	= "SELECT * FROM creditos_solicitud $ByWCredito ORDER BY saldo_actual, fecha_ministracion";
		$rs		=  $ql->getDataRecord($sql);//getRecordset($sql);
        foreach ($rs as $rw ){
					$socio		= $rw["numero_socio"];
					$solicitud	= $rw["numero_solicitud"];
					$saldo		= $rw["saldo_actual"];
					
					$IntMorDev	= ( isset($aMorDev["$socio@$solicitud"]) ) ? round( setNoMenorQueCero($aMorDev["$socio@$solicitud"]), 2) : 0 ;
					$IntMorPag	= ( isset($aMorPag["$socio@$solicitud"]) ) ? round( setNoMenorQueCero($aMorPag["$socio@$solicitud"]), 2) : 0;
					
					$IntNorDev	= ( isset($aNorDev["$socio@$solicitud"]) ) ? round( setNoMenorQueCero($aNorDev["$socio@$solicitud"]), 2) : 0;
					$IntNorPag	= ( isset($aNorPag["$socio@$solicitud"]) ) ? round( setNoMenorQueCero($aNorPag["$socio@$solicitud"]), 2) : 0;
					
					$sdoNorm	= round( ($IntNorDev - $IntNorPag) , 2);
					$sdoMor		= round( ($IntMorDev - $IntMorPag) , 2);
						if ( ( $saldo <= TOLERANCIA_SALDOS) AND ($Forzar == false) ) {
							$msg	.= "$conteo\t$socio\t$solicitud\tNOTA_INTERES\tCredito Pagado, Saldo $saldo, Intereses a Cero\r\n ";
							$IntMorDev	= 0;
							$IntMorPag	= 0;
							$IntNorDev	= 0;
							$IntNorPag	= 0;
							$sdoNorm	= 0;
							$sdoMor		= 0;							
						}

					$sql		= "UPDATE creditos_solicitud
								SET sdo_int_ant=0, interes_normal_devengado=$IntNorDev, interes_normal_pagado=$IntNorPag,
								interes_moratorio_devengado=$IntMorDev, interes_moratorio_pagado=$IntMorPag
								WHERE numero_solicitud=$solicitud AND numero_socio=$socio ";
					my_query($sql);
					$msg	.= "$conteo\t$socio\t$solicitud\tINT_NORMAL_C\tDevengado:\t$IntNorDev\tPagado:\t$IntNorPag\tSaldo\t$sdoNorm\r\n ";
			if ( $IntMorPag != 0 AND $IntMorDev != 0 ){
				$msg	.= "$conteo\t$socio\t$solicitud\tINT_MORATORIO\tDevengado:\t$IntMorDev\tPagado:\t$IntMorPag\tSaldo\t$sdoMor\r \r\n ";
			}
			$conteo++;
		}
		return $msg;	
	}
	function setCleanCreditosConAhorro(){
		$msg	= "============== DEPURANDO CREDITOS CON CUENTAS DE CAPTACION GLOBALES ========\r\n";
	$sql_sentencia =  "SELECT
						`creditos_solicitud`.*,
						`creditos_tipoconvenio`.*
					FROM
						`creditos_solicitud` `creditos_solicitud`
							INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio`
								ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
									`idcreditos_tipoconvenio`
					WHERE
						(`creditos_tipoconvenio`.`tasa_ahorro` >0)
						AND
						(`creditos_solicitud`.`estatus_actual` !=50)
						AND
						(
							(`creditos_solicitud`.`contrato_corriente_relacionado` =" . CTA_GLOBAL_CORRIENTE . ")
							OR
							(`creditos_solicitud`.`contrato_corriente_relacionado` =0 )
							OR
							(`creditos_solicitud`.`contrato_corriente_relacionado` ='' )
							OR
							(SELECT count(numero_cuenta) FROM captacion_cuentas
								WHERE numero_cuenta=creditos_solicitud.contrato_corriente_relacionado) = 0
						)";
						//echo $sql_sentencia;
	$rsUCTA		= mysql_query($sql_sentencia, cnnGeneral() );
		while ( $rw = mysql_fetch_array($rsUCTA) ) {
			$socio				= $rw["numero_socio"];
			$credito			= $rw["numero_solicitud"];
			$cuenta_relacionada = $rw["contrato_corriente_relacionado"];
			$grupo         = $rw["grupo_asociado"];
				/**
	* Busca la Cuenta de Captacion si esta Existe en la Solicitud
	*/
			$sql_cuentas = "SELECT captacion_cuentas.numero_cuenta,
								captacion_cuentas.numero_socio, captacion_cuentas.numero_solicitud,
								fecha_apertura FROM captacion_cuentas
						WHERE
						numero_socio=". $socio . "
						AND tipo_cuenta=10
						ORDER BY fecha_apertura DESC LIMIT 0,1 ";

		$datos_de_la_cuenta = obten_filas($sql_cuentas);

			if ( !$datos_de_la_cuenta["numero_cuenta"] ) {
				/**
				 * Si no Hay datos de la Cuenta, se abrir� una Automaticamente
				 */
				//Cuenta al Tipo de tipo  + socio + 01
				$cuenta_nueva	= "10" . $socio + "01";
					//verificar la disponibilidad de la cuenta
					$sqlExistCta 	= "SELECT COUNT(numero_cuenta) AS
										'es_aqui' FROM captacion_cuentas
										WHERE numero_cuenta = $cuenta_nueva";
					$SiExist		= mifila($sqlExistCta, "es_aqui");
					if($SiExist > 0){
						$cuenta_nueva = "10" . $socio + "02";
					}
					//Verifica por segunda vez la cuenta_nueva
					$def_origen 	= 2;
					$subproducto	= 1;

				$msg	.= date("Y-m-d H:i:s") . "\t$socio\t$credito\tLa Cuenta No Existe se creara la Cuenta NUM $cuenta_nueva\r\n";

				$SQLNCta = "INSERT INTO captacion_cuentas
									(numero_cuenta, numero_socio, numero_grupo, numero_solicitud, tipo_cuenta,
									fecha_apertura, fecha_afectacion, fecha_baja,
									estatus_cuenta, saldo_cuenta, eacp, idusuario, inversion_fecha_vcto,
									inversion_periodo, tasa_otorgada, dias_invertidos, observacion_cuenta,
									origen_cuenta, tipo_titulo, tipo_subproducto, nombre_mancomunado1, nombre_mancomunado2,
									minimo_mancomunantes, saldo_conciliado, fecha_conciliada, sucursal, ultimo_sdpm)
    								VALUES
    								($cuenta_nueva, $socio, $grupo, $credito, 10,
    								'$fecha_de_chequeo', '$fecha_de_chequeo', '2029-12-31',
    								10, 0, '" . EACP_CLAVE . "', $iduser, '$fecha_de_chequeo',
    								0, 0, 0, 'ALTA_AUTOMATICA_POR_CREDITO',
    								$def_origen, 99, $subproducto, '', '',
    								0, 0, '$fecha_de_chequeo', '" . getSucursal() . "', 0)";
    				$xICta	= my_query($SQLNCta);
    				$cuenta_relacionada	= $cuenta_nueva;
    					if ($xICta["stat"] == false ){
    						$msg	.= date("Y-m-d H:i:s") . "\tERROR AL EFECTUAR EL ALTA: EL SISTEMA DEVOLVIO . " . $xICta["error"] . "\r\n";
    					}
    			} else {
    				$cuenta_relacionada	= $datos_de_la_cuenta["numero_cuenta"];
    			}
				//Actualiza el Contrato corriente relacionado

				$sql_update_credito = "UPDATE creditos_solicitud
										SET contrato_corriente_relacionado=$cuenta_relacionada
										WHERE numero_solicitud=$credito";
				$x = my_query($sql_update_credito);
					if( $x["stat"] == false ){
							$msg	.= date("Y-m-d H:i:s") . "\tERROR : EL SISTEMA DEVOLVIO . " . $x["error"] . "\r\n";
					} else {
							$msg	.= date("Y-m-d H:i:s") . "\t$socio\t$credito\tSe Actualizo la Cuenta Relacionada a la NUM $cuenta_relacionada (" . $x["info"] . ")\r\n";
					}

		}
	return $msg;	
	}

	function setActualizarSaldosConciliado( $fecha_final, $force = false ){
		$msg				= "";
		$arrPagosF			= array();
		$arrPagosM			= array();
			    
		//==============================================================================
			$sqlP			= "SELECT
								`operaciones_mvtos`.`socio_afectado`       AS `socio`,
								`operaciones_mvtos`.`docto_afectado`       AS `documento`,
								`operaciones_mvtos`.`tipo_operacion`       AS `operacion`,
								MAX(`operaciones_mvtos`.`fecha_operacion`) AS `fecha`,
								SUM(`operaciones_mvtos`.`afectacion_real`) AS `monto` 
							FROM
								`operaciones_mvtos` `operaciones_mvtos` 
							WHERE
								(`operaciones_mvtos`.`tipo_operacion` = 120) 
								AND
								(`operaciones_mvtos`.`fecha_operacion` <='$fecha_final')
							
							GROUP BY
								`operaciones_mvtos`.`docto_afectado`,
								`operaciones_mvtos`.`tipo_operacion` ";
			$rsP			= mysql_query($sqlP, cnnGeneral());
			$msg			.= "============ ULTIMOS MVTOS HASTA LA FECHA $fecha_final \r\n";
				
			while ( $rwP	= mysql_fetch_array($rsP) ){
				$mSocio			= $rwP["socio"];
				$mCredito		= $rwP["documento"];
				$mFecha			= $rwP["fecha"];
				$mMonto			= $rwP["monto"];
				$arrPagosF[ $mCredito ]	= $mFecha;
				$arrPagosM[ $mCredito ]	= $mMonto;
				$msg			.= "$mSocio\t$mCredito\tFecha $mFecha\t Monto: $mMonto\r\n";
			}
		//==============================================================================
	
			$sql = "SELECT SQL_CACHE
					`creditos_solicitud`.*,
					`creditos_tipoconvenio`.*,
					`creditos_periocidadpagos`.*,
					`creditos_estatus`.*,
					`creditos_solicitud`.`tasa_interes` AS `tasa_ordinaria_anual`,
					`creditos_solicitud`.`tipo_autorizacion` AS `tipo_de_autorizacion`,
					`creditos_solicitud`.`tasa_ahorro` AS `tasa_de_ahorro`
				FROM
					`creditos_tipoconvenio` `creditos_tipoconvenio`
						INNER JOIN `creditos_solicitud` `creditos_solicitud`
						ON `creditos_tipoconvenio`.`idcreditos_tipoconvenio`
						= `creditos_solicitud`.`tipo_convenio`
							INNER JOIN `creditos_periocidadpagos`
							`creditos_periocidadpagos`
							ON `creditos_periocidadpagos`.
							`idcreditos_periocidadpagos` =
							`creditos_solicitud`.`periocidad_de_pago`
								INNER JOIN `creditos_estatus`
								`creditos_estatus`
								ON `creditos_estatus`.`idcreditos_estatus` =
								`creditos_solicitud`.`estatus_actual`
				WHERE
								(`creditos_solicitud`.`estatus_actual` !=50)
							
								 ";
			$rsMx				= mysql_query($sql, cnnGeneral() );
			//$msg				.= $sql ."\r\n" . $sqlDM  . "\r\n";
			$msg				.= "============ GENERANDO MOVIMIENTOS 999: Saldos al Fin del mes \r\n";	
			$xF					= new cFecha();


				
			while ( $rw	= mysql_fetch_array($rsMx) ){
				//corregir cuando yo me acuerde
				//Corregi accion de forzado... que mas?
				$solicitud				= $rw["numero_solicitud"];
				$socio					= $rw["numero_socio"];
				$fechaMinistracion		= $rw["fecha_ministracion"];
				$montoMinistrado		= $rw["monto_autorizado"];
				//Datos a conciliar
				$fechaUltimoMvto		= $rw["fecha_ultimo_mvto"];
				$saldoActual			= $rw["saldo_actual"];
				$FechaInicial			= $fechaMinistracion;
				

					$pagos				= ( isset( $arrPagosM[$solicitud]) ) ? $arrPagosM[$solicitud] : 0;
					$saldo				= $montoMinistrado - $pagos;
					$fechaUltimoMvto	= ( isset( $arrPagosF[$solicitud]) ) ? $arrPagosF[$solicitud] : $fechaUltimoMvto;
					//notas_auditoria= '', fecha_revision='2011-10-23',
					//si la fecha de ministracion es mayor a la final, invalidad fecha y saldo a cero
					$saldo_conciliado	= ( $fechaMinistracion > $fecha_final ) ? 0 : $saldo;
					$fecha_conciliada	= ( $fechaMinistracion > $fecha_final ) ? $fechaMinistracion : $fechaUltimoMvto;
					$sqlUC				= "UPDATE creditos_solicitud SET saldo_conciliado=$saldo_conciliado, fecha_conciliada='$fecha_conciliada'
									    	WHERE numero_solicitud=$solicitud ";
					$msg				.= "$i\t$socio\t$solicitud\tActualizar Saldo a $saldo_conciliado, Fecha a $fecha_conciliada, Ministrado: $montoMinistrado, Monto Pagado: $pagos\r\n";
					my_query($sqlUC);
				}

		return $msg;
	}

	function setRegenerarCreditosMinistraciones(  $incluirSinSaldo ){
    //Reconstruye el Movimiento de Ministracion de Creditos
	//
		$wGeneral				= ( $incluirSinSaldo == "SI" ) ? "" : "WHERE saldo_actual > 0 AND fecha_ministracion > '2006-12-01'";
	//
		$msg	    = "============================\t\tGENERANDO MINISTRACIONES DE CREDITOS \r\n ";
				//Obtiene el Listado de Movimientos 110
		$SQLMinistraciones = " SELECT
								CONCAT(`operaciones_mvtos`.`docto_afectado`, '-',
								`operaciones_mvtos`.`tipo_operacion`) AS 'id',
								`operaciones_mvtos`.`afectacion_real`  AS 'monto'
							FROM
								`operaciones_mvtos` `operaciones_mvtos`
							WHERE
								(`operaciones_mvtos`.`tipo_operacion` =110) ";
	
		$arrMin		= array();
		$rsM		= mysql_query($SQLMinistraciones, cnnGeneral() );
			while( $rx = mysql_fetch_array($rsM) ) {
				$arrMin[ $rx["id"] ]	= $rx["monto"];
			}
					//==============================================================================
		$fecha			= fechasys();
		$cheque 		= DEFAULT_CHEQUE;
		$recibo_fiscal	= "NA";
		$observaciones	= "GENERADO_EN_LA_UTILERIA_874";

				//==============================================================================
				$CRecibo 	= new cReciboDeOperacion(1, true);
				//Set a Mvto Contable
				//$CRecibo->setGenerarPoliza();
				//$CRecibo->setGenerarTesoreria();

				$recibo 	= $CRecibo->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO,
										$fecha, 1, 1,
										$observaciones, $cheque, "ninguno", $recibo_fiscal, DEFAULT_GRUPO );
				$CRecibo->setNumeroDeRecibo($recibo);
				//Genera los Creditos
						$sql = "SELECT
												`creditos_solicitud`.*,
												`creditos_tipoconvenio`.*,
												`creditos_periocidadpagos`.*,
												`creditos_estatus`.*,
												`creditos_solicitud`.`tasa_interes` AS `tasa_ordinaria_anual`,
												`creditos_solicitud`.`tipo_autorizacion` AS `tipo_de_autorizacion`,
							                    `creditos_solicitud`.`tasa_ahorro` AS `tasa_de_ahorro`
											FROM
								`creditos_tipoconvenio` `creditos_tipoconvenio`
									INNER JOIN `creditos_solicitud` `creditos_solicitud`
									ON `creditos_tipoconvenio`.`idcreditos_tipoconvenio`
									= `creditos_solicitud`.`tipo_convenio`
										INNER JOIN `creditos_periocidadpagos`
										`creditos_periocidadpagos`
										ON `creditos_periocidadpagos`.
										`idcreditos_periocidadpagos` =
										`creditos_solicitud`.`periocidad_de_pago`
											INNER JOIN `creditos_estatus`
											`creditos_estatus`
											ON `creditos_estatus`.`idcreditos_estatus` =
											`creditos_solicitud`.`estatus_actual`
											$wGeneral
								  ";
								$rs = mysql_query($sql, cnnGeneral() );
								while ( $rw  = mysql_fetch_array($rs) ){
									$socio				= $rw["numero_socio"];
									$credito			= $rw["numero_solicitud"];
									$monto				= $rw["monto_autorizado"];
									$fecha_ministracion	= $rw["fecha_ministracion"];

									$CCredito	= new cCredito($credito, $socio);
									$CCredito->init($rw);
									$CCredito->setForceMinistracion();

									if ( !isset( $arrMin["$credito-110"] ) ) {
										$idreciboMin		= $CCredito->setMinistrar($recibo_fiscal, DEFAULT_CHEQUE, $monto, DEFAULT_CUENTA_BANCARIA,
													  DEFAULT_CHEQUE, DEFAULT_CUENTA_BANCARIA, $observaciones, $fecha_ministracion, $recibo );
									}
									$msg	.= $CCredito->getMessages("txt");
								}
								$CRecibo->setFinalizarRecibo(true);
								$msg	.= $CRecibo->getMessages("txt");
			return $msg;		
	}
	function setRegenerarCreditosAVencidos($fecha = false){
		$msg		= "";		
		//Obtiene el Listado de Movimientos 111
					$SQLMinistraciones = " SELECT
							CONCAT(`operaciones_mvtos`.`docto_afectado`, '-',
							`operaciones_mvtos`.`tipo_operacion`) AS 'id',
							`operaciones_mvtos`.`afectacion_real`  AS 'monto'
						FROM
							`operaciones_mvtos` `operaciones_mvtos`
						WHERE
							(`operaciones_mvtos`.`tipo_operacion` =111) ";

					$arrMin		= array();
					$rsM		= mysql_query($SQLMinistraciones, cnnGeneral() );
						while( $rx = mysql_fetch_array($rsM) ) {
							$arrMin[ $rx["id"] ]	= $rx["monto"];
						}
				//==============================================================================
				$fecha			= fechasys();
				$cheque 		= DEFAULT_CHEQUE;
				$recibo_fiscal	= "NA";
				$observaciones	= "GENERADO_EN_LA_UTILERIA_875";

				//==============================================================================
				$CRecibo 	= new cReciboDeOperacion(10, true);
				$recibo 	= $CRecibo->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO,
										$fecha, 1, 10,
										$observaciones, $cheque, "ninguno", $recibo_fiscal, DEFAULT_GRUPO );
				$CRecibo->setNumeroDeRecibo($recibo);
				//Genera los Creditos
						$sql = "SELECT
												`creditos_solicitud`.*,
												`creditos_tipoconvenio`.*,
												`creditos_periocidadpagos`.*,
												`creditos_estatus`.*,
												`creditos_solicitud`.`tasa_interes` AS `tasa_ordinaria_anual`,
												`creditos_solicitud`.`tipo_autorizacion` AS `tipo_de_autorizacion`,
							                    `creditos_solicitud`.`tasa_ahorro` AS `tasa_de_ahorro`
											FROM
								`creditos_tipoconvenio` `creditos_tipoconvenio`
									INNER JOIN `creditos_solicitud` `creditos_solicitud`
									ON `creditos_tipoconvenio`.`idcreditos_tipoconvenio`
									= `creditos_solicitud`.`tipo_convenio`
										INNER JOIN `creditos_periocidadpagos`
										`creditos_periocidadpagos`
										ON `creditos_periocidadpagos`.
										`idcreditos_periocidadpagos` =
										`creditos_solicitud`.`periocidad_de_pago`
											INNER JOIN `creditos_estatus`
											`creditos_estatus`
											ON `creditos_estatus`.`idcreditos_estatus` =
											`creditos_solicitud`.`estatus_actual`
											WHERE

								  fecha_vencimiento_dinamico <= '$fecha'
								 AND
								saldo_actual > 0.99";
								$rs = mysql_query($sql, cnnGeneral() );
								while ( $rw  = mysql_fetch_array($rs) ){
									$socio			= $rw["numero_socio"];
									$credito		= $rw["numero_solicitud"];
									$monto			= $rw["monto_autorizado"];
									$parcialidad	= $rw["ultimo_periodo_afectado"];
									$fecha_vencido	= $rw["fecha_vencimiento_dinamico"];
									$CCredito		= new cCredito($credito, $socio);
									$CCredito->init($rw);
									if ( !isset( $arrMin["$credito-111"] ) ){
										$msg	.= $CCredito->setEnviarVencido($fecha_vencido, $parcialidad, $recibo);
									}
									$msg	.= $CCredito->getMessages("txt");
								}
								$CRecibo->setFinalizarRecibo(true);
								$msg	.= $CRecibo->getMessages("txt");
								return $msg;		
	}
	function setRegenerarCreditosAMora($fecha){
		$msg		= "";
				//Obtiene el Listado de Movimientos 115
					$SQLMinistraciones = " SELECT
							CONCAT(`operaciones_mvtos`.`docto_afectado`, '-',
							`operaciones_mvtos`.`tipo_operacion`) AS 'id',
							`operaciones_mvtos`.`afectacion_real`  AS 'monto'
						FROM
							`operaciones_mvtos` `operaciones_mvtos`
						WHERE
							(`operaciones_mvtos`.`tipo_operacion` =115) ";

					$arrMin		= array();
					$rsM		= mysql_query($SQLMinistraciones, cnnGeneral() );
						while( $rx = mysql_fetch_array($rsM) ) {
							$arrMin[ $rx["id"] ]	= $rx["monto"];
						}
				//==============================================================================
				$fecha			= fechasys();
				$cheque 		= DEFAULT_CHEQUE;
				$recibo_fiscal	= "NA";
				$observaciones	= "GENERADO_EN_LA_UTILERIA_875";

				//==============================================================================
				$CRecibo 	= new cReciboDeOperacion(10, true);
				$recibo 	= $CRecibo->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO,
										$fecha, 1, 10,
										$observaciones, $cheque, "ninguno", $recibo_fiscal, DEFAULT_GRUPO );
				$CRecibo->setNumeroDeRecibo($recibo);
				//Genera los Creditos
						$sql = "SELECT
												`creditos_solicitud`.*,
												`creditos_tipoconvenio`.*,
												`creditos_periocidadpagos`.*,
												`creditos_estatus`.*,
												`creditos_solicitud`.`tasa_interes` AS `tasa_ordinaria_anual`,
												`creditos_solicitud`.`tipo_autorizacion` AS `tipo_de_autorizacion`,
							                    `creditos_solicitud`.`tasa_ahorro` AS `tasa_de_ahorro`
											FROM
								`creditos_tipoconvenio` `creditos_tipoconvenio`
									INNER JOIN `creditos_solicitud` `creditos_solicitud`
									ON `creditos_tipoconvenio`.`idcreditos_tipoconvenio`
									= `creditos_solicitud`.`tipo_convenio`
										INNER JOIN `creditos_periocidadpagos`
										`creditos_periocidadpagos`
										ON `creditos_periocidadpagos`.
										`idcreditos_periocidadpagos` =
										`creditos_solicitud`.`periocidad_de_pago`
											INNER JOIN `creditos_estatus`
											`creditos_estatus`
											ON `creditos_estatus`.`idcreditos_estatus` =
											`creditos_solicitud`.`estatus_actual`
											WHERE

								  fecha_mora <= '$fecha'
								AND
								saldo_actual > 0.99";
								$rs = mysql_query($sql, cnnGeneral() );
								while ( $rw  = mysql_fetch_array($rs) ){
									$socio			= $rw["numero_socio"];
									$credito		= $rw["numero_solicitud"];
									$monto			= $rw["monto_autorizado"];
									$parcialidad	= $rw["ultimo_periodo_afectado"];
									$fecha_moroso	= $rw["fecha_mora"];

									$CCredito		= new cCredito($credito, $socio);
									$CCredito->init($rw);
									if ( !isset( $arrMin["$credito-115"] ) ){
										$msg	.= $CCredito->setEnviarMoroso($fecha_moroso, $parcialidad, $recibo);
									}
									$msg	.= $CCredito->getMessages("txt");
								}
								$CRecibo->setFinalizarRecibo(true);
								$msg	.= $CRecibo->getMessages("txt");
							return $msg;
	}
	function setEliminarCreditosNegativos(){
		//Crear un nuevo Recibo de Ajuste
		$cRec		= new cReciboDeOperacion(10);
		$xRec		= $cRec->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO, fechasys(), 1, 10, "RECIBO_DE_AJUSTES_DE_CREDITOS");
		$msg		= "============\tRECIBO\tEl Recibo de Operacion es $xRec\r\n";
		$cRec->setNumeroDeRecibo($xRec, true);
                /*Esta funcion servira para eliminar saldos negativos de Créditos */
        $sql   		= "SELECT
					`creditos_solicitud`.*,
					`creditos_tipoconvenio`.*,
					`creditos_periocidadpagos`.*,
					`creditos_estatus`.*,
					`creditos_solicitud`.`tasa_interes` AS `tasa_ordinaria_anual`,
					`creditos_solicitud`.`tipo_autorizacion` AS `tipo_de_autorizacion`,
                    `creditos_solicitud`.`tasa_ahorro` AS `tasa_de_ahorro`
								FROM
					`creditos_tipoconvenio` `creditos_tipoconvenio`
						INNER JOIN `creditos_solicitud` `creditos_solicitud`
						ON `creditos_tipoconvenio`.`idcreditos_tipoconvenio`
						= `creditos_solicitud`.`tipo_convenio`
							INNER JOIN `creditos_periocidadpagos`
							`creditos_periocidadpagos`
							ON `creditos_periocidadpagos`.
							`idcreditos_periocidadpagos` =
							`creditos_solicitud`.`periocidad_de_pago`
								INNER JOIN `creditos_estatus`
								`creditos_estatus`
								ON `creditos_estatus`.`idcreditos_estatus` =
								`creditos_solicitud`.`estatus_actual`
								WHERE saldo_actual < " . TOLERANCIA_SALDOS . " ORDER BY saldo_actual ";
						$rs			= getRecordset( $sql );
						while ($rw=mysql_fetch_array($rs)) {
								$socio		 	= $rw["numero_socio"];
								$credito	 	= $rw["numero_solicitud"];
								$saldo_actual	= $rw["saldo_actual"];
								//Se inicializa una nueva instancia de crédito
								$cCredito 		= new cCredito($credito, $socio);
								//y se neutralizara con su valor absoluto.
								$cCredito->init($rw);
								$cCredito->setReciboDeOperacion($xRec);
								//Generar un abono a Capital
								$cCredito->setAbonoCapital($saldo_actual);
								$msg	.= "$socio\t$credito\tEliminando el saldo de $saldo_actual\r\n";
								$msg	.=  $cCredito->getMessages("txt");
						}
                        $cRec->setFinalizarRecibo(true);

		return $msg;
	}
	function setEliminarInteresesDeCreditosPagados(){
		$msg		= "";
				//==============================================================================
				$fecha			= fechasys();
				$cheque 		= DEFAULT_CHEQUE;
				$recibo_fiscal	= "NA";
				$observaciones	= "GENERADO_EN_LA_UTILERIA_883";

				//==============================================================================
				$CRecibo 	= new cReciboDeOperacion(1, false);
				//Set a Mvto Contable
				//$CRecibo->setGenerarPoliza();
				//$CRecibo->setGenerarTesoreria();

				$recibo 	= $CRecibo->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO,
										$fecha, 1, 1,
										$observaciones, $cheque, "ninguno", $recibo_fiscal, DEFAULT_GRUPO );
				$CRecibo->setNumeroDeRecibo($recibo);

				//
				$sql	= "SELECT
				*
			FROM
				`creditos_solicitud` `creditos_solicitud`
			WHERE
				(`creditos_solicitud`.`saldo_actual` <= 0) AND
				(
					(`creditos_solicitud`.`interes_moratorio_devengado` != 0)
			OR
					(`creditos_solicitud`.`interes_normal_devengado` != 0)
				)";
			$rs	= mysql_query($sql, cnnGeneral() );
			while( $rw = mysql_fetch_array($rs) ) {
						$socio		= $rw["numero_socio"];
						$solicitud	= $rw["numero_solicitud"];
						$OpNormal	= 420; //420;
						$OpMora		= 421; //431;
						$MontoM		= 0;
						$MontoN		= 0;
						//terminado: oct/2011
						$interesDN	= $rw["interes_normal_devengado"];
						$interesDM	= $rw["interes_moratorio_devengado"];
						
						$interesPN	= $rw["interes_normal_pagado"];
						$interesPM	= $rw["interes_moratorio_pagado"];
						if ( $interesDN > $interesPN ){
							$OpNormal	= 140;
							$MontoN		= $interesDN - $interesPN;
						} else {
							$MontoN		= $interesPN - $interesDN;
						}
						if ( $interesDM > $interesPM ){
							$OpNormal	= 141;
							$MontoM		= $interesDM - $interesPM;
						} else {
							$MontoM		= $interesPM - $interesDM;
						}
												
						if ($MontoN > 0){
							$CRecibo->setNuevoMvto($fecha, $MontoN, $OpNormal, 1, " Ajuste por $MontoN de $interesDN | $interesPN ", 1, TM_CARGO, $socio, $solicitud);
							$msg	.= "$socio\t$solicitud\tINTERES_NORMAL_DEV\tAjuste por $MontoN de $interesDN | $interesPN \r\n";
						} else {
							
						}
						if ($MontoM > 0){
							$CRecibo->setNuevoMvto($fecha, $MontoN, $OpMora, 1, "Ajuste por $MontoM de $interesDM | $interesPM", 1, TM_CARGO, $socio, $solicitud);
							$msg	.= "$socio\t$solicitud\tINTERES_MOR_DEV\tAjuste por $MontoM $interesDM | $interesPM \r\n";
						} else {
							
						}
			}
				$msg	.= $CRecibo->getMessages("txt");
				
			$xCUtils			= new cUtileriasParaCreditos();
			$msg				.= $xCUtils->setAcumularIntereses(true);
			return $msg;		
	}

	function setCambiarPersonaDeCredito($credito, $nueva_persona){
			$xCred				= new cCredito($credito);
			$xCred->init();
			
			$numero_socio 	= $xCred->getClaveDePersona();
			$numero_nuevo	= $nueva_persona;
			$var			= array();
			$xQL			= new MQL();
			$run			= false;
			$msg = "";
			$msg .= "================== MODIFICANDO UN NUMERO DE SOCIO \r\n";
			$msg .= "================== SE ACTUALIZA DEL $numero_socio AL  $numero_nuevo \r\n";
		
			//$var2["aml_alerts"]["C"]						="documento_relacionado";
			//$var2["aml_alerts"]["P"]						="persona_de_destino";
			$var["aml_risk_register"]["C"]				="documento_relacionado";
			$var["aml_risk_register"]["P"]				="persona_relacionada";
			$var["bancos_operaciones"]["C"]				="numero_de_documento";
			$var["bancos_operaciones"]["P"]				="numero_de_socio";
			$var["captacion_cuentas"]["C"]				="numero_solicitud";
			$var["captacion_cuentas"]["P"]				="numero_socio";
			$var["contable_polizas_proforma"]["C"]		="documento";
			$var["contable_polizas_proforma"]["P"]		="socio";
			$var["creditos_flujoefvo"]["C"]				="solicitud_flujo";
			$var["creditos_flujoefvo"]["P"]				="socio_flujo";
			$var["creditos_garantias"]["C"]				="solicitud_garantia";
			$var["creditos_garantias"]["P"]				="socio_garantia";
			$var["creditos_parametros_negociados"]["C"]	="numero_de_credito";
			$var["creditos_parametros_negociados"]["P"]	="numero_de_socio";
			$var["creditos_sdpm_historico"]["C"]		="numero_de_credito";
			$var["creditos_sdpm_historico"]["P"]		="numero_de_socio";
			$var["creditos_solicitud"]["C"]				="numero_solicitud";
			$var["creditos_solicitud"]["P"]				="numero_socio";
			
			$var["operaciones_mvtos"]["C"]				="docto_afectado";
			$var["operaciones_mvtos"]["P"]				="socio_afectado";
			
			$var["operaciones_recibos"]["C"]			="docto_afectado";
			$var["operaciones_recibos"]["P"]			="numero_socio";
			$var["seguimiento_compromisos"]["C"]		="credito_comprometido";
			$var["seguimiento_compromisos"]["P"]		="socio_comprometido";
			$var["seguimiento_llamadas"]["C"]			="numero_solicitud";
			$var["seguimiento_llamadas"]["P"]			="numero_socio";
			$var["seguimiento_notificaciones"]["C"]		="numero_solicitud";
			$var["seguimiento_notificaciones"]["P"]		="socio_notificado";
			$var["socios_memo"]["C"]					="numero_solicitud";
			$var["socios_memo"]["P"]					="numero_socio";
			$var["socios_relaciones"]["C"]				="credito_relacionado";
			$var["socios_relaciones"]["P"]				="socio_relacionado";
			$var["tesoreria_cajas_movimientos"]["C"]	="documento";
			$var["tesoreria_cajas_movimientos"]["P"]	="persona";
			$var["usuarios_web_notas"]["C"]				="documento";
			$var["usuarios_web_notas"]["P"]				="socio";
			$var["aml_alerts"]["C"]					="documento_relacionado";
			$var["aml_alerts"]["P"]					="persona_de_origen";			
			
			//? Funciona bien?
			//$var2["creditos_solicitud"]["C"]			="numero_solicitud";
			//$var2["creditos_solicitud"]["P"]			="persona_asociada";
			//$var2["socios_relaciones"]["C"]			="credito_relacionado";
			//$var2["socios_relaciones"]["P"]			="numero_socio";

			//$var2["operaciones_recibos"]["C"]			="docto_afectado";
			//$var2["operaciones_recibos"]["P"]			="persona_asociada";
				
			
			foreach ($var as $tabla => $items){
				if(isset($items["P"]) AND isset($items["C"]) ){
					$campo	= $items["P"];
					$filtro	= $items["C"];
					$msg	.= "WARN\tCambiar Registros de la Tabla { $tabla }, Campo [ $campo ] de #$numero_socio - #$numero_nuevo con filtro ($filtro).\r\n";
					//$msg	.= "UPDATE  $tabla SET $campo = $numero_nuevo WHERE $filtro = $credito AND $campo = $numero_socio\r\n";
					$xQL->setRawQuery("UPDATE  $tabla SET $campo = $numero_nuevo WHERE $filtro = $credito AND $campo = $numero_socio");
					$run	= true;
				}
			}
			if($run = true){
				$xSoc1	= new cSocio($numero_nuevo); $xSoc1->init();
				$xSoc2	= new cSocio($numero_socio); $xSoc2->init();
				$xSoc1->addMemo(MEMOS_TIPO_NOTA_RENOVACION, "Cambio de Propietario del Credito $credito desde la Persona $numero_socio", $credito);
				$xSoc2->addMemo(MEMOS_TIPO_NOTA_RENOVACION, "Cambio de Propietario del Credito $credito a la Persona $numero_nuevo", $credito);
			}
			$this->mMessages	.= $msg;
		return $msg;	
	}
	function setActualizarPrimerPago($default_fecha = "2014-01-01"){
		$xLi			= new cSQLListas();
		$query			= new MQL();
		$sql			= $xLi->getInicialDeCreditos() . " WHERE `creditos_solicitud`.`fecha_de_primer_pago` ='$default_fecha'";// AND `creditos_solicitud`.`periocidad_de_pago` != " . CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO . " ";
		$data			= $query->getDataRecord($sql);
		$msg			= "";
		$FechaFinal		= "";
		$ByPersona1		= "";
		$ByPersona3		= "";
		
		$rsPagos		= $query->getDataRecord( "SELECT * FROM `creditos_abonos_parciales` WHERE periodo_socio = 1 ");// /*WHERE	(`creditos_abonos_parciales`.`fecha_de_pago` <='$FechaFinal') $ByPersona1 */");
		$DPagos			= array();
		foreach ($rsPagos as $dpags ){
			$credito	= $dpags["docto_afectado"];
			$DPagos[$credito][]	= $dpags;
		}
		
		$rsCal			= $query->getDataRecord("SELECT * FROM `letras` WHERE periodo_socio = 1 ");// /*WHERE	(`fecha_de_pago` <='$FechaFinal') $ByPersona3 */");
		$DCal			= array();
		foreach ($rsCal as $dscal ){
			$credito	= $dscal["docto_afectado"];
			$DCal[$credito][]	= $dscal;
		}
				
		foreach ($data as $rows){
			$idcredito	= $rows["numero_solicitud"];
			$antes		= $rows["fecha_de_primer_pago"];
			$periocidad	= $rows["periocidad_de_pago"];
			$FPrimerAb	= false;
			$Tipo		= "";
			
			if($periocidad == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
				$xCred		= new cCredito($idcredito);
				$xCred->init($rows);
				$FPrimerAb	= $xCred->getFechaDevencimientoLegal();
			} else {
				//info pagos
				$d1			= (isset($DCal[$idcredito])) ? $DCal[$idcredito] : false;
				$d2			= (isset($DPagos[$idcredito])) ? $DPagos[$idcredito] : false;
				if($d2 == false){
					if($d1 != false){
						$Tipo			= "PLAN";
						$record			= $d2[0];
						$FPrimerAb		= $record["fecha_de_pago"];					
					}
				} else {
					$Tipo			= "PAG";
					$record			= $d2[0];
					$FPrimerAb		= $record["fecha_de_pago"];
				}
				if($FPrimerAb == false){
					$xPlanGen	= new cPlanDePagosGenerador();
					$xPlanGen->initPorCredito($idcredito, $rows);
					$FPrimerAb	= $xPlanGen->getFechaDePrimerPago();
					$Tipo			= "EST";
				}
			}
			$msg		.= "WARN\t$idcredito\t$Tipo\tLa fecha de Primer abono es $FPrimerAb de $antes\r\n";
			$query->setRawQuery("UPDATE creditos_solicitud SET `fecha_de_primer_pago`='$FPrimerAb' WHERE numero_solicitud = $idcredito ");
		}
		return $msg;
	}
}


class cSQLFiltros {
	function CreditosPorEstado($estado){
		$ByEstat	= "";
		$estado		= setNoMenorQueCero($estado);
		if($estado>0){ $ByEstat = "	AND (`creditos_solicitud`.`estatus_actual`=$estado)	"; }
		return $ByEstat;
	}
	function CreditosPorProducto($producto){
		$ByProd		= "";
		$producto	= setNoMenorQueCero($producto);
		if($producto > 0){ $ByProd	= " AND (`creditos_solicitud`.`tipo_convenio` = $producto) "; }
		return $ByProd;
	}
	function CreditosPorFrecuencia($frecuencia){
		$ByFreq		= "";
		$frecuencia	= setNoMenorQueCero($frecuencia);
		if($frecuencia > 0) { $ByFreq = " AND (	`creditos_solicitud`.`periocidad_de_pago` = $frecuencia) "; }
		return $ByFreq;
	}
	function CreditosPorSucursal($sucursal){
		$BySuc	= "";
		if($sucursal != SYS_TODAS AND trim($sucursal) != ""){
			$BySuc	= " AND (`creditos_solicitud`.`sucursal`='$sucursal') ";
		}
		return $BySuc;
	}
	function CreditosPorOficial($oficial){
		$ByOficial				= "";
		$oficial				= setNoMenorQueCero($oficial);
		if($oficial > 0){
			$ByOficial			= (CREDITO_USAR_OFICIAL_SEGUIMIENTO == true) ? " AND ( `creditos_solicitud`.`oficial_seguimiento` = $oficial) " : " AND (	`creditos_solicitud`.`oficial_credito` = $oficial) ";
		}
		return $ByOficial;	
	}
	function CreditosPorAutorizacion($autorizacion = false){
		$ByAutorizacion	= "";
		$autorizacion	= setNoMenorQueCero($autorizacion);
		if($autorizacion > 0){ $ByAutorizacion = " AND (`creditos_solicitud`.`tipo_autorizacion`=$autorizacion) "; }
		return $ByAutorizacion;
	}
	function CreditosPorSaldos($monto, $operador){
		$BySaldo = " AND (creditos_solicitud.saldo_actual $operador $monto) ";
		return $BySaldo;
	}
	function CreditosPorEmpresa($empresa){
		$empresa	= setNoMenorQueCero($empresa);
		$ByEmp		= "";
		if($empresa > 0){ $ByEmp = " AND (`creditos_solicitud`.`persona_asociada` = $empresa ) "; }
		return $ByEmp;
	}
	function CreditoPorClave($credito){
		$credito		= setNoMenorQueCero($credito);
		$ByCredito		= ($credito <= 0) ? "" : " AND (`creditos_solicitud`.`numero_solicitud`=$credito)  ";
		return $ByCredito;
	}
}

?>