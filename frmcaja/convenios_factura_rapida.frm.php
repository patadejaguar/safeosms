<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
 */
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	include_once("../core/core.db.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xLog		= new cCoreLog();
$xFil		= new cFileImporter();

//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);



$observaciones	= parametro("idobservaciones");
$cheque 		= parametro("cheque", DEFAULT_CHEQUE);
$comopago 		= parametro("ctipo_pago", DEFAULT_TIPO_PAGO, MQL_RAW);
$foliofiscal 	= parametro("foliofiscal", DEFAULT_RECIBO_FISCAL);
$delimiter		= parametro("idlimitador", "|");
$tipoderecibo	= RECIBOS_TIPO_PAGO_CREDITO;


$xHP->init();

$xFRM		= new cHForm("frmconvfactrapida", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

class cTmp {
	public $PERSONA		= 1;
	public $CREDITO		= 2;
	public $FECHA		= 3;
	public $MONTO		= 4;
	public $NOTAS		= 5;
	public $PER			= 6;
	public $TOPER		= 7;
	public $INTEGRA		= 8;
}
$xEsq	= new cTmp();

if($action == SYS_NINGUNO){
	$xFRM->setAction("../frmcaja/convenios_factura_rapida.frm.php?action=" . MQL_MOD, true);
	
	$xFRM->OHidden("MAX_FILE_SIZE", "1024000");
	$xFRM->OFile("idarchivo","",  "TR.Archivo");
	
	$xFRM->addCobroBasico();
	$xFRM->addHElem( $xSel->getListaDeCuentasBancarias()->get(true) );
	
	$xFRM->addObservaciones();
	$xFRM->addSubmit();
	
	$xUL	= new cHUl();
	$xUL->li("01.- PERSONA|02.- DOCUMENTO|03.- FECHA|04.- MONTO|05.- NOTAS|06.- PERIODO|07.- TIPO|08.- INTEGRADO");
	$xUL->li("Columna 01.- Persona .- Numero de Persona");
	$xUL->li("Columna 02.- Documento .- Numero de Documento");
	$xUL->li("Columna 03.- Fecha .- Fecha de Operacion");
	$xUL->li("Columna 04.- Monto .- Monto de la Operacion");
	$xUL->li("Columna 05.- Notas .- Observaciones");
	$xUL->li("Columna 06.- Periodo .- Periodo o Parcialidad de la Operacion");
	$xUL->li("Columna 07.- Tipo .- Tipo de Operacion");
	$xUL->li("Columna 08.- Integra .- Aplica en Abonos de Credito, si es letra completa");
	
	$xFRM->addSeccion("idhed", "TR.LAYOUT");
	$xFRM->addHTML($xUL->get());
	$xFRM->endSeccion();
	
} else if ($action == MQL_MOD) {
	$doc1			= (isset($_FILES["idarchivo"])) ? $_FILES["idarchivo"] : false;
	
	$xFRM->setAction("../frmcaja/convenios_factura_rapida.frm.php?action=" . MQL_ADD, true);
	
	$xFRM->OHidden("idobservaciones", $observaciones);
	$xFRM->OHidden("cheque", $cheque);
	$xFRM->OHidden("ctipo_pago", $comopago);
	$xFRM->OHidden("foliofiscal", $foliofiscal);
	$xFRM->OHidden("idcodigodecuenta", $ctabancaria);
	
	$xFil->setCharDelimiter($delimiter);
	$xFil->setLimitCampos(10);
	//==================== Porpias de la financiera
	$arrPagos			= array();
	//var_dump($_FILES["f1"]);
	if($xFil->processFile($doc1) == true){
		$data				= $xFil->getData();
		$linea				= 0;
		$sumaNeta			= 0;
		$detalles			= $observaciones;
		
		foreach($data as $valores => $cont){
			$xFil->setDataRow($cont);
			$persona		= $xFil->getEntero($xEsq->PERSONA);
			$credito		= $xFil->getEntero($xEsq->CREDITO);
			$fecha			= $xFil->getFecha($xEsq->FECHA);
			$notas			= $observaciones; //$xFil->getV($xEsq->NOTAS);
			$monto			= $xFil->getFlotante($xEsq->MONTO);
			$periodo		= $xFil->getEntero($xEsq->PER);
			if($monto > 0){
				$xCred		= new cCredito($credito);
				if($xCred->init() == true){
					$mCreditoIVA		= $xCred->getTasaIVA();
					$persona			= $xCred->getClaveDePersona();
					$xRec				= new cReciboDeOperacion($tipoderecibo, true);
					$parcialidad		= (isset($arrPagos[$credito])) ? $arrPagos[$credito] + 1 : 1;		//Propio
					$parcialidad		= ($periodo > 0) ? $periodo : $parcialidad;
					//setLog("$fecha");
					$idrecibo			= $xRec->setNuevoRecibo($persona, $credito, $fecha, $parcialidad, $tipoderecibo, $notas, "", TESORERIA_COBRO_EFECTIVO );
					$xRec->setDatosDePago(EACP_CLAVE_MONEDA_LOCAL, 0, $cheque, $comopago, SYS_NINGUNO, $ctabancaria);
					$arrPagos[$credito] = $parcialidad;
					for($i=$xCred->getPeriodoActual(); $i<= $xCred->getPagosAutorizados(); $i++){
						$ParcPer	= $i;
						//idcreditos_productos_costos, clave_de_producto, clave_de_operacion, unidades, unidad_de_medida,
						$xLetra	= new cParcialidadDeCredito($xCred->getClaveDePersona(), $xCred->getNumeroDeCredito(), $ParcPer);
						$xLetra->init();
						$ParcTotal	= $xLetra->getMonto();
						
						$ParcCap	= $xLetra->getCapital();
						$ParcInt	= $xLetra->getInteres();
						$ParcIVA	= $xLetra->getImpuestos();
						$ParcOtros	= $xLetra->getOtros();
						$ParcAho	= $xLetra->getAhorro();
						$ParcFecha	= $xLetra->getFechaDePago();
						$ParcIDOtros= $xLetra->getIDOtros();
						
						$TotalSinO	= $xLetra->getTotalSinOtros();
						
						if($monto > 0 AND $TotalSinO >0){
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
									$monto			= $monto - $ParcTotal;	//Disminuir letra
								}
								$xLog->add("$persona\tEl Monto $monto ($ParcInt + $ParcIVA + $ParcCap + $ParcAho + $ParcOtros)\r\n", $xLog->DEVELOPER);
							} else {
								$ParcTotal = 0;						//Cancelar letra
								$xLog->add("$persona\tEl Monto llego a Cero $monto\r\n", $xLog->DEVELOPER);
							}
							$sumaNeta		+= ($ParcInt + $ParcIVA + $ParcCap + $ParcAho + $ParcOtros);
							if($ParcTotal > 0){
								$xPag		= new cCreditosPagos($xCred->getNumeroDeCredito());
								if($ParcInt >0){
									$xPag->addPagoInteres($ParcInt, $ParcPer, $detalles, $comopago, $fecha, $idrecibo);
								}
								
								if($ParcOtros > 0){
									if($ParcIDOtros == OPERACION_CLAVE_PLAN_DESGLOSE){
										$DO		= $xCred->getOProductoDeCredito()->getListaOtrosCargosEnParcs();
										foreach ($DO as $idx => $mtasa){
											$mmonto		= setNoMenorQueCero(($ParcOtros * $mtasa),2);
											$xRec->setNuevoMvto($fecha, $mmonto, $idx, $ParcPer, $detalles, 1, false,$xCred->getClaveDePersona(), $xCred->getNumeroDeCredito(),$fecha, $fecha );
										}
										
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
										$xLog->add($xRec2->getMessages(), $xLog->DEVELOPER);
									}
								}
								if($ParcCap > 0){
									$xCred->setAbonoCapital($ParcCap, $ParcPer, $cheque, $comopago, $foliofiscal, $detalles, false, $fecha, $idrecibo);
								}
								if($xCred->getEsNomina() == true){
									$MontoOp		= $ParcTotal;
									$solicitud		= $xCred->getClaveDeCredito();
									$recibo_pago	= $xRec->getCodigoDeRecibo();
									$fecha_operacion= $fecha;
									
									//$xPerNom	= new cCreditosDeNomina($xCred->getClaveDeCredito());
									if($xCred->getClaveDeEmpresa() != FALLBACK_CLAVE_EMPRESA){
										$xCobDet		= new cEmpresasCobranzaDetalle(false, false);
										$xCobDet->initByCreditoID($xCred->getClaveDeCredito(), $ParcPer);
										if($MontoOp >= $xCobDet->getMontoEnviado()){
											$xCobDet->setPagado("[$recibo_pago] $observaciones [$fecha_operacion]", $recibo_pago);
											$xLog->add("WARN\tEliminar Operacion de Empresa $solicitud, $ParcPer, $recibo_pago [$fecha_operacion] \r\n", $xLog->DEVELOPER);
										} else {
											$mMontoActualLetra	= ($xCobDet->getMontoEnviado()-$MontoOp);
											$xCobDet->setActualizarMontoEnviado($mMontoActualLetra, "[$recibo_pago] $observaciones [$fecha_operacion]", $recibo_pago);
											$xLog->add("WARN\tNo se actualiza Operacion de Empresa $solicitud - $ParcPer - $recibo_pago [$fecha_operacion] \r\n", $xLog->DEVELOPER);
										}
										
									}
								}
								$xLog->getMessages($xLetra->getMessages(), $xLog->DEVELOPER);
							}
						} else {
							$xLog->add("$persona\tEl Monto no es valido $monto\r\n", $xLog->DEVELOPER);
						}
					} //end bucle, end recibo
					$xRec->setFinalizarTesoreria();
					$xRec->setForceUpdateSaldos(true);
					$xRec->setFinalizarRecibo(true, true);
					
				}
			}
		}
	}
} else if ($action == MQL_ADD) {
	
}

$xFRM->addLog($xLog->getMessages());

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>