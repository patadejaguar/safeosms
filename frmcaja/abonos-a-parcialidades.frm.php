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
$xHP		= new cHPage("TR.PARCIALIDADES MASIVAS");
$xCaja		= new cCaja();
$xF			= new cFecha();
$xLi		= new cSQLListas();
$xQL		= new MQL();
$xVis		= new cSQLVistas();
$xRuls		= new cReglaDeNegocio();
$useMoraBD	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_USE_MORA_BD);
$NoMoraNom	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_NOMINA_NOMORA);
$jxc 		= new TinyAjax();


function jsaGetComisionPorApertura($idcredito){
	$xCred		= new cCredito($idcredito);
	$cantidad	= 0;
	if( $xCred->init() == true){
		$tasa		= $xCred->getOProductoDeCredito()->getTasaComisionApertura();
		$cantidad = round(($xCred->getMontoAutorizado() * $tasa), 2);
	}
	return $cantidad;
}
$jxc ->exportFunction('jsaGetComisionPorApertura', array('idsolicitud'), '#idcom1');

$jxc ->process();

if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){	header ("location:../404.php?i=200"); }

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT);
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$xHP->init();

$xBase		= new cBases(BASE_IVA_OTROS);
$xBase->init();
$xFRM		= new cHForm("frmabonosparciales", "abonos-a-parcialidades.frm.php");
$xTxt		= new cHText();
$msg		= "";
$xFRM->setTitle($xHP->getTitle());
//$xFRM->setNoAcordion();
//$xFRM->addSeccion("uddu", $titulo)



if($action == MQL_ADD){
	
	$xRec 					= new cReciboDeOperacion(RECIBOS_TIPO_ESTADISTICO, false);
	$xRec->setGenerarPoliza();
	$xRec->setGenerarTesoreria();
	
	$detalles 		= parametro("idobservaciones", "");
	//$monto 		= parametro("idmonto", 0, MQL_FLOAT);
	$cheque 		= parametro("cheque", DEFAULT_CHEQUE);
	$comopago 		= parametro("ctipo_pago", DEFAULT_TIPO_PAGO, MQL_RAW);
	$foliofiscal 	= parametro("foliofiscal", DEFAULT_RECIBO_FISCAL);

	
	
	//if(setNoMenorQueCero($idrecibo) > 0){
		$xCred				= new cCredito($credito);
		$jsSum				= "";
		$MontoHistorico		= 0;
		if($xCred->init() == true){
			
			$xFRM->addButtonPlanDePagos($xCred->getNumeroDePlanDePagos());
			
			$periodo		= $xCred->getPeriodoActual();
			$periodo		= ($periodo <= 0) ? SYS_UNO : $periodo;
			$idrecibo		= $xRec->setNuevoRecibo($persona, $credito, $fecha, $periodo, RECIBOS_TIPO_PAGO_CREDITO , $detalles, $cheque, $comopago, $foliofiscal);
			$rs				= $xQL->getDataRecord($xVis->CreditosLetrasNoPagadas($credito));
			$mCreditoIVA	= $xCred->getTasaIVA();
			//setLog($xVis->CreditosLetrasNoPagadas($credito));
			foreach ($rs as $rw){
				//idcreditos_productos_costos, clave_de_producto, clave_de_operacion, unidades, unidad_de_medida,
				$ParcTotal	= setNoMenorQueCero($rw["letra"],2);
				$ParcPer	= $rw["periodo_socio"];
				$ParcCap	= $rw["capital"];
				$ParcInt	= $rw["interes"];
				$ParcIVA	= $rw["iva"];
				$ParcOtros	= $rw["otros"];
				$ParcAho	= $rw["ahorro"];
				$ParcFecha	= $rw["fecha_de_pago"];
				$ParcIDOtros= $rw["clave_otros"];
				$MoraBD		= isset($rw["interes_moratorio"]) ? $rw["interes_moratorio"] : 0; //Interes Moratorio segun BD
				$penas		= 0;
				$mora		= 0;
				$IvaOtros	= 0;
				
				
				$xMParc		= new cParcialidadDeCredito($xCred->getClaveDePersona(), $xCred->getClaveDeCredito(), $ParcPer);
				//setLog("$persona, $credito, $ParcPer");
				if($xMParc->init() == true){
					$xMParc->setDatos($rw);
					
					
					
					$DD["docto_afectado"]		= $xCred->getNumeroDeCredito();
					
					$xMParc->setIDProducto($xCred->getClaveDeProducto());
					$xMParc->setTasaMora($xCred->getTasaDeMora());
					$xMParc->setPeriocidadDePago($xCred->getPeriocidadDePago());
					
					$xBase	= $xMParc->setCalcularPenas_Y_Mora(false, false, $xCred->getEsNomina());
					$penas	= $xBase[SYS_PENAS];
					if($useMoraBD == true){
						$mora	= $MoraBD;
					} else {
						$mora	= $xBase[SYS_INTERES_MORATORIO];
					}
					if($xCred->getEsNomina() == true AND $NoMoraNom == true){
						$mora	= 0;
					}
					$IvaOtros		= round((($mora+$penas) * TASA_IVA),2);
					$ParcTotal	= $ParcTotal + $mora + $penas + $IvaOtros;
				}
				
				if($monto > 0){
					if($monto < $ParcTotal){
						$montoSinIVA	= setNoMenorQueCero( ($monto * (1/(1+$mCreditoIVA))),2);
						if( $montoSinIVA >= $ParcInt ){
							$monto		= $monto - $ParcInt;								//Disminuir Interes
							$ParcIVA	= setNoMenorQueCero(($ParcInt * $mCreditoIVA),2);	//Calcular IVA
							$monto		= $monto - $ParcIVA;								//Disminuir IVA
						} else {
							//============ Setear el Interes a lo que sobre;
							$ParcInt	= $montoSinIVA;
							$ParcIVA	= setNoMenorQueCero(($ParcInt * $mCreditoIVA),2);//Calcular IVA
							$monto		= 0;
							//============ Establecer los cargos a cero
							$ParcCap	= 0;
							$ParcAho	= 0;
							$ParcOtros	= 0;
						}
						//============= Mora
						if($monto > 0){
							
							if($monto >= $mora){
								$monto		= $monto - $mora;
							} else {
								$mora		= $monto;
								$monto		= 0;
								$ParcCap	= 0;
							}
						}
						//============= penas
						if($monto > 0){
							if($monto >= $penas){
								$monto		= $monto - $penas;
							} else {
								$penas		= $monto;
								$monto		= 0;
								$ParcCap	= 0;
							}
						}
						
						//============ Otros
						if($monto > 0){
							if($monto >= $ParcOtros){
								$monto		= $monto - $ParcOtros;
							} else {
								$ParcOtros	= $monto;
								$monto		= 0;
								$ParcAho	= 0;
								$ParcCap	= 0;
							}
						}
						//============ Iva Otros
						if($monto > 0){
							if($monto >= $IvaOtros){
								$monto		= $monto - $IvaOtros;
							} else {
								$IvaOtros	= $monto;
								$monto		= 0;
								$ParcAho	= 0;
								$ParcCap	= 0;
							}
						}
						//============ Ahorro
						if($monto > 0){
							if($monto >= $ParcAho){
								$monto		= $monto - $ParcAho;
							} else {
								$ParcAho	= $monto;
								$monto		= 0;
								$ParcCap	= 0;
							}
						}
						//============= Capítal
						if($monto > 0){
							if($monto >= $ParcCap ){
								$monto	= $monto - $ParcCap;							//Disminuir el Capital
							} else {
								//============ Establecer los cargos a cero
								$ParcCap	= $monto;
								$monto		= 0;
							}
						}
						//Actualizar total
						$ParcTotal		= $ParcInt + $ParcIVA + $ParcCap + $ParcAho + $ParcOtros;
					} else {
						$monto	= $monto - $ParcTotal;	//Disminuir letra
					}
				} else {
					$ParcTotal = 0;						//Cancelar letra
				}

				if($ParcTotal > 0){
					$xPag		= new cCreditosPagos($xCred->getNumeroDeCredito());
					//=========== Registro de penas
					if($penas > 0){
						$xPag->addPagoPenas($penas, $ParcPer, $detalles, $comopago, $fecha, $idrecibo);
						
					}
					//=========== registro de Mora
					if($mora > 0){
						$xPag->addPagoMora($mora, $ParcPer, $detalles, $comopago, $fecha, $idrecibo);
					}
					//=========== Registro de Interes
					if($ParcInt >0){
						$xPag->addPagoInteres($ParcInt, $ParcPer, $detalles, $comopago, $fecha, $idrecibo);
					}
					//IVA DE OTROS CONCEPTOS
					if($IvaOtros > 0){
						$xRec->setNuevoMvto($fecha, $IvaOtros, OPERACION_CLAVE_PAGO_IVA_OTROS, $ParcPer, $detalles, 1, false,$xCred->getClaveDePersona(), $xCred->getNumeroDeCredito(),$fecha, $fecha );
					}
					if($ParcOtros > 0){
						if($ParcIDOtros == OPERACION_CLAVE_PLAN_DESGLOSE){
							$DO		= $xCred->getOProductoDeCredito()->getListaOtrosCargosEnParcs();
							foreach ($DO as $idx => $mtasa){
								$mmonto		= setNoMenorQueCero(($ParcOtros * $mtasa),2);
								$xRec->setNuevoMvto($fecha, $mmonto, $idx, $ParcPer, $detalles, 1, false,$xCred->getClaveDePersona(), $xCred->getNumeroDeCredito(),$fecha, $fecha );
							}
							$xLetra	= new cParcialidadDeCredito($xCred->getClaveDePersona(), $xCred->getNumeroDeCredito(), $ParcPer);
							$xLetra->setClaveDePlan($xCred->getNumeroDePlanDePagos());
							$xLetra->setActualizarDesglose($ParcOtros, SYS_NEGATIVO);
						} else {
							if($xBase->getIsMember($ParcIDOtros) == true){
								$OtrosSinIVA= setNoMenorQueCero( ($ParcOtros * (1/(1+$xCred->getTasaIVAOtros()))),2);
								$ParcIVA	+= setNoMenorQueCero( ($OtrosSinIVA * $xCred->getTasaIVAOtros()),2);
								$ParcOtros	= $OtrosSinIVA;
								
							}
							$xRec->setNuevoMvto($fecha, $ParcOtros, $ParcIDOtros, $ParcPer, $detalles);
						}							
					}
					if($ParcIVA > 0){
						$xRec->setNuevoMvto($fecha, $ParcIVA, OPERACION_CLAVE_PAGO_IVA_INTS, $ParcPer, $detalles, 1, false,$xCred->getClaveDePersona(), $xCred->getNumeroDeCredito(),$fecha, $fecha );
					}
										
					if($ParcAho > 0){
						$cuenta		= $xCred->getContratoCorriente();
						$xCta		= new cCuentaALaVista($cuenta);
						if($xCta->init() == true){
							$reciboA	= $xCta->setDeposito($ParcAho, $cheque, $comopago, $foliofiscal, $detalles, $xCred->getClaveDeGrupo(), $fecha, false, $xCred->getClaveDeEmpresa(), false, $ParcPer);
							$xRec2		= new cReciboDeOperacion(false, false, $reciboA);
							if($xRec2->init() == true){
								//$xRec2->setFinalizarTesoreria()
								$xFRM->OButton("TR.Imprimir Recibo CAPTACION", "jsImprimirRecibo2()", "imprimir");
								$xFRM->addJsCode($xRec2->getJsPrint(false, "jsImprimirRecibo2"));
							}
						}
					}
					if($ParcCap > 0){
						$xCred->setAbonoCapital($ParcCap, $ParcPer, $cheque, $comopago, $foliofiscal, $detalles, false, $fecha, $idrecibo);
					}
				}			
			}

			$xRec->setForceUpdateSaldos(true);
			if($xRec->setFinalizarRecibo(true) == true){
				$xFRM->setAction("");
				$xFRM->addHElem( $xRec->getFichaSocio() );
				$xFRM->addHElem( $xRec->getFicha(true) );
				$xFRM->OButton("TR.Imprimir Recibo", "jsImprimirRecibo()", "imprimir");
				$xFRM->addAvisoRegistroOK();
				$xFRM->addCerrar();
			
				echo $xRec->getJsPrint(true);	
			}
			$xCred->setCuandoSeActualiza();
			$xCred		= new cCredito($xCred->getClaveDeCredito());
			if($xCred->init() == true){
				//Actualizar Saldo Historico
				$xRec->setMontoHistorico($xCred->getSaldoActual());
			}

		}
		if(MODO_DEBUG == true){
			$xFRM->addLog($xRec->getMessages());
		}
		//$xFRM->addCerrar();
	//} else {
		//$xFRM->addAviso($xRec->getMessages());
	//}
	$jsSum	= "0";
} else {
	
	
	if($credito > DEFAULT_CREDITO){
		$xFRM->setAction("abonos-a-parcialidades.frm.php?action=" . MQL_ADD);
		$xCred	= new cCredito($credito);
		$jsSum	= "";
		
		if($xCred->init() == true){
			$xFRM->addButtonPlanDePagos($xCred->getNumeroDePlanDePagos());
			
			$xFRM->OHidden("idsolicitud", $credito);
			$xFRM->OHidden("idsocio", $xCred->getClaveDePersona());
			$xFRM->OHidden("idmontoautorizado", $xCred->getMontoAutorizado());
			$xFRM->OHidden("idmonto", $monto);
			
			$xFRM->addHElem($xCred->getFichaMini());
			$xFRM->addGuardar();
			$mCreditoIVA	= $xCred->getTasaIVA();
			
			$rs		= $xQL->getDataRecord($xVis->CreditosLetrasNoPagadas($credito));
			
			$xT		= new cHTabla();
			$cspan	= 8;
			
			$xFRM->addFechaRecibo($fecha);
			$xFRM->addCobroBasico();
			$xFRM->addObservaciones();
			
			$sum	= 0;
			$xTxt->setDivClass("");
			$xTxt->addEvent("jsGetSumas()", "onchange");

			$xT->initRow();
			$xT->addTH("TR.Fecha_de Pago");
			$xT->addTH("TR.PARCIALIDAD");
			$xT->addTH("TR.CAPITAL");
			$xT->addTH("TR.INTERES");
			$xT->addTH("TR.IVA");
			if(MODULO_CAPTACION_ACTIVADO== true){
				$xT->addTH("TR.AHORRO");
				$cspan	= $cspan + 1;
			}
			$xT->addTH("TR.MORA");
			$xT->addTH("TR.PENAS_POR_ATRASOS");
			$xT->addTH("TR.IVA OTROS");
			
			$xT->addTH("TR.OTROS");
			$xT->addTH("TR.TOTAL");
			$xT->endRow();
			//Suma de totales
			$TCapital		= 0;
			$TInteres		= 0;
			$TIva			= 0;
			$TOtros			= 0;
			$TAhorro		= 0;
			$TMora			= 0;
			$TPena			= 0;
			$TIvaOtros		= 0;
			
			
			foreach ($rs as $rw){
				//idcreditos_productos_costos, clave_de_producto, clave_de_operacion, unidades, unidad_de_medida,
				$ParcTotal	= setNoMenorQueCero($rw["letra"],2);
				$ParcPer	= $rw["periodo_socio"];
				$ParcCap	= $rw["capital"];
				$ParcInt	= $rw["interes"];
				$ParcIVA	= $rw["iva"];
				$ParcOtros	= $rw["otros"];
				$ParcAho	= $rw["ahorro"];
				$ParcFecha	= $rw["fecha_de_pago"];
				$MoraBD		= isset($rw["interes_moratorio"]) ? $rw["interes_moratorio"] : 0; //Interes Moratorio segun BD
				
				$penas		= 0;
				$mora		= 0;
				$IvaOtros	= 0;
				
				$xMParc		= new cParcialidadDeCredito($xCred->getClaveDePersona(), $xCred->getClaveDeCredito(), $ParcPer);
				//setLog("$persona, $credito, $ParcPer");
				if($xMParc->init() == true){
					$xMParc->setDatos($rw);
					
					
					
					$DD["docto_afectado"]		= $xCred->getNumeroDeCredito();
		
					$xMParc->setIDProducto($xCred->getClaveDeProducto());
					$xMParc->setTasaMora($xCred->getTasaDeMora());
					$xMParc->setPeriocidadDePago($xCred->getPeriocidadDePago());
					
					$xBase	= $xMParc->setCalcularPenas_Y_Mora(false, false);
					$penas	= $xBase[SYS_PENAS];
					if($useMoraBD == true){
						$mora	= $MoraBD;
					} else {
						$mora	= $xBase[SYS_INTERES_MORATORIO];
					}
					if($xCred->getEsNomina() == true AND $NoMoraNom == true){
						$mora	= 0;
					}
					$IvaOtros	= round((($mora+$penas) * TASA_IVA),2);
					$ParcTotal	= $ParcTotal + $mora + $penas + $IvaOtros;
				}
				if($monto > 0){
					if($monto < $ParcTotal){
						$montoSinIVA	= setNoMenorQueCero( ($monto * (1/(1+$mCreditoIVA))),2);
						if( $montoSinIVA >= $ParcInt ){
							$monto		= $monto - $ParcInt;								//Disminuir Interes
							$ParcIVA	= setNoMenorQueCero(($ParcInt * $mCreditoIVA),2);	//Calcular IVA
							$monto		= $monto - $ParcIVA;								//Disminuir IVA
						} else {
							//============ Setear el Interes a lo que sobre;
							$ParcInt	= $montoSinIVA;
							$ParcIVA	= setNoMenorQueCero(($ParcInt * $mCreditoIVA),2);//Calcular IVA
							$monto		= 0;
							//============ Establecer los cargos a cero
							$ParcCap	= 0;
							$ParcAho	= 0;
							$ParcOtros	= 0;							
						}
						//============ Otros
						if($monto > 0){
							if($monto >= $ParcOtros){
								$monto		= $monto - $ParcOtros;
							} else {
								$ParcOtros	= $monto;
								$monto		= 0;
								$ParcAho	= 0;
								$ParcCap	= 0;
							}
						}
						//============ Iva Otros
						if($monto > 0){
							if($monto >= $IvaOtros){
								$monto		= $monto - $IvaOtros;
							} else {
								$IvaOtros	= $monto;
								$monto		= 0;
								$ParcAho	= 0;
								$ParcCap	= 0;
							}
						}
						
						//============ Ahorro
						if(MODULO_CAPTACION_ACTIVADO == true){
							if($monto > 0){
								if($monto >= $ParcAho){
									$monto		= $monto - $ParcAho;
								} else {
									$ParcAho	= $monto;
									$monto		= 0;
									$ParcCap	= 0;
								}
							}
						}
						//============= Mora
						if($monto > 0){
							
							if($monto >= $mora){
								$monto		= $monto - $mora;
							} else {
								$mora		= $monto;
								$monto		= 0;
								$ParcCap	= 0;
							}
						}
						//============= penas
						if($monto > 0){
							if($monto >= $penas){
								$monto		= $monto - $penas;
							} else {
								$penas		= $monto;
								$monto		= 0;
								$ParcCap	= 0;
							}								
						}
						//============= Capítal
						if($monto > 0){
							if($monto >= $ParcCap ){
								$monto	= $monto - $ParcCap;							//Disminuir el Capital
							} else {
								//============ Establecer los cargos a cero
								$ParcCap	= $monto;
								$monto		= 0;
							}
						}						
						//Actualizar total
						$mora				= round($mora,2);
						
						$IvaOtros				= round((($mora+$penas) * TASA_IVA),2);
						$ParcTotal			= $ParcInt + $ParcIVA + $ParcCap + $ParcAho + $ParcOtros + $mora + $penas + $IvaOtros;
						//setLog("$ParcInt + $ParcIVA + $ParcCap + $ParcAho + $ParcOtros + $mora + $penas");
					} else {
						$monto	= $monto - $ParcTotal;	//Disminuir letra
					}
					
					
				} else {
					$ParcTotal = 0;						//Cancelar letra
				}
				
				if($ParcTotal > 0){
					$TAhorro	+= $ParcAho;
					$TCapital	+= $ParcCap;
					$TInteres	+= $ParcInt;
					$TIva		+= $ParcIVA;
					$TOtros		+= $ParcAho;
					$TPena		+= $penas;
					$TMora		+= $mora;
					$TIvaOtros	+= $IvaOtros;
					
					
				 	$xT->initRow();
				 	$xT->addTD( $xF->getFechaMX($ParcFecha));
				 	$xT->addTD($ParcPer);
				 	$xT->addTDM(getFMoney($ParcCap));
				 	$xT->addTDM(getFMoney($ParcInt));
				 	$xT->addTDM(getFMoney($ParcIVA));
				 	if(MODULO_CAPTACION_ACTIVADO == true){
				 		$xT->addTDM(getFMoney($ParcAho));
				 	}
				 	$xT->addTDM(getFMoney($mora));
				 	$xT->addTDM(getFMoney($penas));
				 	$xT->addTDM(getFMoney($IvaOtros));
				 	
				 	
				 	$xT->addTDM(getFMoney($ParcOtros));
				 	$xT->addTDM( getFMoney($ParcTotal) );
				 	
				 	$xT->endRow();
				 	$xFRM->OHidden("idm-$ParcPer", $ParcTotal);
					$sum += $ParcTotal;
					$jsSum	.= ($jsSum == "") ? "flotante(\$('#idm-$ParcPer').val())" :"+flotante(\$('#idm-$ParcPer').val())";
				}
			}
			//SubTotal
			$xT->initRow();
			$xT->addTD("");
			$xT->addTD("SUMAS");
			
			$xT->addTD(getFMoney($TCapital), " class='mny total' ");
			$xT->addTD(getFMoney($TInteres), " class='mny total' ");
			$xT->addTD(getFMoney($TIva), " class='mny total' ");
			if(MODULO_CAPTACION_ACTIVADO== true){
				$xT->addTD($TAhorro, " class='mny total' ");
				
			}
			$xT->addTD(getFMoney($TMora), " class='mny total' ");
			$xT->addTD(getFMoney($TPena), " class='mny total' ");
			$xT->addTD(getFMoney($TIvaOtros), " class='mny total' ");
			
			$xT->addTD(getFMoney($TOtros), " class='mny total' ");
			$xT->addTD(getFMoney($sum), " class='mny total' id='idtsum' ");
			$xT->endRow();
			
			$xFRM->addHElem($xT->get());
		}
	} else {
		$xFRM->addCreditBasico();
		$xFRM->addMonto(0, true);
		$xFRM->addEnviar();
		$xFRM->OButton("TR.Estado de Cuenta", "getEdoCtaCredito()", $xFRM->ic()->ESTADO_CTA);
		$xFRM->OButton("TR.PLAN_DE_PAGOS", "getPlanDePagos()", $xFRM->ic()->CALENDARIO);
		$xFRM->OButton("TR.generar PLAN_DE_PAGOS", "getFormaPlanDePagos()", $xFRM->ic()->CALENDARIO1);
		$xFRM->OButton("TR.NOTAS", "jsAddNota()", $xFRM->ic()->NOTA);
		$xFRM->addAviso("", "divavisos");
		$jsSum	= "0";
	}
}
echo $xFRM->get();
?>
<script>
var xCred	= new CredGen();
var xPer	= new PersGen();
function jsGetSumas(){
	var mTotal = <?php echo $jsSum ?>;
	$("#idtdsum").html( getFMoney(mTotal));
}
function getEdoCtaCredito(){ 
	var idcredito = $("#idsolicitud").val();
	if(idcredito > DEFAULT_CREDITO){
		xCred.getEstadoDeCuenta( idcredito );
	}
}
function getPlanDePagos(){
	var idcredito = $("#idsolicitud").val();
	if(idcredito > DEFAULT_CREDITO){
		xCred.getImprimirPlanPagosPorCred( idcredito );
	}	
}
function getFormaPlanDePagos(){
	var idcredito = $("#idsolicitud").val();
	if(idcredito > DEFAULT_CREDITO){
		xCred.getFormaPlanPagos( idcredito );
	}
}
function jsAddNota(){
	var idcred	= entero($("#idsolicitud").val());
	if(idcred > 1){
		xCred.setNuevaNotaCaja(idcred);
	} else {
		Wo.alerta({msg: "Credito no valido"});
	}
}
function jsGetMemos(){
	var idp	= $("#idsocio").val();
	var idc	= $("#idsolicitud").val();
	xPer.getListaDeNotas({persona:idp,credito:idc, tipo:12, callback:jsLoadedMemos, estado:0});
}
function jsLoadedMemos(data){
	$("#divavisos").empty();
	$.each( data, function( key, val ) {
		xG.alerta({msg:val.notas,info:val.oficial});
		$("#divavisos").append("<div class='error' style='margin-bottom:0.2em'>" + val.notas + "</div>");
	});
}
function jsEvaluarSalida(evt){
	jsGetMemos();
}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>