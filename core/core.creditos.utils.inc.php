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
	 * @param variant $fecha			//Fecha de Operacion
	 * @param bool $AppSucursal		//si Aplica para todas las sucursales
	 * @param int $credito			Numero de credito en particular
	 * @param bool $EnCierre		Si es solo el cierre, no se ejecutan los triggers del credito
	 * @return string $msg			//Mensages del LOG
	 */
	function setEstatusDeCreditos($recibo, $fecha = false, $AppSucursal = true, $force_updates = false, $credito = false, $EnCierre = false){
			$xF					= new cFecha();
			$fecha 				= $xF->getFechaISO($fecha);
			
			$cierre_sucursal	= "";
			$credito			= setNoMenorQueCero($credito);
			$xQL				= new MQL();
			$xLi				= new cSQLListas();
			$xLog				= new cCoreLog();
			$xLog->add("==\t\t\tMODIFICACION_DE_ESTATUS_EN_CREDITOS\r\n", $xLog->DEVELOPER);
			if($force_updates == true){$xLog->add("==\tSe Actualizaran ESTADOS\r\n", $xLog->DEVELOPER);}
			$xRec					= new cReciboDeOperacion(10);
			$tolerancia_en_pesos	= TOLERANCIA_SALDOS;
			//FIX: Creditos 360 que se mueven
			$xQL->setRawQuery("UPDATE creditos_solicitud SET fecha_vencimiento = DATE_ADD(fecha_ministracion, INTERVAL dias_autorizados DAY) WHERE periocidad_de_pago=" . CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO . " AND saldo_actual >0 AND dias_autorizados >0");
			$ByCredito				= $xLi->OFiltro()->CreditoPorClave($credito);
			$sqlH					= "SELECT
				`historial_de_pagos`.* FROM
			`historial_de_pagos` `historial_de_pagos` INNER JOIN `creditos_solicitud` `creditos_solicitud` 
				ON `historial_de_pagos`.`credito` = `creditos_solicitud`.
				`numero_solicitud` WHERE (`creditos_solicitud`.`estatus_actual`!=" . CREDITO_ESTADO_CASTIGADO . ")
				AND (`creditos_solicitud`.`saldo_actual`>$tolerancia_en_pesos) 
				AND (`historial_de_pagos`.`fecha` <='$fecha')				 
				$ByCredito
			ORDER BY `creditos_solicitud`.`fecha_vencimiento` ";
			
			$rsPagos		= $xQL->getRecordset($sqlH);
			//$rsPagos		= $xQL->getDataRecord($sqlH);
			$DPagos			= array();
			//Note: Consulta que genera error. Se elimina la limitante
			while($rwpagos =$rsPagos->fetch_assoc()){
			//foreach ($rsPagos as $rwpagos){
				$id				= $rwpagos["credito"];
				$DPagos[$id][]	= $rwpagos;
			}
			//$rsPagos->free();
			$rsPagos		= null;
			
			/*INICIALIZA EL RECIBO*/
			/** @since 2010-12-27 */
			$sql 			= $xLi->getInicialDeCreditos() . " WHERE (`creditos_solicitud`.`estatus_actual`!=" . CREDITO_ESTADO_CASTIGADO . ") $ByCredito AND (`creditos_solicitud`.`saldo_actual`>$tolerancia_en_pesos) ORDER BY `creditos_solicitud`.`fecha_vencimiento` ";
			//$rs 			= $xQL->getDataRecord($sql);

			$rs 			= $xQL->getRecordset($sql);
			$xDD			= new cCreditos_solicitud();
			while($rw = $rs->fetch_assoc()){
			//foreach ($rs as $rw){
				$xDD->setData($rw);
				$idcredito	= $xDD->numero_solicitud()->v();
				$xCred		= new cCredito($idcredito);
				$xCred->init($rw);
				if(isset($DPagos[$idcredito])){
					$xCred->setDetermineDatosDeEstatus($fecha, false, $force_updates, $DPagos[$idcredito], $EnCierre );
					unset($DPagos[$idcredito]);		//eliminar de memoria 2015-02-03
				} else {
					$xCred->setDetermineDatosDeEstatus($fecha, false, $force_updates, false, $EnCierre );
				}
				$xLog->add($xCred->getMessages(), $xLog->DEVELOPER);
			}
			$DPagos			= null;
		$this->mMessages	.= $xLog->getMessages();
		return $xLog->getMessages() ;
	}			//END FUNCTION
	function setValidarCreditos($fecha, $AppSucursal = true, $ReportToOficial = false){
		$xF		= new cFecha();
		$xQL	= new MQL();
		$fecha	= $xF->getFechaISO($xF);
		
		$msg	= "====\t\tVALIDADOR DE CREDITOS V 1.0.01\r\n";
			$cierre_sucursal	= "";
			if ($AppSucursal == true){
				//$cierre_sucursal = "AND (`creditos_solicitud`.`sucursal`='" . getSucursal() . "')";
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

				$rs_PAM 	= $xQL->getRecordset($sqlValidacion);
				if(!$rs_PAM){
					$msg	.= "LA CONSULTA NO SE EJECUTO (CODE: " . mysql_errno() . ")";
				} else {
					$i = 1;
					while($rw = $rs_PAM->fetch_assoc()){
						$solicitud		= $rw["numero_solicitud"];
						$socio			= $rw["numero_socio"];
						$oficial		= $rw["oficial_credito"];
						$txt			= "";
	
						$msg			.= "$i\tVERIFICANDO EL CREDITO $solicitud DE LA PERSONA # $socio\r\n";
						$clsCred	= new cCredito($solicitud, $socio);
						$clsCred->init($rw);
						$txt		= $clsCred->setVerificarValidez();
						if ( $ReportToOficial == true AND (trim($txt) != "") ){
							$cOficial	= new cOficial($oficial);
							$cOficial->addNote(MEMOS_TIPO_PENDIENTE, $oficial , $socio, $solicitud, $txt);
						}
						$msg	.= $txt;
						$i++;
					}
				}
		$this->mMessages	.= $msg;
		return $msg;
	}
	function setReestructurarICA($fecha_corte){
		$xQL					= new MQL();
		$periodo_de_calculo		= date("m", strtotime($fecha_corte));
		$ejercicio				= date("Y", strtotime($fecha_corte));
		$fecha_operacion		= $fecha_corte;
		$tipo_operacion			= 451;
		$msg					.= "============== REESTRUCTURAR EL ICA \r\n";
		/**
		 * llevar a cero los Intereses
		 */

		$sqlUICA = "UPDATE creditos_solicitud SET sdo_int_ant=0";
		$xQL->setRawQuery($sqlUICA);
		/**
		 * Eliminar el ica
		 */
				$sqlDEL = "DELETE FROM operaciones_mvtos WHERE tipo_operacion = 451 AND periodo_mensual<=$periodo_de_calculo AND periodo_anual <= $ejercicio";
				$myq 	= $xQL->setRawQuery($sqlDEL);

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
			$rs 		= $xQL->getRecordset($sqlConICA);
			while($rw 	= $rs->fetch_assoc()){
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
						$xQL->setRawQuery($UICA_sql);

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
		$credito	= setNoMenorQueCero($credito);
		$xLog		= new cCoreLog();
		$xQL		= new MQL();
		$xVis		= new cSQLVistas();
		$aplicar	= true;	
		$sql		= ($credito > DEFAULT_CREDITO) ? $xVis->CreditoPagosAcumulados($credito) : "SELECT * FROM  `vw_creditos_pagos_acumulados`";
		$rs			= $xQL->getRecordset($sql);
		
		//setLog($sql);
		while ($rw = $rs->fetch_assoc()){
			$persona	= setNoMenorQueCero($rw["persona"]);
			$idcredito	= setNoMenorQueCero($rw["credito"]);
			$interes	= setNoMenorQueCero($rw["interes_normal"],2);
			$mora		= setNoMenorQueCero($rw["interes_moratorio"],2);
			
			$capital	= setNoMenorQueCero($rw["capital"],2);
			$NSql		= "UPDATE `creditos_solicitud` SET `interes_normal_pagado`=$interes, `interes_moratorio_pagado`=$mora, `saldo_actual`= IF((`estatus_actual`=" . CREDITO_ESTADO_AUTORIZADO . " OR `estatus_actual`=" . CREDITO_ESTADO_SOLICITADO . "),0, (`monto_autorizado`-$capital)) WHERE `numero_solicitud`=$idcredito";
			
			//setLog($NSql);
			$ready		= $xQL->setRawQuery($NSql);
			if($ready == true){
				$aplicar	= false;
				$xLog->add("OK\t$persona\t$idcredito\tSaldos de Capital Abonos $capital Interes $interes y Mora $mora Actualizados\r\n", $xLog->DEVELOPER);
				$xM		= new cCreditosMontos($idcredito);
				if($xM->init()){ $xM->setInteresesPagados($interes, $mora); }				
			} else {
				$xLog->add("ERROR\t$persona\t$idcredito\tAl actualizar saldos de Capital Abonos $capital Interes $interes y Mora $mora Actualizados\r\n", $xLog->DEVELOPER);
			}
		}
		//============ Aplica si el Credito está autorizado y no tiene abonos
		if($aplicar == true AND $credito > DEFAULT_CREDITO){
			$xQL->setRawQuery("UPDATE `creditos_solicitud` SET `saldo_actual`=`monto_autorizado` WHERE `numero_solicitud`=$credito AND (`estatus_actual`!=" . CREDITO_ESTADO_AUTORIZADO . ") AND (`estatus_actual`!=" . CREDITO_ESTADO_SOLICITADO . ")");
		}
		$rs			= null;
		//============ Actualizar Saldos de Credito
		if(getEnCierre() == false){
			if($credito > DEFAULT_CREDITO){
				$sql2	= $xVis->CreditoAbonosTotales($credito);
			} else {
				if($xQL->getContarDe("tmp_creditos_abonos_totales")<=1){
					$xQL->setCall("proc_creditos_abonos_totales");
				}
				$sql2	= "SELECT * FROM  `tmp_creditos_abonos_totales` ";
			}
		} else {
			$sql2	= ($credito > DEFAULT_CREDITO) ? "SELECT * FROM  `tmp_creditos_abonos_totales` WHERE `docto_afectado` = $credito " : "SELECT * FROM  `tmp_creditos_abonos_totales` ";
		}
		
		$rs2		= $xQL->getRecordset($sql2);
		if($rs2){
			while($rw2 = $rs2->fetch_assoc() ){
				$credito	= $rw2["docto_afectado"];
				$xCred		= new cCredito();
				$xCred->init();
				$xCred->setRevisarSaldo(false, $rw2);
			}
			$rs2->free();
		}
		
		
		return $xLog->getMessages();
	}
	/**
	 * Cuadra Operaciones segun el Saldo reversible
	 * @deprecated
	 * @param string $FechaInicial
	 * @return string
	 */
	function setCuadrarCreditosBySaldo($FechaInicial = false){
		$xF			= new cFecha();
		$xQL		= new MQL();
		$Fecha		= $xF->getFechaISO();
		$msg		.= "=================== CUADRAR CREDITOS A PARTIR DEL SALDO EN CREDITOS\r\n";
		$cRec		= new cReciboDeOperacion(10);
		$xRec		= $cRec->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO, $Fecha, 1, 10, "RECIBO_DE_AJUSTES_DE_CAPTACION");
		$msg		.= "\t\tRECIBO\tEl Recibo de Operacion es $xRec\r\n";
		$cRec->setNumeroDeRecibo($xRec, true);

		//Array de Abonos Acumulados
		$arrAbAcumulados	= array();
		$SQLAbs 	= "SELECT * FROM creditos_abonos_acumulados";
		$rsAbs		= $xQL->getRecordset($SQLAbs);

		while ($rw = $rsAbs->fetch_assoc()){
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
			$rs	= $xQL->getRecordset($sql);

				while ($rw = $rsAbs->fetch_assoc()){
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

	function setReestructurarSDPM_Planes($SoloConSaldos = true, $creditoFiltrado = false, $forzarTodos = false, $fechaCorte = false, $fechaInicial = false, $EliminarTodo = true){
		$xLog			= new cCoreLog();
		$xF				= new cFecha();
		$xQL			= new MQL();
		$xRuls				= new cReglaDeNegocio();
		$DevengarVencidos	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_DEV_INTNOM_V);
		
		$creditoFiltrado= setNoMenorQueCero($creditoFiltrado);
		
		$fechaCorte		= $xF->getFechaISO($fechaCorte);
		$fechaInicial	= ($fechaInicial == false) ? $xF->getDiaInicial($fechaCorte) : $fechaInicial;
		$wCredito1		= ( $creditoFiltrado > DEFAULT_CREDITO) ? "  `creditos_mvtos_asdpm_planes`.`documento` = $creditoFiltrado " : " `documento` > 0";
		$wCredito2		= ( $creditoFiltrado > DEFAULT_CREDITO) ? "  numero_de_credito = $creditoFiltrado " : " numero_de_credito > 0 ";
		$ByRTM			= ( $creditoFiltrado > DEFAULT_CREDITO) ? " " : " SQL_CACHE " ;
		$By360			= " AND (SELECT COUNT(*) FROM `creditos_a_final_de_plazo` WHERE `credito`=`creditos_sdpm_historico`.`numero_de_credito`) <= 0";
		$ByO360			= " AND (SELECT COUNT(*) FROM `creditos_a_final_de_plazo` WHERE `credito`=`creditos_mvtos_asdpm_planes`.`documento`) <= 0";
		
		$wFecha1		= " AND (fecha >='$fechaInicial' AND fecha <='$fechaCorte' ) ";
		$wFecha2		= " AND (`fecha_actual` >= '$fechaInicial' AND `fecha_actual`<='$fechaCorte' ) ";
		//Establecer fecha de calculos
		$xQL->setRawQuery("SET @fecha_de_corte='$fechaCorte';");
		//
		$xLog->add("============\tSDPM Planes : $creditoFiltrado . $fechaInicial - $fechaCorte \r\n");
		if($creditoFiltrado > DEFAULT_CREDITO){ 
			$xLog->add("Socio\tCredito\tFecha\tMonto\tSaldo\tDias\tOperacion\tEstatus\tInteres\tMoratorios\r\n");
			//Eliminar rango de fechas 2015-02-09
			$By360		= "";
			$wFecha1	= " AND (`fecha` <='$fechaCorte') ";
			$wFecha2	= " AND (`fecha_actual`<='$fechaCorte')";
		}
		$BySaldo		= ($SoloConSaldos == true AND $creditoFiltrado <= DEFAULT_CREDITO) ? " AND (`creditos_mvtos_asdpm_planes`.`saldo_actual` >= " . TOLERANCIA_SALDOS . ") " : "";
		if($EliminarTodo == true){ 
			$DAction		= $xQL->setRawQuery("DELETE FROM creditos_sdpm_historico WHERE $wCredito2 $wFecha2 $By360");
		}
		//setLog(Memory_Usage());
		$sql 				= "SELECT $ByRTM `creditos_mvtos_asdpm_planes`.* FROM `creditos_mvtos_asdpm_planes` 
		WHERE $wCredito1 $wFecha1 $ByO360 $BySaldo ORDER BY `creditos_mvtos_asdpm_planes`.`documento`, `creditos_mvtos_asdpm_planes`.`fecha` ";
		
		//setLog($sql);
		
		//$rs					= $xQL->getDataRecord($sql);
		$rs					= $xQL->getRecordset($sql);
		//setLog(Memory_Usage());
		$saldo				= 0;
		$creditoA			= 0;
		$xT					= new cTipos();
		$xF					= new cFecha();
		//setLog($sql);
		
		$FECHA_DE_ULTIMO_PAGO	= EACP_FECHA_DE_CONSTITUCION;
		$CREDITO_SALDO_ANTERIOR	= 0;
		$MvtoAnterior			= OPERACION_CLAVE_MINISTRACION;
		$ESTADO_ACTUAL			= CREDITO_ESTADO_VIGENTE;
		$DIVISOR_DE_INTERESES	= EACP_DIAS_INTERES;
		$DCred					= array();
		$IsCredNew				= true;
		$xCred					= null;
		$txt					= "";		//mensajes de aplicacion
		$arrSDOS				= array();	//array de Saldos a Insertas
		$txtSDOS				= "";		//TXT SQL de la insercion de Saldos
		$sucursal				= getSucursal();
		$MONTO_AUTORIZADO		= 0;
		$FECHA_CORTE_MORA		= $fechaCorte;
		$ES_CREDITO_PAGADO		= false;	//Si el credito es pagado
		$ES_CREDITO_MORA		= false;	//Si el Credito entro en mora, por defecto no
		//$FECHA_DE_PRIMER_AT
		while($rw = $rs->fetch_assoc()){
		//foreach ( $rs as $rw ){
			$socio				= setNoMenorQueCero($rw["socio"]);
			$credito			= setNoMenorQueCero($rw["documento"]);
			$operacion			= setNoMenorQueCero($rw["operacion"]);
			$periodo			= setNoMenorQueCero($rw["periodo"]);
			$afectacion			= $rw["afectacion"];
			$monto				= setNoMenorQueCero($rw["monto"], 2);
			$monto_nomut		= $monto;
			$fecha				= $xF->getFechaISO( $rw["fecha"]);
			
			$nota				= "";
			//$dias_tolerados		= 0; //DIAS_PAGO_VARIOS;
			$IsCredNew			= true;
			$dias_transcurridos	= 0;
			
			if( $creditoA != $credito ){
				$xLog->add($txt, $xLog->DEVELOPER);
				if($xCred != null){
					//$xLog->add($xCred->getMessages(), $xLog->DEVELOPER);
				}
				$saldo					= 0;
				$FECHA_DE_ULTIMO_PAGO	= $fecha;
				$ESTADO_ACTUAL			= CREDITO_ESTADO_VIGENTE;
				$xCred					= new cCredito($credito, $socio); $xCred->init();
				$DCred					= $xCred->getDatosDeCredito();
				$CREDITO_SALDO_ANTERIOR	= 0;
				$OTipoPer				= $xCred->getOPeriocidad();
				$DIAS_TOLERADOS			= $xCred->getOProductoDeCredito()->getDiasTolerados();
				$DIAS_PARA_VENCIMIENTO	= ($OTipoPer == null) ? 0 : $OTipoPer->getDiasToleradosEnVencer();
				$TASA_MORATORIO			= $xCred->getTasaDeMora();
				$TASA_INTERES			= $xCred->getTasaDeInteres();
				$PERIOCIDAD_DE_PAGO		= $xCred->getPeriocidadDePago();
				$MONTO_AUTORIZADO		= $xCred->getMontoAutorizado();
				//si es Ministracion
				if($MvtoAnterior	== OPERACION_CLAVE_MINISTRACION){ $FECHA_DE_ULTIMO_PAGO	= $xCred->getFechaDeMinistracion(); }
				$xLog->add("------------\t\t$credito\t\t------------\r\n");
				$letras_en_mora			= array();
				if($EliminarTodo == false){ $xQL->setRawQuery("DELETE FROM creditos_sdpm_historico WHERE numero_de_credito = $credito $wFecha2"); }
				//Purgar Variables
				if(isset($FECHA_DE_COMPROMISO)){ unset($FECHA_DE_COMPROMISO); }
				$arrPagos				= array();
				$ESTADO_APLICADO		= false;
				$txt					= "";
				//======================= Evaluar Dias Tolerados
				$xFormulaDiasTolera		= $xCred->getOProductoDeCredito()->getFormulaDeDiasTolerancia();
				eval($xFormulaDiasTolera); //setError($xFormulaDiasTolera);
				if($forzarTodos == false AND $creditoFiltrado <= DEFAULT_CREDITO){
					$saldo				= $xCred->getSaldoActual($fechaCorte);
				}
				$saldo					= round($saldo,2);
				//cortar mora a la fecha de liquidacion
				if($xCred->getEsPagado() == true){
					$FECHA_CORTE_MORA	= $xCred->getFechaUltimoMvtoCapital();
					$FECHA_CORTE_MORA	= $xF->getFechaISO($FECHA_CORTE_MORA);
					$ES_CREDITO_PAGADO	= true;
				}
				$ES_CREDITO_MORA		= false;
			} else {
				$IsCredNew			= false;
			}
			
			
			$interes				= 0;
			$moratorio				= 0;
			$BASE_MORA				= 0;
			$DIAS_MORA				= 0;
			$FECHA_DE_TOLERANCIA	= $xF->setSumarDias($fecha, $DIAS_TOLERADOS );
			if($xCred->getPagosSinCapital() == false){
				//Interes Normal
				
				if($operacion == OPERACION_CLAVE_PLAN_CAPITAL AND $monto >0){

					
					if($xF->getInt($FECHA_DE_TOLERANCIA) < $xF->getInt($FECHA_CORTE_MORA)){
						$BASE_MORA		= $monto;
						$DIAS_MORA		= setNoMenorQueCero($xF->setRestarFechas($FECHA_CORTE_MORA, $fecha));
						$moratorio		= (($BASE_MORA * $DIAS_MORA) * $TASA_MORATORIO) / EACP_DIAS_INTERES;
						$moratorio		= setNoMenorQueCero($moratorio,2);
						$xLog->add( "WARN\t$periodo\t$fecha\tAgregando Mora por $moratorio con Base $BASE_MORA y dias $DIAS_MORA del $fecha al $FECHA_CORTE_MORA\r\n", $xLog->DEVELOPER);
						//Agregar Vencimientop 
						//Agregar Fecha de Primer Atraso
						if(!isset($FECHA_DE_COMPROMISO)){
							$FECHA_DE_COMPROMISO	= $fecha;
							$xLog->add( "WARN\t$periodo\t$fecha\tAgregando fecha de primer atraso a $fecha del pago $periodo PAGOS CON CAPITAL Con saldo $saldo\r\n", $xLog->DEVELOPER);
						//$xLog->add( , $xLog->DEVELOPER);
						}						
					}
				}			
			} else {
				//Establecer como Primer Vencimiento la primera fecha de pago
				if($operacion == OPERACION_CLAVE_PLAN_INTERES AND $monto >0){
					if($xF->getInt($FECHA_DE_TOLERANCIA) < $xF->getInt($fechaCorte)){
						if(!isset($FECHA_DE_COMPROMISO)){
							$FECHA_DE_COMPROMISO	= $fecha;
							$xLog->add( "WARN\t$periodo\t$fecha\tAgregando fecha de primer atraso a $fecha del pago $periodo PAGOS SOLO INTERES Con Saldo $saldo\r\n", $xLog->DEVELOPER);
							//$xLog->add( , $xLog->DEVELOPER);
							//Agregar el Interes sobre la Base Inicial de los Creditos sin pagos de Capital
							$BASE_MORA		= $xCred->getSaldoActual($fechaCorte);
							$DIAS_MORA		= setNoMenorQueCero($xF->setRestarFechas($fechaCorte, $fecha));
							$moratorio		= (($BASE_MORA * $DIAS_MORA) * $TASA_MORATORIO) / EACP_DIAS_INTERES;
							$moratorio		= setNoMenorQueCero($moratorio,2);							
						}
						//====================== VALIDAR
					}
				}
			}
			

			if($operacion == OPERACION_CLAVE_PLAN_INTERES OR $operacion == OPERACION_CLAVE_PAGO_INTERES){
				$interes				= $monto;
				
				if($xCred->getPagosSinCapital() == true){
					$dias_transcurridos		= $xF->setRestarFechas($fecha, $FECHA_DE_ULTIMO_PAGO);
					$saldo_calculado		= setNoMenorQueCero(($saldo * $dias_transcurridos), 2);
					$FECHA_DE_ULTIMO_PAGO	= $fecha;
					$xLog->add("WARN\t$periodo\t$fecha\tPagos sin Capital Dias $dias_transcurridos , Saldo $saldo_calculado , Fecha $fecha\r\n", $xLog->DEVELOPER);
				} else {
					if(!isset($saldo_calculado)){
						$saldo_calculado	= 0;
						$xLog->add("WARN\t$periodo\t$fecha\tNo existe el Saldo Calculado $saldo_calculado para Interes\r\n", $xLog->DEVELOPER);
					}
					//---------- Si el saldo es cero, el interes tambien
					if($saldo_calculado<=0 AND $operacion == OPERACION_CLAVE_PLAN_INTERES){
						$interes			= 0;
						$xLog->add("WARN\t$periodo\t$fecha\tEl Saldo Calculado $saldo_calculado es 0\r\n", $xLog->DEVELOPER);
					}
				}
				if($interes>0){
					$xLog->add("WARN\t$periodo\t$fecha\tAgregando Interes por $interes de la Operacion $operacion con Saldo $saldo_calculado\r\n", $xLog->DEVELOPER);
				}
			} else {
				$interes				= 0;
			}
			
			if($operacion == OPERACION_CLAVE_PAGO_MORA){
				if($moratorio >0){
					$moratorio	= $monto - $moratorio;
				} else {
					$moratorio	= $monto;
				}
				//setLog("MORA a $moratorio");
			}		
			//XXX: Checar
			//$saldo_calculado			= 0;
			/*if(!isset($saldo_calculado)){
				$saldo_calculado	= 0;
				$xLog->add("WARN\t$periodo\t$fecha\tNo existe el Saldo Calculado $saldo_calculado para Capital\r\n", $xLog->DEVELOPER);
			}*/
			//$xCred->getFechaUltimoMvtoCapital()
			if($operacion == OPERACION_CLAVE_PAGO_CAPITAL OR $operacion == OPERACION_CLAVE_MINISTRACION){
				$dias_transcurridos		= $xF->setRestarFechas($fecha, $FECHA_DE_ULTIMO_PAGO);
				$saldo_calculado		= setNoMenorQueCero( ($saldo * $dias_transcurridos), 2);
				$saldo					+= ($monto * $afectacion);
				$saldo					= round($saldo,2);
				$FECHA_DE_ULTIMO_PAGO	= $fecha;
				//disminuye de la letra
				if($operacion == OPERACION_CLAVE_PAGO_CAPITAL){
					
				}
			} else {
				$monto					= 0;
			}
			//Si es pago de interes, no pagado y la fecha actual es mayor a la de hoy
			if($DevengarVencidos == true AND $ES_CREDITO_MORA == true){
				//Si es interes de plan y hay saldo de credito
				if($operacion == OPERACION_CLAVE_PLAN_INTERES AND $saldo> 0){
					//Si la fecha de la operacion es menor a la de corte
					if( $xF->getInt($fecha) <= $xF->getInt($fechaCorte) ){
						$interes				= $monto_nomut;
					}
				}
			}
			//Venciendo la primera fecha
			if( isset($FECHA_DE_COMPROMISO) AND $ESTADO_APLICADO == false){
				$dias_de_atraso			= setNoMenorQueCero($xF->setRestarFechas($fechaCorte, $FECHA_DE_COMPROMISO));
				if($dias_de_atraso > 1){ 
					$ESTADO_ACTUAL		= CREDITO_ESTADO_MOROSO; 
					$ESTADO_APLICADO 	= true;
					$ES_CREDITO_MORA	= true;				//Marcar a Mora
				}		//Cambiar a Moroso
				if($dias_de_atraso > $DIAS_PARA_VENCIMIENTO){
					$ESTADO_ACTUAL		= CREDITO_ESTADO_VENCIDO;
					$ESTADO_APLICADO 	= true;
					$ES_CREDITO_MORA	= true;				//Mrcar a Mora
					$xLog->add( "WARN\t$periodo\t$fecha\tPeriodo a Vencido por $dias_de_atraso Dias de Atraso con $DIAS_PARA_VENCIMIENTO Dias Tolerados\r\n", $xLog->DEVELOPER);
				}
			}
			if($creditoFiltrado > DEFAULT_CREDITO){ 
				$txt	.= "$socio\t$credito\t$fecha\t$monto\t$saldo\t$dias_transcurridos\t$operacion\t$ESTADO_ACTUAL\t$interes\t $moratorio \t$nota\r\n";
			}
			
			$mm							= round(($monto + $interes + $moratorio),2);
			$periodo					= setNoMenorQueCero($periodo);
			if(($xF->getInt($fecha) <= $xF->getInt($fechaCorte) AND ($mm >0 ) )){
				//XXX: Aqui me quede
				//Inserta un nuevo movimiento si es filtrado, si no, genera un array con los valores
				//if($saldo>0.01){
				if($MONTO_AUTORIZADO > 0){ //Filtra por monto autorizado
					if($creditoFiltrado > DEFAULT_CREDITO){
						$xCred->addSDPM($interes, $moratorio, $FECHA_DE_ULTIMO_PAGO, $saldo, $ESTADO_ACTUAL, $fecha, $operacion, $saldo_calculado, $periodo);
					} else {
						if($saldo === false){
							$saldo = 0;
						}
						$arrSDOS[]			= "($socio, $credito, '$fecha', '$FECHA_DE_ULTIMO_PAGO', $dias_transcurridos, $saldo_calculado, $saldo, $ESTADO_ACTUAL, $interes, $moratorio,$operacion, '$sucursal', $periodo)";
					}
				}
				//}
				//numero_de_socio, numero_de_credito, fecha_actual, fecha_anterior, dias_transcurridos, monto_calculado, saldo, estatus, interes_normal, interes_moratorio, tipo_de_operacion, sucursal, periodo
			}
			
		
			if ( ($saldo <= TOLERANCIA_SALDOS) ){ $xLog->add("======\t\tEND- $credito\r\n", $xLog->DEVELOPER); }
			$creditoA					= $credito;
			
			$CREDITO_SALDO_ANTERIOR		= $saldo;
			$MvtoAnterior				= $operacion;
			
		}
		if($creditoFiltrado > DEFAULT_CREDITO){
			$xLog->add($txt, $xLog->DEVELOPER);
		} else {
			$conteo	= 0;
			foreach ($arrSDOS as $idx => $cnt){
				$txt		.= ($txt == "") ? $cnt : ",$cnt";
				if($conteo >= 10){
					$xQL->setRawQuery("INSERT INTO creditos_sdpm_historico( numero_de_socio, numero_de_credito, fecha_actual, fecha_anterior, dias_transcurridos, monto_calculado, saldo, estatus, interes_normal, interes_moratorio, tipo_de_operacion, sucursal, periodo) VALUES $txt");
					//setLog("INSERT INTO creditos_sdpm_historico( numero_de_socio, numero_de_credito, fecha_actual, fecha_anterior, dias_transcurridos, monto_calculado, saldo, estatus, interes_normal, interes_moratorio, tipo_de_operacion, sucursal, periodo) VALUES $txt");
					$conteo	= 0;
					$txt	= "";
				}
				$conteo++;
			}
			if($txt !== ""){
				$xQL->setRawQuery("INSERT INTO creditos_sdpm_historico( numero_de_socio, numero_de_credito, fecha_actual, fecha_anterior, dias_transcurridos, monto_calculado, saldo, estatus, interes_normal, interes_moratorio, tipo_de_operacion, sucursal, periodo) VALUES $txt");
			}
		}
		$rs								= null;
		if(MODO_DEBUG == true){
			$this->mMessages				.= $xLog->getMessages();
		}
		//setLog($this->mMessages);
		return $xLog->getMessages();	
	}
	function setReestructurarSDPM($SoloConSaldos = false, $idcredito = false, $forzarTodos = false, $fechaCorte = false, $fechaInicial = false, $EliminarTodo = true){
		$ql				= new MQL();
		$xLog			= new cCoreLog();
		$xF				= new cFecha();
		$idcredito		= setNoMenorQueCero($idcredito);
		$fechaCorte		= $xF->getFechaISO($fechaCorte);
		$IsGlobal		= ($idcredito >  0) ? false : true;
		
		$fechaInicial	= ($fechaInicial == false) ? $xF->getDiaInicial($fechaCorte) : $fechaInicial;
		$wCredito1		= ($IsGlobal == false) ? "  `creditos_mvtos_asdpm`.`documento` = $idcredito" : " `documento` > 0 ";
		$wCredito2		= ($IsGlobal == false) ? "  numero_de_credito = $idcredito " : " numero_de_credito > 0";
		$ByRTM			= ($IsGlobal == false) ? " " : " SQL_CACHE " ;
		
		$wFecha1		= " AND (fecha >='$fechaInicial' AND fecha <='$fechaCorte' ) ";
		$wFecha2		= " AND (`fecha_actual` >= '$fechaInicial' AND `fecha_actual`<='$fechaCorte' ) ";

		$By360			= " AND (SELECT COUNT(*) FROM `creditos_a_final_de_plazo` WHERE `credito`=`creditos_sdpm_historico`.`numero_de_credito`) > 0";
		$ByO360			= " AND (SELECT COUNT(*) FROM `creditos_a_final_de_plazo` WHERE `credito`=`creditos_mvtos_asdpm`.`documento`) > 0";
		$arrEstatusD	= array ( OPERACION_CLAVE_MINISTRACION, 111,	114, 115);
		$arrEstatus		= array (
				OPERACION_CLAVE_MINISTRACION => CREDITO_ESTADO_VIGENTE, 111 => CREDITO_ESTADO_VENCIDO,
				114 => CREDITO_ESTADO_VIGENTE, 115 => CREDITO_ESTADO_MOROSO
		);
	
		if($IsGlobal == false){
			$ql->setRawQuery("DELETE FROM operaciones_mvtos WHERE docto_afectado=$idcredito AND tipo_operacion = 420");
			$xLog->add("WARN\tEliminando Operaciones 420 del credito $idcredito\r\n", $xLog->DEVELOPER);
			//Eliminar el 360
			$wFecha1	= " AND (`fecha` <='$fechaCorte') ";
			$wFecha2	= " AND (`fecha_actual`<='$fechaCorte')";
			$By360		= "";
			$ByO360		= "";
			
		}
		//Eliminar el SDPM
		if($EliminarTodo == true){
			$DAction		= $ql->setRawQuery("DELETE FROM creditos_sdpm_historico WHERE $wCredito2 $wFecha2 $By360 ");
			//setLog("360: DELETE FROM creditos_sdpm_historico WHERE $wCredito2 $wFecha2 $By360 ");
			$xLog->add("WARN\tEliminando SDPM Creditos a Final de Plazo\r\n", $xLog->DEVELOPER);
		}		
		$xLog->add("Socio\tCredito\tFecha\tMonto\tSaldo\tDias\tOperacion\tEstatus\tInteres\tMoratorios\r\n");
		//Generar saldos de credito por mes
		$sql = "SELECT $ByRTM
		`creditos_mvtos_asdpm`.`socio`,
		`creditos_mvtos_asdpm`.`documento`,
		`creditos_mvtos_asdpm`.`recibo`,
		`creditos_mvtos_asdpm`.`fecha`,
		`creditos_mvtos_asdpm`.`operacion`,
		`creditos_mvtos_asdpm`.`monto`,
		`creditos_mvtos_asdpm`.`afectacion`
		FROM `creditos_mvtos_asdpm`	WHERE $wCredito1 $wFecha1 $ByO360 ";
		//setLog($sql);
		
		//$rsM					= $ql->getDataRecord($sql);
		$rsM					= $ql->getRecordset($sql);
		
		$saldo					= 0;
		$creditoA				= 0;
		$xT						= new cTipos();
		$xF						= new cFecha();
		$FECHA_DE_ULTIMO_PAGO	= "1998-01-01";
		$CREDITO_SALDO_ANTERIOR	= 0;
		$MvtoAnterior			= OPERACION_CLAVE_MINISTRACION;
		$ESTADO_ACTUAL			= CREDITO_ESTADO_VIGENTE;
		$DIVISOR_DE_INTERESES	= EACP_DIAS_INTERES;
		$DCred					= array();
		$IsCredNew				= true;
		$xCred					= null;
		
		if($rsM){
			while ($rw = $rsM->fetch_assoc() ){
					
				$socio			= $xT->cInt($rw["socio"]);
				$credito		= setNoMenorQueCero($rw["documento"]);
				$fecha			= $rw["fecha"];
				$nota			= "";
				//
				$IsCredNew		= true;
				if( $creditoA != $credito ){
					$saldo					= 0;
					$FECHA_DE_ULTIMO_PAGO	= $fecha;
					$ESTADO_ACTUAL			= CREDITO_ESTADO_VIGENTE;
					$xCred					= new cCredito($credito); $xCred->init();
					$DCred					= $xCred->getDatosDeCredito();
					$CREDITO_SALDO_ANTERIOR	= 0;
					$xLog->add("-- \tINIT_CREDITO : $credito ($creditoA)\r\n");
					//si es Ministracion
					if($MvtoAnterior	== OPERACION_CLAVE_MINISTRACION){ 
						$FECHA_DE_ULTIMO_PAGO	= $xCred->getFechaDeMinistracion();
						$xLog->add("$credito\tFecha de Ministracion a $FECHA_DE_ULTIMO_PAGO\r\n", $xLog->DEVELOPER);
					}
					
					if($EliminarTodo == false){ $ql->setRawQuery("DELETE FROM creditos_sdpm_historico WHERE numero_de_credito = $credito $wFecha2"); }
					if($IsGlobal == true){
						//si la fecha es actual, buscar el ultimo pago
						if($FECHA_DE_ULTIMO_PAGO == $xCred->getFechaUltimoMvtoCapital() OR ($xF->getInt($fechaCorte) > $xF->getInt($xCred->getFechaUltimoMvtoCapital())) ){
							$sqlpagoanterior		= "SELECT	MAX(`operaciones_mvtos`.`fecha_operacion`) AS 'fecha', SUM(`operaciones_mvtos`.`afectacion_real`) AS `abonos`  FROM	`operaciones_mvtos`
											WHERE (`operaciones_mvtos`.`fecha_operacion` < '$fecha') AND (`operaciones_mvtos`.`docto_afectado` = $credito) AND
												(`operaciones_mvtos`.`tipo_operacion` =". OPERACION_CLAVE_PAGO_CAPITAL . ") GROUP BY `operaciones_mvtos`.`docto_afectado` ";
							$DPag				= $ql->getDataRow($sqlpagoanterior);
							if(isset($DPag["fecha"])){
								$xLog->add("Fecha anterior : $FECHA_DE_ULTIMO_PAGO a " . $DPag["fecha"] . "\r\n");
								$FECHA_DE_ULTIMO_PAGO		= $DPag["fecha"];
								//Ajustar fecha de Ultimo corte de Mes
								if($xF->getInt($FECHA_DE_ULTIMO_PAGO) < $xF->getInt($xF->getDiaInicial()) ){
									$FECHA_DE_ULTIMO_PAGO	= $xF->setRestarDias(1, $xF->getDiaInicial());
								}
								$saldo						= setNoMenorQueCero(($xCred->getMontoAutorizado() - setNoMenorQueCero($DPag["abonos"])));
							}
						}
					}
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
				$saldo_calculado		= setNoMenorQueCero( ($dias_transcurridos * $saldo), 2);
				//No poner la afectacion
				$saldo					+= $xT->cFloat( ($monto * $afectacion), 2 );
				// si es normal, calcular normal, si es mora: Calcular mora y normal, si es vencido: calcular normal y mora
				$interes					= 0;
				$moratorio					= 0;
				$TASA_NORMAL				= $xCred->getTasaDeInteres();
				$TASA_MORA					= $xCred->getTasaDeMora();
				$TIPO_DE_PAGO				= $xCred->getTipoDePago();
				$PAGOS_SIN_CAPITAL			= $xCred->getPagosSinCapital();
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
				$xLog->add("$socio\t$credito\t$fecha\t$monto\t$saldo\t$dias_transcurridos\t$operacion\t$ESTADO_ACTUAL\t$interes\t$moratorio\t$nota\r\n", $xLog->DEVELOPER);
				
				if($xF->getInt($fecha) <= $xF->getInt(SYS_FECHA_DE_MIGRACION) ){
					$interes				= 0;
					$moratorio				= 0;
					$xLog->add("WARN\tOMITIR Interes $interes y MORA por $moratorio por estar antes de la migracion $fecha\r\n", $xLog->DEVELOPER);
				}
				
				if($xF->getInt($fecha) <= $xF->getInt($fechaCorte)){
					$msgSDPM				= $xCred->addSDPM($interes, $moratorio, $FECHA_DE_ULTIMO_PAGO, $saldo, $ESTADO_ACTUAL, $fecha, $operacion, $saldo_calculado);
				}
		
				if ( ($saldo <= TOLERANCIA_SALDOS) ){ $xLog->add( "--\tEND_CREDITO : $credito\r\n", $xLog->DEVELOPER); }
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
		}
		$rsM							= null;
		$this->mMessages				.= $xLog->getMessages();
		return $xLog->getMessages();
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
		$xLog			= new cCoreLog();
		$recibo 		= $CRecibo->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO,
									$fechaRec, 1, 10,
									$observaciones, DEFAULT_CHEQUE, "ninguno", DEFAULT_RECIBO_FISCAL, DEFAULT_GRUPO );
		$CRecibo->setNumeroDeRecibo($recibo);
		
		$_SESSION["recibo_en_proceso"]		= $recibo;
		$xLog->add("==== Recalcular los Intereses Devengados ====\r\n");
		$xLog->add("===\tFECHA INICIAL: $fechaInicial\tFECHA FINAL: $fechaFinal\r\n");
		//Equivalencias de movimientos
		//TODO: Solo generar creditos 360
		$wSDPM			= "";
		$wMvto			= "";
		$cFols			= 0;
		$items			= 0;	//contar registros
		$wDLimit		= " AND ( fecha_actual >= '$fechaInicial'  AND fecha_actual <='$fechaFinal' )";
		$wDLimitM		= " AND ( fecha_operacion >= '$fechaInicial' AND fecha_operacion <='$fechaFinal' ) ";
		if ( setNoMenorQueCero($credito) > DEFAULT_CREDITO ){
			$wSDPM		= " AND numero_de_credito = $credito ";
			$wMvto		= " AND docto_afectado = $credito ";
			$wDLimit	= "";
			$wDLimitM	= "";
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
		$xQL->setRawQuery($sqlDM);

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
					`creditos_sdpm_historico` `creditos_sdpm_historico`
					WHERE `creditos_sdpm_historico`.`numero_de_credito` >0 
		$wDLimit $wSDPM ORDER BY `creditos_sdpm_historico`.`numero_de_credito`, `creditos_sdpm_historico`.`fecha_actual`";
		//$rs 		= $xQL->getDataRecord($sqlM);
		$rs 		= $xQL->getRecordset($sqlM);
	
		while ($rw = $rs->fetch_assoc() ){
			$socio			= $rw["numero_de_socio"];
			$solicitud		= $rw["numero_de_credito"];
			$observaciones	= "";
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
			//repara los folios por ser eficiente por cada 1000
			if ( $cFols >= 1000 ){ $cFols = 0;	setFoliosAlMaximo(); }
			if ( $moratorio > 0 ){ 
				$CRecibo->setNuevoMvto($fecha, $moratorio, $OpMora, $periodo, $observaciones, 1, TM_CARGO, $socio, $solicitud);
				
			}
			if ( $interes > 0 ){
				$CRecibo->setNuevoMvto($fecha, $interes, $OpNormal, $periodo, $observaciones, 1, TM_CARGO, $socio, $solicitud);
			}
			$cFols++;
			$items++;
		}
		if($items <= 0){
			$CRecibo->setRevertir(true);
		} else {
			$CRecibo->setFinalizarRecibo();
		}
		$xLog->add($CRecibo->getMessages(), $xLog->DEVELOPER);
		return $xLog->getMessages();		
	}
	function setGenerarMvtoFinDeMes($fecha_inicial, $fecha_final, $NumeroDeCredito = false, $ForzarEnPeriodo = false, $recibo = false ){
		$xLog		= new cCoreLog();
		$xQL		= new MQL();
		$xLi		= new cSQLListas();
		$xF			= new cFecha();
		$sql		= $xLi->getInicialDeCreditos();
		$sql		.= " WHERE (`creditos_solicitud`.`estatus_actual` !=50) ";
		
		$IntMes		= $xF->getInt($fecha_final);
		$idcredito	= setNoMenorQueCero($NumeroDeCredito);
		$IsGlobal	= ($idcredito > DEFAULT_CREDITO) ? false : true;

		$W2Cred		= " AND `fecha_operacion`>='$fecha_inicial' AND  `fecha_operacion`<='$fecha_final' ";
		if($IsGlobal == true){
			
		} else {
			$W2Cred	= " AND `docto_afectado`= $idcredito ";
			$sql	.= " AND (`creditos_solicitud`.`numero_solicitud` = $idcredito) ";
		}
		$recibo		= setNoMenorQueCero($recibo);
		$recibo		= ($recibo <= 0) ? DEFAULT_RECIBO : $recibo;
		//Eliminar Operaciones
		$xQL->setRawQuery("DELETE FROM `operaciones_mvtos` WHERE `tipo_operacion` = " . OPERACION_CLAVE_FIN_DE_MES . " $W2Cred ");
		$rs			= $xQL->getRecordset($sql);
		$xRec		= new cReciboDeOperacion(false, false, $recibo);
		$xRec->init();
		if($rs){
			while ($rw = $rs->fetch_assoc() ){
				$credito		= $rw["numero_solicitud"];
				$xCred			= new cCredito($credito);
				$xCred->init($rw);
				$persona		= $xCred->getClaveDePersona();
				//$date = strtotime(date("Y-m-d", strtotime($date)) . " +1 month");
				$meses			= ceil( $xF->setRestarFechas($fecha_final, $xCred->getFechaDeMinistracion()) / SYS_FACTOR_DIAS_MES );
				$IntInit		= ($xF->getInt($fecha_inicial) >= $xF->getInt($xCred->getFechaDeMinistracion()) ) ?  $xF->getInt($fecha_inicial): $xF->getInt($xCred->getFechaDeMinistracion());
				$xLog->add("======= CREDITO $credito Persona $persona en Meses $meses\r\n");
				for($i = 0; $i <= $meses; $i++){
					$FechaMes	= ($i == 0 ) ? $xF->getDiaFinal($xCred->getFechaDeMinistracion()) : $xF->setSumarMeses(1, $FechaMes);
					//$xF->set($FechaMes);
					//$xLog->add("WARN\t$persona\t$credito\tFecha Omitida $FechaMes\r\n", $xLog->DEVELOPER);
					//obtener abonos segun 
					if($xF->getInt($FechaMes) >= $IntInit AND $xF->getInt($FechaMes) <= $IntMes){
						$currInt			= $xF->getInt($FechaMes);
						$annio 				= date("Y", $currInt);
						$mmes				= date("m", $currInt);
						$DQL				= $xQL->getDataRow("SELECT SUM(`abonos`) AS 'monto', MAX(`fecha`) AS 'fecha' FROM `creditos_abonos_por_mes` WHERE `credito`= $credito AND periodo <=$annio$mmes");
						//setLog("SELECT SUM(`abonos`) AS 'monto', MAX(`fecha`) AS 'fecha' FROM `creditos_abonos_por_mes` WHERE `credito`= $credito AND periodo <=$annio$mmes");
						$fechaMov			= date("Y-m-t", strtotime("$annio-$mmes-01"));
						$observaciones		= "Cierre a Fin de mes $fechaMov";
						$monto_al_cierre	= setNoMenorQueCero(($xCred->getMontoAutorizado() - $DQL["monto"]), 2);
						if($monto_al_cierre >= TOLERANCIA_SALDOS){
							$operacion		= $xRec->setNuevoMvto($fechaMov, $monto_al_cierre, OPERACION_CLAVE_FIN_DE_MES, 1, $observaciones, 0, TM_CARGO, $persona, $credito);
							$xLog->add("OK\t$persona\t$credito\t$fechaMov\t$monto_al_cierre\r\n");
						} else {
							$xLog->add("OMITIDO\t$persona\t$credito\t$fechaMov\t$monto_al_cierre\r\n", $xLog->DEVELOPER);
						}
						//$xLog->add("$i\t$socio\t$solicitud\t$fecha\t$fin_de_mes\t$saldoActual\t$operacion\r\n");
					} else {
						$xLog->add("WARN\t$persona\t$credito\tFecha Omitida $FechaMes\r\n", $xLog->DEVELOPER);
					}
				}
			}
		}
		//setLog($xLog->getMessages());
		return $xLog->getMessages();
	}
	function setAcumularIntereses($Forzar = false, $credito = false, $todos = false){
		$xLog		= new cCoreLog();
		$xQL		= new MQL();
		$xL			= new cSQLListas();
		$xFil		= new cSQLFiltros();
		$xRuls		= new cReglaDeNegocio();
		$BaseByDoc	= "";
		$useMoraBD	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_USE_MORA_BD);
		
		$sucursal	= getSucursal();
		if ( $Forzar == true ){ 
			$xLog->add( "======\t\tActualizacion FORZADA\r\n");	
		}
		$credito		= setNoMenorQueCero($credito);
		$ByCredito		= $xFil->CreditoPorClave($credito);
		$BaseByDoc		= $xFil->OperacionesPorDocumento($credito);
		$ByFinalPzo		= $xFil->CreditosSiFinalDePlazo();
		$ByNoFinalPzo	= $xFil->CreditosNoFinalDePlazo();
		
		$BySaldo		= ($todos == true OR $credito > DEFAULT_CREDITO) ? "" : " AND (`saldo_actual` > 0) ";
		$sqlACero		= $xL->setCreditosSaldosDeInteres($credito, 0,0,0,0,0);
		$xQL->setRawQuery($sqlACero);
		
		
		if($useMoraBD == true){
			
		} else {
			
		}
		


		$xB			= new cBases(0);
		$xB->setClave($xB->BASE_MORA_DEV);
		$aMorDev	= $xB->getBaseMvtosInArray($BaseByDoc);
		$xB->setClave($xB->BASE_MORA_PAG);
		$aMorPag	= $xB->getBaseMvtosInArray($BaseByDoc);
		$xB->setClave($xB->BASE_INTS_DEV);
		$aNorDev	= $xB->getBaseMvtosInArray($BaseByDoc);
		$xB->setClave($xB->BASE_INTS_PAG);
		$aNorPag	= $xB->getBaseMvtosInArray($BaseByDoc);

		$conteo		= 1;
		$sql		= "SELECT * FROM creditos_solicitud WHERE numero_solicitud !=0 $ByCredito $BySaldo ORDER BY saldo_actual, fecha_ministracion";
		$rs			=  $xQL->getRecordset($sql);
        while ($rw = $rs->fetch_assoc()){
        	
			$socio				= $rw["numero_socio"];
			$solicitud			= $rw["numero_solicitud"];
			$saldo				= $rw["saldo_actual"];
			$periodicidad		= $rw["periocidad_de_pago"];
			//$sucursal	= $rw["sucursal"];
			$IntMorDev	= 0;
			$IntMorPag	= 0;
					
			$IntNorDev	= 0; 
			$IntNorPag	= 0;
			//eliminacion de memoria
			if( isset($aMorDev["$socio@$solicitud"]) ){ $IntMorDev	= round( setNoMenorQueCero($aMorDev["$socio@$solicitud"]), 2); unset($aMorDev["$socio@$solicitud"]); }
			if( isset($aMorPag["$socio@$solicitud"]) ){ $IntMorPag	= round( setNoMenorQueCero($aMorPag["$socio@$solicitud"]), 2); unset($aMorPag["$socio@$solicitud"]); }
			if( isset($aNorDev["$socio@$solicitud"]) ){ $IntNorDev	= round( setNoMenorQueCero($aNorDev["$socio@$solicitud"]), 2); unset($aNorDev["$socio@$solicitud"]); }
			if( isset($aNorPag["$socio@$solicitud"]) ){ $IntNorPag	= round( setNoMenorQueCero($aNorPag["$socio@$solicitud"]), 2); unset($aNorPag["$socio@$solicitud"]); }
			$sdoNorm	= round( ($IntNorDev - $IntNorPag) , 2);
			$sdoMor		= round( ($IntMorDev - $IntMorPag) , 2);
			if ( ( $saldo <= TOLERANCIA_SALDOS) AND ($Forzar == false) ) {
				$xLog->add("$conteo\t$socio\t$solicitud\tNOTA_INTERES\tCredito Pagado, Saldo $saldo, Intereses a Cero\r\n ", $xLog->DEVELOPER);
				$IntMorDev	= 0;
				$IntMorPag	= 0;
				$IntNorDev	= 0;
				$IntNorPag	= 0;
				$sdoNorm	= 0;
				$sdoMor		= 0;							
			} else {
				$xLog->add("$conteo\t$socio\t$solicitud\tINT_NORMAL_C\tDevengado:\t$IntNorDev\tPagado:\t$IntNorPag\tSaldo\t$sdoNorm\r\n ", $xLog->DEVELOPER);
				if ( $IntMorPag != 0 AND $IntMorDev != 0 ){
					$xLog->add("$conteo\t$socio\t$solicitud\tINT_MORATORIO\tDevengado:\t$IntMorDev\tPagado:\t$IntMorPag\tSaldo\t$sdoMor \r\n ", $xLog->DEVELOPER);
				}				
			}

			
			if($credito > DEFAULT_CREDITO){
				$xIntDev		= new cCreditosMontos($solicitud);
				if($xIntDev->init() == true){
						$xIntDev->setInteresesDevengados($IntNorDev, $IntMorDev);
						$xIntDev->setInteresesPagados($IntNorPag, $IntMorPag);
					
				}
				//$xLog->add($xIntDev->getMe)
			} else {
				$xQL->setRawQuery("UPDATE `creditos_montos` SET  `marca_tiempo`=UNIX_TIMESTAMP(), `marca_acceso`=UNIX_TIMESTAMP(), `interes_n_dev`=$IntNorDev,`interes_n_pag`=$IntNorPag, `interes_m_dev`=$IntMorDev, `interes_m_pag`=$IntMorPag  WHERE `clave_de_credito`=$solicitud");
			}

			$sql				= $xL->setCreditosSaldosDeInteres($solicitud, $IntNorPag, $IntNorDev, $IntMorPag, $IntMorDev, 0);
			$xQL->setRawQuery($sql);
			
			
			$conteo++;
		}
		//Actualizacion Masiva
		$rs			= null;
		
		$aMorDev	= null;
		$aMorPag	= null;
		$aNorDev	= null;
		$aNorPag	= null;
		if(MODO_DEBUG == true){
			$this->mMessages	.= $xLog->getMessages();
		}
		//setLog($xLog->getMessages());
		return $xLog->getMessages();	
	}
	function setCleanCreditosConAhorro(){
		$xQL	= new MQL();
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
	$rsUCTA		= $xQL->getRecordset($sql_sentencia);
	while ( $rw = $rsUCTA->fetch_assoc() ) {
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
    				$xICta	= $xQL->setRawQuery($SQLNCta);
    				$cuenta_relacionada	= $cuenta_nueva;
    					if ($xICta === false ){
    						$msg	.= date("Y-m-d H:i:s") . "\tERROR AL EFECTUAR EL ALTA: EL SISTEMA DEVOLVIO . " . $xICta["error"] . "\r\n";
    					}
    			} else {
    				$cuenta_relacionada	= $datos_de_la_cuenta["numero_cuenta"];
    			}
				//Actualiza el Contrato corriente relacionado

				$sql_update_credito = "UPDATE creditos_solicitud
										SET contrato_corriente_relacionado=$cuenta_relacionada
										WHERE numero_solicitud=$credito";
				$x = $xQL->setRawQuery($sql_update_credito);
					if( $x === false ){
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
		$xQL				= new MQL();	    
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
			$rsP			= $xQL->getRecordset($sqlP);
			$msg			.= "============ ULTIMOS MVTOS HASTA LA FECHA $fecha_final \r\n";
				
			while ( $rwP	= $rsP->fetch_assoc() ){
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
			$rsMx				= $xQL->getRecordset($sql);
			//$msg				.= $sql ."\r\n" . $sqlDM  . "\r\n";
			$msg				.= "============ GENERANDO MOVIMIENTOS 999: Saldos al Fin del mes \r\n";	
			$xF					= new cFecha();


				
			while ( $rw	= $rsMx->fetch_assoc() ){
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
					$xQL->setRawQuery($sqlUC);
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
	function setEliminarCreditosNegativos($tolerancia = 0){
		//Crear un nuevo Recibo de Ajuste
		$tolerancia	= setNoMenorQueCero($tolerancia);
		$tolerancia	= ($tolerancia <=0) ? TOLERANCIA_SALDOS : $tolerancia;
		$xQL        = new MQL();
		$xF			= new cFecha();
		$fecha		= $xF->get();
		//$cRec		= new cReciboDeOperacion(10);
		$msg        = "";
		//$xRec		= $cRec->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO, fechasys(), 1, 10, "RECIBO_DE_AJUSTES_DE_CREDITOS");
		//$msg		= "============\tRECIBO\tEl Recibo de Operacion es $xRec\r\n";
		//$cRec->setNumeroDeRecibo($xRec, true);
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
								WHERE saldo_actual < " . $tolerancia . " ORDER BY saldo_actual ";
						$rs			= $xQL->getRecordset( $sql );
						
						while($rw = $rs->fetch_assoc()){
								$socio		 		= $rw["numero_socio"];
								$credito	 		= $rw["numero_solicitud"];
								$saldo_actual		= round($rw["saldo_actual"],2);
								if($saldo_actual != 0){
									//Se inicializa una nueva instancia de crédito
									$xCred 			= new cCredito($credito, $socio);
									//y se neutralizara con su valor absoluto.
									if($xCred->init($rw) == true){
										$xRec		= new cReciboDeOperacion();
										$parcialidad= $xCred->getPagosAutorizados();
										$idrecibo	= $xRec->setNuevoRecibo($socio, $credito, $fecha, $parcialidad, RECIBOS_TIPO_PAGO_CREDITO, "", "", TESORERIA_COBRO_NINGUNO);
										$xRec->setForzarNegativos(true);
										$monto		= $saldo_actual;
										$xRec->addMovimiento(OPERACION_CLAVE_PAGO_CAPITAL, $monto, $parcialidad, "", 1);
										$xRec->setFinalizarRecibo(true);
										$msg		.= $xRec->getMessages();
										//Generar un abono a Capital
										//$xCred->setAbonoCapital($saldo_actual);
									}
									$xCred->setCuandoSeActualiza();
									$msg	.= "-\r\n-\r\n";
									$msg	.= "$socio\t$credito\tEliminando el saldo de $saldo_actual\r\n";
									$msg	.=  $xCred->getMessages();
								} else {
									$msg	.= "$socio\t$credito\tNo se toca , con un saldo de $saldo_actual\r\n";
								}
						}
						$rs->free();
                        //$cRec->setFinalizarRecibo(true);

		return $msg;
	}
	function setEliminarCreditosEnTemp($tolerancia = 0){
	    //Crear un nuevo Recibo de Ajuste
	    $tolerancia	= setNoMenorQueCero($tolerancia);
	    $tolerancia	= ($tolerancia <=0) ? TOLERANCIA_SALDOS : $tolerancia;
	    $xQL        = new MQL();
	    //$cRec		= new cReciboDeOperacion(10);
	    $msg        = "";
	    //$xRec		= $cRec->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO, fechasys(), 1, 10, "RECIBO_DE_AJUSTES_DE_CREDITOS");
	    //$msg		= "============\tRECIBO\tEl Recibo de Operacion es $xRec\r\n";
	    //$cRec->setNumeroDeRecibo($xRec, true);
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
								WHERE `saldo_actual` >= " . $tolerancia . " AND (SELECT COUNT(`field_id1`) FROM `general_tmp` WHERE `general_tmp`.`field_id1`=`creditos_solicitud`.`numero_socio`)>0 ";
	    //setLog($sql);
	    
	    $rs			= $xQL->getRecordset( $sql );
	    while ($rw=$rs->fetch_assoc()) {
	        $socio		 	= $rw["numero_socio"];
	        $credito	 	= $rw["numero_solicitud"];
	        $saldo_actual	= $rw["saldo_actual"];
	        if($saldo_actual !== 0){
	            //Se inicializa una nueva instancia de crédito
	            $cCredito 		= new cCredito($credito, $socio);
	            //y se neutralizara con su valor absoluto.
	            $cCredito->init($rw);
	            //$cCredito->setReciboDeOperacion($xRec);
	            //Generar un abono a Capital
	            $cCredito->setAbonoCapital($saldo_actual);
	            $msg	.= "-\r\n-\r\n";
	            $msg	.= "$socio\t$credito\tEliminando el saldo de $saldo_actual\r\n";
	            $msg	.=  $cCredito->getMessages();
	        }
	    }
	    $rs->free();
	    //$cRec->setFinalizarRecibo(true);
	    
	    return $msg;
	}
	function setEliminarInteresesDeCreditosPagados(){
		$msg		= "";
		$xQL		= new MQL();
		
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
				$sql	= "SELECT * FROM
				`creditos_solicitud` `creditos_solicitud`
			WHERE
				(`creditos_solicitud`.`saldo_actual` <= 0) AND
				(
				(`creditos_solicitud`.`interes_moratorio_devengado` != 0)
			OR
				(`creditos_solicitud`.`interes_normal_devengado` != 0)
				)";
			$rs	= $xQL->getRecordset($sql);
			
			while( $rw = $rs->fetch_assoc() ) {
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
							$OpNormal	= OPERACION_CLAVE_PAGO_INTERES;
							$MontoN		= $interesDN - $interesPN;
						} else {
							$MontoN		= $interesPN - $interesDN;
						}
						if ( $interesDM > $interesPM ){
							$OpNormal	= OPERACION_CLAVE_PAGO_MORA;
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
			$var["aml_alerts"]["C"]						="documento_relacionado";
			$var["aml_alerts"]["P"]						="persona_de_origen";			
			
			//? Funciona bien?
			//$var2["creditos_solicitud"]["C"]			="numero_solicitud";
			//$var2["creditos_solicitud"]["P"]			="persona_asociada";
			//$var2["socios_relaciones"]["C"]			="credito_relacionado";
			//$var2["socios_relaciones"]["P"]			="numero_socio";

			//$var2["operaciones_recibos"]["C"]			="docto_afectado";
			//$var2["operaciones_recibos"]["P"]			="persona_asociada";
			//================= 18 Agosto 2016
			$var["creditos_destino_detallado"]["C"]		="credito_vinculado";
			$var["creditos_destino_detallado"]["P"]		="clave_de_persona";

			$var["creditos_eventos"]["C"]				="credito";
			$var["creditos_eventos"]["P"]				="personas";
			//== tabla temporal
			$var["creditos_letras_del_dia"]["C"]				="credito";
			$var["creditos_letras_del_dia"]["P"]				="persona";
			
			$var["creditos_letras_pendientes"]["C"]				="docto_afectado";
			$var["creditos_letras_pendientes"]["P"]				="socio_afectado";
			//==
			//$var[""]["C"]				="numero_de_credito";
			//$var[""]["P"]				="numero_de_socio";
			
			//$var[""]["C"]				="";
			//$var[""]["P"]				="";			
			
			//$var[""]["C"]				="";
			//$var[""]["P"]				="";			
			
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
			$xCred->setCuandoSeActualiza();
			$this->mMessages	.= $xCred->getMessages();
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

	function setCorregirFechasDeLetras(){
		
	}
	function setAcumularMoraDeParcialidades($credito= false){
		$xQL	= new MQL();
		$xLi	= new cSQLListas();
		$xF		= new cFecha();
		$credito= setNoMenorQueCero($credito);
		$xLog	= new cCoreLog();
		$fecha	= $xF->get();
		$ByCred	= ($credito <= 0) ? "" : " AND  (`creditos_letras_del_dia`.`credito` = $credito) ";
		$sql	= "SELECT
				`creditos_letras_del_dia`.`credito`,
				SUM(`creditos_letras_del_dia`.`mora`) AS `mora` 
			FROM
				`creditos_letras_del_dia` `creditos_letras_del_dia` 
			WHERE `creditos_letras_del_dia`.`mora` >0 $ByCred
			GROUP BY
				`creditos_letras_del_dia`.`credito` ";
		$xQL->setRawQuery("SET @fecha_de_corte='$fecha';");
		if($credito > DEFAULT_CREDITO){
			$xCred	= new cCredito($credito);
			$idcredito	= $credito;
			if($xCred->init() == true){
				$sql	= $xLi->getListadoDeLetrasPendientes($credito, $xCred->getTasaIVAOtros(), $xCred->getPagosSinCapital());
			} else {
				$sql	= "SELECT `docto_afectado` AS `credito`,`interes_moratorio` AS `mora` FROM `creditos_letras_pendientes_rt` WHERE (`docto_afectado` =$credito) LIMIT 0,1";
			}
			$rs			= $xQL->getDataRecord($sql);
			$mora		= 0;
			foreach ($rs as $rw){
				$idcredito	= $rw["credito"];
				$mora		+= setNoMenorQueCero($rw["mora"], 2);
			}
			if($mora >= 0){
				$rs 	= $xQL->setRawQuery("UPDATE `creditos_solicitud` SET `interes_moratorio_devengado`=$mora WHERE `numero_solicitud`=$idcredito");
				$xQL->setRawQuery("UPDATE `creditos_montos` SET `interes_m_dev`=$mora WHERE `clave_de_credito`=$idcredito");
				//TODO: Agregar Registro en la tabla de Monto
				
				if($rs == false){
					$xLog->add("ERROR\tAl actualizar la mora del credito $idcredito con monto $mora\r\n", $xLog->DEVELOPER);
				} else {
					$xLog->add("OK\tAl actualizar la mora del credito $idcredito con monto $mora\r\n", $xLog->DEVELOPER);
				}
			} else {
				$xLog->add("ERROR\tAl agregar MORA ==\r\n", $xLog->DEVELOPER);
			}
		} else {
			$rs		= $xQL->getDataRecord($sql);
			foreach ($rs as $rw){
				$idcredito	= $rw["credito"];
				$mora		= setNoMenorQueCero($rw["mora"], 2);
					
				if($mora >= 0){
					$rs 	= $xQL->setRawQuery("UPDATE `creditos_solicitud` SET `interes_moratorio_devengado`=$mora WHERE `numero_solicitud`=$idcredito");
					$xQL->setRawQuery("UPDATE `creditos_montos` SET `interes_m_dev`=$mora WHERE `clave_de_credito`=$idcredito");
					//TODO: Agregar Registro en la tabla de Monto
					if($rs == false){
						$xLog->add("ERROR\tAl actualizar la mora del credito $idcredito con monto $mora\r\n", $xLog->DEVELOPER);
					} else {
						$xLog->add("OK\tAl actualizar la mora del credito $idcredito con monto $mora\r\n", $xLog->DEVELOPER);
					}
				}
			}			
		}
		
		//Correr Actualizacion de fecha en sistema congelados
		
		//setLog(" $sql");

		$rs					= null;
		$this->mMessages	.= $xLog->getMessages();
		return $xLog->getMessages();
	}
	function setCreditosCuadrarPlanes(){
		
		$xQL	= new MQL();
		$xLog	= new cCoreLog();
		if(getEnCierre() == false){
			$xQL->setRawQuery("CALL `proc_creds_prox_letras`()");
		}
		$sql	= "SELECT
			`tmp_creds_prox_letras`.`docto_afectado` AS `credito`,
			`creditos_solicitud`.`numero_socio`      AS `persona`,
			`creditos_solicitud`.`saldo_actual`		AS `capital`,
			SUM(`tmp_creds_prox_letras`.`capital`) AS `capital_plan`,
			(`creditos_solicitud`.`saldo_actual` -	SUM(`tmp_creds_prox_letras`.`capital`))   AS `diferencia`,
			if( (`creditos_solicitud`.`saldo_actual` >	SUM(`tmp_creds_prox_letras`.`capital`)), 'El Plan Menor al Saldo', 'El Plan Mayor al Saldo') AS `observaciones`
		FROM
			`creditos_solicitud` `creditos_solicitud`
				INNER JOIN `tmp_creds_prox_letras` `tmp_creds_prox_letras`
				ON `creditos_solicitud`.`numero_solicitud` = `tmp_creds_prox_letras`.
				`docto_afectado`
		WHERE `creditos_solicitud`.`periocidad_de_pago`!=360
		AND `creditos_solicitud`.`saldo_actual` > " . TOLERANCIA_SALDOS . "
			GROUP BY
				`tmp_creds_prox_letras`.`docto_afectado`
				
		HAVING
		 diferencia >1.99 OR  diferencia < -1.99 ORDER BY diferencia DESC ";
		//setLog($sql);
		
		$rs	= $xQL->getRecordset($sql);
		
		
		
		while($rw = $rs->fetch_assoc()){
			
			$idcredito	= $rw["credito"];
			$xCred		= new cCredito($idcredito);
			if($xCred->init() == true){
				if($xCred->getEsArrendamientoPuro() == false){
					$xEm	= new cPlanDePagosGenerador();
					if($xEm->initPorCredito($idcredito, $xCred->getDatosInArray()) == true){
						
						if($xCred->getEsAutorizado() == true OR $xCred->getEsAutorizado() == true OR $xCred->getEsRechazado() == true){
							$xLog->add("WARN\t$idcredito\tCredito con Estado Inactivo : " . $xCred->getEstadoActual() . "\r\n");
						} else {
							
							
							$xEm->setFechaArbitraria($xCred->getFechaPrimeraParc());
							
							$parcial 			= $xEm->getParcialidadPresumida($xCred->getFactorRedondeo());
							$xEm->setCompilar();
							//echo $xEm->getVersionFinal(false);
							$xEm->getVersionFinal(true);
							
							$xLog->add($xEm->getMessages());
						}
					}
				} else {
					$xLog->add("WARN\t$idcredito\tCredito de Arrendamiento Ignorado\r\n");
				} //end Arrendamiento
			
			}
		}
		$this->mMessages	.= $xLog->getMessages();
		
		
		
		return $xLog->getMessages();
	}
	function setRegenerarPlanPagosNoExistentes(){
		$xQL	= new MQL();
		$xLog	= new cCoreLog();
		
		$sql	= "SELECT   `creditos_solicitud`.* FROM `creditos_solicitud` WHERE ( `creditos_solicitud`.`saldo_actual` >=0 ) AND getPlanDePagoByCred(`creditos_solicitud`.`numero_solicitud`)=0";
		$rs		= $xQL->getRecordset($sql);
		
		while($rw = $rs->fetch_assoc()){
			$idcredito	= $rw["numero_solicitud"];
			$xCred		= new cCredito($idcredito);
			if($xCred->init() == true){
				
				$xEm	= new cPlanDePagosGenerador();
				if($xEm->initPorCredito($idcredito, $xCred->getDatosInArray()) == true){
					
					if($xCred->getEsAutorizado() == true OR $xCred->getEsAutorizado() == true OR $xCred->getEsRechazado() == true){
						$xLog->add("WARN\tCredito con Estado Inactivo : " . $xCred->getEstadoActual() . "\r\n");
					} else {
						
						
						$xEm->setFechaArbitraria($xCred->getFechaPrimeraParc());
						
						$parcial 			= $xEm->getParcialidadPresumida(100);
						$xEm->setCompilar();
						//echo $xEm->getVersionFinal(false);
						$xEm->getVersionFinal(true);
						
						$xLog->add($xEm->getMessages());
					}
				}
			}//end init
		}
		$this->mMessages	.= $xLog->getMessages();
		return $xLog->getMessages();
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
	function CreditosPorMunicipioAct($mun){
		$ByMun		= "";
		if(setNoMenorQueCero($mun)>0){
			$ByMun	= " AND getMunicipioByIDPers(`creditos_solicitud`.`numero_socio`)='$mun' ";
		}
		return $ByMun;
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
	function CreditosNoFinalDePlazo(){
		$By 		= " AND (creditos_solicitud.periocidad != 360) ";
		return $By;
	}
	function CreditosSiFinalDePlazo(){
		$By 		= " AND (creditos_solicitud.periocidad = 360) ";
		return $By;
	}
	function CreditosPorEmpresa($empresa){
		$empresa	= setNoMenorQueCero($empresa);
		$ByEmp		= "";
		if($empresa > 0){ $ByEmp = " AND (`creditos_solicitud`.`persona_asociada` = $empresa ) "; }
		return $ByEmp;
	}
	function CreditosPorGrupo($grupo){
		$grupo		= setNoMenorQueCero($grupo);
		$ByGrp		= "";
		if($grupo > 0){ $ByGrp= " AND (`creditos_solicitud`.`grupo_asociado` = $grupo ) "; }
		return $ByGrp;
	}	
	function CreditosPorDestino($destino){
		$destino	= setNoMenorQueCero($destino);
		$ByEmp		= "";
		if($destino > 0){ $ByEmp = " AND (`creditos_solicitud`.`destino_credito` = $destino ) "; }
		return $ByEmp;
	}
	function CreditosPorTipoDeCuota($tipocuota){
		$tipocuota	= setNoMenorQueCero($tipocuota);
		$ByTip		= "";
		if($tipocuota > 0){ $ByTip = " AND (`creditos_solicitud`.`tipo_de_pago` = $tipocuota ) "; }
		return $ByTip;
	}
	function CreditoPorClave($credito){
		$credito		= setNoMenorQueCero($credito);
		$ByCredito		= ($credito <= DEFAULT_CREDITO) ? "" : " AND (`creditos_solicitud`.`numero_solicitud`=$credito)  ";
		return $ByCredito;
	}
	function CreditoPorPersona($persona){
		$persona	= setNoMenorQueCero($persona);
		$By			= ($persona <= DEFAULT_SOCIO) ? "" : " AND (`creditos_solicitud`.`numero_socio`=$persona)  ";
		return $By;
	}
	function CreditosPorFechaDeMinistracion($FechaInicial = false, $FechaFinal=false){
		$filtro		= "";
		$xF			= new cFecha();
		if($FechaInicial !== false){
			$FechaInicial	= $xF->getFechaISO($FechaInicial);
			if($FechaFinal == false){
				$filtro	= " AND (`creditos_solicitud`.`fecha_ministracion`  = '$FechaInicial')";
			} else {
				$FechaFinal	= $xF->getFechaISO($FechaFinal);
				$filtro	= " AND (`creditos_solicitud`.`fecha_ministracion`  >= '$FechaInicial') AND (`creditos_solicitud`.`fecha_ministracion`  <= '$FechaFinal') ";
			}
		}
		return $filtro;		
	}
	function CreditosPorFechaDeSolicitud($FechaInicial = false, $FechaFinal=false){
		$filtro		= "";
		$xF			= new cFecha();
		if($FechaInicial !== false){
			$FechaInicial	= $xF->getFechaISO($FechaInicial);
			if($FechaFinal == false){
				$filtro	= " AND (`creditos_solicitud`.`fecha_solicitud`  = '$FechaInicial')";
			} else {
				$FechaFinal	= $xF->getFechaISO($FechaFinal);
				$filtro	= " AND (`creditos_solicitud`.`fecha_solicitud`  >= '$FechaInicial') AND (`creditos_solicitud`.`fecha_solicitud`  <= '$FechaFinal') ";
			}
		}
		return $filtro;
	}
	function CreditosPorFechaDeAutorizacion($FechaInicial = false, $FechaFinal=false){
		$filtro		= "";
		$xF			= new cFecha();
		if($FechaInicial !== false){
			$FechaInicial	= $xF->getFechaISO($FechaInicial);
			if($FechaFinal == false){
				$filtro	= " AND (`creditos_solicitud`.`fecha_autorizacion`  = '$FechaInicial')";
			} else {
				$FechaFinal	= $xF->getFechaISO($FechaFinal);
				$filtro	= " AND (`creditos_solicitud`.`fecha_autorizacion`  >= '$FechaInicial') AND (`creditos_solicitud`.`fecha_autorizacion`  <= '$FechaFinal') ";
			}
		}
		return $filtro;

	}	
//----------------- Productos de Credito
	function CreditosProductosPorSeguimiento($filtrar = false){
		$filtro		= "";
		//$xT			= new cTipos();
		//$filtrar	= $xT->cBool($filtrar);
		if($filtrar !== false){
			$filtro	= " AND (`creditos_tipoconvenio`.`omitir_seguimiento`=$filtrar) ";
		}
		return $filtro;
	}
	function CreditosProductosPorTSistema($tipo = false){
		$tipo		= setNoMenorQueCero($tipo);
		$By			= "";
		if($tipo>0){
			$By	= " AND (`creditos_tipoconvenio`.`tipo_en_sistema`=$tipo) ";
		}
		return $By;

	}
//----------------- Operaciones
	function OperacionesPorTipo($tipo = false){
		$tipo		= setNoMenorQueCero($tipo);
		$ByTipo		= ($tipo <= 0) ? "" : " AND (`operaciones_mvtos`.`tipo_operacion` = $tipo)  ";
		return $ByTipo;
	}
	function OperacionesPorSucursal($sucursal = SYS_TODAS){
		$ByTipo		= ($this->getEsCadenaDeTipoInvalida($sucursal) == true) ? "" : " AND (`operaciones_mvtos`.`sucursal` = '$sucursal')  ";
		return $ByTipo;
	}	
	function OperacionesPorPersona($persona = false){
		$persona	= setNoMenorQueCero($persona);
		$ByTipo		= ($persona <= DEFAULT_SOCIO) ? "" : " AND (`operaciones_mvtos`.`socio_afectado` = $persona)  ";
		return $ByTipo;
	}	
	function OperacionesPorDocumento($docto = false){
		$docto	= setNoMenorQueCero($docto);
		$ByTipo	= ($docto <= DEFAULT_CREDITO) ? "" : " AND (`operaciones_mvtos`.`docto_afectado` = $docto)  ";
		return $ByTipo;
	}
	function OperacionesPorPeriodo($periodo = false){
		$periodo	= setNoMenorQueCero($periodo);
		$By			= ($periodo <= 0) ? "" : " AND (`operaciones_mvtos`.`periodo_socio` = $periodo)  ";
		return $By;
	}
	function OperacionesPorRecibo($recibo = false){
		$recibo	= setNoMenorQueCero($recibo);
		$ByTipo	= ($recibo <= 0) ? "" : " AND (`operaciones_mvtos`.`recibo_afectado` = $recibo)  ";
		return $ByTipo;
	}
	function OperacionesPorFecha($FechaInicial = false, $FechaFinal = false){
		$ByFecha	= ($FechaInicial === false) ? "" : " AND (`operaciones_mvtos`.`fecha_operacion` >='$FechaInicial') ";
		$ByFecha	.= ($FechaFinal === false) ? "" : " AND	(`operaciones_mvtos`.`fecha_operacion`<='$FechaFinal' ) ";
		return $ByFecha;
	}
	//----------------- Bancos
	function BancosPorCuenta($cuenta = false){
		$cuenta		= setNoMenorQueCero($cuenta);
		$ByCta		= ($cuenta <= 0) ? "" : " AND `bancos_cuentas`.`idbancos_cuentas`=$cuenta  ";
		return $ByCta;
	}
	function BancosPorTipoDeOperacion($tipo = false){
		$tipo		= strtolower($tipo);
		$tipo		= ($tipo == SYS_TODAS OR $tipo == "") ? null : $tipo;
		$ByCta		= ($tipo == null) ? "" : " AND `bancos_operaciones`.`tipo_operacion`='$tipo'  ";
		return $ByCta;
	}	

	function CompromisosPorCredito($credito = false){
		$filtro		= "";
		$credito	= setNoMenorQueCero($credito);
		if($credito > DEFAULT_CREDITO){
			$filtro	= "	AND	(`seguimiento_compromisos`.`credito_comprometido` = $credito)";
		}
		return $filtro;
	}
	function CompromisosPorPersona($persona = false){
		$filtro		= "";
		$persona	= setNoMenorQueCero($persona);
		if($persona > DEFAULT_SOCIO){
			$filtro	= " AND (`seguimiento_compromisos`.`socio_comprometido`=$persona) ";
		}
		return $filtro;		
	}
	function CompromisosPorOficial($oficial = false){
		$filtro		= "";
		$oficial	= setNoMenorQueCero($oficial);
		if($oficial > 0){
			$filtro	= " AND (`seguimiento_compromisos`.`oficial_de_seguimiento`=$oficial) ";
		}
		return $filtro;
	}	
	function CompromisosPorFechas($FechaInicial, $FechaFinal = false){
		$filtro		= "";
		$xF			= new cFecha();
		$FechaInicial	= $xF->getFechaISO($FechaInicial);
		if($FechaFinal == false){
			$filtro	= " AND (`seguimiento_compromisos`.`fecha_vencimiento` = '$FechaInicial')";
		} else {
			$filtro	= " AND (`seguimiento_compromisos`.`fecha_vencimiento` >= '$FechaInicial') AND (`seguimiento_compromisos`.`fecha_vencimiento` <= '$FechaFinal') ";
		}

		return $filtro;
	}
	function OficialPorClave($id = false){
		$filtro		= "";
		$id			= setNoMenorQueCero($id);
		if(($id != DEFAULT_USER) AND ($id > 0)) {
			$filtro	= " AND (`oficiales`.`id` = $id) ";
		}
		return $filtro;
	}	
	function LlamadasPorCredito($credito = false){
		$filtro		= "";
		$credito	= setNoMenorQueCero($credito);
		if($credito > DEFAULT_CREDITO){
			$filtro	= "	AND	(`seguimiento_llamadas`.`numero_solicitud` = $credito)";
		}
		return $filtro;
	}
	function LlamadasPorPersona($persona = false){
		$filtro		= "";
		$persona	= setNoMenorQueCero($persona);
		if($persona > DEFAULT_SOCIO){
			$filtro	= " AND (`seguimiento_llamadas`.`numero_socio`=$persona) ";
		}
		return $filtro;
	}
	function LlamadasPorOficial($oficial = false){
		$filtro		= "";
		$oficial	= setNoMenorQueCero($oficial);
		if($oficial > 0){
			$filtro	= " AND (`seguimiento_llamadas`.`oficial_a_cargo`=$oficial) ";
		}
		return $filtro;
	}	
	function NotificacionesPorCredito($credito = false){
		$filtro		= "";
		$credito	= setNoMenorQueCero($credito);
		if($credito > DEFAULT_CREDITO){
			$filtro	= "	AND	(`seguimiento_notificaciones`.`numero_solicitud` = $credito)";
		}
		return $filtro;
	}
	function NotificacionesPorPersona($persona = false){
		$filtro		= "";
		$persona	= setNoMenorQueCero($persona);
		if($persona > DEFAULT_SOCIO){
			$filtro	= " AND `seguimiento_notificaciones`.`socio_notificado` = $persona";
		}
		return $filtro;
	}
	function NotificacionesPorOficial($oficial = false){
		$filtro		= "";
		$oficial	= setNoMenorQueCero($oficial);
		if($oficial > 0){
			$filtro	= " AND `seguimiento_notificaciones`.`oficial_de_seguimiento` = $oficial";
		}
		return $filtro;
	}	
	//----------------- RECIBOS
	function RecibosPorCodigo($codigo = false){
		$codigo		= setNoMenorQueCero($codigo);
		$ByTipo		= ($codigo <= 0) ? "" : " AND (`operaciones_recibos`.`idoperaciones_recibos` = $codigo)  ";
		return $ByTipo;
	}
	function RecibosPorTipo($tipo = false){
		$tipo		= setNoMenorQueCero($tipo);
		$ByTipo		= ($tipo <= 0) ? "" : " AND (`operaciones_recibos`.`tipo_docto` = $tipo)  ";
		return $ByTipo;
	}
	function RecibosPorPeriodo($periodo = false){
		$periodo	= setNoMenorQueCero($periodo);
		$ByTipo		= ($periodo <= 0) ? "" : " AND (`operaciones_recibos`.`periodo_de_documento` = $periodo)  ";
		return $ByTipo;
	}
	function RecibosPorCajero($cajero = false){
		$cajero	= setNoMenorQueCero($cajero);
		$ByTipo		= ($cajero <= 0) ? "" : " AND (`operaciones_recibos`.`idusuario` = $cajero)  ";
		return $ByTipo;
	}	
	function RecibosPorTipoDePago($tipo = false){
		$ByTipo		= ($this->getEsCadenaDeTipoInvalida($tipo) == true) ? "" : " AND (`operaciones_recibos`.`tipo_pago` ='$tipo')  ";
		return $ByTipo;
	}
	function RecibosPorMoneda($tipo = false){
		$ByTipo		= ($this->getEsCadenaDeTipoInvalida($tipo) == true) ? "" : " AND (`operaciones_recibos`.`clave_de_moneda` ='$tipo')  ";
		return $ByTipo;
	}
	function RecibosPorFechaDeRegistro($FechaInicial, $FechaFinal=false){
		$filtro		= "";
		$xF			= new cFecha();
		$FechaInicial	= $xF->getFechaISO($FechaInicial);
		if($FechaFinal == false){
			$filtro	= " AND (`operaciones_recibos`.`fecha_de_registro`  = '$FechaInicial')";
		} else {
			$filtro	= " AND (`operaciones_recibos`.`fecha_de_registro`  >= '$FechaInicial') AND (`operaciones_recibos`.`fecha_de_registro`  <= '$FechaFinal') ";
		}
	
		return $filtro;
	}
	function RecibosPorFecha($FechaInicial, $FechaFinal=false){
		$filtro		= "";
		$xF			= new cFecha();
		$FechaInicial	= $xF->getFechaISO($FechaInicial);
		if($FechaFinal == false){
			$filtro	= " AND (`operaciones_recibos`.`fecha_operacion`  = '$FechaInicial')";
		} else {
			$filtro	= " AND (`operaciones_recibos`.`fecha_operacion`  >= '$FechaInicial') AND (`operaciones_recibos`.`fecha_operacion`  <= '$FechaFinal') ";
		}
	
		return $filtro;
	}
	function RecibosArchPorFecha($FechaInicial, $FechaFinal=false){
		$filtro		= "";
		$xF			= new cFecha();
		$FechaInicial	= $xF->getFechaISO($FechaInicial);
		if($FechaFinal == false){
			$filtro	= " AND (`operaciones_recibos_arch`.`fecha_operacion`  = '$FechaInicial')";
		} else {
			$filtro	= " AND (`operaciones_recibos_arch`.`fecha_operacion`  >= '$FechaInicial') AND (`operaciones_recibos_arch`.`fecha_operacion`  <= '$FechaFinal') ";
		}
		
		return $filtro;
	}
	function RecibosPorPersonaAsociada($persona = false){
		$persona	= setNoMenorQueCero($persona);
		$ByTipo		= ($persona > 0) ? " AND (`operaciones_recibos`.`persona_asociada`= $persona)" : "";
		return $ByTipo;
	}
	function RecibosPorPersona($persona = false){
		$persona	= setNoMenorQueCero($persona);
		$ByTipo		= ($persona > 0) ? " AND (`operaciones_recibos`.`numero_socio`= $persona)" : "";
		return $ByTipo;
	}
	function RecibosPorSucursal($sucursal = ""){
		$ByTipo		= ($this->getEsCadenaDeTipoInvalida($sucursal) == true) ? "": " AND (`operaciones_recibos`.`sucursal`= '$sucursal')";
		return $ByTipo;
	}
	function RecibosNoEstadisticos(){
		$filtro	= " AND (`operaciones_recibostipo`.`afectacion_en_flujo_efvo`  != 'ninguna')";
		return $filtro;
	}
	private function getEsCadenaDeTipoInvalida($v){
		$v	= strtolower($v);
		return ($v == SYS_TODAS OR trim($v) == "" OR $v == SYS_DEFAULT OR $v === false) ? true : false;
	}
	function PersonasPorCajaLocal($cajalocal = false){
		$cajalocal	= setNoMenorQueCero($cajalocal);
		$ByTipo		= ($cajalocal > 0) ? " AND (`socios_general`.`cajalocal` = $cajalocal ) " : "";
		return $ByTipo;
	}
	function PersonasPorEstado($estado = false){
		$estado	= setNoMenorQueCero($estado);
		$ByTipo	= ($estado > 0) ? " AND (`socios_general`.`estatusactual` = $estado ) " : "";
		return $ByTipo;
	}
	function PersonasPorEmpresa($empresa = false){
		$empresa	= setNoMenorQueCero($empresa);
		$ByTipo		= ($empresa > 0) ? " AND (`socios_general`.`dependencia` = $empresa ) " : "";
		return $ByTipo;
	}
	function PersonasPorSucursal($sucursal = ""){
		$ByTipo		= ($this->getEsCadenaDeTipoInvalida($sucursal) == true) ? "" : " AND (`socios_general`.`sucursal` = '$sucursal' ) ";
		return $ByTipo;
	}
	function PersonasPorSucursalAut(){
		$By		= "";
		if(OPERACION_LIBERAR_SUCURSALES == false){
			$xUsr		= new cSystemUser();
			if($xUsr->getEsCorporativo() == false){
				$sucursal	= $xUsr->getSucursalAccede();
				$By			= " AND (`socios_general`.`sucursal`='" . $sucursal . "') ";
			}
		}
		return $By;
	}
	function CatalogoContPorNiveles($NivelInicial, $NivelFinal = false, $operador = "="){
		$NivelInicial	= setNoMenorQueCero($NivelInicial);
		$NivelFinal		= setNoMenorQueCero($NivelFinal);
		$By				="";
		if($NivelInicial > 0){
			$By			= ($NivelFinal>0) ? " AND (`contable_catalogo`.`digitoagrupador` >=$NivelInicial AND `contable_catalogo`.`digitoagrupador` <=$NivelFinal) " : " AND (`contable_catalogo`.`digitoagrupador` $operador $NivelInicial) ";
		}
		return $By;
	}
	function VPersonasPorCajaLocal($caja){
		$caja	= setNoMenorQueCero($caja);
		$By		= "";
		if($caja > 0){
			$By	= " AND (`personas`.`numero_caja_local`=$caja) ";
		}
		return $By;
	}
	function VPersonasPorSucursalAut($idsucursal = ""){
		$By		= "";
		$idsucursal = ($idsucursal === false) ? "" : $idsucursal;
		
		if(OPERACION_LIBERAR_SUCURSALES == false){
			$xUsr		= new cSystemUser();
			if($xUsr->getEsCorporativo() == false){
				$sucursal	= $xUsr->getSucursalAccede();
				$By			= " AND (`personas`.`sucursal`='" . $sucursal . "') ";
			} else {
				$xVal		= new cReglasDeValidacion();
				if($xVal->sucursal($idsucursal) == true){
					$sucursal	= $idsucursal;
					$By			= " AND (`personas`.`sucursal`='" . $sucursal . "') ";
				}
			}
		} else {
			$xVal		= new cReglasDeValidacion();
			if($xVal->sucursal($idsucursal) == true){
				$sucursal	= $idsucursal;
				$By			= " AND (`personas`.`sucursal`='" . $sucursal . "') ";
			}
		}
		return $By;
	}
	function VPersonasPorCodigo($codigo = false){
		$codigo	= setNoMenorQueCero($codigo);
		$xVals	= new cReglasDeValidacion();
		$By		= "";
		if($xVals->persona($codigo) == true){
			$By	= " AND (`personas`.`codigo`=$codigo) ";
		}
		return $By;
	}
	function VSociosPorCajaLocal($caja){
		$caja	= setNoMenorQueCero($caja);
		$By		= "";
		if($caja > 0){
			$By	= " AND (`socios`.`numero_caja_local`=$caja) ";
		}
		return $By;
	}
	function VSociosPorEmpresa($empresa){
		$empresa	= setNoMenorQueCero($empresa);
		$By		= "";
		if($empresa > 0){
			$By	= " AND (`socios`.`iddependencia`=$empresa) ";
		}
		return $By;
	}
	function VSociosPorSucursalAut(){
		$By		= "";
		if(OPERACION_LIBERAR_SUCURSALES == false){
			$xUsr		= new cSystemUser();
			if($xUsr->getEsCorporativo() == false){
				$sucursal	= $xUsr->getSucursalAccede();
				$By			= " AND (`socios`.`sucursal`='" . $sucursal . "') ";
			}
		}
		return $By;
	}
	function VSociosPorSucursal($sucursal = ""){
		$xVal	= new cReglasDeValidacion();
		$By		= "";
		if($xVal->sucursal($sucursal) == true){
			//$sucursal	= $xVal->v();
			$By			= " AND (`socios`.`sucursal`='" . $sucursal . "') ";
		}
		return $By;
	}
	function VSociosPorMunicipio($mun){
		$ByMun		= "";
		if(setNoMenorQueCero($mun)>0){
			$ByMun	= " AND getMunicipioByIDPers(`socios`.`codigo`)='$mun' ";
		}
		return $ByMun;
	}
	function LogPorFecha($FechaInicial, $FechaFinal = false){
		$filtro		= "";
		$xF			= new cFecha();
		$FechaInicial	= $xF->getFechaISO($FechaInicial);
		if($FechaFinal == false){
			$filtro	= " AND (`general_log`.`fecha_log` = '$FechaInicial')";
		} else {
			$filtro	= " AND (`general_log`.`fecha_log` >= '$FechaInicial') AND (`general_log`.`fecha_log` <= '$FechaFinal') ";
		}

		return $filtro;
		
	}
	function LogPorTipo($tipo =""){
		$filtro		= "";
		if($this->getEsCadenaDeTipoInvalida($tipo) == false){
			$filtro	= " AND (`general_error_codigos`.`type_err`='$tipo') ";
		}
		return $filtro;
	}
	function LogPorCodigoError($codigo = 0){
		$codigo		= setNoMenorQueCero($codigo);
		$filtro		= "";
		if($codigo > 0){
			$filtro	= " AND (`general_log`.`type_error`='$codigo') ";
		}
		return $filtro;
	}
	function LogPorBusqueda($texto = "", $operador= "AND"){
		$filtro		= "";
		if($this->getEsCadenaDeTipoInvalida($texto) == false){
			$filtro	= " $operador (`general_log`.`text_log` LIKE '%$texto%')  ";
		}
		return $filtro;
	}
	function LogPorUsuario($codigo = 0){
		$codigo		= setNoMenorQueCero($codigo);
		$filtro		= "";
		if($codigo > 0){
			$filtro	= " AND (`general_log`.`usr_log`='$codigo') ";
		}
		return $filtro;
	}
	function TesoreriaCajasPorEstado($estado = false){
		$w		= "";
		if($estado !== SYS_TODAS AND $estado !== false){
			$estado	= setNoMenorQueCero($estado);
			$w	= " AND (`tesoreria_cajas`.`estatus` = '$estado') ";
		}
		return $w;
	}
	function TesoreriaCajasPorCajero($usuario = false){
		$w			= "";
		$usuario	= setNoMenorQueCero($usuario);
		
		if($usuario > 0){
			$w	= " AND (`tesoreria_cajas`.`idusuario` = $usuario) ";
		}
		return $w;
	}
	function TesoreriaOperacionesPorCajero($cajero = false){
		$cajero	= setNoMenorQueCero($cajero);
		$w		= "";
		if($cajero > 0){
			$w	= " AND (`tesoreria_cajas_movimientos`.`idusuario` = '$cajero') ";
		}
		return $w;
	}	
	function TesoreriaOperacionesPorFechas($FechaInicial = false, $FechaFinal = false){
		$w		= "";
		if($FechaFinal !== false OR $FechaInicial !== false){
			$xF	= new cFecha();
			$FechaInicial	= $xF->getFechaISO($FechaInicial);
			if($FechaFinal == false){
				$w	= " AND (`tesoreria_cajas_movimientos`.`fecha` = '$FechaInicial') ";
			} else {
				$w	= " AND (`tesoreria_cajas_movimientos`.`fecha` >= '$FechaInicial') AND (`tesoreria_cajas_movimientos`.`fecha` <= '$FechaFinal') ";
			}
				
		}
		return $w;
	}
	function TesoreriaCajasPorFechas($FechaInicial = false, $FechaFinal = false){
		$w		= "";
		if($FechaFinal !== false OR $FechaInicial !== false){
			$xF	= new cFecha();
			$FechaInicial	= $xF->getFechaISO($FechaInicial);
			if($FechaFinal == false){
				$w	= " AND (`tesoreria_cajas`.`fecha_inicio` = '$FechaInicial') ";
			} else {
				$w	= " AND (`tesoreria_cajas`.`fecha_inicio` >= '$FechaInicial') AND (`tesoreria_cajas`.`fecha_inicio` <= '$FechaFinal') ";
			}
			
		}
		return $w;
	}
	function CaptacionSaldosPorFechas($FechaInicial = false, $FechaFinal = false){
		$w		= "";
		if($FechaFinal !== false OR $FechaInicial !== false){
			$xF	= new cFecha();
			$FechaInicial	= $xF->getFechaISO($FechaInicial);
			if($FechaFinal == false){
				$w	= " AND (`captacion_sdpm_historico`.`fecha` = '$FechaInicial') ";
			} else {
				$w	= " AND (`captacion_sdpm_historico`.`fecha` >= '$FechaInicial') AND (`captacion_sdpm_historico`.`fecha` <= '$FechaFinal') ";
			}
			
		}
		return $w;
	}
	function EmpresasOperacionesPorFechas($FechaInicial = false, $FechaFinal=false){
		$filtro		= "";
		$xF			= new cFecha();
		if($FechaInicial !== false){
			$FechaInicial	= $xF->getFechaISO($FechaInicial);
			if($FechaFinal == false){
				$filtro	= " AND (`empresas_operaciones`.`fecha_inicial`  = '$FechaInicial') ";
			} else {
				$FechaFinal	= $xF->getFechaISO($FechaFinal);
				$filtro	= " AND (`empresas_operaciones`.`fecha_inicial`  >= '$FechaInicial') AND (`empresas_operaciones`.`fecha_inicial`  <= '$FechaFinal') ";
			}
		}
		return $filtro;
	}
	function EmpresasOperacionesPorPeriodo($periodo){
		$periodo	= setNoMenorQueCero($periodo);
		$filtro		= "";
		if($periodo > 0){
			$filtro	= "AND ( `empresas_operaciones`.`periodo_marcado` = $periodo) ";
		}
		return $filtro;
	}
	function EmpresasOperacionesPorFrecuencia($frecuencia){
		$frecuencia	= setNoMenorQueCero($frecuencia);
		$filtro		= "";
		if($frecuencia > 0){
			$filtro	= "AND ( `empresas_operaciones`.`periocidad` = $frecuencia) ";
		}
		return $filtro;
	}	
	function DomiciliosPorTipo($tipo = false){
		$tipo		= setNoMenorQueCero($tipo);
		$ByTipo		= ($tipo > 0) ? " AND (`socios_vivienda`.`tipo_domicilio` = $tipo ) " : "";
		return $ByTipo;
	}
	function DomiciliosPorStatus($status = false){
		$status		= setNoMenorQueCero($status);
		$ByTipo		= ($status > 0) ? " AND (`socios_vivienda`.`estado_actual` = $status ) " : " AND (`socios_vivienda`.`estado_actual` != 0 ) ";
		return $ByTipo;
	}

	function AMLAlertasPorTipo($tipo = false){
		$tipo		= setNoMenorQueCero($tipo);
		$ByTipo		= ($tipo > 0) ? " AND (`aml_alerts`.`tipo_de_aviso` = $tipo ) " : "";
		return $ByTipo;
	}
	function AMLRiesgosPorTipo($tipo = false){
		$tipo		= setNoMenorQueCero($tipo);
		$ByTipo		= ($tipo > 0) ? " AND (`aml_risk_catalog`.`tipo_de_riesgo` = $tipo ) " : "";
		return $ByTipo;
	}
	function AMLAlertasPorPersona($persona = false){
		$persona	= setNoMenorQueCero($persona);
		$ByTipo		= ($persona > 0) ? " AND (`aml_alerts`.`persona_de_origen`= $persona)" : "";
		return $ByTipo;
	}
	function AMLAlertasPorFechasR($FechaInicial = false, $FechaFinal = false){
		//$sql	.= ($FechaInicial == false) ? "" : " AND getFechaByInt(`aml_alerts`.`fecha_de_registro`) >= '$FechaInicial' ";
		//$sql	.= ($FechaFinal == false) ? "" : " AND getFechaByInt(`aml_alerts`.`fecha_de_registro`) <= '$FechaFinal' ";
		$w		= "";
		if($FechaFinal !== false OR $FechaInicial !== false){
			$xF	= new cFecha();
			$FechaInicial	= $xF->getFechaISO($FechaInicial);
			if($FechaFinal == false){
				$w	= " AND (getFechaByInt(`aml_alerts`.`fecha_de_registro`) = '$FechaInicial') ";
			} else {
				$w	= " AND (getFechaByInt(`aml_alerts`.`fecha_de_registro`) >= '$FechaInicial') AND ( getFechaByInt(`aml_alerts`.`fecha_de_registro`) <= '$FechaFinal') ";
			}
				
		}
		return $w;
	}
	function LeasignSolicitaPorOriginador($originador = false){
		$originador = setNoMenorQueCero($originador);
		$By			= ($originador> 0) ? " AND ( `originacion_leasing`.`originador` = $originador )" : "";
		return $By;
	}
	function LeasignSolicitaPorSubOriginador($suboriginador = false){
		$suboriginador = setNoMenorQueCero($suboriginador);
		$By			= ($suboriginador> 0) ? " AND ( `originacion_leasing`.`suboriginador` = $suboriginador )" : "";
		return $By;
	}
	function LeasignUsuariosPorOriginador($originador = false){
		$originador = setNoMenorQueCero($originador);
		$By			= ($originador> 0) ? " AND ( `leasing_usuarios`.`originador` = $originador )" : "";
		return $By;
	}
	function LeasignUsuariosPorClave($originador = false){
		$originador = setNoMenorQueCero($originador);
		$By			= ($originador> 0) ? " AND ( `leasing_usuarios`.`idleasing_usuarios` = $originador )" : "";
		return $By;
	}
	
	function PeriodosEmpresaPorEmpresa($empresa = false){
		$empresa	= setNoMenorQueCero($empresa);
		$By			= "";
		if($empresa > 0 AND $empresa !== FALLBACK_CLAVE_EMPRESA AND $empresa !== DEFAULT_EMPRESA){
			$By		= " AND (`empresas_operaciones`.`clave_de_empresa` ='$empresa') ";
		}
		return $By;
	}
	function PeriodosEmpresaPorFecha($FechaInicial, $FechaFinal=false){
		$By				= "";
		$xF				= new cFecha();
		
		if($FechaInicial === false){
				
		} else {
			$FechaInicial	= $xF->getFechaISO($FechaInicial);
			if($FechaFinal == false){
				$By	= " AND (`empresas_operaciones`.`fecha_de_operacion`  = '$FechaInicial')";
			} else {
				$FechaFinal	= $xF->getFechaISO($FechaFinal);
				$By	= " AND (`empresas_operaciones`.`fecha_de_operacion`  >= '$FechaInicial') AND (`empresas_operaciones`.`fecha_de_operacion`  <= '$FechaFinal') ";
			}
		}			
		return $By;
	}
	function PeriodosEmpresaPorFechaCobro($FechaInicial, $FechaFinal=false){
		$By				= "";
		$xF				= new cFecha();
		if($FechaInicial === false){
			
		} else {
			$FechaInicial	= $xF->getFechaISO($FechaInicial);
			if($FechaFinal == false){
				$By	= " AND (`empresas_operaciones`.`fecha_de_operacion`  = '$FechaInicial')";
			} else {
				$FechaFinal	= $xF->getFechaISO($FechaFinal);
				$By	= " AND (`empresas_operaciones`.`fecha_de_cobro`  >= '$FechaInicial') AND (`empresas_operaciones`.`fecha_de_cobro`  <= '$FechaFinal') ";
			}
		}
		return $By;
	
	}
	function PeriodosEmpresaPorFechaEnvio($FechaInicial, $FechaFinal=false){
		$By				= "";
		$xF				= new cFecha();
		if($FechaInicial === false){
			
		} else {
			$FechaInicial	= $xF->getFechaISO($FechaInicial);
			if($FechaFinal === false){
				$By	= " AND (`empresas_operaciones`.`fecha_de_operacion`  = '$FechaInicial')";
			} else {
				$FechaFinal	= $xF->getFechaISO($FechaFinal);
				$By	= " AND (`empresas_operaciones`.`fecha_de_operacion`  >= '$FechaInicial') AND (`empresas_operaciones`.`fecha_de_operacion`  <= '$FechaFinal') ";
			}
		}			
		return $By;
	}

	function LeasignSolicitaPorPersona($persona = false){
		$persona = setNoMenorQueCero($persona);
		$By			= ($persona> 0) ? " AND ( `originacion_leasing`.`persona` = $persona )" : "";
		return $By;
	}
	function CredsGarantiasPorFechaRec($FechaInicial, $FechaFinal=false){
		$By				= "";
		$xF				= new cFecha();
		
		if($FechaInicial === false){
			
		} else {
			$FechaInicial	= $xF->getFechaISO($FechaInicial);
			if($FechaFinal === false){
				$By	= " AND (`creditos_garantias`.`fecha_recibo`  = '$FechaInicial')";
			} else {
				$FechaFinal	= $xF->getFechaISO($FechaFinal);
				$By	= " AND (`creditos_garantias`.`fecha_recibo`  >= '$FechaInicial') AND (`creditos_garantias`.`fecha_recibo`  <= '$FechaFinal') ";
			}
		}
		return $By;
	}
	function CredsGarantiasPorFechaDev($FechaInicial, $FechaFinal=false){
		$By				= "";
		$xF				= new cFecha();
		
		if($FechaInicial === false){
			
		} else {
			$FechaInicial	= $xF->getFechaISO($FechaInicial);
			if($FechaFinal === false){
				$By	= " AND (`creditos_garantias`.`fecha_devolucion`  = '$FechaInicial')";
			} else {
				$FechaFinal	= $xF->getFechaISO($FechaFinal);
				$By	= " AND (`creditos_garantias`.`fecha_devolucion`  >= '$FechaInicial') AND (`creditos_garantias`.`fecha_devolucion`  <= '$FechaFinal') ";
			}
		}
		return $By;
	}
	function CredsGarantiasPorFechaRes($FechaInicial, $FechaFinal=false){
		$By				= "";
		$xF				= new cFecha();
		
		if($FechaInicial === false){
			
		} else {
			$FechaInicial	= $xF->getFechaISO($FechaInicial);
			if($FechaFinal === false){
				$By	= " AND (`creditos_garantias`.`fecha_resguardo`  = '$FechaInicial')";
			} else {
				$FechaFinal	= $xF->getFechaISO($FechaFinal);
				$By	= " AND (`creditos_garantias`.`fecha_resguardo`  >= '$FechaInicial') AND (`creditos_garantias`.`fecha_resguardo`  <= '$FechaFinal') ";
			}
		}
		return $By;
	}
	function CredsPagosEspPorCredito($credito = false){
		$filtro		= "";
		$credito	= setNoMenorQueCero($credito);
		if($credito > DEFAULT_CREDITO){
			$filtro	= "	AND	(`creditos_pagos_esp`.`credito` = $credito)";
		}
		return $filtro;
	}
}
class cCreditosTiposDeAutorizacion {
	private $mClave			= false;
	private $mObj			= null;
	private $mInit			= false;
	private $mNombre		= "";
	private $mMessages		= "";
	private $mIDCache		= "";
	private $mTabla			= "creditos_tipo_de_autorizacion";
	public $TA_NORMAL		= 1;
	public $TA_RENOVADO		= 3;
	public $TA_REESTRUCT	= 4;
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= $this->mTabla . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache			= new cCache();
		$xT				= new cCreditos_tipo_de_autorizacion();
		if(!is_array($data)){
			$data		= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $xT->get() . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if( isset($data[$xT->getKey()]) ){
			$xT->setData($data);
			$this->mObj		= $xT;
			$this->mClave	= $xT->idcreditos_tipo_de_autorizacion()->v();
			$this->mNombre	= $xT->descripcion_tipo_de_autorizacion()->v();
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function setCuandoSeActualiza(){
		$this->setCleanCache();
	}
	function add(){}

}
class cCreditosTipos {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mMessages	= "";
	private $mNombre	= "";
	private $mTabla		= "creditos_modalidades";
	
	function __construct($clave = false){
		$this->mClave	= setNoMenorQueCero($clave);
	}
	function init($data = false){
		$idcx		= $this->mTabla . "-" . $this->mClave;
		$xCache		= new cCache();
		if(!is_array($data)){
			$data	= $xCache->get($idcx);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `creditos_modalidades` WHERE `idcreditos_modalidades`=". $this->mClave . " LIMIT 0,1");
			}
		}
		
		
		if(isset($data["idcreditos_modalidades"])){
			$this->mObj		= new cCreditos_modalidades(); //Cambiar
			$this->mObj->setData($data);
			$this->mNombre	= $this->mObj->descripcion_modalidades()->v();
			$this->mInit	= true;
			$xCache->set($idcx, $data);
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){
		$this->mObj			= null;
		$this->mMessages	= "";
	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}	
}
class cCreditosEstados {
	private $mClave			= false;
	private $mObj			= null;
	private $mInit			= false;
	private $mNombre		= "";
	private $mMessages		= "";
	private $mIDCache		= "";
	private $mTabla			= "creditos_estatus";
	private $mRespetaPlan	= true;

	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= $this->mTabla . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cCreditos_estatus();//Tabla
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $xT->get() . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj			= $xT; //Cambiar
			$this->mClave		= $xT->idcreditos_estatus()->v();
			$this->mNombre		= $xT->descripcion_estatus()->v();
			$this->mRespetaPlan	= ($xT->respetar_plan_de_pagos()->v() == SYS_UNO) ? true : false;
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function setCuandoSeActualiza(){
		$this->setCleanCache();
	}
	function getRespetarPlan(){ return $this->mRespetaPlan; }
	function add(){}

}

class cCreditosEstadisticas {
	private $mCredito	= false;
	function __construct($credito = false){
		$this->mCredito	= setNoMenorQueCero($credito);
	}
	function getNumeroCreditosPorAutorizar(){
		$xL		= new cSQLListas();
		$sql	= $xL->getListaDeCreditosEnProceso(EACP_PER_SOLICITUDES, CREDITO_ESTADO_SOLICITADO, true, false, false);
		$xQL	= new MQL();
		$rs		= $xQL->getDataRecord($sql);
		$rs		= null;
		return $xQL->getNumberOfRows();		
	}
	function getNumeroCreditosPorMinistrar(){
		$xL		= new cSQLListas();
		$sql	= $xL->getListaDeCreditosEnProceso(EACP_PER_SOLICITUDES, CREDITO_ESTADO_AUTORIZADO, true, false, false);
		$xQL	= new MQL();
		$rs		= $xQL->getDataRecord($sql);
		$rs		= null;
		return $xQL->getNumberOfRows();	
	}
	function getNumeroClientesConCredito(){
		$idx		= "creditos.e.c.ccredito";
		$xCache		= new cCache();
		$clientes	= $xCache->get($idx);
		if($clientes === null){
			$xQL		= new MQL();
			$sql 		= "SELECT COUNT(*) AS 'clientes' FROM `tmp_personas_estadisticas` WHERE `creditos_con_saldo`>0 ";
			$DRow		= $xQL->getDataRow($sql);
			$clientes	= (isset($DRow["clientes"])) ? $DRow["clientes"] : 0;
			$xCache->set($idx, $clientes);
		}
		return $clientes;
	}
	function getNumeroDeAvales($credito){

	}
	function getNumeroDeFirmantes($credito){
		$sql	= "SELECT COUNT(`idcreditos_firmantes`) AS `numero` FROM `creditos_firmantes` WHERE `credito`=$credito";
		$xQL	= new MQL();
		$items	= $xQL->getDataValue($sql, "numero");
		$xQL	= null;
		return $items;
	}
	function getNumeroPagosEspeciales($credito = false){
		$xPagEsp	= new cCreditosPlanPagoEsp();
		return $xPagEsp->getCountByCredito($credito);
	}
}



class cCreditosProyecciones {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	public $PROY_SISTEMA	= 1;
	private $mCapital		= 0;
	private $mInteres		= 0;
	private $mIVA			= 0;
	private $mOtros			= 0;
	private $mTotal			= 0;
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "entidad_creditos_proyecciones" . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xQL	= new MQL();
		$data	= (is_array($data)) ? $data : $xQL->getDataRow("SELECT * FROM `entidad_creditos_proyecciones` WHERE `identidad_proyeccion`=". $this->mClave);
		if(isset($data["identidad_proyeccion"])){
			$this->mObj		= new cEntidad_creditos_proyecciones(); //Cambiar
			$this->mObj->setData($data);
			//$this->mObj->identidad_proyeccion()
			$this->mCapital	= $this->mObj->capital()->v();
			$this->mInteres	= $this->mObj->interes()->v();
			$this->mOtros	= $this->mObj->otros()->v();
			$this->mIVA		= $this->mObj->iva()->v();
			$this->mTotal	= $this->mObj->total()->v();
			
			$this->mInit	= true;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getCapital(){ return $this->mCapital;}
	function getInteres(){ return $this->mInteres; }
	function getIVA(){ return $this->mIVA; }
	function getOtros(){ return $this->mOtros; }
	function getTotal(){ return $this->mTotal; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){
		$this->mObj			= null;
		$this->mMessages	= "";
	}
	function getProyeccionMensual($fecha, $tipo, $sucursal = ""){
		$idx		= "proyeccion.mes.$fecha.$tipo.$sucursal";
		$xCache		= new cCache();
		$data		= $xCache->get($idx);
		
		if(!is_array($data)){
			$xF				= new cFecha();
			$FechaInicio	= $xF->getDiaInicial($fecha);
			$FechaFin		= $xF->getDiaFinal($fecha);
			$periocidad		= CREDITO_TIPO_PERIOCIDAD_MENSUAL;
			$xQL			= new MQL();
			$data			= $xQL->getDataRow("SELECT * FROM `entidad_creditos_proyecciones` WHERE `fecha_inicial`='$FechaInicio' AND `fecha_final`='$FechaFin' AND `tipo`=$tipo AND `sucursal`='$sucursal' LIMIT 0,1 ");
			$xCache->set($idx, $data, $xCache->EXPIRA_UNDIA);
		}
		return $this->init($data);
	}	
	function addProyeccionMensual($fecha, $tipo, $sucursal = ""){
		$xF	= new cFecha();
		$FechaInicio	= $xF->getDiaInicial($fecha);
		$FechaFin		= $xF->getDiaFinal($fecha);
		$periocidad		= CREDITO_TIPO_PERIOCIDAD_MENSUAL;
		$this->addProyeccionPorFechas($FechaInicio, $FechaFin, $periocidad, $tipo, $sucursal);
	}
	private function addProyeccionPorFechas($FechaInicial, $FechaFinal, $PeriocidadPro, $tipo = SYS_UNO, $sucursal = ""){
		$xQL		= new MQL();
		$sucursal	= ($sucursal == "") ? SYS_TODAS : $sucursal;
		$xF			= new cFecha();
		
		$sql	= "SELECT
			COUNT(`tmp_creds_prox_letras`.`periodo_socio`) AS `cobros`,
			`tmp_creds_prox_letras`.`fecha_de_pago`        AS `fecha`,
			SUM(`tmp_creds_prox_letras`.`capital`)         AS `capital`,
			SUM(`tmp_creds_prox_letras`.`interes`)         AS `interes`,
			SUM(`tmp_creds_prox_letras`.`iva`)             AS `iva`,
			SUM(`tmp_creds_prox_letras`.`ahorro`)          AS `ahorro`,
			SUM(`tmp_creds_prox_letras`.`otros`)           AS `otros`,
			SUM(`tmp_creds_prox_letras`.`letra`)           AS `total` 
		FROM
			`tmp_creds_prox_letras` `tmp_creds_prox_letras` 
		WHERE
			(`tmp_creds_prox_letras`.`fecha_de_pago` >='$FechaInicial' AND
			`tmp_creds_prox_letras`.`fecha_de_pago`<='$FechaFinal')
		UNION
		SELECT
			COUNT(`creditos_solicitud`.`numero_solicitud`)        AS `cobros`,
			SUM(`creditos_solicitud`.`fecha_vencimiento`)        AS `fecha`,
			SUM(`creditos_solicitud`.`saldo_actual`)             AS `capital`,
			SUM(`creditos_solicitud`.`interes_normal_devengado`) AS `interes`,
			SUM(`creditos_solicitud`.`iva_interes`) +  SUM(`creditos_solicitud`.`iva_otros`) AS `iva`,
			0 AS `ahorro` ,
			SUM(`creditos_solicitud`.`gastoscbza`)               AS `otros` ,
			SUM(`creditos_solicitud`.`saldo_actual`)+SUM(`creditos_solicitud`.`interes_normal_devengado`)+SUM(`creditos_solicitud`.`iva_interes`) +  SUM(`creditos_solicitud`.`iva_otros`)+SUM(`creditos_solicitud`.`gastoscbza`) AS `total`
		FROM
			`creditos_solicitud` `creditos_solicitud` 
				INNER JOIN `creditos_a_final_de_plazo` `creditos_a_final_de_plazo` 
				ON `creditos_solicitud`.`numero_solicitud` = `creditos_a_final_de_plazo`
				.`credito` 
		WHERE
			(`creditos_solicitud`.`fecha_vencimiento` >='$FechaInicial') AND (`creditos_solicitud`.`fecha_vencimiento` <='$FechaFinal') AND `creditos_solicitud`.`saldo_actual`>0";
		$rs	= $xQL->getDataRecord($sql);
		$capital	= 0;
		$interes	= 0;
		$iva		= 0;
		$ahorro		= 0;
		$otros		= 0;
		$total		= 0;
		foreach ($rs as $rw){
			$capital 	+= $rw["capital"];
			$interes	+= $rw["interes"];
			$iva		+= $rw["iva"];
			$otros	 	+= $rw["otros"];
			$total		+= $rw["total"];
		}
		$xPro			= new cEntidad_creditos_proyecciones();
		$xPro->identidad_proyeccion("NULL");
		$xPro->fecha_final($FechaFinal);
		$xPro->fecha_inicial($FechaInicial);
		$xPro->ahorros($ahorro);
		$xPro->capital($capital);
		$xPro->interes($interes);
		$xPro->idusuario(getUsuarioActual());
		$xPro->periocidad($PeriocidadPro);
		$xPro->sucursal($sucursal);
		$xPro->tipo($tipo);
		$xPro->otros($otros);
		$xPro->total($total);
		$xPro->query()->insert()->save();
	} 
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function add(){}
}

class cCreditosProceso {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	public $PASO_REGISTRADO 	= 1;
	public $PASO_ATENDIDO 		= 100;
	public $PASO_CON_OFICIAL 	= 101;
	public $PASO_CON_PERSONA 	= 102;
	public $PASO_CON_CREDITO 	= 103;
	public $PASO_SOLICITADO		= 99;
	public $PASO_AUTORIZADO		= 98;
	
	public $PASO_VIGENTE		= 10;
	public $PASO_ADESEMBOLSO	= 501;
	
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= TOPERACIONES_TIPOS . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xQL	= new MQL();
		$data	= (is_array($data)) ? $data : $xQL->getDataRow("SELECT * FROM `creditos_destino_detallado` WHERE `idcreditos_destino_detallado`=". $this->mClave);
		if(isset($data["idcreditos_destino_detallado"])){
			$this->mObj		= new cCreditos_destino_detallado(); //Cambiar
			$this->mObj->setData($data);
			$this->mInit	= true;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){
		$this->mObj			= null;
		$this->mMessages	= "";
	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function add(){}

}
class cCreditosPreclientes {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTable		= "";
	public $ORIGEN_PAGE	= 1;
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "creditos_preclientes" . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cCreditos_preclientes();
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $xT->get() . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj		= $xT; //Cambiar
			$this->mClave	= $data[$xT->getKey()];
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNHORA);
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function setPersona($idpersona){
		$idpersona 	= setNoMenorQueCero($idpersona);
		if($idpersona>DEFAULT_SOCIO){
			$xSoc	= new cSocio($idpersona);
			if($xSoc->init() == true){
				
				$obj	= $this->getObj();
				$obj->nombres($xSoc->getNombre());
				$obj->apellido1($xSoc->getApellidoPaterno());
				$obj->apellido2($xSoc->getApellidoMaterno());
				$obj->rfc($xSoc->getRFC());
				$obj->curp($xSoc->getCURP());
				$obj->idpersona($idpersona);
				$res	= $obj->query()->update()->save($this->mClave);
				$obj	= null;
				$this->setCleanCache();
			}
		}
		
	}
	function setCredito($idcredito){
		$idcredito 	= setNoMenorQueCero($idcredito);
		$xQL		= new MQL();
		$xQL->setRawQuery("UPDATE `creditos_preclientes` SET `idcredito`=$idcredito WHERE `idcontrol`=" . $this->mClave);
		$xQL		= null;	
		$this->setCleanCache();
	}
	function setInactivo($credito = false){
		$credito	= setNoMenorQueCero($credito);
		$obj		= $this->getObj();
		if($obj !== null){
			$obj->idestado("0");
			if($credito > DEFAULT_CREDITO){
				$obj->idcredito($credito);
			}
			$res	= $obj->query()->update()->save($this->mClave);
			$obj	= null;
			$this->setCleanCache();
		}
	}
	function add($apellido1, $apellido2, $nombre,$telefono, $email, $pagos, $frecuencia, $monto, $origen = 1){
		
		$xT		= new cCreditos_preclientes();
		$xT->idcontrol("NULL");
		$xT->apellido1($apellido1);
		$xT->apellido2($apellido2);
		$xT->aplicacion(FALLBACK_CRED_TIPO_DESTINO);
		$xT->curp("");
		$xT->fecha_de_registro(fechasys());
		$xT->idcredito(0);
		$xT->idpersona(0);
		$xT->monto($monto);
		$xT->nombres($nombre);
		$xT->pagos($pagos);
		$xT->periocidad($frecuencia);
		$xT->producto(DEFAULT_TIPO_CONVENIO);
		$xT->rfc("");
		$xT->telefono($telefono);
		$xT->email($email);
		$xT->idestado(SYS_UNO);//Activo
		$xT->idorigen($origen);
		$res 	= $xT->query()->insert()->save();
		$id		= 0;
		if($res === false){
			$this->mMessages .= "Faltan Datos para registrar la solicitud\r\n";
		} else {
			$this->mMessages .= "Se registro la Solicitud con exito\r\n";
			$id	= $res;
		}
		return $id;
	}

}


class cCreditosNotasSIC {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTabla		= "creditos_sic_notas";
	private $mTipo		= 0;
	private $mTexto		= "";
	private $mEstadoForzado	= 0;
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= $this->mTabla . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cCreditos_sic_notas();//Tabla
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $this->mTabla . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj				= $xT; //Cambiar
			$this->mClave			= $data[$xT->getKey()];
			$this->mTipo			= $xT->clave_nota()->v();
			$this->mTexto			= $xT->texto_nota()->v();
			$this->mEstadoForzado 	= $xT->estatus()->v();
			
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function initByCredito($credito){
		$credito	= setNoMenorQueCero($credito);
		$xQL	= new MQL();
		$data	= $xQL->getDataRow("SELECT * FROM `creditos_sic_notas` WHERE `credito`=$credito LIMIT 0,1");
		return $this->init($data);
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function add(){}
	function getEstadoForzado(){ return $this->mEstadoForzado;	}
	function getClaveDeNota(){ return $this->mTipo; }
}



class cCreditosProductosFormatos {
	private $mClave			= false;
	private $mObj			= null;
	private $mInit			= false;
	private $mNombre		= "";
	private $mMessages		= "";
	private $mIDCache		= "";
	private $mTabla			= "creditos_prods_formatos";
	private $mTipo			= 0;
	private $mEtapaId		= 0;
	private $mUsuario		= 0;
	private $mFecha			= false;
	private $mTiempo		= 0;
	private $mFormatoId		= 0;
	private $mTexto			= "";
	private $mObservacion	= "";
	private $mOFormato		= null;
	private $mConteoByProd	= 0;
	private $mRuta			= "";
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	private function getIDCache(){ return $this->mIDCache; }
	private function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= $this->mTabla . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache		= new cCache();
		$inCache	= true;
		$xT			= new cCreditos_prods_formatos();//Tabla
		
		
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL		= new MQL();
				$data		= $xQL->getDataRow("SELECT * FROM `" . $this->mTabla . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
				$inCache	= false;
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			//$data[$xT->];//
			$this->mClave		= $data[$xT->getKey()];
			$this->mEtapaId		= $data[$xT->ETAPA_ID];
			$this->mFormatoId	= $data[$xT->FORMATO_ID];
			
			$xFmt				= new cFormatosDelSistema($this->mFormatoId);
			if($xFmt->init() == true){
				$this->mRuta	= $xFmt->getRuta();
				$this->mNombre	= $xFmt->getNombre();
			}
			$this->mObj		= $xT;
			$this->setIDCache($this->mClave);
			if($inCache == false){	//Si es Cache no se Guarda en Cache
				$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			}
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre; }
	function getClave(){return $this->mClave; }
	function getTipo(){ return $this->mTipo; }
	function getFormatoId(){ return $this->mFormatoId; }
	function getEtapaId(){ return $this->mEtapaId; }
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function add($etapaId, $formatoId, $productoId, $opcional = false){
		$xT			= new cCreditos_prods_formatos();
		$xT->idcreditos_prods_contratos("NULL");
		$xT->etapa_id($etapaId);
		$xT->estatus(SYS_UNO);
		$xT->formato_id($formatoId);
		if($opcional == false){
			$xT->opcional(SYS_CERO);
		} else {
			$xT->opcional(SYS_UNO);
		}
		$xT->producto_credito_id($productoId);
		
		$res	= $xT->query()->insert()->save();
		
		return ($res === false) ? false : true;
	}
	function getListByProductoCreditoId($producto){
		$xQL	= new MQL();
		$rs		= $xQL->getDataRecord("SELECT * FROM `creditos_prods_formatos` WHERE `estatus`=1 AND `producto_credito_id`=$producto");
		$this->mConteoByProd = $xQL->getNumberOfRows();
		return $rs;
	}
	function getConteoByProductoCredito(){ return $this->mConteoByProd; }
	function getRuta(){ return $this->mRuta; }
	function getRutaCredito($credito){
		$ruta		= $this->mRuta;
		if(strpos($ruta, "?") === false){
			$ruta		= $ruta . "?";
		}
		return $ruta . "&forma=" . $this->mFormatoId . "&credito=" . $credito   . "&solicitud=" . $credito; 
	}
	function getRutaCreditoPDF($credito){ return $this->getRutaCredito($credito) . "&out=" . OUT_PDF; }
	function getRutaCreditoDOC($credito){ return $this->getRutaCredito($credito) . "&out=" . OUT_DOC; }
}
?>