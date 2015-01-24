<?php
/**
 * Core Captacion File
 * @author Balam Gonzalez Luis Humberto
 * @package captacion
 * @subpackage core
 * @version 1.2.35
 */

include_once("core.deprecated.inc.php");
include_once("entidad.datos.php");
include_once("core.config.inc.php");
include_once("core.contable.inc.php");
include_once("core.operaciones.inc.php");
include_once("core.common.inc.php");
include_once("core.html.inc.php");
include_once("core.security.inc.php");
include_once("core.fechas.inc.php");
include_once("core.captacion.inc.php");
@include_once("../libs/sql.inc.php");

/**
 * Utilerias de Captacion
 *
 */
class cUtileriasParaCaptacion {
	private $mMessages		= "";
	
	function __construct(){

	}
	/**
	 * Genera Saldos diarios de las Cuentas de Captacion
	 * @deprecated	1.9.24
	 * @param date $fecha
	 * @return string
	 */
	function setSaldosDiarios($fecha = false){
		return "";
	}
	function setGenerarIDE_mensual($recibo, $fecha_de_corte = false){
		if ($fecha_de_corte == false){
			$fecha_de_corte	= fechasys();
		}
		$dia_inicial	= date("Y-m", strtotime($fecha_de_corte)) . "-01";
		$dia_final		= date("Y-m-t", strtotime($fecha_de_corte));
		$BySucursal		= "";//" AND (`operaciones_recibos`.`sucursal` = '" . getSucursal() . "') ";

		$msg  = "=============\t\tGENERANDO EL IMPUESTO SOBRE DEPOSITOS EN EFECTIVO\r\n";
		//general el Archivo de IDE pagado
	//Impuesto sobre Depositos en Efectivo
		$sql = "SELECT
						`operaciones_mvtos`.`socio_afectado`,
						`operaciones_recibos`.`tipo_pago`,
						`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
						SUM(`operaciones_mvtos`.`afectacion_real`) AS 'monto'
					FROM
						`operaciones_mvtos` `operaciones_mvtos`
							INNER JOIN `operaciones_recibos` `operaciones_recibos`
							ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.
							`idoperaciones_recibos`
								INNER JOIN `eacp_config_bases_de_integracion_miembros`
								`eacp_config_bases_de_integracion_miembros`
								ON `operaciones_mvtos`.`tipo_operacion` =
								`eacp_config_bases_de_integracion_miembros`.`miembro`
				WHERE
					(`operaciones_mvtos`.`fecha_afectacion` >='$dia_inicial')
					AND
					(`operaciones_mvtos`.`fecha_afectacion` <='$dia_final')
					AND
					(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 2600)
					AND
					(`operaciones_recibos`.`tipo_pago` = 'efectivo' )
					$BySucursal
					GROUP BY
					`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
					`operaciones_mvtos`.`socio_afectado`,
					`operaciones_recibos`.`tipo_pago`
				HAVING
					monto > " . EXCENCION_IDE;
		$cRec	= new cReciboDeOperacion(12, false, $recibo);
		$cRec->setNumeroDeRecibo($recibo, true);
			$rsIDE	= mysql_query($sql, cnnGeneral());
			while($rwIDE = mysql_fetch_array($rsIDE)){

				$socio		= $rwIDE["socio_afectado"];
				$monto		= $rwIDE["monto"];

				if ($monto > EXCENCION_IDE){
					//TODO: Analize this line
					$SqlMax		= "
								SELECT
										`captacion_cuentas`.*,
										`captacion_cuentastipos`.`descripcion_cuentastipos` AS `tipo`,
										`captacion_subproductos`.`descripcion_subproductos` AS `subproducto`,
										`captacion_subproductos`.`algoritmo_de_premio`,
										`captacion_subproductos`.`algoritmo_de_tasa_incremental`,
										`captacion_subproductos`.`metodo_de_abono_de_interes`,
										`captacion_subproductos`.`destino_del_interes`
												FROM
													`captacion_cuentas` `captacion_cuentas`
														INNER JOIN `captacion_cuentastipos` `captacion_cuentastipos`
														ON `captacion_cuentas`.`tipo_cuenta` = `captacion_cuentastipos`.
														`idcaptacion_cuentastipos`
															INNER JOIN `captacion_subproductos` `captacion_subproductos`
															ON `captacion_cuentas`.`tipo_subproducto` = `captacion_subproductos`
															.`idcaptacion_subproductos`
										WHERE
										numero_socio=$socio
											AND `captacion_cuentas`.tipo_cuenta = 10
										ORDER BY saldo_cuenta DESC
													LIMIT 0,1";
					$DCtaMax	= obten_filas($SqlMax);

					$CuentaMax	= $DCtaMax["numero_cuenta"];
					$saldo		= $DCtaMax["saldo_cuenta"];
					if ( isset($CuentaMax) AND ($saldo > TOLERANCIA_SALDOS) ){
						$cCta		= new cCuentaALaVista($CuentaMax);
						$cCta->initCuentaByCodigo($DCtaMax);
						$ide		= $cCta->getMontoIDE($fecha_de_corte);
						if ($ide > 0){

								//Si el Saldo de la Cuenta es Mayor al IDE
								if ( ($saldo >= $ide) ){
									$ReciboIDE 	= $cCta->setRetenerIDE($fecha_de_corte, $recibo, $ide);
								} else {
								//Si no el IDE es igual al Saldo
									$ide_por_pagar	= ($ide - $saldo) * -1;
									$ide 			= $saldo;
									$ReciboIDE 		= $cCta->setRetenerIDE($fecha_de_corte, $recibo, $ide);
									//OK: agregar movimiento ide por pagar
									if ( $ide_por_pagar > 0 ){
										$cRec->setNuevoMvto($fecha_de_corte, $ide_por_pagar, 236, 1, "IDE no Retenido del $dia_inicial al $dia_final", 1, TM_CARGO,
													$socio, $CuentaMax);
									}
								}
								$msg	.= $cCta->getMessages("txt");
						} else {
							$msg	.= "$socio\t$CuentaMax\tNO_PIDE\tNo retuvo IDE porque el Monto a Retener es $ide\r\n";
						}
					} else {
						//OK: agregar movimiento ide por pagar
						$msg	.= "$socio\t$CuentaMax\tNO_CTA\tNo retuvo IDE por que no existe una cuenta valida($saldo) para descontar, Base de $monto\r\n";
					}

				} else {
					$msg	.= "$socio\t$CuentaMax\tNO_IDE\tNo retuvo IDE por que a Base de Calculo es $monto\r\n";
				}
			}

		return $msg;
	}
	function setGenerarInteresSobreSDPM($recibo = false, $fecha_de_corte = false){
		$sucursal	= getSucursal();
		$xQL		= new MQL();
		$xErC		= new cErrorCodes();
		$xLog		= new cCoreLog();
		$xDTb		= new cSAFETabla(TCAPTACION_CUENTAS);
		//(`captacion_cuentas`.`sucursal` ='$sucursal') AND
		$txtlog		= "";
		$txtlog		.= "==========================================================================================\r\n";
		$txtlog		.= "==========\tCALCULO DE INTERESES SOBRE SDPM DE CUENTAS A LA VISTA EN LA SUCURSAL $sucursal\r\n";
		$txtlog		.= "==========\tRECIBO DE CALCULO $recibo\r\n";
		$txtlog		.= "==========================================================================================\r\n";
		$xF0					= new cFecha(0, $fecha_de_corte);
		$fecha_de_corte			= ( $fecha_de_corte == false ) ? fechasys() : $fecha_de_corte;
		
		$dias_del_mes			= $xF0->getDiasDelMes();
		$fecha_de_inicio		= $xF0->getDiaInicial();
		$fecha_de_termino		= $xF0->getDiaFinal();
		$ejer					= $xF0->anno();
		$peri					= $xF0->mes();	
		//(`captacion_cuentas`.`sucursal` ='$sucursal') AND
		/**
		* Reestructurar los Dias SDPM para los Creditos que no han tenido Movimiento
		*/
		//Generando el SDPM de los Credito sin movimiento o ultimo SDPM del mes
		$SQL_LST	= $xDTb->getQueryInicial() . "
						WHERE
							
							(`captacion_cuentas`.`estatus_cuenta` ='10') AND
							(`captacion_subproductos`.`metodo_de_abono_de_interes` ='AL_FIN_DE_MES')
							AND
							(`captacion_cuentas`.`saldo_cuenta`>= " . A_LA_VISTA_MONTO_MINIMO . " )
						";
		$txtlog .= date("H:i:s") . "\t=========Cerrando las cuentas a su ultimo SDPM\r\n";
		$txtlog .= date("H:i:s") . "\tLas Cuentas con Saldo Menor a " . A_LA_VISTA_MONTO_MINIMO . " no se Incluyen\r\n";
		$rsUSDPM = $xQL->getDataRecord($SQL_LST);
			foreach ($rsUSDPM as $rwUSDPM){
				$cuenta					= $rwUSDPM["numero_cuenta"];
				$socio					= $rwUSDPM["numero_socio"];
				$fecha_ultimo_mvto 		= $rwUSDPM["fecha_afectacion"];
				$saldo_actual			= $rwUSDPM["saldo_cuenta"];
				$dias_de_sdpm			= restarfechas($fecha_de_corte, $fecha_ultimo_mvto);
				$xCta					= new cCuentaALaVista($cuenta);
				$xCta->init($rwUSDPM);
				if($dias_de_sdpm > $dias_del_mes){
					$txtlog 			.= date("H:i:s") . "\t$socio\t$cuenta\tLos dias del SDPM son de $dias_de_sdpm Mayores al del Mes $dias_del_mes, se ajustan\r\n";
					$xFMes				= new cFecha(0, $fecha_de_corte);
					$dias_de_sdpm 		= $xF0->setRestarFechas($fecha_de_corte, $xF0->getDiaInicial());
				}
				$sdpm	= $saldo_actual	* $dias_de_sdpm;
				//Obtiene la Tasa
				$tasa	= $xCta->getTasaAplicable(0,0, $saldo_actual);
				//Inserta el SDPM

				$sqlUS = "INSERT INTO captacion_sdpm_historico
						(ejercicio, periodo, cuenta, fecha, dias, tasa, monto, recibo)
	    				VALUES( $ejer, $peri, $cuenta, '$fecha_de_corte', $dias_de_sdpm, $tasa,$sdpm, $recibo)";
				my_query($sqlUS);
				//Actualiza la Cuenta
				$sqlUI	= "UPDATE captacion_cuentas
	    						SET fecha_afectacion='$fecha_de_corte', ultimo_sdpm=$sdpm
	    						WHERE numero_cuenta=$cuenta";
				my_query($sqlUI);
				$txtlog .= date("H:i:s") . "\t$socio\t$cuenta\tAgregando y Actualizando el SDPM por $sdpm del $fecha_ultimo_mvto al $fecha_de_corte por $saldo_actual\r\n";
			}
		//considerar fecha de Apertura
		//agregar al Recibo
		//TODO: Actualizar esta linea
		$SQL_Captacion	= "
						SELECT
							`captacion_cuentas`.*,
							`captacion_subproductos`.*,
				
					`captacion_cuentas`.`numero_cuenta`                 AS `cuenta`,
					`captacion_cuentas`.`fecha_afectacion`              AS `apertura`,
					`captacion_cuentas`.`inversion_fecha_vcto`          AS `vencimiento`,
					`captacion_cuentas`.`tasa_otorgada`                 AS `tasa`,
					`captacion_cuentas`.`dias_invertidos`               AS `dias`,
					`captacion_cuentas`.`observacion_cuenta`            AS `observaciones`,
					`captacion_cuentas`.`saldo_cuenta` 			        AS `saldo`,
					`captacion_subproductos`.`descripcion_subproductos` AS `subproducto`,
												
							SUM(`captacion_sdpm_historico`.`monto`) AS 'suma_sdpm'
						FROM
							`captacion_cuentas` `captacion_cuentas`
								INNER JOIN `captacion_subproductos` `captacion_subproductos`
								ON `captacion_cuentas`.`tipo_subproducto` = `captacion_subproductos`.
								`idcaptacion_subproductos`
									INNER JOIN `captacion_sdpm_historico` `captacion_sdpm_historico`
									ON `captacion_cuentas`.`numero_cuenta` = `captacion_sdpm_historico`.
									`cuenta`
						WHERE
							
							(`captacion_cuentas`.`estatus_cuenta` ='10') AND
							(`captacion_subproductos`.`metodo_de_abono_de_interes` ='AL_FIN_DE_MES')
							AND
							(`captacion_sdpm_historico`.`fecha` >='$fecha_de_inicio')
							AND
							(`captacion_sdpm_historico`.`fecha` <='$fecha_de_termino')
							AND
							(`captacion_cuentas`.`saldo_cuenta`>=" . A_LA_VISTA_MONTO_MINIMO . ")
						GROUP BY
							`captacion_sdpm_historico`.`cuenta`
							";
		setLog($SQL_Captacion);
		$rsCAP =  $xQL->getDataRecord($SQL_Captacion);
		if (!$rsCAP){
			if(MODO_DEBUG == true){
				$txtlog .= $xQL->getMessages(); //date("H:i:s") . "\tERROR (" . mysql_errno() . ") AL EJECUTAR LA CONSULTA";
			}
		} else {
			$txtlog	.= "==========================================================================================\r\n";
			//$xT1	= new cTipos();
			
			foreach ($rsCAP as $rwC){
					$cuenta					= $rwC["numero_cuenta"];
					$socio					= $rwC["numero_socio"];
					$apertura				= $rwC["fecha_apertura"];
					$sumaSDPM				= $rwC["suma_sdpm"];
					$dias_de_calc			= $dias_del_mes;
					$promedio				= $rwC["saldo_cuenta"];
					$modificadorDeInteres	= $rwC["algoritmo_modificador_del_interes"];
					$modificadorDeTasa		= $rwC["algoritmo_de_tasa_incremental"];
					$interes				= 0;
					$producto				= $rwC["tipo_subproducto"];
					//Si la Fecha de Apertura es Menor al Inicio de mes
						if(strtotime($apertura) > strtotime($fecha_de_inicio)){
							$dias_de_calc 	= restarfechas($fecha_de_termino, $apertura);
							//Corrige el TIMESTAMP de Interes, la rotacion debe ser de al menos 24 Horas
							if ($dias_de_calc == 0){
								$dias_de_calc	= 1;
							} elseif ($dias_de_calc < 0 ){
								$txtlog			.= date("H:i:s") . "\t$socio\t$cuenta\tEROR_DIAS\t SDPM $sumaSDPM, Dias $dias_de_calc\r\n";
								$dias_de_calc	= 1;
								$sumaSDPM		= $promedio;
							}
						}
					$xCta		= new cCuentaALaVista($cuenta);
					$xCta->init($rwC);
					$promedio	= round(($sumaSDPM / $dias_de_calc), 2);
					$tasa_nueva	= $xCta->getTasaAplicable(0, 0, $promedio);
					$tasa		= $tasa_nueva;
					//OK: Ejecutar Modificador de Tasa
					eval( $modificadorDeTasa);
					//setLog("$modificadorDeTasa");
					$interes	= ($sumaSDPM * $tasa) / EACP_DIAS_INTERES;
					//OK: Ejecutar Consulta de Modificador de Interes
					eval( $modificadorDeInteres );
					$interes	= round($interes, 2);
					//$txtlog		.= $modificadorDeInteres;
					
					if($interes > 0){
						$rsx 		= setNuevoMvto($socio, $cuenta, $recibo, $fecha_de_corte, $interes, 222,1, "CALCULO_AUTOMATICO" );
						if($rsx == false){
							$txtlog .= "ERROR\t$socio\t$cuenta\tInteres por $interes, tasa $tasa_nueva, Promedio $promedio, SDPM $sumaSDPM, Dias $dias_de_calc\r\n";
						} else {
							$txtlog .= "OK\t$socio\t$cuenta\tInteres por $interes, tasa $tasa_nueva, Promedio $promedio, SDPM $sumaSDPM, Dias $dias_de_calc en Rec $recibo\r\n";
						}
						$sqlUI	= "UPDATE captacion_cuentas
		    						SET saldo_cuenta = (saldo_cuenta + $interes)
		    						WHERE numero_cuenta=$cuenta AND numero_socio=$socio";
		    						$x = my_query($sqlUI);
					} else {
						$txtlog .= "WARN\t$socio\t$cuenta\tNOINT\t($interes / $tasa_nueva) Promedio $promedio, SDPM $sumaSDPM, Dias $dias_de_calc\r\n";
					}
			}
			
		}
		return $txtlog;
	}
/**
 * Genera las Inversiones Automaticas
 * @param integer	$recibo numero de recvio al que se agrega los movimientos
 * @param date		$fecha	Fecha de Inversión
 */
	function inversiones_automaticas($recibo = false, $fecha = false){
		$fecha 				= ($fecha == false) ? fechasys() : $fecha;
	 	$msg				= "=================\tINVERSIONES_AUTOMATICAS\t======================\r\n";
	  	$msg				.= "=================\tFECHA:\t$fecha\t======================\r\n";
	  	$msg				.= date("H:i:s") . "\tLas Cuentas con Saldo Minimo a " . INVERSION_MONTO_MINIMO . " se ignoran\r\n";
	  	$cierre_sucursal 	= getSucursal();
	  	$fecha_operacion	= $fecha;
		$xTb				= new cSAFETabla(TCAPTACION_CUENTAS);
		$QL					= new MQL();
		$BySucursal			= ""; //AND (`captacion_cuentas`.`sucursal` = '$cierre_sucursal') ";
		$sql_invs 			= $xTb->getQueryInicial() . "
						WHERE
						  (`captacion_cuentas`.`inversion_fecha_vcto` = '$fecha')
						  AND
						  (`captacion_cuentas`.`saldo_cuenta` >=" . INVERSION_MONTO_MINIMO . ") AND
						  (`captacion_subproductos`.`metodo_de_abono_de_interes` =\"AL_VENCIMIENTO\") $BySucursal ";
		$rs					= $QL->getDataRecord($sql_invs);
		//setLog($sql_invs);
	  foreach($rs as $rw){
	    $socio 				= $rw["numero_socio"];
	    $cuenta 			= $rw["numero_cuenta"];
	    $dias  				= $rw["dias_invertidos"];
	    $periodo  			= $rw["inversion_periodo"];
	    $tasa_anterior 		= $rw["tasa_otorgada"];
	    $subproducto		= $rw["tipo_subproducto"];
	    $saldo				= $rw["saldo_cuenta"];
		$tasa				= $tasa_anterior;
		$tasa2				= $tasa_anterior;
		$periodo			= $rw["inversion_periodo"];
		
		$cInv				= new cCuentaInversionPlazoFijo($cuenta, $socio, $dias);

		$cInv->init($rw);
		if ( $subproducto 	!=  70 ){
			$cInv->setReinversion($fecha, true);
		} else {
			$acciones			= floor( $saldo / COSTE_POR_ACCION );
			$tasa				= 0.08;
			$tasa2				= 0.09;
			//Algoritmo de inversion parcial de
			$cientos			= floor( $acciones / 100 );
			if ( $cientos >= 1 ){
				//TODO: considerar una buena revision
				/*SI: Periodo == PRIMO
				 * TONS DIAS = 180
				 * SI NO: DIAS = (365 - 180)*/

				if ( $periodo %2 == 0 ){
					//par
					$dias		= 365 - 180;
				} else {
					$dias		= 180;
				}
				$msg				.= "MAS_CIEN\tExisten $cientos CENTENAS DE ACCIONES\r\n";
				//inversiones de 100
				$IDeCien			= (COSTE_POR_ACCION * ( $cientos * 100 ) );
				//prevee que no se invierta mas de el saldo
				$IDeCien			= ( $IDeCien > $saldo ) ? $saldo : $IDeCien;
				$RInversion			= $xInv->setReinversion($fecha, true, $tasa2, $dias, true, $IDeCien);
				$msg				.= "MAS_CIEN\tLa Inversion a tasa de $tasa2 es de $IDeCien\r\n";
				//inversiones < 100
				$IMenorDeCien		= $saldo - $IDeCien;
				if ( $IMenorDeCien > 0 ){
					$RInversion2			= $xInv->setReinversion($fecha, true, $tasa, $dias, true, $IMenorDeCien);
					$msg					.= "REM_CIEN\tEl remanente a invertir a tasa de $tasa es de $IMenorDeCien\r\n";
				}
				$xInv->setUpdateInversion(true);
			}
		}

		$msg	.= $cInv->getMessages("txt");
	  }
	return $msg;
	}

	function vencer_intereses_de_inversion($recibo = false, $fecha = false){

	  //DATE_ADD(CURDATE(), INTERVAL 1 DAY)
	  //Vencer los Intereses de las Inversiones de Ma�ana
	  $fecha_programada 	= sumardias($fecha, 1);
	  $sucursal				= getSucursal();
	  $msg		= "================= VENCIMIENTO_DE_INTERESES_SOBRE_INVERSION DEL DIA $fecha_programada =========\r\n";

	  $SQL500 = "SELECT
					  `operaciones_mvtos`.*
					FROM
					  `operaciones_mvtos` `operaciones_mvtos`
					WHERE
					  (`operaciones_mvtos`.`fecha_afectacion` = '$fecha_programada')
					  AND
					  (`operaciones_mvtos`.`tipo_operacion` = 500)
					  AND
					  (`operaciones_mvtos`.`sucursal`='$sucursal')";
	  $rs = mysql_query($SQL500, cnnGeneral());
	    if(!$rs){
	      $msg	.= "LA CONSULTA NO SE EJECUTO (CODE: " . mysql_errno() . ")";
	    }
	  while($rw = mysql_fetch_array($rs)){
	    $iddocto			= $rw["docto_afectado"];
	    $idsocio			= $rw["socio_afectado"];
	    $interes			= $rw["afectacion_real"];
	    //Informacion de la Cuenta
	    $xCta				= new cCuentaInversionPlazoFijo($iddocto, $idsocio);
	    $xCta->init();
	    $infoCapt 			= $xCta->getDatosInArray();
	    $saldo				= $infoCapt["saldo_cuenta"];
	    $periodo			= $infoCapt["inversion_periodo"];
	    $dias				= $infoCapt["dias_invertidos"];
	    $destinoInteres		= $infoCapt["destino_del_interes"];
	    $cuenta_de_int		= $infoCapt["cuenta_de_intereses"];
	    $isr_a_retener		= 0;
	    //CUENTA_INTERESES
	    //Suma el Interes mas el Capital
	    switch ( $destinoInteres ){
	    	case "":
	    		$montofinal			= $saldo;
	    		$xIC				= cCuentaALaVista($idsocio, $cuenta_de_int);
	    		$xIC->init();
	    		$xIC->setDeposito($interes, "NA", "ninguno", "NA",
						"DEPOSITO_AUTOMATICO_INVERSION_CTA_$iddocto", 99,
						$fecha, $recibo);
				setPolizaProforma($recibo, 222, $interes, $idsocio, $cuenta_de_int, TM_ABONO);
				$msg	.= $xIC->getMessages();
	    		break;
	    	default:
	    		$montofinal			= $saldo + $interes;
			    //Agregar el Movimiento, 222 == depositos de Interes
				setNuevoMvto($idsocio, $iddocto, $recibo, $fecha_programada, $interes, 222, $periodo, "DEPOSITO_AUTOMATICO");
				$msg	.= date("H:i:s") . "\t$idsocio\t$iddocto\tAgregando el INTERES POR DEPOSITAR por $interes\r\n";
				setPolizaProforma($recibo, 222, $interes, $idsocio, $iddocto, TM_ABONO);
	    		break;
	    }

	      setPolizaProforma($recibo, 500, $interes, $idsocio, $iddocto, TM_CARGO);
	    /**
	     * Generar el ISR por Inversiones
	     */
	    $isr_a_retener	= getISRByInversion($saldo, $dias);
	    //
	    if ($isr_a_retener > 0){
			setNuevoMvto($idsocio, $iddocto, $recibo, $fecha_programada, $isr_a_retener, 234, $periodo, "ISR_AUTOMATICO", -1);
			$msg	.= date("H:i:s") . "\t$idsocio\t$iddocto\tAgregando el ISR por RETENER por $isr_a_retener\r\n";
	    //Agregar la Prepoliza
			setPolizaProforma($recibo, 222, $isr_a_retener, $idsocio, $iddocto, TM_CARGO);
			setPolizaProforma($recibo, 234, $isr_a_retener, $idsocio, $iddocto, TM_ABONO);
	    //Disminuir el ISR del Monto a Pagar
			$montofinal -= $isr_a_retener;
	    }
	    //Actualizar la Cuenta de Captacion
	        $sqlUCta = "UPDATE captacion_cuentas
	                SET
	                  fecha_afectacion='$fecha_programada',
	                  saldo_cuenta=$montofinal
	                WHERE numero_cuenta=$iddocto
	                  AND
	                  numero_socio=$idsocio";
	        $x = my_query($sqlUCta);
	    if ($x["stat"] == false){
	      $msg	.= $x[SYS_MSG] . "\r\n";
	    } else {
	      $msg	.= date("H:i:s") . "\t$idsocio\t$iddocto\tActualizando la Cuenta a Saldo $montofinal y Fecha Afectacion $fecha_programada \r\n";
	    }
	  } //fin de busqueda
	  $SQL_U_500 = "UPDATE operaciones_mvtos SET
			        estatus_mvto=30,
			        docto_neutralizador = $recibo
			        WHERE
			          (`operaciones_mvtos`.`fecha_afectacion` = '$fecha_programada')
			          AND
			          (`operaciones_mvtos`.`tipo_operacion` = 500)
			        AND
			        (`operaciones_mvtos`.`sucursal`='$sucursal')";
	        $x = my_query($SQL_U_500);
	    if ($x["stat"] == false){
	      $msg	.= $x[SYS_MSG] . "\r\n";
	    } else {
	      $msg	.= date("H:i:s") . "\t\t\tActualizando el INTERES POR DEPOSITAR a 'PAGADO' (" . $x["info"] . ")\r\n";
	    }

	  return $msg;
	}
	/**
	 * funcion que purga la Cuentas a la Vista Menores a Cero, llevandolas a Cero
	 * @param	boolean	$DistinctSucursal		Distinguir Sucursal?: Si/No
	 * @return	string	Mensajes del Log de Proceso
	 */
	function setCleanCuentasMenoresACero_ALaVista($DistinctSucursal = false ){
			$msg		= "============== ELIMINANDO CUENTAS CON SALDO NEGATIVO \r\n";
			$msg		.= "============== " . date("dmY h:i:s") . " \r\n";
			$BySucursal	= "";
			if ( $DistinctSucursal == true ){
				$BySucursal	= " AND (`captacion_cuentas`.`sucursal`='" . getSucursal() . "') ";
				$msg		.= "============== " . getSucursal() . " \r\n";
			}
			$cRec		= new cReciboDeOperacion(10);
			$xRec		= $cRec->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO, fechasys(), 1, 10, "RECIBO_DE_AJUSTES_DE_CAPTACION");
			$msg		.= "\t\tRECIBO\tEl Recibo de Operacion es $xRec\r\n";
			$cRec->setNumeroDeRecibo($xRec, true);
			$msg		.= $cRec->getMessages("txt");
			$contar		= 0;
		 	 $xTb		= new cSAFETabla(TCAPTACION_CUENTAS);
		 	 $sql		= $xTb->getQueryInicial() . "
										WHERE
											(
											(`captacion_cuentas`.`saldo_cuenta` <= " . TOLERANCIA_SALDOS .  ")
												AND
											(`captacion_cuentas`.`saldo_cuenta` != 0)
											)
											AND
											(`captacion_cuentas`.`tipo_cuenta` =10)
											$BySucursal
										ORDER BY
											`captacion_cuentas`.`saldo_cuenta`,
											`captacion_cuentas`.`fecha_afectacion` ";
			$rs				= getRecordset($sql );
			while( $rw = mysql_fetch_array($rs) ){
				$numero		= $rw["numero_cuenta"];
				$monto		= $rw["saldo_cuenta"];
				$cuenta		= $numero;
				$socio		= $rw["numero_socio"];
				
				$retirar	= false;
				if ($monto < 0){
					$monto		= $monto * (-1);
				} else {
					$retirar = true;
				}
				$xCuenta	= new cCuentaALaVista($numero);
				$xCuenta->init($rw);
				$xCuenta->setReciboDeOperacion($xRec);
				$xCuenta->setForceOperations();
				if ( $retirar == true ){
					$xCuenta->setRetiro($monto);
				} else {
					$xCuenta->setDeposito($monto);
				}
				$NSaldo		= $xCuenta->getNuevoSaldo();
				$msg		.= "$contar\t$socio\t$cuenta\tAGREGAR\tSe Agrega la Cuenta un monto de $monto, Saldo Actualizado a $NSaldo\r\n";
				$msg		.= $xCuenta->getMessages("txt");
				
				$contar++;
			}

		return $msg;
	}

	function setCleanDuplicateCuentas(){
		$sql	= "SELECT numero_cuenta,  COUNT(numero_cuenta) AS 'existentes', sucursal
					FROM captacion_cuentas
					GROUP BY numero_cuenta
					HAVING
					existentes >= 2
					ORBER BY numero_socio";
	}
	function setCuadrarCuentasByMvtos($forzar = "NO"){
		$force	= strtoupper($forzar);
		/**
		* Actualiza los saldos de la cuenta de captacion
		*/
		$msg	= "==============\tACTUALIZANDO SALDOS DE CUENTAS DE CAPTACION	========\r\n";
		$msg	.= "==============\tCHECANDO CUENTAS A LA VISTA		========\r\n";

			$sqlChck	= "SELECT
						`captacion_cuentas`.`numero_cuenta`,
						`captacion_cuentas`.`numero_socio`,
						`captacion_cuentas`.`tipo_cuenta`,
						`captacion_cuentas`.`saldo_cuenta`,
						`captacion_cuentas`.`fecha_apertura`,
					
						`captacion_cuentas`.`tipo_subproducto`,
						
					
						ROUND(SUM( (`operaciones_mvtos`.`afectacion_real` *
						`eacp_config_bases_de_integracion_miembros`.`afectacion`) ),2) AS 'saldo_obtenido',
						MAX( `operaciones_mvtos`.`fecha_afectacion` ) AS 'fecha'
				FROM
					`operaciones_mvtos` `operaciones_mvtos`
						INNER JOIN `eacp_config_bases_de_integracion_miembros`
						`eacp_config_bases_de_integracion_miembros`
						ON `operaciones_mvtos`.`tipo_operacion` =
						`eacp_config_bases_de_integracion_miembros`.`miembro`
							INNER JOIN `captacion_cuentas` `captacion_cuentas`
							ON `operaciones_mvtos`.`docto_afectado` = `captacion_cuentas`.
							`numero_cuenta`
				WHERE
				(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 3100)
				AND
				(`captacion_cuentas`.`tipo_cuenta` = " . CAPTACION_TIPO_VISTA . ")
				GROUP BY
					`captacion_cuentas`.`numero_cuenta`
				ORDER BY
					`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`
					";
			$rs = mysql_query($sqlChck, cnnGeneral() );
				if (!$rs){
					$msg	.= date("H:i:s") . "\tERROR : EL SISTEMA DEVOLVIO : " . mysql_error() . "\r\n";
				}
			//nuevo
			if ( $force == "SI" ){
				$sqlDef	= "UPDATE captacion_cuentas SET saldo_cuenta=0 WHERE (`captacion_cuentas`.`tipo_cuenta` = 10)";
				my_query($sqlDef);
				$msg	.= date("H:i:s") . "\tFORZAR: El sistema ha forzado el saldo a Cero\r\n";
			}
			while(	$rw = mysql_fetch_array($rs) ){

				$xT			= new cTipos();

				$socio 		= $rw["numero_socio"];
				$cuenta		= $rw["numero_cuenta"];
				$saldo		= $xT->cFloat($rw["saldo_cuenta"], 2);
				$NSaldo		= $xT->cFloat($rw["saldo_obtenido"], 2);
				$NFecha		= ( $rw["fecha"] == "0000-00-00") ? $rw["fecha_apertura"] : $rw["fecha"];
				$tipo		= $rw["tipo_cuenta"];
				$subproducto= $rw["tipo_subproducto"];

				if ($tipo == 10 ){
					if	( ($saldo != $NSaldo) OR ($force == "SI") ){
						$diferencia = round( ($saldo - $NSaldo), 2);
						$msg	.= date("H:i:s") . "\t$socio\t$cuenta\tDIFERENCIA $diferencia\t Saldo $saldo, Saldo por MVTOS $NSaldo, Fecha : $NFecha \r\n";
						//Actualizar la Cuenta al Obtenido
						//XXX: 1.- Cambiar Tasa de Calculo
						$tasa	=  setNoMenorQueCero( obtentasa($NSaldo, CAPTACION_TIPO_VISTA, 0, $subproducto) );
							$sqlUS 	= "UPDATE captacion_cuentas
										SET saldo_cuenta=$NSaldo,
											fecha_afectacion = '$NFecha',
										tasa_otorgada=$tasa
											WHERE numero_cuenta=$cuenta";
							$x = my_query($sqlUS);
							if( $x["stat"] == false ){
										$msg	.= date("H:i:s") . "\t$socio\t$cuenta\ERROR : EL SISTEMA DEVOLVIO . " . $x["error"] . "\r\n";
							} else {
									$msg	.= date("H:i:s") . "\t$socio\t$cuenta\tSUCESS\tActualizacion Satisfactoria al Monto de $NSaldo, de un saldo inicial de $saldo\r\n";
							}
					} else {
						$msg	.= date("H:i:s") . "\t$socio\t$cuenta\tSIN CONTINGENCIAS\t NO HAY DIFERENCIAS\tSaldo $saldo\r\n";
					}
				} else {
					$msg	.= date("H:i:s") . "\t$socio\t$cuenta\tNO_APP\tLa Cuenta es INVERSION tiene un Saldo de $saldo y uno por MVTOS de $NSaldo\r\n";
				}
			}
			unset( $rw );
			unset( $rs );
//====================================================================================================================================================================================================
			$msg	.= "============== CHECANDO CUENTAS DE INVERSION		========\r\n";

			$sqlChck	= "SELECT
						`captacion_cuentas`.`numero_cuenta`,
						`captacion_cuentas`.`numero_socio`,
						`captacion_cuentas`.`tipo_cuenta`,
						`captacion_cuentas`.`saldo_cuenta`,
						`captacion_cuentas`.`fecha_apertura`,
						`captacion_cuentas`.`inversion_fecha_vcto`,
						`captacion_cuentas`.`dias_invertidos`,
					
						`captacion_cuentas`.`tipo_subproducto`,
					
						ROUND(SUM( (`operaciones_mvtos`.`afectacion_real` *
						`eacp_config_bases_de_integracion_miembros`.`afectacion`) ),2) AS 'saldo_obtenido',
						MAX( `operaciones_mvtos`.`fecha_afectacion` ) AS 'fecha'
				FROM
					`operaciones_mvtos` `operaciones_mvtos`
						INNER JOIN `eacp_config_bases_de_integracion_miembros`
						`eacp_config_bases_de_integracion_miembros`
						ON `operaciones_mvtos`.`tipo_operacion` =
						`eacp_config_bases_de_integracion_miembros`.`miembro`
							INNER JOIN `captacion_cuentas` `captacion_cuentas`
							ON `operaciones_mvtos`.`docto_afectado` = `captacion_cuentas`.
							`numero_cuenta`
				WHERE
				(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 3200)
				AND
				(`captacion_cuentas`.`tipo_cuenta` = " . CAPTACION_TIPO_PLAZO . ")
				GROUP BY
					`captacion_cuentas`.`numero_cuenta`
				ORDER BY
					`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`
					";
			$rs = mysql_query($sqlChck, cnnGeneral() );
				if (!$rs){
					$msg	.= date("H:i:s") . "\tERROR : EL SISTEMA DEVOLVIO : " . mysql_error() . "\r\n";
				}
			//nuevo
			if ( $force == "SI" ){
				$sqlDef	= "UPDATE captacion_cuentas SET saldo_cuenta=0 WHERE (`captacion_cuentas`.`tipo_cuenta` = 20)";
				my_query($sqlDef);
				$msg	.= date("H:i:s") . "\tFORZAR: El sistema ha forzado el saldo a Cero\r\n";
			}
			while(	$rw = mysql_fetch_array($rs) ){

				$xT		= new cTipos();

				$socio 	= $rw["numero_socio"];
				$cuenta	= $rw["numero_cuenta"];
				$saldo	= $xT->cFloat($rw["saldo_cuenta"], 2);
				$NSaldo	= $xT->cFloat($rw["saldo_obtenido"], 2);
				$NFecha	= ( $rw["fecha"] == "0000-00-00") ? $rw["inversion_fecha_vcto"] : $rw["fecha"];
				$tipo	= $rw["tipo_cuenta"];
				$dias	= $rw["dias_invertidos"];

				if ($tipo == 20 ){
					if	( ($saldo != $NSaldo) OR ($force == "SI") ){
						$diferencia = round( ($saldo - $NSaldo), 2);
						$msg	.= date("H:i:s") . "\t$socio\t$cuenta\tDIFERENCIA $diferencia\t Saldo $saldo, Saldo por MVTOS $NSaldo, Fecha : $NFecha \r\n";
						//Actualizar la Cuenta al Obtenido
						//XXX: 1.- Cambiar Tasa de Calculo
						$tasa	= setNoMenorQueCero( obtentasa($NSaldo, CAPTACION_TIPO_PLAZO, $dias) );
							$sqlUS 	= "UPDATE captacion_cuentas
										SET saldo_cuenta=$NSaldo,
											fecha_afectacion = '$NFecha',
										tasa_otorgada=$tasa
											WHERE numero_cuenta=$cuenta ";
							$x = my_query($sqlUS);
							if( $x["stat"] == false ){
										$msg	.= date("H:i:s") . "\t$socio\t$cuenta\ERROR : EL SISTEMA DEVOLVIO . " . $x["error"] . "\r\n";
							} else {
									$msg	.= date("H:i:s") . "\t$socio\t$cuenta\tSUCESS\tActualizacion Satisfactoria al Monto de $NSaldo, de un saldo inicial de $saldo\r\n";
							}
					} else {
						$msg	.= date("H:i:s") . "\t$socio\t$cuenta\tSIN CONTINGENCIAS\t NO HAY DIFERENCIAS\tSaldo $saldo\r\n";
					}
				} else {
					$msg	.= date("H:i:s") . "\t$socio\t$cuenta\tNO_APP\tLa Cuenta es ORDINARIA tiene un Saldo de $saldo y uno por MVTOS de $NSaldo\r\n";
				}
			}				
//====================================================================================================================================================================================================				
		return $msg;
	}
	function getGenerarBaseGravadaMensualIDE($fecha = false){
		if ( $fecha == false ){
			$fecha = fechasys();
		}
		$msg			= "";
		$msg			.= "============== GENERANDO EL MOVIMIENTO DE BASE GRAVABLE DEL IDE	==========\r\n";
		$msg			.= "============== FECHA: $fecha                                    ==========\r\n";

		$xF				= new cFecha(0, $fecha);

		$dia_inicial	= $xF->getDiaInicial();
		$dia_final		= $xF->getDiaFinal();

		$sqlGravados	= "SELECT
								`operaciones_mvtos`.`socio_afectado`,
								SUM(`operaciones_mvtos`.`afectacion_real`) AS 'monto'
							FROM
								`operaciones_mvtos` `operaciones_mvtos`
									INNER JOIN `operaciones_recibos` `operaciones_recibos`
									ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.
									`idoperaciones_recibos`
										INNER JOIN `eacp_config_bases_de_integracion_miembros`
										`eacp_config_bases_de_integracion_miembros`
										ON `operaciones_mvtos`.`tipo_operacion` =
										`eacp_config_bases_de_integracion_miembros`.`miembro`
						WHERE
							(`operaciones_mvtos`.`fecha_afectacion` >='$dia_inicial')
							AND
							(`operaciones_mvtos`.`fecha_afectacion` <='$dia_final')
							AND
							(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 2600)
							AND
							(`operaciones_recibos`.`tipo_pago` = 'efectivo' )
						GROUP BY
							`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
							`operaciones_mvtos`.`socio_afectado`,
							`operaciones_recibos`.`tipo_pago` ";
		//$msg		.= "$sqlGravados\r\n";
		//Agregar Recibo
		$xRec		= new cReciboDeOperacion(10);
		//$xRec->setGenerarPoliza();
		$xRec->setForceUpdateSaldos();
		$idrecibo	=  $xRec->setNuevoRecibo(1,1,$fecha, 1, 10, "BASE_REMANENTE_GRAVABLE_AL_IDE", "NA", "ninguno", "NA", DEFAULT_GRUPO);
		$xRec->setNumeroDeRecibo($idrecibo);

		$rs 		=  getRecordset( $sqlGravados );

			while( $rw = mysql_fetch_array($rs) ){
				$socio				= $rw["socio_afectado"];
				$excencion			= EXCENCION_IDE;
				$base_gravada		= setNoMenorQueCero( ($rw["monto"] - $excencion) );

				if ($base_gravada > 0){
					$xRec->setNuevoMvto($dia_final, $base_gravada, 9302, 1, "", 1, false, $socio, 1);
				}
			}
		$xRec->setFinalizarRecibo();

		$msg			.= $xRec->getMessages("txt");
		return $msg;
	}
	function setValidarCuentas($ForzarCorreccion = false){
		$msg			= "";
		$msg			.= "============== VALIDANDO CUENTAS DE CAPTACION	==========\r\n";
		$msg			.= "============== FECHA:     " . fechasys() . "             ==========\r\n";
		/**
		 * Valida si la Cuenta de captacion por defecto existe 
		 * @since 2010-12-31
		 */
		$xPCta			= new cCuentaALaVista(CTA_GLOBAL_CORRIENTE, DEFAULT_SOCIO);
		if ( $xPCta->setContarCuenta() == 0 ){
			$msg		.= "LA Cuenta por DEFECTO no EXISTE\r\n";
			$xPCta->setNuevaCuenta(99, 1, DEFAULT_SOCIO, "", DEFAULT_CREDITO,"", "", DEFAULT_GRUPO, false, 10);
			$msg		.= $xPCta->getMessages("txt");
		}

		$xTb		= new cSAFETabla(TCAPTACION_CUENTAS);
		$SqlCta 	= $xTb->getQueryInicial() .  "
				FROM
					`captacion_cuentas` `captacion_cuentas`
						INNER JOIN `captacion_cuentastipos` `captacion_cuentastipos`
						ON `captacion_cuentas`.`tipo_cuenta` = `captacion_cuentastipos`.
						`idcaptacion_cuentastipos`
							INNER JOIN `captacion_subproductos` `captacion_subproductos`
							ON `captacion_cuentas`.`tipo_subproducto` = `captacion_subproductos`
							.`idcaptacion_subproductos` ";
		$rs		= getRecordset( $SqlCta );
		while ( $rw 	= mysql_fetch_array($rs) ){
			$xCta		= new cCuentaDeCaptacion($rw["numero_cuenta"], $rw["numero_socio"]);
			$xCta->init($rw);
			$msg		.= $xCta->setValidar($ForzarCorreccion);
			//$msg		.= $xCta->getMessages("txt");
		}
		return $msg;
	}
	
	function setRegenerarSDPM($PFechaInicial, $PFechaFinal, $GenerarInteres = true, $incluirSinSaldo = false, $NumeroCuenta = false ){
//		$PFechaFinal		= "";
		$mBase			= 3100;
		$xT				= new cTipos();
		$ql				= new MQL();
		$BySaldo		= ($incluirSinSaldo == false ) ? " AND captacion_cuentas.saldo_cuenta >=" . TOLERANCIA_SALDOS : "";
		
		$ByCuentaSDPM	= ($NumeroCuenta == false) ? "" : " AND `captacion_sdpm_historico`.`cuenta` = $NumeroCuenta ";
		$ByCuentaMvto	= ($NumeroCuenta == false) ? "" : " AND `operaciones_mvtos`.`docto_afectado` = $NumeroCuenta ";
		$ByCuentaCta	= ($NumeroCuenta == false) ? "" : " AND captacion_cuentas.numero_cuenta = $NumeroCuenta ";
		
		$msg			= "";
		$msg			.= "==========================================================================================\r\n";
		$msg			.= "==================\tGenerando SDPM desde el $PFechaInicial al $PFechaFinal\r\n";
		$msg			.= "==========================================================================================\r\n";
		$msg			.= "Socio\tCuenta\tOPER\tEjercicio\tPeriodo\tFecha\tDias\tMonto\tSaldo\tSDPM\r\n";

		$sqlM				= "SELECT 
									`operaciones_mvtos`.`fecha_operacion`  AS 'fecha_operacion',
									`operaciones_mvtos`.`recibo_afectado` AS 'recibo_afectado',
									`operaciones_mvtos`.`docto_afectado` AS 'docto_afectado',
									`operaciones_mvtos`.`afectacion_real` AS 'afectacion_real',
									`operaciones_mvtos`.`valor_afectacion`,
									`eacp_config_bases_de_integracion_miembros`.`afectacion` AS 'afectacion'
								FROM
									`operaciones_mvtos` `operaciones_mvtos` 
										INNER JOIN `eacp_config_bases_de_integracion_miembros` 
											`eacp_config_bases_de_integracion_miembros` 
										ON `operaciones_mvtos`.`tipo_operacion` = 
										`eacp_config_bases_de_integracion_miembros`.`miembro` 
									WHERE
									(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =$mBase)
									$ByCuentaMvto
								ORDER BY
									`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
									`operaciones_mvtos`.`fecha_operacion` ASC";
		$rsM 				= getRecordset( $sqlM );
		$arrOps				= array();			//Array de montos de operacion
		$arrRecs			= array();			//Array de Recibos de Operacion
		while($rwM = mysql_fetch_array($rsM)){
			//clave cuenta fecha
			$cuenta			= $rwM["docto_afectado"];
			$fecha			= $rwM["fecha_operacion"];
			if ( isset($arrOps[$cuenta . "-" . $fecha ]) ){
				$arrOps[$cuenta . "-" . $fecha ]	+= ($rwM["afectacion_real"] * $rwM["afectacion"] );
			} else {
				$arrOps[$cuenta . "-" . $fecha ]	= ($rwM["afectacion_real"] * $rwM["afectacion"] );
			} 
			$arrRecs[$cuenta . "-" . $fecha ]		= $rwM["recibo_afectado"];
			//$msg					.= "WARN\t$cuenta\tAgregar " . ($rwM["afectacion_real"] * $rwM["afectacion"] ) . "\r\n";
		}
		//Eliminar periodos anteriores
		$sqlDF				= "DELETE FROM captacion_sdpm_historico WHERE (fecha>='$PFechaInicial' AND fecha<='$PFechaFinal') $ByCuentaSDPM ";

		my_query($sqlDF);
		//FECHAS
		$xF					= new cFecha(0);
		//
		$xTbl				= new cSAFETabla( TCAPTACION_CUENTAS );
		$sqlCX				= $xTbl->getQueryInicial() . " WHERE captacion_cuentas.tipo_cuenta = " . CAPTACION_TIPO_VISTA . " $BySaldo $ByCuentaCta ";
		$rs1				= getRecordset( $sqlCX );
		
		while($rw1 = mysql_fetch_array($rs1) ){
			$socio				= $rw1["numero_socio"];
			$cuenta				= $rw1["numero_cuenta"];
			
			$xCta				= new cCuentaALaVista($cuenta, $socio);
			$xCta->init($rw1);
			$DCta				= $xCta->getDatosInArray();
			$FApertura			= $DCta["fecha_apertura"];
			$dias				= $xF->setRestarFechas($PFechaFinal, $FApertura);
			$dias				+= 1;
			$sucursal			= $DCta["sucursal"];
			
			$FechaAnterior		= $FApertura;
			$saldoAnterior		= 0;
			$xF2				= new cFecha(1);
			
			for ( $i = 0; $i <= $dias; $i++ ){
				$OpFecha		= $xF->setSumarDias($i, $FApertura);
				$xF->set($OpFecha);
				$OpFechaFin		= $xF->getDiaFinal();
				$OpMonto		= ( isset( $arrOps[$cuenta . "-" . $OpFecha] ) ) ?  $arrOps[$cuenta . "-" . $OpFecha] : 0;
				$idrecibo		= ( isset( $arrRecs[$cuenta . "-" . $OpFecha] ) ) ? $arrRecs[$cuenta . "-" . $OpFecha] : DEFAULT_RECIBO;
				//si la operacion es mayor a cero o es FIN DE MES
				if( ($OpMonto != 0) OR ($OpFecha == $OpFechaFin) ){
						$diasTrans	= $xF2->setRestarFechas($OpFecha, $FechaAnterior);
						$xF2->set($OpFecha);
						$ejercicio	= $xF2->anno();
						$periodo	= $xF2->mes();
						
						$sdpd		= $saldoAnterior * $diasTrans;
						$nuevatasa	= $xCta->getTasaAplicable(0,0, $saldoAnterior);
						
						//corregir fecha
						$sqlUSPM	= "INSERT INTO captacion_sdpm_historico
										(ejercicio, periodo, cuenta, fecha, dias, tasa, monto, recibo, numero_de_socio, sucursal)
	    								VALUES( $ejercicio, $periodo, $cuenta, '$OpFecha', $diasTrans, $nuevatasa, $sdpd, $idrecibo, $socio, '$sucursal') ";
						//si es valida la operacion, se actualizan
						if ( ($OpFecha >= $PFechaInicial) AND ($OpFecha <= $PFechaFinal) ){
							my_query($sqlUSPM);
							$msg		.= "$socio\t$cuenta\t+SDPM\t$ejercicio\t$periodo\t$OpFecha\t$diasTrans\t$OpMonto\t$saldoAnterior\t$sdpd\r\n";
						} else {
							$msg		.= "$socio\t$cuenta\t=SDPM\t$ejercicio\t$periodo\t$OpFecha\t$diasTrans\t$OpMonto\t$saldoAnterior\t$sdpd\r\n";
						}
						$FechaAnterior	= $OpFecha;
						$saldoAnterior	+= $OpMonto;
				}
			}
		}
			//Agregar Movimientos Finales del MES.
			//FIXME: Corregir incidencias
			//opcional: agregar Interes
			if ( $GenerarInteres == true){
				$xRec		= new cReciboDeOperacion(12, false);
				$recibo		=  $xRec->setNuevoRecibo(DEFAULT_SOCIO, 1, $PFechaFinal, 1, 12, "REGENERAR_INTERES_SDPM_$PFechaFinal", "NA", "ninguno", "NA", DEFAULT_GRUPO);
				$msg		.= "==========================================================================================\r\n";
				$msg		.= "==================\tAGREGADO INTERES :: RECIBO $recibo\r\n";
				$msg		.= "==========================================================================================\r\n";
				$_SESSION["recibo_en_proceso"]		= $recibo;				
				//sumar sdpm del mes por cuenta
				$sqlSDPM	= "SELECT
								`captacion_sdpm_historico`.`numero_de_socio`,
								`captacion_sdpm_historico`.`cuenta`,
								SUM(`captacion_sdpm_historico`.`dias`)  AS `dias_transcurridos`,
								SUM(`captacion_sdpm_historico`.`monto`) AS `sdpm`,
								ROUND( (`captacion_sdpm_historico`.`monto` / `captacion_sdpm_historico`.`dias`), 2) AS `ultimo_saldo`,
								MAX(`captacion_sdpm_historico`.`fecha`) AS 'UltimaFecha'
							FROM
								`captacion_sdpm_historico` `captacion_sdpm_historico` 
							WHERE
								(`captacion_sdpm_historico`.`fecha` >= '$PFechaInicial')
								AND
								(`captacion_sdpm_historico`.`fecha` <= '$PFechaFinal')
								$ByCuentaSDPM
							GROUP BY
								`captacion_sdpm_historico`.`cuenta`,
								`captacion_sdpm_historico`.`ejercicio`,
								`captacion_sdpm_historico`.`periodo`
							ORDER BY
								`captacion_sdpm_historico`.`fecha` DESC ";
				$rsCAP 		= $ql->getDataRecord( $sqlSDPM );
				//setLog($sqlSDPM);
				foreach ($rsCAP as $rwC){
					$socio			= $rwC["numero_de_socio"];
					$cuenta			= $rwC["cuenta"];
					$dias_de_calc	= $rwC["dias_transcurridos"];
					$sumaSDPM		= $rwC["sdpm"];
					$FechaI			= $rwC["UltimaFecha"];
					$promedio		= $xT->cFloat(($sumaSDPM / $dias_de_calc), 2);
					//XXX: Solucionar Tasa de Interes y hacer las rapida la consulta
					$xCta			= new cCuentaDeCaptacion($cuenta); $xCta->init();
					$subtipo		= $xCta->getTipoDeSubproducto();
					$tasa_nueva		= obtentasa($promedio, CAPTACION_TIPO_VISTA, 0, $subtipo);
					$interes		= ($sumaSDPM * $tasa_nueva) / EACP_DIAS_INTERES;
					$interes		= $xT->cFloat($interes, 2);
					//agregar movimiento
					if ( $interes > 0 ){
						setNuevoMvto($socio, $cuenta, $recibo, $FechaI, $interes, 222,1, "CALCULO_AUTOMATICO_DESDE_$PFechaInicial" );
						$msg .= "$socio\t$cuenta\tAGREGAR\tInteres por $interes, tasa $tasa_nueva, Promedio $promedio, SDPM $sumaSDPM, Dias $dias_de_calc\r\n";
					} else {
						$msg .= "$socio\t$cuenta\tIGNORAR\tInteres por $interes, tasa $tasa_nueva, Promedio $promedio, SDPM $sumaSDPM, Dias $dias_de_calc\r\n";
					}
				}
			}
		return $msg;
	}
	/**
	 * Establece las Invesiones existentes - validas - a los dias minimos de Inversion
	 * @see cierre.captacion
	 */
	function setInversionesDiasMinimos(){
		$sql	= "UPDATE captacion_cuentas SET 
					`dias_invertidos` =  " . INVERSION_DIAS_MINIMOS . " 
					WHERE dias_invertidos < " . INVERSION_DIAS_MINIMOS . " 
					AND `tipo_cuenta`  = " . CAPTACION_TIPO_PLAZO ."
					AND `saldo_cuenta` >= " . INVERSION_MONTO_MINIMO . " ";
		my_query($sql);
		
	}
	function setActualizarTasasDeInteres($cuenta = false, $todas = false, $tipo = false){
		
		$xDTb		= new cSAFETabla(TCAPTACION_CUENTAS);
		
		$wh			= "";
		if($cuenta == false){
			$wh		.= " AND (`captacion_cuentas`.`numero_cuenta` = $cuenta ) /*setActualizarInteres*/ ";
		} else {
			$wh		.= ($todas == false) ? "" : " AND 	(`captacion_cuentas`.`saldo_cuenta` > " . TOLERANCIA_SALDOS  . ") ";
		}
		
		$SqlCta 	=  $xDTb->getQueryInicial() . " WHERE	(`captacion_cuentas`.`numero_cuenta` !=" . DEFAULT_CUENTA_CORRIENTE . ") $wh ";
		
		$xCta		= new cCuentaDeCaptacion($cuenta);
		$xCta->init();
		$xCta->getTasaAplicable();
	}
}
?>